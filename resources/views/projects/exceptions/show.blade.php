@extends('layouts.app')

@section('title', $group->exception_type . ' - Exceptions - ' . $project->name . ' - Watchtower')

@push('styles')
<style>
    .stack-frame {
        @apply font-mono text-sm bg-gray-75 dark:bg-gray-900 rounded p-3 mb-2;
    }
    .stack-frame-line {
        @apply text-red-600 dark:text-red-400;
    }
    .stack-file {
        @apply text-gray-500 dark:text-gray-400;
    }
    .code-block {
        @apply font-mono text-sm bg-gray-100 dark:bg-gray-900 rounded p-4 overflow-x-auto;
    }
    .metadata-label {
        @apply text-xs font-medium text-gray-500 dark:text-gray-400 uppercase;
    }
    .metadata-value {
        @apply text-sm text-gray-900 dark:text-gray-300;
    }
    .status-badge {
        @apply px-2 py-0.5 rounded text-xs font-medium;
    }
    .status-open {
        @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400;
    }
    .status-resolved {
        @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('organizations.projects.exceptions.index', [$organization, $project]) }}"
                       class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        ← Exceptions
                    </a>
                    <span class="status-badge {{ $group->isOpen() ? 'status-open' : 'status-resolved' }}">
                        {{ ucfirst($group->status) }}
                    </span>
                </div>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ class_basename($group->exception_type) }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $group->normalized_message ?? 'No message' }}
                </p>
            </div>
            <div class="flex gap-2">
                @if($group->isOpen())
                    <form method="POST"
                          action="{{ route('organizations.projects.exceptions.update-status', [$organization, $project, $group->uuid]) }}"
                          onsubmit="return confirm('Mark this exception as resolved?');">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="resolved">
                        <button type="submit"
                                class="bg-green-600 text-white rounded-md px-4 py-2 text-sm font-medium hover:bg-green-700">
                            Mark Resolved
                        </button>
                    </form>
                @else
                    <form method="POST"
                          action="{{ route('organizations.projects.exceptions.update-status', [$organization, $project, $group->uuid]) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="open">
                        <button type="submit"
                                class="bg-gray-600 text-white rounded-md px-4 py-2 text-sm font-medium hover:bg-gray-700">
                            Reopen
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $group->occurrence_count }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Occurrences</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-900 dark:text-white">
                {{ $group->first_seen_at->format('M j, Y H:i') }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">First Seen</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-900 dark:text-white">
                {{ $group->last_seen_at->format('M j, Y H:i') }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Last Seen</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $group->file }}">
                {{ Str::after($group->file, 'app/') ?? $group->file }}:{{ $group->line }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Location</div>
        </div>
    </div>

    <!-- Latest Occurrence Detail -->
    @if($group->latestOccurrence)
        @php
            $latest = $group->latestOccurrence;
            $latestRelatedRequest = $latest->getRelatedRequestEvent();
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Latest Occurrence</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $latest->occurred_at->format('M j, Y H:i:s') }}
                </p>
            </div>

            <!-- Source Context -->
            @if($latest->isFromJob() || $latest->hasRequest() || $latestRelatedRequest || $latest->isFromCommand())
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    @if($latest->isFromJob())
                        @php
                            $relatedJob = $latest->jobEvent;
                        @endphp
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                Source: Job
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <div class="metadata-label">Job</div>
                                <div class="metadata-value font-mono">{{ class_basename($relatedJob?->job_name ?? $latest->controller_action ?? 'UnknownJob') }}</div>
                            </div>
                            <div>
                                <div class="metadata-label">Job ID</div>
                                <div class="metadata-value font-mono">{{ $latest->job_uuid }}</div>
                            </div>
                            @if($latest->environment)
                                <div>
                                    <div class="metadata-label">Environment</div>
                                    <div class="metadata-value">{{ $latest->environment }}</div>
                                </div>
                            @endif
                        </div>
                        @if($relatedJob)
                            <div class="mt-3">
                                <a href="{{ route('organizations.projects.jobs.show', [$organization, $project, $relatedJob->uuid]) }}"
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    View Job →
                                </a>
                            </div>
                        @endif
                    @elseif($latest->isFromCommand())
                        @php
                            $relatedCommand = $latest->commandEvent;
                        @endphp
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                Source: Artisan Command
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <div class="metadata-label">Command</div>
                                <div class="metadata-value font-mono">{{ $relatedCommand?->command_name ?? $latest->controller_action ?? 'UnknownCommand' }}</div>
                            </div>
                            <div>
                                <div class="metadata-label">Command ID</div>
                                <div class="metadata-value font-mono">{{ $latest->command_uuid }}</div>
                            </div>
                            @if($relatedCommand?->exit_code !== null)
                                <div>
                                    <div class="metadata-label">Exit Code</div>
                                    <div class="metadata-value">
                                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $relatedCommand->exit_code === 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                            {{ $relatedCommand->exit_code }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                            @if($latest->environment)
                                <div>
                                    <div class="metadata-label">Environment</div>
                                    <div class="metadata-value">{{ $latest->environment }}</div>
                                </div>
                            @endif
                        </div>
                        @if($relatedCommand)
                            <div class="mt-3">
                                <a href="{{ route('organizations.projects.commands.show', [$organization, $project, $relatedCommand->uuid]) }}"
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    View Command →
                                </a>
                            </div>
                        @endif
                    @elseif($latest->hasRequest() || $latestRelatedRequest)
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                Source: HTTP Request
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <div class="metadata-label">Request ID</div>
                                <div class="metadata-value font-mono">{{ $latestRelatedRequest ? $latestRelatedRequest->request_id : $latest->request_id }}</div>
                            </div>
                            @if($latestRelatedRequest ? $latestRelatedRequest->method : $latest->method)
                                <div>
                                    <div class="metadata-label">Method</div>
                                    <div class="metadata-value">{{ $latestRelatedRequest ? $latestRelatedRequest->method : $latest->method }}</div>
                                </div>
                            @endif
                            @if($latestRelatedRequest ? $latestRelatedRequest->path : $latest->path)
                                <div>
                                    <div class="metadata-label">Path</div>
                                    <div class="metadata-value font-mono truncate">{{ $latestRelatedRequest ? $latestRelatedRequest->path : $latest->path }}</div>
                                </div>
                            @endif
                            @php
                                $latestStatus = $latestRelatedRequest ? $latestRelatedRequest->status_code : $latest->status_code;
                            @endphp
                            @if($latestStatus)
                                <div>
                                    <div class="metadata-label">Status</div>
                                    <div class="metadata-value">
                                        <span class="px-2 py-0.5 rounded text-xs font-medium
                                            @if($latestStatus >= 500) bg-red-100 text-red-800
                                            @elseif($latestStatus >= 400) bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ $latestStatus }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                            @if($latestRelatedRequest ? $latestRelatedRequest->route_name : $latest->route_name)
                                <div>
                                    <div class="metadata-label">Route</div>
                                    <div class="metadata-value font-mono">{{ $latestRelatedRequest ? $latestRelatedRequest->route_name : $latest->route_name }}</div>
                                </div>
                            @endif
                            @if($latestRelatedRequest ? $latestRelatedRequest->controller_action : $latest->controller_action)
                                <div>
                                    <div class="metadata-label">Controller</div>
                                    <div class="metadata-value font-mono">{{ $latestRelatedRequest ? $latestRelatedRequest->controller_action : $latest->controller_action }}</div>
                                </div>
                            @endif
                            @if($latestRelatedRequest ? $latestRelatedRequest->host : $latest->host)
                                <div>
                                    <div class="metadata-label">Host</div>
                                    <div class="metadata-value">{{ $latestRelatedRequest ? $latestRelatedRequest->host : $latest->host }}</div>
                                </div>
                            @endif
                            @if($latestRelatedRequest ? $latestRelatedRequest->environment : $latest->environment)
                                <div>
                                    <div class="metadata-label">Environment</div>
                                    <div class="metadata-value">{{ $latestRelatedRequest ? $latestRelatedRequest->environment : $latest->environment }}</div>
                                </div>
                            @endif
                        </div>
                        @if($latestRelatedRequest)
                            <div class="mt-3">
                                <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $latestRelatedRequest->uuid]) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    View Request →
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            <!-- Environment Info -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($latest->laravel_version)
                        <div>
                            <div class="metadata-label">Laravel</div>
                            <div class="metadata-value">{{ $latest->laravel_version }}</div>
                        </div>
                    @endif
                    @if($latest->php_version)
                        <div>
                            <div class="metadata-label">PHP</div>
                            <div class="metadata-value">{{ $latest->php_version }}</div>
                        </div>
                    @endif
                    @if($latest->agent_version)
                        <div>
                            <div class="metadata-label">Agent</div>
                            <div class="metadata-value">{{ $latest->agent_version }}</div>
                        </div>
                    @endif
                    @if($latest->user_agent)
                        <div>
                            <div class="metadata-label">User Agent</div>
                            <div class="metadata-value text-xs truncate" title="{{ $latest->user_agent }}">
                                {{ $latest->user_agent }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Exception Message -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="metadata-label mb-2">Exception Message</div>
                <div class="code-block text-red-600 dark:text-red-400">
                    {{ $latest->message }}
                </div>
            </div>

            <!-- Stack Trace -->
            <div class="px-6 py-4">
                <div class="metadata-label mb-2">Stack Trace</div>
                <pre class="code-block whitespace-pre-wrap text-gray-700 dark:text-gray-300">{{ $latest->stack_trace }}</pre>
            </div>
        </div>
    @endif

    <!-- Related Logs -->
    @php
        $relatedLogs = $group->getRelatedLogs(10);
    @endphp
    @if($relatedLogs->isNotEmpty())
    <div class="bg-blue-50 dark:bg-blue-900/10 rounded-lg shadow border border-blue-200 dark:border-blue-800">
        <div class="px-6 py-4 border-b border-blue-200 dark:border-blue-800">
            <h2 class="text-lg font-medium text-blue-900 dark:text-blue-400">Related Logs</h2>
        </div>
        <div class="divide-y divide-blue-100 dark:divide-blue-800">
            @foreach($relatedLogs as $log)
                <a href="{{ route('organizations.projects.logs.show', [$organization, $project, $log->uuid]) }}"
                   class="block px-6 py-3 hover:bg-blue-100/50 dark:hover:bg-blue-900/30 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->getLevelBadgeClass() }}">
                                {{ $log->level }}
                            </span>
                            <span class="text-sm text-gray-900 dark:text-gray-100 truncate max-w-lg">
                                {{ Str::limit($log->message, 70) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            @if($log->channel)
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $log->channel }}</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $log->logged_at->format('H:i:s') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Occurrence History -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Occurrence History</h2>
        </div>

        @if($occurrences->isEmpty())
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                No occurrences found
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date/Time
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Source
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            HTTP Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Request / Job
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Environment
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($occurrences as $occurrence)
                        @php
                            $relatedRequest = $occurrence->getRelatedRequestEvent();
                            $relatedJob = $occurrence->isFromJob() ? $occurrence->jobEvent : null;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $occurrence->occurred_at->format('M j, Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($occurrence->isFromJob())
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                        Job
                                    </span>
                                @elseif($occurrence->hasRequest() || $relatedRequest)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        HTTP
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-400">
                                        Other
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $displayStatus = $occurrence->status_code ?? $relatedRequest?->status_code;
                                @endphp
                                @if($displayStatus)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium
                                        @if($displayStatus >= 500) bg-red-100 text-red-800
                                        @elseif($displayStatus >= 400) bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $displayStatus }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                @if($occurrence->isFromJob())
                                    <div class="font-mono truncate max-w-xs" title="{{ class_basename($relatedJob?->job_name ?? $occurrence->controller_action ?? 'UnknownJob') }}">
                                        {{ class_basename($relatedJob?->job_name ?? $occurrence->controller_action ?? '—') }}
                                    </div>
                                @elseif($occurrence->method || $occurrence->path)
                                    <span class="font-mono">{{ $occurrence->method ?? '' }}</span>
                                    <span class="ml-1">{{ Str::limit($occurrence->path, 30) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $occurrence->environment ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($occurrence->isFromJob() && $relatedJob)
                                    <a href="{{ route('organizations.projects.jobs.show', [$organization, $project, $relatedJob->uuid]) }}"
                                       class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        View Job →
                                    </a>
                                @elseif($relatedRequest)
                                    <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $relatedRequest->uuid]) }}"
                                       class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        View Request →
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $occurrences->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
