import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import {
    FileText,
    ChevronRight,
    Clock,
    Search,
    RefreshCw,
    AlertTriangle,
    Info,
} from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function LogsIndex() {
    const { organization, project, logs } = usePage().props;

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
                    <span className="text-gray-900 dark:text-white font-medium">Logs</span>
                </div>

                {/* Header */}
                <div className="flex items-center justify-between mb-6">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Application Logs</h1>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            View and search application log entries
                        </p>
                    </div>
                    <button className="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <RefreshCw className="w-4 h-4" />
                        Refresh
                    </button>
                </div>

                {/* Logs */}
                <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    {logs && logs.length > 0 ? (
                        <div className="divide-y divide-gray-200 dark:divide-gray-700">
                            {logs.map((log) => (
                                <div key={log.id} className="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div className="flex items-start gap-3">
                                        <div className={`mt-0.5 p-1.5 rounded-lg ${
                                            log.level === 'error' || log.level === 'critical'
                                                ? 'bg-red-100 dark:bg-red-900/30'
                                                : log.level === 'warning'
                                                ? 'bg-yellow-100 dark:bg-yellow-900/30'
                                                : 'bg-blue-100 dark:bg-blue-900/30'
                                        }`}>
                                            {log.level === 'error' || log.level === 'critical' ? (
                                                <AlertTriangle className="w-4 h-4 text-red-600 dark:text-red-400" />
                                            ) : log.level === 'warning' ? (
                                                <AlertTriangle className="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                            ) : (
                                                <Info className="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                            )}
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center gap-2 mb-1">
                                                <span className={`px-2 py-0.5 rounded text-xs font-medium uppercase ${
                                                    log.level === 'error' || log.level === 'critical'
                                                        ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                                        : log.level === 'warning'
                                                        ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
                                                        : log.level === 'info'
                                                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                                        : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                                }`}>
                                                    {log.level}
                                                </span>
                                                <span className="text-xs text-gray-500 dark:text-gray-400">
                                                    {new Date(log.created_at).toLocaleString()}
                                                </span>
                                            </div>
                                            <p className="text-sm text-gray-900 dark:text-white mb-1">
                                                {log.message}
                                            </p>
                                            {log.context && Object.keys(log.context).length > 0 && (
                                                <pre className="mt-2 p-2 bg-gray-100 dark:bg-gray-900 rounded text-xs font-mono overflow-x-auto">
                                                    {JSON.stringify(log.context, null, 2)}
                                                </pre>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="p-12 text-center">
                            <FileText className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                No logs found
                            </h3>
                            <p className="text-gray-500 dark:text-gray-400">
                                Application logs will appear here once your agent starts sending data.
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
