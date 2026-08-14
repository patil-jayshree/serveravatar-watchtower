import { Link, useForm, usePage } from '@inertiajs/react';
import { User, Shield, Palette, Key, CheckCircle } from 'lucide-react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';

const tabs = [
    { name: 'Profile', href: '/settings/profile', icon: User, current: false },
    { name: 'Security', href: '/settings/security', icon: Shield, current: false },
    { name: 'Preferences', href: '/settings/preferences', icon: Palette, current: true },
    { name: 'Sessions', href: '/settings/sessions', icon: Key, current: false },
];

export default function Preferences({ user, timezones, status }) {
    const { data, setData, put, processing } = useForm({
        timezone: user.timezone || 'UTC',
        theme: user.theme || 'system',
    });

    const submit = (e) => {
        e.preventDefault();
        put('/settings/preferences');
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
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">Preferences</h1>

                    {/* Success Alert */}
                    {status && (
                        <div className="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-center gap-3">
                            <CheckCircle className="w-5 h-5 text-green-600 dark:text-green-400" />
                            <p className="text-sm text-green-800 dark:text-green-200">{status}</p>
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-6">
                        {/* Theme */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Appearance</h3>

                            <div className="space-y-4">
                                <div>
                                    <label htmlFor="theme" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Theme
                                    </label>
                                    <select
                                        id="theme"
                                        value={data.theme}
                                        onChange={(e) => setData('theme', e.target.value)}
                                        className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    >
                                        <option value="system">System (Auto)</option>
                                        <option value="light">Light</option>
                                        <option value="dark">Dark</option>
                                    </select>
                                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Choose your preferred color scheme
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Timezone */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Timezone</h3>

                            <div>
                                <label htmlFor="timezone" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Timezone
                                </label>
                                <select
                                    id="timezone"
                                    value={data.timezone}
                                    onChange={(e) => setData('timezone', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                >
                                    {timezones.map((tz) => (
                                        <option key={tz} value={tz}>
                                            {tz}
                                        </option>
                                    ))}
                                </select>
                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    All timestamps will be displayed in this timezone
                                </p>
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex items-center justify-end gap-3">
                            <button
                                type="button"
                                className="px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg text-sm font-medium transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50"
                            >
                                {processing ? 'Saving...' : 'Save Preferences'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
