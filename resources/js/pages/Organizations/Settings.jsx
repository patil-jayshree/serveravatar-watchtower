import { Link, useForm, usePage } from '@inertiajs/react';
import { Building2, Loader2, Trash2 } from 'lucide-react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';

export default function OrganizationSettings({ organization, isOwner, status }) {
    const { data, setData, post, processing, errors } = useForm({
        name: organization.name || '',
    });
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    const submit = (e) => {
        e.preventDefault();
        data.append('_method', 'PUT');
        post(`/organizations/${organization.uuid}/settings`, {
            preserveScroll: true,
        });
    };

    const deleteOrg = () => {
        if (confirm('Are you sure you want to delete this organization? This action cannot be undone.')) {
            // Would need to implement delete route
            console.log('Delete organization');
        }
    };

    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="mb-8">
                    <Link
                        href={`/organizations/${organization.uuid}`}
                        className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4"
                    >
                        Back to organization
                    </Link>
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Organization Settings</h1>
                </div>

                {/* Status Alert */}
                {status && (
                    <div className="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <p className="text-sm text-green-800 dark:text-green-200">{status}</p>
                    </div>
                )}

                <div className="max-w-2xl space-y-6">
                    {/* General Settings */}
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div className="flex items-center gap-3 mb-6">
                            <Building2 className="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">General</h2>
                        </div>

                        <form onSubmit={submit} className="space-y-6">
                            {/* Logo */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Logo
                                </label>
                                <div className="flex items-center gap-4">
                                    {organization.logo_url ? (
                                        <img
                                            src={organization.logo_url}
                                            alt={organization.name}
                                            className="w-16 h-16 rounded-lg object-cover"
                                        />
                                    ) : (
                                        <div className="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <Building2 className="w-8 h-8 text-gray-400" />
                                        </div>
                                    )}
                                    <div>
                                        <input type="file" name="logo" className="hidden" />
                                        <button
                                            type="button"
                                            className="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                        >
                                            Upload new logo
                                        </button>
                                        <button
                                            type="button"
                                            className="ml-2 px-3 py-1.5 text-sm text-red-600 hover:text-red-700 dark:hover:text-red-400"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {/* Name */}
                            <div>
                                <label htmlFor="name" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Organization Name <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    required
                                    className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                />
                                {errors.name && (
                                    <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.name}</p>
                                )}
                            </div>

                            {/* Actions */}
                            <div className="flex items-center justify-end gap-3 pt-4">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50 inline-flex items-center gap-2"
                                >
                                    {processing && <Loader2 className="w-4 h-4 animate-spin" />}
                                    {processing ? 'Saving...' : 'Save Changes'}
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Danger Zone */}
                    {isOwner && (
                        <div className="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 p-6">
                            <div className="flex items-center gap-3 mb-4">
                                <Trash2 className="w-5 h-5 text-red-600 dark:text-red-400" />
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Danger Zone</h2>
                            </div>
                            <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Once you delete an organization, there is no going back. All projects, data, and settings will be permanently removed.
                            </p>
                            <button
                                onClick={deleteOrg}
                                className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"
                            >
                                <Trash2 className="w-4 h-4" />
                                Delete Organization
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
