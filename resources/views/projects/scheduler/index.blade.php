@extends('layouts.app')

@section('title', 'Scheduler - ' . $project->name . ' - Watchtower')

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
                        <span>Scheduler</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Scheduler</h1>
                </div>
            </div>
        </div>

        {{-- Stats Overview --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Tasks</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['healthy']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Healthy</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['running']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Running</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['failed']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Failed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($stats['missed']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Missed</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4">
            <form method="GET" action="{{ route('organizations.projects.scheduler.index', [$organization, $project]) }}"
                  class="flex flex-wrap gap-4 items-end">
                {{-- Search --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}"
                           placeholder="Task name..."
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All</option>
                        <option value="healthy" {{ $filters['status'] === 'healthy' ? 'selected' : '' }}>Healthy</option>
                        <option value="running" {{ $filters['status'] === 'running' ? 'selected' : '' }}>Running</option>
                        <option value="failed" {{ $filters['status'] === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="missed" {{ $filters['status'] === 'missed' ? 'selected' : '' }}>Missed</option>
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
                        <option value="all" {{ $filters['time_range'] === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="24h" {{ $filters['time_range'] === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                        <option value="7d" {{ $filters['time_range'] === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30d" {{ $filters['time_range'] === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('organizations.projects.scheduler.index', [$organization, $project]) }}"
                       class="ml-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Scheduler Tasks List --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            @if($tasks->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No scheduled tasks found</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        @if(request('search') || request('status') !== 'all' || request('time_range') !== 'all')
                            No tasks match your current filters. Try adjusting your search criteria.
                        @else
                            Your Laravel scheduler tasks will appear here once they are discovered from your connected application.
                        @endif
                    </p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Schedule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Timezone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Run</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Next Run</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($tasks as $task)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('organizations.projects.scheduler.show', [$organization, $project, $task->uuid]) }}"
                                       class="text-sm font-mono font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ $task->task_name }}
                                    </a>
                                    @if($task->task_type)
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            Type: {{ $task->task_type }}
                                            @if($task->task_type === 'command' && $task->command_name)
                                                → {{ $task->command_name }}
                                            @elseif($task->task_type === 'job' && $task->job_name)
                                                → {{ $task->job_name }}
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ $task->frequency }}
                                    </span>
                                    @if($task->expression)
                                        <div class="text-xs text-gray-400 dark:text-gray-500 font-mono mt-0.5">
                                            {{ $task->expression }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $task->timezone ?? 'UTC' }}
                                </td>
                                <td class="px-6 py-3">
                                    @php
                                        $statusDisplay = $task->status_display;
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        @if($statusDisplay['color'] === 'green') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($statusDisplay['color'] === 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($statusDisplay['color'] === 'red') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @elseif($statusDisplay['color'] === 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400 @endif">
                                        {{ $statusDisplay['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    @if($task->last_run_at)
                                        <span class="text-gray-900 dark:text-white">{{ $task->last_run_at->format('M j, H:i') }}</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">Never</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    @if($task->next_run_at)
                                        <span class="text-gray-900 dark:text-white">{{ $task->next_run_at->format('M j, H:i') }}</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($tasks->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $tasks->withQueryString()->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
