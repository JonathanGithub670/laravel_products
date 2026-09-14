import { Head } from '@inertiajs/react';
import AppLayout from '@/components/AppLayout';
import {
    DeleteButton,
    Pagination,
    SectionCard,
    TableShell,
} from '@/components/AppShell';
import { formatDate, formatDateTime, formatPercent } from '@/lib/format';

interface SalesDiscountRow {
    id: number;
    sale_id: number;
    product: {
        id: number;
        name: string;
    } | null;
    qty: number | null;
    sale_date: string | null;
    discount: number | null;
    start_date: string;
    end_date: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export default function SalesDiscountsIndex({
    salesDiscounts,
}: {
    salesDiscounts: { data: SalesDiscountRow[]; links: PaginationLink[] };
}) {
    return (
        <AppLayout>
            <Head title="Diskon Terpakai" />

            <div className="space-y-6">
                <h2 className="text-lg font-semibold text-gray-900">
                    Diskon Terpakai
                </h2>

                <SectionCard>
                    {salesDiscounts.data.length === 0 ? (
                        <p className="text-sm text-gray-500">
                            Belum ada data diskon terpakai.
                        </p>
                    ) : (
                        <>
                            <TableShell
                                headers={[
                                    'Produk',
                                    'Jumlah',
                                    'Tanggal Penjualan',
                                    'Diskon',
                                    'Berlaku Dari',
                                    'Berlaku Sampai',
                                    'Aksi',
                                ]}
                            >
                                {salesDiscounts.data.map((salesDiscount) => (
                                    <tr key={salesDiscount.id}>
                                        <td className="px-4 py-3 font-medium text-gray-900">
                                            {salesDiscount.product?.name ?? '-'}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {salesDiscount.qty ?? '-'}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {formatDateTime(
                                                salesDiscount.sale_date,
                                            )}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap">
                                            <span className="rounded-full bg-emerald-100 px-2 py-0.5 text-sm font-medium text-emerald-700">
                                                {formatPercent(
                                                    salesDiscount.discount,
                                                )}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {formatDate(
                                                salesDiscount.start_date,
                                            )}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {formatDate(salesDiscount.end_date)}
                                        </td>
                                        <td className="px-4 py-3">
                                            <DeleteButton
                                                action={`/sales-discounts/${salesDiscount.id}`}
                                            />
                                        </td>
                                    </tr>
                                ))}
                            </TableShell>

                            <Pagination links={salesDiscounts.links} />
                        </>
                    )}
                </SectionCard>
            </div>
        </AppLayout>
    );
}
