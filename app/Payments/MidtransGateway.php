<?php

namespace App\Payments;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MidtransGateway implements PaymentGateway
{
    public function createPayment(Order $order): PaymentData
    {
        // Mock implementation - replace with actual Midtrans API calls
        $reference = 'MT-' . $order->number . '-' . Str::random(8);

        // Simulate API call to Midtrans
        $mockResponse = [
            'token' => Str::random(32),
            'redirect_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$reference}",
        ];

        return new PaymentData(
            reference: $reference,
            redirectUrl: $mockResponse['redirect_url'],
            instructions: [
                'Klik tombol "Bayar Sekarang" untuk melanjutkan ke halaman pembayaran',
                'Pilih metode pembayaran yang diinginkan',
                'Ikuti instruksi pembayaran yang diberikan',
                'Pembayaran akan dikonfirmasi secara otomatis'
            ],
            status: 'pending'
        );
    }

    public function handleCallback(Request $request): CallbackData
    {
        // Mock callback handling - replace with actual Midtrans verification
        $reference = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');

        $status = match ($transactionStatus) {
            'settlement', 'capture' => 'paid',
            'pending' => 'pending',
            'deny', 'cancel', 'expire' => 'failed',
            default => 'pending'
        };

        return new CallbackData(
            reference: $reference,
            status: $status,
            data: $request->all()
        );
    }

    public function getPaymentStatus(string $reference): string
    {
        // Mock status check - replace with actual Midtrans API call
        $statuses = ['pending', 'paid', 'failed'];
        return $statuses[array_rand($statuses)];
    }
}
