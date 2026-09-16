import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import ConfirmModal from '@/components/ConfirmModal';
import CodeBlock from '@/components/CodeBlock';
import { 
    Package, Terminal, CheckCircle2, Key, Rocket, 
    AlertTriangle, Copy, Check, RefreshCw, Shield
} from 'lucide-react';

export default function ProjectAgent() {
    const { project, organization, agentToken: initialToken, isConnected, justGenerated: initialJustGenerated, rawToken: initialRawToken } = usePage().props;
    
    // Use local state for token — updated via fetch (no page reload)
    const [agentToken, setAgentToken] = useState(initialToken);
    const [justGenerated, setJustGenerated] = useState(initialJustGenerated);
    const [rawToken, setRawToken] = useState(initialRawToken);
    const [copied, setCopied] = useState(null);
    const [loading, setLoading] = useState(false);

    // Confirmation modal state
    const [showModal, setShowModal] = useState(false);
    const [modalAction, setModalAction] = useState(null); // 'regenerate' | 'revoke'

    const copyToClipboard = (text, key) => {
        navigator.clipboard.writeText(text);
        setCopied(key);
        setTimeout(() => setCopied(null), 2000);
    };

    const handleGenerate = async () => {
        setLoading(true);
        try {
            const res = await fetch(`/organizations/${organization?.id}/projects/${project?.uuid}/agent/generate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();
            setRawToken(data.token);
            setAgentToken(data.token);
            setJustGenerated(true);
        } catch (err) {
            alert('Failed to generate token');
        } finally {
            setLoading(false);
        }
    };

    const openRegenerateModal = () => {
        setModalAction('regenerate');
        setShowModal(true);
    };

    const openRevokeModal = () => {
        setModalAction('revoke');
        setShowModal(true);
    };

    const handleModalConfirm = async () => {
        setShowModal(false);
        setLoading(true);
        try {
            if (modalAction === 'regenerate') {
                const res = await fetch(`/organizations/${organization?.id}/projects/${project?.uuid}/agent/regenerate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                setRawToken(data.token);
                setAgentToken(data.token);
                setJustGenerated(true);
            } else if (modalAction === 'revoke') {
                await fetch(`/organizations/${organization?.id}/projects/${project?.uuid}/agent`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json',
                    },
                });
                setAgentToken(null);
                setRawToken(null);
                setJustGenerated(false);
            }
        } catch (err) {
            alert('Failed to process request');
        } finally {
            setLoading(false);
            setModalAction(null);
        }
    };

    const steps = [
        { num: '1', title: 'Install Agent Package', icon: Package },
        { num: '2', title: 'Run Install Command', icon: Terminal },
        { num: '3', title: 'Verify Connection', icon: CheckCircle2 },
        { num: '4', title: 'Get Token', icon: Key },
        { num: '5', title: 'Start Agent', icon: Rocket },
    ];

    const currentStep = isConnected ? 5 : 0;

    return (
        <AppLayout>
            <div className="min-h-full bg-gray-50 dark:bg-slate-900">
                <div className="max-w-4xl mx-auto px-8 py-8">
                    {/* Breadcrumb */}
                    <div className="mb-6">
                        <div className="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                            <Link href="/organizations" className="hover:text-cyan-600 dark:hover:text-cyan-400">
                                Organizations
                            </Link>
                            <span>/</span>
                            <Link href={`/organizations/${organization?.id}`} className="hover:text-cyan-600 dark:hover:text-cyan-400">
                                {organization?.name}
                            </Link>
                            <span>/</span>
                            <Link href={`/organizations/${organization?.id}/projects/${project?.uuid}`} className="hover:text-cyan-600 dark:hover:text-cyan-400">
                                {project?.name}
                            </Link>
                            <span>/</span>
                            <span className="text-gray-900 dark:text-white">Agent</span>
                        </div>
                    </div>

                    {/* Header */}
                    <div className="flex items-center justify-between mb-8">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                Laravel Agent Setup
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                Connect your Laravel application to Watchtower
                            </p>
                        </div>
                        
                        {/* Status Badge */}
                        <div className={`inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium ${
                            isConnected 
                                ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                        }`}>
                            {isConnected ? (
                                <>
                                    <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Agent Connected
                                </>
                            ) : (
                                <>
                                    <span className="w-2 h-2 rounded-full bg-red-500"></span>
                                    Agent Not Connected
                                </>
                            )}
                        </div>
                    </div>

                    {/* Steps Progress Bar */}
                    <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 mb-6">
                        <div className="flex items-center justify-between">
                            {steps.map((step, index) => {
                                const Icon = step.icon;
                                const isActive = index === currentStep;
                                const isCompleted = index < currentStep;
                                
                                return (
                                    <div key={step.num} className="flex items-center">
                                        <div className="flex flex-col items-center">
                                            <div className={`w-10 h-10 rounded-full flex items-center justify-center mb-2 ${
                                                isCompleted 
                                                    ? 'bg-emerald-100 dark:bg-emerald-900/40'
                                                    : isActive
                                                        ? 'bg-cyan-100 dark:bg-cyan-900/40'
                                                        : 'bg-gray-100 dark:bg-slate-700'
                                            }`}>
                                                {isCompleted ? (
                                                    <Check className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                                ) : (
                                                    <Icon className={`w-5 h-5 ${
                                                        isActive 
                                                            ? 'text-cyan-600 dark:text-cyan-400'
                                                            : 'text-gray-400 dark:text-gray-500'
                                                    }`} />
                                                )}
                                            </div>
                                            <span className={`text-xs font-medium text-center max-w-[80px] ${
                                                isActive || isCompleted
                                                    ? 'text-gray-900 dark:text-white'
                                                    : 'text-gray-400 dark:text-gray-500'
                                            }`}>
                                                {step.title}
                                            </span>
                                        </div>
                                        {index < steps.length - 1 && (
                                            <div className={`w-16 h-0.5 mx-2 mb-6 ${
                                                isCompleted 
                                                    ? 'bg-emerald-400'
                                                    : 'bg-gray-200 dark:bg-gray-700'
                                            }`} />
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Steps Content */}
                    <div className="space-y-6">
                        {/* Step 1: Install Agent Package */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start gap-4 mb-4">
                                <div className="w-8 h-8 bg-cyan-100 dark:bg-cyan-900/40 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span className="text-sm font-semibold text-cyan-600 dark:text-cyan-400">1</span>
                                </div>
                                <div>
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">Install Agent Package</h2>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Add the Watchtower agent to your Laravel application</p>
                                </div>
                            </div>

                            {/* Development */}
                            <div className="mb-6">
                                <div className="flex items-center gap-2 mb-3">
                                    <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                        Development
                                    </span>
                                </div>
                                <p className="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                    For local development, configure a path repository in your composer.json:
                                </p>
                                <CodeBlock>
                                    <p className="text-gray-400 mb-2">// Add to your composer.json:</p>
                                    <p className="text-green-400">"repositories": [</p>
                                    <p className="text-green-400 pl-4">{'{'}&quot;type&quot;: &quot;path&quot;, &quot;url&quot;: &quot;../sa-watchtower-agent&quot;, &quot;options&quot;: {'{'}&quot;symlink&quot;: true{'}'}{'}'}</p>
                                    <p className="text-green-400">]</p>
                                </CodeBlock>
                                <CodeBlock className="mt-3">
                                    <p className="text-gray-400 mb-2">// Then run:</p>
                                    <p className="text-green-400">composer require serveravatar/watchtower-agent:*@dev --ignore-platform-req=ext-bcmath</p>
                                </CodeBlock>
                            </div>

                            {/* Production */}
                            <div>
                                <div className="flex items-center gap-2 mb-3">
                                    <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                        Production
                                    </span>
                                </div>
                                <CodeBlock>
                                    <p className="text-green-400">composer require serveravatar/watchtower-agent --ignore-platform-req=ext-bcmath</p>
                                </CodeBlock>
                            </div>
                        </div>

                        {/* Step 2: Run Install Command */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start gap-4 mb-4">
                                <div className="w-8 h-8 bg-cyan-100 dark:bg-cyan-900/40 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span className="text-sm font-semibold text-cyan-600 dark:text-cyan-400">2</span>
                                </div>
                                <div>
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">Run Install Command</h2>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        The <code className="px-1 py-0.5 bg-gray-100 dark:bg-slate-700 rounded text-xs">watchtower:install</code> command auto-configures everything
                                    </p>
                                </div>
                            </div>

                            <CodeBlock className="mb-3">
                                <p className="text-gray-400 mb-2">// Interactive:</p>
                                <p className="text-green-400">php artisan watchtower:install</p>
                            </CodeBlock>

                            <CodeBlock className="mb-4">
                                <p className="text-gray-400 mb-2">// Or with URL and token:</p>
                                <p className="text-green-400">php artisan watchtower:install \</p>
                                <p className="text-green-400 pl-4">--url=&quot;YOUR_APP_URL&quot; \</p>
                                <p className="text-green-400 pl-4">--token=&quot;your-agent-token&quot; \</p>
                                <p className="text-green-400 pl-4">--no-interaction</p>
                            </CodeBlock>

                            <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div className="flex items-start gap-3">
                                    <Shield className="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p className="text-sm font-medium text-blue-800 dark:text-blue-200">What it does:</p>
                                        <ul className="text-sm text-blue-700 dark:text-blue-300 mt-1 space-y-1">
                                            <li>• Validates your agent token</li>
                                            <li>• Auto-configures all <code className="px-1 py-0.5 bg-blue-100 dark:bg-blue-800 rounded text-xs">WATCHTOWER_*</code> environment variables</li>
                                            <li>• Enables all monitoring features by default</li>
                                            <li>• Never overwrites existing values (idempotent)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Step 3: Verify Connection */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start gap-4 mb-4">
                                <div className="w-8 h-8 bg-cyan-100 dark:bg-cyan-900/40 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span className="text-sm font-semibold text-cyan-600 dark:text-cyan-400">3</span>
                                </div>
                                <div>
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">Verify Connection</h2>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Check agent status and all monitoring features</p>
                                </div>
                            </div>

                            <CodeBlock>
                                <p className="text-green-400">php artisan watchtower:status</p>
                            </CodeBlock>

                            <div className="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">What it shows:</h3>
                                <div className="grid grid-cols-2 gap-2 text-sm">
                                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <span className="w-2 h-2 rounded-full bg-green-500"></span> Connection status
                                    </div>
                                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <span className="w-2 h-2 rounded-full bg-green-500"></span> Project details
                                    </div>
                                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <span className="w-2 h-2 rounded-full bg-green-500"></span> HTTP Request monitoring
                                    </div>
                                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <span className="w-2 h-2 rounded-full bg-green-500"></span> Exception monitoring
                                    </div>
                                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <span className="w-2 h-2 rounded-full bg-green-500"></span> SQL Query monitoring
                                    </div>
                                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <span className="w-2 h-2 rounded-full bg-green-500"></span> Command monitoring
                                    </div>
                                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <span className="w-2 h-2 rounded-full bg-green-500"></span> Job monitoring
                                    </div>
                                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <span className="w-2 h-2 rounded-full bg-green-500"></span> Log monitoring
                                    </div>
                                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <span className="w-2 h-2 rounded-full bg-green-500"></span> Scheduler monitoring
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Step 4: Get Token */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start gap-4 mb-4">
                                <div className="w-8 h-8 bg-cyan-100 dark:bg-cyan-900/40 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span className="text-sm font-semibold text-cyan-600 dark:text-cyan-400">4</span>
                                </div>
                                <div>
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">Get Agent Token</h2>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Generate a token to authenticate your agent</p>
                                </div>
                            </div>

                            {!agentToken ? (
                                <button
                                    onClick={handleGenerate}
                                    disabled={loading}
                                    className="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
                                >
                                    <Key className="w-4 h-4" />
                                    {loading ? 'Generating...' : 'Generate Token'}
                                </button>
                            ) : (
                                <div className="space-y-4">
                                    <div className="bg-gray-900 dark:bg-slate-950 rounded-lg p-4 pr-12 font-mono text-sm overflow-hidden relative">
                                        <p className="text-green-400 break-all pr-8">{agentToken}</p>
                                        <button
                                            onClick={() => copyToClipboard(agentToken, 'token')}
                                            className="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-white rounded-md hover:bg-gray-700 transition-colors"
                                            title="Copy token"
                                        >
                                            {copied === 'token' ? (
                                                <Check className="w-4 h-4 text-emerald-400" />
                                            ) : (
                                                <Copy className="w-4 h-4" />
                                            )}
                                        </button>
                                    </div>

                                    <div className="flex gap-3">
                                        <button
                                            onClick={openRegenerateModal}
                                            disabled={loading}
                                            className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 disabled:opacity-50 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors"
                                        >
                                            <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
                                            Regenerate Token
                                        </button>
                                        <button
                                            onClick={openRevokeModal}
                                            disabled={loading}
                                            className="inline-flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 disabled:opacity-50 text-red-700 dark:text-red-300 text-sm font-medium rounded-lg transition-colors"
                                        >
                                            Revoke Token
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Step 5: Start Agent */}
                        <div className="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6">
                            <div className="flex items-start gap-4 mb-4">
                                <div className="w-8 h-8 bg-cyan-100 dark:bg-cyan-900/40 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span className="text-sm font-semibold text-cyan-600 dark:text-cyan-400">5</span>
                                </div>
                                <div>
                                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">Start Agent</h2>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">Run the agent to start monitoring your application</p>
                                </div>
                            </div>

                            <CodeBlock>
                                <p className="text-green-400">php artisan watchtower:run</p>
                            </CodeBlock>

                            <div className="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                <div className="flex items-start gap-3">
                                    <AlertTriangle className="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p className="text-sm font-medium text-amber-800 dark:text-amber-200">For Production:</p>
                                        <p className="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                            Run with Supervisor or systemd to keep it running in the background.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Confirmation Modal */}
            <ConfirmModal
                isOpen={showModal}
                onClose={() => { setShowModal(false); setModalAction(null); }}
                onConfirm={handleModalConfirm}
                title={modalAction === 'regenerate' ? 'Regenerate Token?' : 'Revoke Token?'}
                message={
                    modalAction === 'regenerate'
                        ? 'This will revoke the current token and generate a new one. Your agent will need to be updated with the new token.'
                        : 'This will disconnect the current agent and revoke the token. You will need to generate a new token to reconnect.'
                }
                confirmText={modalAction === 'regenerate' ? 'Regenerate' : 'Revoke'}
                confirmClass={modalAction === 'regenerate' ? 'bg-cyan-600 hover:bg-cyan-700' : 'bg-red-600 hover:bg-red-700'}
                isLoading={loading}
            />
        </AppLayout>
    );
}
