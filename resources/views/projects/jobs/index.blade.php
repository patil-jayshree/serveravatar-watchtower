@extends('layouts.app')

@section('title', 'Jobs - ' . $project->name . ' - Watchtower')

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
                <span>Jobs</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Jobs</h1>
        </div>

        {{-- Stats Overview --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Jobs</div>
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
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['running']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Running</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_duration'] }} ms</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Duration</div>
            </div>
        </div>

        {{-- Failed Jobs Summary --}}
        @if($failedJobsByName->isNotEmpty())
        <div class="bg-red-50 dark:bg-red-900/10 rounded-lg shadow mb-8 border border-red-200 dark:border-red-800 p-4">
            <h3 class="text-sm font-semibold text-red-900 dark:text-red-400 mb-3">Failed Jobs Summary</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($failedJobsByName as $failed)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                        {{ class_basename($failed->job_name) }}
                        <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-xs bg-red-200 dark:bg-red-800 text-red-900 dark:text-red-200">
                            {{ $failed->count }}
                        </span>
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('organizations.projects.jobs.index', [$organization, $project]) }}" class="p-4">
                <div class="flex flex-wrap gap-4">
                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Search job name..."
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" id="status" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="all">All Status</option>
                            <option value="queued" {{ ($filters['status'] ?? '') === 'queued' ? 'selected' : '' }}>Queued</option>
                            <option value="started" {{ ($filters['status'] ?? '') === 'started' ? 'selected' : '' }}>Running</option>
                            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ ($filters['status'] ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    {{-- Queue --}}
                    <div>
                        <label for="queue" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Queue</label>
                        <select name="queue" id="queue" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="all">All Queues</option>
                            @foreach($queues as $q)
                                <option value="{{ $q }}" {{ ($filters['queue'] ?? '') === $q ? 'selected' : '' }}>{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Connection --}}
                    <div>
                        <label for="connection" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Connection</label>
                        <select name="connection" id="connection" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="all">All Connections</option>
                            @foreach($connections as $conn)
                                <option value="{{ $conn }}" {{ ($filters['connection'] ?? '') === $conn ? 'selected' : '' }}>{{ $conn }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Time Range --}}
                    <div>
                        <label for="time_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Range</label>
                        <select name="time_range" id="time_range" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="all">All Time</option>
                            <option value="24h" {{ ($filters['time_range'] ?? '') === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                            <option value="7d" {{ ($filters['time_range'] ?? '') === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="30d" {{ ($filters['time_range'] ?? '') === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-indigo-600 text-white rounded-md px-4 py-2 text-sm font-medium hover:bg-indigo-700">
                            Filter
                        </button>
                        <a href="{{ route('organizations.projects.jobs.index', [$organization, $project]) }}" class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md px-4 py-2 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Job List --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            @if($jobs->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    No jobs found matching your filters.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Job
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Queue
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Attempts
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Timestamp
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($jobs as $job)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('organizations.projects.jobs.show', [$organization, $project, $job->uuid]) }}"
                                           class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                            {{ class_basename($job->job_name) }}
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                            {{ $job->job_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($job->status === 'completed')
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                Completed
                                            </span>
                                        @elseif($job->status === 'failed')
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                Failed
                                            </span>
                                        @elseif($job->status === 'started')
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                Running
                                            </span>
                                        @else
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400">
                                                Queued
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $job->queue ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $job->formatted_duration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $job->attempts }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $job->created_at->format('M j, Y H:i:s') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $jobs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
