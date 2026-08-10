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
        @php $latest = $group->latestOccurrence; @endphp
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Latest Occurrence</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $latest->occurred_at->format('M j, Y H:i:s') }}
                </p>
            </div>

            <!-- Request Context -->
            @if($latest->hasRequest())
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <div class="metadata-label">Request ID</div>
                            <div class="metadata-value font-mono">{{ $latest->request_id }}</div>
                        </div>
                        @if($latest->method)
                            <div>
                                <div class="metadata-label">Method</div>
                                <div class="metadata-value">{{ $latest->method }}</div>
                            </div>
                        @endif
                        @if($latest->path)
                            <div>
                                <div class="metadata-label">Path</div>
                                <div class="metadata-value font-mono truncate">{{ $latest->path }}</div>
                            </div>
                        @endif
                        @if($latest->status_code)
                            <div>
                                <div class="metadata-label">Status</div>
                                <div class="metadata-value">
                                    <span class="px-2 py-0.5 rounded text-xs font-medium
                                        @if($latest->status_code >= 500) bg-red-100 text-red-800
                                        @elseif($latest->status_code >= 400) bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $latest->status_code }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if($latest->route_name)
                            <div>
                                <div class="metadata-label">Route</div>
                                <div class="metadata-value font-mono">{{ $latest->route_name }}</div>
                            </div>
                        @endif
                        @if($latest->controller_action)
                            <div>
                                <div class="metadata-label">Controller</div>
                                <div class="metadata-value font-mono">{{ $latest->controller_action }}</div>
                            </div>
                        @endif
                        @if($latest->host)
                            <div>
                                <div class="metadata-label">Host</div>
                                <div class="metadata-value">{{ $latest->host }}</div>
                            </div>
                        @endif
                        @if($latest->environment)
                            <div>
                                <div class="metadata-label">Environment</div>
                                <div class="metadata-value">{{ $latest->environment }}</div>
                            </div>
                        @endif
                    </div>
                    @if($latest->request_id)
                        <div class="mt-3">
                            <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $latest->request_id]) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View Request →
                            </a>
                        </div>
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
                            Time
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Method
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Path
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Request ID
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($occurrences as $occurrence)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $occurrence->occurred_at->format('M j, Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($occurrence->status_code)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium
                                        @if($occurrence->status_code >= 500) bg-red-100 text-red-800
                                        @elseif($occurrence->status_code >= 400) bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $occurrence->status_code }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $occurrence->method ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                {{ $occurrence->path ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                                @if($occurrence->request_id)
                                    <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $occurrence->request_id]) }}"
                                       class="text-indigo-600 hover:text-indigo-800">
                                        {{ Str::limit($occurrence->request_id, 15) }}
                                    </a>
                                @else
                                    —
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
