import { Head } from '@inertiajs/react';
import AppLayout from '@/components/AppLayout';
import { QueryFilterForm, SectionCard } from '@/components/AppShell';
import { formatCurrency, formatDateTime, formatPercent } from '@/lib/format';

interface LatestSale {
    id: number;
    product_name: string;
    qty: number;
    date: string;
    total: number;
    discount_percent: number | null;
}

type Filters = {
    [key: string]: string;
};

export default function Dashboard({
    latestSales,
    totalSales,
    filters,
}: {
    latestSales: LatestSale[];
    totalSales: number;
    filters: Filters;
}) {
    return (
        <AppLayout>
            <Head title="Dashboard" />

            <div className="space-y-6">
                <SectionCard>
                    <h2 className="text-base font-semibold text-gray-900">
                        Total Penjualan (Omset)
                    </h2>

                    <div className="mt-4">
                        <QueryFilterForm params={filters} />
                    </div>

                    <p className="mt-4 text-3xl font-bold text-gray-900">
                        {formatCurrency(totalSales)}
                    </p>
                    <p className="mt-1 text-sm text-gray-500">
                        Periode {filters.start_date} s.d. {filters.end_date}
                    </p>
                </SectionCard>

                <SectionCard>
                    <h2 className="text-base font-semibold text-gray-900">
                        Penjualan Terbaru
                    </h2>

                    {latestSales.length === 0 ? (
                        <p className="mt-4 text-sm text-gray-500">
                            Belum ada penjualan pada periode ini.
                        </p>
                    ) : (
                        <ul className="mt-4 divide-y divide-gray-100">
                            {latestSales.map((sale) => (
                                <li
                                    key={sale.id}
                                    className="flex items-center justify-between py-3"
                                >
                                    <div>
                                        <p className="font-medium text-gray-900">
                                            {sale.product_name}
                                        </p>
                                        <p className="text-sm text-gray-500">
                                            {sale.qty} pcs &middot;{' '}
                                            {formatDateTime(sale.date)}
                                        </p>
                                    </div>
                                    <div className="text-right">
                                        <p className="font-medium text-gray-900">
                                            {formatCurrency(sale.total)}
                                        </p>
                                        {sale.discount_percent !== null && (
                                            <p className="text-sm text-emerald-600">
                                                Diskon{' '}
                                                {formatPercent(
                                                    sale.discount_percent,
                                                )}
                                            </p>
                                        )}
                                    </div>
                                </li>
                            ))}
                        </ul>
                    )}
                </SectionCard>
            </div>
        </AppLayout>
    );
}
