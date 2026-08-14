import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Building2, FolderPlus, Download, Activity, HelpCircle, Plus } from 'lucide-react';

export default function Welcome() {
    const { user } = usePage().props;

    const steps = [
        {
            number: '01',
            title: 'Create Organization',
            description: 'Create your organization to get started.',
            icon: Building2,
        },
        {
            number: '02',
            title: 'Add Project',
            description: 'Add your Laravel application to start monitoring.',
            icon: FolderPlus,
        },
        {
            number: '03',
            title: 'Install Agent',
            description: 'Install the Watchtower agent in your application.',
            icon: Download,
        },
        {
            number: '04',
            title: 'Start Monitoring',
            description: 'Start receiving real-time telemetry and alerts.',
            icon: Activity,
        },
    ];

    return (
        <AppLayout>
            <div className="p-8 max-w-5xl mx-auto">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">
                        Welcome back, {user?.name || 'User'} 👋
                    </h1>
                </div>

                {/* Empty State Card */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-12 mb-8">
                    <div className="flex flex-col items-center text-center">
                        {/* Telescope Illustration with Signal Waves */}
                        <div className="mb-8">
                            <svg
                                width="200"
                                height="160"
                                viewBox="0 0 200 160"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                className="text-indigo-600 dark:text-indigo-400"
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
                                    opacity="0.5"
                                />
                                <path
                                    d="M55 48Q40 62 55 76"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    opacity="0.3"
                                />
                                
                                {/* Signal Waves Right */}
                                <path
                                    d="M135 55Q145 62 135 70"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    opacity="0.5"
                                />
                                <path
                                    d="M145 48Q160 62 145 76"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    opacity="0.3"
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

                        {/* CTA Button - Purple */}
                        <Link
                            href="/organizations/create"
                            className="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors"
                        >
                            <Plus className="w-5 h-5" />
                            Create Organization
                        </Link>
                    </div>
                </div>

                {/* How Watchtower Works - Horizontal Steps */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-8">
                    <div className="flex items-center gap-3 mb-8">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                            How Watchtower works
                        </h2>
                        <div className="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <HelpCircle className="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                    </div>

                    {/* Steps - Horizontal Layout */}
                    <div className="flex flex-col md:flex-row md:items-start gap-6 md:gap-4">
                        {steps.map((step, index) => {
                            const Icon = step.icon;
                            return (
                                <div key={step.number} className="flex-1 flex flex-col items-center text-center">
                                    {/* Step Card */}
                                    <div className="w-full flex flex-col items-center">
                                        {/* Icon with number badge */}
                                        <div className="relative mb-4">
                                            {/* Connector line - only show on desktop between items */}
                                            {index < steps.length - 1 && (
                                                <div className="hidden md:block absolute top-1/2 -right-6 w-6 h-0.5 bg-gray-200 dark:bg-slate-700" />
                                            )}
                                            <div className="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center">
                                                <Icon className="w-7 h-7 text-white" />
                                            </div>
                                            {/* Number badge */}
                                            <span className="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                                                <span className="text-[10px] font-bold text-indigo-600 dark:text-indigo-400">
                                                    {step.number}
                                                </span>
                                            </span>
                                        </div>

                                        {/* Title */}
                                        <h3 className="font-medium text-gray-900 dark:text-white mb-1 text-sm">
                                            {step.title}
                                        </h3>

                                        {/* Description */}
                                        <p className="text-xs text-gray-500 dark:text-gray-400">
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
