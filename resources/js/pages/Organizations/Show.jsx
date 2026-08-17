import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Building2, FolderOpen, Plus } from 'lucide-react';

export default function OrganizationShow() {
    const { organization, projects } = usePage().props;

    return (
        <AppLayout>
            <div className="p-8">
                <div className="flex items-center justify-between mb-8">
                    <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">{organization?.name || 'Organization'}</h1>
                    <Link
                        href={`/organizations/${organization.id}/projects/create`}
                        className="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors"
                    >
                        <Plus className="w-4 h-4" />
                        New Project
                    </Link>
                </div>
                {projects && projects.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {projects.map((project) => (
                            <Link
                                key={project.id}
                                href={`/organizations/${organization.id}/projects/${project.id}`}
                                className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                        <FolderOpen className="w-5 h-5 text-cyan-600" />
                                    </div>
                                    <div>
                                        <h3 className="font-semibold text-gray-900 dark:text-white">{project.name}</h3>
                                        <p className="text-sm text-gray-500">{project.projects_count || 0} projects</p>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>
                ) : (
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-12 text-center">
                        <Building2 className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                        <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No projects yet</h3>
                        <p className="text-gray-500 mb-6">Create your first project to start monitoring</p>
                        <Link
                            href={`/organizations/${organization.id}/projects/create`}
                            className="inline-flex items-center gap-2 px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium"
                        >
                            <Plus className="w-5 h-5" />
                            Create Project
                        </Link>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
