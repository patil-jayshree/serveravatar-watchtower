import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Clock, CheckCircle, XCircle, AlertTriangle, Terminal } from 'lucide-react';
import AppLayout from '@/layouts/AppLayout';
import { formatDuration, formatDateTime, timeAgo } from '@/lib/utils';

const statusColors = {
    completed: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    failed: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    running: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    missed: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
};

const getStatusIcon = (status) => {
    switch (status) {
        case 'completed':
            return <CheckCircle className="w-4 h-4" />;
        case 'failed':
            return <XCircle className="w-4 h-4" />;
        case 'running':
            return <Clock className="w-4 h-4" />;
        case 'missed':
            return <AlertTriangle className="w-4 h-4" />;
        default:
            return <Clock className="w-4 h-4" />;
    }
};

export default function SchedulerShow({ organization, project, task, executions, executionStats, commandEvent, jobEvent, exceptionOccurrence, filters }) {
    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="mb-8">
                    <Link
                        href={`/organizations/${organization.id}/projects/${project.id}/scheduler`}
                        className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        Back to scheduler
                    </Link>
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <Clock className="w-6 h-6" />
                            {task.task_name}
                        </h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {task.description || 'Scheduled task'}
                        </p>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Execution History */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Execution History</h2>
                            {executions && executions.length > 0 ? (
                                <div className="space-y-3">
                                    {executions.map((execution) => (
                                        <div
                                            key={execution.id}
                                            className="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg"
                                        >
                                            <div className="flex items-center gap-3">
                                                <span className={`inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium ${statusColors[execution.status] || statusColors.completed}`}>
                                                    {getStatusIcon(execution.status)}
                                                    {execution.status}
                                                </span>
                                                <span className="text-sm text-gray-700 dark:text-gray-300">
                                                    {formatDateTime(execution.created_at)}
                                                </span>
                                            </div>
                                            <div className="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span>Duration: {formatDuration(execution.duration_ms)}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-center text-gray-500 dark:text-gray-400 py-4">
                                    No executions found for this task.
                                </p>
                            )}
                        </div>

                        {/* Related Command */}
                        {commandEvent && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <Terminal className="w-5 h-5" />
                                    Related Command
                                </h2>
                                <Link
                                    href={`/organizations/${organization.id}/projects/${project.id}/commands/${commandEvent.uuid}`}
                                    className="block p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <p className="text-sm font-mono text-gray-700 dark:text-gray-300">
                                        {commandEvent.command_name}
                                    </p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Exit code: {commandEvent.exit_code} • {timeAgo(commandEvent.created_at)}
                                    </p>
                                </Link>
                            </div>
                        )}
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Task Details */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Task Details</h2>
                            <dl className="space-y-3">
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Command</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {task.command_name || 'N/A'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Schedule</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {task.schedule_expression || 'N/A'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Environment</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                        {task.environment || 'all'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Without Overlapping</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {task.without_overlapping ? 'Yes' : 'No'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Last Run</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {task.last_run_at ? timeAgo(task.last_run_at) : 'Never'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Next Run</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {task.next_run_at ? formatDateTime(task.next_run_at) : 'Not scheduled'}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {/* Execution Stats */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistics</h2>
                            <dl className="space-y-3">
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Total Executions</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {executionStats.total || 0}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Completed</dt>
                                    <dd className="text-sm font-medium text-green-600 dark:text-green-400">
                                        {executionStats.completed || 0}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Failed</dt>
                                    <dd className="text-sm font-medium text-red-600 dark:text-red-400">
                                        {executionStats.failed || 0}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Missed</dt>
                                    <dd className="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                        {executionStats.missed || 0}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Avg Duration</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {formatDuration(executionStats.avg_duration)}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {/* Exception */}
                        {exceptionOccurrence && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 p-6">
                                <div className="flex items-center gap-2 mb-4">
                                    <AlertTriangle className="w-5 h-5 text-red-600 dark:text-red-400" />
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Latest Exception</h2>
                                </div>
                                <Link
                                    href={`/organizations/${organization.id}/projects/${project.id}/exceptions/${exceptionOccurrence.uuid}`}
                                    className="block p-3 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                                >
                                    <p className="text-sm font-medium text-red-800 dark:text-red-200">
                                        {exceptionOccurrence.exception_class}
                                    </p>
                                    <p className="text-xs text-red-600 dark:text-red-400 mt-1 line-clamp-2">
                                        {exceptionOccurrence.exception_message}
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
