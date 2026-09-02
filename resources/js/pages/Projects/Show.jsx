import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { Activity, AlertCircle, Clock, Database, Terminal, Cpu, Key } from 'lucide-react';

export default function ProjectShow() {
    const { project, organization } = usePage().props;
    const links = [
        { name: 'Agent', href: `/organizations/${organization?.id}/projects/${project?.uuid}/agent`, icon: Cpu, color: 'cyan' },
        { name: 'Exceptions', href: `/organizations/${organization?.id}/projects/${project?.uuid}/exceptions`, icon: AlertCircle, color: 'red' },
        { name: 'Jobs', href: `/organizations/${organization?.id}/projects/${project?.uuid}/jobs`, icon: Clock, color: 'blue' },
        { name: 'Queries', href: `/organizations/${organization?.id}/projects/${project?.uuid}/queries`, icon: Database, color: 'purple' },
        { name: 'Commands', href: `/organizations/${organization?.id}/projects/${project?.uuid}/commands`, icon: Terminal, color: 'green' },
        { name: 'Requests', href: `/organizations/${organization?.id}/projects/${project?.uuid}/requests`, icon: Activity, color: 'orange' },
    ];
    const colors = { 
        red: 'bg-red-100 dark:bg-red-900/30 text-red-600', 
        blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600', 
        purple: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600', 
        green: 'bg-green-100 dark:bg-green-900/30 text-green-600', 
        orange: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600',
        cyan: 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600'
    };
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">{project?.name || 'Project'}</h1>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {links.map((item) => {
                        const Icon = item.icon;
                        return (
                            <Link key={item.name} href={item.href} className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 hover:border-cyan-500 transition-colors">
                                <div className={`w-12 h-12 rounded-xl ${colors[item.color]} flex items-center justify-center mb-4`}>
                                    <Icon className="w-6 h-6" />
                                </div>
                                <h3 className="font-semibold text-gray-900 dark:text-white">{item.name}</h3>
                            </Link>
                        );
                    })}
                </div>
            </div>
        </AppLayout>
    );
}
