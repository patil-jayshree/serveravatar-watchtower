import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { FolderKanban, Plus } from 'lucide-react';

export default function ProjectsIndex() {
    const { projects } = usePage().props;
    return (
        <AppLayout>
            <div className="p-8">
                <div className="flex items-center justify-between mb-8">
                    <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">Projects</h1>
                    <Link href="/projects/create" className="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium">
                        <Plus className="w-4 h-4" /> New Project
                    </Link>
                </div>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-12 text-center">
                    <FolderKanban className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">No projects yet</h3>
                    <p className="text-gray-500 mb-6">Create your first project to start monitoring</p>
                    <Link href="/projects/create" className="inline-flex items-center gap-2 px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium">
                        <Plus className="w-5 h-5" /> Create Project
                    </Link>
                </div>
            </div>
        </AppLayout>
    );
}
