import { Link, usePage, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Clock, RefreshCw, ChevronLeft, ChevronRight, Search, X, CheckCircle, XCircle, PlayCircle, Calendar } from 'lucide-react';
import { useState } from 'react';

const STATUS_COLORS = {
    healthy: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    completed: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    running: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
    failed: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
    missed: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400',
};

function getStatusColor(status) {
    return STATUS_COLORS[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
}

export default function SchedulerIndex() {
    const { organization, project, tasks, stats, filters } = usePage().props;
    const { data: tasksData, links: pagination } = tasks || { data: [], links: null };

    const [search, setSearch] = useState(filters?.search || '');
    const [status, setStatus] = useState(filters?.status || '');
    const [refreshing, setRefreshing] = useState(false);

    const tabs = [
        { key: '', label: 'All', count: stats?.total || 0, color: 'text-gray-600 dark:text-gray-400' },
        { key: 'healthy', label: 'Healthy', count: stats?.healthy || 0, color: 'text-emerald-600 dark:text-emerald-400' },
        { key: 'running', label: 'Running', count: stats?.running || 0, color: 'text-blue-600 dark:text-blue-400' },
        { key: 'failed', label: 'Failed', count: stats?.failed || 0, color: 'text-red-600 dark:text-red-400' },
    ];

    const handleSearch = (e) => {
        e.preventDefault();
        applyFilters({ search });
    };

    const handleFilterChange = (key, value) => {
        applyFilters({ [key]: value });
    };

    const applyFilters = (extraParams = {}) => {
        const params = {
            ...(search && { search }),
            ...(status && { status }),
            ...extraParams,
        };
        router.get(`/organizations/${organization?.uuid}/projects/${project?.uuid}/scheduler`, params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const clearFilters = () => {
        setSearch('');
        setStatus('');
        router.get(`/organizations/${organization?.uuid}/projects/${project?.uuid}/scheduler`, {}, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleRefresh = () => {
        setRefreshing(true);
        router.reload({ only: ['tasks', 'stats'] });
        setTimeout(() => setRefreshing(false), 500);
    };

    const handlePageChange = (page) => {
        router.get(`/organizations/${organization?.uuid}/projects/${project?.uuid}/scheduler`, {
            ...filters,
            page,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const hasActiveFilters = search || status;

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-7xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="flex items-center justify-between mb-6">
                        <div>
                            <Link
                                href={`/organizations/${organization?.uuid}/projects/${project?.uuid}`}
                                className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors mb-2"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to {project?.name}
                            </Link>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                Scheduler
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Monitor scheduled tasks for {project?.name}
                            </p>
                        </div>
                        <button
                            onClick={handleRefresh}
                            className="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors"
                        >
                            <RefreshCw className={`w-4 h-4 ${refreshing ? 'animate-spin' : ''}`} />
                            Refresh
                        </button>
                    </div>

                    {/* Stats Cards */}
                    <div className="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                                    <Clock className="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                </div>
                                <span className="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tasks</span>
                            </div>
                            <p className="text-2xl font-bold text-gray-900 dark:text-white">{stats?.total || 0}</p>
                        </div>

                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                                    <CheckCircle className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <span className="text-sm font-medium text-gray-500 dark:text-gray-400">Healthy</span>
                            </div>
                            <p className="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{stats?.healthy || 0}</p>
                        </div>

                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                                    <PlayCircle className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <span className="text-sm font-medium text-gray-500 dark:text-gray-400">Running</span>
                            </div>
                            <p className="text-2xl font-bold text-blue-600 dark:text-blue-400">{stats?.running || 0}</p>
                        </div>

                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                                    <XCircle className="w-5 h-5 text-red-600 dark:text-red-400" />
                                </div>
                                <span className="text-sm font-medium text-gray-500 dark:text-gray-400">Failed</span>
                            </div>
                            <p className="text-2xl font-bold text-red-600 dark:text-red-400">{stats?.failed || 0}</p>
                        </div>

                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center">
                                    <Clock className="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                                </div>
                                <span className="text-sm font-medium text-gray-500 dark:text-gray-400">Missed</span>
                            </div>
                            <p className="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{stats?.missed || 0}</p>
                        </div>
                    </div>

                    {/* Filters Bar */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 mb-6">
                        {/* Status Tabs */}
                        <div className="border-b border-gray-200 dark:border-slate-700 px-5 pt-4">
                            <div className="flex items-center gap-1">
                                {tabs.map((tab) => (
                                    <button
                                        key={tab.key}
                                        onClick={() => handleFilterChange('status', tab.key)}
                                        className={`px-4 py-2.5 rounded-t-lg text-sm font-medium transition-colors border-b-2 ${
                                            (status || '') === tab.key
                                                ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-gray-50 dark:bg-slate-700/50'
                                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700/30'
                                        }`}
                                    >
                                        {tab.label}
                                        <span className={`ml-2 px-2 py-0.5 rounded-full text-xs font-semibold ${
                                            (status || '') === tab.key
                                                ? 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-400'
                                                : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400'
                                        }`}>
                                            {tab.count}
                                        </span>
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Filter Controls */}
                        <div className="p-5">
                            <div className="flex items-center gap-3 flex-wrap">
                                {/* Search */}
                                <form onSubmit={handleSearch} className="relative flex-1 min-w-[200px]">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input
                                        type="text"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        placeholder="Search tasks..."
                                        className="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                    />
                                    {search && (
                                        <button
                                            type="button"
                                            onClick={() => { setSearch(''); applyFilters({ search: '' }); }}
                                            className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                        >
                                            <X className="w-4 h-4" />
                                        </button>
                                    )}
                                </form>

                                {hasActiveFilters && (
                                    <button
                                        onClick={clearFilters}
                                        className="inline-flex items-center gap-1.5 px-3 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg text-sm font-medium transition-colors"
                                    >
                                        <X className="w-4 h-4" />
                                        Clear
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Tasks Table */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                        {tasksData && tasksData.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Task</th>
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Frequency</th>
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Run</th>
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Next Run</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100 dark:divide-slate-700">
                                        {tasksData.map((task) => (
                                            <tr key={task.uuid} className="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                                                <td className="px-5 py-4">
                                                    <Link
                                                        href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/scheduler/${task.uuid}`}
                                                        className="block"
                                                    >
                                                        <span className="text-sm font-mono text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors">
                                                            {task.task_name}
                                                        </span>
                                                        {task.description && (
                                                            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate max-w-md">
                                                                {task.description}
                                                            </p>
                                                        )}
                                                    </Link>
                                                </td>
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <span className="text-sm text-gray-600 dark:text-gray-400 capitalize">
                                                        {task.task_type || 'command'}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <span className={`inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold ${getStatusColor(task.last_status)}`}>
                                                        {task.last_status || 'unknown'}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <div className="flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400">
                                                        <Calendar className="w-4 h-4" />
                                                        {task.frequency || task.expression || '-'}
                                                    </div>
                                                </td>
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                                        {task.last_run_at ? (
                                                            <>
                                                                <span>{formatDate(task.last_run_at)}</span>
                                                                <span className="mx-1">·</span>
                                                                <span>{formatDateTime(task.last_run_at)}</span>
                                                            </>
                                                        ) : (
                                                            <span>Never</span>
                                                        )}
                                                    </div>
                                                </td>
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                                        {task.next_run_at ? (
                                                            <>
                                                                <span>{formatDate(task.next_run_at)}</span>
                                                                <span className="mx-1">·</span>
                                                                <span>{formatDateTime(task.next_run_at)}</span>
                                                            </>
                                                        ) : (
                                                            <span>-</span>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="p-12 text-center">
                                <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                                    <Clock className="w-8 h-8 text-gray-400" />
                                </div>
                                <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No tasks found</h3>
                                <p className="text-gray-500 dark:text-gray-400">
                                    {hasActiveFilters
                                        ? 'Try adjusting your filters or search criteria'
                                        : 'Your scheduled tasks will appear here once captured'}
                                </p>
                            </div>
                        )}

                        {/* Pagination */}
                        {pagination && pagination.last_page > 1 && (
                            <div className="flex items-center justify-between px-5 py-4 border-t border-gray-200 dark:border-slate-700">
                                <p className="text-sm text-gray-500 dark:text-gray-400">
                                    Showing {((pagination.current_page - 1) * pagination.per_page) + 1} to {Math.min(pagination.current_page * pagination.per_page, pagination.total)} of {pagination.total} tasks
                                </p>
                                <div className="flex items-center gap-1">
                                    <button
                                        onClick={() => handlePageChange(pagination.current_page - 1)}
                                        disabled={pagination.current_page <= 1}
                                        className="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        <ChevronLeft className="w-5 h-5" />
                                    </button>
                                    {Array.from({ length: Math.min(5, pagination.last_page) }, (_, i) => {
                                        let pageNum;
                                        if (pagination.last_page <= 5) {
                                            pageNum = i + 1;
                                        } else if (pagination.current_page <= 3) {
                                            pageNum = i + 1;
                                        } else if (pagination.current_page >= pagination.last_page - 2) {
                                            pageNum = pagination.last_page - 4 + i;
                                        } else {
                                            pageNum = pagination.current_page - 2 + i;
                                        }
                                        return (
                                            <button
                                                key={pageNum}
                                                onClick={() => handlePageChange(pageNum)}
                                                className={`w-9 h-9 text-sm rounded-lg font-medium transition-colors ${
                                                    pageNum === pagination.current_page
                                                        ? 'bg-cyan-600 text-white'
                                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700'
                                                }`}
                                            >
                                                {pageNum}
                                            </button>
                                        );
                                    })}
                                    <button
                                        onClick={() => handlePageChange(pagination.current_page + 1)}
                                        disabled={pagination.current_page >= pagination.last_page}
                                        className="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        <ChevronRight className="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
