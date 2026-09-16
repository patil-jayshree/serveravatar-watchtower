import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Database, Clock, Server, Activity, AlertTriangle, FileText } from 'lucide-react';
import { useState } from 'react';

const QUERY_TYPE_COLORS = {
    select: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
    insert: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    update: 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
    delete: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
    other: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
};

function getQueryTypeColor(type) {
    return QUERY_TYPE_COLORS[type?.toLowerCase()] || QUERY_TYPE_COLORS.other;
}

function formatDuration(ms) {
    if (!ms && ms !== 0) return '-';
    if (ms < 1000) return `${ms}ms`;
    return `${(ms / 1000).toFixed(2)}s`;
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

export default function QueryShow() {
    const { organization, project, query, relatedRequest } = usePage().props;
    const [activeTab, setActiveTab] = useState('overview');

    if (!query) {
        return (
            <AppLayout>
                <div className="p-8">
                    <p className="text-gray-500 dark:text-gray-400">Query not found</p>
                </div>
            </AppLayout>
        );
    }

    const tabs = [
        { key: 'overview', label: 'Overview' },
        { key: 'sql', label: 'SQL' },
        { key: 'related', label: 'Related' + (relatedRequest ? ' (1)' : '') },
    ];

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-5xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="mb-6">
                        <Link
                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/queries`}
                            className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors mb-3"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Queries
                        </Link>

                        {/* Query Summary */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start justify-between gap-4">
                                <div className="flex items-center gap-4">
                                    <div className="w-14 h-14 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        <Database className="w-7 h-7 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-3 mb-1">
                                            <span className={`inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold ${getQueryTypeColor(query.query_type)}`}>
                                                {query.query_type?.toUpperCase() || 'OTHER'}
                                            </span>
                                            {query.is_slow && (
                                                <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-semibold bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400">
                                                    <AlertTriangle className="w-3.5 h-3.5" />
                                                    Slow
                                                </span>
                                            )}
                                        </div>
                                        <div className="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span className="flex items-center gap-1">
                                                <Clock className="w-4 h-4" />
                                                {formatDuration(query.duration_ms)}
                                            </span>
                                            <span>{formatDateTime(query.occurred_at)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Quick Stats */}
                            <div className="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Connection</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white truncate">{query.connection_name || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Database</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white truncate">{query.database_name || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Driver</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white truncate">{query.driver || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Slow Query</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{query.is_slow ? 'Yes' : 'No'}</p>
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
                            <DetailCard icon={Database} title="Query Details">
                                <DetailRow label="Type" value={query.query_type?.toUpperCase()} />
                                <DetailRow label="Connection" value={query.connection_name} />
                                <DetailRow label="Database" value={query.database_name} />
                                <DetailRow label="Driver" value={query.driver} />
                                <DetailRow label="Duration" value={formatDuration(query.duration_ms)} />
                            </DetailCard>

                            <DetailCard icon={Server} title="Context">
                                <DetailRow label="Occurred At" value={formatDateTime(query.occurred_at)} />
                                {query.request_id && (
                                    <DetailRow label="Request ID" value={query.request_id} mono />
                                )}
                                {query.transaction_id && (
                                    <DetailRow label="Transaction ID" value={query.transaction_id} mono />
                                )}
                            </DetailCard>
                        </div>
                    )}

                    {activeTab === 'sql' && (
                        <div className="grid grid-cols-1 gap-6">
                            <DetailCard icon={FileText} title="SQL Query">
                                {query.sql ? (
                                    <pre className="text-sm font-mono text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-slate-900 rounded-lg p-4 overflow-auto max-h-96 whitespace-pre-wrap">
                                        {query.sql}
                                    </pre>
                                ) : (
                                    <p className="text-sm text-gray-500 dark:text-gray-400">No SQL captured</p>
                                )}
                            </DetailCard>

                            {query.bindings && query.bindings.length > 0 && (
                                <DetailCard icon={FileText} title="Bindings">
                                    <pre className="text-sm font-mono text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-slate-900 rounded-lg p-4 overflow-auto max-h-48">
                                        {JSON.stringify(query.bindings, null, 2)}
                                    </pre>
                                </DetailCard>
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
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
