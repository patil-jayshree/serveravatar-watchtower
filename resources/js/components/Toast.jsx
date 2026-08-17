import { useEffect, useState } from 'react';
import { CheckCircle, XCircle, AlertTriangle, X } from 'lucide-react';

const icons = {
    success: CheckCircle,
    error: XCircle,
    warning: AlertTriangle,
};

const bgColors = {
    success: 'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-200 dark:border-emerald-800',
    error: 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800',
    warning: 'bg-amber-50 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800',
};

const textColors = {
    success: 'text-emerald-800 dark:text-emerald-300',
    error: 'text-red-800 dark:text-red-300',
    warning: 'text-amber-800 dark:text-amber-300',
};

const iconColors = {
    success: 'text-emerald-500 dark:text-emerald-400',
    error: 'text-red-500 dark:text-red-400',
    warning: 'text-amber-500 dark:text-amber-400',
};

export default function Toast({ message, type = 'success', onClose, duration = 4000 }) {
    const Icon = icons[type] || icons.success;

    useEffect(() => {
        const timer = setTimeout(() => {
            onClose();
        }, duration);

        return () => clearTimeout(timer);
    }, [duration, onClose]);

    return (
        <div className="fixed bottom-6 right-6 z-[100] animate-slide-up">
            <div className={`flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg ${bgColors[type]}`}>
                <Icon className={`w-5 h-5 flex-shrink-0 ${iconColors[type]}`} />
                <p className={`text-sm font-medium ${textColors[type]}`}>{message}</p>
                <button
                    onClick={onClose}
                    className={`ml-2 p-1 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 transition-colors ${textColors[type]}`}
                >
                    <X className="w-4 h-4" />
                </button>
            </div>
        </div>
    );
}
