import { Link, usePage } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import AppLayout from '@/layouts/AppLayout';
import {
    Activity,
    AlertCircle,
    AlertTriangle,
    ArrowRight,
    Building2,
    CheckCircle2,
    ChevronDown,
    FolderOpen,
    HeartPulse,
    RefreshCw,
    XCircle,
} from 'lucide-react';
import {
    Chart,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
    Filler,
    Tooltip,
    Legend,
    DoughnutController,
    LineController,
} from 'chart.js';

Chart.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
    Filler,
    Tooltip,
    Legend,
    DoughnutController,
    LineController,
);

export default function Dashboard() {
    const { user, organizations, selectedOrg, globalStats, dashboardData, timeRange } = usePage().props;

    const timeRangeLabel = {
        '1h': 'Last 1 Hour',
        '24h': 'Last 24 Hours',
        '7d': 'Last 7 Days',
        '30d': 'Last 30 Days',
    }[timeRange] || 'Last 24 Hours';

    const summary = dashboardData?.projects_summary || {};
    const requests = dashboardData?.requests || {};
    const exceptions = dashboardData?.exceptions || {};
    const health = dashboardData?.health || {};
    const recentActivity = dashboardData?.recent_activity || [];
    const topProjects = dashboardData?.top_projects || [];

    const totalProjects = summary.total || 0;
    const healthyCount = summary.healthy || 0;
    const warningCount = summary.warning || 0;
    const criticalCount = summary.critical || 0;
    const noDataCount = summary.no_data || 0;

    const doughnutCanvasRef = useRef(null);
    const lineCanvasRef = useRef(null);
    const doughnutChartRef = useRef(null);
    const lineChartRef = useRef(null);
    const [isSpinning, setIsSpinning] = useState(false);

    useEffect(() => {
        if (!doughnutCanvasRef.current || !lineCanvasRef.current) return;

        // Donut chart - ensure at least some data for visibility
        if (doughnutChartRef.current) doughnutChartRef.current.destroy();
        const chartData = [healthyCount, warningCount, criticalCount, noDataCount];
        const totalData = chartData.reduce((a, b) => a + b, 0);
        
        // If all zeros, show placeholder data to make chart visible
        const displayData = totalData === 0 ? [0, 0, 0, 1] : chartData;
        
        doughnutChartRef.current = new Chart(doughnutCanvasRef.current, {
            type: 'doughnut',
            data: {
                labels: ['Healthy', 'Warning', 'Critical', 'No Data'],
                datasets: [{
                    data: displayData,
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', totalData === 0 ? '#9ca3af' : '#9ca3af'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                animation: { duration: 600 },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true },
                },
            },
        });

        // Line chart
        const requestChartData = requests?.chart_data || { labels: [], requests: [], errors: [] };
        if (lineChartRef.current) lineChartRef.current.destroy();
        lineChartRef.current = new Chart(lineCanvasRef.current, {
            type: 'line',
            data: {
                labels: requestChartData.labels || [],
                datasets: [
                    {
                        label: 'Requests',
                        data: requestChartData.requests || [],
                        borderColor: '#7c3aed',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: '#7c3aed',
                        pointBorderColor: '#7c3aed',
                        pointHoverRadius: 5,
                    },
                    {
                        label: 'Errors',
                        data: requestChartData.errors || [],
                        borderColor: '#ef4444',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ef4444',
                        pointHoverRadius: 5,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'start',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 2,
                            pointWidth: 8,
                            pointHeight: 8,
                            usePointStyle: true,
                            font: { size: 11 },
                            color: '#6b7280',
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#9ca3af' } },
                    y: { grid: { color: 'rgba(156, 163, 175, 0.15)' }, ticks: { font: { size: 11 }, color: '#9ca3af' } },
                },
            },
        });

        return () => {
            if (doughnutChartRef.current) doughnutChartRef.current.destroy();
            if (lineChartRef.current) lineChartRef.current.destroy();
        };
    }, [dashboardData, healthyCount, warningCount, criticalCount, noDataCount]);

    const activityStatusColor = {
        critical: 'bg-red-500',
        warning: 'bg-orange-500',
        info: 'bg-blue-500',
    };

    // Build stat cards from summary data + aggregation
    const statCards = [
        {
            title: 'Organizations',
            value: globalStats?.total_organizations || 0,
            change: null,
            icon: Building2,
            iconBg: 'bg-cyan-100 dark:bg-cyan-900/30',
            iconColor: 'text-cyan-600 dark:text-cyan-400',
        },
        {
            title: 'Projects',
            value: globalStats?.total_projects || 0,
            change: null,
            icon: FolderOpen,
            iconBg: 'bg-blue-100 dark:bg-blue-900/30',
            iconColor: 'text-blue-600 dark:text-blue-400',
        },
        {
            title: 'Healthy',
            value: healthyCount,
            change: null,
            icon: CheckCircle2,
            iconBg: 'bg-green-100 dark:bg-green-900/30',
            iconColor: 'text-green-600 dark:text-green-400',
        },
        {
            title: 'Warning',
            value: warningCount,
            change: null,
            icon: AlertTriangle,
            iconBg: 'bg-orange-100 dark:bg-orange-900/30',
            iconColor: 'text-orange-600 dark:text-orange-400',
        },
        {
            title: 'Critical',
            value: criticalCount,
            change: null,
            icon: XCircle,
            iconBg: 'bg-red-100 dark:bg-red-900/30',
            iconColor: 'text-red-600 dark:text-red-400',
        },
        {
            title: 'No Data',
            value: noDataCount,
            change: null,
            icon: HeartPulse,
            iconBg: 'bg-purple-100 dark:bg-purple-900/30',
            iconColor: 'text-purple-600 dark:text-purple-400',
        },
    ];

    return (
        <AppLayout>
            <div className="p-6 space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">
                            Welcome back, {user?.name?.split(' ')[0] || 'User'} 👋
                        </h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            Here's what's happening across your organizations and projects.
                        </p>
                    </div>
                    <div className="flex items-center gap-3">
                        {/* Time Range Dropdown */}
                        <div className="relative">
                            <select
                                value={timeRange}
                                onChange={(e) => {
                                    window.location.href = `/dashboard?range=${e.target.value}`;
                                }}
                                className="appearance-none pl-3 pr-8 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 cursor-pointer focus:outline-none focus:ring-2 focus:ring-cyan-500"
                            >
                                <option value="1h">Last 1 Hour</option>
                                <option value="24h">Last 24 Hours</option>
                                <option value="7d">Last 7 Days</option>
                                <option value="30d">Last 30 Days</option>
                            </select>
                            <ChevronDown className="w-4 h-4 absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                        </div>
                        <button
                            onClick={() => {
                                setIsSpinning(true);
                                window.location.reload();
                            }}
                            className="p-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                            title="Refresh"
                        >
                            <RefreshCw className={`w-4 h-4 ${isSpinning ? 'animate-spin_once' : ''}`} />
                        </button>
                    </div>
                </div>

                {/* Stats Cards Row */}
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    {statCards.map((card) => {
                        const Icon = card.icon;
                        const change = card.change;
                        const isUp = change?.direction === 'up';
                        const isDown = change?.direction === 'down';
                        const isNeutral = change?.direction === 'neutral' || !change;

                        const changeColor = card.title === 'Critical'
                            ? (isDown ? 'text-green-500' : 'text-red-500')
                            : isUp ? 'text-green-500' : (isDown ? 'text-red-500' : 'text-gray-500');

                        return (
                            <div
                                key={card.title}
                                className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 flex flex-col gap-2"
                            >
                                {/* Icon + Title in a column, left-aligned */}
                                <div className="flex items-center gap-3">
                                    <div className={`w-10 h-10 rounded-lg ${card.iconBg} flex items-center justify-center flex-shrink-0`}>
                                        <Icon className={`w-5 h-5 ${card.iconColor}`} />
                                    </div>
                                    <span className="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {card.title}
                                    </span>
                                </div>

                                {/* Count below title (aligned with icon start) */}
                                <div className="pl-[3.5rem]">
                                    <div className="text-3xl font-bold text-gray-900 dark:text-white leading-none">
                                        {card.value}
                                    </div>
                                    <div className={`text-xs font-medium ${changeColor} mt-1`}>
                                        {!change || isNeutral ? (
                                            '— 0%'
                                        ) : (
                                            <>
                                                {isUp ? '▲' : '▼'} {change.percent}%
                                            </>
                                        )}
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>

                {/* Middle Section - 3 columns */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Projects Health Overview - Donut Chart */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                        <h2 className="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                            Projects Health Overview
                        </h2>
                        {/* Chart + Legend side by side */}
                        <div className="flex items-center gap-6">
                            {/* Donut Chart */}
                            <div className="relative flex-shrink-0 w-40 h-40">
                                <canvas ref={doughnutCanvasRef} />
                                <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div className="text-center">
                                        <div className="text-2xl font-bold text-gray-900 dark:text-white">{globalStats?.total_projects || 0}</div>
                                        <div className="text-xs text-gray-500">Total</div>
                                    </div>
                                </div>
                            </div>

                            {/* Legend on the right */}
                            <div className="flex-1 space-y-3">
                                {[
                                    { label: 'Healthy', count: healthyCount, pct: totalProjects > 0 ? ((healthyCount / totalProjects) * 100).toFixed(1) : '0.0', color: '#10b981' },
                                    { label: 'Warning', count: warningCount, pct: totalProjects > 0 ? ((warningCount / totalProjects) * 100).toFixed(1) : '0.0', color: '#f59e0b' },
                                    { label: 'Critical', count: criticalCount, pct: totalProjects > 0 ? ((criticalCount / totalProjects) * 100).toFixed(1) : '0.0', color: '#ef4444' },
                                    { label: 'No Data', count: noDataCount, pct: totalProjects > 0 ? ((noDataCount / totalProjects) * 100).toFixed(1) : '0.0', color: '#9ca3af' },
                                ].map((item) => (
                                    <div key={item.label} className="flex items-center justify-between">
                                        <div className="flex items-center gap-2">
                                            <span className="w-2.5 h-2.5 rounded-full flex-shrink-0" style={{ backgroundColor: item.color }}></span>
                                            <span className="text-sm text-gray-600 dark:text-gray-400">{item.label}</span>
                                        </div>
                                        <div className="flex items-center gap-3">
                                            <span className="text-sm font-semibold text-gray-900 dark:text-white">{item.count}</span>
                                            <span className="text-xs text-gray-500 w-12 text-right">{item.pct}%</span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Projects Needing Attention */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                        <h2 className="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                            Projects Needing Attention
                        </h2>
                        <div className="space-y-3">
                            {topProjects.filter(p => p.status !== 'healthy').slice(0, 4).map((project) => (
                                <Link
                                    key={project.id}
                                    href={`/organizations/${project.organization_id}/projects/${project.id}`}
                                    className="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-slate-700/50 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                                >
                                    <div className="flex items-center gap-3">
                                        <div className={`w-2 h-2 rounded-full flex-shrink-0 ${project.status === 'critical' ? 'bg-red-500' : 'bg-orange-500'}`}></div>
                                        <div>
                                            <p className="text-sm font-medium text-gray-900 dark:text-white">{project.name}</p>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">{project.organization_name}</p>
                                        </div>
                                    </div>
                                    <span className={`text-xs font-medium px-2 py-0.5 rounded-full ${
                                        project.status === 'critical'
                                            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                            : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'
                                    }`}>
                                        {project.status === 'critical' ? 'Critical' : 'Warning'}
                                    </span>
                                </Link>
                            ))}
                            {topProjects.length === 0 ? (
                                <div className="text-center py-8">
                                    <FolderOpen className="w-10 h-10 text-gray-300 mx-auto mb-2" />
                                    <p className="text-sm text-gray-500 dark:text-gray-400">No Projects yet</p>
                                </div>
                            ) : topProjects.filter(p => p.status !== 'healthy').length === 0 ? (
                                <div className="text-center py-8">
                                    <CheckCircle2 className="w-10 h-10 text-green-500 mx-auto mb-2" />
                                    <p className="text-sm text-gray-500 dark:text-gray-400">All projects healthy!</p>
                                </div>
                            ) : null}
                        </div>
                        <Link
                            href="/projects"
                            className="flex items-center justify-center gap-1 mt-4 text-xs text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 font-medium"
                        >
                            View all projects <ArrowRight className="w-3 h-3" />
                        </Link>
                    </div>

                    {/* Request Overview */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="text-sm font-semibold text-gray-900 dark:text-white">Request Overview (All Projects)</h2>
                            <span className="text-xs text-gray-500 dark:text-gray-400">{timeRangeLabel}</span>
                        </div>

                        {/* Metric boxes with dividers */}
                        <div className="grid grid-cols-3 mb-4">
                            {/* Requests */}
                            <div className="px-3 py-2 text-center">
                                <div className="text-xs text-gray-500 dark:text-gray-400 mb-1">Requests</div>
                                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                                    {requests.total ? (requests.total >= 1000 ? `${(requests.total / 1000).toFixed(1)}K` : requests.total) : '0'}
                                </div>
                                {requests.change && (
                                    <div className={`text-xs font-medium mt-1 ${
                                        requests.change.direction === 'up' ? 'text-green-500' :
                                        requests.change.direction === 'down' ? 'text-red-500' : 'text-gray-500'
                                    }`}>
                                        {requests.change.direction === 'up' ? '▲' : '▼'} {requests.change.percent}%
                                    </div>
                                )}
                            </div>

                            {/* Divider */}
                            <div className="border-l border-gray-200 dark:border-slate-700 px-3 py-2 text-center">
                                <div className="text-xs text-gray-500 dark:text-gray-400 mb-1">Errors</div>
                                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                                    {requests.errors ? (requests.errors >= 1000 ? `${(requests.errors / 1000).toFixed(1)}K` : requests.errors) : '0'}
                                </div>
                                {requests.change && (
                                    <div className={`text-xs font-medium mt-1 ${
                                        requests.change.direction === 'down' ? 'text-green-500' : 'text-red-500'
                                    }`}>
                                        {requests.change.direction === 'down' ? '▼' : '▲'} {requests.change.percent}%
                                    </div>
                                )}
                            </div>

                            {/* Divider */}
                            <div className="border-l border-gray-200 dark:border-slate-700 px-3 py-2 text-center">
                                <div className="text-xs text-gray-500 dark:text-gray-400 mb-1">Error Rate</div>
                                <div className="text-2xl font-bold text-gray-900 dark:text-white">
                                    {requests.error_rate ? `${requests.error_rate}%` : '0%'}
                                </div>
                                {requests.change && (
                                    <div className={`text-xs font-medium mt-1 ${
                                        requests.change.direction === 'up' ? 'text-red-500' : 'text-green-500'
                                    }`}>
                                        {requests.change.direction === 'up' ? '▲' : '▼'} {requests.change.percent}%
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Line Chart */}
                        <div style={{ height: 160 }}>
                            <canvas ref={lineCanvasRef} />
                        </div>
                    </div>
                </div>

                {/* Bottom Section - 2 columns */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Recent Activity */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                        <h2 className="text-sm font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
                        <div className="space-y-2">
                            {recentActivity.slice(0, 5).map((item, idx) => (
                                <div key={idx} className="flex items-start gap-3 py-2">
                                    <span className={`w-2 h-2 rounded-full mt-1.5 flex-shrink-0 ${activityStatusColor[item.status] || 'bg-blue-500'}`}></span>
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2">
                                            <span className="text-xs font-medium text-gray-700 dark:text-gray-300 capitalize">{item.type}</span>
                                            {item.tag && (
                                                <span className={`text-xs px-1.5 py-0.5 rounded-full font-medium ${
                                                    item.tag === 'Critical' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                                    item.tag === 'Warning' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' :
                                                    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                                }`}>{item.tag}</span>
                                            )}
                                        </div>
                                        <p className="text-xs text-gray-500 dark:text-gray-400 truncate">{item.message}</p>
                                        <p className="text-xs text-gray-400 dark:text-gray-500">{item.project_name} · {item.time_ago}</p>
                                    </div>
                                </div>
                            ))}
                            {recentActivity.length === 0 && (
                                <div className="text-center py-8">
                                    <Activity className="w-10 h-10 text-gray-300 mx-auto mb-2" />
                                    <p className="text-sm text-gray-500 dark:text-gray-400">No recent activity</p>
                                </div>
                            )}
                        </div>
                        <Link href="/activity" className="flex items-center justify-center gap-1 mt-4 text-xs text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 font-medium">
                            View all activity <ArrowRight className="w-3 h-3" />
                        </Link>
                    </div>

                    {/* Top Projects by Request Volume */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="text-sm font-semibold text-gray-900 dark:text-white">Top Projects by Request Volume</h2>
                            <span className="text-xs text-gray-500 dark:text-gray-400">{timeRangeLabel}</span>
                        </div>
                        <div className="space-y-3">
                            {topProjects.slice(0, 5).map((project, idx) => {
                                const maxRequests = topProjects[0]?.requests || 1;
                                const barWidth = maxRequests > 0 ? Math.round((project.requests / maxRequests) * 100) : 0;
                                return (
                                    <Link
                                        key={project.id}
                                        href={`/organizations/${project.organization_id}/projects/${project.id}`}
                                        className="block group"
                                    >
                                        <div className="flex items-center justify-between mb-1">
                                            <div className="flex items-center gap-2">
                                                <span className="text-xs text-gray-400 w-4">{idx + 1}</span>
                                                <div>
                                                    <p className="text-sm font-medium text-gray-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400">{project.name}</p>
                                                    <p className="text-xs text-gray-500 dark:text-gray-400">{project.organization_name}</p>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-sm font-medium text-gray-900 dark:text-white">
                                                    {project.requests >= 1000 ? `${(project.requests / 1000).toFixed(1)}K` : project.requests}
                                                </p>
                                                <p className={`text-xs font-medium ${
                                                    project.error_rate > 10 ? 'text-red-600 dark:text-red-400' :
                                                    project.error_rate > 5 ? 'text-orange-600 dark:text-orange-400' :
                                                    'text-green-600 dark:text-green-400'
                                                }`}>{project.error_rate}% errors</p>
                                            </div>
                                        </div>
                                        <div className="ml-6 h-1 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div className="h-full bg-purple-500 rounded-full" style={{ width: `${barWidth}%` }}></div>
                                        </div>
                                    </Link>
                                );
                            })}
                            {topProjects.length === 0 && (
                                <div className="text-center py-8">
                                    <FolderOpen className="w-10 h-10 text-gray-300 mx-auto mb-2" />
                                    <p className="text-sm text-gray-500 dark:text-gray-400">No project data yet</p>
                                </div>
                            )}
                        </div>
                        <Link href="/projects" className="flex items-center justify-center gap-1 mt-4 text-xs text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 font-medium">
                            View all projects <ArrowRight className="w-3 h-3" />
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
