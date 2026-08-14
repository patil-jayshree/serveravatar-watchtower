import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import {
    Building2,
    FolderPlus,
    Download,
    Activity,
    Plus,
    CheckCircle2,
    ArrowRight,
    Sun,
    Moon,
} from 'lucide-react';

export default function Welcome() {
    const { user } = usePage().props;
    const [isDark, setIsDark] = useState(() => {
        if (typeof window !== 'undefined') {
            return document.documentElement.classList.contains('dark');
        }
        return false;
    });

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
        <div className="min-h-screen bg-background">
            {/* Header */}
            <header className="border-b bg-card">
                <div className="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <img
                            src="/logos/brand-logo.png"
                            alt="Watchtower"
                            className="h-8 w-auto"
                        />
                    </div>
                    <div className="flex items-center gap-4">
                        <button
                            onClick={() => {
                                setIsDark(!isDark);
                                document.documentElement.classList.toggle('dark');
                            }}
                            className="p-2 rounded-lg hover:bg-accent transition-colors"
                            aria-label="Toggle theme"
                        >
                            {isDark ? (
                                <Sun className="w-5 h-5 text-foreground" />
                            ) : (
                                <Moon className="w-5 h-5 text-foreground" />
                            )}
                        </button>
                        <form method="POST" action="/logout">
                            <input type="hidden" name="_token" />
                            <button
                                type="submit"
                                className="text-sm text-muted-foreground hover:text-foreground transition-colors"
                            >
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {/* Main Content */}
            <main className="max-w-7xl mx-auto px-6 py-12">
                {/* Welcome */}
                <div className="mb-8">
                    <h1 className="text-3xl font-semibold text-foreground mb-1">
                        Welcome back, {user?.name} 👋
                    </h1>
                    <p className="text-muted-foreground">
                        Get started by creating your first organization.
                    </p>
                </div>

                {/* Empty State Card */}
                <div className="bg-card border rounded-xl p-8 mb-8 max-w-xl">
                    <div className="flex flex-col items-center text-center">
                        {/* Icon */}
                        <div className="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                            <Building2 className="w-8 h-8 text-primary" />
                        </div>

                        {/* Title & Description */}
                        <h2 className="text-xl font-semibold text-foreground mb-2">
                            No organization added yet
                        </h2>
                        <p className="text-muted-foreground mb-6 max-w-sm">
                            Organizations help you manage and monitor your Laravel applications.
                            Create one to get started with Watchtower.
                        </p>

                        {/* CTA Button */}
                        <Link
                            href="/organizations/create"
                            className="inline-flex items-center gap-2 px-6 py-3 bg-primary text-primary-foreground font-medium rounded-lg hover:bg-primary/90 transition-colors"
                        >
                            <Plus className="w-5 h-5" />
                            Create Organization
                        </Link>
                    </div>
                </div>

                {/* How Watchtower Works */}
                <div className="bg-card border rounded-xl p-8">
                    <h2 className="text-lg font-semibold text-foreground mb-6">
                        How Watchtower works
                    </h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {steps.map((step, index) => {
                            const Icon = step.icon;
                            return (
                                <div key={step.number} className="relative">
                                    {/* Connector line */}
                                    {index < steps.length - 1 && (
                                        <div className="hidden lg:block absolute top-8 left-full w-full h-px bg-border -translate-x-1/2" />
                                    )}
                                    <div className="flex flex-col">
                                        <div className="flex items-center gap-3 mb-2">
                                            <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                                <Icon className="w-5 h-5 text-primary" />
                                            </div>
                                            <span className="text-xs font-mono text-muted-foreground">
                                                {step.number}
                                            </span>
                                        </div>
                                        <h3 className="font-medium text-foreground mb-1">
                                            {step.title}
                                        </h3>
                                        <p className="text-sm text-muted-foreground">
                                            {step.description}
                                        </p>
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    {/* CTA */}
                    <div className="mt-8 pt-6 border-t flex items-center justify-between">
                        <p className="text-sm text-muted-foreground">
                            Ready to start monitoring your Laravel apps?
                        </p>
                        <Link
                            href="/organizations/create"
                            className="inline-flex items-center gap-2 text-sm font-medium text-primary hover:text-primary/80 transition-colors"
                        >
                            Get started
                            <ArrowRight className="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </main>
        </div>
    );
}
