import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Key, Copy, CheckCircle, AlertTriangle, RefreshCw } from 'lucide-react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';

export default function Agent({ organization, project, agentToken, rawToken, justGenerated }) {
    const [copied, setCopied] = useState(false);
    const [showToken, setShowToken] = useState(justGenerated);

    const copyToClipboard = () => {
        navigator.clipboard.writeText(rawToken);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <AppLayout>
            <div className="p-8">
                {/* Header */}
                <div className="mb-8">
                    <Link
                        href={`/organizations/${organization.id}/projects/${project.id}`}
                        className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4"
                    >
                        <ArrowLeft className="w-4 h-4" />
                        Back to project
                    </Link>
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <Key className="w-6 h-6" />
                        Agent Token
                    </h1>
                </div>

                <div className="max-w-2xl space-y-6">
                    {/* Just Generated Alert */}
                    {justGenerated && (
                        <div className="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 flex items-start gap-3">
                            <CheckCircle className="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" />
                            <div>
                                <p className="text-sm font-medium text-green-800 dark:text-green-200">
                                    Token generated successfully!
                                </p>
                                <p className="text-sm text-green-700 dark:text-green-300 mt-1">
                                    Make sure to copy your token now. You won't be able to see it again.
                                </p>
                            </div>
                        </div>
                    )}

                    {/* Token Display */}
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Your Agent Token</h2>
                            {agentToken?.status && (
                                <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${
                                    agentToken.status === 'active' 
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                }`}>
                                    {agentToken.status}
                                </span>
                            )}
                        </div>

                        {rawToken ? (
                            <div className="space-y-4">
                                <div className="relative">
                                    <div className="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg font-mono text-sm break-all">
                                        {showToken ? rawToken : '•'.repeat(32) + '•'.repeat(16)}
                                    </div>
                                    <button
                                        onClick={() => setShowToken(!showToken)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                                    >
                                        {showToken ? 'Hide' : 'Show'}
                                    </button>
                                </div>

                                <div className="flex items-center gap-3">
                                    <button
                                        onClick={copyToClipboard}
                                        className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors"
                                    >
                                        {copied ? (
                                            <>
                                                <CheckCircle className="w-4 h-4" />
                                                Copied!
                                            </>
                                        ) : (
                                            <>
                                                <Copy className="w-4 h-4" />
                                                Copy Token
                                            </>
                                        )}
                                    </button>
                                </div>
                            </div>
                        ) : (
                            <div className="text-center py-8">
                                <Key className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                                <p className="text-gray-500 dark:text-gray-400 mb-4">
                                    No agent token generated yet.
                                </p>
                                <form method="POST" action={`/organizations/${organization.id}/projects/${project.id}/agent`}>
                                    <input type="hidden" name="_token" />
                                    <button
                                        type="submit"
                                        className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors"
                                    >
                                        <RefreshCw className="w-4 h-4" />
                                        Generate Token
                                    </button>
                                </form>
                            </div>
                        )}
                    </div>

                    {/* Instructions */}
                    <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Installation Instructions</h2>
                        <div className="space-y-4 text-sm">
                            <div>
                                <p className="font-medium text-gray-700 dark:text-gray-300 mb-2">1. Install the Watchtower Agent</p>
                                <code className="block bg-gray-100 dark:bg-gray-900 p-3 rounded-lg text-gray-800 dark:text-gray-200 font-mono">
                                    composer require serveravatar/watchtower-agent
                                </code>
                            </div>
                            <div>
                                <p className="font-medium text-gray-700 dark:text-gray-300 mb-2">2. Set your token in .env</p>
                                <code className="block bg-gray-100 dark:bg-gray-900 p-3 rounded-lg text-gray-800 dark:text-gray-200 font-mono">
                                    WATCHTOWER_TOKEN="{rawToken || 'your-token-here'}"
                                </code>
                            </div>
                            <div>
                                <p className="font-medium text-gray-700 dark:text-gray-300 mb-2">3. Configure the agent</p>
                                <code className="block bg-gray-100 dark:bg-gray-900 p-3 rounded-lg text-gray-800 dark:text-gray-200 font-mono">
                                    php artisan watchtower:install
                                </code>
                            </div>
                        </div>
                    </div>

                    {/* Security Notice */}
                    <div className="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                        <div>
                            <p className="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Keep your token secure
                            </p>
                            <p className="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                Never share your agent token publicly. It provides access to your project's monitoring data.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
