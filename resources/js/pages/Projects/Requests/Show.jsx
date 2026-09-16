import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Activity, Clock, Server, Monitor, Globe, User, Cpu, Database, AlertCircle, CheckCircle, XCircle, ChevronRight, FileText, Logs } from 'lucide-react';
import { useState } from 'react';

const METHOD_COLORS = {
    GET: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    POST: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
    PUT: 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
    PATCH: 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400',
    DELETE: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
};

const STATUS_COLORS = {
    success: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    error: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
    redirect: 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
};

function getMethodColor(method) {
    return METHOD_COLORS[method?.toUpperCase()] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
}

function getStatusColor(statusCode) {
    if (statusCode >= 200 && statusCode < 300) return 'text-emerald-600 dark:text-emerald-400';
    if (statusCode >= 300 && statusCode < 400) return 'text-amber-600 dark:text-amber-400';
    if (statusCode >= 400) return 'text-red-600 dark:text-red-400';
    return 'text-gray-600 dark:text-gray-400';
}

function formatDuration(ms) {
    if (!ms && ms !== 0) return '-';
    if (ms < 1000) return `${ms}ms`;
    return `${(ms / 1000).toFixed(2)}s`;
}

function formatBytes(bytes) {
    if (!bytes) return '-';
    if (bytes < 1024) return `${bytes}B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)}KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)}MB`;
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

function DetailRow({ label, value, mono = false, truncate = false }) {
    if (!value && value !== 0) return null;
    return (
        <div className="flex items-start py-2.5 border-b border-gray-100 dark:border-slate-700 last:border-0">
            <span className="w-36 text-sm text-gray-500 dark:text-gray-400 flex-shrink-0">{label}</span>
            <span className={`text-sm text-gray-900 dark:text-white ${mono ? 'font-mono' : ''} ${truncate ? 'truncate' : ''}`}>
                {String(value)}
            </span>
        </div>
    );
}

export default function RequestShow() {
    const { organization, project, event } = usePage().props;
    const [activeTab, setActiveTab] = useState('overview');

    if (!event) {
        return (
            <AppLayout>
                <div className="p-8">
                    <p className="text-gray-500 dark:text-gray-400">Request not found</p>
                </div>
            </AppLayout>
        );
    }

    const tabs = [
        { key: 'overview', label: 'Overview' },
        { key: 'response', label: 'Response' },
        { key: 'logs', label: 'Logs' + (event.logs?.length ? ` (${event.logs.length})` : '') },
    ];

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-5xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="mb-6">
                        <Link
                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/requests`}
                            className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors mb-3"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Requests
                        </Link>

                        {/* Request Summary */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start justify-between gap-4">
                                <div className="flex items-center gap-4">
                                    <div className="w-14 h-14 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                        <Activity className="w-7 h-7 text-orange-600 dark:text-orange-400" />
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-3 mb-1">
                                            <span className={`inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold ${getMethodColor(event.method)}`}>
                                                {event.method}
                                            </span>
                                            <span className={`text-2xl font-bold font-mono text-gray-900 dark:text-white`}>
                                                {event.path}
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span className={`font-semibold ${getStatusColor(event.status_code)}`}>
                                                {event.status_code} {event.status_text || (event.status_code >= 200 && event.status_code < 300 ? 'OK' : '')}
                                            </span>
                                            <span className="flex items-center gap-1">
                                                <Clock className="w-4 h-4" />
                                                {formatDuration(event.duration_ms)}
                                            </span>
                                            <span>{formatDateTime(event.requested_at)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Quick Stats */}
                            <div className="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Memory</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{formatBytes(event.memory_bytes)}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Environment</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white capitalize">{event.environment || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Host</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white truncate">{event.host || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Request ID</p>
                                    <p className="text-sm font-semibold text-gray-500 dark:text-gray-400 font-mono truncate">{event.request_id || '-'}</p>
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
                            <DetailCard icon={Globe} title="Request">
                                <DetailRow label="Method" value={event.method} />
                                <DetailRow label="Path" value={event.path} mono />
                                <DetailRow label="Full URL" value={event.url} mono truncate />
                                <DetailRow label="Content Type" value={event.content_type} />
                                <DetailRow label="IP Address" value={event.ip} mono />
                                <DetailRow label="User Agent" value={event.user_agent} truncate />
                            </DetailCard>

                            <DetailCard icon={Server} title="Response">
                                <DetailRow label="Status Code" value={event.status_code} />
                                <DetailRow label="Status Text" value={event.status_text} />
                                {event.error_type && (
                                    <>
                                        <DetailRow label="Error Type" value={event.error_type} />
                                        <DetailRow label="Error Message" value={event.error_message} />
                                    </>
                                )}
                            </DetailCard>

                            <DetailCard icon={Monitor} title="Application">
                                <DetailRow label="Route Name" value={event.route_name || '-'} mono />
                                <DetailRow label="Controller" value={event.controller_action || '-'} mono />
                                <DetailRow label="Environment" value={event.environment} />
                            </DetailCard>

                            <DetailCard icon={Cpu} title="Performance">
                                <DetailRow label="Duration" value={formatDuration(event.duration_ms)} />
                                <DetailRow label="Memory" value={formatBytes(event.memory_bytes)} />
                            </DetailCard>
                        </div>
                    )}

                    {activeTab === 'response' && (
                        <div className="grid grid-cols-1 gap-6">
                            <DetailCard icon={FileText} title="Response Body">
                                {event.response_body ? (
                                    <pre className="text-sm font-mono text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-slate-900 rounded-lg p-4 overflow-auto max-h-96 whitespace-pre-wrap">
                                        {event.response_body}
                                    </pre>
                                ) : (
                                    <p className="text-sm text-gray-500 dark:text-gray-400">No response body captured</p>
                                )}
                            </DetailCard>

                            {event.error_message && (
                                <DetailCard icon={AlertCircle} title="Error Details">
                                    <pre className="text-sm font-mono text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg p-4 overflow-auto max-h-48 whitespace-pre-wrap">
                                        {event.error_message}
                                    </pre>
                                </DetailCard>
                            )}
                        </div>
                    )}

                    {activeTab === 'logs' && (
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                            {event.logs && event.logs.length > 0 ? (
                                <div className="divide-y divide-gray-100 dark:divide-slate-700">
                                    {event.logs.map((log) => (
                                        <div key={log.id} className="p-5">
                                            <div className="flex items-center gap-3 mb-2">
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold ${
                                                    log.level === 'error' ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400' :
                                                    log.level === 'warning' ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400' :
                                                    'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400'
                                                }`}>
                                                    {log.level?.toUpperCase()}
                                                </span>
                                                <span className="text-xs text-gray-500 dark:text-gray-400">
                                                    {formatDateTime(log.created_at)}
                                                </span>
                                            </div>
                                            <p className="text-sm text-gray-900 dark:text-white font-medium mb-1">{log.message}</p>
                                            {log.context && Object.keys(log.context).length > 0 && (
                                                <pre className="text-xs font-mono text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-slate-900 rounded p-2 mt-2 overflow-auto">
                                                    {JSON.stringify(log.context, null, 2)}
                                                </pre>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="p-12 text-center">
                                    <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                                        <Logs className="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No logs for this request</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Log entries related to this request will appear here</p>
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
