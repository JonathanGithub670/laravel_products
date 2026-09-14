import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/components/AppLayout';
import {
    DeleteButton,
    Pagination,
    SectionCard,
    TableShell,
} from '@/components/AppShell';
import { formatDate, formatPercent } from '@/lib/format';

interface ProductOption {
    id: number;
    name: string;
}

interface DiscountRow {
    id: number;
    product_id: number;
    discount: number;
    start_date: string;
    end_date: string;
    used_count: number;
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

function DiscountForm({
    products,
    discount,
    onCancel,
}: {
    products: ProductOption[];
    discount?: DiscountRow;
    onCancel: () => void;
}) {
    const isEdit = discount !== undefined;

    return (
        <Form
            method={isEdit ? 'put' : 'post'}
            action={isEdit ? `/discounts/${discount.id}` : '/discounts'}
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
                                defaultValue={discount?.product_id ?? ''}
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
                                htmlFor="discount"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Diskon (%)
                            </label>
                            <input
                                id="discount"
                                type="number"
                                name="discount"
                                step="0.01"
                                min={0.01}
                                max={100}
                                defaultValue={discount?.discount ?? ''}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.discount && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.discount}
                                </p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="start_date"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Tanggal Mulai
                            </label>
                            <input
                                id="start_date"
                                type="date"
                                name="start_date"
                                defaultValue={discount?.start_date ?? ''}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.start_date && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.start_date}
                                </p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="end_date"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Tanggal Selesai
                            </label>
                            <input
                                id="end_date"
                                type="date"
                                name="end_date"
                                defaultValue={discount?.end_date ?? ''}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.end_date && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.end_date}
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
                                  : 'Tambah Diskon'}
                        </button>
                    </div>
                </>
            )}
        </Form>
    );
}

export default function DiscountsIndex({
    discounts,
    products,
}: {
    discounts: { data: DiscountRow[]; links: PaginationLink[] };
    products: ProductOption[];
}) {
    const [showCreate, setShowCreate] = useState(false);
    const [editingId, setEditingId] = useState<number | null>(null);

    const editingDiscount = discounts.data.find(
        (discount) => discount.id === editingId,
    );

    return (
        <AppLayout>
            <Head title="Daftar Diskon" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-900">
                        Daftar Diskon
                    </h2>
                    <button
                        type="button"
                        onClick={() => setShowCreate((value) => !value)}
                        className="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
                    >
                        {showCreate ? 'Tutup Form' : 'Tambah Diskon'}
                    </button>
                </div>

                {showCreate && (
                    <DiscountForm
                        products={products}
                        onCancel={() => setShowCreate(false)}
                    />
                )}

                <SectionCard>
                    {discounts.data.length === 0 ? (
                        <p className="text-sm text-gray-500">
                            Belum ada data diskon.
                        </p>
                    ) : (
                        <>
                            <TableShell
                                headers={[
                                    'Produk',
                                    'Diskon',
                                    'Berlaku Dari',
                                    'Berlaku Sampai',
                                    'Terpakai',
                                    'Aksi',
                                ]}
                            >
                                {discounts.data.map((discount) =>
                                    editingId === discount.id ? (
                                        <tr key={discount.id}>
                                            <td
                                                colSpan={6}
                                                className="px-4 py-3"
                                            >
                                                <DiscountForm
                                                    products={products}
                                                    discount={editingDiscount}
                                                    onCancel={() =>
                                                        setEditingId(null)
                                                    }
                                                />
                                            </td>
                                        </tr>
                                    ) : (
                                        <tr key={discount.id}>
                                            <td className="px-4 py-3 font-medium text-gray-900">
                                                {discount.product.name}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap">
                                                <span className="rounded-full bg-emerald-100 px-2 py-0.5 text-sm font-medium text-emerald-700">
                                                    {formatPercent(
                                                        discount.discount,
                                                    )}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {formatDate(
                                                    discount.start_date,
                                                )}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {formatDate(discount.end_date)}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {discount.used_count}x
                                            </td>
                                            <td className="flex gap-2 px-4 py-3">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        setEditingId(
                                                            discount.id,
                                                        )
                                                    }
                                                    className="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                >
                                                    Edit
                                                </button>
                                                <DeleteButton
                                                    action={`/discounts/${discount.id}`}
                                                />
                                            </td>
                                        </tr>
                                    ),
                                )}
                            </TableShell>

                            <Pagination links={discounts.links} />
                        </>
                    )}
                </SectionCard>
            </div>
        </AppLayout>
    );
}
