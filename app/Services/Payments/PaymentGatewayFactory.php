<?php

namespace App\Services\Payments;

use App\Models\Tenant;

class PaymentGatewayFactory
{
    public static function for(Tenant $tenant): PaymentGateway
    {
        return match ($tenant->payment_gateway) {
            'stripe_connect' => new StripeConnectGateway(),
            default => new NullGateway(),
        };
    }
}
