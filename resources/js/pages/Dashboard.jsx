import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';
import {
    Building2,
    FolderKanban,
    Activity,
    AlertTriangle,
    Clock,
    CheckCircle2,
    TrendingUp,
    Server,
    ChevronDown,
    Sun,
    Moon,
    Plus,
} from 'lucide-react';

function cn(...classes) {
    return classes.filter(Boolean).join(' ');
}

function StatCard({ title, value, change, changeType, icon: Icon, href }) {
    return (
        <Link
            href={href || '#'}
            className="bg-card border rounded-xl p-6 hover:border-primary/50 transition-colors"
        >
            <div className="flex items-start justify-between mb-4">
                <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <Icon className="w-5 h-5 text-primary" />
                </div>
                {change && (
                    <span className={cn(
                        'text-xs font-medium px-2 py-1 rounded-full',
                        changeType === 'positive' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                        changeType === 'negative' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                        'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
                    )}>
                        {change}
                    </span>
                )}
            </div>
            <div className="text-2xl font-semibold text-foreground mb-1">{value}</div>
            <div className="text-sm text-muted-foreground">{title}</div>
        </Link>
    );
}

export default function Dashboard() {
    const { user, organizations, selectedOrg, dashboardData, timeRange } = usePage().props;
    const [isDark, setIsDark] = useState(() => {
        if (typeof window !== 'undefined') {
            return document.documentElement.classList.contains('dark');
        }
        return false;
    });

    const toggleTheme = () => {
        setIsDark(!isDark);
        document.documentElement.classList.toggle('dark');
    };

    const handleOrgSwitch = (orgId) => {
        // Will be handled by form submission to switch endpoint
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/organizations/switch/${orgId}`;
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    };

    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="flex items-center justify-between mb-8">
                    <div>
                        <h1 className="text-2xl font-semibold text-foreground">
                            Welcome back, {user?.name} 👋
                        </h1>
                        <p className="text-muted-foreground mt-1">
                            {selectedOrg ? `Monitoring ${selectedOrg.name}` : 'Select an organization'}
                        </p>
                    </div>
                    <div className="flex items-center gap-4">
                        {/* Time Range Selector */}
                        <select
                            value={timeRange}
                            className="px-3 py-2 rounded-lg border bg-background text-foreground text-sm"
                        >
                            <option value="1h">Last 1 hour</option>
                            <option value="24h">Last 24 hours</option>
                            <option value="7d">Last 7 days</option>
                            <option value="30d">Last 30 days</option>
                        </select>

                        {/* Theme Toggle */}
                        <button
                            onClick={toggleTheme}
                            className="p-2 rounded-lg hover:bg-accent transition-colors"
                            aria-label="Toggle theme"
                        >
                            {isDark ? (
                                <Sun className="w-5 h-5 text-foreground" />
                            ) : (
                                <Moon className="w-5 h-5 text-foreground" />
                            )}
                        </button>
                    </div>
                </div>

                {/* Organization Selector (if multiple) */}
                {organizations && organizations.length > 1 && (
                    <div className="mb-6">
                        <div className="relative inline-block">
                            <select
                                value={selectedOrg?.id || ''}
                                onChange={(e) => handleOrgSwitch(e.target.value)}
                                className="appearance-none px-4 py-2 pr-10 rounded-lg border bg-card text-foreground font-medium cursor-pointer"
                            >
                                {organizations.map(org => (
                                    <option key={org.id} value={org.id}>
                                        {org.name}
                                    </option>
                                ))}
                            </select>
                            <ChevronDown className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none" />
                        </div>
                    </div>
                )}

                {/* Stats Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatCard
                        title="Total Requests"
                        value={dashboardData?.requests?.total?.toLocaleString() || '0'}
                        change="+12.5%"
                        changeType="positive"
                        icon={Activity}
                        href={selectedOrg ? `/organizations/${selectedOrg.id}/projects` : '#'}
                    />
                    <StatCard
                        title="Exceptions"
                        value={dashboardData?.exceptions?.total || '0'}
                        change={dashboardData?.exceptions?.change || '0%'}
                        changeType={dashboardData?.exceptions?.changeType}
                        icon={AlertTriangle}
                        href={selectedOrg ? `/organizations/${selectedOrg.id}/exceptions` : '#'}
                    />
                    <StatCard
                        title="Avg Response Time"
                        value={dashboardData?.performance?.avgDuration || '0ms'}
                        change={dashboardData?.performance?.change || '0%'}
                        changeType={dashboardData?.performance?.changeType}
                        icon={Clock}
                        href={selectedOrg ? `/organizations/${selectedOrg.id}/performance` : '#'}
                    />
                    <StatCard
                        title="Jobs Processed"
                        value={dashboardData?.jobs?.total?.toLocaleString() || '0'}
                        change={dashboardData?.jobs?.change || '0%'}
                        changeType={dashboardData?.jobs?.changeType}
                        icon={Server}
                        href={selectedOrg ? `/organizations/${selectedOrg.id}/jobs` : '#'}
                    />
                </div>

                {/* Quick Actions */}
                <div className="bg-card border rounded-xl p-6 mb-8">
                    <h2 className="text-lg font-semibold text-foreground mb-4">Quick Actions</h2>
                    <div className="flex flex-wrap gap-4">
                        <Link
                            href="/organizations/create"
                            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                        >
                            <Plus className="w-4 h-4" />
                            Create Organization
                        </Link>
                        {selectedOrg && (
                            <>
                                <Link
                                    href={`/organizations/${selectedOrg.id}/projects/create`}
                                    className="inline-flex items-center gap-2 px-4 py-2 border rounded-lg hover:bg-accent transition-colors"
                                >
                                    <FolderKanban className="w-4 h-4" />
                                    Add Project
                                </Link>
                            </>
                        )}
                    </div>
                </div>

                {/* Recent Activity */}
                <div className="bg-card border rounded-xl p-6">
                    <h2 className="text-lg font-semibold text-foreground mb-4">Recent Activity</h2>
                    {dashboardData?.recentActivity?.length > 0 ? (
                        <div className="space-y-4">
                            {dashboardData.recentActivity.map((activity, index) => (
                                <div key={index} className="flex items-center gap-4 py-3 border-b last:border-0">
                                    <div className={cn(
                                        'w-2 h-2 rounded-full',
                                        activity.type === 'error' ? 'bg-red-500' :
                                        activity.type === 'warning' ? 'bg-yellow-500' :
                                        'bg-green-500'
                                    )} />
                                    <div className="flex-1 min-w-0">
                                        <p className="text-sm text-foreground truncate">{activity.message}</p>
                                        <p className="text-xs text-muted-foreground">{activity.time}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-muted-foreground text-sm">No recent activity</p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
