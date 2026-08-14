import { Link, useForm, usePage } from '@inertiajs/react';
import { User, Shield, Palette, Key, CheckCircle, Monitor, Smartphone, Globe, Trash2 } from 'lucide-react';
import AppLayout from '@/layouts/AppLayout';

const tabs = [
    { name: 'Profile', href: '/settings/profile', icon: User, current: false },
    { name: 'Security', href: '/settings/security', icon: Shield, current: false },
    { name: 'Preferences', href: '/settings/preferences', icon: Palette, current: false },
    { name: 'Sessions', href: '/settings/sessions', icon: Key, current: true },
];

function formatDate(timestamp) {
    if (!timestamp) return 'Unknown';
    const date = new Date(timestamp * 1000);
    return date.toLocaleString();
}

function getDeviceIcon(userAgent) {
    if (!userAgent) return Monitor;
    const ua = userAgent.toLowerCase();
    if (ua.includes('mobile') || ua.includes('android') || ua.includes('iphone')) {
        return Smartphone;
    }
    return Monitor;
}

export default function Sessions({ sessions, currentSessionId, status }) {
    const { delete: destroy, processing } = useForm({});

    const revokeSession = (sessionId) => {
        if (confirm('Are you sure you want to revoke this session?')) {
            destroy(`/settings/sessions/${sessionId}`, {
                preserveScroll: true,
            });
        }
    };

    const revokeAll = () => {
        if (confirm('Are you sure you want to revoke all other sessions? This will log out all devices except your current one.')) {
            destroy('/settings/sessions/revoke-all', {
                preserveScroll: true,
            });
        }
    };

    return (
        <AppLayout>
            <div className="flex min-h-screen">
                {/* Sidebar */}
                <div className="w-64 border-r border-gray-200 dark:border-gray-700 p-6">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Settings</h2>
                    <nav className="space-y-1">
                        {tabs.map((tab) => {
                            const Icon = tab.icon;
                            return (
                                <Link
                                    key={tab.name}
                                    href={tab.href}
                                    className={`flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                                        tab.current
                                            ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'
                                    }`}
                                >
                                    <Icon className="w-5 h-5" />
                                    {tab.name}
                                </Link>
                            );
                        })}
                    </nav>
                </div>

                {/* Content */}
                <div className="flex-1 p-6 max-w-2xl">
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">Sessions</h1>

                    {/* Success Alert */}
                    {status && (
                        <div className="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-center gap-3">
                            <CheckCircle className="w-5 h-5 text-green-600 dark:text-green-400" />
                            <p className="text-sm text-green-800 dark:text-green-200">{status}</p>
                        </div>
                    )}

                    {/* Sessions Info */}
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white">Active Sessions</h3>
                            {sessions.length > 1 && (
                                <button
                                    onClick={revokeAll}
                                    disabled={processing}
                                    className="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50"
                                >
                                    Revoke all other sessions
                                </button>
                            )}
                        </div>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            These devices are currently logged into your account. Revoke any sessions that you don't recognize.
                        </p>

                        <div className="space-y-4">
                            {sessions.map((session) => {
                                const DeviceIcon = getDeviceIcon(session.user_agent);
                                const isCurrent = session.id === currentSessionId;

                                return (
                                    <div
                                        key={session.id}
                                        className="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg"
                                    >
                                        <div className="flex items-start gap-3">
                                            <div className="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                <DeviceIcon className="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                            </div>
                                            <div>
                                                <p className="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                                    {session.user_agent || 'Unknown Device'}
                                                    {isCurrent && (
                                                        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                            Current
                                                        </span>
                                                    )}
                                                </p>
                                                <p className="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-1">
                                                    <Globe className="w-4 h-4" />
                                                    {session.ip_address || 'Unknown IP'}
                                                </p>
                                                <p className="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                    Last active: {formatDate(session.last_activity)}
                                                </p>
                                            </div>
                                        </div>

                                        {!isCurrent && (
                                            <button
                                                onClick={() => revokeSession(session.id)}
                                                disabled={processing}
                                                className="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors disabled:opacity-50"
                                                title="Revoke session"
                                            >
                                                <Trash2 className="w-5 h-5" />
                                            </button>
                                        )}
                                    </div>
                                );
                            })}

                            {sessions.length === 0 && (
                                <p className="text-center text-gray-500 dark:text-gray-400 py-4">
                                    No active sessions found.
                                </p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
