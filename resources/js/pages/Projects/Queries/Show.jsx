import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Database, Clock, AlertTriangle } from 'lucide-react';
import AppLayout from '@/layouts/AppLayout';
import { formatDuration, formatDateTime } from '@/lib/utils';

export default function QueryShow({ organization, project, query, relatedRequest }) {
    const getSlowBadge = (duration, threshold = 100) => {
        if (duration > threshold) {
            return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
        }
        return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
    };

    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="mb-8">
                    <Link
                        href={`/organizations/${organization.id}/projects/${project.id}/queries`}
                        className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        Back to queries
                    </Link>
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                <Database className="w-6 h-6" />
                                Query Details
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {query.connection} • {formatDateTime(query.created_at)}
                            </p>
                        </div>
                        <span className={`inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium ${getSlowBadge(query.duration_ms)}`}>
                            {formatDuration(query.duration_ms)}
                        </span>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* SQL Query */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">SQL Query</h2>
                            <div className="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto">
                                <pre className="text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap">
                                    {query.sql}
                                </pre>
                            </div>
                        </div>

                        {/* Bindings */}
                        {query.bindings && query.bindings.length > 0 && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bindings</h2>
                                <div className="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg">
                                    <pre className="text-sm font-mono text-gray-800 dark:text-gray-200">
                                        {JSON.stringify(query.bindings, null, 2)}
                                    </pre>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Metadata */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Metadata</h2>
                            <dl className="space-y-3">
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Connection</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {query.connection || 'default'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Duration</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {formatDuration(query.duration_ms)}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Environment</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                        {query.environment || 'N/A'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Executed At</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {formatDateTime(query.created_at)}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {/* Slow Query Warning */}
                        {query.duration_ms > 100 && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-yellow-200 dark:border-yellow-800 p-6">
                                <div className="flex items-center gap-2 mb-2">
                                    <AlertTriangle className="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Slow Query</h2>
                                </div>
                                <p className="text-sm text-gray-600 dark:text-gray-400">
                                    This query took longer than 100ms to execute. Consider optimizing or adding indexes.
                                </p>
                            </div>
                        )}

                        {/* Related Request */}
                        {relatedRequest && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Request</h2>
                                <Link
                                    href={`/organizations/${organization.id}/projects/${project.id}/requests/${relatedRequest.uuid}`}
                                    className="block p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <p className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {relatedRequest.method} {relatedRequest.uri}
                                    </p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {relatedRequest.status_code}
                                    </p>
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
