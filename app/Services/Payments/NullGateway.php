<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Http\Request;

class NullGateway implements PaymentGateway
{
    public function createCheckoutSession(Order $order, array $options = []): CheckoutSession
    {
        throw new \LogicException('No payment gateway configured');
    }

    public function handleWebhook(Request $request): WebhookResult
    {
        throw new \LogicException('No payment gateway configured');
    }

    public function getThreeDsRedirectUrl(Order $order): ?string
    {
        throw new \LogicException('No payment gateway configured');
    }

    public function refund(Order $order, int $amountCents): RefundResult
    {
        throw new \LogicException('No payment gateway configured');
    }
}
