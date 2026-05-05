<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'id' => 'tenant-' . Str::random(8),
            'name' => fake()->company(),
            'theme' => 'sanzahra',
            'ecommerce_enabled' => false,
            'header_layout' => 'center',
            'payment_gateway' => 'none',
            'plan' => 'basic',
        ];
    }
}
