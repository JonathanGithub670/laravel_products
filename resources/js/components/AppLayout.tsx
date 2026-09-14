import type { PageProps } from '@inertiajs/core';
import { Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

interface User {
    id: number;
    username: string;
    name: string;
}

interface SharedProps extends PageProps {
    auth: {
        user: User | null;
    };
    flash: {
        success?: string | null;
        error?: string | null;
    };
}

const navItems = [
    { label: 'Dashboard', href: '/dashboard', activePattern: /^\/dashboard/ },
    { label: 'Produk', href: '/products', activePattern: /^\/products/ },
    { label: 'Pengadaan Stok', href: '/stocks', activePattern: /^\/stocks/ },
    { label: 'Penjualan', href: '/sales', activePattern: /^\/sales(?=-|$)/ },
    { label: 'Diskon', href: '/discounts', activePattern: /^\/discounts/ },
    {
        label: 'Diskon Terpakai',
        href: '/sales-discounts',
        activePattern: /^\/sales-discounts/,
    },
];

export default function AppLayout({ children }: { children: ReactNode }) {
    const { auth, flash } = usePage<SharedProps>().props;
    const { url } = usePage<SharedProps>();

    return (
        <div className="min-h-screen bg-gray-50">
            <header className="border-b border-gray-200 bg-white">
                <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <div className="flex items-center gap-8">
                        <h1 className="text-lg font-semibold text-gray-900">
                            Mini POS
                        </h1>
                        <nav className="hidden items-center gap-1 md:flex">
                            {navItems.map((item) => (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    className={cn(
                                        'rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                                        item.activePattern.test(url) &&
                                            'bg-gray-100 text-gray-900',
                                    )}
                                >
                                    {item.label}
                                </Link>
                            ))}
                        </nav>
                    </div>

                    <div className="flex items-center gap-4">
                        <span className="text-sm text-gray-600">
                            {auth.user?.username}
                        </span>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            className="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700"
                        >
                            Logout
                        </Link>
                    </div>
                </div>

                <nav className="flex gap-1 overflow-x-auto px-4 pb-2 md:hidden">
                    {navItems.map((item) => (
                        <Link
                            key={item.href}
                            href={item.href}
                            className={cn(
                                'rounded-md px-3 py-2 text-sm font-medium whitespace-nowrap text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                                item.activePattern.test(url) &&
                                    'bg-gray-100 text-gray-900',
                            )}
                        >
                            {item.label}
                        </Link>
                    ))}
                </nav>
            </header>

            <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                {flash.success && (
                    <div className="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {flash.success}
                    </div>
                )}

                {flash.error && (
                    <div className="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {flash.error}
                    </div>
                )}

                {children}
            </main>
        </div>
    );
}
