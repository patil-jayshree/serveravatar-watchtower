@extends('layouts.app')

@section('title', 'Query Detail - ' . $project->name . ' - Watchtower')

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
                <a href="{{ route('organizations.projects.queries.index', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">Queries</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="truncate max-w-xs">{{ Str::limit($query->sql, 30) }}</span>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Query Detail --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Query</h2>
                        @if($query->is_slow)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                Slow Query
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    {{-- SQL --}}
                    <div class="mb-6">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">SQL</div>
                        <pre class="font-mono text-sm bg-gray-100 dark:bg-gray-900 rounded p-4 overflow-x-auto whitespace-pre-wrap break-all text-gray-800 dark:text-gray-200">{{ $query->sql }}</pre>
                    </div>

                    {{-- Bindings --}}
                    @if($query->bindings && count($query->bindings) > 0)
                        <div class="mb-6">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Bindings</div>
                            <div class="bg-gray-100 dark:bg-gray-900 rounded p-4">
                                <div class="space-y-2">
                                    @foreach($query->bindings as $key => $value)
                                        <div class="flex gap-4">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-mono min-w-[40px]">
                                                @if(is_string($key)){{ $key }}@else#{{ $key }}@endif:
                                            </span>
                                            <span class="text-sm text-gray-800 dark:text-gray-200 font-mono break-all">
                                                @if(is_string($value))
                                                    {{ strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value }}
                                                @else
                                                    {{ var_export($value, true) }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Performance & Info Grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Duration</div>
                            <div class="text-2xl font-bold {{ $query->is_slow ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                {{ $query->duration_ms }} ms
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $query->is_slow ? 'Slow' : 'Normal' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Query Type</div>
                            <div>
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($query->query_type === 'select') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($query->query_type === 'insert') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($query->query_type === 'update') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @elseif($query->query_type === 'delete') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                    @endif">
                                    {{ strtoupper($query->query_type) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Connection</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $query->connection_name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Driver</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $query->driver ?? '—' }}</div>
                        </div>
                        @if($query->database_name)
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Database</div>
                                <div class="text-sm text-gray-900 dark:text-white">{{ $query->database_name }}</div>
                            </div>
                        @endif
                        @if($query->transaction_id)
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Transaction</div>
                                <div class="text-sm text-gray-900 dark:text-white font-mono">{{ $query->transaction_id }}</div>
                            </div>
                        @endif
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Timestamp</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $query->occurred_at->format('M j, Y H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Request Context --}}
            @if($relatedRequest)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Context</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Request ID</div>
                                <div class="text-sm text-gray-900 dark:text-white font-mono">{{ $relatedRequest->request_id }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Method</div>
                                <div class="text-sm text-gray-900 dark:text-white">{{ $relatedRequest->method }}</div>
                            </div>
                            <div class="md:col-span-2">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Path</div>
                                <div class="text-sm text-gray-900 dark:text-white font-mono truncate">{{ $relatedRequest->path }}</div>
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
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Duration</div>
                                <div class="text-sm text-gray-900 dark:text-white">{{ $relatedRequest->duration_ms }} ms</div>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Environment</div>
                                <div class="text-sm text-gray-900 dark:text-white">{{ ucfirst($relatedRequest->environment) }}</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $relatedRequest->uuid]) }}"
                               class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                View Request →
                            </a>
                        </div>
                    </div>
                </div>
            @elseif($query->request_id)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Request ID: <code class="font-mono">{{ $query->request_id }}</code><br>
                        <span class="text-xs">Related request not found (may have expired)</span>
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
