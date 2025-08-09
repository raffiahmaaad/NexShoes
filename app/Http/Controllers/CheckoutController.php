<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $checkoutService;
    protected $paymentService;

    public function __construct(
        CartService $cartService,
        CheckoutService $checkoutService,
        PaymentService $paymentService
    ) {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
        $this->paymentService = $paymentService;
    }

    /**
     * Tampilkan halaman checkout
     */
    public function index()
    {
        $user = Auth::user();

        // Cek apakah ada item di keranjang
        $cartItems = $this->cartService->getCartItems($user->id);
        $cartSummary = $this->cartService->getCartSummary($user->id);

        if (empty($cartItems)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja Anda kosong');
        }

        // Validasi stok produk
        $stockValidation = $this->cartService->validateStock($user->id);
        if (!$stockValidation['valid']) {
            return redirect()->route('cart')->with('error', $stockValidation['message']);
        }

        // Ambil data yang diperlukan untuk checkout
        $shippingMethods = $this->checkoutService->getShippingMethods();
        $paymentMethods = $this->paymentService->getPaymentMethods();
        $userAddresses = $this->checkoutService->getUserAddresses($user->id);

        return view('pages.checkout.index', compact(
            'cartItems',
            'cartSummary',
            'shippingMethods',
            'paymentMethods',
            'userAddresses'
        ));
    }

    /**
     * Process checkout dan buat order
     */
    public function process(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $validatedData = $request->validate([
            'shipping_address_id' => 'required|exists:user_addresses,id',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:500',
            // Tambahan validasi sesuai kebutuhan
        ]);

        // Cek apakah ada item di keranjang
        $cartItems = $this->cartService->getCartItems($user->id);
        if (empty($cartItems)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja Anda kosong');
        }

        // Validasi stok sekali lagi sebelum process
        $stockValidation = $this->cartService->validateStock($user->id);
        if (!$stockValidation['valid']) {
            return redirect()->route('cart')->with('error', $stockValidation['message']);
        }

        DB::beginTransaction();

        try {
            // Buat order
            $orderData = [
                'user_id' => $user->id,
                'shipping_address_id' => $validatedData['shipping_address_id'],
                'shipping_method' => $validatedData['shipping_method'],
                'payment_method' => $validatedData['payment_method'],
                'notes' => $validatedData['notes'] ?? null,
                'cart_items' => $cartItems
            ];

            $order = $this->checkoutService->createOrder($orderData);

            if (!$order) {
                throw new \Exception('Gagal membuat order');
            }

            // Process payment jika diperlukan
            if ($validatedData['payment_method'] !== 'cod') {
                $paymentResult = $this->paymentService->processPayment($order, $validatedData);

                if (!$paymentResult['success']) {
                    throw new \Exception($paymentResult['message']);
                }

                // Update order dengan payment info jika perlu
                $this->checkoutService->updateOrderPayment($order->id, $paymentResult);
            }

            // Kosongkan keranjang setelah order berhasil dibuat
            $this->cartService->clearCart($user->id);

            DB::commit();

            // Redirect ke halaman success
            return redirect()->route('checkout.success', $order->id)
                           ->with('success', 'Pesanan Anda berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Checkout process failed: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage())
                         ->withInput();
        }
    }

    /**
     * Halaman sukses setelah checkout
     */
    public function success($orderId)
    {
        $user = Auth::user();
        $order = $this->checkoutService->getOrderById($orderId, $user->id);

        if (!$order) {
            return redirect()->route('dashboard')->with('error', 'Order tidak ditemukan');
        }

        return view('pages.checkout.success', compact('order'));
    }

    /**
     * Halaman cancel jika checkout dibatalkan
     */
    public function cancel()
    {
        return view('pages.checkout.cancel')
               ->with('info', 'Pesanan Anda telah dibatalkan');
    }
}
