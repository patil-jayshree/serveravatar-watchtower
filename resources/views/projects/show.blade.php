@extends('layouts.app')

@section('title', $project->name)

@push('styles')
<style>
    .health-indicator {
        @apply inline-block w-3 h-3 rounded-full mr-2;
    }
    .health-healthy { @apply bg-green-500; }
    .health-warning { @apply bg-yellow-500; }
    .health-critical { @apply bg-red-500; }
    .health-no-data { @apply bg-gray-400; }

    .component-badge {
        @apply inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium;
    }
    .component-healthy { @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400; }
    .component-warning { @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400; }
    .component-critical { @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400; }

    .card {
        @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6;
    }

    .stat-value {
        @apply text-2xl font-bold text-gray-900 dark:text-white;
    }

    .stat-label {
        @apply text-sm text-gray-500 dark:text-gray-400;
    }

    .activity-item {
        @apply flex items-start gap-3 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0;
    }

    .activity-icon {
        @apply flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center;
    }
    .activity-icon-error { @apply bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400; }
    .activity-icon-warning { @apply bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400; }
    .activity-icon-info { @apply bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400; }
</style>
@endpush

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <a href="{{ route('organizations.show', $organization) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $organization->name }}</a>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('organizations.projects.index', $organization) }}" class="hover:text-gray-700 dark:hover:text-gray-300">Projects</a>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span>{{ $project->name }}</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h1>
            </div>

            {{-- Time Range & Settings --}}
            <div class="flex items-center gap-3">
                @if($project->is_connected)
                <select id="timeRange" onchange="window.location.href=this.value" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="{{ route('organizations.projects.show', [$organization, $project], false) }}?range=1h" {{ $timeRange === '1h' ? 'selected' : '' }}>Last 1 Hour</option>
                    <option value="{{ route('organizations.projects.show', [$organization, $project], false) }}?range=24h" {{ $timeRange === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                    <option value="{{ route('organizations.projects.show', [$organization, $project], false) }}?range=7d" {{ $timeRange === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="{{ route('organizations.projects.show', [$organization, $project], false) }}?range=30d" {{ $timeRange === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                </select>
                @endif
                <a href="{{ route('organizations.projects.edit', [$organization, $project]) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex items-center gap-1 mb-8 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.show') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Overview
            </a>
            <a href="{{ route('organizations.projects.agent.show', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.agent.*') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Agent
            </a>
            <a href="{{ route('organizations.projects.edit', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.edit') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Settings
            </a>
            @if($project->is_connected)
            <a href="{{ route('organizations.projects.requests.index', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.requests.*') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Requests
            </a>
            <a href="{{ route('organizations.projects.exceptions.index', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.exceptions.*') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Exceptions
            </a>
            <a href="{{ route('organizations.projects.queries.index', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.queries.*') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Queries
            </a>
            <a href="{{ route('organizations.projects.jobs.index', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.jobs.*') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Jobs
            </a>
            <a href="{{ route('organizations.projects.commands.index', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.commands.*') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Commands
            </a>
            <a href="{{ route('organizations.projects.logs.index', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.logs.*') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Logs
            </a>
            <a href="{{ route('organizations.projects.scheduler.index', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.scheduler.*') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Scheduler
            </a>
            <a href="{{ route('organizations.projects.performance.index', [$organization, $project]) }}" class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('organizations.projects.performance.*') ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Performance
            </a>
            @endif
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

        @php
            $overview = $overviewService->getOverview();
            $header = $overview['header'];
            $health = $overview['health'];
            $hasData = $overview['has_data'];
        @endphp

        {{-- Project Header Card --}}
        <div class="card mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $header['name'] }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                {{ $header['status'] }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-400">
                                {{ $header['environment'] }}
                            </span>
                            @if($project->is_connected)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Agent Connected
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                Not Connected
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    @if($header['last_activity'])
                        <p class="text-sm text-gray-500 dark:text-gray-400">Last activity: {{ $header['last_activity']['text'] }}</p>
                    @endif
                    @if($header['agent_version'])
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Agent v{{ $header['agent_version'] }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Application Health Card --}}
        <div class="card mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Application Health</h3>
            <div class="flex items-center gap-3">
                <span class="health-indicator health-{{ $health['color'] }}"></span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $health['label'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $health['description'] }}</p>

            @if(!empty($health['warnings']) && $health['status'] !== 'no_data')
                <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong>Warnings:</strong> {{ implode('; ', $health['warnings']) }}
                    </p>
                </div>
            @endif
        </div>

        @if(!$hasData && !$project->is_connected)
            {{-- Empty State - Not Connected --}}
            <div class="card mb-6">
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No telemetry data yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-4">
                        Connect your Laravel application using the Agent tab to start monitoring.
                    </p>
                    <a href="{{ route('organizations.projects.agent.show', [$organization, $project]) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Setup Agent Connection
                    </a>
                </div>
            </div>
        @else
            {{-- Monitoring Sections Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                {{-- Requests Overview --}}
                <a href="{{ route('organizations.projects.requests.index', [$organization, $project]) }}?range={{ $timeRange }}" class="card hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Requests</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="stat-value text-lg">{{ number_format($overview['requests']['total']) }}</p>
                            <p class="stat-label text-xs">Total</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg {{ $overview['requests']['error_rate'] > 0 ? 'text-red-600 dark:text-red-400' : '' }}">{{ $overview['requests']['error_rate'] }}%</p>
                            <p class="stat-label text-xs">Error Rate</p>
                        </div>
                        <div>
                            <p class="stat-value text-sm">{{ $overview['requests']['avg_duration_ms'] ?? '—' }}{{ isset($overview['requests']['avg_duration_ms']) ? ' ms' : '' }}</p>
                            <p class="stat-label text-xs">Avg</p>
                        </div>
                        <div>
                            <p class="stat-value text-sm">{{ $overview['requests']['p95_duration_ms'] ?? '—' }}{{ isset($overview['requests']['p95_duration_ms']) ? ' ms' : '' }}</p>
                            <p class="stat-label text-xs">P95</p>
                        </div>
                    </div>
                </a>

                {{-- Exceptions Overview --}}
                <a href="{{ route('organizations.projects.exceptions.index', [$organization, $project]) }}?range={{ $timeRange }}" class="card hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Exceptions</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <p class="stat-value text-lg {{ $overview['exceptions']['open'] > 0 ? 'text-red-600 dark:text-red-400' : '' }}">{{ $overview['exceptions']['open'] }}</p>
                            <p class="stat-label text-xs">Open</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg">{{ $overview['exceptions']['new'] }}</p>
                            <p class="stat-label text-xs">New</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg text-green-600 dark:text-green-400">{{ $overview['exceptions']['resolved'] }}</p>
                            <p class="stat-label text-xs">Resolved</p>
                        </div>
                    </div>
                    @if(!empty($overview['exceptions']['recent']))
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-2 space-y-1">
                            @foreach(array_slice($overview['exceptions']['recent'], 0, 2) as $exception)
                                <a href="{{ route('organizations.projects.exceptions.show', [$organization, $project, $exception['uuid']]) }}" class="block text-xs py-0.5 hover:text-primary-600 dark:hover:text-primary-400 truncate">
                                    <span class="font-mono text-red-600 dark:text-red-400">{{ $exception['exception_type'] }}</span>
                                    <span class="text-gray-400 ml-1">{{ $exception['time_ago'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </a>

                {{-- Performance Overview --}}
                <a href="{{ route('organizations.projects.performance.index', [$organization, $project]) }}?range={{ $timeRange }}" class="card hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Performance</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="stat-value text-sm">{{ $overview['performance']['avg_duration_ms'] ?? '—' }}{{ isset($overview['performance']['avg_duration_ms']) ? ' ms' : '' }}</p>
                            <p class="stat-label text-xs">Avg</p>
                        </div>
                        <div>
                            <p class="stat-value text-sm">{{ $overview['performance']['p95_duration_ms'] ?? '—' }}{{ isset($overview['performance']['p95_duration_ms']) ? ' ms' : '' }}</p>
                            <p class="stat-label text-xs">P95</p>
                        </div>
                        <div>
                            <p class="stat-value text-sm">{{ $overview['performance']['p99_duration_ms'] ?? '—' }}{{ isset($overview['performance']['p99_duration_ms']) ? ' ms' : '' }}</p>
                            <p class="stat-label text-xs">P99</p>
                        </div>
                        <div>
                            <p class="stat-value text-sm {{ $overview['performance']['slow_requests'] > 0 ? 'text-yellow-600 dark:text-yellow-400' : '' }}">
                                {{ $overview['performance']['slow_requests'] }}
                            </p>
                            <p class="stat-label text-xs">Slow</p>
                        </div>
                    </div>
                </a>

                {{-- Database Overview --}}
                <a href="{{ route('organizations.projects.queries.index', [$organization, $project]) }}?range={{ $timeRange }}" class="card hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Database</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="stat-value text-lg">{{ number_format($overview['database']['total']) }}</p>
                            <p class="stat-label text-xs">Queries</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg {{ $overview['database']['slow_queries'] > 0 ? 'text-red-600 dark:text-red-400' : '' }}">
                                {{ $overview['database']['slow_queries'] }}
                            </p>
                            <p class="stat-label text-xs">Slow</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Avg: {{ $overview['database']['avg_duration_ms'] ?? '—' }}{{ isset($overview['database']['avg_duration_ms']) ? ' ms' : '' }}
                            </p>
                        </div>
                    </div>
                </a>

                {{-- Jobs Overview --}}
                <a href="{{ route('organizations.projects.jobs.index', [$organization, $project]) }}?range={{ $timeRange }}" class="card hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Jobs</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <p class="stat-value text-lg">{{ number_format($overview['jobs']['total']) }}</p>
                            <p class="stat-label text-xs">Total</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg {{ $overview['jobs']['failed'] > 0 ? 'text-red-600 dark:text-red-400' : '' }}">
                                {{ $overview['jobs']['failed'] }}
                            </p>
                            <p class="stat-label text-xs">Failed</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg text-blue-600 dark:text-blue-400">{{ $overview['jobs']['running'] }}</p>
                            <p class="stat-label text-xs">Running</p>
                        </div>
                    </div>
                    @if(!empty($overview['jobs']['recent_failed']))
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-2 space-y-1">
                            @foreach(array_slice($overview['jobs']['recent_failed'], 0, 2) as $job)
                                <a href="{{ route('organizations.projects.jobs.show', [$organization, $project, $job['uuid']]) }}" class="block text-xs py-0.5 hover:text-primary-600 dark:hover:text-primary-400 truncate">
                                    <span class="text-red-600 dark:text-red-400">{{ $job['job_name'] }}</span>
                                    @if($job['failed_at'])
                                        <span class="text-gray-400 ml-1">{{ $job['failed_at'] }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </a>

                {{-- Commands Overview --}}
                <a href="{{ route('organizations.projects.commands.index', [$organization, $project]) }}?range={{ $timeRange }}" class="card hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Commands</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <p class="stat-value text-lg">{{ number_format($overview['commands']['total']) }}</p>
                            <p class="stat-label text-xs">Total</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg {{ $overview['commands']['failed'] > 0 ? 'text-red-600 dark:text-red-400' : '' }}">
                                {{ $overview['commands']['failed'] }}
                            </p>
                            <p class="stat-label text-xs">Failed</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg {{ $overview['commands']['slow'] > 0 ? 'text-yellow-600 dark:text-yellow-400' : '' }}">
                                {{ $overview['commands']['slow'] }}
                            </p>
                            <p class="stat-label text-xs">Slow</p>
                        </div>
                    </div>
                </a>

                {{-- Scheduler Overview --}}
                <a href="{{ route('organizations.projects.scheduler.index', [$organization, $project]) }}?range={{ $timeRange }}" class="card hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Scheduler</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-4 gap-1">
                        <div>
                            <p class="stat-value text-lg">{{ $overview['scheduler']['total'] }}</p>
                            <p class="stat-label text-xs">Tasks</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg text-green-600 dark:text-green-400">{{ $overview['scheduler']['healthy'] }}</p>
                            <p class="stat-label text-xs">Healthy</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg {{ $overview['scheduler']['failed'] > 0 ? 'text-red-600 dark:text-red-400' : '' }}">
                                {{ $overview['scheduler']['failed'] }}
                            </p>
                            <p class="stat-label text-xs">Failed</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg {{ $overview['scheduler']['missed'] > 0 ? 'text-yellow-600 dark:text-yellow-400' : '' }}">
                                {{ $overview['scheduler']['missed'] }}
                            </p>
                            <p class="stat-label text-xs">Missed</p>
                        </div>
                    </div>
                </a>

                {{-- Logs Overview --}}
                @php
                    $errorLogs = $project->logEvents()->whereIn('level', ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])->where('logged_at', '>=', $overviewService->getFrom())->count();
                    $warningLogs = $project->logEvents()->where('level', 'WARNING')->where('logged_at', '>=', $overviewService->getFrom())->count();
                @endphp
                <a href="{{ route('organizations.projects.logs.index', [$organization, $project]) }}?range={{ $timeRange }}" class="card hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Logs</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="stat-value text-lg {{ $errorLogs > 0 ? 'text-red-600 dark:text-red-400' : '' }}">{{ $errorLogs }}</p>
                            <p class="stat-label text-xs">Errors</p>
                        </div>
                        <div>
                            <p class="stat-value text-lg {{ $warningLogs > 0 ? 'text-yellow-600 dark:text-yellow-400' : '' }}">{{ $warningLogs }}</p>
                            <p class="stat-label text-xs">Warnings</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Bottom Row: Recent Activity & Slow Requests --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Recent Activity --}}
                <div class="card">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Recent Activity</h3>
                    @if(empty($overview['recent_activity']))
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No recent activity</p>
                    @else
                        <div class="space-y-0 max-h-64 overflow-y-auto">
                            @foreach($overview['recent_activity'] as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon {{ str_contains($activity['type'], 'failed') || str_contains($activity['type'], 'exception') || $activity['type'] === 'request_error' ? 'activity-icon-error' : 'activity-icon-info' }}">
                                        @if($activity['type'] === 'request_error')
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        @elseif(str_contains($activity['type'], 'exception'))
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif(str_contains($activity['type'], 'job'))
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4" />
                                            </svg>
                                        @elseif(str_contains($activity['type'], 'command'))
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3" />
                                            </svg>
                                        @elseif(str_contains($activity['type'], 'scheduler'))
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity['title'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $activity['subtitle'] }}</p>
                                    </div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">
                                        {{ $activity['time'] }}
                                        @php
                                            $detailRoute = match(true) {
                                                $activity['type'] === 'request_error' => route('organizations.projects.requests.show', [$organization, $project, $activity['uuid']]),
                                                str_contains($activity['type'], 'exception') => route('organizations.projects.exceptions.show', [$organization, $project, $activity['uuid']]),
                                                str_contains($activity['type'], 'job') => route('organizations.projects.jobs.show', [$organization, $project, $activity['uuid']]),
                                                str_contains($activity['type'], 'command') => route('organizations.projects.commands.show', [$organization, $project, $activity['uuid']]),
                                                str_contains($activity['type'], 'scheduler') => route('organizations.projects.scheduler.show', [$organization, $project, $activity['uuid']]),
                                                default => null
                                            };
                                        @endphp
                                        @if($detailRoute)
                                            <a href="{{ $detailRoute }}" class="ml-2 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">View</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Recent Slow Requests --}}
                <div class="card">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Slow Requests</h3>
                    @if(empty($overview['recent_slow_requests']))
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No slow requests</p>
                    @else
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($overview['recent_slow_requests'] as $request)
                                <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $request['uuid']]) }}" class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="px-1.5 py-0.5 text-xs font-mono font-medium rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                            {{ $request['method'] }}
                                        </span>
                                        <span class="text-sm text-gray-900 dark:text-gray-100 truncate">
                                            {{ $request['path'] }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                            {{ $request['duration_ms'] }} ms
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $request['time'] }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Component Health Summary --}}
            <div class="card">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Component Status</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($overview['component_health'] as $key => $component)
                        <span class="component-badge component-{{ $component['status'] }}">
                            <span class="w-2 h-2 rounded-full {{ $component['status'] === 'healthy' ? 'bg-green-500' : ($component['status'] === 'warning' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                            {{ $component['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Project Info (Always shown at bottom) --}}
        <div class="card mt-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Project Information</h3>
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Framework</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                            {{ $project->framework_enum?->label() ?? $project->framework ?? 'Laravel' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Environment</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                            {{ $project->environment_enum?->label() ?? $project->environment ?? 'Unknown' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                        @if($project->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">Inactive</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400 mb-1">Created</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
