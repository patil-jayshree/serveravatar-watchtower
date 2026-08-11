@extends('layouts.app')

@section('title', 'Logs - ' . $project->name . ' - Watchtower')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('organizations.show', $organization) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $organization->name }}</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $project->name }}</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>Logs</span>
            </div>
        </div>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Total Logs</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Errors</div>
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['errors']) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Warnings</div>
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($stats['warnings']) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Last 24h</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['last_24h']) }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filters</h2>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('organizations.projects.logs.index', [$organization, $project]) }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    {{-- Search --}}
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                               placeholder="Search message, exception..."
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    {{-- Level --}}
                    <div>
                        <label for="level" class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Level</label>
                        <select name="level" id="level"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all">All Levels</option>
                            @foreach(['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'] as $level)
                                <option value="{{ $level }}" {{ ($filters['level'] ?? '') === $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Channel --}}
                    <div>
                        <label for="channel" class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Channel</label>
                        <select name="channel" id="channel"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all">All Channels</option>
                            @foreach($channels as $channel)
                                <option value="{{ $channel }}" {{ ($filters['channel'] ?? '') === $channel ? 'selected' : '' }}>{{ $channel }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Environment --}}
                    <div>
                        <label for="environment" class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Environment</label>
                        <select name="environment" id="environment"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all">All</option>
                            @foreach(['production', 'staging', 'development', 'local'] as $env)
                                <option value="{{ $env }}" {{ ($filters['environment'] ?? '') === $env ? 'selected' : '' }}>{{ ucfirst($env) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Time Range --}}
                    <div>
                        <label for="time_range" class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Time Range</label>
                        <select name="time_range" id="time_range"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all" {{ ($filters['time_range'] ?? 'all') === 'all' ? 'selected' : '' }}>All Time</option>
                            <option value="24h" {{ ($filters['time_range'] ?? '') === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                            <option value="7d" {{ ($filters['time_range'] ?? '') === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="30d" {{ ($filters['time_range'] ?? '') === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="md:col-span-6 flex items-center gap-2 pt-4">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                            Apply Filters
                        </button>
                        <a href="{{ route('organizations.projects.logs.index', [$organization, $project]) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Log List --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Logs
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
                        ({{ $logs->total() }} total)
                    </span>
                </h2>
            </div>

            @if($logs->isEmpty())
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No logs found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if(count(array_filter($filters)))
                            No logs match your current filters.
                        @else
                            No log events have been recorded for this project yet.
                        @endif
                    </p>
                </div>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($logs as $log)
                        <a href="{{ route('organizations.projects.logs.show', [$organization, $project, $log->uuid]) }}"
                           class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-start gap-4">
                                {{-- Level Badge --}}
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $log->getLevelBadgeClass() }}">
                                        {{ $log->level }}
                                    </span>
                                </div>

                                {{-- Message --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $log->message }}
                                    </p>
                                    <div class="mt-1 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                        @if($log->channel)
                                            <span class="font-mono">{{ $log->channel }}</span>
                                            <span>·</span>
                                        @endif
                                        @if($log->environment)
                                            <span>{{ ucfirst($log->environment) }}</span>
                                            <span>·</span>
                                        @endif
                                        @if($log->request_id)
                                            <span class="font-mono text-indigo-600 dark:text-indigo-400" title="Request ID">
                                                {{ Str::limit($log->request_id, 12) }}
                                            </span>
                                            <span>·</span>
                                        @endif
                                        <span>{{ $log->logged_at->format('M j, Y H:i:s') }}</span>
                                    </div>
                                </div>

                                {{-- Arrow --}}
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($logs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $logs->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
