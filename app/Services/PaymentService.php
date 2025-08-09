<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Get available payment methods
     */
    public function getPaymentMethods()
    {
        return [
            'cod' => [
                'name' => 'Cash on Delivery',
                'description' => 'Bayar saat barang diterima',
                'fee' => 0,
                'formatted_fee' => 'Gratis',
                'icon' => 'money-bill',
                'enabled' => true
            ],
            'bank_transfer' => [
                'name' => 'Transfer Bank',
                'description' => 'Transfer ke rekening bank',
                'fee' => 0,
                'formatted_fee' => 'Gratis',
                'icon' => 'university',
                'enabled' => true
            ],
            'midtrans' => [
                'name' => 'Kartu Kredit/Debit',
                'description' => 'Visa, Mastercard, dll via Midtrans',
                'fee' => 5000,
                'formatted_fee' => 'Rp 5.000',
                'icon' => 'credit-card',
                'enabled' => true
            ],
            'gopay' => [
                'name' => 'GoPay',
                'description' => 'Pembayaran via GoPay',
                'fee' => 2500,
                'formatted_fee' => 'Rp 2.500',
                'icon' => 'mobile-alt',
                'enabled' => true
            ],
            'ovo' => [
                'name' => 'OVO',
                'description' => 'Pembayaran via OVO',
                'fee' => 2500,
                'formatted_fee' => 'Rp 2.500',
                'icon' => 'mobile-alt',
                'enabled' => true
            ],
        ];
    }

    /**
     * Process payment berdasarkan method
     */
    public function processPayment(Order $order, array $paymentData)
    {
        $paymentMethod = $paymentData['payment_method'];

        try {
            switch ($paymentMethod) {
                case 'cod':
                    return $this->processCOD($order);

                case 'bank_transfer':
                    return $this->processBankTransfer($order);

                case 'midtrans':
                case 'gopay':
                case 'ovo':
                    return $this->processMidtrans($order, $paymentMethod);

                default:
                    return [
                        'success' => false,
                        'message' => 'Metode pembayaran tidak didukung'
                    ];
            }
        } catch (\Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process Cash on Delivery
     */
    private function processCOD(Order $order)
    {
        // COD tidak perlu payment processing, langsung success
        return [
            'success' => true,
            'message' => 'Pesanan berhasil dibuat dengan metode COD',
            'reference' => 'COD-' . $order->number,
            'status' => 'pending'
        ];
    }

    /**
     * Process Bank Transfer
     */
    private function processBankTransfer(Order $order)
    {
        // Untuk bank transfer, buat instruksi pembayaran
        $bankAccounts = $this->getBankAccounts();

        return [
            'success' => true,
            'message' => 'Silakan transfer ke salah satu rekening berikut',
            'reference' => 'BT-' . $order->number,
            'status' => 'pending',
            'bank_accounts' => $bankAccounts,
            'amount' => $order->grand_total
        ];
    }

    /**
     * Process payment via Midtrans
     */
    private function processMidtrans(Order $order, string $paymentType)
    {
        // Konfigurasi Midtrans
        $serverKey = config('services.midtrans.server_key');
        $isProduction = config('services.midtrans.is_production', false);

        $baseUrl = $isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        // Prepare transaction data
        $transactionData = [
            'transaction_details' => [
                'order_id' => $order->number,
                'gross_amount' => (int) $order->grand_total
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->shipping_phone
            ],
            'item_details' => $this->prepareItemDetails($order),
            'shipping_address' => [
                'first_name' => $order->shipping_name,
                'phone' => $order->shipping_phone,
                'address' => $order->shipping_street,
                'city' => $order->shipping_city,
                'postal_code' => $order->shipping_postal_code
            ]
        ];

        // Add payment type specific data
        if ($paymentType === 'gopay') {
            $transactionData['payment_type'] = 'gopay';
        } elseif ($paymentType === 'ovo') {
            $transactionData['payment_type'] = 'ovo';
        }

        // Call Midtrans API
        $response = Http::withBasicAuth($serverKey, '')
            ->post($baseUrl . '/charge', $transactionData);

        if ($response->successful()) {
            $result = $response->json();

            return [
                'success' => true,
                'message' => 'Payment berhasil diinisiasi',
                'reference' => $result['transaction_id'] ?? null,
                'status' => 'pending',
                'midtrans_response' => $result,
                'redirect_url' => $result['redirect_url'] ?? null,
                'qr_string' => $result['actions'][0]['url'] ?? null
            ];
        } else {
            throw new \Exception('Midtrans API Error: ' . $response->body());
        }
    }

    /**
     * Prepare item details untuk Midtrans
     */
    private function prepareItemDetails(Order $order)
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->qty,
                'name' => $item->product_name
            ];
        }

        // Add shipping cost as item
        if ($order->shipping_total > 0) {
            $items[] = [
                'id' => 'shipping',
                'price' => (int) $order->shipping_total,
                'quantity' => 1,
                'name' => 'Biaya Pengiriman'
            ];
        }

        return $items;
    }

    /**
     * Get bank accounts untuk transfer
     */
    private function getBankAccounts()
    {
        return [
            [
                'bank' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'NextShoes Store'
            ],
            [
                'bank' => 'Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'NextShoes Store'
            ],
            [
                'bank' => 'BRI',
                'account_number' => '5555666677',
                'account_name' => 'NextShoes Store'
            ]
        ];
    }

    /**
     * Handle payment notification (webhook)
     */
    public function handlePaymentNotification(array $notificationData)
    {
        $orderId = $notificationData['order_id'] ?? null;
        $transactionStatus = $notificationData['transaction_status'] ?? null;

        if (!$orderId) {
            return ['success' => false, 'message' => 'Order ID tidak ditemukan'];
        }

        $order = Order::where('number', $orderId)->first();
        if (!$order) {
            return ['success' => false, 'message' => 'Order tidak ditemukan'];
        }

        // Update order status berdasarkan notification
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $order->update(['status' => 'paid']);
                break;

            case 'pending':
                $order->update(['status' => 'pending']);
                break;

            case 'deny':
            case 'expire':
            case 'cancel':
                $order->update(['status' => 'failed']);
                break;
        }

        return ['success' => true, 'message' => 'Status berhasil diupdate'];
    }

    /**
     * Get payment fee
     */
    public function getPaymentFee(string $paymentMethod): int
    {
        $methods = $this->getPaymentMethods();
        return $methods[$paymentMethod]['fee'] ?? 0;
    }
}
