import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import {
    Activity,
    AlertTriangle,
    Database,
    Server,
    Clock,
    Shield,
    Zap,
    ArrowRight,
    CheckCircle2,
    Sun,
    Moon,
    ChevronDown,
} from 'lucide-react';
import AppLayout from '@/layouts/AppLayout';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

const features = [
    {
        icon: Activity,
        title: 'Request Monitoring',
        description: 'Track every HTTP request with timing, status codes, and payload inspection. Identify slow endpoints and bottlenecks instantly.',
    },
    {
        icon: AlertTriangle,
        title: 'Exception Tracking',
        description: 'Capture and organize exceptions with full stack traces. Get notified immediately when errors occur.',
    },
    {
        icon: Database,
        title: 'Database Queries',
        description: 'Monitor query performance, identify N+1 problems, and optimize your database interactions.',
    },
    {
        icon: Server,
        title: 'Queue Monitoring',
        description: 'Track background job performance, failed jobs, and queue depth in real-time.',
    },
    {
        icon: Clock,
        title: 'Scheduler Tasks',
        description: 'Monitor scheduled tasks, track execution times, and get alerts for missed or failed jobs.',
    },
    {
        icon: Shield,
        title: 'Security Monitoring',
        description: 'Track command security events, monitor sensitive operations, and maintain audit trails.',
    },
];

const stats = [
    { label: 'Requests/sec', value: '10K+' },
    { label: 'Uptime', value: '99.9%' },
    { label: 'Latency', value: '<50ms' },
    { label: 'Teams', value: '500+' },
];

export default function Landing() {
    const [isDark, setIsDark] = useState(() => {
        if (typeof window !== 'undefined') {
            return document.documentElement.classList.contains('dark');
        }
        return false;
    });

    const toggleTheme = () => {
        setIsDark(!isDark);
        document.documentElement.classList.toggle('dark');
    };

    return (
        <div className="min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
            {/* Navbar */}
            <nav className="fixed top-0 left-0 right-0 z-50 glass border-b border-gray-200/50 dark:border-gray-800/50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-16">
                        {/* Logo */}
                        <a href="/" className="flex items-center gap-0">
                            <img
                                src="/logos/brand-logo.png"
                                alt="ServerAvatar Watchtower"
                                className="h-14 w-auto hidden dark:block transition-all duration-300"
                            />
                            <img
                                src="/logos/brand-logo.png"
                                alt="ServerAvatar Watchtower"
                                className="h-14 w-auto dark:hidden transition-all duration-300"
                            />
                            <div className="flex flex-col leading-tight -ml-2">
                                <span className="text-xl font-bold text-gray-900 dark:text-white">Server<span className="text-primary-600 dark:text-primary-400">Avatar</span></span>
                                <div className="flex items-center gap-2">
                                    <span className="h-px flex-1 bg-primary-600 dark:bg-primary-400"></span>
                                    <span className="text-xs font-bold text-primary-600 dark:text-primary-400 tracking-widest">WATCHTOWER</span>
                                    <span className="h-px flex-1 bg-primary-600 dark:bg-primary-400"></span>
                                </div>
                            </div>
                        </a>

                        {/* Right Side */}
                        <div className="flex items-center gap-3">
                            <button
                                onClick={toggleTheme}
                                className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                aria-label="Toggle theme"
                            >
                                {isDark ? (
                                    <Sun className="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                ) : (
                                    <Moon className="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                )}
                            </button>
                            <Link
                                href="/login"
                                className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
                            >
                                Log in
                            </Link>
                            <Link
                                href="/register"
                                className="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 rounded-lg transition-colors"
                            >
                                Get Started
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Hero Section */}
            <section className="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-b from-primary-50/50 via-white to-white dark:from-gray-900 dark:via-gray-900 dark:to-gray-900 pt-16">
                {/* Background Decoration */}
                <div className="absolute inset-0 overflow-hidden">
                    <div className="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-primary-200/30 dark:bg-primary-900/20 blur-3xl"></div>
                    <div className="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-primary-100/40 dark:bg-primary-800/10 blur-3xl"></div>
                    <div className="absolute inset-0 bg-[linear-gradient(to_right,#00000008_1px,transparent_1px),linear-gradient(to_bottom,#00000008_1px,transparent_1px)] bg-[size:3rem_3rem] dark:bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)]"></div>
                </div>

                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
                    <div className="text-center max-w-4xl mx-auto">
                        {/* Logo */}
                        <div className="mb-8 flex justify-center">
                            <img
                                src="/logos/brand-logo.png"
                                alt="ServerAvatar Watchtower"
                                className="h-16 hidden dark:block mx-auto transition-all duration-500"
                            />
                            <img
                                src="/logos/brand-logo.png"
                                alt="ServerAvatar Watchtower"
                                className="h-16 dark:hidden mx-auto transition-all duration-500"
                            />
                        </div>

                        {/* Product Name */}
                        <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-4 tracking-tight">
                            ServerAvatar Watchtower
                        </h1>

                        {/* Tagline */}
                        <p className="text-xl sm:text-2xl text-primary-600 dark:text-primary-400 font-semibold mb-6">
                            Monitor. Debug. Ship.
                        </p>

                        {/* Description */}
                        <p className="text-lg sm:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                            A powerful observability platform built for modern development teams.
                            Track exceptions, monitor requests, analyze database queries,
                            and gain AI-powered insights — all in one place.
                        </p>

                        {/* CTA Buttons */}
                        <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href="/login"
                                className="inline-flex items-center gap-2 px-8 py-4 text-base font-medium text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 rounded-lg transition-colors"
                            >
                                <ArrowRight className="w-5 h-5" />
                                Get Started Free
                            </Link>
                            <Link
                                href="/register"
                                className="inline-flex items-center gap-2 px-8 py-4 text-base font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            >
                                Create Account
                            </Link>
                        </div>

                        {/* Stats */}
                        <div className="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8">
                            {stats.map((stat) => (
                                <div key={stat.label} className="text-center">
                                    <div className="text-3xl font-bold text-primary-600 dark:text-primary-400">{stat.value}</div>
                                    <div className="text-sm text-gray-500 dark:text-gray-400">{stat.label}</div>
                                </div>
                            ))}
                        </div>

                        {/* Scroll Indicator */}
                        <div className="mt-16 animate-bounce">
                            <ChevronDown className="w-6 h-6 mx-auto text-gray-400 dark:text-gray-600" />
                        </div>
                    </div>
                </div>
            </section>

            {/* Features Section */}
            <section className="py-20 bg-gray-50 dark:bg-gray-800/50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Section Header */}
                    <div className="text-center mb-16">
                        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium mb-4">
                            Features
                        </div>
                        <h2 className="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                            Everything you need to ship with confidence
                        </h2>
                        <p className="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                            Get complete visibility into your applications health, performance, and errors.
                        </p>
                    </div>

                    {/* Feature Cards Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        {features.map((feature, index) => {
                            const Icon = feature.icon;
                            return (
                                <div
                                    key={feature.title}
                                    className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-primary/50 dark:hover:border-primary-400/50 transition-colors"
                                >
                                    <div className="w-12 h-12 rounded-lg bg-primary/10 dark:bg-primary-400/10 flex items-center justify-center mb-4">
                                        <Icon className="w-6 h-6 text-primary dark:text-primary-400" />
                                    </div>
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        {feature.title}
                                    </h3>
                                    <p className="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                        {feature.description}
                                    </p>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* CTA Section */}
            <section className="py-20 bg-primary-600 dark:bg-primary-900">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 className="text-3xl sm:text-4xl font-bold text-white mb-4">
                        Ready to get started?
                    </h2>
                    <p className="text-lg text-primary-100 dark:text-primary-200 mb-8 max-w-2xl mx-auto">
                        Join thousands of developers who trust ServerAvatar Watchtower to monitor their applications.
                    </p>
                    <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link
                            href="/register"
                            className="inline-flex items-center gap-2 px-8 py-4 text-base font-medium text-primary-600 dark:text-primary-900 bg-white hover:bg-gray-100 dark:bg-primary-100 dark:hover:bg-primary-200 rounded-lg transition-colors"
                        >
                            Create Free Account
                            <ArrowRight className="w-5 h-5" />
                        </Link>
                        <Link
                            href="/login"
                            className="inline-flex items-center gap-2 px-8 py-4 text-base font-medium text-white border border-white/30 hover:bg-white/10 rounded-lg transition-colors"
                        >
                            Sign In
                        </Link>
                    </div>
                </div>
            </section>

            {/* Footer */}
            <footer className="bg-gray-100 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-col md:flex-row items-center justify-between gap-4">
                        {/* Logo */}
                        <div className="flex items-center gap-2">
                            <img
                                src="/logos/brand-logo.png"
                                alt="Watchtower"
                                className="h-6 w-auto hidden dark:block"
                            />
                            <img
                                src="/logos/brand-logo.png"
                                alt="Watchtower"
                                className="h-6 w-auto dark:hidden"
                            />
                        </div>

                        {/* Links */}
                        <div className="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400">
                            <a href="#" className="hover:text-primary-600 dark:hover:text-primary-400">Privacy Policy</a>
                            <a href="#" className="hover:text-primary-600 dark:hover:text-primary-400">Terms</a>
                            <a href="#" className="hover:text-primary-600 dark:hover:text-primary-400">Contact</a>
                        </div>

                        {/* Copyright */}
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            © {new Date().getFullYear()} ServerAvatar Watchtower. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
