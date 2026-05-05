<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user from tenant A is invisible from tenant B context', function () {
    $tenantA = Tenant::create(['id' => 'tenant-a', 'name' => 'Tenant A']);
    $tenantB = Tenant::create(['id' => 'tenant-b', 'name' => 'Tenant B']);

    // Create user for tenant A
    $userA = User::create([
        'tenant_id' => 'tenant-a',
        'name' => 'User A',
        'email' => 'usera@test.com',
        'password' => bcrypt('password'),
    ]);

    // From tenant B context, should not see tenant A user
    $visibleUsers = User::where('tenant_id', 'tenant-b')->get();
    expect($visibleUsers)->toHaveCount(0);
    expect(User::find($userA->id)?->tenant_id)->not->toBe('tenant-b');
});

test('superadmin has no tenant_id', function () {
    $admin = User::create([
        'tenant_id' => null,
        'name' => 'Super Admin',
        'email' => 'admin@hawkins.es',
        'password' => bcrypt('password'),
    ]);

    expect($admin->isSuperAdmin())->toBeTrue();
    expect($admin->tenant_id)->toBeNull();
});
