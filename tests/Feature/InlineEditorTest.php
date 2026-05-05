<?php
use App\Models\Block;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('editor', 'web');
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('superadmin', 'web');
});

test('unauthenticated user cannot call editor field endpoint', function () {
    $response = $this->patchJson('/edit/api/field', [
        'block_id' => 1, 'path' => 'title', 'value' => 'Test',
    ]);
    $response->assertUnauthorized();
});

test('authenticated editor can update block field', function () {
    $user = User::create([
        'name' => 'Editor',
        'email' => 'editor@test.com',
        'password' => bcrypt('password'),
    ]);
    $user->assignRole('editor');

    $page = Page::create([
        'title' => 'Test Page',
        'slug' => 'test',
        'status' => 'published',
    ]);

    $block = Block::create([
        'page_id' => $page->id,
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
