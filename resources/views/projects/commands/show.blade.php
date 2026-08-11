@extends('layouts.app')

@section('title', $command->command_name . ' - Commands - ' . $project->name . ' - Watchtower')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('organizations.show', $organization) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $organization->name }}</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $project->name }}</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('organizations.projects.commands.index', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">Commands</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="truncate max-w-[200px]">{{ $command->command_name }}</span>
            </div>
        </div>

        {{-- Overview Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white font-mono">{{ $command->command_name }}</h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                        @if($command->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @elseif($command->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                        @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 @endif">
                        {{ ucfirst($command->status) }}
                    </span>
                    @if($command->is_slow)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                            Slow
                        </span>
                    @endif
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $command->created_at->format('M j, Y H:i:s') }}
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Exit Code</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white font-mono">
                            @if($command->exit_code !== null)
                                <span class="{{ $command->exit_code === 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $command->exit_code }}
                                </span>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Duration</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white font-mono">
                            {{ $command->duration_formatted }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Environment</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $command->environment ?? '—' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Server</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white text-sm truncate">
                            {{ $command->server_name ?? '—' }}
                        </div>
                    </div>
                </div>

                {{-- Timing --}}
                @if($command->started_at || $command->finished_at)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Timing</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($command->started_at)
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Started</div>
                            <div class="text-sm text-gray-900 dark:text-white font-mono">
                                {{ $command->started_at_formatted }}
                                <span class="text-gray-400 text-xs ml-1">
                                    ({{ \Carbon\Carbon::createFromTimestamp($command->started_at)->format('M j, H:i:s') }})
                                </span>
                            </div>
                        </div>
                        @endif
                        @if($command->finished_at)
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Finished</div>
                            <div class="text-sm text-gray-900 dark:text-white font-mono">
                                {{ $command->finished_at_formatted }}
                                <span class="text-gray-400 text-xs ml-1">
                                    ({{ \Carbon\Carbon::createFromTimestamp($command->finished_at)->format('M j, H:i:s') }})
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Arguments & Options --}}
                @if(($command->arguments && count($command->arguments) > 0) || ($command->options && count($command->options) > 0))
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Arguments & Options</h3>
                    <div class="space-y-2">
                        @if($command->arguments && count($command->arguments) > 0)
                            @foreach($command->arguments as $key => $value)
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="text-gray-500 dark:text-gray-400 font-mono">{{ $key }}:</span>
                                    <span class="text-gray-900 dark:text-white font-mono break-all">
                                        @if(is_array($value))
                                            {{ json_encode($value) }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        @endif
                        @if($command->options && count($command->options) > 0)
                            @foreach($command->options as $key => $value)
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="text-gray-500 dark:text-gray-400 font-mono">--{{ $key }}</span>
                                    @if($value === '[REDACTED]')
                                        <span class="text-amber-600 dark:text-amber-400 font-mono">[REDACTED]</span>
                                    @elseif(is_bool($value) && $value)
                                        <span class="text-gray-400 text-xs">(boolean)</span>
                                    @elseif($value !== null && $value !== '')
                                        <span class="text-gray-900 dark:text-white font-mono break-all">
                                            @if(is_array($value))
                                                {{ json_encode($value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                @endif

                {{-- UUID --}}
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Execution UUID</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300 font-mono">{{ $command->uuid }}</div>
                </div>
            </div>
        </div>

        {{-- Request Context --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request</h2>
            </div>
            <div class="p-6">
                @if($requestEvent)
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-900 dark:text-white">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($requestEvent->method === 'GET') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($requestEvent->method === 'POST') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 @endif
                                    mb-1">
                                    {{ $requestEvent->method }}
                                </span>
                                <span class="ml-2 font-mono text-sm">{{ $requestEvent->path }}</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Request ID: {{ $requestEvent->request_id }}</div>
                        </div>
                        <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $requestEvent->uuid]) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors">
                            View Request
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Not available — command was run outside of HTTP context</div>
                        <span class="text-gray-400 text-xs">—</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Exception Context --}}
        @if($command->hasException())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border-l-4 border-red-500">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-red-600 dark:text-red-400">Exception</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    {{ class_basename($command->exception_class) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-900 dark:text-white mb-2">
                                {{ Str::limit($command->exception_message, 200) }}
                            </div>
                            @if($command->exception_file)
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $command->exception_file }}:{{ $command->exception_line }}
                                </div>
                            @endif
                        </div>
                        @if($exceptionOccurrence)
                            <a href="{{ route('organizations.projects.exceptions.show', [$organization, $project, $exceptionOccurrence->uuid]) }}"
                               class="ml-4 inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors shrink-0">
                                View Exception
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        @endif
                    </div>

                    {{-- Stack Trace --}}
                    @if($command->stack_trace)
                        <details class="mt-4">
                            <summary class="text-sm text-gray-500 dark:text-gray-400 cursor-pointer hover:text-gray-700 dark:hover:text-gray-300">
                                Stack Trace
                            </summary>
                            <pre class="mt-2 p-3 bg-gray-100 dark:bg-gray-900 rounded text-xs text-gray-800 dark:text-gray-300 overflow-x-auto font-mono leading-relaxed">{{ $command->stack_trace }}</pre>
                        </details>
                    @endif
                </div>
            </div>
        @endif

        {{-- Related Commands --}}
        @if($relatedCommands->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent "{{ $command->command_name }}" Executions</h2>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($relatedCommands as $related)
                    <a href="{{ route('organizations.projects.commands.show', [$organization, $project, $related->uuid]) }}"
                       class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if($related->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($related->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 @endif">
                                {{ $related->status }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $related->created_at->format('M j, H:i:s') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-mono text-gray-500 dark:text-gray-400">
                                {{ $related->duration_formatted }}
                            </span>
                            @if($related->exit_code !== null)
                                <span class="text-sm font-mono {{ $related->exit_code === 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    exit {{ $related->exit_code }}
                                </span>
                            @endif
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Metadata Footer --}}
        <div class="mt-6 text-xs text-gray-400 dark:text-gray-500 space-y-1">
            <div>Agent: {{ $command->agent_version ?? '—' }} | Laravel: {{ $command->laravel_version ?? '—' }} | PHP: {{ $command->php_version ?? '—' }}</div>
        </div>
    </div>
</div>
@endsection
