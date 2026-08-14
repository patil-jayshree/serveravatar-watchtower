import { Link, usePage, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { ArrowLeft, Building2, Upload, X } from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function OrganizationCreate() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        logo: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/organizations');
    };

    return (
        <AppLayout>
            <div className="p-6 max-w-2xl mx-auto">
                {/* Breadcrumb */}
                <div className="flex items-center gap-2 text-sm mb-6">
                    <Link href="/organizations" className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Organizations
                    </Link>
                    <span className="text-gray-400">/</span>
                    <span className="text-gray-900 dark:text-white font-medium">Create</span>
                </div>

                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                        Create Organization
                    </h1>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Create a new organization to manage your projects
                    </p>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    {/* Name */}
                    <div className="mb-6">
                        <label htmlFor="name" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Organization Name *
                        </label>
                        <input
                            type="text"
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Enter organization name"
                            required
                        />
                        {errors.name && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.name}</p>
                        )}
                    </div>

                    {/* Logo */}
                    <div className="mb-6">
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Logo (optional)
                        </label>
                        <div className="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 dark:border-gray-700 border-dashed rounded-lg hover:border-primary-500 transition-colors">
                            <div className="space-y-2 text-center">
                                {data.logo ? (
                                    <div className="relative inline-block">
                                        <img
                                            src={URL.createObjectURL(data.logo)}
                                            alt="Logo preview"
                                            className="w-20 h-20 rounded-lg object-cover"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setData('logo', null)}
                                            className="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600"
                                        >
                                            <X className="w-4 h-4" />
                                        </button>
                                    </div>
                                ) : (
                                    <>
                                        <Upload className="mx-auto w-12 h-12 text-gray-400" />
                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                            <label htmlFor="logo-upload" className="cursor-pointer text-primary-600 hover:text-primary-500 dark:text-primary-400">
                                                Upload a file
                                            </label>
                                            <input
                                                id="logo-upload"
                                                name="logo-upload"
                                                type="file"
                                                className="sr-only"
                                                accept="image/*"
                                                onChange={(e) => setData('logo', e.target.files[0])}
                                            />
                                            <p className="mt-1">or drag and drop</p>
                                            <p className="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>
                        {errors.logo && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.logo}</p>
                        )}
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <Link
                            href="/organizations"
                            className="px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-sm font-medium transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50"
                        >
                            {processing ? 'Creating...' : 'Create Organization'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
