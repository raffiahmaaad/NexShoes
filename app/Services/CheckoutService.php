<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutService
{
    /**
     * Get shipping methods
     */
    public function getShippingMethods()
    {
        return [
            'regular' => [
                'name' => 'Pengiriman Reguler',
                'description' => '3-5 hari kerja',
                'price' => 15000,
                'formatted_price' => 'Rp 15.000'
            ],
            'express' => [
                'name' => 'Pengiriman Express',
                'description' => '1-2 hari kerja',
                'price' => 25000,
                'formatted_price' => 'Rp 25.000'
            ],
            'same_day' => [
                'name' => 'Same Day',
                'description' => 'Hari yang sama',
                'price' => 35000,
                'formatted_price' => 'Rp 35.000'
            ]
        ];
    }

    /**
     * Get user addresses - sementara return empty array karena menggunakan Address model yang berbeda
     * Anda bisa membuat UserAddress model terpisah atau menggunakan Address yang ada
     */
    public function getUserAddresses($userId)
    {
        // Untuk sementara return array kosong
        // Nanti bisa disesuaikan dengan Address model yang sudah ada
        return [];

        // Atau jika ingin menggunakan Address model yang ada:
        // return Address::whereHas('order', function($query) use ($userId) {
        //     $query->where('user_id', $userId);
        // })->distinct('street_address')->get();
    }

    /**
     * Create order dari data checkout
     */
    public function createOrder($orderData)
    {
        try {
            DB::beginTransaction();

            // Get shipping address - untuk sementara buat dummy data
            // Karena struktur UserAddress belum disesuaikan dengan Address yang ada
            $shippingAddress = (object) [
                'name' => 'Nama Default',
                'phone' => '081234567890',
                'street' => 'Alamat Default',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40115'
            ];

            // Jika $orderData['shipping_address_id'] ada, bisa ambil dari database
            // $shippingAddress = UserAddress::find($orderData['shipping_address_id']);
            // if (!$shippingAddress) {
            //     throw new \Exception('Alamat pengiriman tidak ditemukan');
            // }

            // Get shipping cost
            $shippingMethods = $this->getShippingMethods();
            $shippingCost = $shippingMethods[$orderData['shipping_method']]['price'] ?? 0;

            // Calculate totals
            $subtotal = 0;
            foreach ($orderData['cart_items'] as $item) {
                $subtotal += $item['subtotal'];
            }

            $grandTotal = $subtotal + $shippingCost;

            // Create order
            $order = Order::create([
                'user_id' => $orderData['user_id'],
                'number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'currency' => 'IDR',
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'shipping_total' => $shippingCost,
                'grand_total' => $grandTotal,
                'payment_method' => $orderData['payment_method'],
                'shipping_method' => $orderData['shipping_method'],
                'shipping_name' => $shippingAddress->name,
                'shipping_phone' => $shippingAddress->phone,
                'shipping_street' => $shippingAddress->street,
                'shipping_city' => $shippingAddress->city,
                'shipping_province' => $shippingAddress->province,
                'shipping_postal_code' => $shippingAddress->postal_code,
                'notes' => $orderData['notes'],
            ]);

            // Create order items
            foreach ($orderData['cart_items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'variant_id' => $item['variant']->id ?? null,
                    'product_name' => $item['product']->name,
                    'price' => $item['price'],
                    'qty' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Kurangi stok
                if ($item['variant']) {
                    $item['variant']->decrement('stock', $item['quantity']);
                } else {
                    $item['product']->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();
            return $order;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating order: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get order by ID dengan validasi user
     */
    public function getOrderById($orderId, $userId)
    {
        return Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->with(['items.product', 'items.variant'])
            ->first();
    }

    /**
     * Update order payment info
     */
    public function updateOrderPayment($orderId, $paymentData)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return false;
        }

        $order->update([
            'payment_reference' => $paymentData['reference'] ?? null,
            'status' => $paymentData['status'] ?? 'pending'
        ]);

        return true;
    }

    /**
     * Get order status options
     */
    public function getOrderStatuses()
    {
        return [
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'failed' => 'Gagal',
            'canceled' => 'Dibatalkan'
        ];
    }

    /**
     * Calculate shipping cost berdasarkan method dan alamat
     */
    public function calculateShippingCost($shippingMethod, $addressId = null)
    {
        $shippingMethods = $this->getShippingMethods();

        if (!isset($shippingMethods[$shippingMethod])) {
            return 0;
        }

        // Bisa ditambahkan logika perhitungan berdasarkan alamat
        return $shippingMethods[$shippingMethod]['price'];
    }
}
