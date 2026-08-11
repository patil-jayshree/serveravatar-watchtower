@extends('layouts.app')

@section('title', 'Database Queries - ' . $project->name . ' - Watchtower')

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
                <span>Database Queries</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Database Queries</h1>
        </div>

        {{-- Stats Overview --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Queries</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['slow']) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Slow Queries</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_duration'] }} ms</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Duration</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $queries->total() }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Displayed</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('organizations.projects.queries.index', [$organization, $project]) }}" class="p-4">
                <div class="flex flex-wrap gap-4">
                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Search normalized SQL..."
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                    </div>

                    {{-- Query Type --}}
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                        <select name="type" id="type" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="all">All Types</option>
                            <option value="select" {{ ($filters['type'] ?? '') === 'select' ? 'selected' : '' }}>SELECT</option>
                            <option value="insert" {{ ($filters['type'] ?? '') === 'insert' ? 'selected' : '' }}>INSERT</option>
                            <option value="update" {{ ($filters['type'] ?? '') === 'update' ? 'selected' : '' }}>UPDATE</option>
                            <option value="delete" {{ ($filters['type'] ?? '') === 'delete' ? 'selected' : '' }}>DELETE</option>
                            <option value="other" {{ ($filters['type'] ?? '') === 'other' ? 'selected' : '' }}>OTHER</option>
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

                    {{-- Slow Queries --}}
                    <div>
                        <label for="slow" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slow</label>
                        <select name="slow" id="slow" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="all">All</option>
                            <option value="slow" {{ ($filters['slow'] ?? '') === 'slow' ? 'selected' : '' }}>Slow Only</option>
                            <option value="normal" {{ ($filters['slow'] ?? '') === 'normal' ? 'selected' : '' }}>Normal Only</option>
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
                        <a href="{{ route('organizations.projects.queries.index', [$organization, $project]) }}" class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md px-4 py-2 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Query List --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            @if($queries->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    No queries found matching your filters.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    SQL
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Connection
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Request ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Timestamp
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($queries as $q)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('organizations.projects.queries.show', [$organization, $project, $q->uuid]) }}"
                                           class="text-sm font-mono text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                            <span class="block max-w-md truncate">{{ Str::limit($q->sql_preview, 80) }}</span>
                                            @if($q->is_slow)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 mt-1">
                                                    Slow
                                                </span>
                                            @endif
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium {{ $q->is_slow ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                            {{ $q->duration_ms }} ms
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            @if($q->query_type === 'select') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                            @elseif($q->query_type === 'insert') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($q->query_type === 'update') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                            @elseif($q->query_type === 'delete') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                            @endif">
                                            {{ strtoupper($q->query_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $q->connection_name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                                        @if($q->request_id)
                                            {{ Str::limit($q->request_id, 12) }}
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $q->occurred_at->format('M j, Y H:i:s') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $queries->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
