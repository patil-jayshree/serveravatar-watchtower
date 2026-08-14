@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .health-indicator {
        @apply inline-block w-3 h-3 rounded-full mr-2;
    }
    .health-healthy { @apply bg-green-500; }
    .health-warning { @apply bg-yellow-500; }
    .health-critical { @apply bg-red-500; }
    .health-no-data { @apply bg-gray-400; }
</style>
@endpush

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Welcome back, {{ $user->name }}!</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Time Range Selector --}}
                @if($selectedOrg)
                <select id="timeRange" onchange="window.location.href=this.value" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="{{ route('dashboard', ['range' => '1h']) }}" {{ $timeRange === '1h' ? 'selected' : '' }}>Last 1 Hour</option>
                    <option value="{{ route('dashboard', ['range' => '24h']) }}" {{ $timeRange === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                    <option value="{{ route('dashboard', ['range' => '7d']) }}" {{ $timeRange === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="{{ route('dashboard', ['range' => '30d']) }}" {{ $timeRange === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                </select>
                @endif

                {{-- Organization Switcher --}}
                @if($organizations->count() > 0)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ $selectedOrg ? $selectedOrg->name : 'Select Organization' }}
                            </span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50">
                            <div class="p-2">
                                @foreach($organizations as $org)
                                    <form method="POST" action="{{ route('organizations.switch', $org) }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ session('selected_organization_id') == $org->id ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                            <div class="flex items-center gap-3">
                                                @if($org->logo_path)
                                                    <img src="{{ $org->logo_url }}" alt="{{ $org->name }}" class="w-8 h-8 rounded-lg object-cover">
                                                @else
                                                    <img src="{{ $org->default_logo_url }}" alt="{{ $org->name }}" class="w-8 h-8 rounded-lg object-cover">
                                                @endif
                                                <div class="text-left">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $org->name }}</p>
                                                </div>
                                            </div>
                                            @if(session('selected_organization_id') == $org->id)
                                                <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 p-2">
                                <a href="{{ route('organizations.create') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Organization
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- No Organizations --}}
        @if($organizations->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No organizations yet</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Create your first organization to start monitoring your Laravel applications.</p>
                <div class="mt-6">
                    <a href="{{ route('organizations.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Create Organization
                    </a>
                </div>
            </div>
        {{-- Multiple Organizations but None Selected --}}
        @elseif(!$selectedOrg)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Select an Organization</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Choose an organization from the dropdown above to view its dashboard.</p>
            </div>
        @else
            {{-- Organization Dashboard --}}
            @php
                $header = $dashboardData['header'];
                $projectsSummary = $dashboardData['projects_summary'];
                $health = $dashboardData['health'];
                $requests = $dashboardData['requests'];
                $exceptions = $dashboardData['exceptions'];
                $jobs = $dashboardData['jobs'];
                $commands = $dashboardData['commands'];
                $scheduler = $dashboardData['scheduler'];
                $recentActivity = $dashboardData['recent_activity'];
                $projectsNeedingAttention = $dashboardData['projects_needing_attention'];
            @endphp

            {{-- Organization Header --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if($selectedOrg->logo_path)
                            <img src="{{ $selectedOrg->logo_url }}" alt="{{ $selectedOrg->name }}" class="w-12 h-12 rounded-xl object-cover">
                        @else
                            <img src="{{ $selectedOrg->default_logo_url }}" alt="{{ $selectedOrg->name }}" class="w-12 h-12 rounded-xl object-cover">
                        @endif
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $selectedOrg->name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $projectsSummary['connected'] }} of {{ $projectsSummary['total'] }} projects connected
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('organizations.show', $selectedOrg) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                            View Organization
                        </a>
                        <a href="{{ route('organizations.projects.index', $selectedOrg) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                            View Projects
                        </a>
                    </div>
                </div>
            </div>

            {{-- Health Status --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <span class="health-indicator health-{{ $health['color'] }}"></span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $health['label'] }}</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $health['description'] }}</p>

                @if(!empty($health['warnings']))
                    <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>Issues:</strong> {{ implode('; ', $health['warnings']) }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Projects Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Projects</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $projectsSummary['total'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Healthy</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $projectsSummary['healthy'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Warning</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $projectsSummary['warning'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Critical</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $projectsSummary['critical'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Connected</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $projectsSummary['connected'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">No Data</p>
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400 mt-1">{{ $projectsSummary['no_data'] }}</p>
                </div>
            </div>

            {{-- Monitoring Overview Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                {{-- Requests --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Requests</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($requests['total']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold {{ $requests['error_rate'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ $requests['error_rate'] }}%
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Error Rate</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $requests['avg_duration_ms'] ?? '—' }}{{ isset($requests['avg_duration_ms']) ? ' ms' : '' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Avg</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $requests['p95_duration_ms'] ?? '—' }}{{ isset($requests['p95_duration_ms']) ? ' ms' : '' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">P95</p>
                        </div>
                    </div>
                </div>

                {{-- Exceptions --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Exceptions</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <p class="text-2xl font-bold {{ $exceptions['open'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $exceptions['open'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Open</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exceptions['new'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">New</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $exceptions['resolved'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Resolved</p>
                        </div>
                    </div>
                </div>

                {{-- Jobs --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Jobs</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($jobs['total']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold {{ $jobs['failed'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $jobs['failed'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Failed</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $jobs['running'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Running</p>
                        </div>
                    </div>
                </div>

                {{-- Commands --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Commands</h3>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($commands['total']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold {{ $commands['failed'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">{{ $commands['failed'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Failed</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scheduler --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Scheduler</h3>
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $scheduler['total'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Runs</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $scheduler['healthy'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Successful</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold {{ $scheduler['failed'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $scheduler['failed'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Failed</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold {{ $scheduler['missed'] > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-900 dark:text-white' }}">{{ $scheduler['missed'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Missed</p>
                    </div>
                </div>
            </div>

            {{-- Projects Needing Attention --}}
            @if(count($projectsNeedingAttention) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Projects Needing Attention</h3>
                    <div class="space-y-2">
                        @foreach($projectsNeedingAttention as $project)
                            <a href="{{ route('organizations.projects.show', [$selectedOrg, $project['uuid']]) }}" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full {{ $project['health'] === 'critical' ? 'bg-red-500' : 'bg-yellow-500' }}"></span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $project['name'] }}</span>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $project['health'] === 'critical' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                    {{ ucfirst($project['health']) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recent Activity --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Recent Activity</h3>
                @if(empty($recentActivity))
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No recent activity</p>
                @else
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($recentActivity as $activity)
                            <div class="flex items-start gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $activity['severity'] === 'error' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30' }}">
                                    @if(str_contains($activity['type'], 'exception'))
                                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    @elseif(str_contains($activity['type'], 'job'))
                                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
