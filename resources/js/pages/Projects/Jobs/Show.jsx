import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Clock, AlertCircle, Activity, Server, FileText } from 'lucide-react';
import { useState } from 'react';

const STATUS_COLORS = {
    queued: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    started: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
    completed: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    failed: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
};

function getStatusColor(status) {
    return STATUS_COLORS[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
}

function formatDuration(ms) {
    if (!ms && ms !== 0) return '-';
    if (ms < 1000) return `${Math.round(ms)}ms`;
    return `${(ms / 1000).toFixed(2)}s`;
}

function formatDateTime(timestamp) {
    if (!timestamp) return '-';
    const date = new Date(timestamp * 1000);
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

export default function JobShow() {
    const { organization, project, job, relatedRequest, relatedExceptionGroup } = usePage().props;
    const [activeTab, setActiveTab] = useState('overview');

    if (!job) {
        return (
            <AppLayout>
                <div className="p-8">
                    <p className="text-gray-500 dark:text-gray-400">Job not found</p>
                </div>
            </AppLayout>
        );
    }

    const tabs = [
        { key: 'overview', label: 'Overview' },
        { key: 'error', label: 'Error' + (job.exception_class ? ' (1)' : '') },
        { key: 'related', label: 'Related' },
    ];

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-5xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="mb-6">
                        <Link
                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/jobs`}
                            className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors mb-3"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Jobs
                        </Link>

                        {/* Job Summary */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start justify-between gap-4">
                                <div className="flex items-center gap-4">
                                    <div className={`w-14 h-14 rounded-xl flex items-center justify-center ${
                                        job.status === 'failed'
                                            ? 'bg-red-100 dark:bg-red-900/30'
                                            : job.status === 'completed'
                                            ? 'bg-emerald-100 dark:bg-emerald-900/30'
                                            : job.status === 'started'
                                            ? 'bg-blue-100 dark:bg-blue-900/30'
                                            : 'bg-gray-100 dark:bg-slate-700'
                                    }`}>
                                        <Clock className={`w-7 h-7 ${
                                            job.status === 'failed'
                                                ? 'text-red-600 dark:text-red-400'
                                                : job.status === 'completed'
                                                ? 'text-emerald-600 dark:text-emerald-400'
                                                : job.status === 'started'
                                                ? 'text-blue-600 dark:text-blue-400'
                                                : 'text-gray-600 dark:text-gray-400'
                                        }`} />
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-3 mb-1">
                                            <span className="text-xl font-bold font-mono text-gray-900 dark:text-white">
                                                {job.job_name}
                                            </span>
                                            <span className={`inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold ${getStatusColor(job.status)}`}>
                                                {job.status}
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span className="flex items-center gap-1">
                                                <Clock className="w-4 h-4" />
                                                {formatDuration(job.duration_ms)}
                                            </span>
                                            <span>Queue: {job.queue || 'default'}</span>
                                            <span>{formatDateTime(job.queued_at)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Quick Stats */}
                            <div className="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Attempts</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{job.attempts || 0}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Queue</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{job.queue || 'default'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Connection</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white truncate">{job.connection || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Environment</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white capitalize">{job.environment || '-'}</p>
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
                            <DetailCard icon={Server} title="Job Details">
                                <DetailRow label="Job Name" value={job.job_name} mono />
                                <DetailRow label="Queue" value={job.queue || 'default'} />
                                <DetailRow label="Connection" value={job.connection} />
                                <DetailRow label="Attempts" value={job.attempts} />
                            </DetailCard>

                            <DetailCard icon={Clock} title="Timing">
                                <DetailRow label="Queued At" value={formatDateTime(job.queued_at)} />
                                <DetailRow label="Started At" value={formatDateTime(job.started_at)} />
                                <DetailRow label="Completed At" value={formatDateTime(job.completed_at || job.failed_at)} />
                                <DetailRow label="Duration" value={formatDuration(job.duration_ms)} />
                            </DetailCard>

                            <DetailCard icon={Server} title="Environment">
                                <DetailRow label="Environment" value={job.environment} />
                                <DetailRow label="Server Name" value={job.server_name} />
                                <DetailRow label="Laravel Version" value={job.laravel_version} />
                                <DetailRow label="PHP Version" value={job.php_version} />
                            </DetailCard>

                            <DetailCard icon={FileText} title="Context">
                                {job.request_id && <DetailRow label="Request ID" value={job.request_id} mono />}
                                {job.job_uuid && <DetailRow label="Job UUID" value={job.job_uuid} mono />}
                            </DetailCard>
                        </div>
                    )}

                    {activeTab === 'error' && (
                        <div className="grid grid-cols-1 gap-6">
                            {job.exception_class ? (
                                <DetailCard icon={AlertCircle} title="Exception Details">
                                    <DetailRow label="Exception Class" value={job.exception_class} mono />
                                    <DetailRow label="Message" value={job.exception_message} />
                                    <DetailRow label="File" value={job.exception_file} mono />
                                    <DetailRow label="Line" value={job.exception_line} />
                                    {job.stack_trace && (
                                        <div className="py-3">
                                            <p className="text-sm text-gray-500 dark:text-gray-400 mb-2">Stack Trace</p>
                                            <pre className="text-xs font-mono text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg p-4 overflow-auto max-h-64 whitespace-pre-wrap">
                                                {job.stack_trace}
                                            </pre>
                                        </div>
                                    )}
                                </DetailCard>
                            ) : (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                                    <div className="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-4">
                                        <Clock className="w-8 h-8 text-emerald-400" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No error</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">This job completed successfully</p>
                                </div>
                            )}
                        </div>
                    )}

                    {activeTab === 'related' && (
                        <div className="grid grid-cols-1 gap-6">
                            {relatedRequest ? (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                                    <div className="px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                                        <h3 className="font-semibold text-gray-900 dark:text-white">Related Request</h3>
                                    </div>
                                    <div className="p-5">
                                        <Link
                                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/requests/${relatedRequest.uuid}`}
                                            className="flex items-center gap-3 text-sm hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                                        >
                                            <Activity className="w-4 h-4 text-gray-400" />
                                            <span className="font-mono text-gray-900 dark:text-white">
                                                {relatedRequest.method} {relatedRequest.path}
                                            </span>
                                            <span className={`px-2 py-0.5 rounded text-xs font-semibold ${
                                                relatedRequest.status_code >= 200 && relatedRequest.status_code < 300
                                                    ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400'
                                                    : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400'
                                            }`}>
                                                {relatedRequest.status_code}
                                            </span>
                                        </Link>
                                    </div>
                                </div>
                            ) : (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                                    <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                                        <Activity className="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No related request</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">The related HTTP request will appear here</p>
                                </div>
                            )}

                            {relatedExceptionGroup && (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                                    <div className="px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                                        <h3 className="font-semibold text-gray-900 dark:text-white">Related Exception</h3>
                                    </div>
                                    <div className="p-5">
                                        <Link
                                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/exceptions/${relatedExceptionGroup.uuid}`}
                                            className="flex items-center gap-3 text-sm hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                                        >
                                            <AlertCircle className="w-4 h-4 text-red-400" />
                                            <span className="font-mono text-gray-900 dark:text-white">
                                                {relatedExceptionGroup.exception_type}
                                            </span>
                                            <span className={`px-2 py-0.5 rounded text-xs font-semibold ${
                                                relatedExceptionGroup.status === 'open'
                                                    ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400'
                                                    : 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400'
                                            }`}>
                                                {relatedExceptionGroup.status}
                                            </span>
                                        </Link>
                                    </div>
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
