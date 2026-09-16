import { useState } from 'react';
import { Copy, Check } from 'lucide-react';

export default function CodeBlock({ children, className = '' }) {
    const [copied, setCopied] = useState(false);

    // Extract text content from children for clipboard
    const getTextContent = () => {
        if (typeof children === 'string') return children;
        if (Array.isArray(children)) {
            return children.map(child => {
                if (typeof child === 'string') return child;
                if (child?.props?.children) {
                    if (typeof child.props.children === 'string') return child.props.children;
                    if (Array.isArray(child.props.children)) {
                        return child.props.children.map(c => typeof c === 'string' ? c : c?.props?.children || '').join('\n');
                    }
                    return child.props.children;
                }
                return '';
            }).join('\n');
        }
        if (children?.props?.children) {
            if (typeof children.props.children === 'string') return children.props.children;
            if (Array.isArray(children.props.children)) {
                return children.props.children.map(c => typeof c === 'string' ? c : '').join('\n');
            }
        }
        return '';
    };

    const handleCopy = () => {
        const text = getTextContent();
        navigator.clipboard.writeText(text);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <div className={`bg-gray-900 dark:bg-slate-950 rounded-lg p-4 font-mono text-sm overflow-x-auto relative group ${className}`}>
            <button
                onClick={handleCopy}
                className="absolute right-3 top-3 p-2 text-gray-400 hover:text-white rounded-md hover:bg-gray-700 transition-colors opacity-0 group-hover:opacity-100"
                title="Copy to clipboard"
            >
                {copied ? (
                    <Check className="w-4 h-4 text-emerald-400" />
                ) : (
                    <Copy className="w-4 h-4" />
                )}
            </button>
            <div className="pr-12">
                {children}
            </div>
        </div>
    );
}
