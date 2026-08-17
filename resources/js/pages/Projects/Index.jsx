import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';
import { Building2, FolderOpen, Plus, Search, ArrowRight, Wifi, WifiOff } from 'lucide-react';

export default function ProjectsIndex() {
    const { projects, isGlobal, filters } = usePage().props;
    const [search, setSearch] = useState(filters?.search || '');

    const filteredProjects = (projects || []).filter((p) =>
        p.name.toLowerCase().includes(search.toLowerCase())
    );

    const handleSearch = (e) => {
        setSearch(e.target.value);
        const params = new URLSearchParams(window.location.search);
        if (e.target.value) {
            params.set('search', e.target.value);
        } else {
            params.delete('search');
        }
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    };

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-7xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="flex items-center justify-between mb-6">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                {isGlobal ? 'Projects' : 'Projects'}
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {isGlobal
                                    ? `All projects across ${projects?.length || 0} organization${(projects?.length || 0) !== 1 ? 's' : ''}`
                                    : `Managing ${projects?.length || 0} project${(projects?.length || 0) !== 1 ? 's' : ''}`}
                            </p>
                        </div>
                        <Link
                            href="/organizations/create"
                            className="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors"
                        >
                            <Plus className="w-4 h-4" />
                            Add Project
                        </Link>
                    </div>

                    {/* Search */}
                    <div className="flex items-center gap-3 mb-6">
                        <div className="relative w-80">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                type="text"
                                placeholder="Search projects..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                onKeyDown={(e) => e.key === 'Enter' && handleSearch(e)}
                                className="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    {/* Content */}
                    {filteredProjects.length > 0 ? (
                        <>
                            {/* Projects Grid */}
                            <div className="grid grid-cols-2 gap-6 mb-6">
                                {filteredProjects.map((project) => (
                                    <Link
                                        key={project.id}
                                        href={`/organizations/${project.organization_id}/projects/${project.id}`}
                                        className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 hover:border-cyan-300 dark:hover:border-cyan-600 transition-all group"
                                    >
                                        {/* Top Row: Avatar + Name + Org */}
                                        <div className="flex items-start justify-between mb-5">
                                            <div className="flex items-center gap-3">
                                                <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-cyan-700 flex items-center justify-center text-white font-semibold text-base flex-shrink-0">
                                                    {project.name.charAt(0).toUpperCase()}
                                                </div>
                                                <div>
                                                    <h3 className="font-semibold text-gray-900 dark:text-white text-base group-hover:text-cyan-600 dark:group-hover:text-cyan-400">
                                                        {project.name}
                                                    </h3>
                                                    {isGlobal && project.organization_name && (
                                                        <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1">
                                                            <Building2 className="w-3 h-3" />
                                                            {project.organization_name}
                                                        </p>
                                                    )}
                                                </div>
                                            </div>
                                            {/* Agent Status */}
                                            {project.is_agent_connected ? (
                                                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                                    <Wifi className="w-3 h-3" />
                                                    Connected
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                                    <WifiOff className="w-3 h-3" />
                                                    Disconnected
                                                </span>
                                            )}
                                        </div>

                                        {/* Meta Row */}
                                        <div className="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-slate-700">
                                            {project.environment && (
                                                <span className="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">
                                                    {project.environment}
                                                </span>
                                            )}
                                            {project.framework && (
                                                <span className="text-xs font-medium text-gray-500 dark:text-gray-400">
                                                    {project.framework}
                                                </span>
                                            )}
                                            <span className="text-xs text-gray-400 dark:text-gray-500">
                                                Created {project.created_at || 'N/A'}
                                            </span>
                                        </div>

                                        {/* Open Button */}
                                        <div className="mt-4 pt-3 border-t border-gray-100 dark:border-slate-700">
                                            <span className="inline-flex items-center justify-center gap-2 w-full px-4 py-3 bg-cyan-50 dark:bg-cyan-900/20 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 rounded-lg text-sm font-medium transition-colors">
                                                Open Project
                                                <ArrowRight className="w-4 h-4" />
                                            </span>
                                        </div>
                                    </Link>
                                ))}
                            </div>

                            {/* Pagination Placeholder */}
                            <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-slate-700">
                                <p className="text-sm text-gray-500 dark:text-gray-400">
                                    Showing {filteredProjects.length} of {filteredProjects.length} projects
                                </p>
                                <div className="flex items-center gap-1">
                                    <button className="p-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800 disabled:opacity-50" disabled>
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button className="px-3 py-1.5 rounded-lg bg-cyan-600 text-white text-sm font-medium">1</button>
                                    <button className="p-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800 disabled:opacity-50" disabled>
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </>
                    ) : (
                        /* Empty State */
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-16 text-center">
                            <div className="w-16 h-16 mx-auto mb-4 bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center">
                                <FolderOpen className="w-8 h-8 text-cyan-600 dark:text-cyan-400" />
                            </div>
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {search ? 'No projects found' : 'No projects yet'}
                            </h3>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                                {search
                                    ? 'Try adjusting your search terms.'
                                    : 'Create your first project to start monitoring your Laravel applications.'}
                            </p>
                            {!search && (
                                <Link
                                    href="/organizations/create"
                                    className="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors"
                                >
                                    <Plus className="w-4 h-4" />
                                    Add Project
                                </Link>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
