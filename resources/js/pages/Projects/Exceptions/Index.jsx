import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import {
    Bug,
    ChevronRight,
    AlertTriangle,
    Clock,
    Filter,
    Search,
    RefreshCw,
    CheckCircle2,
    XCircle,
} from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function ExceptionsIndex() {
    const { organization, project, exceptions, filters } = usePage().props;

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
                    <Link href={`/organizations/${organization?.id}/projects/${project?.id}`} className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {project?.name}
                    </Link>
                    <ChevronRight className="w-4 h-4 text-gray-400" />
                    <span className="text-gray-900 dark:text-white font-medium">Exceptions</span>
                </div>

                {/* Header */}
                <div className="flex items-center justify-between mb-6">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Exceptions</h1>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Track and manage application errors
                        </p>
                    </div>
                    <button className="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <RefreshCw className="w-4 h-4" />
                        Refresh
                    </button>
                </div>

                {/* Filters */}
                <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
                    <div className="flex flex-wrap items-center gap-4">
                        <div className="flex-1 min-w-[200px]">
                            <div className="relative">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    type="text"
                                    placeholder="Search exceptions..."
                                    className="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500"
                                />
                            </div>
                        </div>
                        <select className="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                            <option value="">All Status</option>
                            <option value="resolved">Resolved</option>
                            <option value="unresolved">Unresolved</option>
                            <option value="ignored">Ignored</option>
                        </select>
                        <select className="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                            <option value="">All Types</option>
                            <option value="24h">Last 24 hours</option>
                            <option value="7d">Last 7 days</option>
                            <option value="30d">Last 30 days</option>
                        </select>
                    </div>
                </div>

                {/* Exceptions List */}
                <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    {exceptions && exceptions.length > 0 ? (
                        <div className="divide-y divide-gray-200 dark:divide-gray-700">
                            {exceptions.map((exception) => (
                                <Link
                                    key={exception.id}
                                    href={`/organizations/${organization?.id}/projects/${project?.id}/exceptions/${exception.uuid}`}
                                    className="flex items-start gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                >
                                    <div className={`w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 ${
                                        exception.status === 'resolved' 
                                            ? 'bg-green-100 dark:bg-green-900/30' 
                                            : 'bg-red-100 dark:bg-red-900/30'
                                    }`}>
                                        {exception.status === 'resolved' ? (
                                            <CheckCircle2 className="w-5 h-5 text-green-600 dark:text-green-400" />
                                        ) : (
                                            <AlertTriangle className="w-5 h-5 text-red-600 dark:text-red-400" />
                                        )}
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2 mb-1">
                                            <h4 className="font-medium text-gray-900 dark:text-white truncate">
                                                {exception.exception_class}
                                            </h4>
                                            <span className={`px-2 py-0.5 rounded text-xs font-medium ${
                                                exception.status === 'resolved'
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                    : exception.status === 'ignored'
                                                    ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                                    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                            }`}>
                                                {exception.status}
                                            </span>
                                        </div>
                                        <p className="text-sm text-gray-500 dark:text-gray-400 truncate mb-2">
                                            {exception.message}
                                        </p>
                                        <div className="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
                                            <span className="flex items-center gap-1">
                                                <Clock className="w-3 h-3" />
                                                {new Date(exception.last_occurrence_at).toLocaleString()}
                                            </span>
                                            <span>
                                                {exception.occurrence_count} occurrence{exception.occurrence_count !== 1 ? 's' : ''}
                                            </span>
                                        </div>
                                    </div>
                                    <ChevronRight className="w-5 h-5 text-gray-400 flex-shrink-0" />
                                </Link>
                            ))}
                        </div>
                    ) : (
                        <div className="p-12 text-center">
                            <Bug className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                No exceptions found
                            </h3>
                            <p className="text-gray-500 dark:text-gray-400">
                                Your application has no recorded exceptions.
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
