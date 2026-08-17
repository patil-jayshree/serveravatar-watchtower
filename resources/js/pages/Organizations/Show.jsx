import { Link, usePage, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Building2, Plus, ArrowRight, Wifi, WifiOff, Server, TrendingUp, TrendingDown, AlertTriangle, ExternalLink, Search, XCircle, X, RefreshCw } from 'lucide-react';
import { useState, useState as useSetState } from 'react';

const steps = [
    {
        num: '1',
        title: 'Create Project',
        desc: 'Add your Laravel application to Watchtower',
    },
    {
        num: '2',
        title: 'Install Agent',
        desc: 'Install the Watchtower agent in your application server',
    },
    {
        num: '3',
        title: 'Connect & Authorize',
        desc: 'Connect your project and authorize telemetry',
    },
    {
        num: '4',
        title: 'Start Monitoring',
        desc: 'Receive real-time insights and stay in control',
    },
];

const badgeColors = [
    { bg: 'bg-cyan-100 dark:bg-cyan-900/40', text: 'text-cyan-700 dark:text-cyan-400' },
    { bg: 'bg-emerald-100 dark:bg-emerald-900/40', text: 'text-emerald-700 dark:text-emerald-400' },
    { bg: 'bg-violet-100 dark:bg-violet-900/40', text: 'text-violet-700 dark:text-violet-400' },
    { bg: 'bg-amber-100 dark:bg-amber-900/40', text: 'text-amber-700 dark:text-amber-400' },
    { bg: 'bg-blue-100 dark:bg-blue-900/40', text: 'text-blue-700 dark:text-blue-400' },
    { bg: 'bg-indigo-100 dark:bg-indigo-900/40', text: 'text-indigo-700 dark:text-indigo-400' },
    { bg: 'bg-teal-100 dark:bg-teal-900/40', text: 'text-teal-700 dark:text-teal-400' },
];

const avatarGradients = [
    'from-cyan-500 to-cyan-600',
    'from-emerald-500 to-emerald-600',
    'from-violet-500 to-violet-600',
    'from-amber-500 to-amber-600',
    'from-blue-500 to-blue-600',
    'from-indigo-500 to-indigo-600',
    'from-teal-500 to-teal-600',
];

const getOrgBadgeColor = () => {
    const index = Math.floor(Math.random() * badgeColors.length);
    return badgeColors[index];
};

const getAvatarGradient = (index) => {
    const colors = [
        'from-cyan-500 to-cyan-600',
        'from-emerald-500 to-emerald-600',
        'from-violet-500 to-violet-600',
        'from-amber-500 to-amber-600',
        'from-blue-500 to-blue-600',
        'from-indigo-500 to-indigo-600',
        'from-teal-500 to-teal-600',
    ];
    return colors[index % colors.length];
};

export default function OrganizationShow() {
    const { organization, projects, projects_pagination } = usePage().props;
    const [search, setSearch] = useState('');
    const [environment, setEnvironment] = useState('');
    const [status, setStatus] = useState('');

    const filteredProjects = (projects || []).filter((p) => {
        const matchSearch = p.name.toLowerCase().includes(search.toLowerCase());
        const matchEnv = !environment || p.environment === environment;
        const matchStatus = !status || (status === 'healthy' && p.is_agent_connected);
        return matchSearch && matchEnv && matchStatus;
    });

    const handleSearch = (e) => {
        setSearch(e.target.value);
    };

    const clearFilters = () => {
        setSearch('');
        setEnvironment('');
        setStatus('');
    };

    const handleRefresh = () => {
        setRefreshing(true);
        router.reload({ only: ['projects', 'projects_pagination'] });
        setTimeout(() => setRefreshing(false), 1000);
    };

    const hasActiveFilters = environment || status;
    const [refreshing, setRefreshing] = useState(false);

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-7xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="flex items-center justify-between mb-6">
                        <div>
                            <Link
                                href="/organizations"
                                className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors mb-2"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Organizations
                            </Link>
                            <div className="flex items-center gap-3">
                                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                    Projects
                                </h1>
                                <span className={`px-3 py-1 text-sm font-medium rounded-full ${getOrgBadgeColor().bg} ${getOrgBadgeColor().text}`}>
                                    {organization?.name}
                                </span>
                            </div>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Manage and monitor the Laravel projects in this organization.
                            </p>
                        </div>
                        <Link
                            href={`/organizations/${organization.id}/projects/create`}
                            className="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors"
                        >
                            <Plus className="w-4 h-4" />
                            New Project
                        </Link>
                    </div>

                    {/* Getting Started Section - Only show when no projects */}
                    {(!projects || projects.length === 0) && (
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden mb-6">
                        {/* Header */}
                        <div className="px-8 pt-8 pb-6">
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center">
                                    <Server className="w-6 h-6 text-cyan-600 dark:text-cyan-400" />
                                </div>
                                <div>
                                    <h2 className="text-xl font-bold text-gray-900 dark:text-white">
                                        Getting Started with Watchtower
                                    </h2>
                                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                        Follow the steps below to add your first project
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Divider */}
                        <div className="border-t border-gray-100 dark:border-slate-700" />

                        {/* Steps Grid */}
                        <div className="grid grid-cols-4 divide-x divide-gray-100 dark:divide-slate-700">
                            {steps.map((step) => (
                                <div key={step.num} className="px-8 py-6 flex flex-col items-center text-center">
                                    <div className="w-10 h-10 rounded-full bg-cyan-500 flex items-center justify-center text-white font-bold text-sm mb-4">
                                        {step.num}
                                    </div>
                                    <h3 className="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                        {step.title}
                                    </h3>
                                    <p className="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                        {step.desc}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                    )}

                    {/* Projects Section */}
                    {projects && projects.length > 0 ? (
                        <>
                            {/* Controls: Search + Filters */}
                            <div className="flex items-center justify-between mb-6">
                                <div className="flex items-center gap-3">
                                    <div className="relative w-72">
                                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                        <input
                                            type="text"
                                            value={search}
                                            onChange={handleSearch}
                                            placeholder="Search projects..."
                                            className="w-full pl-10 pr-10 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                        />
                                        {search && (
                                            <button
                                                onClick={() => setSearch('')}
                                                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                            >
                                                <XCircle className="w-4 h-4" />
                                            </button>
                                        )}
                                    </div>
                                    <select
                                        value={environment}
                                        onChange={(e) => setEnvironment(e.target.value)}
                                        className="px-3 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-600 dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                    >
                                        <option value="">All Environments</option>
                                        <option value="production">Production</option>
                                        <option value="staging">Staging</option>
                                        <option value="development">Development</option>
                                        <option value="local">Local</option>
                                    </select>
                                    <select
                                        value={status}
                                        onChange={(e) => setStatus(e.target.value)}
                                        className="px-3 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-600 dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                    >
                                        <option value="">All Status</option>
                                        <option value="healthy">Healthy</option>
                                        <option value="warning">Warning</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                    {hasActiveFilters && (
                                        <button
                                            onClick={clearFilters}
                                            className="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg text-sm font-medium transition-colors"
                                        >
                                            <X className="w-4 h-4" />
                                            Clear Filters
                                        </button>
                                    )}
                                </div>
                                <button
                                    onClick={handleRefresh}
                                    className="p-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors"
                                >
                                    <RefreshCw className={`w-4 h-4 text-gray-600 dark:text-gray-400 ${refreshing ? 'animate-spin' : ''}`} />
                                </button>
                            </div>

                            {/* Projects Grid */}
                            {filteredProjects.length > 0 ? (
                                <div className="grid grid-cols-1 gap-4 mb-6">
                                    {filteredProjects.map((project, idx) => (
                                        <div
                                            key={project.id}
                                            className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-600 transition-all group flex items-center gap-4"
                                        >
                                            <div className="flex items-center gap-4 w-[450px] flex-shrink-0">
                                                {/* Avatar */}
                                                <div className={`w-12 h-12 rounded-xl bg-gradient-to-br ${getAvatarGradient(idx)} flex items-center justify-center text-white font-bold text-base flex-shrink-0`}>
                                                    {project.name.split(' ').map(w => w.charAt(0).toUpperCase()).join('').slice(0, 2)}
                                                </div>

                                                {/* Name + URL + Meta */}
                                                <div className="min-w-0">
                                                    <h3 className="font-semibold text-gray-900 dark:text-white text-base group-hover:text-cyan-600 dark:group-hover:text-cyan-400 truncate">
                                                        {project.name}
                                                    </h3>
                                                    {project.url && (
                                                        <a
                                                            href={project.url}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            onClick={(e) => e.stopPropagation()}
                                                            className="text-sm text-cyan-600 dark:text-cyan-400 hover:underline inline-flex items-center gap-1"
                                                        >
                                                            {project.url.replace(/^https?:\/\//, '')}
                                                            <ExternalLink className="w-3 h-3" />
                                                        </a>
                                                    )}
                                                    <div className="flex items-center gap-2 mt-1">
                                                        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400">
                                                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                                                            Healthy
                                                        </span>
                                                        <span className="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                                            {project.framework.charAt(0).toUpperCase() + project.framework.slice(1)} {project.version}
                                                        </span>
                                                        <span className="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                                                            <span className="capitalize">{project.environment}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Stats Section */}
                                            <div className="hidden lg:flex items-center gap-4">
                                                {/* Requests */}
                                                <div className="text-center w-24 flex-shrink-0">
                                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Requests</p>
                                                    <div className="flex items-center justify-center gap-1">
                                                        <TrendingUp className="w-3 h-3 text-emerald-500" />
                                                        <span className="text-sm font-bold text-gray-900 dark:text-white">12.4K</span>
                                                    </div>
                                                    <p className="text-xs text-emerald-600 dark:text-emerald-400">↑ 12.5%</p>
                                                </div>

                                                <div className="w-px h-10 bg-gray-200 dark:bg-gray-700 flex-shrink-0" />

                                                {/* Error Rate */}
                                                <div className="text-center w-24 flex-shrink-0">
                                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Error Rate</p>
                                                    <span className="text-sm font-bold text-gray-900 dark:text-white">2.45%</span>
                                                    <p className="text-xs text-emerald-600 dark:text-emerald-400">↓ 4.2%</p>
                                                </div>

                                                <div className="w-px h-10 bg-gray-200 dark:bg-gray-700 flex-shrink-0" />

                                                {/* Agent */}
                                                <div className="text-center w-24 flex-shrink-0">
                                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Agent</p>
                                                    <div className="flex items-center justify-center gap-1">
                                                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                                                        <span className="text-xs font-medium text-emerald-600 dark:text-emerald-400">Connected</span>
                                                    </div>
                                                </div>

                                                <div className="w-px h-10 bg-gray-200 dark:bg-gray-700 flex-shrink-0" />

                                                {/* Last Activity */}
                                                <div className="text-center w-24 flex-shrink-0">
                                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Last Activity</p>
                                                    <span className="text-xs font-medium text-gray-700 dark:text-gray-300">2 min ago</span>
                                                </div>
                                            </div>

                                            {/* Arrow Box - Clickable */}
                                            <Link
                                                href={`/organizations/${organization.id}/projects/${project.id}`}
                                                className="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-slate-700 hover:bg-cyan-100 dark:hover:bg-cyan-900/40 transition-colors flex-shrink-0 ml-auto"
                                            >
                                                <ArrowRight className="w-5 h-5 text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400" />
                                            </Link>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-12">
                                    <div className="flex flex-col items-center justify-center text-center">
                                        <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                                            <Search className="w-8 h-8 text-gray-400" />
                                        </div>
                                        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Projects Found</h3>
                                        <p className="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter criteria</p>
                                    </div>
                                </div>
                            )}

                            {/* Pagination */}
                            {projects_pagination && projects_pagination.total > 5 && (
                                <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-slate-700">
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        Showing {projects.length} of {projects_pagination.total} projects
                                    </p>
                                    <div className="flex items-center gap-1">
                                        {projects_pagination.current_page > 1 && (
                                            <button
                                                onClick={() => router.get(`/organizations/${organization.id}?page=${projects_pagination.current_page - 1}&per_page=5`)}
                                                className="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                                            >
                                                Previous
                                            </button>
                                        )}
                                        {Array.from({ length: projects_pagination.last_page }, (_, i) => i + 1).map((page) => (
                                            <button
                                                key={page}
                                                onClick={() => router.get(`/organizations/${organization.id}?page=${page}&per_page=5`)}
                                                className={`w-8 h-8 text-sm rounded-lg transition-colors ${
                                                    page === projects_pagination.current_page
                                                        ? 'bg-cyan-600 text-white'
                                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700'
                                                }`}
                                            >
                                                {page}
                                            </button>
                                        ))}
                                        {projects_pagination.current_page < projects_pagination.last_page && (
                                            <button
                                                onClick={() => router.get(`/organizations/${organization.id}?page=${projects_pagination.current_page + 1}&per_page=5`)}
                                                className="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                                            >
                                                Next
                                            </button>
                                        )}
                                    </div>
                                </div>
                            )}
                        </>
                    ) : (
                        /* Empty State */
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                            <img
                                src="/images/illustration-checklist-teal.png"
                                alt="No projects"
                                className="w-52 mx-auto mb-4 object-contain"
                                style={{ height: 'auto' }}
                            />
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">No projects yet</h3>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                                Create your first project to start monitoring your Laravel applications.
                            </p>
                            <Link
                                href={`/organizations/${organization.id}/projects/create`}
                                className="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors"
                            >
                                <Plus className="w-4 h-4" />
                                New Project
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
