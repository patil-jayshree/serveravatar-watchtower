@extends('layouts.app')

@section('title', 'Request ' . $event->method . ' ' . $event->path . ' - ' . $project->name)

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
                <a href="{{ route('organizations.projects.requests.index', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">Requests</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="truncate max-w-xs">{{ $event->method }} {{ $event->path }}</span>
            </div>
            <div class="flex items-center gap-3 mt-2">
                <span class="inline-flex items-center px-3 py-1 rounded text-sm font-medium
                    @if($event->method === 'GET') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                    @elseif($event->method === 'POST') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                    @elseif($event->method === 'PUT' || $event->method === 'PATCH') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                    @elseif($event->method === 'DELETE') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                    @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                    @endif">
                    {{ $event->method }}
                </span>
                <code class="text-lg font-mono text-gray-900 dark:text-white">{{ $event->path }}</code>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Status & Overview --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overview</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Status</p>
                        <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium
                            @if($event->status_code >= 200 && $event->status_code < 300) bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                            @elseif($event->status_code >= 300 && $event->status_code < 400) bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                            @elseif($event->status_code >= 400 && $event->status_code < 500) bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400
                            @else bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                            @endif">
                            {{ $event->status_code }} {{ $event->statusText() }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Duration</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $event->duration_ms }} ms</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Memory</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $event->memory_bytes ? number_format($event->memory_bytes / 1024 / 1024, 1) . ' MB' : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Timestamp</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->requested_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->requested_at->format('H:i:s') }}</p>
                    </div>
                </div>
            </div>

            {{-- Request Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request Details</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Request ID</dt>
                        <dd class="text-sm font-mono text-gray-900 dark:text-white mt-1">{{ $event->request_id }}</dd>
                    </div>
                    @if($event->url)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Full URL</dt>
                        <dd class="text-sm font-mono text-gray-900 dark:text-white mt-1 break-all">{{ $event->url }}</dd>
                    </div>
                    @endif
                    @if($event->route_name)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Route</dt>
                        <dd class="text-sm font-mono text-gray-900 dark:text-white mt-1">{{ $event->route_name }}</dd>
                    </div>
                    @endif
                    @if($event->controller_action)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Controller</dt>
                        <dd class="text-sm font-mono text-gray-900 dark:text-white mt-1">{{ $event->controller_action }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Environment</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ ucfirst($event->environment) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Host</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $event->host ?? '-' }}</dd>
                    </div>
                    @if($event->content_type)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Content Type</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $event->content_type }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Client Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Client</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">IP Address</dt>
                        <dd class="text-sm font-mono text-gray-900 dark:text-white mt-1">{{ $event->ip ?? '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">User Agent</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1 break-all">{{ $event->user_agent ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Related Exceptions --}}
            @php
                // First try exact request_id match
                $relatedExceptions = $project->exceptionOccurrences()
                    ->where('request_id', $event->request_id)
                    ->with('exceptionGroup')
                    ->orderByDesc('occurred_at')
                    ->limit(5)
                    ->get();

                // Fallback: match by method + path + timestamp if no exact matches
                if ($relatedExceptions->isEmpty()) {
                    $relatedExceptions = $project->exceptionOccurrences()
                        ->where('method', $event->method)
                        ->where('path', $event->path)
                        ->whereBetween('occurred_at', [
                            $event->requested_at->subSeconds(5),
                            $event->requested_at->addSeconds(5),
                        ])
                        ->with('exceptionGroup')
                        ->orderByDesc('occurred_at')
                        ->limit(5)
                        ->get();
                }
            @endphp
            @if($relatedExceptions->count() > 0)
            <div class="bg-red-50 dark:bg-red-900/10 rounded-xl shadow-sm border border-red-200 dark:border-red-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-red-900 dark:text-red-400">
                        Related Exceptions
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                        {{ $relatedExceptions->count() }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($relatedExceptions as $exc)
                        <a href="{{ route('organizations.projects.exceptions.show', [$organization, $project, $exc->exception_group_uuid]) }}"
                           class="block p-3 bg-white dark:bg-gray-800 rounded-lg border border-red-100 dark:border-red-800 hover:border-red-300 dark:hover:border-red-700 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-mono text-sm font-medium text-red-600 dark:text-red-400">
                                        {{ class_basename($exc->exceptionGroup->exception_type) }}
                                    </span>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 truncate max-w-md">
                                        {{ $exc->message_preview ?? Str::limit($exc->message, 100) }}
                                    </p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ $exc->occurred_at->format('M j, Y H:i:s') }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Database Queries --}}
            @php
                $relatedQueries = $project->queryEvents()
                    ->where('request_id', $event->request_id)
                    ->orderByDesc('occurred_at')
                    ->limit(10)
                    ->get();

                $totalQueryTime = $relatedQueries->sum('duration_ms');
            @endphp
            @if($relatedQueries->count() > 0)
            <div class="bg-indigo-50 dark:bg-indigo-900/10 rounded-xl shadow-sm border border-indigo-200 dark:border-indigo-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-indigo-900 dark:text-indigo-400">
                        Database Queries
                    </h2>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-indigo-700 dark:text-indigo-300">
                            {{ $relatedQueries->count() }} queries · {{ $totalQueryTime }} ms total
                        </span>
                        <a href="{{ route('organizations.projects.queries.index', [$organization, $project]) }}?search=&connection=all&slow=all&time_range=all"
                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                            View All →
                        </a>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach($relatedQueries as $q)
                        <a href="{{ route('organizations.projects.queries.show', [$organization, $project, $q->uuid]) }}"
                           class="block p-3 bg-white dark:bg-gray-800 rounded-lg border border-indigo-100 dark:border-indigo-800 hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-medium
                                        @if($q->query_type === 'select') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($q->query_type === 'insert') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($q->query_type === 'update') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @elseif($q->query_type === 'delete') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                        @endif">
                                        {{ strtoupper($q->query_type) }}
                                    </span>
                                    <span class="font-mono text-sm text-gray-700 dark:text-gray-300 truncate max-w-md">
                                        {{ Str::limit($q->sql_preview, 60) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($q->is_slow)
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            Slow
                                        </span>
                                    @endif
                                    <span class="text-sm font-medium {{ $q->is_slow ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                        {{ $q->duration_ms }} ms
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
