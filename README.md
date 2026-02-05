# InventariSIS - Sistem Informasi Inventaris PLN

Aplikasi berbasis web untuk mengelola inventarisasi barang di lingkungan PLN. Sistem ini memudahkan pencatatan barang masuk, pemantauan stok, pengelolaan peminjaman barang, serta pelaporan aktivitas inventaris secara real-time.

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Alur Flow Website](#alur-flow-website)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Pengguna Default](#pengguna-default)
- [Struktur Proyek](#struktur-proyek)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Kategori Barang](#kategori-barang)
- [Hak Akses](#hak-akses)
- [Troubleshooting](#troubleshooting)

## Fitur Utama

### Untuk Administrator
- **Dashboard**: Statistik inventaris real-time, notifikasi permintaan barang, dan ringkasan aktivitas
- **Kelola Barang Masuk**: Pencatatan barang baru yang masuk ke gudang dengan dukungan berbagai kategori
- **Kelola Stok Barang**: Pemantauan stok, update kondisi barang, dan perpanjangan masa peminjaman
- **Kelola Barang Keluar**: Pencatatan barang yang keluar, approval peminjaman, dan pengembalian barang
- **Kelola Permintaan**: Proses approval permintaan barang dari user dengan workflow lengkap
- **Kelola User**: Manajemen akun pengguna dan pembagian divisi
- **Kelola Divisi**: Pengelolaan struktur divisi organisasi
- **Log Aktivitas**: Pencatatan otomatis semua aktivitas sistem dengan detail lengkap
- **Laporan PDF**: Export data ke format PDF untuk keperluan dokumentasi

### Untuk User
- **Dashboard**: Informasi stok tersedia dan riwayat peminjaman pribadi
- **Lihat Stok**: Melihat ketersediaan barang dan detail informasi barang
- **Permintaan Barang**: Mengajukan permintaan peminjaman atau penggunaan barang
- **Pemakaian Saya**: Tracking status permintaan, perpanjangan waktu pinjam, dan penyelesaian peminjaman
- **Notifikasi**: Pemberitahuan real-time untuk status permintaan barang

### Fitur Tambahan
- **QR Code**: Setiap barang memiliki QR code unik untuk identifikasi cepat
- **Notifikasi Real-time**: Sistem notifikasi untuk update status permintaan
- **Pagination**: Tampilan data 10 item per halaman untuk kemudahan navigasi
- **Search & Filter**: Pencarian dan filter data berdasarkan kategori, status, dan periode waktu
- **Responsive Design**: Tampilan yang optimal di desktop, tablet, dan mobile

## Alur Flow Website

### 1. Alur Login

```
User/Admin mengakses website
        ↓
Halaman Login
        ↓
Input Username & Password
        ↓
Sistem Validasi Kredensial
        ↓
    ┌───────┴───────┐
    ↓               ↓
  Sukses         Gagal
    ↓               ↓
Cek Role      Kembali ke Login
    │         (Tampilkan Error)
    ├─→ Admin: Redirect ke Admin Dashboard
    └─→ User: Redirect ke User Dashboard
```

### 2. Alur Barang Masuk (Admin)

```
Admin Login
        ↓
Menu Barang Masuk → Tambah Barang
        ↓
Pilih Kategori Barang:
  • Material Umum - Habis Pakai
  • Material Umum - Pinjam
  • Aset Tetap
  • Aset Sewa
        ↓
Input Data Barang:
  - Nama Barang
  - Kode Barang (otomatis: rack + 3 digit)
  - Jumlah/Stock
  - Lokasi Rak
  - Tanggal Masuk
  - Keterangan
        ↓
Sistem Validasi
        ↓
    ┌───────┴───────┐
    ↓               ↓
  Valid         Invalid
    ↓               ↓
Simpan ke Database    Tampilkan Error
    ↓               ↓
Generate QR Code    Perbaiki Input
    ↓
Tambah ke Stok Barang
    ↓
Catat ke Transaksi Masuk
    ↓
Log Aktivitas
    ↓
Selesai (Tampilkan Notifikasi Sukses)
```

### 3. Alur Permintaan Barang (User)

```
User Login
        ↓
Dashboard User → Lihat Stok Barang
        ↓
Pilih Barang yang Tersedia
        ↓
Klik "Ajukan Permintaan"
        ↓
Isi Form Permintaan:
  - Pilih Barang
  - Jumlah yang Diminta
  - Keperluan
  - Tanggal Pemakaian (untuk Material Umum - Pinjam)
  - Estimasi Pengembalian (untuk Material Umum - Pinjam)
        ↓
Submit Permintaan
        ↓
Status: "Menunggu Approval"
        ↓
Notifikasi Email ke Admin
        ↓
User dapat tracking di "Pemakaian Saya"
```

### 4. Alur Approval Permintaan (Admin)

```
Admin Login
        ↓
Notifikasi Permintaan Baru
        ↓
Menu Kelola Permintaan
        ↓
Lihat Detail Permintaan:
  - Data Pemohon
  - Barang yang Diminta
  - Jumlah
  - Keperluan
  - Tanggal Pemakaian
        ↓
Cek Ketersediaan Stok
        ↓
    ┌───────┴───────┐
    ↓               ↓
 Approve         Reject
    ↓               ↓
Kurangi Stok    Alasan Penolakan
    ↓               ↓
Update Status   Update Status
"Disetujui"     "Ditolak"
    ↓               ↓
Input Tanggal   Notifikasi ke User
Peminjaman (jika
Material Pinjam)
    ↓
Buat Transaksi
Barang Keluar
    ↓
Notifikasi ke User
    ↓
Log Aktivitas
```

### 5. Alur Pengembalian Barang (Material Umum - Pinjam)

```
User sudah selesai pakai barang
        ↓
Buka "Pemakaian Saya"
        ↓
Pilih item yang sedang dipinjam
        ↓
Klik "Tandai Selesai" / "Kembalikan"
        ↓
Konfirmasi Pengembalian
        ↓
Status berubah: "Menunggu Konfirmasi Admin"
        ↓
Notifikasi ke Admin
        ↓
--- ADMIN SIDE ---
        ↓
Admin terima notifikasi
        ↓
Verifikasi kondisi barang
        ↓
Update Status Barang:
  - Tersedia (jika barang baik)
  - Diperbaiki (jika ada kerusakan)
  - Rusak (jika tidak dapat digunakan)
        ↓
Kembalikan stok ke inventory
        ↓
Update Status Peminjaman: "Selesai"
        ↓
Notifikasi ke User
        ↓
Log Aktivitas
```

### 6. Alur Perpanjangan Peminjaman

```
User perlu perpanjangan waktu pinjam
        ↓
Buka "Pemakaian Saya"
        ↓
Pilih item yang sedang dipinjam
        ↓
Klik "Request Perpanjangan"
        ↓
Input:
  - Tanggal Pengembalian Baru
  - Alasan Perpanjangan
        ↓
Submit Request
        ↓
Status: "Menunggu Approval Perpanjangan"
        ↓
Notifikasi ke Admin
        ↓
--- ADMIN SIDE ---
        ↓
Admin review request
        ↓
    ┌───────┴───────┐
    ↓               ↓
 Approve         Reject
    ↓               ↓
Update Tanggal  Tetap dengan
Pengembalian    Tanggal Lama
    ↓               ↓
Notifikasi      Notifikasi
ke User         ke User
    ↓
Log Aktivitas
```

### 7. Alur Update Status Kondisi Barang (Admin)

```
Admin Login
        ↓
Menu Stok Barang
        ↓
Pilih Barang
        ↓
Klik "Update Status"
        ↓
Pilih Status Kondisi:
  • Tersedia
  • Digunakan
  • Diperbaiki
  • Rusak
  • Selesai (untuk Aset Sewa)
        ↓
Input Keterangan (opsional)
        ↓
Simpan Perubahan
        ↓
Update Database
        ↓
Log Aktivitas
        ↓
Tampilkan Notifikasi Sukses
```

### 8. Alur Export Laporan PDF (Admin)

```
Admin Login
        ↓
Pilih Menu:
  • Stok Barang
  • Barang Masuk
  • Barang Keluar
  • User Management
  • Divisi
  • Activity Log
        ↓
Klik "Export PDF"
        ↓
Sistem mengambil data dari database
        ↓
Generate PDF dengan format terstruktur:
  - Header (Logo, Judul, Tanggal)
  - Tabel Data
  - Footer (Total, Tanggal Export)
        ↓
Download PDF otomatis
        ↓
Log Aktivitas "Export Data"
```

### 9. Workflow Material Umum - Habis Pakai

```
User ajukan permintaan
        ↓
Admin approve
        ↓
Stok berkurang permanent
        ↓
Barang keluar (tidak perlu dikembalikan)
        ↓
Status langsung "Selesai"
        ↓
Log tercatat
```

### 10. Workflow Aset Sewa

```
Barang masuk (jumlah selalu 1 unit)
        ↓
User ajukan permintaan
        ↓
Admin approve dengan set:
  - Tanggal Mulai Sewa
  - Tanggal Selesai Sewa
        ↓
Barang keluar (status: Digunakan)
        ↓
Setelah masa sewa selesai:
        ↓
Admin update status "Selesai"
        ↓
Barang tidak dikembalikan ke stok
(karena merupakan aset sewa/rental)
        ↓
Log tercatat
```

### Diagram Hubungan Antar Modul

```
┌─────────────────────────────────────────────────────────┐
│                    USER INTERFACE                        │
├─────────────────┬───────────────────────────────────────┤
│  ADMIN PANEL    │         USER PANEL                    │
├─────────────────┼───────────────────────────────────────┤
│ • Dashboard     │ • Dashboard                           │
│ • Barang Masuk  │ • Lihat Stok                          │
│ • Stok Barang   │ • Permintaan Barang                   │
│ • Barang Keluar │ • Pemakaian Saya                      │
│ • Permintaan    │ • Notifikasi                          │
│ • User Mgmt     │                                       │
│ • Divisi        │                                       │
│ • Activity Log  │                                       │
│ • Notifikasi    │                                       │
└─────────────────┴───────────────────────────────────────┘
                          ↕
         ┌────────────────────────────────┐
         │   BUSINESS LOGIC LAYER         │
         │  (Controllers & Middleware)    │
         └────────────────────────────────┘
                          ↕
         ┌────────────────────────────────┐
         │      DATA ACCESS LAYER         │
         │     (Models & Eloquent)        │
         └────────────────────────────────┘
                          ↕
         ┌────────────────────────────────┐
         │         DATABASE               │
         │   (PostgreSQL / MySQL)         │
         │                                │
         │ Tables:                        │
         │ • stocks                       │
         │ • incoming_transactions        │
         │ • outgoing_transactions        │
         │ • request_barangs              │
         │ • users                        │
         │ • divisions                    │
         │ • activity_logs                │
         │ • rack_assignments             │
         └────────────────────────────────┘
```

## Persyaratan Sistem

Pastikan sistem Anda memenuhi spesifikasi berikut:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x dan **NPM** >= 9.x
- **Database**: PostgreSQL >= 13 atau MySQL >= 8.0
- **Extension PHP** yang diperlukan:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath
  - GD (untuk QR Code)

## Instalasi

### 1. Clone atau Download Repository

```bash
cd C:\Users\ASUS\Documents\PKL PLN\InventariSIS
```

### 2. Install Dependencies PHP

```bash
composer install
```

### 3. Install Dependencies JavaScript

```bash
npm install
```

### 4. Copy File Environment

```bash
copy .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

## Konfigurasi

### Database

Buka file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=inventarisis
DB_USERNAME=postgres
DB_PASSWORD=password_anda
```

Untuk MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventarisis
DB_USERNAME=root
DB_PASSWORD=password_anda
```

### Buat Database

Buat database baru sesuai nama yang Anda tentukan di `.env`:

**PostgreSQL:**
```sql
CREATE DATABASE inventarisis;
```

**MySQL:**
```sql
CREATE DATABASE inventarisis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Jalankan Migration dan Seeder

```bash
php artisan migrate --seed
```

Perintah ini akan membuat tabel database dan mengisi data awal termasuk:
- Akun admin dan user default
- 20 sample data barang dengan berbagai kategori
- Data divisi

## Menjalankan Aplikasi

### Mode Development

Jalankan server Laravel:

```bash
php artisan serve
```

Aplikasi akan berjalan di: `http://127.0.0.1:8000`

Untuk development dengan hot reload (opsional):

```bash
npm run dev
```

### Mode Production

Build assets untuk production:

```bash
npm run build
```

## Pengguna Default

Setelah menjalankan seeder, gunakan akun berikut untuk login:

### Administrator
- **Username**: `admin`
- **Password**: `password`
- **Hak Akses**: Full access ke semua fitur

### User Biasa
- **Username**: `user`
- **Password**: `password`
- **Hak Akses**: Akses terbatas sesuai role user

**Catatan**: Segera ganti password default setelah login pertama kali untuk keamanan.

## Struktur Proyek

```
InventariSIS/
├── app/
│   ├── Http/Controllers/    # Controller Admin dan User
│   ├── Models/               # Model database (Stock, User, dll)
│   └── Notifications/        # Notifikasi sistem
├── database/
│   ├── migrations/           # Schema database
│   └── seeders/              # Data awal (admin, user, barang)
├── public/                   # File publik (CSS, JS, images)
├── resources/
│   ├── views/                # Template Blade (admin & user)
│   ├── css/                  # Styling
│   └── js/                   # JavaScript
├── routes/
│   └── web.php               # Routing aplikasi
└── storage/                  # File storage, logs, cache
```

## Teknologi yang Digunakan

### Backend
- **Laravel 12** - PHP Framework
- **PHP 8.2+** - Bahasa pemrograman
- **PostgreSQL/MySQL** - Database

### Frontend
- **Blade Template** - Template engine
- **Tailwind CSS** - CSS framework
- **Alpine.js** - JavaScript framework ringan
- **Heroicons** - Icon library

### Library Pendukung
- **DomPDF** - Generate laporan PDF
- **SimpleSoftwareIO QR Code** - Generate QR code
- **Laravel Notifications** - Sistem notifikasi
- **Laravel Pagination** - Pagination otomatis

## Kategori Barang

Sistem mendukung 4 kategori barang utama:

### 1. Material Umum - Habis Pakai
Barang yang langsung habis setelah digunakan (alat tulis, bahan monitor, dll)

### 2. Material Umum - Pinjam
Barang yang bisa dipinjam dan dikembalikan (laptop, proyektor, alat ukur)

### 3. Aset Tetap
Barang inventaris permanen milik perusahaan (meja, kursi, AC, kendaraan)

### 4. Aset Sewa
Barang yang disewakan dengan jumlah terbatas (1 unit per item)

## Hak Akses

### Administrator
| Fitur | Hak Akses |
|-------|-----------|
| Dashboard | ✓ View statistik lengkap |
| Barang Masuk | ✓ Create, Read, Update, Delete, Export PDF |
| Stok Barang | ✓ Read, Update status/kondisi, Export PDF |
| Barang Keluar | ✓ Create, Read, Update, Delete, Export PDF |
| Permintaan | ✓ Approve, Reject, Process, Complete |
| User Management | ✓ Create, Read, Update, Delete, Export PDF |
| Divisi | ✓ Create, Read, Update, Delete, Export PDF |
| Activity Log | ✓ View semua log, Export PDF |
| Notifikasi | ✓ Receive notification untuk permintaan baru |

### User
| Fitur | Hak Akses |
|-------|-----------|
| Dashboard | ✓ View stok tersedia & pemakaian sendiri |
| Stok Barang | ✓ Read only |
| Barang Masuk | ✓ Read only |
| Barang Keluar | ✓ Read only (semua data) |
| Permintaan | ✓ Create permintaan baru |
| Pemakaian Saya | ✓ Create, Read, Update, Delete (data sendiri) |
| Barang di Rak | ✓ Read only |
| Notifikasi | ✓ Receive notification untuk status permintaan |

## Troubleshooting

### Error: "Could not find driver"
Install extension PDO untuk database Anda:
```bash
# Untuk PostgreSQL
sudo apt-get install php8.2-pgsql

# Untuk MySQL
sudo apt-get install php8.2-mysql
```

### Error: "Access denied for user"
Pastikan konfigurasi database di `.env` sudah benar dan user memiliki hak akses.

### Error: "Class 'DomPDF' not found"
Jalankan ulang composer install:
```bash
composer install
composer dump-autoload
```

### Pagination tidak muncul
Pastikan data sudah lebih dari 10 item atau cek apakah ada error di console browser.

### QR Code tidak muncul
Pastikan extension GD sudah terinstall:
```bash
sudo apt-get install php8.2-gd
```

---

**Dikembangkan untuk Program Praktik Kerja Lapangan (PKL) PLN**

*Proyek ini dibuat sebagai solusi manajemen inventaris yang efisien dan user-friendly untuk mendukung operasional PLN.*
