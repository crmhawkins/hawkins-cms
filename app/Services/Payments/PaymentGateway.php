<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function createCheckoutSession(Order $order, array $options = []): CheckoutSession;
    public function handleWebhook(Request $request): WebhookResult;
    public function getThreeDsRedirectUrl(Order $order): ?string;
    public function refund(Order $order, int $amountCents): RefundResult;
}
