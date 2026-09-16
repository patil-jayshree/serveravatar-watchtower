import { Link, usePage, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Terminal, Search, X, RefreshCw, Clock, CheckCircle, XCircle, ChevronLeft, ChevronRight } from 'lucide-react';
import { useState } from 'react';

const STATUS_COLORS = {
    completed: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    failed: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
};

function getStatusColor(status) {
    return STATUS_COLORS[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
}

function formatDuration(ms) {
    if (!ms && ms !== 0) return '-';
    if (ms < 1000) return `${Math.round(ms)}ms`;
    return `${(ms / 1000).toFixed(1)}s`;
}

function formatTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === today.toDateString()) {
        return 'Today';
    } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    }
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

export default function CommandsIndex() {
    const { organization, project, commands, stats, filters } = usePage().props;
    const { data: commandsData, links: pagination } = commands || { data: [], links: null };

    const [search, setSearch] = useState(filters?.search || '');
    const [status, setStatus] = useState(filters?.status || '');
    const [exitCode, setExitCode] = useState(filters?.exit_code || '');
    const [refreshing, setRefreshing] = useState(false);

    const tabs = [
        { key: '', label: 'All', count: stats?.total || 0, color: 'text-gray-600 dark:text-gray-400' },
        { key: 'completed', label: 'Completed', count: stats?.completed || 0, color: 'text-emerald-600 dark:text-emerald-400' },
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
            ...(exitCode && { exit_code: exitCode }),
            ...extraParams,
        };
        router.get(`/organizations/${organization?.uuid}/projects/${project?.uuid}/commands`, params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const clearFilters = () => {
        setSearch('');
        setStatus('');
        setExitCode('');
        router.get(`/organizations/${organization?.uuid}/projects/${project?.uuid}/commands`, {}, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleRefresh = () => {
        setRefreshing(true);
        router.reload({ only: ['commands', 'stats'] });
        setTimeout(() => setRefreshing(false), 500);
    };

    const handlePageChange = (page) => {
        router.get(`/organizations/${organization?.uuid}/projects/${project?.uuid}/commands`, {
            ...filters,
            page,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const hasActiveFilters = search || status || exitCode;

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
                                Commands
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Monitor Artisan commands for {project?.name}
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
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                                    <Terminal className="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                </div>
                                <span className="text-sm font-medium text-gray-500 dark:text-gray-400">Total Commands</span>
                            </div>
                            <p className="text-2xl font-bold text-gray-900 dark:text-white">{stats?.total || 0}</p>
                        </div>

                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                                    <CheckCircle className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <span className="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</span>
                            </div>
                            <p className="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{stats?.completed || 0}</p>
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
                                <div className="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center">
                                    <Clock className="w-5 h-5 text-cyan-600 dark:text-cyan-400" />
                                </div>
                                <span className="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Duration</span>
                            </div>
                            <p className="text-2xl font-bold text-cyan-600 dark:text-cyan-400">{formatDuration(stats?.avg_duration)}</p>
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
                                        placeholder="Search commands..."
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

                                {/* Exit Code Filter */}
                                <select
                                    value={exitCode}
                                    onChange={(e) => handleFilterChange('exit_code', e.target.value)}
                                    className="px-3 py-2.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-600 dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                >
                                    <option value="">All Exit Codes</option>
                                    <option value="0">Success (0)</option>
                                    <option value="1">Failed (1)</option>
                                </select>

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

                    {/* Commands Table */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                        {commandsData && commandsData.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Command</th>
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Exit Code</th>
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                                            <th className="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100 dark:divide-slate-700">
                                        {commandsData.map((cmd) => (
                                            <tr key={cmd.uuid} className="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                                                <td className="px-5 py-4">
                                                    <Link
                                                        href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/commands/${cmd.uuid}`}
                                                        className="block"
                                                    >
                                                        <span className="text-sm font-mono text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors">
                                                            {cmd.command_name}
                                                        </span>
                                                        {cmd.exception_message && (
                                                            <p className="text-xs text-red-600 dark:text-red-400 mt-1 truncate max-w-md">
                                                                {cmd.exception_message}
                                                            </p>
                                                        )}
                                                    </Link>
                                                </td>
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <span className={`inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold ${getStatusColor(cmd.status)}`}>
                                                        {cmd.status}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <span className={`text-sm font-medium ${
                                                        cmd.exit_code === 0
                                                            ? 'text-emerald-600 dark:text-emerald-400'
                                                            : cmd.exit_code !== null
                                                            ? 'text-red-600 dark:text-red-400'
                                                            : 'text-gray-500 dark:text-gray-400'
                                                    }`}>
                                                        {cmd.exit_code !== null ? cmd.exit_code : '-'}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <span className={`text-sm font-medium ${
                                                        cmd.duration_ms > 5000
                                                            ? 'text-red-600 dark:text-red-400'
                                                            : cmd.duration_ms > 2000
                                                            ? 'text-amber-600 dark:text-amber-400'
                                                            : 'text-gray-900 dark:text-white'
                                                    }`}>
                                                        {formatDuration(cmd.duration_ms)}
                                                    </span>
                                                </td>
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                                        <span>{formatDate(cmd.created_at)}</span>
                                                        <span className="mx-1">·</span>
                                                        <span>{formatTime(cmd.created_at)}</span>
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
                                    <Terminal className="w-8 h-8 text-gray-400" />
                                </div>
                                <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No commands found</h3>
                                <p className="text-gray-500 dark:text-gray-400">
                                    {hasActiveFilters
                                        ? 'Try adjusting your filters or search criteria'
                                        : 'Your Artisan commands will appear here once captured'}
                                </p>
                            </div>
                        )}

                        {/* Pagination */}
                        {pagination && pagination.last_page > 1 && (
                            <div className="flex items-center justify-between px-5 py-4 border-t border-gray-200 dark:border-slate-700">
                                <p className="text-sm text-gray-500 dark:text-gray-400">
                                    Showing {((pagination.current_page - 1) * pagination.per_page) + 1} to {Math.min(pagination.current_page * pagination.per_page, pagination.total)} of {pagination.total} commands
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
