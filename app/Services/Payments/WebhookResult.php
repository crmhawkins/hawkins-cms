<?php

namespace App\Services\Payments;

class WebhookResult
{
    public function __construct(
        public readonly bool $handled,
        public readonly string $event,
        public readonly ?string $orderId = null,
    ) {}
}
