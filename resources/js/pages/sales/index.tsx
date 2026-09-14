import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/components/AppLayout';
import {
    DeleteButton,
    Pagination,
    SectionCard,
    TableShell,
} from '@/components/AppShell';
import { formatCurrency, formatDateTime, formatPercent } from '@/lib/format';

interface ProductOption {
    id: number;
    name: string;
}

interface SaleRow {
    id: number;
    product_id: number;
    qty: number;
    date: string;
    discount_percent: number | null;
    total: number;
    product: {
        id: number;
        name: string;
    };
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

function SaleForm({
    products,
    sale,
    onCancel,
}: {
    products: ProductOption[];
    sale?: SaleRow;
    onCancel: () => void;
}) {
    const isEdit = sale !== undefined;

    return (
        <Form
            method={isEdit ? 'put' : 'post'}
            action={isEdit ? `/sales/${sale.id}` : '/sales'}
            className="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4"
        >
            {({ errors, processing }) => (
                <>
                    <div className="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label
                                htmlFor="product_id"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Produk
                            </label>
                            <select
                                id="product_id"
                                name="product_id"
                                defaultValue={sale?.product_id ?? ''}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            >
                                <option value="" disabled>
                                    Pilih produk
                                </option>
                                {products.map((product) => (
                                    <option key={product.id} value={product.id}>
                                        {product.name}
                                    </option>
                                ))}
                            </select>
                            {errors.product_id && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.product_id}
                                </p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="qty"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Jumlah
                            </label>
                            <input
                                id="qty"
                                type="number"
                                name="qty"
                                min={1}
                                defaultValue={sale?.qty ?? ''}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.qty && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.qty}
                                </p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="sale_date"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Tanggal Pembelian
                            </label>
                            <input
                                id="sale_date"
                                type="date"
                                name="sale_date"
                                defaultValue={
                                    sale
                                        ? String(sale.date).slice(0, 10)
                                        : new Date().toISOString().slice(0, 10)
                                }
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.sale_date && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.sale_date}
                                </p>
                            )}
                        </div>
                    </div>

                    <p className="text-sm text-gray-500">
                        Diskon yang berlaku pada tanggal pembelian akan
                        diterapkan otomatis.
                    </p>

                    <div className="flex justify-end gap-2">
                        <button
                            type="button"
                            onClick={onCancel}
                            className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-50"
                        >
                            {processing
                                ? 'Menyimpan...'
                                : isEdit
                                  ? 'Simpan Perubahan'
                                  : 'Tambah Penjualan'}
                        </button>
                    </div>
                </>
            )}
        </Form>
    );
}

export default function SalesIndex({
    sales,
    products,
}: {
    sales: { data: SaleRow[]; links: PaginationLink[] };
    products: ProductOption[];
}) {
    const [showCreate, setShowCreate] = useState(false);
    const [editingId, setEditingId] = useState<number | null>(null);

    const editingSale = sales.data.find((sale) => sale.id === editingId);

    return (
        <AppLayout>
            <Head title="Penjualan" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-900">
                        Penjualan
                    </h2>
                    <button
                        type="button"
                        onClick={() => setShowCreate((value) => !value)}
                        className="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
                    >
                        {showCreate ? 'Tutup Form' : 'Tambah Penjualan'}
                    </button>
                </div>

                {showCreate && (
                    <SaleForm
                        products={products}
                        onCancel={() => setShowCreate(false)}
                    />
                )}

                <SectionCard>
                    {sales.data.length === 0 ? (
                        <p className="text-sm text-gray-500">
                            Belum ada data penjualan.
                        </p>
                    ) : (
                        <>
                            <TableShell
                                headers={[
                                    'Produk',
                                    'Jumlah',
                                    'Tanggal',
                                    'Diskon',
                                    'Total',
                                    'Aksi',
                                ]}
                            >
                                {sales.data.map((sale) =>
                                    editingId === sale.id ? (
                                        <tr key={sale.id}>
                                            <td
                                                colSpan={6}
                                                className="px-4 py-3"
                                            >
                                                <SaleForm
                                                    products={products}
                                                    sale={editingSale}
                                                    onCancel={() =>
                                                        setEditingId(null)
                                                    }
                                                />
                                            </td>
                                        </tr>
                                    ) : (
                                        <tr key={sale.id}>
                                            <td className="px-4 py-3 font-medium text-gray-900">
                                                {sale.product.name}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {sale.qty}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {formatDateTime(sale.date)}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap">
                                                {sale.discount_percent !==
                                                null ? (
                                                    <span className="rounded-full bg-emerald-100 px-2 py-0.5 text-sm font-medium text-emerald-700">
                                                        {formatPercent(
                                                            sale.discount_percent,
                                                        )}
                                                    </span>
                                                ) : (
                                                    <span className="text-gray-400">
                                                        -
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-900">
                                                {formatCurrency(sale.total)}
                                            </td>
                                            <td className="flex gap-2 px-4 py-3">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        setEditingId(sale.id)
                                                    }
                                                    className="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                >
                                                    Edit
                                                </button>
                                                <DeleteButton
                                                    action={`/sales/${sale.id}`}
                                                />
                                            </td>
                                        </tr>
                                    ),
                                )}
                            </TableShell>

                            <Pagination links={sales.links} />
                        </>
                    )}
                </SectionCard>
            </div>
        </AppLayout>
    );
}
