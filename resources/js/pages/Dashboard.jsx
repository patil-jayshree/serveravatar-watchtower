import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import {
    Building2,
    FolderPlus,
    Download,
    Activity,
    Plus,
    ChevronRight,
} from 'lucide-react';

export default function Dashboard() {
    const { user } = usePage().props;

    const steps = [
        {
            number: '01',
            title: 'Create Organization',
            description: 'Start by creating your organization to manage your projects.',
            icon: Building2,
        },
        {
            number: '02',
            title: 'Add Laravel Project',
            description: 'Register your Laravel application with Watchtower.',
            icon: FolderPlus,
        },
        {
            number: '03',
            title: 'Install Watchtower Agent',
            description: 'Install the Watchtower agent in your Laravel project.',
            icon: Download,
        },
        {
            number: '04',
            title: 'Start Monitoring',
            description: 'View real-time telemetry, exceptions, and performance metrics.',
            icon: Activity,
        },
    ];

    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">
                        Welcome back, {user?.name || 'User'} 👋
                    </h1>
                </div>

                {/* Empty State Card */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-12 mb-8 max-w-3xl mx-auto">
                    <div className="flex flex-col items-center text-center">
                        {/* Telescope Illustration */}
                        <div className="mb-8 relative">
                            <svg
                                width="200"
                                height="160"
                                viewBox="0 0 200 160"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                className="text-cyan-600 dark:text-cyan-400"
                            >
                                {/* Ground */}
                                <path
                                    d="M20 140H180"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    opacity="0.3"
                                />
                                
                                {/* Tower Base */}
                                <path
                                    d="M100 140V100M85 140H115M90 140V115H110V140"
                                    stroke="currentColor"
                                    strokeWidth="3"
                                    strokeLinecap="round"
                                />
                                
                                {/* Tower Middle */}
                                <path
                                    d="M95 100L100 70L105 100"
                                    stroke="currentColor"
                                    strokeWidth="2.5"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                />
                                
                                {/* Observation Deck */}
                                <ellipse
                                    cx="100"
                                    cy="70"
                                    rx="12"
                                    ry="5"
                                    fill="currentColor"
                                    opacity="0.2"
                                />
                                
                                {/* Telescope */}
                                <circle
                                    cx="100"
                                    cy="62"
                                    r="10"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                />
                                <circle
                                    cx="100"
                                    cy="62"
                                    r="5"
                                    fill="currentColor"
                                    opacity="0.4"
                                />
                                
                                {/* Telescope Arms */}
                                <path
                                    d="M90 62H82M110 62H118"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                />
                                
                                {/* Signal Waves Left */}
                                <path
                                    d="M65 55Q55 62 65 70"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    opacity="0.4"
                                />
                                <path
                                    d="M55 48Q40 62 55 76"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    opacity="0.25"
                                />
                                
                                {/* Signal Waves Right */}
                                <path
                                    d="M135 55Q145 62 135 70"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    opacity="0.4"
                                />
                                <path
                                    d="M145 48Q160 62 145 76"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    opacity="0.25"
                                />
                                
                                {/* Stars */}
                                <circle cx="40" cy="30" r="2" fill="currentColor" opacity="0.3" />
                                <circle cx="160" cy="25" r="2" fill="currentColor" opacity="0.3" />
                                <circle cx="175" cy="45" r="1.5" fill="currentColor" opacity="0.2" />
                                <circle cx="25" cy="50" r="1.5" fill="currentColor" opacity="0.2" />
                                <circle cx="80" cy="20" r="1.5" fill="currentColor" opacity="0.25" />
                                <circle cx="130" cy="15" r="1.5" fill="currentColor" opacity="0.25" />
                                
                                {/* Cloud Left */}
                                <ellipse cx="35" cy="85" rx="15" ry="8" fill="currentColor" opacity="0.15" />
                                <ellipse cx="45" cy="82" rx="12" ry="6" fill="currentColor" opacity="0.15" />
                                
                                {/* Cloud Right */}
                                <ellipse cx="165" cy="80" rx="12" ry="6" fill="currentColor" opacity="0.15" />
                                <ellipse cx="175" cy="83" rx="10" ry="5" fill="currentColor" opacity="0.15" />
                            </svg>
                        </div>

                        {/* Title & Description */}
                        <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            No organization added yet
                        </h2>
                        <p className="text-gray-500 dark:text-gray-400 mb-8 max-w-md">
                            Organizations help you manage and monitor multiple Laravel applications in one place.
                        </p>

                        {/* CTA Button */}
                        <Link
                            href="/organizations/create"
                            className="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors"
                        >
                            <Plus className="w-5 h-5" />
                            Create Organization
                        </Link>
                    </div>
                </div>

                {/* How Watchtower Works */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                        How Watchtower works
                    </h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {steps.map((step, index) => {
                            const Icon = step.icon;
                            return (
                                <div key={step.number} className="relative">
                                    {/* Connector line */}
                                    {index < steps.length - 1 && (
                                        <div className="hidden lg:block absolute top-10 left-full w-full h-0.5 bg-slate-200 dark:bg-slate-700 -translate-x-1/2" />
                                    )}
                                    <div className="flex flex-col">
                                        <div className="flex items-center gap-3 mb-3">
                                            <div className="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                                <Icon className="w-6 h-6 text-cyan-600 dark:text-cyan-400" />
                                            </div>
                                            <span className="text-sm font-mono text-gray-400 dark:text-gray-500">
                                                {step.number}
                                            </span>
                                        </div>
                                        <h3 className="font-medium text-gray-900 dark:text-white mb-1">
                                            {step.title}
                                        </h3>
                                        <p className="text-sm text-gray-500 dark:text-gray-400">
                                            {step.description}
                                        </p>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
