<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Tampilkan halaman keranjang belanja
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = $this->cartService->getCartItems($user->id);
        $cartSummary = $this->cartService->getCartSummary($user->id);

        return view('pages.cart.index', compact('cartItems', 'cartSummary'));
    }

    /**
     * Tambah item ke keranjang
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        $user = Auth::user();

        try {
            $result = $this->cartService->addItem(
                $user->id,
                $request->product_id,
                $request->quantity
            );

            if ($result['success']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Produk berhasil ditambahkan ke keranjang',
                        'cart_count' => $this->cartService->getCartCount($user->id)
                    ]);
                }

                return redirect()->route('cart')->with('success', 'Produk berhasil ditambahkan ke keranjang');
            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ], 400);
                }

                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menambahkan produk'
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat menambahkan produk');
        }
    }

    /**
     * Update quantity item di keranjang
     */
    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        $user = Auth::user();

        try {
            $result = $this->cartService->updateQuantity(
                $user->id,
                $id,
                $request->quantity
            );

            if ($result['success']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Quantity berhasil diupdate',
                        'cart_summary' => $this->cartService->getCartSummary($user->id)
                    ]);
                }

                return redirect()->route('cart')->with('success', 'Quantity berhasil diupdate');
            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ], 400);
                }

                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengupdate quantity'
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat mengupdate quantity');
        }
    }

    /**
     * Hapus item dari keranjang
     */
    public function removeItem(Request $request, $id)
    {
        $user = Auth::user();

        try {
            $result = $this->cartService->removeItem($user->id, $id);

            if ($result['success']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Item berhasil dihapus dari keranjang',
                        'cart_count' => $this->cartService->getCartCount($user->id)
                    ]);
                }

                return redirect()->route('cart')->with('success', 'Item berhasil dihapus dari keranjang');
            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ], 400);
                }

                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus item'
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat menghapus item');
        }
    }

    /**
     * Kosongkan seluruh keranjang
     */
    public function clear(Request $request)
    {
        $user = Auth::user();

        try {
            $result = $this->cartService->clearCart($user->id);

            if ($result['success']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Keranjang berhasil dikosongkan'
                    ]);
                }

                return redirect()->route('cart')->with('success', 'Keranjang berhasil dikosongkan');
            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ], 400);
                }

                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengosongkan keranjang'
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat mengosongkan keranjang');
        }
    }
}
