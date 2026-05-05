<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @stack('styles')
</head>
<body @auth @can('edit-content') data-editor="1" @endcan @endauth>

    @yield('content')

    @stack('scripts')

    @auth
        @can('edit-content')
            <script src="{{ asset('js/editor/overlay.js') }}"></script>
        @endcan
    @endauth
</body>
</html>
