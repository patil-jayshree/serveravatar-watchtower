import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import {
    FolderKanban,
    Plus,
    Settings,
    Activity,
    AlertTriangle,
    Clock,
    Database,
    Terminal,
    Bell,
    LineChart,
    FileText,
    ChevronRight,
    Server,
    RefreshCw,
    Bug,
    ListChecks,
} from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function ProjectShow() {
    const { organization, project, stats } = usePage().props;

    const monitoringLinks = [
        {
            name: 'Requests',
            href: `/organizations/${organization?.id}/projects/${project.id}/requests`,
            icon: Activity,
            description: 'HTTP request monitoring',
            color: 'blue',
        },
        {
            name: 'Exceptions',
            href: `/organizations/${organization?.id}/projects/${project.id}/exceptions`,
            icon: Bug,
            description: 'Error & exception tracking',
            color: 'red',
        },
        {
            name: 'Queries',
            href: `/organizations/${organization?.id}/projects/${project.id}/queries`,
            icon: Database,
            description: 'Database query monitoring',
            color: 'green',
        },
        {
            name: 'Jobs',
            href: `/organizations/${organization?.id}/projects/${project.id}/jobs`,
            icon: ListChecks,
            description: 'Background job monitoring',
            color: 'purple',
        },
        {
            name: 'Commands',
            href: `/organizations/${organization?.id}/projects/${project.id}/commands`,
            icon: Terminal,
            description: 'Artisan command monitoring',
            color: 'orange',
        },
        {
            name: 'Logs',
            href: `/organizations/${organization?.id}/projects/${project.id}/logs`,
            icon: FileText,
            description: 'Application log monitoring',
            color: 'gray',
        },
        {
            name: 'Scheduler',
            href: `/organizations/${organization?.id}/projects/${project.id}/scheduler`,
            icon: Clock,
            description: 'Scheduled task monitoring',
            color: 'cyan',
        },
        {
            name: 'Performance',
            href: `/organizations/${organization?.id}/projects/${project.id}/performance`,
            icon: LineChart,
            description: 'Performance metrics',
            color: 'pink',
        },
    ];

    const colorClasses = {
        blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
        red: 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
        green: 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        purple: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        orange: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
        gray: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
        cyan: 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
        pink: 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
    };

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
                    <span className="text-gray-900 dark:text-white font-medium">{project.name}</span>
                </div>

                {/* Header */}
                <div className="flex items-start justify-between mb-8">
                    <div className="flex items-center gap-4">
                        <div className="w-16 h-16 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <FolderKanban className="w-8 h-8 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                {project.name}
                            </h1>
                            <div className="flex items-center gap-3 mt-2">
                                <div className="flex items-center gap-1.5">
                                    <div className={`w-2 h-2 rounded-full ${project.is_agent_connected ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'}`}></div>
                                    <span className="text-sm text-gray-500 dark:text-gray-400">
                                        {project.is_agent_connected ? 'Agent Connected' : 'Agent Disconnected'}
                                    </span>
                                </div>
                                <span className="text-gray-300 dark:text-gray-600">•</span>
                                <span className="text-sm text-gray-500 dark:text-gray-400">
                                    Created {new Date(project.created_at).toLocaleDateString()}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div className="flex items-center gap-3">
                        <Link
                            href={`/organizations/${organization?.id}/projects/${project.id}/agent`}
                            className="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            <Server className="w-4 h-4" />
                            Agent
                        </Link>
                        <Link
                            href={`/organizations/${organization?.id}/projects/${project.id}/edit`}
                            className="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            <Settings className="w-4 h-4" />
                            Settings
                        </Link>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <Activity className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <p className="text-sm text-gray-500 dark:text-gray-400">Requests (24h)</p>
                                <p className="text-xl font-bold text-gray-900 dark:text-white">{stats?.requests_24h?.toLocaleString() || 0}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <AlertTriangle className="w-5 h-5 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <p className="text-sm text-gray-500 dark:text-gray-400">Errors (24h)</p>
                                <p className="text-xl font-bold text-gray-900 dark:text-white">{stats?.errors_24h || 0}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <Clock className="w-5 h-5 text-green-600 dark:text-green-400" />
                            </div>
                            <div>
                                <p className="text-sm text-gray-500 dark:text-gray-400">Avg Response</p>
                                <p className="text-xl font-bold text-gray-900 dark:text-white">{stats?.avg_response || '0ms'}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <Database className="w-5 h-5 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div>
                                <p className="text-sm text-gray-500 dark:text-gray-400">Slow Queries</p>
                                <p className="text-xl font-bold text-gray-900 dark:text-white">{stats?.slow_queries || 0}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Monitoring Links */}
                <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 className="font-semibold text-gray-900 dark:text-white">Monitoring</h2>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Access different monitoring features for this project
                        </p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-6">
                        {monitoringLinks.map((link) => {
                            const Icon = link.icon;
                            return (
                                <Link
                                    key={link.name}
                                    href={link.href}
                                    className="flex items-center gap-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all"
                                >
                                    <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${colorClasses[link.color]}`}>
                                        <Icon className="w-5 h-5" />
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <p className="font-medium text-gray-900 dark:text-white">{link.name}</p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400 truncate">{link.description}</p>
                                    </div>
                                </Link>
                            );
                        })}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
