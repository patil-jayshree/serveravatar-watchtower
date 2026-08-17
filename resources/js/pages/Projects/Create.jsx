import { Link, usePage, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { ArrowLeft, Check, Folder } from 'lucide-react';
import { useState } from 'react';

export default function CreateProject() {
    const { organization, organizations } = usePage().props;
    const [selectedOrg, setSelectedOrg] = useState(organization?.id || '');
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        description: '',
        url: '',
        environment: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        const targetOrgId = organization?.id || selectedOrg;
        post(`/organizations/${targetOrgId}/projects`, {
            onSuccess: () => {
                // Redirect will happen automatically
            },
        });
    };

    const environments = [
        { value: 'production', label: 'Production' },
        { value: 'staging', label: 'Staging' },
        { value: 'development', label: 'Development' },
        { value: 'local', label: 'Local' },
    ];

    return (
        <AppLayout>
            <div className="p-8 max-w-2xl mx-auto">
                {/* Back Link */}
                <Link
                    href={organization ? `/organizations/${organization.id}` : '/projects'}
                    className="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-6"
                >
                    <ArrowLeft className="w-4 h-4" />
                    Back to {organization ? 'Organization' : 'Projects'}
                </Link>

                {/* Form Card */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                    {/* Header - Light Blue Section */}
                    <div className="bg-cyan-50 dark:bg-cyan-900/20 px-8 py-6 border-b border-gray-100 dark:border-slate-700">
                        <h1 className="text-xl font-semibold text-gray-900 dark:text-white mb-1">
                            Create project
                        </h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Add a new project to start monitoring your application.
                        </p>
                    </div>

                    {/* Body - Form Fields */}
                    <div className="px-8 py-6">
                        <form onSubmit={handleSubmit} className="space-y-5">
                            {/* Organization Dropdown - Only show when not org-scoped */}
                            {!organization && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Organization <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={selectedOrg}
                                        onChange={(e) => setSelectedOrg(e.target.value)}
                                        className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all text-sm"
                                        required
                                    >
                                        <option value="">Select an organization</option>
                                        {organizations?.map((org) => (
                                            <option key={org.id} value={org.id}>
                                                {org.name}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.organization_id && (
                                        <p className="mt-1.5 text-sm text-red-500">{errors.organization_id}</p>
                                    )}
                                </div>
                            )}

                            {/* Project Name */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Project name <span className="text-red-500">*</span>
                                </label>
                                <div className="relative">
                                    <Folder className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input
                                        type="text"
                                        placeholder="e.g. My Laravel App"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        className="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all text-sm"
                                    />
                                </div>
                                {errors.name && (
                                    <p className="mt-1.5 text-sm text-red-500">{errors.name}</p>
                                )}
                            </div>

                            {/* Description */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Description <span className="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <textarea
                                    rows="3"
                                    placeholder="e.g. Main application for monitoring API requests and exceptions."
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all resize-none text-sm"
                                />
                            </div>

                            {/* Application URL */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Application URL <span className="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <input
                                    type="url"
                                    placeholder="https://example.com"
                                    value={data.url}
                                    onChange={(e) => setData('url', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all text-sm"
                                />
                                {errors.url && (
                                    <p className="mt-1.5 text-sm text-red-500">{errors.url}</p>
                                )}
                            </div>

                            {/* Environment */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Environment <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={data.environment}
                                    onChange={(e) => setData('environment', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all text-sm"
                                    required
                                >
                                    <option value="">Select an environment</option>
                                    {environments.map((env) => (
                                        <option key={env.value} value={env.value}>
                                            {env.label}
                                        </option>
                                    ))}
                                </select>
                                {errors.environment && (
                                    <p className="mt-1.5 text-sm text-red-500">{errors.environment}</p>
                                )}
                            </div>

                            {/* Submit Button */}
                            <div className="flex justify-end gap-3 pt-4">
                                <Link
                                    href={organization ? `/organizations/${organization.id}` : '/projects'}
                                    className="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing || (!organization && !selectedOrg)}
                                    className="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-colors text-sm font-medium inline-flex items-center gap-2 disabled:opacity-50"
                                >
                                    <Check className="w-4 h-4" />
                                    Create project
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
