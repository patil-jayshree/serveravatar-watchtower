import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import {
    Activity,
    AlertCircle,
    ArrowRight,
    CheckCircle2,
    Clock,
    Database,
    FolderOpen,
    Plus,
    Server,
    Shield,
    TrendingUp,
} from 'lucide-react';

export default function Dashboard() {
    const { user, organizations, selectedOrg, dashboardData, timeRange } = usePage().props;

    const stats = dashboardData?.stats || {};
    const recentExceptions = dashboardData?.recent_exceptions || [];
    const recentJobs = dashboardData?.recent_jobs || [];
    const projects = dashboardData?.projects || [];

    const statCards = [
        {
            title: 'Requests',
            value: stats.requests?.toLocaleString() || '0',
            change: '+12%',
            changeType: 'positive',
            icon: Server,
            color: 'cyan',
        },
        {
            title: 'Exceptions',
            value: stats.exceptions?.toLocaleString() || '0',
            change: '-8%',
            changeType: 'positive',
            icon: AlertCircle,
            color: 'red',
        },
        {
            title: 'Jobs',
            value: stats.jobs?.toLocaleString() || '0',
            change: '+5%',
            changeType: 'positive',
            icon: Clock,
            color: 'green',
        },
        {
            title: 'Queries',
            value: stats.queries?.toLocaleString() || '0',
            change: '-3%',
            changeType: 'positive',
            icon: Database,
            color: 'purple',
        },
    ];

    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="flex items-center justify-between mb-8">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">
                            Welcome back, {user?.name || 'User'} 👋
                        </h1>
                        {selectedOrg && (
                            <p className="text-gray-500 dark:text-gray-400 text-sm mt-1">
                                {selectedOrg.name}
                            </p>
                        )}
                    </div>
                    <Link
                        href="/projects/create"
                        className="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors"
                    >
                        <Plus className="w-4 h-4" />
                        New Project
                    </Link>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    {statCards.map((stat) => {
                        const Icon = stat.icon;
                        return (
                            <div
                                key={stat.title}
                                className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5"
                            >
                                <div className="flex items-center justify-between mb-3">
                                    <span className="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {stat.title}
                                    </span>
                                    <div className={`w-9 h-9 rounded-lg bg-${stat.color}-100 dark:bg-${stat.color}-900/30 flex items-center justify-center`}>
                                        <Icon className={`w-5 h-5 text-${stat.color}-600 dark:text-${stat.color}-400`} />
                                    </div>
                                </div>
                                <div className="flex items-end justify-between">
                                    <span className="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {stat.value}
                                    </span>
                                    <span className={`text-xs font-medium ${stat.changeType === 'positive' ? 'text-green-600' : 'text-red-600'}`}>
                                        {stat.change}
                                    </span>
                                </div>
                            </div>
                        );
                    })}
                </div>

                {/* Main Content Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Recent Exceptions */}
                    <div className="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                Recent Exceptions
                            </h2>
                            <Link
                                href="/projects/exceptions"
                                className="text-sm text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 flex items-center gap-1"
                            >
                                View all <ArrowRight className="w-4 h-4" />
                            </Link>
                        </div>
                        {recentExceptions.length > 0 ? (
                            <div className="space-y-3">
                                {recentExceptions.slice(0, 5).map((exception) => (
                                    <div
                                        key={exception.id}
                                        className="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-slate-700/50"
                                    >
                                        <div className="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                            <AlertCircle className="w-4 h-4 text-red-600 dark:text-red-400" />
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {exception.message}
                                            </p>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                {exception.project?.name} • {exception.time_ago}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8">
                                <CheckCircle2 className="w-12 h-12 text-green-500 mx-auto mb-3" />
                                <p className="text-gray-500 dark:text-gray-400">No exceptions recorded</p>
                            </div>
                        )}
                    </div>

                    {/* Recent Jobs */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                Recent Jobs
                            </h2>
                            <Link
                                href="/projects/jobs"
                                className="text-sm text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 flex items-center gap-1"
                            >
                                View all <ArrowRight className="w-4 h-4" />
                            </Link>
                        </div>
                        {recentJobs.length > 0 ? (
                            <div className="space-y-3">
                                {recentJobs.slice(0, 5).map((job) => (
                                    <div
                                        key={job.id}
                                        className="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-slate-700/50"
                                    >
                                        <div className={`w-8 h-8 rounded-full flex items-center justify-center ${job.status === 'completed' ? 'bg-green-100 dark:bg-green-900/30' : job.status === 'failed' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30'}`}>
                                            <Clock className={`w-4 h-4 ${job.status === 'completed' ? 'text-green-600 dark:text-green-400' : job.status === 'failed' ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400'}`} />
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {job.name}
                                            </p>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                {job.time_ago}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8">
                                <Clock className="w-12 h-12 text-gray-400 mx-auto mb-3" />
                                <p className="text-gray-500 dark:text-gray-400">No jobs recorded</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Projects Section */}
                <div className="mt-8">
                    <div className="flex items-center justify-between mb-4">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                            Your Projects
                        </h2>
                        <Link
                            href="/projects"
                            className="text-sm text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 flex items-center gap-1"
                        >
                            View all <ArrowRight className="w-4 h-4" />
                        </Link>
                    </div>
                    {projects.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {projects.slice(0, 6).map((project) => (
                                <Link
                                    key={project.id}
                                    href={`/projects/${project.id}`}
                                    className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors"
                                >
                                    <div className="flex items-center gap-3 mb-3">
                                        <div className="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                            <FolderOpen className="w-5 h-5 text-cyan-600 dark:text-cyan-400" />
                                        </div>
                                        <div>
                                            <h3 className="font-medium text-gray-900 dark:text-white">
                                                {project.name}
                                            </h3>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                {project.exceptions_count || 0} exceptions
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span className="flex items-center gap-1">
                                            <Server className="w-3 h-3" />
                                            {project.requests_count || 0} requests
                                        </span>
                                        <span className="flex items-center gap-1">
                                            <Clock className="w-3 h-3" />
                                            {project.jobs_count || 0} jobs
                                        </span>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    ) : (
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-12 text-center">
                            <FolderOpen className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                No projects yet
                            </h3>
                            <p className="text-gray-500 dark:text-gray-400 mb-6">
                                Add your first Laravel project to start monitoring
                            </p>
                            <Link
                                href="/projects/create"
                                className="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors"
                            >
                                <Plus className="w-4 h-4" />
                                Add Project
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
