import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import {
    FolderKanban,
    Plus,
    Settings,
    Activity,
    AlertTriangle,
    Clock,
    ChevronRight,
    Server,
} from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function ProjectsIndex() {
    const { organization, projects } = usePage().props;

    return (
        <AppLayout>
            <div className="p-6">
                {/* Breadcrumb */}
                <div className="flex items-center gap-2 text-sm mb-6">
                    <Link href="/organizations" className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Organizations
                    </Link>
                    <ChevronRight className="w-4 h-4 text-gray-400" />
                    <Link href={`/organizations/${organization?.id}`} className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {organization?.name}
                    </Link>
                    <ChevronRight className="w-4 h-4 text-gray-400" />
                    <span className="text-gray-900 dark:text-white font-medium">Projects</span>
                </div>

                {/* Header */}
                <div className="flex items-center justify-between mb-8">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                            Projects
                        </h1>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Manage your monitoring projects
                        </p>
                    </div>
                    <Link
                        href={`/organizations/${organization?.id}/projects/create`}
                        className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors"
                    >
                        <Plus className="w-4 h-4" />
                        New Project
                    </Link>
                </div>

                {/* Projects Grid */}
                {projects && projects.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {projects.map((project) => (
                            <Link
                                key={project.id}
                                href={`/organizations/${organization?.id}/projects/${project.id}`}
                                className="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-primary-500 dark:hover:border-primary-500 transition-all"
                            >
                                <div className="flex items-start justify-between mb-4">
                                    <div className="flex items-center gap-3">
                                        <div className="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                            <FolderKanban className="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                        </div>
                                        <div>
                                            <h3 className="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                                {project.name}
                                            </h3>
                                            <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {project.environments_count || 1} environment{(project.environments_count || 1) !== 1 ? 's' : ''}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRight className="w-5 h-5 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
                                </div>

                                {/* Quick Stats */}
                                <div className="grid grid-cols-3 gap-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div className="text-center">
                                        <p className="text-lg font-bold text-gray-900 dark:text-white">{project.stats?.requests_24h || 0}</p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">Requests</p>
                                    </div>
                                    <div className="text-center">
                                        <p className="text-lg font-bold text-red-600 dark:text-red-400">{project.stats?.errors_24h || 0}</p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">Errors</p>
                                    </div>
                                    <div className="text-center">
                                        <p className="text-lg font-bold text-gray-900 dark:text-white">{project.stats?.avg_response || '0ms'}</p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">Avg Time</p>
                                    </div>
                                </div>

                                {/* Agent Status */}
                                <div className="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-2">
                                            <div className={`w-2 h-2 rounded-full ${project.is_agent_connected ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'}`}></div>
                                            <span className="text-xs text-gray-500 dark:text-gray-400">
                                                {project.is_agent_connected ? 'Agent Connected' : 'Agent Disconnected'}
                                            </span>
                                        </div>
                                        <Link
                                            href={`/organizations/${organization?.id}/projects/${project.id}/agent`}
                                            className="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400"
                                            onClick={(e) => e.stopPropagation()}
                                        >
                                            View Agent
                                        </Link>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>
                ) : (
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                        <FolderKanban className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                        <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            No projects yet
                        </h3>
                        <p className="text-gray-500 dark:text-gray-400 mb-6">
                            Create your first project to start monitoring.
                        </p>
                        <Link
                            href={`/organizations/${organization?.id}/projects/create`}
                            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors"
                        >
                            <Plus className="w-4 h-4" />
                            Create Project
                        </Link>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
