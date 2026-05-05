<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_successful_response(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
