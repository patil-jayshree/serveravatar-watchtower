import AppLayout from '@/layouts/AppLayout';
import { AlertCircle } from 'lucide-react';

export default function ExceptionShow() {
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Exception Details</h1>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-8">
                    <div className="flex items-center gap-4 mb-6">
                        <div className="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <AlertCircle className="w-6 h-6 text-red-600" />
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Exception Details</h2>
                            <p className="text-gray-500">View exception information</p>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
