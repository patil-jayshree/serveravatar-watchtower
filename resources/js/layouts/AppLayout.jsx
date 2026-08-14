import { Link, usePage } from '@inertiajs/react';
import { ReactElement } from 'react';
import {
    LayoutDashboard,
    Building2,
    FileText,
    HelpCircle,
    User,
    LogOut,
    Sun,
    Moon,
} from 'lucide-react';
import './app-layout.css';

export default function AppLayout({ children }) {
    const { url, props } = usePage();
    const user = props.user || { name: 'User', email: 'user@example.com' };

    const navigation = [
        { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard, current: url === '/dashboard' },
        { name: 'Organizations', href: '/organizations', icon: Building2, current: url.startsWith('/organizations') },
        { name: 'Documentation', href: '/docs', icon: FileText, current: url.startsWith('/docs') },
        { name: 'Help & Support', href: '/support', icon: HelpCircle, current: url.startsWith('/support') },
    ];

    return (
        <div className="app-layout">
            {/* Sidebar */}
            <aside className="sidebar">
                {/* Logo */}
                <div className="sidebar-logo">
                    <Link href="/dashboard">
                        <img
                            src="/logos/brand-logo.png"
                            alt="Watchtower"
                            className="h-8 w-auto"
                            data-logo-light="/logos/brand-logo.png"
                            data-logo-dark="/logos/brand-logo.png"
                        />
                    </Link>
                </div>

                {/* Navigation */}
                <nav className="sidebar-nav">
                    {navigation.map((item) => {
                        const Icon = item.icon;
                        return (
                            <Link
                                key={item.name}
                                href={item.href}
                                className={`nav-item ${item.current ? 'active' : ''}`}
                            >
                                <Icon className="nav-icon" />
                                <span>{item.name}</span>
                            </Link>
                        );
                    })}
                </nav>

                {/* User Profile at bottom */}
                <div className="sidebar-footer">
                    <div className="user-profile">
                        <div className="user-avatar">
                            {user.avatar_url ? (
                                <img src={user.avatar_url} alt={user.name} className="w-5 h-5 rounded-full object-cover" />
                            ) : (
                                <User className="w-5 h-5" />
                            )}
                        </div>
                        <div className="user-info">
                            <p className="user-name">{user.name}</p>
                            <p className="user-email">{user.email}</p>
                        </div>
                    </div>
                    <form method="POST" action="/logout">
                        <input type="hidden" name="_token" />
                        <button type="submit" className="logout-btn" title="Logout">
                            <LogOut className="w-5 h-5" />
                        </button>
                    </form>
                </div>
            </aside>

            {/* Main Content */}
            <main className="main-content">
                {children}
            </main>
        </div>
    );
}
