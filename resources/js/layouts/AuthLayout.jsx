import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { Sun, Moon } from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function AuthLayout({ children, title = 'Authentication' }) {
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
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased transition-colors duration-300">
            {/* Navbar */}
            <nav className="fixed top-0 left-0 right-0 z-50 glass border-b border-gray-200/50 dark:border-gray-800/50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-16">
                        {/* Logo & Brand */}
                        <Link href="/" className="flex items-center gap-0">
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
                        </Link>

                        {/* Theme Toggle */}
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
                    </div>
                </div>
            </nav>

            {/* Main Content */}
            <main className="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 pt-24">
                <div className="w-full max-w-md space-y-8">
                    {children}
                </div>
            </main>

            {/* Footer */}
            <footer className="bg-gray-100 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="py-8 text-center">
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            © {new Date().getFullYear()} ServerAvatar Watchtower. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
