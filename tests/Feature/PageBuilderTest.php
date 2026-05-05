<?php

use App\Models\Block;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('tenant can create page with blocks and view it published', function () {
    $tenant = Tenant::create(['id' => 'test-tenant', 'name' => 'Test Tenant']);

    $page = Page::create([
        'tenant_id' => 'test-tenant',
        'title'     => 'Home Page',
        'slug'      => 'home',
        'status'    => 'published',
    ]);

    $block = Block::create([
        'tenant_id' => 'test-tenant',
        'page_id'   => $page->id,
        'type'      => 'hero',
        'content'   => ['title' => 'Welcome', 'subtitle' => 'Hello world'],
        'sort'      => 0,
    ]);

    expect($page->blocks)->toHaveCount(1);
    expect($page->blocks->first()->type)->toBe('hero');
    expect($page->status)->toBe('published');
});

test('slug is unique per tenant but two tenants can share the same slug', function () {
    $tenantA = Tenant::create(['id' => 'slug-tenant-a', 'name' => 'Tenant A']);
    $tenantB = Tenant::create(['id' => 'slug-tenant-b', 'name' => 'Tenant B']);

    $pageA = Page::create([
        'tenant_id' => 'slug-tenant-a',
        'title'     => 'About',
        'slug'      => 'about',
        'status'    => 'draft',
    ]);

    $pageB = Page::create([
        'tenant_id' => 'slug-tenant-b',
        'title'     => 'About',
        'slug'      => 'about',
        'status'    => 'draft',
    ]);

    expect($pageA->slug)->toBe('about');
    expect($pageB->slug)->toBe('about');
    expect($pageA->tenant_id)->not->toBe($pageB->tenant_id);
});

test('draft page is not returned when filtering by published status', function () {
    $tenant = Tenant::create(['id' => 'draft-tenant', 'name' => 'Draft Tenant']);

    Page::create([
        'tenant_id' => 'draft-tenant',
        'title'     => 'Hidden Page',
        'slug'      => 'hidden',
        'status'    => 'draft',
    ]);

    $published = Page::where('tenant_id', 'draft-tenant')
        ->where('status', 'published')
        ->get();

    expect($published)->toHaveCount(0);
});

test('menu items are scoped to tenant', function () {
    $tenantA = Tenant::create(['id' => 'menu-tenant-a', 'name' => 'Menu A']);
    $tenantB = Tenant::create(['id' => 'menu-tenant-b', 'name' => 'Menu B']);

    MenuItem::create(['tenant_id' => 'menu-tenant-a', 'label' => 'Home A', 'url' => '/', 'sort' => 0]);
    MenuItem::create(['tenant_id' => 'menu-tenant-a', 'label' => 'About A', 'url' => '/about', 'sort' => 1]);
    MenuItem::create(['tenant_id' => 'menu-tenant-b', 'label' => 'Home B', 'url' => '/', 'sort' => 0]);

    $itemsA = MenuItem::where('tenant_id', 'menu-tenant-a')->orderBy('sort')->get();
    $itemsB = MenuItem::where('tenant_id', 'menu-tenant-b')->orderBy('sort')->get();

    expect($itemsA)->toHaveCount(2);
    expect($itemsB)->toHaveCount(1);
    expect($itemsA->first()->label)->toBe('Home A');
    expect($itemsB->first()->label)->toBe('Home B');
});
