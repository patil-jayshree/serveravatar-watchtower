import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { AlertCircle, Clock, ChevronRight, FileText, History, XCircle, CheckCircle } from 'lucide-react';
import { useState } from 'react';

const STATUS_COLORS = {
    open: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
    resolved: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
};

function getStatusColor(status) {
    return STATUS_COLORS[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
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
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
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

export default function ExceptionShow() {
    const { organization, project, exception, occurrences } = usePage().props;
    const [activeTab, setActiveTab] = useState('overview');

    if (!exception) {
        return (
            <AppLayout>
                <div className="p-8">
                    <p className="text-gray-500 dark:text-gray-400">Exception not found</p>
                </div>
            </AppLayout>
        );
    }

    const tabs = [
        { key: 'overview', label: 'Overview' },
        { key: 'occurrences', label: 'Occurrences' + (occurrences?.length ? ` (${occurrences.length})` : '') },
    ];

    const handleUpdateStatus = () => {
        // Status update is handled via API in the controller
        window.location.reload();
    };

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-5xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="mb-6">
                        <Link
                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/exceptions`}
                            className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors mb-3"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Exceptions
                        </Link>

                        {/* Exception Summary */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start justify-between gap-4">
                                <div className="flex items-center gap-4">
                                    <div className="w-14 h-14 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                        <AlertCircle className="w-7 h-7 text-red-600 dark:text-red-400" />
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-3 mb-1">
                                            <span className="text-xl font-bold font-mono text-gray-900 dark:text-white">
                                                {exception.exception_class}
                                            </span>
                                            <span className={`inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold ${getStatusColor(exception.status)}`}>
                                                {exception.status === 'open' ? 'Open' : 'Resolved'}
                                            </span>
                                        </div>
                                        {exception.message && (
                                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-2xl">
                                                {exception.message}
                                            </p>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Quick Stats */}
                            <div className="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Occurrences</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{exception.occurrence_count || 0}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">First Seen</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{formatDate(exception.first_occurrence_at)}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Last Seen</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">{formatDate(exception.last_occurrence_at)}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white capitalize">{exception.status || 'open'}</p>
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
                            <DetailCard icon={FileText} title="Exception Details">
                                <DetailRow label="Exception Class" value={exception.exception_class} mono />
                                <DetailRow label="Message" value={exception.message} />
                                {exception.stack_trace && (
                                    <div className="py-3">
                                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-2">Stack Trace</p>
                                        <pre className="text-xs font-mono text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-slate-900 rounded-lg p-4 overflow-auto max-h-64 whitespace-pre-wrap">
                                            {exception.stack_trace}
                                        </pre>
                                    </div>
                                )}
                            </DetailCard>
                        </div>
                    )}

                    {activeTab === 'occurrences' && (
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                            {occurrences && occurrences.length > 0 ? (
                                <div className="divide-y divide-gray-100 dark:divide-slate-700">
                                    {occurrences.map((occ) => (
                                        <div key={occ.uuid} className="p-5">
                                            <div className="flex items-center gap-3 mb-3">
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold ${
                                                    occ.level === 'error' || occ.level === 'critical'
                                                        ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400'
                                                        : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400'
                                                }`}>
                                                    {occ.level?.toUpperCase() || 'ERROR'}
                                                </span>
                                                <span className="text-xs text-gray-500 dark:text-gray-400">
                                                    {formatDateTime(occ.occurred_at)}
                                                </span>
                                                {occ.environment && (
                                                    <span className="text-xs text-gray-500 dark:text-gray-400 capitalize">
                                                        {occ.environment}
                                                    </span>
                                                )}
                                            </div>
                                            {occ.exception_message && (
                                                <p className="text-sm text-gray-900 dark:text-white font-medium mb-2">{occ.exception_message}</p>
                                            )}
                                            {occ.stack_trace && (
                                                <pre className="text-xs font-mono text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-slate-900 rounded-lg p-3 mt-2 overflow-auto max-h-32 whitespace-pre-wrap">
                                                    {occ.stack_trace}
                                                </pre>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="p-12 text-center">
                                    <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                                        <History className="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No occurrences</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Exception occurrence history will appear here</p>
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
