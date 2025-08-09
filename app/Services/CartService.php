<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Get atau create cart untuk user
     */
    public function getOrCreateCart($userId)
    {
        return Cart::firstOrCreate(
            ['user_id' => $userId],
            [
                'currency' => 'IDR',
                'subtotal' => 0,
                'discount_total' => 0,
                'shipping_total' => 0,
                'grand_total' => 0
            ]
        );
    }

    /**
     * Get semua item di keranjang
     */
    public function getCartItems($userId)
    {
        $cart = $this->getOrCreateCart($userId);

        return CartItem::where('cart_id', $cart->id)
            ->with(['product', 'variant'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => $item->product,
                    'variant' => $item->variant,
                    'price' => $item->price,
                    'quantity' => $item->qty,
                    'subtotal' => $item->subtotal,
                    'display_name' => $item->display_name,
                    'formatted_price' => $item->formatted_price,
                    'formatted_subtotal' => $item->formatted_subtotal,
                ];
            })->toArray();
    }

    /**
     * Get ringkasan keranjang
     */
    public function getCartSummary($userId)
    {
        $cart = $this->getOrCreateCart($userId);
        $cart->calculateTotals();

        return [
            'subtotal' => $cart->subtotal,
            'discount_total' => $cart->discount_total,
            'shipping_total' => $cart->shipping_total,
            'grand_total' => $cart->grand_total,
            'total_items' => $cart->total_items,
            'formatted_subtotal' => 'Rp ' . number_format($cart->subtotal, 0, ',', '.'),
            'formatted_grand_total' => 'Rp ' . number_format($cart->grand_total, 0, ',', '.'),
        ];
    }

    /**
     * Get jumlah item di keranjang
     */
    public function getCartCount($userId)
    {
        $cart = $this->getOrCreateCart($userId);
        return $cart->items->sum('qty');
    }

    /**
     * Tambah item ke keranjang - Updated signature for compatibility
     */
    public function addItem($productId, $quantity, $variantId = null, $userId = null)
    {
        try {
            // Jika $userId tidak diberikan, ambil dari auth user
            if (!$userId) {
                $userId = Auth::id();
            }

            // Jika masih null, return error
            if (!$userId) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                    'cart_count' => 0
                ];
            }

            DB::beginTransaction();

            $cart = $this->getOrCreateCart($userId);
            $product = Product::findOrFail($productId);

            // Validasi variant jika ada
            $variant = null;
            if ($variantId) {
                $variant = ProductVariant::where('product_id', $productId)
                    ->where('id', $variantId)
                    ->first();

                if (!$variant) {
                    return [
                        'success' => false,
                        'message' => 'Varian produk tidak ditemukan',
                        'cart_count' => $this->getCartCount($userId)
                    ];
                }
            }

            // Cek stok - menggunakan stock_quantity dari product model
            $availableStock = $variant ? $variant->stock : $product->stock_quantity;
            if ($quantity > $availableStock) {
                return [
                    'success' => false,
                    'message' => 'Stok tidak mencukupi',
                    'cart_count' => $this->getCartCount($userId)
                ];
            }

            // Cek apakah item sudah ada di keranjang
            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            // Gunakan sale_price jika produk sedang diskon
            $price = $variant ? $variant->price : (
                $product->on_sale && $product->sale_price ?
                $product->sale_price :
                $product->price
            );

            if ($existingItem) {
                // Update quantity jika item sudah ada
                $newQuantity = $existingItem->qty + $quantity;

                if ($newQuantity > $availableStock) {
                    return [
                        'success' => false,
                        'message' => 'Stok tidak mencukupi',
                        'cart_count' => $this->getCartCount($userId)
                    ];
                }

                $existingItem->update([
                    'qty' => $newQuantity,
                    'subtotal' => $price * $newQuantity
                ]);
            } else {
                // Buat item baru
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'price' => $price,
                    'qty' => $quantity,
                    'subtotal' => $price * $quantity
                ]);
            }

            $cart->calculateTotals();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke keranjang',
                'cart_count' => $this->getCartCount($userId)
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error adding item to cart: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan item',
                'cart_count' => $userId ? $this->getCartCount($userId) : 0
            ];
        }
    }

    /**
     * Update quantity item
     */
    public function updateQuantity($userId, $itemId, $quantity)
    {
        try {
            $cart = $this->getOrCreateCart($userId);

            $item = CartItem::where('cart_id', $cart->id)
                ->where('id', $itemId)
                ->first();

            if (!$item) {
                return ['success' => false, 'message' => 'Item tidak ditemukan'];
            }

            // Cek stok - menggunakan stock_quantity
            $availableStock = $item->variant ? $item->variant->stock : $item->product->stock_quantity;
            if ($quantity > $availableStock) {
                return ['success' => false, 'message' => 'Stok tidak mencukupi'];
            }

            $item->update([
                'qty' => $quantity,
                'subtotal' => $item->price * $quantity
            ]);

            $cart->calculateTotals();

            return ['success' => true, 'message' => 'Quantity berhasil diupdate'];

        } catch (\Exception $e) {
            Log::error('Error updating cart quantity: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat mengupdate quantity'];
        }
    }

    /**
     * Hapus item dari keranjang
     */
    public function removeItem($userId, $itemId)
    {
        try {
            $cart = $this->getOrCreateCart($userId);

            $item = CartItem::where('cart_id', $cart->id)
                ->where('id', $itemId)
                ->first();

            if (!$item) {
                return ['success' => false, 'message' => 'Item tidak ditemukan'];
            }

            $item->delete();
            $cart->calculateTotals();

            return ['success' => true, 'message' => 'Item berhasil dihapus'];

        } catch (\Exception $e) {
            Log::error('Error removing cart item: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menghapus item'];
        }
    }

    /**
     * Kosongkan keranjang
     */
    public function clearCart($userId)
    {
        try {
            $cart = $this->getOrCreateCart($userId);

            CartItem::where('cart_id', $cart->id)->delete();
            $cart->calculateTotals();

            return ['success' => true, 'message' => 'Keranjang berhasil dikosongkan'];

        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat mengosongkan keranjang'];
        }
    }

    /**
     * Validasi stok semua item di keranjang
     */
    public function validateStock($userId)
    {
        $cartItems = $this->getCartItems($userId);

        foreach ($cartItems as $item) {
            $availableStock = $item['variant']
                ? $item['variant']->stock
                : $item['product']->stock_quantity;

            if ($item['quantity'] > $availableStock) {
                return [
                    'valid' => false,
                    'message' => "Stok {$item['display_name']} tidak mencukupi. Tersedia: {$availableStock}"
                ];
            }
        }

        return ['valid' => true];
    }
}
