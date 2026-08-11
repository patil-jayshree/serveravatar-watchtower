@extends('layouts.app')

@section('title', 'Performance - ' . $project->name . ' - Watchtower')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared chart options
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? '#374151' : '#e5e7eb';
    const textColor = isDark ? '#9ca3af' : '#6b7280';
    const tooltipBg = isDark ? '#1f2937' : '#ffffff';
    const tooltipText = isDark ? '#f3f4f6' : '#1f2937';

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index',
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: tooltipBg,
                titleColor: tooltipText,
                bodyColor: tooltipText,
                borderColor: gridColor,
                borderWidth: 1,
                cornerRadius: 8,
                padding: 10,
            },
        },
        scales: {
            x: {
                grid: { color: gridColor, drawBorder: false },
                ticks: { color: textColor, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 },
            },
            y: {
                beginAtZero: true,
                grid: { color: gridColor, drawBorder: false },
                ticks: { color: textColor },
            },
        },
    };

    // Response Time Trend Chart
    const rtCanvas = document.getElementById('rtTrendChart');
    if (rtCanvas && rtCanvas.dataset.labels) {
        const rtLabels = JSON.parse(rtCanvas.dataset.labels);
        const rtData = JSON.parse(rtCanvas.dataset.values);
        const avgLine = parseFloat(rtCanvas.dataset.avg) || 0;

        new Chart(rtCanvas, {
            type: 'line',
            data: {
                labels: rtLabels,
                datasets: [
                    {
                        label: 'Avg Response Time',
                        data: rtData,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#6366f1',
                    },
                    {
                        label: 'Avg',
                        data: rtData.map(() => avgLine),
                        borderColor: '#f59e0b',
                        borderWidth: 1.5,
                        borderDash: [5, 5],
                        fill: false,
                        pointRadius: 0,
                        tension: 0,
                    },
                ],
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        ...commonOptions.plugins.tooltip,
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.datasetIndex === 0) {
                                    return ' ' + ctx.parsed.y.toFixed(0) + ' ms';
                                }
                                return ' Avg: ' + ctx.parsed.y.toFixed(0) + ' ms';
                            },
                        },
                    },
                },
                scales: {
                    ...commonOptions.scales,
                    y: {
                        ...commonOptions.scales.y,
                        ticks: {
                            ...commonOptions.scales.y.ticks,
                            callback: v => v + ' ms',
                        },
                    },
                },
            },
        });
    }

    // Throughput Trend Chart
    const tpCanvas = document.getElementById('tpTrendChart');
    if (tpCanvas && tpCanvas.dataset.labels) {
        const tpLabels = JSON.parse(tpCanvas.dataset.labels);
        const tpData = JSON.parse(tpCanvas.dataset.values);

        new Chart(tpCanvas, {
            type: 'bar',
            data: {
                labels: tpLabels,
                datasets: [{
                    label: 'Requests',
                    data: tpData,
                    backgroundColor: '#6366f1',
                    borderRadius: 3,
                    maxBarThickness: 20,
                }],
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        ...commonOptions.plugins.tooltip,
                        callbacks: {
                            label: ctx => ' ' + ctx.parsed.y + ' requests',
                        },
                    },
                },
                scales: {
                    ...commonOptions.scales,
                    y: {
                        ...commonOptions.scales.y,
                        ticks: {
                            ...commonOptions.scales.y.ticks,
                            callback: v => Number.isInteger(v) ? v : '',
                        },
                    },
                },
            },
        });
    }

    // Error Trend Chart
    const errCanvas = document.getElementById('errTrendChart');
    if (errCanvas && errCanvas.dataset.labels) {
        const errLabels = JSON.parse(errCanvas.dataset.labels);
        const errData = JSON.parse(errCanvas.dataset.values);

        new Chart(errCanvas, {
            type: 'bar',
            data: {
                labels: errLabels,
                datasets: [{
                    label: 'Errors',
                    data: errData,
                    backgroundColor: '#ef4444',
                    borderRadius: 3,
                    maxBarThickness: 20,
                }],
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        ...commonOptions.plugins.tooltip,
                        callbacks: {
                            label: ctx => ' ' + ctx.parsed.y + ' errors',
                        },
                    },
                },
                scales: {
                    ...commonOptions.scales,
                    y: {
                        ...commonOptions.scales.y,
                        ticks: {
                            ...commonOptions.scales.y.ticks,
                            callback: v => Number.isInteger(v) ? v : '',
                        },
                        beginAtZero: true,
                    },
                },
            },
        });
    }
});
</script>
@endpush

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                        <a href="{{ route('organizations.show', $organization) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $organization->name }}</a>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $project->name }}</a>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        <span>Performance</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Performance</h1>
                </div>

                {{-- Time Range Selector --}}
                <form method="GET" action="{{ route('organizations.projects.performance.index', [$organization, $project]) }}" class="flex items-center gap-2">
                    <label for="range" class="text-sm text-gray-500 dark:text-gray-400">Time Range</label>
                    <select name="range" id="range" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="1h" {{ $timeRange === '1h' ? 'selected' : '' }}>Last 1 Hour</option>
                        <option value="24h" {{ $timeRange === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                        <option value="7d" {{ $timeRange === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30d" {{ $timeRange === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                    </select>
                </form>
            </div>
        </div>

        @if(!$service->hasData())
            {{-- Empty State --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No performance data yet</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    Connect your Laravel application and generate some traffic to start seeing performance metrics.
                </p>
            </div>
        @else
            @php
                $metrics = $service->getMetrics();
                $rtTrend = $service->getResponseTimeTrend();
                $tpTrend = $service->getThroughputTrend();
                $errTrend = $service->getErrorTrend();

                // Format labels for charts
                $rtLabels = json_encode(array_map(fn($b) => \Carbon\Carbon::parse($b['bucket'])->format($timeRange === '1h' ? 'H:i' : ($timeRange === '30d' ? 'M j' : 'M j H:i')), $rtTrend));
                $rtValues = json_encode(array_map(fn($b) => round($b['value'], 2), $rtTrend));
                $tpLabels = json_encode(array_map(fn($b) => \Carbon\Carbon::parse($b['bucket'])->format($timeRange === '1h' ? 'H:i' : ($timeRange === '30d' ? 'M j' : 'M j H:i')), $tpTrend));
                $tpValues = json_encode(array_map(fn($b) => (int)$b['value'], $tpTrend));
                $errLabels = json_encode(array_map(fn($b) => \Carbon\Carbon::parse($b['bucket'])->format($timeRange === '1h' ? 'H:i' : ($timeRange === '30d' ? 'M j' : 'M j H:i')), $errTrend['buckets']));
                $errValues = json_encode(array_map(fn($b) => (int)$b['value'], $errTrend['buckets']));
                $avgMs = $metrics['response_time']['avg_ms'] ?? 0;
            @endphp

            {{-- Performance Overview Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Requests</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($metrics['throughput']['total']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $metrics['throughput']['requests_per_minute'] }} / min
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Avg Response</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $metrics['response_time']['avg_ms'] ?? '—' }}
                        @if($metrics['response_time']['avg_ms'])
                            <span class="text-lg font-medium text-gray-500 dark:text-gray-400">ms</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        P95 {{ $metrics['response_time']['p95_ms'] ?? '—' }} ms
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Error Rate</div>
                    <div class="text-3xl font-bold {{ ($metrics['error_rate']['error_rate'] ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                        {{ $metrics['error_rate']['error_rate'] ?? 0 }}%
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ number_format($metrics['error_rate']['error_count'] ?? 0) }} errors
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Slow Requests</div>
                    <div class="text-3xl font-bold text-amber-600 dark:text-amber-400">
                        {{ number_format($metrics['slow_requests']['count']) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $metrics['slow_requests']['rate'] }}%
                    </div>
                </div>
            </div>

            {{-- Response Time Percentiles --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Response Time</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Average</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $metrics['response_time']['avg_ms'] ?? '—' }}
                                @if($metrics['response_time']['avg_ms'])<span class="text-base font-normal text-gray-500">ms</span>@endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">P50</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $metrics['response_time']['p50_ms'] ?? '—' }}
                                @if($metrics['response_time']['p50_ms'])<span class="text-base font-normal text-gray-500">ms</span>@endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">P95</div>
                            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                {{ $metrics['response_time']['p95_ms'] ?? '—' }}
                                @if($metrics['response_time']['p95_ms'])<span class="text-base font-normal text-gray-500">ms</span>@endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">P99</div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                {{ $metrics['response_time']['p99_ms'] ?? '—' }}
                                @if($metrics['response_time']['p99_ms'])<span class="text-base font-normal text-gray-500">ms</span>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Memory Usage --}}
            @if($service->getMemoryMetrics()['has_data'])
                @php $mem = $service->getMemoryMetrics(); @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Memory Usage</h2>
                    </div>
                    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Average</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $mem['avg_mb'] ?? '—' }}<span class="text-base font-normal text-gray-500">MB</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Peak</div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                {{ $mem['peak_mb'] ?? '—' }}<span class="text-base font-normal text-gray-500">MB</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Response Time Trend Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Response Time Trend</h2>
                </div>
                <div class="p-6" style="height: 240px;">
                    <canvas id="rtTrendChart"
                        data-labels="{{ $rtLabels }}"
                        data-values="{{ $rtValues }}"
                        data-avg="{{ $avgMs }}">
                    </canvas>
                </div>
            </div>

            {{-- Throughput & Error Trend (Side by Side) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                {{-- Request Volume Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Volume</h2>
                    </div>
                    <div class="p-6" style="height: 200px;">
                        <canvas id="tpTrendChart"
                            data-labels="{{ $tpLabels }}"
                            data-values="{{ $tpValues }}">
                        </canvas>
                    </div>
                </div>

                {{-- Error Trend Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Errors</h2>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $errTrend['total_errors'] }} total
                            </span>
                        </div>
                    </div>
                    <div class="p-6" style="height: 200px;">
                        <canvas id="errTrendChart"
                            data-labels="{{ $errLabels }}"
                            data-values="{{ $errValues }}">
                        </canvas>
                    </div>
                </div>
            </div>

            {{-- SQL Contribution --}}
            @if($service->getSqlContribution()['has_data'])
                @php
                    $sql = $service->getSqlContribution();
                    $appTime = max(0, $sql['total_request_time_ms'] - $sql['total_sql_time_ms']);
                    $sqlPercent = $sql['sql_contribution_percent'];
                    $appPercent = 100 - $sqlPercent;
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Time Breakdown</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">SQL Time</div>
                                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($sql['total_sql_time_ms']) }}<span class="text-base font-normal text-gray-500">ms</span>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $sql['query_count'] }} queries</div>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Application / Other</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($appTime) }}<span class="text-base font-normal text-gray-500">ms</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex h-3 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                                <div class="bg-indigo-500" style="width: {{ $sqlPercent }}%;"></div>
                                <div class="bg-gray-400" style="width: {{ $appPercent }}%;"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span>SQL {{ $sqlPercent }}%</span>
                                <span>App {{ $appPercent }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Slowest Endpoints --}}
            @php $endpoints = $service->getSlowestEndpoints(); @endphp
            @if(count($endpoints) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Slowest Endpoints</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Endpoint</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Avg</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">P95</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Max</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Requests</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($endpoints as $ep)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-3 font-mono text-gray-900 dark:text-white text-xs max-w-xs truncate">{{ $ep['endpoint'] }}</td>
                                        <td class="px-6 py-3 text-right font-mono text-gray-900 dark:text-white">{{ $ep['avg_duration_ms'] }} ms</td>
                                        <td class="px-6 py-3 text-right font-mono text-amber-600 dark:text-amber-400">{{ $ep['p95_duration_ms'] ? $ep['p95_duration_ms'].' ms' : '—' }}</td>
                                        <td class="px-6 py-3 text-right font-mono text-red-600 dark:text-red-400">{{ $ep['max_duration_ms'] }} ms</td>
                                        <td class="px-6 py-3 text-right text-gray-500 dark:text-gray-400">{{ number_format($ep['request_count']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Recent Slow Requests --}}
            @php $slowReqs = $service->getRecentSlowRequests(); @endphp
            @if(count($slowReqs) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Slow Requests</h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($slowReqs as $req)
                            <a href="{{ route('organizations.projects.requests.show', [$organization, $project, $req['uuid']]) }}"
                               class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($req['method'] === 'GET') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($req['method'] === 'POST') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif(in_array($req['method'], ['PUT','PATCH'])) bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 @endif">
                                        {{ $req['method'] }}
                                    </span>
                                    <div>
                                        <div class="text-sm font-mono text-gray-900 dark:text-white">{{ $req['path'] }}</div>
                                        @if($req['route_name'])<div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($req['route_name'], 30) }}</div>@endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-red-600 dark:text-red-400">{{ number_format($req['duration_ms']) }} ms</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($req['requested_at'])->format('M j, H:i') }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Commands Context --}}
            @php $cmdCtx = $service->getCommandsContext(); @endphp
            @if($cmdCtx['has_data'])
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Artisan Commands</h2>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $cmdCtx['total_count'] }} executed</span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach(array_slice($cmdCtx['commands'], 0, 5) as $cmd)
                            <div class="flex items-center justify-between px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($cmd['status'] === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($cmd['status'] === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 @endif">
                                        {{ $cmd['status'] }}
                                    </span>
                                    <span class="text-sm font-mono text-gray-900 dark:text-white">{{ $cmd['command_name'] }}</span>
                                </div>
                                <div class="text-right flex items-center gap-3">
                                    @if($cmd['duration_ms'] !== null)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($cmd['duration_ms']) }} ms</div>
                                    @endif
                                    @if($cmd['exit_code'] !== null)
                                        <span class="text-xs font-mono {{ $cmd['exit_code'] === 0 ? 'text-green-600' : 'text-red-600' }}">exit {{ $cmd['exit_code'] }}</span>
                                    @endif
                                    <div class="text-xs text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($cmd['created_at'])->format('M j, H:i') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Job Context --}}
            @php $jobsCtx = $service->getJobsContext(); @endphp
            @if($jobsCtx['has_data'])
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Background Jobs</h2>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $jobsCtx['dispatched_count'] }} dispatched</span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach(array_slice($jobsCtx['jobs'], 0, 5) as $job)
                            <div class="flex items-center justify-between px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($job['status'] === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($job['status'] === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 @endif">
                                        {{ $job['status'] }}
                                    </span>
                                    <span class="text-sm font-mono text-gray-900 dark:text-white">{{ class_basename($job['job_name']) }}</span>
                                </div>
                                <div class="text-right">
                                    @if($job['duration_ms'])<div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($job['duration_ms']) }} ms</div>@endif
                                    <div class="text-xs text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($job['created_at'])->format('M j, H:i') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
