# Mini POS — Laravel CRUD + API

Aplikasi sederhana untuk mengelola **produk, pengadaan stok, penjualan, dan diskon** berbasis Laravel 13, Inertia + React (TypeScript), dan Tailwind CSS 4, dengan REST API serta deployment berbasis Docker (PHP-FPM + NGINX + PostgreSQL).

## Fitur

- Login sederhana dengan username & password
- Dashboard infografis: 5 penjualan terbaru dan total omset dengan filter rentang tanggal (default awal s.d. akhir bulan berjalan)
- CRUD lengkap: master produk, pengadaan stok, penjualan, daftar diskon, dan diskon terpakai
- Stok produk dihitung dari akumulasi pengadaan stok dikurangi penjualan (stok tidak boleh minus)
- Penjualan ditolak jika stok tidak mencukupi; diskon yang berlaku pada tanggal pembelian diterapkan otomatis (diskon terbaru menang jika ada lebih dari satu)
- Diskon ditampilkan dalam persen dan menampilkan jumlah pemakaian
- REST API `GET /api/products/{id}` yang mengembalikan deskripsi produk dan diskon yang sedang berlaku
- Unit & feature test dengan Pest

## Struktur Tabel

| Tabel               | Kolom                                                                                              |
| ------------------- | -------------------------------------------------------------------------------------------------- |
| `m_products`        | id, name, description, price, stock, created_at, updated_at, deleted_at                            |
| `t_stocks`          | id, product_id, qty, created_at, updated_at, deleted_at                                            |
| `t_sales`           | id, product_id, qty, created_at, updated_at, deleted_at                                            |
| `t_discounts`       | id, product_id, discount (float, persen), start_date, end_date, created_at, updated_at, deleted_at |
| `t_sales_discounts` | id, sales_id, discount_id, start_date, end_date, created_at, updated_at, deleted_at                |

> Catatan: kolom `start_date` / `end_date` ditambahkan pada `t_discounts` sebagai periode berlakunya diskon; nilainya disalin (snapshot) ke `t_sales_discounts` saat diskon dipakai pada sebuah penjualan.

## Kebutuhan

- PHP >= 8.3 dengan ekstensi `pdo_pgsql` (atau MySQL — sesuaikan `.env`)
- Composer
- Node.js >= 20
- PostgreSQL (atau gunakan Docker)

## Menjalankan Secara Lokal

```bash
# 1. Install dependency PHP
composer install

# 2. Salin dan sesuaikan file environment
cp .env.example .env
# Atur DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Generate application key
php artisan key:generate

# 4. Jalankan migration dan seeder (membuat akun demo dan data contoh)
php artisan migrate:fresh --seed

# 5. Install dan build asset frontend
npm install
npm run build

# 6. Jalankan server
php artisan serve
# Buka http://127.0.0.1:8000
```

Untuk pengembangan frontend dengan hot reload, jalankan `npm run dev` di terminal terpisah.

### Akun Demo

| Username | Password   |
| -------- | ---------- |
| `admin`  | `password` |

## REST API

Endpoint produk:

```
GET /api/products/{id}
```

Karena API dilindungi session, login dulu melalui web (`POST /login`), lalu gunakan cookie session yang sama. Contoh dengan `curl`:

```bash
# 1. Login dan simpan cookie
curl -c cookies.txt -d "username=admin&password=password" http://127.0.0.1:8000/login

# 2. Akses API
curl -b cookies.txt http://127.0.0.1:8000/api/products/1
```

Contoh respons:

```json
{
    "data": {
        "id": 1,
        "name": "Kopi Gayo",
        "description": "Kopi arabika asal Aceh",
        "price": 50000,
        "stock": 12,
        "discount": {
            "percent": 10,
            "start_date": "2026-09-11",
            "end_date": "2026-09-17"
        }
    }
}
```

Jika tidak ada diskon yang berlaku, `discount` bernilai `null`.

## Menjalankan Test

```bash
php artisan test            # semua test Pest
composer run test           # test + pint + phpstan
npm run types:check         # type check TypeScript
```

## Menjalankan dengan Docker

Aplikasi dikemas dalam container **PHP-FPM** (service `app`) yang dilayani **NGINX** (service `webserver`) dengan database **PostgreSQL** (service `db`). Pastikan Docker Desktop/Engine sudah berjalan.

```bash
# 1. Build dan jalankan semua service
docker compose up -d --build

# 2. Buka aplikasi
#    http://localhost:8000
```

Saat pertama kali menyala, entrypoint container otomatis menjalankan `migrate` dan `db:seed` (data demo hanya dibuat sekali; restart tidak menduplikasi data). Database PostgreSQL terekspos di port host `5433` agar tidak bentrok dengan PostgreSQL lokal (`5432`).

Kode aplikasi dibagikan antara container `app` (PHP-FPM) dan `webserver` (NGINX) melalui named volume `app-code`, yang diisi dari image saat volume pertama kali dibuat. Jika Anda mengubah kode dan membangun ulang image, hapus volume tersebut agar kode baru ikut terpakai:

```bash
docker compose build          # build ulang image
docker compose down -v        # hapus volume (termasuk data database demo)
docker compose up -d          # jalankan ulang dengan kode terbaru
```

Perintah berguna:

```bash
docker compose ps               # status service
docker compose logs -f app      # lihat log PHP-FPM
docker compose down             # hentikan service (volume data tetap tersimpan)
```

## Aturan Bisnis Utama

1. **Stok produk** = `stock` awal + total pengadaan (`t_stocks`) − total penjualan (`t_sales`). Tidak pernah minus: penjualan melebihi stok ditolak, dan stok awal produk tidak bisa diedit di bawah jumlah yang sudah terjual.
2. **Diskon otomatis**: saat penjualan dibuat, sistem mencari diskon produk yang `start_date <= tanggal pembelian <= end_date`. Jika lebih dari satu yang valid, diskon yang **paling baru dibuat** yang dipakai. Pemakaian dicatat di `t_sales_discounts` (diskon terpakai) dengan snapshot periode berlaku.
3. **Omset** dihitung dari harga produk × qty, dikurangi diskon yang terpakai pada penjualan tersebut.
4. Penghapusan memakai soft delete (`deleted_at`).
