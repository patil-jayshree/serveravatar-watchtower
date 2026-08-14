import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import {
    LineChart,
    ChevronRight,
    RefreshCw,
    TrendingUp,
    TrendingDown,
    Activity,
    Clock,
    Zap,
} from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function PerformanceIndex() {
    const { organization, project, metrics } = usePage().props;

    return (
        <AppLayout>
            <div className="p-6">
                {/* Breadcrumb */}
                <div className="flex items-center gap-2 text-sm mb-6">
                    <Link href="/organizations" className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Organizations
                    </Link>
                    <ChevronRight className="w-4 h-4 text-gray-400" />
                    <Link href={`/organizations/${organization?.id}`} className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {organization?.name}
                    </Link>
                    <ChevronRight className="w-4 h-4 text-gray-400" />
                    <Link href={`/organizations/${organization?.id}/projects/${project?.id}`} className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {project?.name}
                    </Link>
                    <ChevronRight className="w-4 h-4 text-gray-400" />
                    <span className="text-gray-900 dark:text-white font-medium">Performance</span>
                </div>

                {/* Header */}
                <div className="flex items-center justify-between mb-6">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Performance Dashboard</h1>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Application performance metrics and insights
                        </p>
                    </div>
                    <div className="flex items-center gap-3">
                        <select className="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm">
                            <option value="24h">Last 24 hours</option>
                            <option value="7d">Last 7 days</option>
                            <option value="30d">Last 30 days</option>
                        </select>
                        <button className="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <RefreshCw className="w-4 h-4" />
                            Refresh
                        </button>
                    </div>
                </div>

                {/* Metrics Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-gray-500 dark:text-gray-400">Avg Response Time</p>
                                <p className="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                    {metrics?.avg_response_time || '0ms'}
                                </p>
                            </div>
                            <div className="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <Clock className="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                        <div className="mt-3 flex items-center gap-1 text-sm text-green-600 dark:text-green-400">
                            <TrendingDown className="w-4 h-4" />
                            <span>12% faster</span>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-gray-500 dark:text-gray-400">Requests/sec</p>
                                <p className="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                    {metrics?.requests_per_second || 0}
                                </p>
                            </div>
                            <div className="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <Activity className="w-6 h-6 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                        <div className="mt-3 flex items-center gap-1 text-sm text-green-600 dark:text-green-400">
                            <TrendingUp className="w-4 h-4" />
                            <span>8% increase</span>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-gray-500 dark:text-gray-400">Error Rate</p>
                                <p className="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                    {metrics?.error_rate || '0'}%
                                </p>
                            </div>
                            <div className="w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <Zap className="w-6 h-6 text-red-600 dark:text-red-400" />
                            </div>
                        </div>
                        <div className="mt-3 flex items-center gap-1 text-sm text-red-600 dark:text-red-400">
                            <TrendingUp className="w-4 h-4" />
                            <span>3% increase</span>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-gray-500 dark:text-gray-400">CPU Usage</p>
                                <p className="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                    {metrics?.cpu_usage || '0'}%
                                </p>
                            </div>
                            <div className="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <LineChart className="w-6 h-6 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                        <div className="mt-3 flex items-center gap-1 text-sm text-yellow-600 dark:text-yellow-400">
                            <span>Normal</span>
                        </div>
                    </div>
                </div>

                {/* Performance Chart Placeholder */}
                <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <h2 className="font-semibold text-gray-900 dark:text-white mb-4">Response Time Trend</h2>
                    <div className="h-64 flex items-center justify-center text-gray-400 dark:text-gray-500">
                        <div className="text-center">
                            <LineChart className="w-16 h-16 mx-auto mb-4 opacity-50" />
                            <p>Performance chart will render when data is available</p>
                        </div>
                    </div>
                </div>

                {/* Throughput & Memory */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h2 className="font-semibold text-gray-900 dark:text-white mb-4">Request Throughput</h2>
                        <div className="h-48 flex items-center justify-center text-gray-400 dark:text-gray-500">
                            <Activity className="w-12 h-12 opacity-50" />
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h2 className="font-semibold text-gray-900 dark:text-white mb-4">Memory Usage</h2>
                        <div className="h-48 flex items-center justify-center text-gray-400 dark:text-gray-500">
                            <Zap className="w-12 h-12 opacity-50" />
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
