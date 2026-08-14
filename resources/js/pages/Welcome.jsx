import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Building2, Plus, ChevronRight } from 'lucide-react';

export default function Welcome() {
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Welcome to Watchtower 👋</h1>
                <div className="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-12 max-w-3xl mx-auto text-center">
                    <svg width="200" height="160" viewBox="0 0 200 160" fill="none" className="text-cyan-600 dark:text-cyan-400 mx-auto mb-8">
                        <path d="M20 140H180" stroke="currentColor" strokeWidth="2" strokeLinecap="round" opacity="0.3"/>
                        <path d="M100 140V100M85 140H115M90 140V115H110V140" stroke="currentColor" strokeWidth="3" strokeLinecap="round"/>
                        <path d="M95 100L100 70L105 100" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"/>
                        <ellipse cx="100" cy="70" rx="12" ry="5" fill="currentColor" opacity="0.2"/>
                        <circle cx="100" cy="62" r="10" stroke="currentColor" strokeWidth="2"/>
                        <circle cx="100" cy="62" r="5" fill="currentColor" opacity="0.4"/>
                        <path d="M90 62H82M110 62H118" stroke="currentColor" strokeWidth="2" strokeLinecap="round"/>
                        <circle cx="40" cy="30" r="2" fill="currentColor" opacity="0.3"/>
                        <circle cx="160" cy="25" r="2" fill="currentColor" opacity="0.3"/>
                    </svg>
                    <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-3">No organization added yet</h2>
                    <p className="text-gray-500 mb-8">Organizations help you manage and monitor multiple Laravel applications in one place.</p>
                    <Link href="/organizations/create" className="inline-flex items-center gap-2 px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium">
                        <Plus className="w-5 h-5" /> Create Organization <ChevronRight className="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </AppLayout>
    );
}
