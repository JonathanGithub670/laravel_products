Task - Laravel CRUD + API (wajib Semua Level)

Buat aplikasi Laravel sederhana dengan ketentuan berikut:

1. buat project laravel (versi 12 ke atas)
2. buat model, migration, seeder dan factory berikut
    a. m_products (id, name, description, price, stock, created_at, updated_at, deleted_at)
    b. t_stocks (id, product_id, qty, created_at, updated_at, deleted_at) 
    c. t_sales (id, product_id, qty, created_at, updated_at, deleted_at)
    d. t_discount (id, product_id, discount, created_at, updated_at, deleted_at) note: tipe data discount menggunakan float
    e. t_sales_discount (id, sales_id, discount_id, start_date, end_date, created_at, updated_at, deleted_at)
3. Buat View dengan library Frontend (bisa menggunakan react js typescrpit (tailwind css))
    a. halaman login sederhana (username dan password)
    b. halaman dashboard yang menampilkan infografis
        i. Latest 5 sales
        ii. total penjualan (omset) dengan default filter tanggal awal dan akhir bulan ini.tanggal filter bisa diubah dan otomatis menampilkan omset sesuai range date
    c. halaman index (list data), form tambah, form edit dan delete
        i. master produk (m_products)
            note: tampilkan jumlah stok sesuai akumlasi dari pengadaan stok dan penjualan. stok tidak boleh minus
        ii. pengadaan stok (t_stocks)
        iii. penjualan (t_sales)
            note: tambah data penjualan invalid jika jumlah stok kurang. jika ada diskon yang valid pada tanggal pembelian, maka diskon otomatis diberlakukan
        iv. daftar diskon (t_discount)
            note: tampilkan discount dalam bentuk persentase ("%") dan tampilkan jumlah diskon yang telah digunakan
        v. diskon terpakai (t_sales_discount)
        vi. buat validasi sederhana pada setiap form tambah dan edit
4. buat RESTful API endpoint : GET /api/products/{id}
    a. hasil return dalam format JSON yang menampilkan deskripsi produk dan diskon yang sedang berlaku
5. commit kode ke repository Git dengan pesan commit yang deskiptif
6. deployment aplikasi berbasis container docker menggunakan PHP-FPM dan web server (NGINX/Apache)
6. Dokumentasikan langkah menjalankan project di README.md