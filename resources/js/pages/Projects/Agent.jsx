import AppLayout from '@/layouts/AppLayout';
import { Terminal, Copy, Check } from 'lucide-react';
import { useState } from 'react';

export default function Agent() {
    const [copied, setCopied] = useState(false);
    const command = 'composer require serveravatar/watchtower-agent';
    const copyToClipboard = () => {
        navigator.clipboard.writeText(command);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };
    return (
        <AppLayout>
            <div className="p-8">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-8">Agent Installation</h1>
                <div className="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-8 max-w-2xl">
                    <h3 className="font-semibold text-gray-900 dark:text-white mb-4">Install via Composer</h3>
                    <div className="flex items-center gap-2 bg-slate-900 dark:bg-slate-950 rounded-lg p-4">
                        <Terminal className="w-5 h-5 text-gray-400 flex-shrink-0" />
                        <code className="text-cyan-400 font-mono text-sm flex-1">{command}</code>
                        <button onClick={copyToClipboard} className="p-2 hover:bg-slate-800 rounded transition-colors">
                            {copied ? <Check className="w-5 h-5 text-green-400" /> : <Copy className="w-5 h-5 text-gray-400" />}
                        </button>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
