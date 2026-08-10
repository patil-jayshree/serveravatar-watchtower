@extends('layouts.app')

@section('title', 'Exceptions - ' . $project->name . ' - Watchtower')

@push('styles')
<style>
    .exception-type {
        @apply font-mono text-sm;
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
    .exception-row:hover {
        @apply bg-gray-50 dark:bg-gray-800/50;
    }
    .stack-preview {
        @apply font-mono text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded p-2 max-h-20 overflow-hidden;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Exceptions</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $project->name }} — {{ $project->environment }}
                </p>
            </div>
            <a href="{{ route('organizations.projects.show', [$organization, $project]) }}"
               class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                ← Back to Project
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Exception Groups</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-red-600">{{ $stats['open'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Open Issues</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-green-600">{{ $stats['resolved'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Resolved</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $groups->total() }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Showing</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4">
        <form method="GET" action="{{ route('organizations.projects.exceptions.index', [$organization, $project]) }}"
              class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Exception type or message..."
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" id="status"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All</option>
                    <option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="resolved" {{ ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>

            <!-- Exception Type Filter -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Exception Type</label>
                <select name="type" id="type"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Types</option>
                    @foreach($exceptionTypes as $type)
                        <option value="{{ $type }}" {{ ($filters['type'] ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Environment Filter -->
            <div>
                <label for="environment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Environment</label>
                <select name="environment" id="environment"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All</option>
                    <option value="production" {{ ($filters['environment'] ?? '') === 'production' ? 'selected' : '' }}>Production</option>
                    <option value="local" {{ ($filters['environment'] ?? '') === 'local' ? 'selected' : '' }}>Local</option>
                    <option value="staging" {{ ($filters['environment'] ?? '') === 'staging' ? 'selected' : '' }}>Staging</option>
                    <option value="development" {{ ($filters['environment'] ?? '') === 'development' ? 'selected' : '' }}>Development</option>
                </select>
            </div>

            <!-- Time Range Filter -->
            <div>
                <label for="time_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Range</label>
                <select name="time_range" id="time_range"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Time</option>
                    <option value="24h" {{ ($filters['time_range'] ?? '') === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                    <option value="7d" {{ ($filters['time_range'] ?? '') === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30d" {{ ($filters['time_range'] ?? '') === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="custom" {{ ($filters['time_range'] ?? '') === 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
        </div>

        <!-- Custom Date Range (shown when Custom is selected) -->
        <div id="customDateRange" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4" style="display: none;">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $filters['start_date'] ?? '' }}"
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $filters['end_date'] ?? '' }}"
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex items-center justify-between mt-4">
            <button type="submit"
                    class="bg-indigo-600 text-white rounded-md px-4 py-2 text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Apply Filters
            </button>
            @if(array_filter($filters))
                <a href="{{ route('organizations.projects.exceptions.index', [$organization, $project]) }}"
                   class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    Clear Filters
                </a>
            @endif
        </div>
    </form>
    </div>

    <!-- Exception Groups List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        @if($groups->isEmpty())
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2 text-sm">No exception groups found</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Exception
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Occurrences
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Last Seen
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            First Seen
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Location
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($groups as $group)
                        <tr class="exception-row cursor-pointer"
                            onclick="window.location.href='{{ route('organizations.projects.exceptions.show', [$organization, $project, $group->uuid]) }}'">
                            <td class="px-6 py-4">
                                <div class="exception-type text-red-600 font-medium">
                                    {{ class_basename($group->exception_type) }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 truncate max-w-md">
                                    {{ $group->normalized_message ?? 'No message' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge {{ $group->isOpen() ? 'status-open' : 'status-resolved' }}">
                                    {{ ucfirst($group->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $group->occurrence_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $group->last_seen_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $group->first_seen_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <div class="truncate max-w-xs" title="{{ $group->file }}:{{ $group->line }}">
                                    {{ Str::after($group->file, 'app/') ?? $group->file }}:{{ $group->line }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($group->isOpen())
                                    <form method="POST"
                                          action="{{ route('organizations.projects.exceptions.update-status', [$organization, $project, $group->uuid]) }}"
                                          style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="resolved">
                                        <button type="submit"
                                                class="text-green-600 hover:text-green-800 font-medium text-xs"
                                                onclick="return confirm('Mark this exception as resolved?');">
                                            Resolve
                                        </button>
                                    </form>
                                @else
                                    <form method="POST"
                                          action="{{ route('organizations.projects.exceptions.update-status', [$organization, $project, $group->uuid]) }}"
                                          style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="open">
                                        <button type="submit"
                                                class="text-gray-600 hover:text-gray-800 font-medium text-xs">
                                            Reopen
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $groups->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeRangeSelect = document.getElementById('time_range');
        const customDateRange = document.getElementById('customDateRange');

        function toggleCustomDateRange() {
            if (timeRangeSelect.value === 'custom') {
                customDateRange.style.display = 'grid';
            } else {
                customDateRange.style.display = 'none';
            }
        }

        if (timeRangeSelect) {
            timeRangeSelect.addEventListener('change', toggleCustomDateRange);
            toggleCustomDateRange();
        }
    });
</script>
@endpush
@endsection
