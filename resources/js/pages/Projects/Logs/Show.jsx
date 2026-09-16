import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { FileText, Clock, Server, AlertCircle, Activity } from 'lucide-react';
import { useState } from 'react';

const LEVEL_COLORS = {
    DEBUG: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    INFO: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
    NOTICE: 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-400',
    WARNING: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400',
    ERROR: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
    CRITICAL: 'bg-red-200 dark:bg-red-900/50 text-red-800 dark:text-red-300',
    ALERT: 'bg-orange-200 dark:bg-orange-900/50 text-orange-800 dark:text-orange-300',
    EMERGENCY: 'bg-purple-200 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300',
};

function getLevelColor(level) {
    return LEVEL_COLORS[level?.toUpperCase()] || LEVEL_COLORS.INFO;
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

export default function LogShow() {
    const { organization, project, log, relatedRequest, relatedExceptionGroup, relatedLogs } = usePage().props;
    const [activeTab, setActiveTab] = useState('overview');

    if (!log) {
        return (
            <AppLayout>
                <div className="p-8">
                    <p className="text-gray-500 dark:text-gray-400">Log not found</p>
                </div>
            </AppLayout>
        );
    }

    const tabs = [
        { key: 'overview', label: 'Overview' },
        { key: 'context', label: 'Context' + (log.context && Object.keys(log.context).length > 0 ? ` (${Object.keys(log.context).length})` : '') },
        { key: 'related', label: 'Related' },
    ];

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-5xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="mb-6">
                        <Link
                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/logs`}
                            className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors mb-3"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Logs
                        </Link>

                        {/* Log Summary */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start justify-between gap-4">
                                <div className="flex items-center gap-4">
                                    <div className={`w-14 h-14 rounded-xl flex items-center justify-center ${getLevelColor(log.level)}`}>
                                        <FileText className="w-7 h-7" />
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-3 mb-1">
                                            <span className="text-xl font-bold text-gray-900 dark:text-white">
                                                {log.message}
                                            </span>
                                            <span className={`inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold ${getLevelColor(log.level)}`}>
                                                {log.level}
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span className="flex items-center gap-1">
                                                <Clock className="w-4 h-4" />
                                                {formatDateTime(log.logged_at)}
                                            </span>
                                            {log.channel && <span>Channel: {log.channel}</span>}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Quick Stats */}
                            <div className="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Level</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{log.level || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Channel</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{log.channel || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Environment</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white capitalize">{log.environment || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Host</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white truncate">{log.host || '-'}</p>
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
                        <div className="grid grid-cols-1 gap-6">
                            <DetailCard icon={FileText} title="Log Details">
                                <DetailRow label="Level" value={log.level} />
                                <DetailRow label="Channel" value={log.channel} />
                                <DetailRow label="Message" value={log.message} />
                                <DetailRow label="Logged At" value={formatDateTime(log.logged_at)} />
                                {log.file && <DetailRow label="File" value={`${log.file}:${log.line}`} mono />}
                                {log.exception_class && <DetailRow label="Exception Class" value={log.exception_class} mono />}
                                {log.exception_message && <DetailRow label="Exception Message" value={log.exception_message} />}
                            </DetailCard>

                            <DetailCard icon={Server} title="Environment">
                                <DetailRow label="Environment" value={log.environment} />
                                <DetailRow label="Host" value={log.host} />
                                <DetailRow label="Agent Version" value={log.agent_version} />
                            </DetailCard>
                        </div>
                    )}

                    {activeTab === 'context' && (
                        <div className="grid grid-cols-1 gap-6">
                            {log.context && Object.keys(log.context).length > 0 ? (
                                <DetailCard icon={FileText} title="Context Data">
                                    <pre className="text-sm font-mono text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-slate-900 rounded-lg p-4 overflow-auto max-h-96 whitespace-pre-wrap">
                                        {JSON.stringify(log.context, null, 2)}
                                    </pre>
                                </DetailCard>
                            ) : (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                                    <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                                        <FileText className="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No context data</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Context data for this log entry will appear here</p>
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
                            ) : null}

                            {relatedExceptionGroup ? (
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
                            ) : null}

                            {relatedLogs && relatedLogs.length > 0 && (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                                    <div className="px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                                        <h3 className="font-semibold text-gray-900 dark:text-white">Other Logs in Request</h3>
                                    </div>
                                    <div className="divide-y divide-gray-100 dark:divide-slate-700">
                                        {relatedLogs.slice(0, 5).map((l) => (
                                            <div key={l.uuid} className="p-5">
                                                <Link
                                                    href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/logs/${l.uuid}`}
                                                    className="flex items-center justify-between text-sm hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                                                >
                                                    <div className="flex items-center gap-3">
                                                        <span className={`px-2 py-0.5 rounded text-xs font-semibold ${getLevelColor(l.level)}`}>
                                                            {l.level}
                                                        </span>
                                                        <span className="text-gray-900 dark:text-white truncate max-w-md">{l.message}</span>
                                                    </div>
                                                    <span className="text-gray-500 dark:text-gray-400 ml-4">
                                                        {formatDateTime(l.logged_at)}
                                                    </span>
                                                </Link>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {!relatedRequest && !relatedExceptionGroup && (!relatedLogs || relatedLogs.length === 0) && (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                                    <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                                        <Activity className="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No related items</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Related requests, exceptions, and logs will appear here</p>
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
