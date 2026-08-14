import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Server, AlertTriangle, CheckCircle, XCircle, Clock } from 'lucide-react';
import AppLayout from '@/layouts/AppLayout';
import { formatDuration, formatDateTime } from '@/lib/utils';

export default function JobShow({ organization, project, job, relatedRequest, relatedExceptionGroup }) {
    const getStatusBadge = (status) => {
        switch (status) {
            case 'completed':
                return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            case 'failed':
                return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
            case 'running':
                return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
            case 'queued':
                return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
            default:
                return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400';
        }
    };

    const getStatusIcon = (status) => {
        switch (status) {
            case 'completed':
                return <CheckCircle className="w-4 h-4" />;
            case 'failed':
                return <XCircle className="w-4 h-4" />;
            case 'running':
                return <Clock className="w-4 h-4" />;
            default:
                return <Server className="w-4 h-4" />;
        }
    };

    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="mb-8">
                    <Link
                        href={`/organizations/${organization.id}/projects/${project.id}/jobs`}
                        className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        Back to jobs
                    </Link>
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                <Server className="w-6 h-6" />
                                {job.job_name || job.job_uuid}
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {job.job_uuid}
                            </p>
                        </div>
                        <div className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium ${getStatusBadge(job.status)}`}>
                            {getStatusIcon(job.status)}
                            {job.status}
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Job Payload */}
                        {job.payload && Object.keys(job.payload).length > 0 && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Job Payload</h2>
                                <div className="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg overflow-x-auto">
                                    <pre className="text-sm font-mono text-gray-800 dark:text-gray-200">
                                        {JSON.stringify(job.payload, null, 2)}
                                    </pre>
                                </div>
                            </div>
                        )}

                        {/* Exception Details */}
                        {relatedExceptionGroup && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 p-6">
                                <div className="flex items-center gap-2 mb-4">
                                    <AlertTriangle className="w-5 h-5 text-red-600 dark:text-red-400" />
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Exception Details</h2>
                                </div>
                                <Link
                                    href={`/organizations/${organization.id}/projects/${project.id}/exceptions/${relatedExceptionGroup.uuid}`}
                                    className="block p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                                >
                                    <p className="font-medium text-red-800 dark:text-red-200">
                                        {relatedExceptionGroup.exception_class}
                                    </p>
                                    <p className="text-sm text-red-600 dark:text-red-400 mt-1 line-clamp-2">
                                        {relatedExceptionGroup.exception_message}
                                    </p>
                                    <p className="text-xs text-red-500 dark:text-red-500 mt-2">
                                        {relatedExceptionGroup.occurrences_count} occurrences
                                    </p>
                                </Link>
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
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Queue</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {job.queue || 'default'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Attempts</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {job.attempts || 1}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Duration</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {formatDuration(job.duration_ms)}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Started At</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {formatDateTime(job.started_at)}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Completed At</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {job.completed_at ? formatDateTime(job.completed_at) : 'N/A'}
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
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
