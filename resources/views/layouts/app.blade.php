<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('watchtower.branding.browser_title'))</title>
    <meta name="description" content="@yield('meta_description', config('watchtower.branding.meta.description'))">

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <meta name="theme-color" content="#0891b2" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0e7490" media="(prefers-color-scheme: dark)">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased transition-colors duration-300">
    <div id="app">
        @yield('content')
    </div>
</body>
</html>
