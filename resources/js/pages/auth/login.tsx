import { Form, Head } from '@inertiajs/react';

interface FieldErrors {
    username?: string;
    password?: string;
}

export default function Login({ errors }: { errors?: FieldErrors }) {
    return (
        <>
            <Head title="Login" />

            <div className="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12">
                <div className="w-full max-w-md">
                    <div className="rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
                        <h1 className="text-center text-2xl font-bold text-gray-900">
                            Mini POS
                        </h1>
                        <p className="mt-2 text-center text-sm text-gray-500">
                            Silakan masuk untuk melanjutkan
                        </p>

                        <Form
                            method="post"
                            action="/login"
                            className="mt-8 space-y-4"
                        >
                            {({ errors: formErrors, processing }) => (
                                <>
                                    <div>
                                        <label
                                            htmlFor="username"
                                            className="mb-1 block text-sm font-medium text-gray-700"
                                        >
                                            Username
                                        </label>
                                        <input
                                            id="username"
                                            type="text"
                                            name="username"
                                            autoFocus
                                            className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                                        />
                                        {formErrors.username && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {formErrors.username}
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <label
                                            htmlFor="password"
                                            className="mb-1 block text-sm font-medium text-gray-700"
                                        >
                                            Password
                                        </label>
                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                                        />
                                        {formErrors.password && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {formErrors.password}
                                            </p>
                                        )}
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-50"
                                    >
                                        {processing ? 'Memproses...' : 'Masuk'}
                                    </button>
                                </>
                            )}
                        </Form>
                    </div>
                </div>
            </div>
        </>
    );
}
