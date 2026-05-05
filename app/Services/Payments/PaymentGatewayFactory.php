<?php

namespace App\Services\Payments;

use App\Models\SiteSettings;

class PaymentGatewayFactory
{
    public static function make(): PaymentGateway
    {
        $settings = SiteSettings::instance();

        return match ($settings->payment_gateway) {
            'stripe_connect' => new StripeConnectGateway(),
            default => new NullGateway(),
        };
    }
}
