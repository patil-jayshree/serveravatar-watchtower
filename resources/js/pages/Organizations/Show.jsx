import { Link, usePage, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Building2, Plus, ArrowRight, Wifi, WifiOff, Server } from 'lucide-react';

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

export default function OrganizationShow() {
    const { organization, projects, projects_pagination } = usePage().props;

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-7xl mx-auto px-8 py-8">
                    {/* Back Link */}
                    <div className="mb-4">
                        <Link
                            href="/organizations"
                            className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Organizations
                        </Link>
                    </div>

                    {/* Page Header */}
                    <div className="flex items-center justify-between mb-6">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                {organization?.name || 'Organization'}
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {organization?.created_at ? `Created on ${organization.created_at}` : 'Organization overview'}
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

                    {/* Getting Started Section */}
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

                    {/* Projects Section */}
                    {projects && projects.length > 0 ? (
                        <>
                            {/* Projects Grid */}
                            <div className="grid grid-cols-2 gap-6 mb-6">
                                {projects.map((project) => (
                                    <Link
                                        key={project.id}
                                        href={`/organizations/${organization.id}/projects/${project.id}`}
                                        className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 hover:border-cyan-300 dark:hover:border-cyan-600 transition-all group"
                                    >
                                        {/* Top Row: Avatar + Name + Status */}
                                        <div className="flex items-start justify-between mb-5">
                                            <div className="flex items-center gap-3">
                                                <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-cyan-700 flex items-center justify-center text-white font-semibold text-base flex-shrink-0">
                                                    {project.name.charAt(0).toUpperCase()}
                                                </div>
                                                <div>
                                                    <h3 className="font-semibold text-gray-900 dark:text-white text-base group-hover:text-cyan-600 dark:group-hover:text-cyan-400">
                                                        {project.name}
                                                    </h3>
                                                    <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                        Created {project.created_at || 'N/A'}
                                                    </p>
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

                            {/* Pagination */}
                            {projects_pagination && projects_pagination.last_page > 1 && (
                                <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-slate-700">
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        Showing {projects.length} of {projects_pagination.total} projects
                                    </p>
                                    <div className="flex items-center gap-1">
                                        {projects_pagination.current_page > 1 && (
                                            <button
                                                onClick={() => router.get(`/organizations/${organization.id}?page=${projects_pagination.current_page - 1}&per_page=${projects_pagination.per_page}`)}
                                                className="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                                            >
                                                Previous
                                            </button>
                                        )}
                                        {Array.from({ length: projects_pagination.last_page }, (_, i) => i + 1).map((page) => (
                                            <button
                                                key={page}
                                                onClick={() => router.get(`/organizations/${organization.id}?page=${page}&per_page=${projects_pagination.per_page}`)}
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
                                                onClick={() => router.get(`/organizations/${organization.id}?page=${projects_pagination.current_page + 1}&per_page=${projects_pagination.per_page}`)}
                                                className="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                                            >
                                                Next
                                            </button>
                                        )}
                                    </div>
                                </div>
                            )}
                            {(!projects_pagination || projects_pagination.last_page <= 1) && (
                                <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-slate-700">
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        Showing {projects.length} of {projects_pagination?.total || projects.length} projects
                                    </p>
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
