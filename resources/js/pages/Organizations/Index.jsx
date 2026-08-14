import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Building2, Plus, Shield, BarChart3, Bell, FileText } from 'lucide-react';

export default function OrganizationsIndex() {
    const { organizations } = usePage().props;

    return (
        <AppLayout>
            <div className="p-8">
                <div className="flex items-center justify-between mb-8">
                    <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">Organizations</h1>
                    <Link href="/organizations/create" className="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium">
                        <Plus className="w-4 h-4" /> New Organization
                    </Link>
                </div>
                {organizations && organizations.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {organizations.map((org) => (
                            <Link key={org.id} href={`/organizations/${org.id}`} className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 hover:border-cyan-500 transition-colors">
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                        <Building2 className="w-6 h-6 text-cyan-600" />
                                    </div>
                                    <div>
                                        <h3 className="font-semibold text-gray-900 dark:text-white">{org.name}</h3>
                                        <p className="text-sm text-gray-500">{org.projects_count || 0} projects</p>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>
                ) : (
                    <div>
                        <div className="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                            {/* Building Illustration */}
                            <img
                                src="/images/building.png"
                                alt="Building"
                                className="w-48 h-auto mx-auto mb-6"
                            />

                            {/* Text Content */}
                            <h3 className="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                No organization added yet
                            </h3>
                            <p className="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                                Organizations help you manage and monitor multiple Laravel applications in one place.
                            </p>

                            {/* Button */}
                            <Link href="/organizations/create" className="inline-flex items-center gap-2 px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-sm font-medium transition-colors">
                                <Plus className="w-5 h-5" />
                                Create Organization
                            </Link>
                        </div>

                        {/* How Watchtower Works */}
                        <div className="mt-8">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                {/* Real-time Monitoring */}
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors">
                                    <div className="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-3">
                                        <Shield className="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                    </div>
                                    <h3 className="font-semibold text-gray-900 dark:text-white mb-1 text-sm">
                                        Real-time Monitoring
                                    </h3>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                        Monitor requests, exceptions, jobs, queries and more in real-time.
                                    </p>
                                </div>

                                {/* Performance Insights */}
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors">
                                    <div className="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-3">
                                        <BarChart3 className="w-5 h-5 text-green-600 dark:text-green-400" />
                                    </div>
                                    <h3 className="font-semibold text-gray-900 dark:text-white mb-1 text-sm">
                                        Performance Insights
                                    </h3>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                        Track performance and identify bottlenecks quickly.
                                    </p>
                                </div>

                                {/* Smart Alerts */}
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors">
                                    <div className="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mb-3">
                                        <Bell className="w-5 h-5 text-orange-600 dark:text-orange-400" />
                                    </div>
                                    <h3 className="font-semibold text-gray-900 dark:text-white mb-1 text-sm">
                                        Smart Alerts
                                    </h3>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                        Get notified about critical issues before your users notice.
                                    </p>
                                </div>

                                {/* Detailed Reports */}
                                <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors">
                                    <div className="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-3">
                                        <FileText className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <h3 className="font-semibold text-gray-900 dark:text-white mb-1 text-sm">
                                        Detailed Reports
                                    </h3>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                        Get detailed reports and insights about your application.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
