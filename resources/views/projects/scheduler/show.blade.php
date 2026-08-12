@extends('layouts.app')

@section('title', $task->task_name . ' - Scheduler - ' . $project->name . ' - Watchtower')

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('organizations.show', $organization) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $organization->name }}</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $project->name }}</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('organizations.projects.scheduler.index', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">Scheduler</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="truncate max-w-[200px]">{{ $task->task_name }}</span>
            </div>
        </div>

        {{-- Task Overview Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white font-mono">{{ $task->task_name }}</h1>
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
                </div>
                @if($task->last_run_at)
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Last run: {{ $task->last_run_at->format('M j, Y H:i:s') }}
                    </div>
                @endif
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Schedule</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $task->frequency }}
                        </div>
                        @if($task->expression)
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">{{ $task->expression }}</div>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Timezone</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $task->timezone ?? 'UTC' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Task Type</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $task->task_type ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Environment</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $task->environment ?? '—' }}</div>
                    </div>
                </div>

                {{-- Next/Last Run --}}
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-6">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Next Run</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            @if($task->next_run_at)
                                {{ $task->next_run_at->format('M j, Y H:i') }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Last Run</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            @if($task->last_run_at)
                                {{ $task->last_run_at->format('M j, Y H:i') }}
                            @else
                                Never
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Command Link --}}
                @if($task->command_name && $commandEvent)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Command</div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white font-mono">{{ $task->command_name }}</div>
                        </div>
                        <a href="{{ route('organizations.projects.commands.show', [$organization, $project, $commandEvent->uuid]) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-sm font-medium rounded-md transition-colors">
                            View Command →
                        </a>
                    </div>
                </div>
                @endif

                {{-- Job Link --}}
                @if($task->job_name && $jobEvent)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Job</div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white font-mono">{{ $task->job_name }}</div>
                        </div>
                        <a href="{{ route('organizations.projects.jobs.show', [$organization, $project, $jobEvent->uuid]) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-sm font-medium rounded-md transition-colors">
                            View Job →
                        </a>
                    </div>
                </div>
                @endif

                {{-- Exception Link --}}
                @if($exceptionOccurrence)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Exception</div>
                            <div class="text-sm font-bold text-red-600 dark:text-red-400">{{ $exceptionOccurrence->exception_group->exception_type ?? $exceptionOccurrence->exception_class }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $exceptionOccurrence->message_preview }}</div>
                        </div>
                        <a href="{{ route('organizations.projects.exceptions.show', [$organization, $project, $exceptionOccurrence->exception_group_uuid]) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-700 dark:text-red-300 text-sm font-medium rounded-md transition-colors">
                            View Exception →
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Execution Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($executionStats['total']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Executions</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($executionStats['completed']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($executionStats['failed']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Failed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($executionStats['missed']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Missed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $executionStats['avg_duration'] > 0 ? $executionStats['avg_duration'] . ' ms' : '—' }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Duration</div>
            </div>
        </div>

        {{-- Execution History --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Execution History</h2>
            </div>

            {{-- Filters --}}
            <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <form method="GET" action="{{ route('organizations.projects.scheduler.show', [$organization, $project, $task->uuid]) }}"
                      class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                        <select name="status" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All</option>
                            <option value="completed" {{ $filters['status'] === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ $filters['status'] === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="missed" {{ $filters['status'] === 'missed' ? 'selected' : '' }}>Missed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Time Range</label>
                        <select name="time_range" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="7d" {{ $filters['time_range'] === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="30d" {{ $filters['time_range'] === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="all" {{ $filters['time_range'] === 'all' ? 'selected' : '' }}>All Time</option>
                        </select>
                    </div>
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors">
                        Filter
                    </button>
                </form>
            </div>

            @if($executions->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No executions found</h3>
                    <p class="text-gray-500 dark:text-gray-400">No execution history matches your filters.</p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expected</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Delay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Links</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($executions as $execution)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">
                                    @if($execution->expected_at)
                                        {{ $execution->expected_at->format('M j, H:i:s') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">
                                    @if($execution->started_at)
                                        {{ $execution->started_at->format('M j, H:i:s') }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @php $execStatus = $execution->status_display; @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($execStatus['color'] === 'green') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($execStatus['color'] === 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($execStatus['color'] === 'red') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @elseif($execStatus['color'] === 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400 @endif">
                                        {{ $execStatus['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $execution->duration_formatted }}
                                </td>
                                <td class="px-6 py-3 text-sm font-mono">
                                    @if($execution->delay_ms !== null && $execution->delay_ms > 0)
                                        <span class="text-yellow-600 dark:text-yellow-400">{{ $execution->delay_formatted }}</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($execution->command_uuid)
                                            <a href="{{ route('organizations.projects.commands.show', [$organization, $project, $execution->command_uuid]) }}"
                                               class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Command
                                            </a>
                                        @endif
                                        @if($execution->job_uuid)
                                            <a href="{{ route('organizations.projects.jobs.show', [$organization, $project, $execution->job_uuid]) }}"
                                               class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Job
                                            </a>
                                        @endif
                                        @if($execution->exception_uuid)
                                            <a href="{{ route('organizations.projects.exceptions.show', [$organization, $project, $execution->exceptionOccurrence?->exception_group_uuid]) }}"
                                               class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                Exception
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($executions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $executions->withQueryString()->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
