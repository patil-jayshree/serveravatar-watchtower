import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Clock, Terminal, AlertTriangle, CheckCircle, XCircle } from 'lucide-react';
import AppLayout from '@/layouts/AppLayout';
import { formatDuration, formatDateTime } from '@/lib/utils';

export default function CommandShow({ organization, project, command, exceptionGroup, exceptionOccurrence, relatedCommands }) {
    const getExitCodeBadge = (exitCode) => {
        if (exitCode === 0) {
            return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
        }
        return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
    };

    const getExitCodeIcon = (exitCode) => {
        if (exitCode === 0) {
            return <CheckCircle className="w-4 h-4" />;
        }
        return <XCircle className="w-4 h-4" />;
    };

    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="mb-8">
                    <Link
                        href={`/organizations/${organization.id}/projects/${project.id}/commands`}
                        className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        Back to commands
                    </Link>
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                <Terminal className="w-6 h-6" />
                                {command.command_name}
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Executed on {formatDateTime(command.created_at)}
                            </p>
                        </div>
                        <div className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium ${getExitCodeBadge(command.exit_code)}`}>
                            {getExitCodeIcon(command.exit_code)}
                            Exit Code: {command.exit_code}
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Command Details */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Command Details</h2>
                            <div className="space-y-4">
                                <div>
                                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Full Command</p>
                                    <code className="block bg-gray-100 dark:bg-gray-900 p-3 rounded-lg text-sm font-mono text-gray-800 dark:text-gray-200 overflow-x-auto">
                                        {command.command}
                                    </code>
                                </div>
                                {command.options && Object.keys(command.options).length > 0 && (
                                    <div>
                                        <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Options</p>
                                        <div className="bg-gray-100 dark:bg-gray-900 p-3 rounded-lg">
                                            <pre className="text-sm font-mono text-gray-800 dark:text-gray-200">
                                                {JSON.stringify(command.options, null, 2)}
                                            </pre>
                                        </div>
                                    </div>
                                )}
                                {command.exit_code !== 0 && command.output && (
                                    <div>
                                        <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Output</p>
                                        <div className="bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                                            <pre className="text-sm font-mono text-red-800 dark:text-red-200 whitespace-pre-wrap">{command.output}</pre>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Related Commands */}
                        {relatedCommands && relatedCommands.length > 0 && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Commands</h2>
                                <div className="space-y-3">
                                    {relatedCommands.map((cmd) => (
                                        <Link
                                            key={cmd.uuid}
                                            href={`/organizations/${organization.id}/projects/${project.id}/commands/${cmd.uuid}`}
                                            className="block p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                        >
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm font-mono text-gray-700 dark:text-gray-300 truncate">
                                                    {cmd.command_name}
                                                </span>
                                                <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium ${cmd.exit_code === 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'}`}>
                                                    {cmd.exit_code}
                                                </span>
                                            </div>
                                            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {formatDateTime(cmd.created_at)}
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
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Duration</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {formatDuration(command.duration_ms)}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Environment</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                        {command.environment || 'N/A'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Executed By</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white">
                                        {command.user || 'System'}
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt className="text-sm text-gray-500 dark:text-gray-400">Triggered</dt>
                                    <dd className="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                        {command.triggered_by || 'Schedule'}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {/* Related Exception */}
                        {exceptionOccurrence && (
                            <div className="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 p-6">
                                <div className="flex items-center gap-2 mb-4">
                                    <AlertTriangle className="w-5 h-5 text-red-600 dark:text-red-400" />
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Related Exception</h2>
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
