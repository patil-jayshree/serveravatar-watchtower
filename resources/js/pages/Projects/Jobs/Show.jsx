import AppLayout from '@/layouts/AppLayout';
import { Clock } from 'lucide-react';

export default function JobShow() {
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Job Details</h1>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-8">
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <Clock className="w-6 h-6 text-blue-600" />
                        </div>
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Job Details</h2>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
