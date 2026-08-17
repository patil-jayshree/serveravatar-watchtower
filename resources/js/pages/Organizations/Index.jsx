import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';
import {
    Building2,
    Plus,
    Search,
    ArrowRight,
} from 'lucide-react';

export default function OrganizationsIndex() {
    const { organizations } = usePage().props;
    const [search, setSearch] = useState('');

    const filteredOrgs = (organizations || []).filter((org) =>
        org.name.toLowerCase().includes(search.toLowerCase())
    );

    const getInitials = (name) => {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-7xl mx-auto px-8 py-8">
                    {/* Page Header: Title + Button */}
                    <div className="flex items-center justify-between mb-6">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                Organizations
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Manage and monitor your organizations and their projects.
                            </p>
                        </div>
                        <Link
                            href="/organizations/create"
                            className="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors"
                        >
                            <Plus className="w-4 h-4" />
                            Create Organization
                        </Link>
                    </div>

                    {/* Controls: Search */}
                    <div className="flex items-center gap-3 mb-6">
                        <div className="relative w-80">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                type="text"
                                placeholder="Search organizations..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    {/* Content */}
                    {filteredOrgs.length > 0 ? (
                        <>
                            {/* Organizations Grid */}
                            <div className="grid grid-cols-2 gap-6 mb-6">
                                {filteredOrgs.map((org) => (
                                    <Link
                                        key={org.id}
                                        href={`/organizations/${org.id}`}
                                        className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 hover:border-cyan-300 dark:hover:border-cyan-600 transition-all group"
                                    >
                                        {/* Top Row: Avatar + Name + Status */}
                                        <div className="flex items-start justify-between mb-5">
                                            <div className="flex items-center gap-3">
                                                {/* Org Avatar */}
                                                <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-cyan-700 flex items-center justify-center text-white font-semibold text-base flex-shrink-0">
                                                    {getInitials(org.name)}
                                                </div>
                                                <div>
                                                    <h3 className="font-semibold text-gray-900 dark:text-white text-base group-hover:text-cyan-600 dark:group-hover:text-cyan-400">
                                                        {org.name}
                                                    </h3>
                                                    <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                        Created on {org.created_at || 'N/A'}
                                                    </p>
                                                </div>
                                            </div>
                                            {/* Status Badge */}
                                            <span
                                                className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${
                                                    org.status === 'active'
                                                        ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                                        : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'
                                                }`}
                                            >
                                                {org.status === 'active' ? 'Active' : 'Inactive'}
                                            </span>
                                        </div>

                                        {/* Stats Row - VERTICAL Layout */}
                                        <div className="flex items-stretch gap-6 pt-4 border-t border-gray-100 dark:border-slate-700 mb-4">
                                            {/* Projects */}
                                            <div className="flex-1 flex flex-col items-center text-center">
                                                <span className="text-2xl font-bold text-gray-900 dark:text-white">
                                                    {org.projects_count}
                                                </span>
                                                <span className="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1">
                                                    Projects
                                                </span>
                                            </div>
                                            {/* Healthy */}
                                            <div className="flex-1 flex flex-col items-center text-center">
                                                <span className="text-2xl font-bold text-emerald-500">
                                                    {org.stats?.healthy || 0}
                                                </span>
                                                <span className="text-xs font-medium text-emerald-500 mt-1">
                                                    Healthy
                                                </span>
                                            </div>
                                            {/* Warning */}
                                            <div className="flex-1 flex flex-col items-center text-center">
                                                <span className="text-2xl font-bold text-amber-500">
                                                    {org.stats?.warning || 0}
                                                </span>
                                                <span className="text-xs font-medium text-amber-500 mt-1">
                                                    Warning
                                                </span>
                                            </div>
                                            {/* Critical */}
                                            <div className="flex-1 flex flex-col items-center text-center">
                                                <span className="text-2xl font-bold text-red-500">
                                                    {org.stats?.critical || 0}
                                                </span>
                                                <span className="text-xs font-medium text-red-500 mt-1">
                                                    Critical
                                                </span>
                                            </div>
                                        </div>

                                        {/* Open Button - Full Width */}
                                        <div className="w-full">
                                            <span className="inline-flex items-center justify-center gap-2 w-full px-4 py-3 bg-cyan-50 dark:bg-cyan-900/20 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 rounded-lg text-sm font-medium transition-colors">
                                                Open Organization
                                                <ArrowRight className="w-4 h-4" />
                                            </span>
                                        </div>
                                    </Link>
                                ))}
                            </div>

                            {/* Pagination */}
                            <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-slate-700">
                                <p className="text-sm text-gray-500 dark:text-gray-400">
                                    Showing 1 to {filteredOrgs.length} of {filteredOrgs.length} organizations
                                </p>
                                <div className="flex items-center gap-1">
                                    <button className="p-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800 disabled:opacity-50" disabled>
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button className="px-3 py-1.5 rounded-lg bg-cyan-600 text-white text-sm font-medium">1</button>
                                    <button className="p-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800 disabled:opacity-50" disabled>
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </>
                    ) : (
                        /* Empty State */
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-16 text-center">
                            <div className="w-16 h-16 mx-auto mb-4 bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center">
                                <Building2 className="w-8 h-8 text-cyan-600 dark:text-cyan-400" />
                            </div>
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {search ? 'No organizations found' : 'No organizations yet'}
                            </h3>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                                {search
                                    ? 'Try adjusting your search terms.'
                                    : 'Create your first organization to start managing and monitoring your Laravel projects.'}
                            </p>
                            {!search && (
                                <Link
                                    href="/organizations/create"
                                    className="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors"
                                >
                                    <Plus className="w-4 h-4" />
                                    Create Organization
                                </Link>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
