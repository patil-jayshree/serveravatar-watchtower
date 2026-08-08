<?php

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Route;

?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    {{-- Prevent theme flash --}}
    <script>
        (function() {
            const stored = localStorage.getItem('watchtower_theme');
            const system = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = stored || system;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    {{-- SEO Meta Tags --}}
    <title>@yield('title', 'Authentication')</title>
    <meta name="description" content="ServerAvatar Watchtower authentication">
    <meta name="author" content="ServerAvatar">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Theme Color --}}
    <meta name="theme-color" content="#0891b2" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0e7490" media="(prefers-color-scheme: dark)">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased transition-colors duration-300">

    <div class="min-h-screen flex flex-col">
        {{-- Navbar --}}
        <nav class="fixed top-0 left-0 right-0 z-50 glass border-b border-gray-200/50 dark:border-gray-800/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    {{-- Logo & Brand --}}
                    <a href="/" class="flex items-center gap-0">
                        <img
                            src="{{ config('watchtower.logos.dark') }}"
                            alt="ServerAvatar Watchtower"
                            class="h-14 w-auto hidden dark:block transition-all duration-300"
                        >
                        <img
                            src="{{ config('watchtower.logos.light') }}"
                            alt="ServerAvatar Watchtower"
                            class="h-14 w-auto dark:hidden transition-all duration-300"
                        >
                        <div class="flex flex-col leading-tight -ml-2">
                            <span class="text-xl font-bold text-gray-900 dark:text-white">Server<span class="text-primary-600 dark:text-primary-400">Avatar</span></span>
                            <div class="flex items-center gap-2">
                                <span class="h-px flex-1 bg-primary-600 dark:bg-primary-400"></span>
                                <span class="text-xs font-bold text-primary-600 dark:text-primary-400 tracking-widest">WATCHTOWER</span>
                                <span class="h-px flex-1 bg-primary-600 dark:bg-primary-400"></span>
                            </div>
                        </div>
                    </a>

                    {{-- Theme Toggle --}}
                    <x-theme-toggle />
                </div>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 pt-24">
            <div class="w-full max-w-md space-y-8">
                @yield('content')
            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-gray-100 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} ServerAvatar Watchtower. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>

</body>
</html>
