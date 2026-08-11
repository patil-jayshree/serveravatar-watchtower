@extends('layouts.app')

@section('title', class_basename($job->job_name) . ' - ' . $project->name . ' - Watchtower')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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
                <a href="{{ route('organizations.projects.jobs.index', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">Jobs</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="truncate max-w-xs">{{ class_basename($job->job_name) }}</span>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Job Overview --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ class_basename($job->job_name) }}
                            </h2>
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
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                            {{ $job->uuid }}
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    {{-- Full Job Name --}}
                    <div class="mb-6">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Job Class</div>
                        <div class="text-sm font-mono text-gray-800 dark:text-gray-200 bg-gray-100 dark:bg-gray-900 rounded p-2">
                            {{ $job->job_name }}
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Status</div>
                            <div class="text-sm text-gray-900 dark:text-white">
                                @if($job->status === 'completed')
                                    <span class="text-green-600 dark:text-green-400">Completed</span>
                                @elseif($job->status === 'failed')
                                    <span class="text-red-600 dark:text-red-400">Failed</span>
                                @elseif($job->status === 'started')
                                    <span class="text-blue-600 dark:text-blue-400">Running</span>
                                @else
                                    <span class="text-gray-600 dark:text-gray-400">Queued</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Queue</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $job->queue ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Connection</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $job->connection ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Attempts</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $job->attempts }}</div>
                        </div>
                        @if($job->duration_ms !== null)
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Duration</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $job->formatted_duration }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Timing --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Timing</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @if($job->queued_at)
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Queued</div>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::createFromTimestamp($job->queued_at)->format('M j, Y H:i:s') }}
                                </div>
                            </div>
                        @endif
                        @if($job->started_at)
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Started</div>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::createFromTimestamp($job->started_at)->format('M j, Y H:i:s') }}
                                </div>
                            </div>
                        @endif
                        @if($job->completed_at)
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Completed</div>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::createFromTimestamp($job->completed_at)->format('M j, Y H:i:s') }}
                                </div>
                            </div>
                        @endif
                        @if($job->failed_at)
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Failed</div>
                                <div class="text-sm text-red-600 dark:text-red-400">
                                    {{ \Carbon\Carbon::createFromTimestamp($job->failed_at)->format('M j, Y H:i:s') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Environment --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Environment</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Environment</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ ucfirst($job->environment ?? '—') }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Laravel</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $job->laravel_version ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">PHP</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $job->php_version ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Agent</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $job->agent_version ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Request Correlation --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Correlation</h2>
                </div>
                <div class="p-6">
                    @if($relatedRequest)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Request ID</div>
                                <div class="text-sm font-mono text-gray-900 dark:text-white">{{ $relatedRequest->request_id }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Method</div>
                                <div class="text-sm text-gray-900 dark:text-white">{{ $relatedRequest->method }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Status</div>
                                <div class="text-sm">
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        @if($relatedRequest->status_code >= 500) bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @elseif($relatedRequest->status_code >= 400) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @else bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @endif">
                                        {{ $relatedRequest->status_code }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $relatedRequest->uuid]) }}"
                               class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                View Request →
                            </a>
                        </div>
                    @elseif($job->request_id)
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Request ID</div>
                            <div class="text-sm font-mono text-gray-900 dark:text-white">{{ $job->request_id }}</div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Related request not found (may have expired)</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium">Request:</span> Not available
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            This job was not dispatched from a monitored HTTP request.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Exception Summary (compact) - only for failed jobs --}}
            @if($job->isFailed() && $relatedExceptionGroup)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Exception</h2>
                            <span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                {{ class_basename($relatedExceptionGroup->exception_type) }}
                            </span>
                            <span class="px-2 py-1 rounded text-xs font-medium
                                {{ $relatedExceptionGroup->isOpen() ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' }}">
                                {{ ucfirst($relatedExceptionGroup->status) }}
                            </span>
                        </div>
                        <a href="{{ route('organizations.projects.exceptions.show', [$organization, $project, $relatedExceptionGroup->uuid]) }}"
                           class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                            View Full Exception
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($job->exception_message)
                            <p class="text-sm font-mono text-red-600 dark:text-red-400">
                                {{ $job->exception_message }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
