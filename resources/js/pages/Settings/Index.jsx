import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { User, Mail, Bell, Shield, Palette, LogOut, Key, Globe } from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

const settingsNav = [
    {
        name: 'Profile',
        href: '/settings/profile',
        icon: User,
        description: 'Update your profile information and avatar',
        color: 'blue',
    },
    {
        name: 'Security',
        href: '/settings/security',
        icon: Shield,
        description: 'Manage your password and security settings',
        color: 'red',
    },
    {
        name: 'Preferences',
        href: '/settings/preferences',
        icon: Palette,
        description: 'Customize your experience',
        color: 'purple',
    },
    {
        name: 'Sessions',
        href: '/settings/sessions',
        icon: Key,
        description: 'Manage your active sessions',
        color: 'green',
    },
];

const colorClasses = {
    blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
    red: 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
    green: 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
    purple: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
};

export default function SettingsIndex() {
    return (
        <AppLayout>
            <div className="p-6 max-w-4xl mx-auto">
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white mb-2">Settings</h1>
                <p className="text-gray-500 dark:text-gray-400 mb-8">
                    Manage your account settings and preferences
                </p>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {settingsNav.map((item) => {
                        const Icon = item.icon;
                        return (
                            <Link
                                key={item.name}
                                href={item.href}
                                className="flex items-start gap-4 p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-all"
                            >
                                <div className={`w-12 h-12 rounded-lg flex items-center justify-center ${colorClasses[item.color]}`}>
                                    <Icon className="w-6 h-6" />
                                </div>
                                <div className="flex-1">
                                    <h3 className="font-semibold text-gray-900 dark:text-white">{item.name}</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{item.description}</p>
                                </div>
                            </Link>
                        );
                    })}
                </div>
            </div>
        </AppLayout>
    );
}
