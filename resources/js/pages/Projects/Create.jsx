import { Link, usePage, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { ArrowLeft, FolderKanban, Plus, X } from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

const frameworks = [
    { value: 'laravel', label: 'Laravel' },
    { value: 'php', label: 'PHP' },
    { value: 'nodejs', label: 'Node.js' },
    { value: 'python', label: 'Python' },
    { value: 'django', label: 'Django' },
    { value: 'rails', label: 'Ruby on Rails' },
    { value: 'express', label: 'Express.js' },
    { value: 'fastapi', label: 'FastAPI' },
    { value: 'spring', label: 'Spring Boot' },
];

const environments = [
    { value: 'production', label: 'Production' },
    { value: 'staging', label: 'Staging' },
    { value: 'development', label: 'Development' },
    { value: 'local', label: 'Local' },
];

export default function ProjectCreate() {
    const { organization } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        environment: 'production',
        framework: 'laravel',
        repository_url: '',
        homepage_url: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(`/organizations/${organization.id}/projects`);
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
                    <Link href={`/organizations/${organization?.id}`} className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {organization?.name}
                    </Link>
                    <span className="text-gray-400">/</span>
                    <span className="text-gray-900 dark:text-white font-medium">Create Project</span>
                </div>

                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                        Create Project
                    </h1>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Add a new project to start monitoring
                    </p>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    {/* Name */}
                    <div className="mb-6">
                        <label htmlFor="name" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Project Name *
                        </label>
                        <input
                            type="text"
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Enter project name"
                            required
                        />
                        {errors.name && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.name}</p>
                        )}
                    </div>

                    {/* Environment */}
                    <div className="mb-6">
                        <label htmlFor="environment" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Environment *
                        </label>
                        <select
                            id="environment"
                            value={data.environment}
                            onChange={(e) => setData('environment', e.target.value)}
                            className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            {environments.map((env) => (
                                <option key={env.value} value={env.value}>
                                    {env.label}
                                </option>
                            ))}
                        </select>
                        {errors.environment && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.environment}</p>
                        )}
                    </div>

                    {/* Framework */}
                    <div className="mb-6">
                        <label htmlFor="framework" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Framework *
                        </label>
                        <select
                            id="framework"
                            value={data.framework}
                            onChange={(e) => setData('framework', e.target.value)}
                            className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            {frameworks.map((fw) => (
                                <option key={fw.value} value={fw.value}>
                                    {fw.label}
                                </option>
                            ))}
                        </select>
                        {errors.framework && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.framework}</p>
                        )}
                    </div>

                    {/* Repository URL */}
                    <div className="mb-6">
                        <label htmlFor="repository_url" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Repository URL (optional)
                        </label>
                        <input
                            type="url"
                            id="repository_url"
                            value={data.repository_url}
                            onChange={(e) => setData('repository_url', e.target.value)}
                            className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="https://github.com/..."
                        />
                        {errors.repository_url && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.repository_url}</p>
                        )}
                    </div>

                    {/* Homepage URL */}
                    <div className="mb-6">
                        <label htmlFor="homepage_url" className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Homepage URL (optional)
                        </label>
                        <input
                            type="url"
                            id="homepage_url"
                            value={data.homepage_url}
                            onChange={(e) => setData('homepage_url', e.target.value)}
                            className="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="https://..."
                        />
                        {errors.homepage_url && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.homepage_url}</p>
                        )}
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <Link
                            href={`/organizations/${organization?.id}/projects`}
                            className="px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-sm font-medium transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50"
                        >
                            {processing ? 'Creating...' : 'Create Project'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
