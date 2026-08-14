import AppLayout from '@/layouts/AppLayout';
import { Laptop, Globe, X } from 'lucide-react';

export default function Sessions() {
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Active Sessions</h1>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 max-w-2xl">
                    <div className="p-6 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                <Laptop className="w-6 h-6 text-gray-500" />
                            </div>
                            <div>
                                <div className="flex items-center gap-2">
                                    <h3 className="font-medium text-gray-900 dark:text-white">Chrome on MacOS</h3>
                                    <span className="px-2 py-0.5 text-xs font-medium bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 rounded-full">Current</span>
                                </div>
                                <div className="flex items-center gap-2 mt-1">
                                    <Globe className="w-4 h-4 text-gray-400" />
                                    <span className="text-sm text-gray-500">Mumbai, India</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
