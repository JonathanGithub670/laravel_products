import { Form, router, usePage } from '@inertiajs/react';
import type { FormEvent, ReactNode } from 'react';
import { cn } from '@/lib/utils';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

function decodeHtmlLabel(label: string): string {
    return label
        .replace(/&laquo;/g, '«')
        .replace(/&raquo;/g, '»')
        .replace(/&nbsp;/g, ' ');
}

export function Pagination({ links }: { links?: PaginationLink[] }) {
    if (!links || links.length <= 3) {
        return null;
    }

    return (
        <div className="mt-4 flex flex-wrap items-center gap-2">
            {links.map((link) => (
                <button
                    key={link.label}
                    type="button"
                    onClick={() => {
                        if (link.url) {
                            router.visit(link.url, { preserveScroll: true });
                        }
                    }}
                    disabled={!link.url}
                    className={cn(
                        'rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50',
                        link.active &&
                            'bg-gray-900 text-white hover:bg-gray-900',
                    )}
                >
                    {decodeHtmlLabel(link.label)}
                </button>
            ))}
        </div>
    );
}

export function QueryFilterForm({
    params,
}: {
    params: Record<string, string | null>;
}) {
    const { url } = usePage();

    function applyFilter(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        const formData = new FormData(event.currentTarget);
        const query = new URLSearchParams();

        formData.forEach((value, key) => {
            if (typeof value === 'string' && value) {
                query.set(key, value);
            }
        });

        router.visit(`${url.split('?')[0]}?${query.toString()}`, {
            preserveScroll: true,
        });
    }

    return (
        <form onSubmit={applyFilter} className="flex flex-wrap items-end gap-3">
            {Object.entries(params).map(([key, defaultValue]) => (
                <label key={key} className="block">
                    <span className="mb-1 block text-sm font-medium text-gray-700">
                        {key
                            .split('_')
                            .map(
                                (part) =>
                                    part.charAt(0).toUpperCase() +
                                    part.slice(1),
                            )
                            .join(' ')}
                    </span>
                    <input
                        type="date"
                        name={key}
                        defaultValue={defaultValue ? String(defaultValue) : ''}
                        className="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                    />
                </label>
            ))}
            <button
                type="submit"
                className="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
            >
                Terapkan
            </button>
        </form>
    );
}

export function DeleteButton({
    action,
    label = 'Apakah Anda yakin ingin menghapus data ini?',
    children = 'Hapus',
}: {
    action: string;
    label?: string;
    children?: ReactNode;
}) {
    return (
        <Form method="delete" action={action} className="inline-block">
            <button
                type="submit"
                onClick={(event) => {
                    if (!window.confirm(label)) {
                        event.preventDefault();
                    }
                }}
                className="rounded-md border border-red-300 bg-white px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-50"
            >
                {children}
            </button>
        </Form>
    );
}

export function SectionCard({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <div
            className={cn(
                'rounded-lg border border-gray-200 bg-white p-6 shadow-sm',
                className,
            )}
        >
            {children}
        </div>
    );
}

export function TableShell({
    headers,
    children,
}: {
    headers: string[];
    children: ReactNode;
}) {
    return (
        <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200 text-sm">
                <thead className="bg-gray-50">
                    <tr>
                        {headers.map((header) => (
                            <th
                                key={header}
                                scope="col"
                                className="px-4 py-3 text-left font-semibold whitespace-nowrap text-gray-700"
                            >
                                {header}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-200 bg-white">
                    {children}
                </tbody>
            </table>
        </div>
    );
}
