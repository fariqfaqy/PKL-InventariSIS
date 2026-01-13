# Sistem Kelola Permintaan - Implementation Summary

## Overview
Sistem manajemen permintaan barang telah berhasil diimplementasikan. User dapat mengajukan permintaan untuk meminjam barang sewa atau menggunakan barang habis pakai, dan admin dapat menyetujui, memproses, atau menolak permintaan tersebut.

## Database Schema

### Tabel: request_barang
- **id_request** (Primary Key): ID permintaan
- **user_id** (Foreign Key): ID user yang mengajukan
- **idbarang** (Foreign Key): ID barang yang diminta
- **qty**: Jumlah barang yang diminta
- **tipe_request** (Enum): 'pinjam_sewa' atau 'pakai_habis_pakai'
- **status** (Enum): 'pending', 'approved', 'processing', 'rejected', 'completed'
- **keperluan**: Alasan/keperluan permintaan
- **catatan_user**: Catatan tambahan dari user
- **catatan_admin**: Catatan dari admin (untuk penolakan/informasi)
- **tanggal_mulai_sewa**: Tanggal mulai sewa (nullable, hanya untuk barang_sewa)
- **tanggal_akhir_sewa**: Tanggal akhir sewa (nullable, hanya untuk barang_sewa)
- **diproses_oleh**: Nama admin yang memproses
- **tanggal_request**: Waktu permintaan dibuat
- **tanggal_diproses**: Waktu permintaan diproses admin

## Alur Workflow Status

```
PENDING → APPROVED → PROCESSING → COMPLETED
    ↓
REJECTED (dapat ditolak dari pending atau approved)
```

### Status Detail:
1. **PENDING**: Permintaan baru dari user, menunggu persetujuan admin
2. **APPROVED**: Admin menyetujui, siap untuk diproses
3. **PROCESSING**: Admin memproses permintaan, stok dikurangi, transaksi keluar dibuat
4. **COMPLETED**: Permintaan selesai diproses
5. **REJECTED**: Admin menolak permintaan dengan alasan

## Fitur Admin

### 1. Kelola Permintaan - Index (admin/permintaan)
- **Statistik Dashboard**: Menampilkan jumlah permintaan berdasarkan status
- **Filter**: Filter berdasarkan status dan tipe request
- **Tabel Permintaan**: 
  - ID permintaan
  - Tanggal request
  - Nama user
  - Detail barang (nama & kode)
  - Jumlah
  - Tipe request (badge warna)
  - Status (badge warna)
  - Tombol aksi (detail)
- **Notification Badge**: Badge merah di menu sidebar menampilkan jumlah pending

### 2. Detail Permintaan (admin/permintaan/{id})
- **Informasi Lengkap**:
  - Status permintaan dengan warna
  - Informasi user (nama, email, tanggal request)
  - Detail barang (nama, kode, kategori, qty, stok tersedia)
  - Periode sewa (jika barang_sewa)
  - Keperluan dan catatan
  
- **Tombol Aksi** (kondisional berdasarkan status):
  - **Status PENDING**: Setujui / Tolak
  - **Status APPROVED**: Proses / Tolak
  - **Status PROCESSING**: Selesai
  - **Status REJECTED/COMPLETED**: Tidak ada aksi

### 3. Proses Permintaan
#### Approve (POST admin/permintaan/{id}/approve)
- Validasi status harus 'pending'
- Cek ketersediaan stok
- Ubah status menjadi 'approved'
- Catat admin dan tanggal diproses

#### Process (POST admin/permintaan/{id}/process)
- Validasi status harus 'approved'
- Cek stok lagi sebelum memproses
- Buat transaksi barang keluar (OutgoingTransaction)
- Kurangi stok barang
- Ubah status menjadi 'processing'
- Gunakan database transaction untuk integritas data

#### Reject (POST admin/permintaan/{id}/reject)
- Validasi status 'pending' atau 'approved'
- Wajib input alasan penolakan (catatan_admin)
- Ubah status menjadi 'rejected'

#### Complete (POST admin/permintaan/{id}/complete)
- Validasi status harus 'processing'
- Ubah status menjadi 'completed'

## Fitur User

### 1. Request Barang - Index (user/request-barang)
- **Filter**: Filter berdasarkan status
- **Tabel Request**: 
  - ID request
  - Tanggal
  - Nama barang & kode
  - Jumlah
  - Tipe request
  - Status dengan badge warna
  - Aksi: Lihat detail, Hapus (hanya pending)
- **Notification Badge**: Badge merah di menu sidebar menampilkan jumlah active requests (pending, approved, processing)

### 2. Buat Request (user/request-barang/create)
- **Form Fields**:
  - Dropdown pilih barang (hanya barang_sewa dan habis_pakai, exclude aset_tetap)
  - Info barang dinamis (nama, kode, kategori, stok tersedia)
  - Input jumlah (max sesuai stok)
  - Periode sewa (hidden by default, muncul untuk barang_sewa)
  - Keperluan (required)
  - Catatan tambahan (optional)

- **Validasi**:
  - Qty tidak boleh melebihi stok
  - Tanggal sewa wajib untuk barang_sewa
  - Tanggal akhir harus setelah tanggal mulai
  - Kategori barang valid (sewa/habis pakai)

- **JavaScript**:
  - Auto update info barang saat pilih
  - Show/hide section rental dates
  - Set max qty sesuai stok
  - Validasi tanggal sewa client-side

### 3. Detail Request (user/request-barang/{id})
- **Informasi Lengkap**:
  - Status dengan deskripsi kondisi
  - Timeline (tanggal request, tanggal diproses)
  - Detail barang
  - Periode sewa (jika ada)
  - Keperluan dan catatan
  - Catatan admin (jika ada)
  
- **Aksi**:
  - Hapus permintaan (hanya status pending)
  - Info admin yang memproses

### 4. Delete Request (DELETE user/request-barang/{id})
- Hanya permintaan dengan status 'pending' yang bisa dihapus
- Validasi user hanya bisa hapus request milik sendiri

## Routes

### Admin Routes:
```php
Route::get('permintaan', [PermintaanController::class, 'index'])
Route::get('permintaan/{id}', [PermintaanController::class, 'show'])
Route::post('permintaan/{id}/approve', [PermintaanController::class, 'approve'])
Route::post('permintaan/{id}/process', [PermintaanController::class, 'process'])
Route::post('permintaan/{id}/reject', [PermintaanController::class, 'reject'])
Route::post('permintaan/{id}/complete', [PermintaanController::class, 'complete'])
```

### User Routes:
```php
Route::get('request-barang', [RequestBarangController::class, 'index'])
Route::get('request-barang/create', [RequestBarangController::class, 'create'])
Route::post('request-barang', [RequestBarangController::class, 'store'])
Route::get('request-barang/{id}', [RequestBarangController::class, 'show'])
Route::delete('request-barang/{id}', [RequestBarangController::class, 'destroy'])
```

## Controllers

### Admin\PermintaanController
- **index()**: List permintaan dengan filter & stats
- **show()**: Detail permintaan
- **approve()**: Setujui permintaan
- **process()**: Proses permintaan & buat transaksi keluar
- **reject()**: Tolak permintaan dengan catatan
- **complete()**: Selesaikan permintaan

### User\RequestBarangController
- **index()**: List request user dengan filter
- **create()**: Form buat request
- **store()**: Simpan request baru dengan validasi
- **show()**: Detail request
- **destroy()**: Hapus request (pending only)

## Model: RequestBarang

### Relationships:
- `user()`: belongsTo User
- `stock()`: belongsTo Stock

### Accessor Methods:
- `status_color`: Mengembalikan warna badge (yellow, blue, purple, red, green)
- `status_label`: Label status dalam bahasa Indonesia
- `tipe_request_label`: Label tipe request dalam bahasa Indonesia

### Casts:
- `tanggal_request`: datetime
- `tanggal_diproses`: datetime
- `tanggal_mulai_sewa`: date
- `tanggal_akhir_sewa`: date

## UI Components

### Badges Warna Status:
- **Pending**: Yellow (bg-yellow-100 text-yellow-800)
- **Approved**: Blue (bg-blue-100 text-blue-800)
- **Processing**: Purple (bg-purple-100 text-purple-800)
- **Rejected**: Red (bg-red-100 text-red-800)
- **Completed**: Green (bg-green-100 text-green-800)

### Notification Badges:
- **Admin**: Menampilkan jumlah permintaan pending
- **User**: Menampilkan jumlah permintaan aktif (pending, approved, processing)

### Icons:
- **Kelola Permintaan** (Admin): clipboard-document-list
- **Request Barang** (User): shopping-cart

## Integrasi dengan Sistem Existing

### Barang Keluar (OutgoingTransaction):
Ketika admin memproses permintaan, sistem otomatis membuat transaksi barang keluar dengan data:
- idbarang, tanggal, penerima (nama user)
- qty, namabarang_k, kodebarang_k
- penginput (nama admin)
- kategori, tanggal sewa (jika ada)
- tipe_request

### Stock Management:
- Stok otomatis dikurangi saat permintaan diproses
- Validasi stok dilakukan sebelum approve dan process
- Gunakan database transaction untuk mencegah race condition

### Activity Log:
Semua aksi akan tercatat otomatis di activity log melalui middleware yang sudah ada.

## Keamanan & Validasi

### Admin:
- Middleware 'role:admin' memastikan hanya admin yang akses
- Validasi status sebelum setiap aksi
- Validasi stok sebelum approve dan process
- Database transaction untuk integritas data

### User:
- Middleware 'role:user' memastikan hanya user yang akses
- User hanya bisa lihat dan hapus request milik sendiri
- Validasi qty tidak melebihi stok
- Validasi tanggal sewa untuk barang_sewa
- Aset tetap tidak bisa direquest (filter di controller)

## Testing Checklist

### Admin:
- [ ] Lihat list permintaan dengan stats
- [ ] Filter berdasarkan status
- [ ] Filter berdasarkan tipe request
- [ ] Lihat detail permintaan
- [ ] Setujui permintaan pending
- [ ] Tolak permintaan pending
- [ ] Proses permintaan approved (cek stok berkurang)
- [ ] Tolak permintaan approved
- [ ] Selesaikan permintaan processing
- [ ] Lihat notification badge pending count

### User:
- [ ] Buat request barang sewa (dengan tanggal)
- [ ] Buat request habis pakai (tanpa tanggal)
- [ ] Validasi qty melebihi stok
- [ ] Lihat list request
- [ ] Filter request by status
- [ ] Lihat detail request
- [ ] Hapus request pending
- [ ] Coba hapus request non-pending (harusnya error)
- [ ] Lihat notification badge active count
- [ ] Lihat catatan admin saat ditolak

## Files Created/Modified

### Created:
- database/migrations/2026_01_13_132224_create_request_barang_table.php
- app/Models/RequestBarang.php
- app/Http/Controllers/Admin/PermintaanController.php
- app/Http/Controllers/User/RequestBarangController.php
- resources/views/admin/permintaan/index.blade.php
- resources/views/admin/permintaan/show.blade.php
- resources/views/user/request-barang/index.blade.php
- resources/views/user/request-barang/create.blade.php
- resources/views/user/request-barang/show.blade.php

### Modified:
- routes/web.php (added routes)
- resources/views/layouts/admin.blade.php (added menu & badge)
- resources/views/layouts/user.blade.php (added menu & badge)

## Database Migration

Jalankan migration yang sudah dibuat:
```bash
php artisan migrate
```

Migration akan membuat tabel `request_barang` dengan semua field dan foreign keys yang diperlukan.

## Next Steps / Future Enhancements

1. **Email Notifications**: Kirim email ke user saat status berubah
2. **Real-time Updates**: Gunakan WebSocket untuk update status real-time
3. **History Timeline**: Tampilkan timeline lengkap perubahan status
4. **Export Report**: Export data permintaan ke PDF/Excel
5. **Request Bulk**: Fitur request multiple items sekaligus
6. **Return System**: Sistem pengembalian untuk barang sewa
7. **Rating**: User bisa rating barang setelah selesai
8. **Auto Reject**: Auto reject permintaan pending setelah X hari
9. **Stock Reservation**: Reserve stok saat approved
10. **Dashboard Analytics**: Grafik statistik permintaan per periode
