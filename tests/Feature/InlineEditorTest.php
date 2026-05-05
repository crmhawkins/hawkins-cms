<?php
use App\Models\Block;
use App\Models\Page;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('editor', 'web');
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('superadmin', 'web');
});

function makeTenant(string $id, string $name): Tenant
{
    return Tenant::withoutEvents(function () use ($id, $name) {
        return Tenant::create(['id' => $id, 'name' => $name]);
    });
}

test('unauthenticated user cannot call editor field endpoint', function () {
    $response = $this->patchJson('/edit/api/field', [
        'block_id' => 1, 'path' => 'title', 'value' => 'Test',
    ]);
    $response->assertUnauthorized();
});

test('authenticated editor can update block field', function () {
    $tenant = makeTenant('test-tenant', 'Test');
    $user = User::create([
        'tenant_id' => 'test-tenant',
        'name' => 'Editor',
        'email' => 'editor@test.com',
        'password' => bcrypt('password'),
    ]);
    $user->assignRole('editor');

    $page = Page::create([
        'tenant_id' => 'test-tenant',
        'title' => 'Test Page',
        'slug' => 'test',
        'status' => 'published',
    ]);

    $block = Block::create([
        'page_id' => $page->id,
        'tenant_id' => 'test-tenant',
        'type' => 'hero',
        'content' => ['title' => 'Old Title'],
        'sort' => 0,
    ]);

    $response = $this->actingAs($user)->patchJson('/edit/api/field', [
        'block_id' => $block->id,
        'path' => 'title',
        'value' => 'New Title',
    ]);

    $response->assertOk()->assertJsonPath('ok', true);
    expect($block->fresh()->content['title'])->toBe('New Title');
});

test('editor from different tenant cannot edit another tenants block', function () {
    $tenantA = makeTenant('tenant-a', 'A');
    $tenantB = makeTenant('tenant-b', 'B');

    $userB = User::create([
        'tenant_id' => 'tenant-b',
        'name' => 'Editor B',
        'email' => 'editorb@test.com',
        'password' => bcrypt('password'),
    ]);
    $userB->assignRole('editor');

    $pageA = Page::create(['tenant_id' => 'tenant-a', 'title' => 'Page A', 'slug' => 'page-a', 'status' => 'published']);
    $blockA = Block::create(['page_id' => $pageA->id, 'tenant_id' => 'tenant-a', 'type' => 'hero', 'content' => ['title' => 'A Title'], 'sort' => 0]);

    $response = $this->actingAs($userB)->patchJson('/edit/api/field', [
        'block_id' => $blockA->id,
        'path' => 'title',
        'value' => 'Hacked',
    ]);

    $response->assertForbidden();
    expect($blockA->fresh()->content['title'])->toBe('A Title');
});
