<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- ← add this line -->
        <title>@yield('title', 'My App')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @viteReactRefresh
        @vite('resources/css/app.css')
    </head>
    <body class="font-sans antialiased dark:bg-[#030712] dark:text-white  ">
        <div>
            @yield('content')
        </div>
        @stack('scripts')
    </body>
</html>
