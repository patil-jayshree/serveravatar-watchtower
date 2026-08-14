import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { ArrowLeft, Check, Upload } from 'lucide-react';
import { useState } from 'react';

export default function CreateOrganization() {
    const [logoPreview, setLogoPreview] = useState(null);
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        slug: '',
        description: '',
        logo: null,
    });

    const handleLogoChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('logo', file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setLogoPreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/organizations');
    };

    return (
        <AppLayout>
            <div className="p-8 max-w-2xl mx-auto">
                {/* Back Link */}
                <Link href="/organizations" className="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-6">
                    <ArrowLeft className="w-4 h-4" /> Back to organizations
                </Link>

                {/* Form Card */}
                <div className="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                    {/* Header - Light Blue Section */}
                    <div className="bg-cyan-50 dark:bg-cyan-900/20 px-8 py-6 border-b border-gray-100 dark:border-slate-700">
                        <h1 className="text-xl font-semibold text-gray-900 dark:text-white mb-1">
                            Create organization
                        </h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            Add a new organization to organize and manage your projects.
                        </p>
                    </div>

                    {/* Body - Form Fields */}
                    <div className="px-8 py-6">
                        <form onSubmit={handleSubmit} className="space-y-5">
                            {/* Logo Upload */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Logo
                                </label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleLogoChange}
                                    className="hidden"
                                    id="logo-upload"
                                />
                                <label
                                    htmlFor="logo-upload"
                                    className="flex items-center gap-4 cursor-pointer"
                                >
                                    <div className="w-14 h-14 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center border border-gray-200 dark:border-slate-600 hover:border-cyan-400 dark:hover:border-cyan-500 transition-colors overflow-hidden">
                                        {logoPreview ? (
                                            <img
                                                src={logoPreview}
                                                alt="Logo preview"
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <Upload className="w-5 h-5 text-gray-400" />
                                        )}
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">
                                            Click to upload logo
                                        </p>
                                        <p className="text-xs text-gray-400 dark:text-gray-500">
                                            PNG or JPG, up to 2MB
                                        </p>
                                    </div>
                                </label>
                            </div>

                            {/* Organization Name */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Organization name
                                </label>
                                <input
                                    type="text"
                                    placeholder="e.g. Monitoring-Org"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all text-sm"
                                />
                            </div>

                            {/* Slug */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Slug <span className="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <input
                                    type="text"
                                    placeholder="monitoring-org"
                                    value={data.slug}
                                    onChange={(e) => setData('slug', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all text-sm"
                                />
                                <p className="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                                    This will be used in the URL.
                                </p>
                            </div>

                            {/* Description */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Description <span className="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <textarea
                                    rows="3"
                                    placeholder="e.g. Organization for monitoring our Laravel applications."
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all resize-none text-sm"
                                />
                            </div>

                            {/* Submit Button */}
                            <div className="flex justify-end gap-3 pt-4">
                                <Link
                                    href="/organizations"
                                    className="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-colors text-sm font-medium inline-flex items-center gap-2 disabled:opacity-50"
                                >
                                    <Check className="w-4 h-4" />
                                    Create organization
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
