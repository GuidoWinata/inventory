# PRD: Sistem Manajemen Stok Gudang (Simple Web Storage)

## 1. Gambaran Proyek & Aturan Arsitektur Khusus
Aplikasi web manajemen inventaris/gudang sederhana berbasis **Laravel** dan **MySQL** untuk kebutuhan tugas kuliah. Sistem ini mengelola pencatatan barang masuk, barang keluar, dan pelaporan stok dengan 3 *role*: **Admin**, **Staff Gudang**, dan **Manager**.

### ⚠️ Instruksi Krusial untuk AI Agent (Wajib Dipatuhi):
1. **DILARANG Menggunakan Laravel Migration:** Jangan membuat atau menjalankan file migrasi di dalam folder `database/migrations` bawaan Laravel (`php artisan migrate`).
2. **Gunakan Drizzle untuk Migrasi Database:** Seluruh perubahan skema database (pembuatan tabel baru) **WAJIB** dilakukan melalui proyek Drizzle yang berada di dalam folder `db-migrator-with-drizzle`.
3. **Tabel `users` & Sistem Role Sudah Ada:** Tabel `users` beserta kolom/sistem *assign role*-nya sudah tersedia di database. Agent **tidak perlu** membuat migrasi untuk tabel `users`. Cukup periksa skema tabel `users` yang ada di `db-migrator-with-drizzle` (atau Model `User` di Laravel) untuk menyesuaikan cara pengecekan *role* (`admin`, `staff`, `manager`).
4. **Skala Tugas Kuliah:** Fokus pada fungsionalitas alur bisnis dan kemudahan penggunaan. Tidak perlu menerapkan birokrasi atau aturan keamanan tingkat lanjut yang terlalu rumit.

---

## 2. Role & Hak Akses Pengguna (RBAC)
Sistem menggunakan autentikasi yang terhubung dengan tabel `users` yang sudah ada. Menu navigasi dan akses halaman menyesuaikan *role* pengguna:

| Fitur / Halaman | Admin | Staff Gudang | Manager | Detail Akses |
| :--- | :---: | :---: | :---: | :--- |
| **Dashboard** | ✅ | ✅ | ✅ | Semua *role* melihat ringkasan stok & 5 transaksi terakhir. |
| **Kelola Akun User** | ✅ (CRUD) | ❌ | ❌ | Admin mengelola data pengguna di tabel `users`. |
| **Data & Info Stok Barang** | ✅ (CRUD) | 👁️ (Read) | 👁️ (Read) | Admin kelola master barang. Staff & Manager hanya melihat stok. |
| **Barang Masuk & Keluar** | ❌ | ✅ (Create/Read) | ❌ | Staff menginput transaksi masuk/keluar & melihat riwayatnya. |
| **Laporan Stok** | ❌ | ❌ | ✅ | Manager memfilter riwayat transaksi & mencetak laporan. |

---

## 3. Struktur Data (Dikelola via `db-migrator-with-drizzle`)

### A. Tabel `users` (EXISTING — Jangan Buat Migrasi Baru)
* Gunakan struktur tabel `users` dan kolom *role* yang sudah didefinisikan di dalam sistem saat ini. Pastikan Eloquent Model `User` di Laravel memetakan kolom-kolom tersebut dengan benar.

### B. Tabel `items` (Data Master Barang — Buat via Drizzle)
* `id` (Primary Key, Auto Increment)
* `code` (Varchar, Unique — Kode barang, contoh: `BRG-001`)
* `name` (Varchar — Nama barang)
* `category` (Varchar — Kategori langsung berupa teks, contoh: `'Elektronik'`, `'ATK'`)
* `unit` (Varchar — Satuan barang, contoh: `'Pcs'`, `'Box'`, `'Unit'`)
* `stock` (Int, Default `0` — Menyimpan jumlah stok saat ini)
* `created_at` & `updated_at` (Timestamps)

### C. Tabel `transactions` (Barang Masuk & Keluar — Buat via Drizzle)
* `id` (Primary Key, Auto Increment)
* `item_id` (Foreign Key merujuk ke `items.id`, Cascade on Delete)
* `user_id` (Foreign Key merujuk ke `users.id` — pencatat transaksi, sesuaikan tipe datanya dengan PK `users.id` yang sudah ada)
* `type` (Enum / Varchar: `'in'` untuk barang masuk, `'out'` untuk barang keluar)
* `quantity` (Int — Jumlah barang yang masuk/keluar, minimal 1)
* `date` (Date — Tanggal transaksi)
* `notes` (Text, Nullable — Keterangan tambahan seperti supplier atau tujuan)
* `created_at` & `updated_at` (Timestamps)

---

## 4. Alur Fitur & Instruksi Logika (User Flows)

### Fitur 1: Autentikasi & Data Dummy (Seeder)
* **Alur Login:** Gunakan sistem login yang memvalidasi akun dari tabel `users`. Setelah login, arahkan ke `/dashboard`.
* **Data Seeder:** Buat seeder (via Laravel Seeder atau script di Drizzle) untuk memastikan tersedia minimal:
  * 1 Akun untuk masing-masing *role* (`admin`, `staff`, `manager`) jika belum ada di database.
  * 5 Data Barang *dummy* di tabel `items` beserta beberapa contoh transaksi awal di tabel `transactions`.

### Fitur 2: Dashboard (`/dashboard` — Semua Role)
* **Kartu Ringkasan (Summary Cards):**
  1. **Total Jenis Barang:** Jumlah baris di tabel `items`.
  2. **Total Stok Fisik:** Penjumlahan (`SUM`) dari kolom `stock` di tabel `items`.
  3. **Total Transaksi Masuk:** Jumlah transaksi dengan `type = 'in'`.
  4. **Total Transaksi Keluar:** Jumlah transaksi dengan `type = 'out'`.
* **Tabel Ringkas:** Menampilkan 5 riwayat transaksi terakhir (Tanggal, Nama Barang, Tipe Masuk/Keluar, Jumlah, dan Nama Pencatat).

### Fitur 3: Kelola Pengguna (`/users` — Khusus Admin)
* **Read:** Menampilkan daftar user dari tabel `users` beserta *role*-nya.
* **Create:** Form tambah user baru beserta pemilihan *role* sesuai struktur tabel `users` yang ada.
* **Update:** Form edit data user dan *role*. Jika input password dikosongkan saat edit, gunakan password lama.
* **Delete:** Hapus user dari database.

### Fitur 4: Data Barang & Informasi Stok (`/items`)
* **Tampilan Tabel (Semua Role):** Menampilkan Kode Barang, Nama, Kategori, Satuan, Stok Saat Ini, dan Badge Status Stok:
  * `stock == 0` -> Badge Merah **"Habis"**
  * `stock < 10` -> Badge Kuning **"Menipis"**
  * `stock >= 10` -> Badge Hijau **"Aman"**
* **Fitur Pencarian:** Input *search* sederhana berdasarkan Nama atau Kode Barang.
* **Aksi CRUD (Khusus Admin):** Tombol Tambah Barang, Edit Barang, dan Hapus Barang hanya muncul dan hanya bisa diakses oleh **Admin**. Saat tambah barang baru, Admin bisa mengisi stok awal (default `0`).

### Fitur 5: Transaksi Barang Masuk & Keluar (`/transactions` — Khusus Staff Gudang)
Halaman ini menampilkan tabel riwayat transaksi dan form untuk mencatat barang masuk/keluar.
* **Input Form Transaksi:**
  * Pilih Barang (Dropdown menampilkan: `[Kode] Nama Barang (Stok: X)`).
  * Pilih Jenis Transaksi: **Barang Masuk (`in`)** atau **Barang Keluar (`out`)**.
  * Masukkan Jumlah (`quantity` > 0).
  * Pilih Tanggal Transaksi (Default hari ini).
  * Masukkan Keterangan (`notes`, opsional).
* **Logika Pembaruan Stok Otomatis:**
  * **Jika Barang Masuk (`in`):**
    1. Simpan record ke tabel `transactions` (`user_id` diambil dari ID Staff yang sedang login).
    2. Tambahkan stok di tabel `items`: `item.stock = item.stock + quantity`.
  * **Jika Barang Keluar (`out`):**
    1. Cek apakah `quantity` yang diinput melebihi `item.stock` saat ini.
    2. Jika `quantity > item.stock`, tolak transaksi dan tampilkan pesan error: *"Stok tidak mencukupi! Sisa stok saat ini: [X]"*.
    3. Jika mencukupi, simpan record ke tabel `transactions` dan kurangi stok di tabel `items`: `item.stock = item.stock - quantity`.

### Fitur 6: Laporan Stok Sederhana (`/reports` — Khusus Manager)
* **Filter Laporan:**
  * Rentang Tanggal: Tanggal Mulai (`start_date`) s/d Tanggal Akhir (`end_date`).
  * Filter Tipe: `Semua`, `Barang Masuk (in)`, `Barang Keluar (out)`.
* **Tabel Output:** Menampilkan data dari tabel `transactions` yang sesuai dengan filter (Tanggal, Kode Barang, Nama Barang, Jenis Transaksi, Jumlah, Pencatat, Keterangan) serta ringkasan posisi stok barang saat ini.
* **Cetak Laporan:** Sediakan tombol **"Cetak / Print"** yang memanggil `window.print()` dengan styling `@media print` sederhana (sembunyikan navigasi dan form filter saat dicetak).

---

## 5. Urutan Langkah Kerja untuk AI Agent (Execution Plan)

1. **Inspeksi Skema Existing:** Buka folder `db-migrator-with-drizzle` dan periksa skema tabel `users` yang sudah ada (termasuk tipe data Primary Key `id` dan struktur penyimpanan *role*-nya).
2. **Migrasi via Drizzle:** Tambahkan skema tabel `items` dan `transactions` ke dalam konfigurasi Drizzle di folder `db-migrator-with-drizzle`, lalu jalankan perintah *generate/push/migrate* milik Drizzle hingga kedua tabel terbentuk di MySQL.
3. **Setup Model Laravel:** Buat Eloquent Model `Item` dan `Transaction` di Laravel, lalu hubungkan relasinya dengan Model `User` yang sudah ada (`belongsTo`, `hasMany`).
4. **Setup Role Access & Seeder:** Pastikan middleware/pengecekan *role* membaca atribut *role* yang sudah ada di tabel `users`. Buat data *seeder* untuk barang dan contoh transaksi.
5. **Implementasi Fitur Inti:**
   * Bangun halaman & Controller **Dashboard** (`/dashboard`).
   * Bangun halaman & Controller **Data Barang / Info Stok** (`/items`).
   * Bangun halaman & Controller **Barang Masuk & Keluar** (`/transactions`) beserta validasi stok keluar.
   * Bangun halaman & Controller **Kelola User** (`/users`) untuk Admin dan **Laporan Stok** (`/reports`) untuk Manager.