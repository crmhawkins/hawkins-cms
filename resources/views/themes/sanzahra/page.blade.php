@extends('themes.sanzahra.layouts.app', ['title' => $page->title])

@section('content')
    @foreach ($page->blocks as $block)
        @includeIf("themes.sanzahra.blocks.{$block->type}", ['block' => $block])
    @endforeach
@endsection
