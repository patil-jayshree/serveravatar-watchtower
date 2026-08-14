import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Globe, Clock, CheckCircle, XCircle } from 'lucide-react';
import AppLayout from '@/layouts/AppLayout';
import { formatDuration, formatDateTime } from '@/lib/utils';

const methodColors = {
    GET: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    POST: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    PUT: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    PATCH: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
    DELETE: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
};

const statusColors = (code) => {
    if (code >= 200 && code < 300) return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
    if (code >= 300 && code < 400) return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
    if (code >= 400 && code < 500) return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
    if (code >= 500) return 'bg-red-600 text-white';
    return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400';
};

export default function RequestShow({ organization, project, event }) {
    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="mb-8">
                    <Link
                        href={`/organizations/${organization.id}/projects/${project.id}/requests`}
                        className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        Back to requests
                    </Link>
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                <Globe className="w-6 h-6" />
                                {event.method} {event.uri}
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {event.uuid}
                            </p>
                        </div>
                        <div className="flex items-center gap-3">
                            <span className={`inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium ${methodColors[event.method] || methodColors.GET}`}>
                                {event.method}
                            </span>
                            <span className={`inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-medium ${statusColors(event.status_code)}`}>
                                {event.status_code >= 400 ? (
                                    <XCircle className="w-4 h-4" />
                                ) : (
                                    <CheckCircle className="w-4 h-4" />
                                )}
                                {event.status_code}
                            </span>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Request Headers */}
                        {event.request_headers && Object.keys(event.request_headers).length > 0 && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request Headers</h2>
                                <div className="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg overflow-x-auto">
                                    <pre className="text-sm font-mono text-gray-800 dark:text-gray-200">
                                        {JSON.stringify(event.request_headers, null, 2)}
                                    </pre>
                                </div>
                            </div>
                        )}

                        {/* Response Headers */}
                        {event.response_headers && Object.keys(event.response_headers).length > 0 && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Response Headers</h2>
                                <div className="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg overflow-x-auto">
                                    <pre className="text-sm font-mono text-gray-800 dark:text-gray-200">
                                        {JSON.stringify(event.response_headers, null, 2)}
                                    </pre>
                                </div>
                            </div>
                        )}

                        {/* Request Body */}
                        {event.request_body && event.request_body !== '{}' && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request Body</h2>
                                <div className="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg overflow-x-auto">
                                    <pre className="text-sm font-mono text-gray-800 dark:text-gray-200">
                                        {JSON.stringify(JSON.parse(event.request_body), null, 2)}
                                    </pre>
                                </div>
                            </div>
                        )}

                        {/* Response Body */}
                        {event.response_body && event.response_body !== '{}' && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Response Body</h2>
                                <div className="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg overflow-x-auto max-h-96">
                                    <pre className="text-sm font-mono text-gray-800 dark:text-gray-200">
                                        {JSON.stringify(JSON.parse(event.response_body), null, 2)}
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
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Duration</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {formatDuration(event.duration_ms)}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Memory</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {event.memory_usage_mb ? `${event.memory_usage_mb} MB` : 'N/A'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Environment</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                        {event.environment || 'N/A'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Executed At</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {formatDateTime(event.created_at)}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">IP Address</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {event.ip_address || 'N/A'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">User Agent</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[150px]">
                                        {event.user_agent || 'N/A'}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
