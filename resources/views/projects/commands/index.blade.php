@extends('layouts.app')

@section('title', 'Commands - ' . $project->name . ' - Watchtower')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                        <a href="{{ route('organizations.show', $organization) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $organization->name }}</a>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $project->name }}</a>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        <span>Commands</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Commands</h1>
                </div>
            </div>
        </div>

        {{-- Stats Overview --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['completed']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['failed']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Failed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($stats['slow']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Slow</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_duration'] > 0 ? $stats['avg_duration'] . ' ms' : '—' }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Duration</div>
            </div>
        </div>

        {{-- Slow Commands --}}
        @if($slowCommands->isNotEmpty())
        <div class="bg-amber-50 dark:bg-amber-900/10 rounded-lg shadow mb-8 border border-amber-200 dark:border-amber-800 p-4">
            <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-400 mb-3">Slow Commands</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($slowCommands as $cmd)
                    <a href="{{ route('organizations.projects.commands.show', [$organization, $project, $cmd->uuid]) }}"
                       class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">
                        {{ $cmd->command_name }}
                        <span class="text-xs opacity-75">{{ $cmd->duration_formatted }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4">
            <form method="GET" action="{{ route('organizations.projects.commands.index', [$organization, $project]) }}"
                  class="flex flex-wrap gap-4 items-end">
                {{-- Search --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}"
                           placeholder="Command name..."
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All</option>
                        <option value="started" {{ $filters['status'] === 'started' ? 'selected' : '' }}>Running</option>
                        <option value="completed" {{ $filters['status'] === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ $filters['status'] === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                {{-- Exit Code --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Exit Code</label>
                    <select name="exit_code" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="all" {{ $filters['exit_code'] === 'all' ? 'selected' : '' }}>All</option>
                        <option value="0" {{ $filters['exit_code'] === '0' ? 'selected' : '' }}>Success (0)</option>
                        <option value="1" {{ $filters['exit_code'] === '1' ? 'selected' : '' }}>Failed (non-zero)</option>
                    </select>
                </div>

                {{-- Environment --}}
                @if($environments->isNotEmpty())
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Environment</label>
                    <select name="environment" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="all" {{ $filters['environment'] === 'all' ? 'selected' : '' }}>All</option>
                        @foreach($environments as $env)
                            <option value="{{ $env }}" {{ $filters['environment'] === $env ? 'selected' : '' }}>{{ $env }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Time Range --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Range</label>
                    <select name="time_range" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="1h" {{ $filters['time_range'] === '1h' ? 'selected' : '' }}>Last 1 Hour</option>
                        <option value="24h" {{ $filters['time_range'] === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                        <option value="7d" {{ $filters['time_range'] === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30d" {{ $filters['time_range'] === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('organizations.projects.commands.index', [$organization, $project]) }}"
                       class="ml-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Commands List --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            @if($commands->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No commands found</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        @if(request('search') || request('status') !== 'all' || request('time_range') !== 'all')
                            No commands match your current filters. Try adjusting your search criteria.
                        @else
                            Run some Artisan commands in your connected application to start seeing them here.
                        @endif
                    </p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Command</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Exit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Environment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($commands as $cmd)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-3">
                                    <a href="{{ route('organizations.projects.commands.show', [$organization, $project, $cmd->uuid]) }}"
                                       class="text-sm font-mono text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ $cmd->command_name }}
                                    </a>
                                    @if($cmd->options && count($cmd->options) > 0)
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ collect($cmd->options)->keys()->map(fn($k) => '--' . $k)->join(' ') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($cmd->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($cmd->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 @endif">
                                        {{ ucfirst($cmd->status) }}
                                    </span>
                                    @if($cmd->is_slow && $cmd->status === 'completed')
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                            slow
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $cmd->duration_formatted }}
                                </td>
                                <td class="px-6 py-3">
                                    @if($cmd->exit_code !== null)
                                        <span class="text-sm font-mono {{ $cmd->exit_code === 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $cmd->exit_code }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $cmd->environment ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $cmd->created_at->format('M j, H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($commands->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $commands->withQueryString()->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
