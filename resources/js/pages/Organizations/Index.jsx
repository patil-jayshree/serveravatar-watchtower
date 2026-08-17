import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';
import {
    Building2,
    Filter,
    Plus,
    Search,
    ArrowRight,
    CheckCircle2,
    AlertTriangle,
    XCircle,
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
            <div className="min-h-screen bg-gray-50 dark:bg-slate-900">
                {/* Page Header */}
                <div className="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700">
                    <div className="max-w-7xl mx-auto px-6 py-6">
                        {/* Top Row: Title + Create Button */}
                        <div className="flex items-center justify-between mb-5">
                            <div>
                                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                    Organizations
                                </h1>
                                <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    Manage and monitor your organizations and their projects.
                                </p>
                            </div>
                            <Link
                                href="/organizations/create"
                                className="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition-colors"
                            >
                                <Plus className="w-4 h-4" />
                                Create Organization
                            </Link>
                        </div>

                        {/* Search + Filter Row */}
                        <div className="flex items-center gap-3">
                            <div className="relative flex-1 max-w-md">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    type="text"
                                    placeholder="Search organizations..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                />
                            </div>
                            <button className="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                                <Filter className="w-4 h-4" />
                                Filter
                            </button>
                        </div>
                    </div>
                </div>

                {/* Content Area */}
                <div className="max-w-7xl mx-auto px-6 py-6">
                    {filteredOrgs.length > 0 ? (
                        <>
                            {/* Organizations Grid */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                                {filteredOrgs.map((org) => (
                                    <Link
                                        key={org.id}
                                        href={`/organizations/${org.id}`}
                                        className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md transition-all group"
                                    >
                                        {/* Top Row: Avatar + Name + Status */}
                                        <div className="flex items-start justify-between mb-4">
                                            <div className="flex items-center gap-3">
                                                {/* Org Avatar */}
                                                <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                                    {getInitials(org.name)}
                                                </div>
                                                <div>
                                                    <h3 className="font-semibold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
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
                                                        ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                                        : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'
                                                }`}
                                            >
                                                {org.status === 'active' ? 'Active' : 'Inactive'}
                                            </span>
                                        </div>

                                        {/* Stats Row */}
                                        <div className="flex items-center gap-4 pt-3 border-t border-gray-100 dark:border-slate-700">
                                            <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {org.projects_count} Project{org.projects_count !== 1 ? 's' : ''}
                                            </span>
                                            <span className="text-gray-300 dark:text-slate-600">|</span>
                                            <div className="flex items-center gap-1.5">
                                                <CheckCircle2 className="w-3.5 h-3.5 text-green-500" />
                                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {org.stats?.healthy || 0} Healthy
                                                </span>
                                            </div>
                                            <div className="flex items-center gap-1.5">
                                                <AlertTriangle className="w-3.5 h-3.5 text-orange-500" />
                                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {org.stats?.warning || 0} Warning
                                                </span>
                                            </div>
                                            <div className="flex items-center gap-1.5">
                                                <XCircle className="w-3.5 h-3.5 text-red-500" />
                                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {org.stats?.critical || 0} Critical
                                                </span>
                                            </div>
                                        </div>

                                        {/* Open Button */}
                                        <div className="mt-4 pt-3 border-t border-gray-100 dark:border-slate-700">
                                            <span className="inline-flex items-center gap-1 text-sm font-medium text-purple-600 dark:text-purple-400 group-hover:gap-2 transition-all">
                                                Open Organization
                                                <ArrowRight className="w-4 h-4" />
                                            </span>
                                        </div>
                                    </Link>
                                ))}
                            </div>

                            {/* Pagination */}
                            <div className="flex items-center justify-between">
                                <p className="text-sm text-gray-500 dark:text-gray-400">
                                    Showing {filteredOrgs.length} of {filteredOrgs.length} organizations
                                </p>
                                <div className="flex items-center gap-1">
                                    <button className="p-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800 disabled:opacity-50" disabled>
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button className="px-3 py-1.5 rounded-lg bg-purple-600 text-white text-sm font-medium">1</button>
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
                            <div className="w-16 h-16 mx-auto mb-4 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                <Building2 className="w-8 h-8 text-purple-600 dark:text-purple-400" />
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
                                    className="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition-colors"
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
