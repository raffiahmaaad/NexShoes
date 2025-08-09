<?php

namespace App\Payments;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function createPayment(Order $order): PaymentData;

    public function handleCallback(Request $request): CallbackData;

    public function getPaymentStatus(string $reference): string;
}

class PaymentData
{
    public function __construct(
        public string $reference,
        public string $redirectUrl,
        public array $instructions = [],
        public string $status = 'pending'
    ) {}
}

class CallbackData
{
    public function __construct(
        public string $reference,
        public string $status,
        public array $data = []
    ) {}
}
