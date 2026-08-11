@extends('layouts.app')

@section('title', class_basename($log->level) . ' - ' . Str::limit($log->message, 40) . ' - ' . $project->name . ' - Watchtower')

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
                <a href="{{ route('organizations.projects.logs.index', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">Logs</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="truncate max-w-xs">{{ Str::limit($log->message, 40) }}</span>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Log Overview --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded text-sm font-bold {{ $log->getLevelBadgeClass() }}">
                                {{ $log->level }}
                            </span>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ Str::limit($log->message, 80) }}
                            </h2>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                            {{ $log->uuid }}
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Channel</div>
                            <div class="text-sm text-gray-900 dark:text-white font-mono">{{ $log->channel ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Environment</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ ucfirst($log->environment ?? '—') }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Host</div>
                            <div class="text-sm text-gray-900 dark:text-white font-mono">{{ $log->host ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Logged At</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $log->logged_at->format('M j, Y H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Full Message --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Message</h2>
                </div>
                <div class="p-6">
                    <pre class="text-sm text-gray-800 dark:text-gray-200 font-mono whitespace-pre-wrap break-words">{{ $log->message }}</pre>
                </div>
            </div>

            {{-- Context --}}
            @if($log->context)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Context</h2>
                    </div>
                    <div class="p-6">
                        <pre class="text-sm text-gray-800 dark:text-gray-200 font-mono whitespace-pre-wrap break-words bg-gray-50 dark:bg-gray-900 rounded p-4 overflow-x-auto">{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            @endif

            {{-- Location --}}
            @if($log->file || $log->line)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Location</h2>
                    </div>
                    <div class="p-6">
                        <div class="text-sm font-mono text-gray-700 dark:text-gray-300">
                            {{ $log->file ?? '—' }}
                            @if($log->line)
                                <span class="text-gray-500">:{{ $log->line }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Exception --}}
            @if($log->exception_class || $log->exception_message)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Exception</h2>
                            @if($relatedExceptionGroup)
                                <a href="{{ route('organizations.projects.exceptions.show', [$organization, $project, $relatedExceptionGroup->uuid]) }}"
                                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                                    <span>View Exception</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($log->exception_class)
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Type</div>
                                <div class="text-sm font-mono text-red-600 dark:text-red-400">
                                    {{ $log->exception_class }}
                                </div>
                            </div>
                        @endif
                        @if($log->exception_message)
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Message</div>
                                <div class="text-sm font-mono text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/10 rounded p-3 border border-red-100 dark:border-red-800">
                                    {{ $log->exception_message }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Request --}}
            @if($log->request_id)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request</h2>
                            @if($relatedRequest)
                                <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $relatedRequest->uuid]) }}"
                                   class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                                    <span>View Request</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Request ID</div>
                        <div class="text-sm font-mono text-gray-900 dark:text-white">{{ $log->request_id }}</div>
                    </div>
                </div>
            @endif

            {{-- Related Logs from Same Request --}}
            @if($relatedLogs->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Other Logs from This Request
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
                                ({{ $relatedLogs->count() }})
                            </span>
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($relatedLogs as $relatedLog)
                            <a href="{{ route('organizations.projects.logs.show', [$organization, $project, $relatedLog->uuid]) }}"
                               class="block px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $relatedLog->getLevelBadgeClass() }}">
                                        {{ $relatedLog->level }}
                                    </span>
                                    <span class="flex-1 text-sm text-gray-900 dark:text-white truncate">
                                        {{ Str::limit($relatedLog->message, 60) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $relatedLog->logged_at->format('H:i:s') }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Metadata --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Metadata</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Agent Version</div>
                            <div class="text-gray-900 dark:text-white">{{ $log->agent_version ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Logged At</div>
                            <div class="text-gray-900 dark:text-white">{{ $log->logged_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
