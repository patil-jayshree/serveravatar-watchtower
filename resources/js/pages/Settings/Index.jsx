import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { User, Shield, Monitor } from 'lucide-react';

export default function SettingsIndex() {
    const items = [
        { name: 'Profile', href: '/settings/profile', icon: User, desc: 'Update your name, email and avatar' },
        { name: 'Security', href: '/settings/security', icon: Shield, desc: 'Manage password and two-factor authentication' },
        { name: 'Preferences', href: '/settings/preferences', icon: Monitor, desc: 'Customize your theme and language' },
    ];
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Settings</h1>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {items.map((item) => {
                        const Icon = item.icon;
                        return (
                            <Link key={item.name} href={item.href} className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 hover:border-cyan-500 transition-colors">
                                <div className="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center mb-4">
                                    <Icon className="w-6 h-6 text-cyan-600" />
                                </div>
                                <h3 className="font-semibold text-gray-900 dark:text-white mb-1">{item.name}</h3>
                                <p className="text-sm text-gray-500">{item.desc}</p>
                            </Link>
                        );
                    })}
                </div>
            </div>
        </AppLayout>
    );
}
