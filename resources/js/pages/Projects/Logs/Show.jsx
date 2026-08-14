import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, FileText, AlertTriangle, Clock } from 'lucide-react';
import AppLayout from '@/layouts/AppLayout';
import { formatDateTime } from '@/lib/utils';

const levelColors = {
    DEBUG: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    INFO: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    NOTICE: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
    WARNING: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    ERROR: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    CRITICAL: 'bg-red-600 text-white',
    ALERT: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
    EMERGENCY: 'bg-red-700 text-white',
};

export default function LogShow({ organization, project, log, relatedRequest, relatedExceptionGroup, relatedLogs }) {
    const levelColor = levelColors[log.level] || levelColors.INFO;

    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="mb-8">
                    <Link
                        href={`/organizations/${organization.id}/projects/${project.id}/logs`}
                        className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        Back to logs
                    </Link>
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                <FileText className="w-6 h-6" />
                                Log Entry
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {log.channel} • {formatDateTime(log.logged_at)}
                            </p>
                        </div>
                        <span className={`inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium ${levelColor}`}>
                            {log.level}
                        </span>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Message */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Message</h2>
                            <div className="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg">
                                <pre className="text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap break-all">
                                    {log.message}
                                </pre>
                            </div>
                        </div>

                        {/* Context */}
                        {log.context && Object.keys(log.context).length > 0 && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Context</h2>
                                <div className="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg overflow-x-auto">
                                    <pre className="text-sm font-mono text-gray-800 dark:text-gray-200">
                                        {JSON.stringify(log.context, null, 2)}
                                    </pre>
                                </div>
                            </div>
                        )}

                        {/* Extra */}
                        {log.extra && Object.keys(log.extra).length > 0 && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Extra</h2>
                                <div className="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg overflow-x-auto">
                                    <pre className="text-sm font-mono text-gray-800 dark:text-gray-200">
                                        {JSON.stringify(log.extra, null, 2)}
                                    </pre>
                                </div>
                            </div>
                        )}

                        {/* Related Logs */}
                        {relatedLogs && relatedLogs.length > 0 && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Logs</h2>
                                <div className="space-y-3">
                                    {relatedLogs.map((rl) => (
                                        <Link
                                            key={rl.uuid}
                                            href={`/organizations/${organization.id}/projects/${project.id}/logs/${rl.uuid}`}
                                            className="block p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                        >
                                            <div className="flex items-center justify-between mb-1">
                                                <span className={`inline-flex px-2 py-0.5 rounded text-xs font-medium ${levelColors[rl.level] || levelColors.INFO}`}>
                                                    {rl.level}
                                                </span>
                                                <span className="text-xs text-gray-500 dark:text-gray-400">
                                                    {formatDateTime(rl.logged_at)}
                                                </span>
                                            </div>
                                            <p className="text-sm text-gray-700 dark:text-gray-300 line-clamp-1">
                                                {rl.message}
                                            </p>
                                        </Link>
                                    ))}
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
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Channel</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {log.channel || 'N/A'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Environment</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                        {log.environment || 'N/A'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Logged At</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {formatDateTime(log.logged_at)}
                                    </dd>
                                </div>
                            </dl>
                        </div>

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

                        {/* Related Exception */}
                        {relatedExceptionGroup && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 p-6">
                                <div className="flex items-center gap-2 mb-4">
                                    <AlertTriangle className="w-5 h-5 text-red-600 dark:text-red-400" />
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Related Exception</h2>
                                </div>
                                <Link
                                    href={`/organizations/${organization.id}/projects/${project.id}/exceptions/${relatedExceptionGroup.uuid}`}
                                    className="block p-3 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                                >
                                    <p className="text-sm font-medium text-red-800 dark:text-red-200">
                                        {relatedExceptionGroup.exception_class}
                                    </p>
                                    <p className="text-xs text-red-600 dark:text-red-400 mt-1 line-clamp-2">
                                        {relatedExceptionGroup.exception_message}
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
