import { Link, useForm } from '@inertiajs/react';
import { CheckCircle, Mail } from 'lucide-react';
import AuthLayout from '@/layouts/AuthLayout';

export default function VerifyEmail({ user, status }) {
    const { post, processing } = useForm({});

    const resend = (e) => {
        e.preventDefault();
        post('/email/verification-notification');
    };

    return (
        <AuthLayout title="Verify email">
            {/* Header */}
            <div className="text-center mb-8">
                <div className="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4">
                    <Mail className="w-8 h-8 text-primary" />
                </div>
                <h1 className="text-3xl font-bold text-gray-900 dark:text-white mb-2">Verify your email</h1>
                <p className="text-gray-600 dark:text-gray-400">
                    We've sent a verification link to <span className="font-medium">{user?.email}</span>
                </p>
            </div>

            {/* Success Alert */}
            {status && (
                <div className="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div className="flex items-center gap-3">
                        <CheckCircle className="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
                        <p className="text-sm text-green-800 dark:text-green-200">{status}</p>
                    </div>
                </div>
            )}

            {/* Info Card */}
            <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                <p className="text-sm text-gray-600 dark:text-gray-400 text-center">
                    Click the verification link in your email to verify your account. If you didn't receive the email, we can send another one.
                </p>
            </div>

            {/* Resend Form */}
            <form onSubmit={resend} className="space-y-4">
                <button
                    type="submit"
                    disabled={processing}
                    className="w-full py-3 px-4 rounded-lg font-semibold text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-900 transition-colors duration-200 disabled:opacity-50"
                >
                    {processing ? 'Sending...' : 'Resend verification email'}
                </button>
            </form>

            {/* Back to Dashboard */}
            <div className="mt-8 text-center">
                <Link
                    href="/dashboard"
                    className="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
                >
                    Go to dashboard
                </Link>
            </div>
        </AuthLayout>
    );
}
