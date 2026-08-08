<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <title>@yield('title', config('watchtower.branding.browser_title'))</title>
    <meta name="description" content="@yield('meta_description', config('watchtower.branding.meta.description'))">
    <meta name="keywords" content="{{ config('watchtower.branding.meta.keywords') }}">
    <meta name="author" content="ServerAvatar">

    {{-- OpenGraph --}}
    <meta property="og:title" content="@yield('og_title', config('watchtower.branding.og.title'))">
    <meta property="og:description" content="@yield('og_description', config('watchtower.branding.og.description'))">
    <meta property="og:type" content="{{ config('watchtower.branding.og.type') }}">
    <meta property="og:image" content="{{ asset(config('watchtower.branding.og.image')) }}">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ config('watchtower.branding.twitter.card') }}">
    <meta name="twitter:site" content="{{ config('watchtower.branding.twitter.site') }}">
    <meta name="twitter:creator" content="{{ config('watchtower.branding.twitter.creator') }}">
    <meta name="twitter:title" content="@yield('twitter_title', config('watchtower.branding.og.title'))">
    <meta name="twitter:description" content="@yield('twitter_description', config('watchtower.branding.og.description'))">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Preload Logo --}}
    <link rel="preload" as="image" href="{{ asset('logos/brand-logo.png') }}">

    {{-- Theme Color --}}
    <meta name="theme-color" content="#0891b2" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0e7490" media="(prefers-color-scheme: dark)">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased transition-colors duration-300">

    {{-- Navbar --}}
    <nav class="fixed top-0 left-0 right-0 z-50 glass border-b border-gray-200/50 dark:border-gray-800/50">
        <x-container>
            <div class="flex items-center justify-between h-16">
                {{-- Logo & Brand --}}
                <a href="/" class="flex items-center gap-0">
                    <img
                        src="{{ config('watchtower.logos.dark') }}"
                        alt="{{ config('watchtower.application_name') }}"
                        class="h-20 w-auto hidden dark:block transition-opacity duration-500"
                    >
                    <img
                        src="{{ config('watchtower.logos.light') }}"
                        alt="{{ config('watchtower.application_name') }}"
                        class="h-20 w-auto dark:hidden transition-opacity duration-500"
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

                {{-- Right Side --}}
                <div class="flex items-center gap-3">
                    {{-- Theme Toggle --}}
                    <x-theme-toggle />

                    {{-- Auth Buttons --}}
                    <div class="hidden sm:flex items-center gap-2">
                        <x-btn.secondary href="{{ route('login') }}" class="!py-2 !px-4 text-sm">
                            Log in
                        </x-btn.secondary>
                        <x-btn.primary href="{{ route('register') }}" class="!py-2 !px-4 text-sm">
                            Register
                        </x-btn.primary>
                    </div>

                    {{-- Mobile Menu Button --}}
                    <button
                        type="button"
                        id="mobile-menu-btn"
                        class="sm:hidden p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        aria-label="Toggle menu"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div id="mobile-menu" class="hidden sm:hidden pb-4">
                <div class="flex flex-col gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-btn.secondary href="{{ route('login') }}" class="!w-full justify-center">
                        Log in
                    </x-btn.secondary>
                    <x-btn.primary href="{{ route('register') }}" class="!w-full justify-center">
                        Register
                    </x-btn.primary>
                </div>
            </div>
        </x-container>
    </nav>

    {{-- Main Content --}}
    <main class="pt-16 min-h-screen">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-100 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
        <x-container>
            <div class="py-12">
                <div class="flex flex-col items-center justify-center text-center gap-4">
                    {{-- Copyright --}}
                    <div class="flex flex-col items-center gap-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ config('watchtower.footer.copyright') }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Version {{ config('watchtower.version') }}
                        </p>
                    </div>

                    {{-- Links --}}
                    <div class="flex items-center gap-6">
                        @if(config('watchtower.footer.links.documentation'))
                            <a href="#" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                                Documentation
                            </a>
                        @endif
                        @if(config('watchtower.footer.links.privacy'))
                            <a href="#" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                                Privacy
                            </a>
                        @endif
                        @if(config('watchtower.footer.links.terms'))
                            <a href="#" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                                Terms
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </x-container>
    </footer>

    {{-- Mobile Menu Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            if (menuBtn && mobileMenu) {
                menuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>
