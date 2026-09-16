import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Terminal, Clock, Server, AlertCircle, Activity, FileText } from 'lucide-react';
import { useState } from 'react';

const STATUS_COLORS = {
    completed: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
    failed: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
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

export default function CommandShow() {
    const { organization, project, command, exceptionGroup, exceptionOccurrence, relatedCommands } = usePage().props;
    const [activeTab, setActiveTab] = useState('overview');

    if (!command) {
        return (
            <AppLayout>
                <div className="p-8">
                    <p className="text-gray-500 dark:text-gray-400">Command not found</p>
                </div>
            </AppLayout>
        );
    }

    const tabs = [
        { key: 'overview', label: 'Overview' },
        { key: 'error', label: 'Error' + (command.exception_class ? ' (1)' : '') },
        { key: 'related', label: 'Related' },
    ];

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-5xl mx-auto px-8 py-8">
                    {/* Page Header */}
                    <div className="mb-6">
                        <Link
                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/commands`}
                            className="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors mb-3"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Commands
                        </Link>

                        {/* Command Summary */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start justify-between gap-4">
                                <div className="flex items-center gap-4">
                                    <div className={`w-14 h-14 rounded-xl flex items-center justify-center ${
                                        command.status === 'failed'
                                            ? 'bg-red-100 dark:bg-red-900/30'
                                            : command.status === 'completed'
                                            ? 'bg-emerald-100 dark:bg-emerald-900/30'
                                            : 'bg-gray-100 dark:bg-slate-700'
                                    }`}>
                                        <Terminal className={`w-7 h-7 ${
                                            command.status === 'failed'
                                                ? 'text-red-600 dark:text-red-400'
                                                : command.status === 'completed'
                                                ? 'text-emerald-600 dark:text-emerald-400'
                                                : 'text-gray-600 dark:text-gray-400'
                                        }`} />
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-3 mb-1">
                                            <span className="text-xl font-bold font-mono text-gray-900 dark:text-white">
                                                {command.command_name}
                                            </span>
                                            <span className={`inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold ${getStatusColor(command.status)}`}>
                                                {command.status}
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span className="flex items-center gap-1">
                                                <Clock className="w-4 h-4" />
                                                {formatDuration(command.duration_ms)}
                                            </span>
                                            {command.exit_code !== null && (
                                                <span className={command.exit_code === 0 ? 'text-emerald-600' : 'text-red-600'}>
                                                    Exit: {command.exit_code}
                                                </span>
                                            )}
                                            <span>{formatDateTime(command.created_at)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Quick Stats */}
                            <div className="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Exit Code</p>
                                    <p className={`text-sm font-semibold ${
                                        command.exit_code === 0
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : command.exit_code !== null
                                            ? 'text-red-600 dark:text-red-400'
                                            : 'text-gray-900 dark:text-white'
                                    }`}>
                                        {command.exit_code !== null ? command.exit_code : '-'}
                                    </p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Environment</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white capitalize">{command.environment || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Server</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white truncate">{command.server_name || '-'}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Source</p>
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">Artisan</p>
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
                            <DetailCard icon={Terminal} title="Command Details">
                                <DetailRow label="Command" value={command.command_name} mono />
                                <DetailRow label="Status" value={command.status} />
                                <DetailRow label="Exit Code" value={command.exit_code} />
                                <DetailRow label="Duration" value={formatDuration(command.duration_ms)} />
                                <DetailRow label="Started At" value={command.started_at ? formatDateTime(new Date(command.started_at * 1000).toISOString()) : '-'} />
                                <DetailRow label="Finished At" value={command.finished_at ? formatDateTime(new Date(command.finished_at * 1000).toISOString()) : '-'} />
                            </DetailCard>

                            <DetailCard icon={Server} title="Environment">
                                <DetailRow label="Environment" value={command.environment} />
                                <DetailRow label="Server Name" value={command.server_name} />
                                <DetailRow label="Agent Version" value={command.agent_version} />
                                <DetailRow label="Laravel Version" value={command.laravel_version} />
                                <DetailRow label="PHP Version" value={command.php_version} />
                            </DetailCard>

                            {command.arguments && Object.keys(command.arguments).length > 0 && (
                                <DetailCard icon={FileText} title="Arguments">
                                    <pre className="text-sm font-mono text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-slate-900 rounded-lg p-4 overflow-auto max-h-48">
                                        {JSON.stringify(command.arguments, null, 2)}
                                    </pre>
                                </DetailCard>
                            )}

                            {command.options && Object.keys(command.options).length > 0 && (
                                <DetailCard icon={FileText} title="Options">
                                    <pre className="text-sm font-mono text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-slate-900 rounded-lg p-4 overflow-auto max-h-48">
                                        {JSON.stringify(command.options, null, 2)}
                                    </pre>
                                </DetailCard>
                            )}
                        </div>
                    )}

                    {activeTab === 'error' && (
                        <div className="grid grid-cols-1 gap-6">
                            {command.exception_class ? (
                                <DetailCard icon={AlertCircle} title="Exception Details">
                                    <DetailRow label="Exception Class" value={command.exception_class} mono />
                                    <DetailRow label="Message" value={command.exception_message} />
                                    <DetailRow label="File" value={command.exception_file} mono />
                                    <DetailRow label="Line" value={command.exception_line} />
                                    {command.stack_trace && (
                                        <div className="py-3">
                                            <p className="text-sm text-gray-500 dark:text-gray-400 mb-2">Stack Trace</p>
                                            <pre className="text-xs font-mono text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg p-4 overflow-auto max-h-64 whitespace-pre-wrap">
                                                {command.stack_trace}
                                            </pre>
                                        </div>
                                    )}
                                </DetailCard>
                            ) : (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                                    <div className="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-4">
                                        <Terminal className="w-8 h-8 text-emerald-400" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No error</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">This command completed successfully</p>
                                </div>
                            )}
                        </div>
                    )}

                    {activeTab === 'related' && (
                        <div className="grid grid-cols-1 gap-6">
                            {exceptionGroup ? (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                                    <div className="px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                                        <h3 className="font-semibold text-gray-900 dark:text-white">Related Exception</h3>
                                    </div>
                                    <div className="p-5">
                                        <Link
                                            href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/exceptions/${exceptionGroup.uuid}`}
                                            className="flex items-center gap-3 text-sm hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                                        >
                                            <AlertCircle className="w-4 h-4 text-red-400" />
                                            <span className="font-mono text-gray-900 dark:text-white">
                                                {exceptionGroup.exception_type}
                                            </span>
                                            <span className={`px-2 py-0.5 rounded text-xs font-semibold ${
                                                exceptionGroup.status === 'open'
                                                    ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400'
                                                    : 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400'
                                            }`}>
                                                {exceptionGroup.status}
                                            </span>
                                        </Link>
                                    </div>
                                </div>
                            ) : null}

                            {relatedCommands && relatedCommands.length > 0 && (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                                    <div className="px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                                        <h3 className="font-semibold text-gray-900 dark:text-white">Recent Similar Commands</h3>
                                    </div>
                                    <div className="divide-y divide-gray-100 dark:divide-slate-700">
                                        {relatedCommands.slice(0, 5).map((cmd) => (
                                            <div key={cmd.uuid} className="p-5">
                                                <Link
                                                    href={`/organizations/${organization?.uuid}/projects/${project?.uuid}/commands/${cmd.uuid}`}
                                                    className="flex items-center justify-between text-sm hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors"
                                                >
                                                    <span className="font-mono text-gray-900 dark:text-white">{cmd.command_name}</span>
                                                    <div className="flex items-center gap-3">
                                                        <span className={`px-2 py-0.5 rounded text-xs font-semibold ${getStatusColor(cmd.status)}`}>
                                                            {cmd.status}
                                                        </span>
                                                        <span className="text-gray-500 dark:text-gray-400">
                                                            {formatDateTime(cmd.created_at)}
                                                        </span>
                                                    </div>
                                                </Link>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {!exceptionGroup && (!relatedCommands || relatedCommands.length === 0) && (
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                                    <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                                        <Activity className="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No related items</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Related exceptions and commands will appear here</p>
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
