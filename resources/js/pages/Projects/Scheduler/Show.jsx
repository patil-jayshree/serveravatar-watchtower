import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Clock, Calendar, Terminal, Activity, ChevronRight, FileText } from 'lucide-react';
import { useState } from 'react';

const STATUS_COLORS = {
    healthy: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    completed: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    running: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
    failed: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
    missed: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400',
};

function getStatusColor(status) {
    return STATUS_COLORS[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
}

function formatDuration(ms) {
    if (!ms && ms !== 0) return '-';
    if (ms < 1000) return `${Math.round(ms)}ms`;
    return `${(ms / 1000).toFixed(1)}s`;
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    });
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function formatTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
}

function DetailCard({ icon: Icon, title, children }) {
    return (
        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div className="px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50 flex items-center gap-3">
                {Icon && <Icon className="w-5 h-5 text-gray-400" />}
                <h3 className="font-semibold text-gray-900 dark:text-white">{title}</h3>
            </div>
            <div className="p-5">
                {children}
            </div>
        </div>
    );
}

function DetailRow({ label, value, mono = false, truncate = false, className = '' }) {
    if (!value && value !== 0) return null;
    return (
        <div className="flex items-start py-2.5 border-b border-gray-100 dark:border-slate-700 last:border-0">
            <span className="w-36 text-sm text-gray-500 dark:text-gray-400 flex-shrink-0">{label}</span>
            <span className={`text-sm text-gray-900 dark:text-white ${mono ? 'font-mono' : ''} ${truncate ? 'truncate' : ''} ${className}`}>
                {String(value)}
            </span>
        </div>
    );
}

export default function SchedulerShow() {
    const { organization, project, task, executions, executionStats, commandEvent, jobEvent, exceptionOccurrence } = usePage().props;
    const [activeTab, setActiveTab] = useState('overview');

    if (!task) {
        return (
            <AppLayout>
                <div className="p-8">
                    <p className="text-gray-500 dark:text-gray-400">Task not found</p>
                </div>
            </AppLayout>
        );
    }

    const tabs = [
        { key: 'overview', label: 'Overview' },
        { key: 'executions', label: 'Executions' + (executions?.length ? ` (${executions.length})` : '') },
    ];

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-5xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="mb-6">
                        <Link
                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/scheduler`}
                            className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors mb-3"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Scheduler
                        </Link>

                        {/* Task Summary */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start justify-between gap-4">
                                <div className="flex items-center gap-4">
                                    <div className={`w-14 h-14 rounded-xl flex items-center justify-center ${getStatusColor(task.last_status)}`}>
                                        <Clock className="w-7 h-7" />
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-3 mb-1">
                                            <span className="text-xl font-bold font-mono text-gray-900 dark:text-white">
                                                {task.task_name}
                                            </span>
                                            <span className={`inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold ${getStatusColor(task.last_status)}`}>
                                                {task.last_status || 'unknown'}
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span className="flex items-center gap-1">
                                                <Calendar className="w-4 h-4" />
                                                {task.frequency || task.expression || '-'}
                                            </span>
                                            <span>Type: {task.task_type || 'command'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Quick Stats */}
                            <div className="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Runs</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{executionStats?.total || 0}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Completed</p>
                                    <p className="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{executionStats?.completed || 0}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Failed</p>
                                    <p className="text-sm font-semibold text-red-600 dark:text-red-400">{executionStats?.failed || 0}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Avg Duration</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{formatDuration(executionStats?.avg_duration)}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Tabs */}
                    <div className="border-b border-gray-200 dark:border-slate-700 mb-6">
                        <div className="flex items-center gap-1">
                            {tabs.map((tab) => (
                                <button
                                    key={tab.key}
                                    onClick={() => setActiveTab(tab.key)}
                                    className={`px-4 py-3 text-sm font-medium transition-colors border-b-2 ${
                                        activeTab === tab.key
                                            ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400'
                                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                                    }`}
                                >
                                    {tab.label}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Tab Content */}
                    {activeTab === 'overview' && (
                        <div className="grid grid-cols-2 gap-6">
                            <DetailCard icon={Clock} title="Task Details">
                                <DetailRow label="Task Name" value={task.task_name} mono />
                                <DetailRow label="Type" value={task.task_type || 'command'} />
                                <DetailRow label="Expression" value={task.expression} mono />
                                <DetailRow label="Frequency" value={task.frequency} />
                                <DetailRow label="Description" value={task.description} />
                                <DetailRow label="Timezone" value={task.timezone} />
                            </DetailCard>

                            <DetailCard icon={Calendar} title="Schedule">
                                <DetailRow label="Last Run" value={task.last_run_at ? formatDateTime(task.last_run_at) : 'Never'} />
                                <DetailRow label="Next Run" value={task.next_run_at ? formatDateTime(task.next_run_at) : 'Not scheduled'} />
                                <DetailRow label="Last Status" value={task.last_status} />
                                <DetailRow label="Environment" value={task.environment || 'All'} />
                            </DetailCard>

                            {task.command_name && (
                                <DetailCard icon={Terminal} title="Command">
                                    <DetailRow label="Command Name" value={task.command_name} mono />
                                    {commandEvent && (
                                        <div className="py-2">
                                            <Link
                                                href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/commands/${commandEvent.uuid}`}
                                                className="flex items-center gap-2 text-sm text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300"
                                            >
                                                <Activity className="w-4 h-4" />
                                                View latest execution
                                                <ChevronRight className="w-4 h-4" />
                                            </Link>
                                        </div>
                                    )}
                                </DetailCard>
                            )}

                            {task.job_uuid && (
                                <DetailCard icon={Activity} title="Job">
                                    <DetailRow label="Job UUID" value={task.job_uuid} mono />
                                    {jobEvent && (
                                        <div className="py-2">
                                            <Link
                                                href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/jobs/${jobEvent.uuid}`}
                                                className="flex items-center gap-2 text-sm text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300"
                                            >
                                                <Activity className="w-4 h-4" />
                                                View job
                                                <ChevronRight className="w-4 h-4" />
                                            </Link>
                                        </div>
                                    )}
                                </DetailCard>
                            )}

                            {exceptionOccurrence && (
                                <DetailCard icon={FileText} title="Latest Error">
                                    <DetailRow label="Exception" value={exceptionOccurrence.exception_class} mono />
                                    <DetailRow label="Message" value={exceptionOccurrence.exception_message} />
                                    <div className="py-2">
                                        <Link
                                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/exceptions/${exceptionOccurrence.exception_group_uuid}`}
                                            className="flex items-center gap-2 text-sm text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300"
                                        >
                                            <Activity className="w-4 h-4" />
                                            View exception
                                            <ChevronRight className="w-4 h-4" />
                                        </Link>
                                    </div>
                                </DetailCard>
                            )}
                        </div>
                    )}

                    {activeTab === 'executions' && (
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                            {executions && executions.length > 0 ? (
                                <div className="divide-y divide-gray-100 dark:divide-slate-700">
                                    {executions.map((exec) => (
                                        <div key={exec.uuid} className="p-5">
                                            <div className="flex items-center justify-between mb-2">
                                                <div className="flex items-center gap-3">
                                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold ${getStatusColor(exec.status)}`}>
                                                        {exec.status}
                                                    </span>
                                                    <span className="text-sm text-gray-500 dark:text-gray-400">
                                                        {formatDateTime(exec.created_at)}
                                                    </span>
                                                </div>
                                                <span className={`text-sm font-medium ${
                                                    exec.duration_ms > 5000
                                                        ? 'text-red-600 dark:text-red-400'
                                                        : exec.duration_ms > 2000
                                                        ? 'text-amber-600 dark:text-amber-400'
                                                        : 'text-gray-900 dark:text-white'
                                                }`}>
                                                    {formatDuration(exec.duration_ms)}
                                                </span>
                                            </div>
                                            {exec.exception_message && (
                                                <p className="text-sm text-red-600 dark:text-red-400 truncate max-w-xl">
                                                    {exec.exception_message}
                                                </p>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="p-12 text-center">
                                    <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                                        <Clock className="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No executions</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Task execution history will appear here</p>
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
