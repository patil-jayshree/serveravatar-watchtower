import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { ArrowLeft } from 'lucide-react';

export default function EditProject() {
    return (
        <AppLayout>
            <div className="p-8">
                <Link href="/projects" className="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-4">
                    <ArrowLeft className="w-4 h-4" /> Back to Projects
                </Link>
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Edit Project</h1>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-8 max-w-2xl">
                    <form className="space-y-6">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Project Name</label>
                            <input type="text" className="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500" />
                        </div>
                        <button type="submit" className="px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium">Save Changes</button>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
