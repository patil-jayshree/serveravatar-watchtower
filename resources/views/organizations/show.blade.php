@extends('layouts.app')

@section('title', $organization->name)

@push('styles')
<style>
    .stat-card {
        @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6;
    }
</style>
@endpush

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Dashboard</a>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span>{{ $organization->name }}</span>
        </div>

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                @if($organization->logo_path)
                    <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}" class="w-16 h-16 rounded-xl object-cover">
                @else
                    <img src="{{ $organization->default_logo_url }}" alt="{{ $organization->name }}" class="w-16 h-16 rounded-xl object-cover">
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $organization->name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $organization->projects()->count() }} {{ Str::plural('project', $organization->projects()->count()) }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('organizations.settings', $organization) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
                <a href="{{ route('organizations.projects.index', $organization) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    View Projects
                </a>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-800 dark:text-green-200">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Projects Card --}}
            <a href="{{ route('organizations.projects.index', $organization) }}" class="stat-card hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Projects</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $organization->projects()->count() }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ $organization->projects()->where('is_connected', true)->count() }} connected
                        </p>
                    </div>
                    <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-4 text-sm text-primary-600 dark:text-primary-400 font-medium">View all projects →</p>
            </a>

            {{-- Created Date --}}
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white mt-2">{{ $organization->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $organization->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Owner --}}
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Owner</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white mt-2">{{ $organization->owner->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $organization->owner->email ?? '' }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Organization Details --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Organization Information</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Organization Name</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $organization->name }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Organization ID</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $organization->uuid }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Total Projects</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $organization->projects()->count() }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Connected Projects</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $organization->projects()->where('is_connected', true)->count() }}</dd>
                </div>
            </dl>
        </div>

        {{-- Recent Projects --}}
        @php
            $recentProjects = $organization->projects()->orderBy('created_at', 'desc')->limit(5)->get();
        @endphp
        @if($recentProjects->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Projects</h2>
                    <a href="{{ route('organizations.projects.index', $organization) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                        View all →
                    </a>
                </div>
                <div class="space-y-3">
                    @foreach($recentProjects as $project)
                        <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    @if($project->is_connected)
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    @endif
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->name }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                    {{ $project->environment }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $project->created_at->format('M d') }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
