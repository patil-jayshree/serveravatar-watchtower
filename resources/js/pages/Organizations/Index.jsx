import { Link, usePage, router, useForm } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';
import AppLayout from '@/layouts/AppLayout';
import {
    Building2,
    Plus,
    Search,
    ArrowRight,
    Pencil,
    Trash2,
    X,
    XCircle,
} from 'lucide-react';
import Toast from '@/components/Toast';

export default function OrganizationsIndex() {
    const { organizations, orgs_pagination } = usePage().props;
    const [search, setSearch] = useState('');
    const [openMenuId, setOpenMenuId] = useState(null);
    const [showUpdateModal, setShowUpdateModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [orgToUpdate, setOrgToUpdate] = useState(null);
    const [orgToDelete, setOrgToDelete] = useState(null);
    const [deleteConfirmed, setDeleteConfirmed] = useState('');
    const [logoPreview, setLogoPreview] = useState(null);
    const [logoFile, setLogoFile] = useState(null);
    const [toast, setToast] = useState(null);
    const menuRef = useRef(null);
    const { data, setData, post, put, processing, errors } = useForm({
        name: '',
        description: '',
    });

    // Close dropdown when clicking outside
    useEffect(() => {
        const handleClickOutside = (e) => {
            if (menuRef.current && !menuRef.current.contains(e.target)) {
                setOpenMenuId(null);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

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

    const handleDeleteClick = (e, org) => {
        e.preventDefault();
        e.stopPropagation();
        setOrgToDelete(org);
        setDeleteConfirmed('');
        setShowDeleteModal(true);
        setOpenMenuId(null);
    };

    const handleDeleteSubmit = (e) => {
        e.preventDefault();
        if (deleteConfirmed !== orgToDelete.slug) return;
        router.delete(`/organizations/${orgToDelete.id}`, {
            onSuccess: () => {
                setShowDeleteModal(false);
                setOrgToDelete(null);
                setDeleteConfirmed('');
                setToast({ message: 'Organization deleted successfully!', type: 'success' });
            },
            onError: () => {
                setToast({ message: 'Failed to delete organization.', type: 'error' });
            },
        });
    };

    const handleUpdate = (e, org) => {
        e.preventDefault();
        e.stopPropagation();
        setOrgToUpdate(org);
        setData('name', org.name);
        setData('description', org.description || '');
        setLogoPreview(org.logo_url || null);
        setLogoFile(null);
        setShowUpdateModal(true);
        setOpenMenuId(null);
    };

    const handleLogoChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setLogoFile(file);
            setLogoPreview(URL.createObjectURL(file));
        }
    };

    const handleUpdateSubmit = (e) => {
        e.preventDefault();
        const options = {
            onSuccess: () => {
                setShowUpdateModal(false);
                setOrgToUpdate(null);
                setLogoPreview(null);
                setLogoFile(null);
                setToast({ message: 'Organization updated successfully!', type: 'success' });
                router.reload({ only: ['organizations'] });
            },
            onError: () => {
                setToast({ message: 'Failed to update organization.', type: 'error' });
            },
        };
        if (logoFile) {
            const formData = new FormData();
            formData.append('_method', 'put');
            formData.append('name', data.name);
            formData.append('description', data.description);
            formData.append('logo', logoFile);
            router.post(`/organizations/${orgToUpdate.id}`, formData, options);
        } else {
            router.post(`/organizations/${orgToUpdate.id}`, {
                name: data.name,
                description: data.description,
                _method: 'put',
            }, options);
        }
    };

    const toggleMenu = (e, orgId) => {
        e.preventDefault();
        e.stopPropagation();
        setOpenMenuId(openMenuId === orgId ? null : orgId);
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
                                className="w-full pl-10 pr-10 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            />
                            {search && (
                                <button
                                    type="button"
                                    onClick={() => setSearch('')}
                                    className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                >
                                    <XCircle className="w-4 h-4" />
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Content */}
                    {filteredOrgs.length > 0 ? (
                        <>
                            {/* Organizations Grid */}
                            <div className="grid grid-cols-2 gap-6 mb-6" ref={menuRef}>
                                {filteredOrgs.map((org) => (
                                    <div
                                        key={org.id}
                                        className="relative bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 hover:border-cyan-300 dark:hover:border-cyan-600 transition-all group"
                                    >
                                        {/* 3-Dot Menu Button */}
                                        <button
                                            onClick={(e) => toggleMenu(e, org.id)}
                                            className="absolute top-4 right-4 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                                        >
                                            <svg className="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        {/* Dropdown Menu */}
                                        {openMenuId === org.id && (
                                            <div className="absolute top-12 right-4 z-10 w-44 bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 shadow-lg py-1">
                                                <button
                                                    onClick={(e) => handleUpdate(e, org)}
                                                    className="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors"
                                                >
                                                    <Pencil className="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                                    Update
                                                </button>
                                                <div className="border-t border-gray-100 dark:border-slate-700 my-1" />
                                                <button
                                                    onClick={(e) => handleDeleteClick(e, org)}
                                                    className="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                >
                                                    <Trash2 className="w-4 h-4" />
                                                    Delete
                                                </button>
                                            </div>
                                        )}

                                        {/* Card Link */}
                                        <Link
                                            href={`/organizations/${org.id}`}
                                            className="block"
                                        >
                                            {/* Top Row: Avatar + Name + Status */}
                                            <div className="flex items-start justify-between mb-5 pr-8">
                                                <div className="flex items-center gap-3">
                                                    {/* Org Avatar */}
                                                    {org.logo_url ? (
                                                        <img
                                                            src={org.logo_url}
                                                            alt={org.name}
                                                            className="w-10 h-10 rounded-lg object-cover flex-shrink-0"
                                                        />
                                                    ) : (
                                                        <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-cyan-700 flex items-center justify-center text-white font-semibold text-base flex-shrink-0">
                                                            {getInitials(org.name)}
                                                        </div>
                                                    )}
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
                                    </div>
                                ))}
                            </div>

                            {/* Pagination */}
                            {orgs_pagination && orgs_pagination.last_page > 1 ? (
                                <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-slate-700">
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        Showing {filteredOrgs.length} of {orgs_pagination.total} organizations
                                    </p>
                                    <div className="flex items-center gap-1">
                                        {orgs_pagination.current_page > 1 ? (
                                            <button
                                                onClick={() => router.get(`/organizations?page=${orgs_pagination.current_page - 1}&per_page=${orgs_pagination.per_page}`)}
                                                className="p-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                        ) : (
                                            <button className="p-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-300 dark:text-gray-600 cursor-not-allowed" disabled>
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                        )}
                                        {Array.from({ length: orgs_pagination.last_page }, (_, i) => i + 1).map((page) => (
                                            <button
                                                key={page}
                                                onClick={() => router.get(`/organizations?page=${page}&per_page=${orgs_pagination.per_page}`)}
                                                className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
                                                    page === orgs_pagination.current_page
                                                        ? 'bg-cyan-600 text-white'
                                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700'
                                                }`}
                                            >
                                                {page}
                                            </button>
                                        ))}
                                        {orgs_pagination.current_page < orgs_pagination.last_page ? (
                                            <button
                                                onClick={() => router.get(`/organizations?page=${orgs_pagination.current_page + 1}&per_page=${orgs_pagination.per_page}`)}
                                                className="p-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        ) : (
                                            <button className="p-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-300 dark:text-gray-600 cursor-not-allowed" disabled>
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        )}
                                    </div>
                                </div>
                            ) : (
                                <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-slate-700">
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        Showing {filteredOrgs.length} of {orgs_pagination?.total || filteredOrgs.length} organizations
                                    </p>
                                </div>
                            )}
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

                {/* Update Organization Modal */}
                {showUpdateModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                        <div
                            className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 w-full max-w-md mx-4 shadow-xl"
                            onClick={(e) => e.stopPropagation()}
                        >
                            {/* Header */}
                            <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Update Organization</h2>
                                <button
                                    onClick={() => setShowUpdateModal(false)}
                                    className="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                                >
                                    <X className="w-5 h-5 text-gray-500" />
                                </button>
                            </div>

                            {/* Form */}
                            <form onSubmit={handleUpdateSubmit}>
                                <div className="px-6 py-4 space-y-4">
                                    {/* Logo Upload */}
                                    <div className="flex items-center gap-4">
                                        <div className="w-16 h-16 rounded-lg bg-gradient-to-br from-cyan-500 to-cyan-700 flex items-center justify-center text-white font-bold text-xl overflow-hidden">
                                            {logoPreview ? (
                                                <img src={logoPreview} alt="Logo" className="w-full h-full object-cover" />
                                            ) : (
                                                orgToUpdate?.name?.charAt(0).toUpperCase()
                                            )}
                                        </div>
                                        <div>
                                            <label
                                                htmlFor="logo-upload"
                                                className="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium cursor-pointer transition-colors"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Change Logo
                                            </label>
                                            <input
                                                id="logo-upload"
                                                type="file"
                                                accept="image/*"
                                                onChange={handleLogoChange}
                                                className="hidden"
                                            />
                                        </div>
                                    </div>

                                    {/* Name */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Organization Name
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            className="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                            placeholder="Enter organization name"
                                        />
                                        {errors.name && (
                                            <p className="mt-1.5 text-sm text-red-500">{errors.name}</p>
                                        )}
                                    </div>

                                    {/* Description */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Description
                                        </label>
                                        <textarea
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            rows="3"
                                            className="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent resize-none"
                                            placeholder="Enter organization description (optional)"
                                        />
                                        {errors.description && (
                                            <p className="mt-1.5 text-sm text-red-500">{errors.description}</p>
                                        )}
                                    </div>
                                </div>

                                {/* Footer */}
                                <div className="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-slate-700">
                                    <button
                                        type="button"
                                        onClick={() => setShowUpdateModal(false)}
                                        className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-4 py-2 text-sm font-medium text-white bg-cyan-600 hover:bg-cyan-700 rounded-lg transition-colors disabled:opacity-50"
                                    >
                                        {processing ? 'Updating...' : 'Update'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* Delete Organization Modal */}
                {showDeleteModal && orgToDelete && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                        <div
                            className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 w-full max-w-md mx-4 shadow-xl"
                            onClick={(e) => e.stopPropagation()}
                        >
                            {/* Header */}
                            <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-slate-700">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Delete Organization</h2>
                                <button
                                    onClick={() => setShowDeleteModal(false)}
                                    className="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                                >
                                    <X className="w-5 h-5 text-gray-500" />
                                </button>
                            </div>

                            {/* Warning Box */}
                            <div className="mx-6 mt-6 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 mt-0.5">
                                        <svg className="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p className="text-sm font-semibold text-red-800 dark:text-red-300">
                                            Are you sure you want to delete this organization?
                                        </p>
                                        <p className="text-xs text-red-700 dark:text-red-400 mt-1">
                                            This action cannot be undone. All projects and related data under this organization will be permanently deleted.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Form */}
                            <form onSubmit={handleDeleteSubmit}>
                                <div className="px-6 py-4">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Type <span className="font-semibold">{orgToDelete.slug}</span> to confirm
                                    </label>
                                    <input
                                        type="text"
                                        value={deleteConfirmed}
                                        onChange={(e) => setDeleteConfirmed(e.target.value)}
                                        placeholder={orgToDelete.slug}
                                        className="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    />
                                </div>

                                {/* Footer */}
                                <div className="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-slate-700">
                                    <button
                                        type="button"
                                        onClick={() => setShowDeleteModal(false)}
                                        className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={deleteConfirmed !== orgToDelete.slug}
                                        className="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Delete Organization
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>

            {/* Toast Notification */}
            {toast && (
                <Toast
                    message={toast.message}
                    type={toast.type}
                    onClose={() => setToast(null)}
                />
            )}
        </AppLayout>
    );
}
