# 📦 FITUR PERPANJANG DAN SELESAIKAN PEMINJAMAN MATERIAL UMUM

## 📋 Overview

Fitur baru untuk admin mengelola peminjaman material umum (barang pinjam) dengan kemampuan:
1. **Perpanjang peminjaman** - Extend rental period
2. **Selesaikan peminjaman** - Complete rental dan kembalikan stok

---

## 🎯 Fitur yang Ditambahkan

### 1️⃣ **Perpanjang Peminjaman (Extend Rental)**

**Fungsi:** Memperpanjang masa peminjaman material umum

**Cara Kerja:**
- Admin dapat memperpanjang tanggal kembali peminjaman
- Validasi: tanggal baru harus lebih dari tanggal kembali saat ini
- Update dilakukan pada `request_barang` dan `outgoing_transactions`

**Lokasi:**
- Controller: `PermintaanController@extendRental`
- Route: `POST admin/permintaan/{id}/extend-rental`
- View: Modal di halaman detail permintaan

**Contoh Penggunaan:**
1. Buka detail permintaan yang sudah disetujui (status: approved)
2. Pada bagian "Periode Peminjaman", klik tombol "Perpanjang Peminjaman"
3. Pilih tanggal kembali baru (harus setelah tanggal kembali saat ini)
4. Klik "Perpanjang"

**Validasi:**
- Hanya untuk `tipe_request = 'pinjam_material'`
- Status harus `'approved'`
- Tanggal kembali baru harus > tanggal kembali saat ini

---

### 2️⃣ **Selesaikan Peminjaman (Complete Rental)**

**Fungsi:** Menandai peminjaman selesai dan mengembalikan barang ke stok

**Cara Kerja:**
- Admin menandai peminjaman sudah selesai
- Stok barang otomatis bertambah sesuai qty yang dipinjam
- Status request berubah menjadi `'completed'`
- OutgoingTransaction status menjadi `'selesai'`
- Auto-reject semua pending change request

**Lokasi:**
- Controller: `PermintaanController@completeRental`
- Route: `POST admin/permintaan/{id}/complete-rental`
- View: Tombol di halaman detail & list permintaan

**Contoh Penggunaan:**
1. Buka detail permintaan yang sudah disetujui (status: approved)
2. Klik tombol "Selesaikan Peminjaman" di sidebar kanan
3. Konfirmasi untuk menyelesaikan
4. Stok akan otomatis bertambah

**Validasi:**
- Hanya untuk `tipe_request = 'pinjam_material'`
- Status harus `'approved'`

**Efek Samping:**
- ✅ Stok bertambah sesuai qty
- ✅ Status request → `'completed'`
- ✅ OutgoingTransaction status → `'selesai'`
- ✅ Tanggal selesai diisi dengan waktu sekarang
- ✅ Auto-reject pending change requests

---

## 📊 Database Changes

Tidak ada perubahan schema database. Fitur menggunakan kolom yang sudah ada:

### `request_barang`
- `tanggal_akhir_sewa` - Updated saat perpanjang
- `status` - Berubah ke `'completed'` saat selesai

### `outgoing_transactions`
- `tanggal_akhir_pakai` - Updated saat perpanjang
- `status` - Berubah ke `'selesai'` saat selesai
- `tanggal_selesai` - Diisi saat selesai

### `stock`
- `stock` - Bertambah saat peminjaman selesai

---

## 🖥️ User Interface

### Halaman Detail Permintaan (`show.blade.php`)

**Periode Peminjaman Section:**
- Menampilkan tanggal pinjam & kembali
- Status peminjaman (Terlambat / Mendekati deadline / Masih lama)
- Tombol "Perpanjang Peminjaman" (untuk approved rentals)

**Aksi Cepat Sidebar:**
- Tombol "Selesaikan Peminjaman" (untuk approved pinjam_material)
- Informasi: "Klik tombol di atas ketika barang sudah dikembalikan"

**Modal Perpanjang:**
- Input tanggal kembali baru
- Min date validation
- Info tanggal kembali saat ini

### Halaman List Permintaan (`index.blade.php`)

**Tab "Request Biasa" → Section "Disetujui":**
- Tombol quick action "Selesaikan" untuk barang pinjam
- Link "Detail" untuk melihat detail lengkap

---

## 🔄 Workflow

### Alur Normal Peminjaman Material Umum:

```
1. User request barang pinjam
   ├─ tipe_request: 'pinjam_material'
   ├─ tanggal_mulai_sewa: [tanggal]
   └─ tanggal_akhir_sewa: [tanggal]

2. Admin approve
   ├─ Status: 'approved'
   ├─ Stok berkurang
   └─ OutgoingTransaction dibuat (status: 'sedang_dipakai')

3. Admin bisa:
   ├─ [PERPANJANG] Extend tanggal kembali (jika butuh lebih lama)
   └─ [SELESAI] Complete rental (ketika barang dikembalikan)

4. Setelah selesai:
   ├─ Status: 'completed'
   ├─ Stok bertambah lagi
   ├─ OutgoingTransaction status: 'selesai'
   └─ Masuk ke history
```

---

## 🎨 Visual Indicators

### Status Peminjaman di Detail Page:

```php
// Terlambat (Overdue)
@if($isOverdue)
    <span class="bg-red-100 text-red-800">
        ⚠️ Terlambat {N} hari
    </span>

// Mendekati deadline (≤ 3 hari)
@elseif($daysRemaining <= 3)
    <span class="bg-yellow-100 text-yellow-800">
        🕐 {N} hari lagi
    </span>

// Masih lama (> 3 hari)
@else
    <span class="bg-green-100 text-green-800">
        ✅ Masih {N} hari
    </span>
```

---

## 🔒 Security & Validations

### Controller Validations:

**extendRental:**
```php
✓ Hanya pinjam_material
✓ Status = approved
✓ Tanggal baru > tanggal lama
✓ Tanggal kembali harus ada
```

**completeRental:**
```php
✓ Hanya pinjam_material
✓ Status = approved
✓ DB Transaction untuk atomicity
✓ Auto-reject pending change requests
```

---

## 📝 Code Locations

### Backend
- **Controller:** `app/Http/Controllers/Admin/PermintaanController.php`
  - `extendRental()` - Line ~463
  - `completeRental()` - Line ~504

### Frontend
- **Detail View:** `resources/views/admin/permintaan/show.blade.php`
  - Periode Peminjaman section - Line ~180
  - Extend button - Line ~218
  - Complete button - Line ~270
  - Extend modal - Line ~307

- **Index View:** `resources/views/admin/permintaan/index.blade.php`
  - Complete button - Line ~215

### Routes
- **Routes:** `routes/web.php`
  - `POST admin/permintaan/{id}/extend-rental` - Line ~68
  - `POST admin/permintaan/{id}/complete-rental` - Line ~69

---

## ✅ Testing Checklist

### Test Perpanjang Peminjaman:
- [ ] Buka detail peminjaman yang approved
- [ ] Klik "Perpanjang Peminjaman"
- [ ] Input tanggal kembali baru (setelah tanggal saat ini)
- [ ] Verify: tanggal berhasil diupdate
- [ ] Verify: OutgoingTransaction juga terupdate

### Test Selesaikan Peminjaman:
- [ ] Buka detail peminjaman yang approved
- [ ] Klik "Selesaikan Peminjaman"
- [ ] Konfirmasi
- [ ] Verify: status → completed
- [ ] Verify: stok bertambah sesuai qty
- [ ] Verify: OutgoingTransaction status → selesai
- [ ] Verify: masuk ke tab History

### Edge Cases:
- [ ] Coba perpanjang dengan tanggal ≤ tanggal saat ini (harus error)
- [ ] Coba selesaikan barang habis pakai (harus error)
- [ ] Coba perpanjang/selesaikan yang status bukan approved (harus error)

---

## 🎯 Key Features Summary

| Fitur | Untuk | Aksi | Efek |
|-------|-------|------|------|
| **Perpanjang** | Barang Pinjam (approved) | Update tanggal kembali | Tanggal berubah, stok tetap |
| **Selesaikan** | Barang Pinjam (approved) | Mark completed | Stok bertambah, status selesai |

---

## 📞 Support

Jika ada bug atau pertanyaan, silakan hubungi developer atau buat issue di repository.

---

**Created:** 2 Februari 2026  
**Version:** 1.0  
**Author:** GitHub Copilot
