import AppLayout from '@/layouts/AppLayout';
import { FileText } from 'lucide-react';

export default function LogShow() {
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Log Details</h1>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-8">
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                            <FileText className="w-6 h-6 text-gray-500" />
                        </div>
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Log Details</h2>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
