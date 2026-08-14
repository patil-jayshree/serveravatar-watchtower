import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import {
    Bug,
    ChevronRight,
    AlertTriangle,
    Clock,
    CheckCircle2,
    ArrowLeft,
    Code,
    FileText,
    RefreshCw,
} from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function ExceptionShow() {
    const { organization, project, exception, occurrences } = usePage().props;

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
                    <Link href={`/organizations/${organization?.id}/projects/${project?.id}/exceptions`} className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Exceptions
                    </Link>
                    <ChevronRight className="w-4 h-4 text-gray-400" />
                    <span className="text-gray-900 dark:text-white font-medium">Details</span>
                </div>

                {/* Header */}
                <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center gap-4">
                        <Link
                            href={`/organizations/${organization?.id}/projects/${project?.id}/exceptions`}
                            className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        >
                            <ArrowLeft className="w-5 h-5 text-gray-500" />
                        </Link>
                        <div>
                            <div className="flex items-center gap-3">
                                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                    {exception?.exception_class || 'Exception'}
                                </h1>
                                <span className={`px-2 py-0.5 rounded text-xs font-medium ${
                                    exception?.status === 'resolved'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                }`}>
                                    {exception?.status || 'unresolved'}
                                </span>
                            </div>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {exception?.message || 'No message'}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center gap-3">
                        <button className="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <RefreshCw className="w-4 h-4" />
                            Refresh
                        </button>
                        {exception?.status !== 'resolved' && (
                            <button className="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                                <CheckCircle2 className="w-4 h-4" />
                                Mark Resolved
                            </button>
                        )}
                    </div>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">Occurrences</p>
                        <p className="text-2xl font-bold text-gray-900 dark:text-white">
                            {exception?.occurrence_count || 0}
                        </p>
                    </div>
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">First Seen</p>
                        <p className="text-lg font-medium text-gray-900 dark:text-white">
                            {exception?.first_occurrence_at ? new Date(exception.first_occurrence_at).toLocaleDateString() : 'N/A'}
                        </p>
                    </div>
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">Last Seen</p>
                        <p className="text-lg font-medium text-gray-900 dark:text-white">
                            {exception?.last_occurrence_at ? new Date(exception.last_occurrence_at).toLocaleString() : 'N/A'}
                        </p>
                    </div>
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">Status</p>
                        <p className="text-lg font-medium text-gray-900 dark:text-white capitalize">
                            {exception?.status || 'unresolved'}
                        </p>
                    </div>
                </div>

                {/* Stack Trace */}
                {exception?.stack_trace && (
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <div className="flex items-center gap-2 mb-4">
                            <Code className="w-5 h-5 text-gray-500" />
                            <h2 className="font-semibold text-gray-900 dark:text-white">Stack Trace</h2>
                        </div>
                        <pre className="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm font-mono">
                            {exception.stack_trace}
                        </pre>
                    </div>
                )}

                {/* Recent Occurrences */}
                <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 className="font-semibold text-gray-900 dark:text-white">Recent Occurrences</h2>
                    </div>
                    <div className="divide-y divide-gray-200 dark:divide-gray-700">
                        {occurrences && occurrences.length > 0 ? (
                            occurrences.map((occurrence) => (
                                <div key={occurrence.id} className="p-4">
                                    <div className="flex items-center justify-between mb-2">
                                        <div className="flex items-center gap-2">
                                            <Clock className="w-4 h-4 text-gray-400" />
                                            <span className="text-sm text-gray-600 dark:text-gray-300">
                                                {new Date(occurrence.created_at).toLocaleString()}
                                            </span>
                                        </div>
                                    </div>
                                    {occurrence.context && (
                                        <pre className="bg-gray-50 dark:bg-gray-900 p-3 rounded text-xs font-mono overflow-x-auto">
                                            {JSON.stringify(occurrence.context, null, 2)}
                                        </pre>
                                    )}
                                </div>
                            ))
                        ) : (
                            <div className="p-8 text-center text-gray-500 dark:text-gray-400">
                                No recent occurrences
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
