<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar pesanan user
     */
    public function index()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.orders.index', compact('orders'));
    }

    /**
     * Tampilkan detail pesanan
     */
    public function show(Order $order)
    {
        $user = Auth::user();

        // Pastikan order milik user yang sedang login
        if ($order->user_id !== $user->id) {
            abort(404);
        }

        // Load relasi yang diperlukan
        $order->load(['items.product', 'items.variant']);

        return view('pages.orders.show', compact('order'));
    }

    /**
     * Update status pesanan (untuk admin)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,shipped,completed,failed,canceled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status pesanan berhasil diupdate');
    }

    /**
     * Cancel pesanan
     */
    public function cancel(Order $order)
    {
        $user = Auth::user();

        // Pastikan order milik user dan masih bisa dibatalkan
        if ($order->user_id !== $user->id || !in_array($order->status, ['pending', 'paid'])) {
            abort(403, 'Pesanan tidak dapat dibatalkan');
        }

        $order->update([
            'status' => 'canceled'
        ]);

        // Kembalikan stok produk jika diperlukan
        foreach ($order->items as $item) {
            if ($item->variant) {
                $item->variant->increment('stock', $item->qty);
            } elseif ($item->product) {
                $item->product->increment('stock', $item->qty);
            }
        }

        return redirect()->route('orders.index')
            ->with('success', 'Pesanan berhasil dibatalkan');
    }
}
