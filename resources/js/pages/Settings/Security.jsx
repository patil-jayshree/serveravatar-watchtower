import AppLayout from '@/layouts/AppLayout';
import { Shield, Key, Smartphone } from 'lucide-react';

export default function Security() {
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Security Settings</h1>
                <div className="space-y-6 max-w-2xl">
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                        <div className="flex items-start gap-4">
                            <div className="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                <Key className="w-6 h-6 text-cyan-600" />
                            </div>
                            <div className="flex-1">
                                <h3 className="font-semibold text-gray-900 dark:text-white">Password</h3>
                                <p className="text-sm text-gray-500 mb-4">Change your password</p>
                                <button className="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium">Update Password</button>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                        <div className="flex items-start gap-4">
                            <div className="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                <Smartphone className="w-6 h-6 text-cyan-600" />
                            </div>
                            <div className="flex-1">
                                <h3 className="font-semibold text-gray-900 dark:text-white">Two-Factor Authentication</h3>
                                <p className="text-sm text-gray-500 mb-4">Add extra security</p>
                                <button className="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium">Enable 2FA</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
