import AppLayout from '@/layouts/AppLayout';
import { AlertCircle } from 'lucide-react';

export default function ExceptionsIndex() {
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Exceptions</h1>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-12 text-center">
                    <div className="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                        <AlertCircle className="w-8 h-8 text-gray-400" />
                    </div>
                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No exceptions yet</h3>
                    <p className="text-gray-500">Your application exceptions will appear here</p>
                </div>
            </div>
        </AppLayout>
    );
}
