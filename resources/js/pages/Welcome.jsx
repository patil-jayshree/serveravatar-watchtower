import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Building2, FolderPlus, Download, Activity, Plus, Sparkles, ArrowRight, FileText, Gauge, Bug, Shield } from 'lucide-react';

export default function Welcome() {
    const { user } = usePage().props;

    const steps = [
        {
            number: '01',
            title: 'Create organization',
            description: 'Group your team and applications under one workspace.',
            icon: Building2,
        },
        {
            number: '02',
            title: 'Add project',
            description: 'Register the Laravel application you want to watch.',
            icon: FolderPlus,
        },
        {
            number: '03',
            title: 'Install agent',
            description: 'Drop in the Watchtower package and paste your token.',
            icon: Download,
        },
        {
            number: '04',
            title: 'Start monitoring',
            description: 'Start receiving real-time telemetry and alerts.',
            icon: Activity,
        },
    ];

    return (
        <AppLayout>
            <div className="p-8 max-w-5xl mx-auto">
                {/* Header Banner */}
                <div className="bg-gray-50 dark:bg-slate-800/50 rounded-2xl border border-gray-200 dark:border-slate-700 p-6 mb-6">
                    <div className="flex items-center justify-between">
                        {/* Left - Text Content */}
                        <div>
                            <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-1">
                                Welcome back, {user?.name || 'User'}
                            </h1>
                            <p className="text-gray-500 dark:text-gray-400 text-sm">
                                Set up your first workspace to start watching your Laravel apps.
                            </p>
                        </div>

                        {/* Right - Setup Pending Badge */}
                        <div className="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-full">
                            <span className="w-2 h-2 bg-orange-500 rounded-full"></span>
                            <span className="text-sm text-gray-600 dark:text-gray-300">Setup pending</span>
                        </div>
                    </div>
                </div>

                {/* First Step Card */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-8 mb-8">
                    <div className="flex flex-col lg:flex-row gap-8">
                        {/* Left - Content */}
                        <div className="flex-1">
                            {/* FIRST STEP Badge */}
                            <div className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-cyan-50 dark:bg-cyan-900/20 rounded-full mb-4">
                                <Sparkles className="w-3.5 h-3.5 text-cyan-600 dark:text-cyan-400" />
                                <span className="text-xs font-semibold text-cyan-600 dark:text-cyan-400 tracking-wide">
                                    FIRST STEP
                                </span>
                            </div>

                            {/* Heading */}
                            <h2 className="text-2xl font-semibold text-gray-900 dark:text-white mb-3">
                                No organization added yet
                            </h2>

                            {/* Description */}
                            <p className="text-gray-500 dark:text-gray-400 mb-6 max-w-lg">
                                An organization is where your projects, team members and alert rules live. Create one and Watchtower will guide you through connecting your first Laravel application.
                            </p>

                            {/* Buttons */}
                            <div className="flex flex-wrap gap-3">
                                <Link
                                    href="/organizations/create"
                                    className="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium rounded-lg transition-colors"
                                >
                                    <Plus className="w-4 h-4" />
                                    Create organization
                                </Link>
                                <Link
                                    href="/docs"
                                    className="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors"
                                >
                                    <FileText className="w-4 h-4" />
                                    Read the docs
                                    <ArrowRight className="w-4 h-4" />
                                </Link>
                            </div>
                        </div>

                        {/* Right - Telescope Illustration */}
                        <div className="flex-shrink-0 flex items-center">
                            <img
                                src="/images/telescope.png"
                                alt="Telescope"
                                className="w-80 h-auto"
                            />
                        </div>
                    </div>
                </div>

                {/* How Watchtower Works - Cards Section */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-8">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white tracking-wide mb-6">
                        HOW WATCHTOWER WORKS
                    </h2>

                    {/* Steps - Cards Layout */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {steps.map((step) => {
                            const Icon = step.icon;
                            return (
                                <div 
                                    key={step.number}
                                    className="border border-gray-200 dark:border-slate-700 rounded-xl p-4 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors"
                                >
                                    {/* Icon and Number in one line */}
                                    <div className="flex items-center gap-2 mb-3">
                                        <div className="w-10 h-10 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                            <Icon className="w-5 h-5 text-cyan-600 dark:text-cyan-400" />
                                        </div>
                                        <p className="text-xs font-medium text-cyan-600 dark:text-cyan-400">
                                            {step.number}
                                        </p>
                                    </div>

                                    {/* Title */}
                                    <h3 className="font-semibold text-gray-900 dark:text-white mb-1 text-sm">
                                        {step.title}
                                    </h3>

                                    {/* Description */}
                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                        {step.description}
                                    </p>
                                </div>
                            );
                        })}
                    </div>
                </div>

                {/* Feature Cards Section */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                    {/* Request & Queue Telemetry */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors">
                        <div className="w-12 h-12 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center mb-4">
                            <Gauge className="w-6 h-6 text-cyan-600 dark:text-cyan-400" />
                        </div>
                        <h3 className="font-semibold text-gray-900 dark:text-white mb-2">
                            Request & queue telemetry
                        </h3>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Tracks latency and throughput in real time.
                        </p>
                    </div>

                    {/* Exception Tracking */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors">
                        <div className="w-12 h-12 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center mb-4">
                            <Bug className="w-6 h-6 text-cyan-600 dark:text-cyan-400" />
                        </div>
                        <h3 className="font-semibold text-gray-900 dark:text-white mb-2">
                            Exception tracking
                        </h3>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Provides grouped stack traces with release context.
                        </p>
                    </div>

                    {/* Uptime & Alerts */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors">
                        <div className="w-12 h-12 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center mb-4">
                            <Shield className="w-6 h-6 text-cyan-600 dark:text-cyan-400" />
                        </div>
                        <h3 className="font-semibold text-gray-900 dark:text-white mb-2">
                            Uptime & alerts
                        </h3>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Offers notifications for uptime issues before users notice.
                        </p>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
