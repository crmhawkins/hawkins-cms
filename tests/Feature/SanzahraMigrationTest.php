<?php

use App\Models\Page;
use App\Models\Tenant;
use Database\Seeders\SanzahraTenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Events\TenantCreated;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Suppress tenant DB creation (single-DB row-scoped project)
    Event::fake([TenantCreated::class]);

    // Ensure the 'admin' role exists for the seeder
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
});

test('sanzahra seeder creates tenant with correct theme', function () {
    $this->seed(SanzahraTenantSeeder::class);
    $tenant = Tenant::find('sanzahra');
    expect($tenant)->not->toBeNull();
    expect($tenant->theme)->toBe('sanzahra');
});

test('sanzahra migration creates 13 pages', function () {
    $this->seed(SanzahraTenantSeeder::class);
    $tenant = Tenant::find('sanzahra');
    $count = Page::withoutGlobalScopes()
        ->where('tenant_id', $tenant->id)
        ->count();
    expect($count)->toBe(13);
});

test('all sanzahra pages have at least one block', function () {
    $this->seed(SanzahraTenantSeeder::class);
    $tenant = Tenant::find('sanzahra');
    $pages = Page::withoutGlobalScopes()
        ->where('tenant_id', $tenant->id)
        ->with('blocks')
        ->get();
    foreach ($pages as $page) {
        expect($page->blocks->count())->toBeGreaterThan(0, "Page {$page->slug} has no blocks");
    }
});

test('sanzahra home page slug is home', function () {
    $this->seed(SanzahraTenantSeeder::class);
    $tenant = Tenant::find('sanzahra');
    $home = Page::withoutGlobalScopes()
        ->where('tenant_id', $tenant->id)
        ->where('slug', 'home')
        ->first();
    expect($home)->not->toBeNull();
});
