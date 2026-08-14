import AppLayout from '@/layouts/AppLayout';
import { Sun, Moon, Monitor } from 'lucide-react';
import { useState } from 'react';

export default function Preferences() {
    const [theme, setTheme] = useState('system');
    const themes = [
        { value: 'light', label: 'Light', icon: Sun },
        { value: 'dark', label: 'Dark', icon: Moon },
        { value: 'system', label: 'System', icon: Monitor },
    ];
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Preferences</h1>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 max-w-2xl">
                    <h3 className="font-semibold text-gray-900 dark:text-white mb-4">Theme</h3>
                    <div className="grid grid-cols-3 gap-4">
                        {themes.map((t) => {
                            const Icon = t.icon;
                            return (
                                <button key={t.value} onClick={() => setTheme(t.value)} className={`flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-colors ${theme === t.value ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20' : 'border-slate-200 dark:border-slate-700'}`}>
                                    <Icon className={`w-6 h-6 ${theme === t.value ? 'text-cyan-600' : 'text-gray-400'}`} />
                                    <span className={`text-sm font-medium ${theme === t.value ? 'text-cyan-600' : 'text-gray-600 dark:text-gray-400'}`}>{t.label}</span>
                                </button>
                            );
                        })}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
