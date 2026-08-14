import AppLayout from '@/layouts/AppLayout';
import { Terminal } from 'lucide-react';

export default function CommandShow() {
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Command Details</h1>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-8">
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <Terminal className="w-6 h-6 text-green-600" />
                        </div>
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Command Details</h2>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
