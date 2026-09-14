import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/components/AppLayout';
import {
    DeleteButton,
    Pagination,
    SectionCard,
    TableShell,
} from '@/components/AppShell';
import { formatCurrency } from '@/lib/format';

interface ProductRow {
    id: number;
    name: string;
    description: string | null;
    price: number;
    stock: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

function ProductForm({
    product,
    onCancel,
}: {
    product?: ProductRow;
    onCancel: () => void;
}) {
    const isEdit = product !== undefined;

    return (
        <Form
            method={isEdit ? 'put' : 'post'}
            action={isEdit ? `/products/${product.id}` : '/products'}
            className="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4"
        >
            {({ errors, processing }) => (
                <>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                htmlFor="name"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Nama Produk
                            </label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                defaultValue={product?.name ?? ''}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.name && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.name}
                                </p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="price"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Harga
                            </label>
                            <input
                                id="price"
                                type="number"
                                name="price"
                                min={1}
                                defaultValue={product?.price ?? ''}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.price && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.price}
                                </p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="stock"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Stok Awal
                            </label>
                            <input
                                id="stock"
                                type="number"
                                name="stock"
                                min={0}
                                defaultValue={product?.stock ?? 0}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.stock && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.stock}
                                </p>
                            )}
                        </div>

                        <div className="sm:col-span-2">
                            <label
                                htmlFor="description"
                                className="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Deskripsi
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows={2}
                                defaultValue={product?.description ?? ''}
                                className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:ring-1 focus:ring-gray-500 focus:outline-none"
                            />
                            {errors.description && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.description}
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
                                  : 'Tambah Produk'}
                        </button>
                    </div>
                </>
            )}
        </Form>
    );
}

export default function ProductsIndex({
    products,
}: {
    products: { data: ProductRow[]; links: PaginationLink[] };
}) {
    const [showCreate, setShowCreate] = useState(false);
    const [editingId, setEditingId] = useState<number | null>(null);

    const editingProduct = products.data.find(
        (product) => product.id === editingId,
    );

    return (
        <AppLayout>
            <Head title="Master Produk" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-900">
                        Master Produk
                    </h2>
                    <button
                        type="button"
                        onClick={() => setShowCreate((value) => !value)}
                        className="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
                    >
                        {showCreate ? 'Tutup Form' : 'Tambah Produk'}
                    </button>
                </div>

                {showCreate && (
                    <ProductForm onCancel={() => setShowCreate(false)} />
                )}

                <SectionCard>
                    {products.data.length === 0 ? (
                        <p className="text-sm text-gray-500">
                            Belum ada data produk.
                        </p>
                    ) : (
                        <>
                            <TableShell
                                headers={[
                                    'Nama',
                                    'Deskripsi',
                                    'Harga',
                                    'Stok',
                                    'Aksi',
                                ]}
                            >
                                {products.data.map((product) =>
                                    editingId === product.id ? (
                                        <tr key={product.id}>
                                            <td
                                                colSpan={5}
                                                className="px-4 py-3"
                                            >
                                                <ProductForm
                                                    product={editingProduct}
                                                    onCancel={() =>
                                                        setEditingId(null)
                                                    }
                                                />
                                            </td>
                                        </tr>
                                    ) : (
                                        <tr key={product.id}>
                                            <td className="px-4 py-3 font-medium text-gray-900">
                                                {product.name}
                                            </td>
                                            <td className="max-w-xs px-4 py-3 text-gray-500">
                                                <p className="line-clamp-2">
                                                    {product.description ?? '-'}
                                                </p>
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {formatCurrency(product.price)}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {product.stock}
                                            </td>
                                            <td className="flex gap-2 px-4 py-3">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        setEditingId(product.id)
                                                    }
                                                    className="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                >
                                                    Edit
                                                </button>
                                                <DeleteButton
                                                    action={`/products/${product.id}`}
                                                />
                                            </td>
                                        </tr>
                                    ),
                                )}
                            </TableShell>

                            <Pagination links={products.links} />
                        </>
                    )}
                </SectionCard>
            </div>
        </AppLayout>
    );
}
