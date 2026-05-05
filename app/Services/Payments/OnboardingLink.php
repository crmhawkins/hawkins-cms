<?php

namespace App\Services\Payments;

class OnboardingLink
{
    public function __construct(
        public readonly string $url,
        public readonly string $provider,
    ) {}
}
