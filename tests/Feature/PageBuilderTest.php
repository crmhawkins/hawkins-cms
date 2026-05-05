<?php

use App\Models\Block;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('page can be created with blocks and viewed published', function () {
    $page = Page::create([
        'title'  => 'Home Page',
        'slug'   => 'home',
        'status' => 'published',
    ]);

    Block::create([
        'page_id' => $page->id,
        'type'    => 'hero',
        'content' => ['title' => 'Welcome', 'subtitle' => 'Hello world'],
        'sort'    => 0,
    ]);

    expect($page->blocks)->toHaveCount(1);
    expect($page->blocks->first()->type)->toBe('hero');
    expect($page->status)->toBe('published');
});

test('slug is unique site-wide', function () {
    Page::create([
        'title'  => 'About',
        'slug'   => 'about',
        'status' => 'draft',
    ]);

    expect(fn () => Page::create([
        'title'  => 'About 2',
        'slug'   => 'about',
        'status' => 'draft',
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('draft page is not returned when filtering by published status', function () {
    Page::create([
        'title'  => 'Hidden Page',
        'slug'   => 'hidden',
        'status' => 'draft',
    ]);

    $published = Page::where('status', 'published')->get();

    expect($published)->toHaveCount(0);
});

test('menu items can be created and ordered', function () {
    MenuItem::create(['label' => 'Home', 'url' => '/', 'sort' => 0]);
    MenuItem::create(['label' => 'About', 'url' => '/about', 'sort' => 1]);

    $items = MenuItem::orderBy('sort')->get();

    expect($items)->toHaveCount(2);
    expect($items->first()->label)->toBe('Home');
});
