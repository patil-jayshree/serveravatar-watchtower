import { Link, usePage } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';
import {
    Building2,
    ChevronDown,
    FileText,
    FolderOpen,
    Gauge,
    HelpCircle,
    LogOut,
    Moon,
    Monitor,
    Settings,
    Sun,
    User,
} from 'lucide-react';
import './app-layout.css';

export default function AppLayout({ children }) {
    const { url, props } = usePage();
    const user = props.user || { name: 'Jayashree Patil', email: 'jayshree@serveravatar.com' };
    const organizations = props.organizations || [];
    const selectedOrg = props.selectedOrg;

    const [profileOpen, setProfileOpen] = useState(false);
    const [themeOpen, setThemeOpen] = useState(false);
    const [orgSwitcherOpen, setOrgSwitcherOpen] = useState(false);
    const profileRef = useRef(null);
    const themeRef = useRef(null);
    const orgSwitcherRef = useRef(null);

    const getInitials = (name) => {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    const mainNav = [
        {
            name: 'Dashboard',
            href: '/dashboard',
            icon: Gauge,
            current: url === '/dashboard',
        },
        {
            name: 'Organizations',
            href: '/organizations',
            icon: Building2,
            current: url.startsWith('/organizations'),
        },
        {
            name: 'Projects',
            href: '/all-projects',
            icon: FolderOpen,
            current: url === '/all-projects',
        },
    ];

    const bottomNav = [
        { name: 'Documentation', href: '/docs', icon: FileText },
        { name: 'Help & Support', href: '/support', icon: HelpCircle },
    ];

    useEffect(() => {
        function handleClickOutside(event) {
            if (profileRef.current && !profileRef.current.contains(event.target)) {
                setProfileOpen(false);
            }
            if (themeRef.current && !themeRef.current.contains(event.target)) {
                setThemeOpen(false);
            }
            if (orgSwitcherRef.current && !orgSwitcherRef.current.contains(event.target)) {
                setOrgSwitcherOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const setTheme = (theme) => {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('watchtower_theme', 'dark');
        } else if (theme === 'light') {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('watchtower_theme', 'light');
        } else {
            localStorage.removeItem('watchtower_theme');
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        setThemeOpen(false);
    };

    const currentTheme = () => {
        const stored = localStorage.getItem('watchtower_theme');
        if (stored === 'dark') return 'dark';
        if (stored === 'light') return 'light';
        return 'system';
    };

    const themeIcon = () => {
        const t = currentTheme();
        if (t === 'dark') return <Moon className="w-4 h-4" />;
        if (t === 'light') return <Sun className="w-4 h-4" />;
        return <Monitor className="w-4 h-4" />;
    };

    return (
        <div className="app-layout">
            {/* Top Header Bar */}
            <header className="top-header">
                {/* Left: Logo & Branding */}
                <Link href="/dashboard" className="header-logo">
                    <img
                        src="/logos/brand-logo.png"
                        alt="ServerAvatar Watchtower"
                        className="h-14 w-auto"
                    />
                    <div className="flex flex-col leading-tight -ml-2">
                        <span className="text-xl font-bold text-gray-900 dark:text-white">
                            Server<span className="text-cyan-600 dark:text-cyan-400">Avatar</span>
                        </span>
                        <div className="flex items-center gap-2">
                            <span className="h-px flex-1 bg-cyan-600 dark:bg-cyan-400"></span>
                            <span className="text-xs font-bold text-cyan-600 dark:text-cyan-400 tracking-widest">WATCHTOWER</span>
                            <span className="h-px flex-1 bg-cyan-600 dark:bg-cyan-400"></span>
                        </div>
                    </div>
                </Link>

                {/* Right: Theme + Notifications + Profile */}
                <div className="header-right">
                    {/* Theme Dropdown */}
                    <div className="dropdown" ref={themeRef}>
                        <button
                            onClick={() => setThemeOpen(!themeOpen)}
                            className="header-icon-btn"
                            title="Toggle theme"
                        >
                            {themeIcon()}
                        </button>
                        {themeOpen && (
                            <div className="dropdown-menu">
                                <button
                                    onClick={() => setTheme('light')}
                                    className={`dropdown-item ${currentTheme() === 'light' ? 'active' : ''}`}
                                >
                                    <Sun className="w-4 h-4" />
                                    <span>Light</span>
                                </button>
                                <button
                                    onClick={() => setTheme('dark')}
                                    className={`dropdown-item ${currentTheme() === 'dark' ? 'active' : ''}`}
                                >
                                    <Moon className="w-4 h-4" />
                                    <span>Dark</span>
                                </button>
                                <button
                                    onClick={() => setTheme('system')}
                                    className={`dropdown-item ${currentTheme() === 'system' ? 'active' : ''}`}
                                >
                                    <Monitor className="w-4 h-4" />
                                    <span>System</span>
                                </button>
                            </div>
                        )}
                    </div>

                    {/* Notifications */}
                    <button className="header-icon-btn" title="Notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="w-4 h-4"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    </button>

                    {/* Profile Dropdown */}
                    <div className="dropdown" ref={profileRef}>
                        <button
                            onClick={() => setProfileOpen(!profileOpen)}
                            className="profile-trigger"
                        >
                            <div className="profile-avatar">
                                <span>{getInitials(user.name)}</span>
                            </div>
                            <ChevronDown className={`w-4 h-4 profile-chevron ${profileOpen ? 'rotate-180' : ''}`} />
                        </button>
                        {profileOpen && (
                            <div className="dropdown-menu profile-dropdown">
                                <div className="dropdown-header">
                                    <p className="dropdown-user-name">{user.name}</p>
                                    <p className="dropdown-user-email">{user.email}</p>
                                </div>
                                <div className="dropdown-divider" />
                                <Link href="/settings/profile" className="dropdown-item">
                                    <User className="w-4 h-4" />
                                    <span>Profile</span>
                                </Link>
                                <Link href="/settings" className="dropdown-item">
                                    <Settings className="w-4 h-4" />
                                    <span>Settings</span>
                                </Link>
                                <div className="dropdown-divider" />
                                <form method="POST" action="/logout" className="dropdown-form">
                                    <input type="hidden" name="_token" />
                                    <button type="submit" className="dropdown-item dropdown-item-danger">
                                        <LogOut className="w-4 h-4" />
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        )}
                    </div>
                </div>
            </header>

            <div className="app-body">
                {/* Sidebar */}
                <aside className="sidebar">
                    {/* Main Navigation */}
                    <nav className="sidebar-nav">
                        {mainNav.map((item) => {
                            const Icon = item.icon;
                            return (
                                <Link
                                    key={item.name}
                                    href={item.href}
                                    className={`sidebar-nav-item ${item.current ? 'active' : ''}`}
                                >
                                    <Icon className="sidebar-nav-icon" />
                                    <span>{item.name}</span>
                                </Link>
                            );
                        })}
                    </nav>

                    {/* Bottom Navigation */}
                    <div className="sidebar-bottom">
                        {bottomNav.map((item) => {
                            const Icon = item.icon;
                            return (
                                <Link
                                    key={item.name}
                                    href={item.href}
                                    className="sidebar-nav-item"
                                >
                                    <Icon className="sidebar-nav-icon" />
                                    <span>{item.name}</span>
                                </Link>
                            );
                        })}
                    </div>

                    {/* User Profile */}
                    <div className="sidebar-footer">
                        <div className="sidebar-user">
                            <div className="sidebar-user-avatar">
                                <span>{getInitials(user.name)}</span>
                            </div>
                            <div className="sidebar-user-info">
                                <p className="sidebar-user-name">{user.name}</p>
                                <p className="sidebar-user-role">Administrator</p>
                            </div>
                        </div>
                    </div>
                </aside>

                {/* Main Content */}
                <main className="main-content">
                    {children}
                </main>
            </div>
        </div>
    );
}
