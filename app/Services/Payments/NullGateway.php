<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Tenant;
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

    public function onboardTenant(Tenant $tenant): OnboardingLink
    {
        throw new \LogicException('No payment gateway configured');
    }

    public function offboardTenant(Tenant $tenant): void
    {
        throw new \LogicException('No payment gateway configured');
    }
}
