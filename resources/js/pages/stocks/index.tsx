import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/components/AppLayout';
import {
    DeleteButton,
    Pagination,
    SectionCard,
    TableShell,
} from '@/components/AppShell';
import { formatDateTime } from '@/lib/format';

interface ProductOption {
    id: number;
    name: string;
}

interface StockRow {
    id: number;
    product_id: number;
    qty: number;
    created_at: string;
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

function StockForm({
    products,
    stock,
    onCancel,
}: {
    products: ProductOption[];
    stock?: StockRow;
    onCancel: () => void;
}) {
    const isEdit = stock !== undefined;

    return (
        <Form
            method={isEdit ? 'put' : 'post'}
            action={isEdit ? `/stocks/${stock.id}` : '/stocks'}
            className="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4"
        >
            {({ errors, processing }) => (
                <>
                    <div className="grid gap-4 sm:grid-cols-2">
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
                                defaultValue={stock?.product_id ?? ''}
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
                                defaultValue={stock?.qty ?? ''}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.qty && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.qty}
                                </p>
                            )}
                        </div>
                    </div>

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
                                  : 'Tambah Pengadaan'}
                        </button>
                    </div>
                </>
            )}
        </Form>
    );
}

export default function StocksIndex({
    stocks,
    products,
}: {
    stocks: { data: StockRow[]; links: PaginationLink[] };
    products: ProductOption[];
}) {
    const [showCreate, setShowCreate] = useState(false);
    const [editingId, setEditingId] = useState<number | null>(null);

    const editingStock = stocks.data.find((stock) => stock.id === editingId);

    return (
        <AppLayout>
            <Head title="Pengadaan Stok" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-900">
                        Pengadaan Stok
                    </h2>
                    <button
                        type="button"
                        onClick={() => setShowCreate((value) => !value)}
                        className="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
                    >
                        {showCreate ? 'Tutup Form' : 'Tambah Pengadaan'}
                    </button>
                </div>

                {showCreate && (
                    <StockForm
                        products={products}
                        onCancel={() => setShowCreate(false)}
                    />
                )}

                <SectionCard>
                    {stocks.data.length === 0 ? (
                        <p className="text-sm text-gray-500">
                            Belum ada data pengadaan stok.
                        </p>
                    ) : (
                        <>
                            <TableShell
                                headers={[
                                    'Produk',
                                    'Jumlah',
                                    'Tanggal',
                                    'Aksi',
                                ]}
                            >
                                {stocks.data.map((stock) =>
                                    editingId === stock.id ? (
                                        <tr key={stock.id}>
                                            <td
                                                colSpan={4}
                                                className="px-4 py-3"
                                            >
                                                <StockForm
                                                    products={products}
                                                    stock={editingStock}
                                                    onCancel={() =>
                                                        setEditingId(null)
                                                    }
                                                />
                                            </td>
                                        </tr>
                                    ) : (
                                        <tr key={stock.id}>
                                            <td className="px-4 py-3 font-medium text-gray-900">
                                                {stock.product.name}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {stock.qty}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {formatDateTime(
                                                    stock.created_at,
                                                )}
                                            </td>
                                            <td className="flex gap-2 px-4 py-3">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        setEditingId(stock.id)
                                                    }
                                                    className="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                >
                                                    Edit
                                                </button>
                                                <DeleteButton
                                                    action={`/stocks/${stock.id}`}
                                                />
                                            </td>
                                        </tr>
                                    ),
                                )}
                            </TableShell>

                            <Pagination links={stocks.links} />
                        </>
                    )}
                </SectionCard>
            </div>
        </AppLayout>
    );
}
