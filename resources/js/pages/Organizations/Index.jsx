import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import {
    Building2,
    Plus,
    Settings,
    Users,
    FolderKanban,
    ChevronRight,
    MoreHorizontal,
} from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function OrganizationsIndex() {
    const { organizations } = usePage().props;

    return (
        <AppLayout>
            <div className="p-6">
                {/* Header */}
                <div className="flex items-center justify-between mb-8">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                            Organizations
                        </h1>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Manage your organizations and projects
                        </p>
                    </div>
                    <Link
                        href="/organizations/create"
                        className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors"
                    >
                        <Plus className="w-4 h-4" />
                        New Organization
                    </Link>
                </div>

                {/* Organizations Grid */}
                {organizations && organizations.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {organizations.map((org) => (
                            <Link
                                key={org.id}
                                href={`/organizations/${org.id}`}
                                className="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-primary-500 dark:hover:border-primary-500 transition-all"
                            >
                                <div className="flex items-start justify-between">
                                    <div className="flex items-center gap-4">
                                        {org.logo_url ? (
                                            <img
                                                src={org.logo_url}
                                                alt={org.name}
                                                className="w-12 h-12 rounded-lg object-cover"
                                            />
                                        ) : (
                                            <div className="w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                                <Building2 className="w-6 h-6 text-primary-600 dark:text-primary-400" />
                                            </div>
                                        )}
                                        <div>
                                            <h3 className="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                                {org.name}
                                            </h3>
                                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                {org.projects_count || 0} projects
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRight className="w-5 h-5 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
                                </div>

                                <div className="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center gap-4">
                                    <Link
                                        href={`/organizations/${org.id}/settings`}
                                        className="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                        onClick={(e) => e.stopPropagation()}
                                    >
                                        <Settings className="w-4 h-4" />
                                        Settings
                                    </Link>
                                </div>
                            </Link>
                        ))}
                    </div>
                ) : (
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                        <Building2 className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                        <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            No organizations yet
                        </h3>
                        <p className="text-gray-500 dark:text-gray-400 mb-6">
                            Create your first organization to start monitoring your projects.
                        </p>
                        <Link
                            href="/organizations/create"
                            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors"
                        >
                            <Plus className="w-4 h-4" />
                            Create Organization
                        </Link>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
