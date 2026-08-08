@extends('layouts.guest')

@push('meta_description', 'A powerful observability platform for modern development teams. Monitor requests, track exceptions, analyze database queries, and gain AI-powered insights.')

@section('content')

    {{-- ========================================
         HERO SECTION
    ======================================== --}}
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-b from-primary-50/50 via-white to-white dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        {{-- Background Decoration --}}
        <div class="absolute inset-0 overflow-hidden">
            {{-- Gradient Orbs --}}
            <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-primary-200/30 dark:bg-primary-900/20 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-primary-100/40 dark:bg-primary-800/10 blur-3xl"></div>
            {{-- Grid Pattern --}}
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#00000008_1px,transparent_1px),linear-gradient(to_bottom,#00000008_1px,transparent_1px)] bg-[size:3rem_3rem] dark:bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)]"></div>
        </div>

        <x-container class="relative py-20 lg:py-32">
            <div class="text-center max-w-4xl mx-auto animate-fade-in-up">
                {{-- Logo --}}
                <div class="mb-8 flex justify-center">
                    <div class="relative">
                        <img
                            src="{{ config('watchtower.logos.dark') }}"
                            alt="{{ config('watchtower.application_name') }}"
                            class="h-16 hidden dark:block mx-auto transition-all duration-500"
                        >
                        <img
                            src="{{ config('watchtower.logos.light') }}"
                            alt="{{ config('watchtower.application_name') }}"
                            class="h-16 dark:hidden mx-auto transition-all duration-500"
                        >
                    </div>
                </div>

                {{-- Product Name --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-4 tracking-tight">
                    {{ config('watchtower.application_name') }}
                </h1>

                {{-- Tagline --}}
                <p class="text-xl sm:text-2xl text-primary-600 dark:text-primary-400 font-semibold mb-6">
                    Monitor. Debug. Ship.
                </p>

                {{-- Description --}}
                <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                    A powerful observability platform built for modern development teams.
                    Track exceptions, monitor requests, analyze database queries,
                    and gain AI-powered insights — all in one place.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <x-btn.primary href="{{ route('login') }}" class="text-base px-8 py-4 w-full sm:w-auto">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Get Started Free
                    </x-btn.primary>
                    <x-btn.secondary href="{{ route('register') }}" class="text-base px-8 py-4 w-full sm:w-auto">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Account
                    </x-btn.secondary>
                </div>

                {{-- Scroll Indicator --}}
                <div class="mt-16 animate-bounce">
                    <svg class="w-6 h-6 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
            </div>
        </x-container>
    </section>

    {{-- ========================================
         FEATURES PREVIEW SECTION
    ======================================== --}}
    <x-section class="bg-gray-50 dark:bg-gray-800/50" id="features">
        <x-container>
            {{-- Section Header --}}
            <div class="text-center mb-16 animate-fade-in-up">
                <x-badge variant="info" class="mb-4">Features</x-badge>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Everything you need to ship with confidence
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Get complete visibility into your application's health, performance, and errors.
                </p>
            </div>

            {{-- Feature Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                {{-- Request Monitoring --}}
                <x-feature-card
                    title="Request Monitoring"
                    description="Track every HTTP request with timing, status codes, and payload inspection. Identify slow endpoints and bottlenecks instantly."
                    class="animate-fade-in-up stagger-1"
                >
                    <x-slot name="icon">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </x-slot>
                </x-feature-card>

                {{-- Exception Tracking --}}
                <x-feature-card
                    title="Exception Tracking"
                    description="Capture and organize exceptions with full stack traces. Get notified immediately when errors occur."
                    class="animate-fade-in-up stagger-2"
                >
                    <x-slot name="icon">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </x-slot>
                </x-feature-card>

                {{-- Database Queries --}}
                <x-feature-card
                    title="Database Queries"
                    description="Monitor query performance, identify N+1 problems, and optimize your database interactions."
                    class="animate-fade-in-up stagger-3"
                >
                    <x-slot name="icon">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </x-slot>
                </x-feature-card>

                {{-- Queue Monitoring --}}
                <x-feature-card
                    title="Queue Monitoring"
                    description="Track background job performance, failed jobs, and queue depth in real-time."
                    class="animate-fade-in-up stagger-4"
                >
                    <x-slot name="icon">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </x-slot>
                </x-feature-card>

                {{-- Performance Insights --}}
                <x-feature-card
                    title="Performance Insights"
                    description="Analyze application performance trends, track core Web Vitals, and optimize user experience."
                    class="animate-fade-in-up stagger-5"
                >
                    <x-slot name="icon">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </x-slot>
                </x-feature-card>

                {{-- AI Analysis --}}
                <x-feature-card
                    title="AI Analysis"
                    description="Get intelligent insights powered by AI. Automatic root cause analysis and smart recommendations."
                    class="animate-fade-in-up stagger-6"
                >
                    <x-slot name="icon">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </x-slot>
                </x-feature-card>
            </div>
        </x-container>
    </x-section>

    {{-- ========================================
         PRODUCT PREVIEW SECTION
    ======================================== --}}
    <x-section class="bg-white dark:bg-gray-900" id="preview">
        <x-container>
            {{-- Section Header --}}
            <div class="text-center mb-16">
                <x-badge variant="info" class="mb-4">Preview</x-badge>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Beautiful, intuitive dashboard
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    A modern dashboard designed for developers. Get all your insights at a glance.
                </p>
            </div>

            {{-- Dashboard Mockup --}}
            <div class="relative max-w-5xl mx-auto">
                {{-- Browser Frame --}}
                <div class="rounded-xl overflow-hidden shadow-2xl border border-gray-200 dark:border-gray-700 bg-gray-900">
                    {{-- Browser Header --}}
                    <div class="flex items-center gap-2 px-4 py-3 bg-gray-800 border-b border-gray-700">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="flex-1 mx-4">
                            <div class="bg-gray-700 rounded-md px-3 py-1 text-xs text-gray-400 text-center">
                                app.watchtower.dev
                            </div>
                        </div>
                    </div>

                    {{-- Dashboard Content --}}
                    <div class="relative aspect-video bg-gradient-to-br from-gray-900 via-primary-900/20 to-gray-900 p-6 overflow-hidden">
                        {{-- Decorative Grid --}}
                        <div class="absolute inset-0 bg-[linear-gradient(to_right,#0891b210_1px,transparent_1px),linear-gradient(to_bottom,#0891b210_1px,transparent_1px)] bg-[size:2rem_2rem]"></div>

                        {{-- Mock Dashboard UI --}}
                        <div class="relative z-10 h-full flex gap-4">
                            {{-- Sidebar --}}
                            <div class="w-12 h-full bg-gray-800/80 rounded-lg border border-gray-700/50 flex flex-col items-center py-4 gap-4">
                                <div class="w-8 h-8 rounded-md bg-primary-500/20 flex items-center justify-center">
                                    <div class="w-4 h-4 rounded bg-primary-500"></div>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-gray-700/50 flex items-center justify-center text-gray-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-gray-700/50 flex items-center justify-center text-gray-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-gray-700/50 flex items-center justify-center text-gray-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                </div>
                            </div>

                            {{-- Main Content --}}
                            <div class="flex-1 flex flex-col gap-4">
                                {{-- Top Stats --}}
                                <div class="flex gap-4">
                                    <div class="flex-1 bg-gray-800/80 rounded-lg border border-gray-700/50 p-4">
                                        <div class="text-xs text-gray-500 mb-1">Total Requests</div>
                                        <div class="text-2xl font-bold text-white">1.2M</div>
                                        <div class="text-xs text-green-400 mt-1">↑ 12.5%</div>
                                    </div>
                                    <div class="flex-1 bg-gray-800/80 rounded-lg border border-gray-700/50 p-4">
                                        <div class="text-xs text-gray-500 mb-1">Errors</div>
                                        <div class="text-2xl font-bold text-white">23</div>
                                        <div class="text-xs text-red-400 mt-1">↓ 8.2%</div>
                                    </div>
                                    <div class="flex-1 bg-gray-800/80 rounded-lg border border-gray-700/50 p-4">
                                        <div class="text-xs text-gray-500 mb-1">Avg Response</div>
                                        <div class="text-2xl font-bold text-white">145ms</div>
                                        <div class="text-xs text-green-400 mt-1">↑ 23ms</div>
                                    </div>
                                </div>

                                {{-- Chart Area --}}
                                <div class="flex-1 bg-gray-800/80 rounded-lg border border-gray-700/50 p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="text-sm font-medium text-white">Request Timeline</div>
                                        <div class="flex gap-2">
                                            <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        </div>
                                    </div>
                                    {{-- Simplified Chart --}}
                                    <div class="h-full flex items-end gap-1">
                                        @for($i = 0; $i < 24; $i++)
                                            <div class="flex-1 bg-gradient-to-t from-primary-600 to-primary-400 rounded-t" style="height: {{ rand(20, 100) }}%"></div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Floating Cards --}}
                <div class="absolute -left-4 top-1/4 hidden lg:block animate-fade-in">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-3">
                        <div class="flex items-center gap-2 text-xs">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">All systems operational</span>
                        </div>
                    </div>
                </div>

                <div class="absolute -right-4 bottom-1/4 hidden lg:block animate-fade-in">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Avg Response Time</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">145ms</div>
                    </div>
                </div>
            </div>
        </x-container>
    </x-section>

    {{-- ========================================
         WHY WATCHTOWER SECTION
    ======================================== --}}
    <x-section class="bg-gray-50 dark:bg-gray-800/50" id="why">
        <x-container>
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                {{-- Content --}}
                <div class="animate-fade-in-up">
                    <x-badge variant="info" class="mb-4">Why Watchtower</x-badge>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                        Built for developers who care about reliability
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                        ServerAvatar Watchtower gives you complete visibility into your application's
                        health — so you can catch issues before they become outages and ship with confidence.
                    </p>

                    {{-- Benefits List --}}
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                    Instant Notifications
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Get notified immediately via Slack, Email, or Discord when issues arise.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                    Zero Configuration
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Works out of the box with Laravel applications. Just install and go.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                    AI-Powered Insights
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Let AI analyze patterns and provide actionable recommendations.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Visual --}}
                <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="relative">
                        {{-- Stats Cards --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-1">99.9%</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Uptime SLA</div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mt-8">
                                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-1">&lt;50ms</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">SDK Overhead</div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-1">50K+</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Requests/min</div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mt-8">
                                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-1">24/7</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Support</div>
                            </div>
                        </div>

                        {{-- Decorative Element --}}
                        <div class="absolute -z-10 -inset-4 bg-gradient-to-r from-primary-200 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/20 rounded-3xl blur-xl opacity-50"></div>
                    </div>
                </div>
            </div>
        </x-container>
    </x-section>

    {{-- ========================================
         CTA SECTION
    ======================================== --}}
    <x-section class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 dark:from-primary-800 dark:via-primary-900 dark:to-primary-950 relative overflow-hidden">
        {{-- Background Decoration --}}
        <div class="absolute inset-0">
            <div class="absolute top-0 left-1/4 w-96 h-96 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 rounded-full bg-white/5 blur-3xl"></div>
        </div>

        <x-container class="relative text-center">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6 animate-fade-in-up">
                Ready to ship with confidence?
            </h2>
            <p class="text-lg sm:text-xl text-primary-100 mb-10 max-w-2xl mx-auto animate-fade-in-up" style="animation-delay: 0.1s;">
                Start monitoring your applications in minutes. No credit card required.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.2s;">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-primary-600 bg-white hover:bg-gray-100 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Free Account
                </a>
                <a href="#" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-semibold text-white border-2 border-white/30 hover:border-white/50 hover:bg-white/10 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Watch Demo
                </a>
            </div>
        </x-container>
    </x-section>

@endsection
