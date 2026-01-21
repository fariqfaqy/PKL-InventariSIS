# Implementasi Sub-Kategori Material Umum

## 📋 Overview
Dokumen ini menjelaskan implementasi sistem sub-kategorisasi untuk Material Umum (habis_pakai) yang memisahkan Barang Habis Pakai dan Barang Pinjam.

## 🎯 Tujuan
- Memberikan struktur yang lebih jelas untuk Material Umum
- Memisahkan barang yang habis pakai (tidak dikembalikan) dan barang pinjam (harus dikembalikan)
- Mempermudah pegawai dalam memilih tipe request yang sesuai
- Meningkatkan akurasi data dan laporan

---

## 🔧 Perubahan Database

### Migration: `2026_01_20_150000_add_sub_kategori_constraint_to_stock_table.php`

**Status:** ✅ Berhasil dijalankan (14.74ms)

**Fungsi:**
- Menambahkan constraint CHECK pada kolom `sub_kategori`
- Validasi database-level: hanya nilai `barang_habis_pakai`, `barang_pinjam`, atau `NULL` yang diizinkan

**SQL Constraint:**
```sql
ALTER TABLE stock 
ADD CONSTRAINT check_sub_kategori 
CHECK (sub_kategori IN ('barang_habis_pakai', 'barang_pinjam') OR sub_kategori IS NULL);
```

**Catatan Penting:**
- Migration tidak mengubah data yang sudah ada (untuk menghindari konflik)
- Admin perlu mengatur sub_kategori untuk barang Material Umum yang sudah ada secara manual
- Untuk barang baru, sub_kategori wajib diisi jika kategori = 'habis_pakai'

---

## 📦 Struktur Kategori

### Kategori Utama

| Kategori | Akses | Sub-Kategori | Dapat Direquest User |
|----------|-------|--------------|---------------------|
| **Material Umum** (`habis_pakai`) | User + Admin | ✅ Ya | ✅ Ya |
| **Aset Sewa** (`barang_sewa`) | Admin Only | ❌ Tidak | ❌ Tidak |
| **Aset Tetap** (`aset_tetap`) | Admin Only | ❌ Tidak | ❌ Tidak |

### Sub-Kategori Material Umum

#### 1. Barang Habis Pakai (`barang_habis_pakai`)
- **Sifat:** Permintaan (tidak perlu dikembalikan)
- **Contoh:** ATK, tinta printer, kertas, spidol
- **Tipe Request:** `pakai_habis_pakai`
- **Tanggal:** Tidak memerlukan tanggal pinjam/kembali
- **Badge:** 🟢 Hijau dengan icon archive-box

#### 2. Barang Pinjam (`barang_pinjam`)
- **Sifat:** Peminjaman (harus dikembalikan)
- **Contoh:** Proyektor portable, kamera, tablet
- **Tipe Request:** `pinjam_material`
- **Tanggal:** ✅ Wajib tanggal pinjam & tanggal kembali
- **Badge:** 🔵 Biru dengan icon arrow-path

---

## 💻 Perubahan Backend

### 1. Model: `RequestBarang.php`

**Accessor Baru:**

```php
getTipeRequestLabelAttribute()
```
- Mapping: `pakai_habis_pakai` → "Barang Habis Pakai"
- Mapping: `pinjam_material` → "Barang Pinjam"
- Mapping: `pinjam_sewa` → "Pinjam Aset Sewa" (legacy)

```php
getSubKategoriLabelAttribute()
```
- Mengambil label sub_kategori dari relasi stock
- Return: "Barang Habis Pakai" | "Barang Pinjam" | "-"

```php
getSubKategoriBadgeColorAttribute()
```
- Return color untuk badge: `green` | `blue` | `gray`

### 2. Controller: `PemakaianController.php`

#### Method `create()`
```php
$barangHabisPakai = Stock::where('kategori', 'habis_pakai')
    ->where('sub_kategori', 'barang_habis_pakai')
    ->where('stock', '>', 0)
    ->get();

$barangPinjam = Stock::where('kategori', 'habis_pakai')
    ->where('sub_kategori', 'barang_pinjam')
    ->where('stock', '>', 0)
    ->get();
```
- Memisahkan data barang berdasarkan sub_kategori
- Mengirim 2 collection terpisah ke view

#### Method `store()`
**Validasi Ketat:**
```php
'sub_kategori' => 'required|in:barang_habis_pakai,barang_pinjam'
'idbarang' => 'required|exists:stock,idbarang'
'tanggal_pinjam' => 'nullable|required_if:sub_kategori,barang_pinjam|date|after_or_equal:today'
'tanggal_kembali' => 'nullable|required_if:sub_kategori,barang_pinjam|date|after:tanggal_pinjam'
```

**Business Logic:**
1. ✅ Validasi: Pegawai hanya bisa request kategori `habis_pakai`
2. ✅ Validasi: Sub-kategori request harus sesuai dengan sub-kategori barang
3. ✅ Validasi: Stok harus mencukupi
4. ✅ Mapping tipe_request:
   - `barang_habis_pakai` → `pakai_habis_pakai`
   - `barang_pinjam` → `pinjam_material`
5. ✅ Tanggal hanya diisi jika sub_kategori = barang_pinjam

### 3. Controller: `StokBarangController.php`

#### Method `store()`
```php
'kategori' => 'required|in:barang_sewa,habis_pakai,aset_tetap'
'sub_kategori' => 'nullable|required_if:kategori,habis_pakai|in:barang_habis_pakai,barang_pinjam'
```
- Sub-kategori wajib diisi untuk Material Umum
- Sub-kategori di-set NULL untuk kategori lain

#### Method `update()`
- Sama seperti `store()`, dengan validasi sub_kategori

---

## 🎨 Perubahan Frontend

### 1. Form Request: `pemakaian/create.blade.php`

#### Struktur Form Baru

**1. Info Box Material Umum**
```blade
<div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <x-heroicon-o-information-circle class="h-5 w-5 text-blue-400" />
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Tentang Material Umum</h3>
            <div class="mt-2 text-sm text-blue-700">
                <p>Material Umum terdiri dari 2 sub-kategori:</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li><strong>Barang Habis Pakai:</strong> Permintaan barang yang tidak perlu dikembalikan</li>
                    <li><strong>Barang Pinjam:</strong> Peminjaman barang yang harus dikembalikan sesuai tanggal</li>
                </ul>
            </div>
        </div>
    </div>
</div>
```

**2. Sub-Kategori Dropdown**
```blade
<select name="sub_kategori" id="sub_kategori" required>
    <option value="">-- Pilih Sub-Kategori --</option>
    <option value="barang_habis_pakai">🟢 Barang Habis Pakai (Permintaan)</option>
    <option value="barang_pinjam">🔵 Barang Pinjam (Peminjaman)</option>
</select>
```

**3. Dynamic Barang Dropdown**
- Otomatis update berdasarkan sub_kategori yang dipilih
- Mengambil data dari hidden containers terpisah

**4. Tanggal Section (Conditional)**
- Hanya muncul jika sub_kategori = `barang_pinjam`
- Wajib diisi untuk barang pinjam
- Tersembunyi untuk barang habis pakai

#### JavaScript Functions

**updateBarangList()**
```javascript
function updateBarangList() {
    const subKategori = document.getElementById('sub_kategori').value;
    const barangSelect = document.getElementById('idbarang');
    
    barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
    
    let barangData = [];
    if (subKategori === 'barang_habis_pakai') {
        barangData = JSON.parse(document.getElementById('barangHabisPakaiData').textContent);
    } else if (subKategori === 'barang_pinjam') {
        barangData = JSON.parse(document.getElementById('barangPinjamData').textContent);
    }
    
    barangData.forEach(barang => {
        const option = document.createElement('option');
        option.value = barang.idbarang;
        option.textContent = `${barang.namabarang} (${barang.kodebarang}) - Stok: ${barang.stock}`;
        option.dataset.stock = barang.stock;
        option.dataset.kodebarang = barang.kodebarang;
        option.dataset.sub_kategori = barang.sub_kategori;
        barangSelect.appendChild(option);
    });
}
```

**updateStockInfo()**
```javascript
function updateStockInfo() {
    // Show/hide tanggal section based on sub_kategori
    const selectedOption = barangSelect.options[barangSelect.selectedIndex];
    const subKategori = selectedOption?.dataset.sub_kategori;
    const tanggalSection = document.getElementById('tanggalSection');
    
    if (subKategori === 'barang_pinjam') {
        tanggalSection.style.display = 'block';
        tanggalPinjam.required = true;
        tanggalKembali.required = true;
    } else {
        tanggalSection.style.display = 'none';
        tanggalPinjam.required = false;
        tanggalKembali.required = false;
        tanggalPinjam.value = '';
        tanggalKembali.value = '';
    }
    
    // Update badge color
    const badgeColor = subKategori === 'barang_habis_pakai' ? 'green' : 'blue';
    // ... update UI
}
```

### 2. Index Request: `pemakaian/index.blade.php`

**Header Kolom:**
- Changed: "Kategori" & "Tipe" → "Sub-Kategori" (single column)

**Badge Display:**
```blade
@if($item->stock && $item->stock->sub_kategori)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
        {{ $item->sub_kategori_badge_color == 'green' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
        @if($item->stock->sub_kategori == 'barang_habis_pakai')
            <x-heroicon-o-archive-box class="w-3 h-3 mr-1" />
            Barang Habis Pakai
        @else
            <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
            Barang Pinjam
        @endif
    </span>
@else
    <span class="text-gray-400 text-xs">-</span>
@endif
```

### 3. Form Stok Admin: `admin/stok-barang/create.blade.php`

**Sub-Kategori Field (Conditional)**
```blade
<div id="subKategoriField" style="display: none;">
    <label for="sub_kategori">
        Sub-Kategori Material Umum <span class="text-red-500">*</span>
    </label>
    <select name="sub_kategori" id="sub_kategori">
        <option value="">-- Pilih Sub-Kategori --</option>
        <option value="barang_habis_pakai">Barang Habis Pakai</option>
        <option value="barang_pinjam">Barang Pinjam</option>
    </select>
    <p class="text-xs text-gray-500">
        Habis Pakai: tidak perlu dikembalikan | Pinjam: harus dikembalikan
    </p>
</div>
```

**JavaScript Toggle:**
```javascript
function toggleSubKategoriField() {
    const kategori = document.getElementById('kategori').value;
    const subKategoriField = document.getElementById('subKategoriField');
    const subKategoriSelect = document.getElementById('sub_kategori');
    
    if (kategori === 'habis_pakai') {
        subKategoriField.style.display = 'block';
        subKategoriSelect.required = true;
    } else {
        subKategoriField.style.display = 'none';
        subKategoriSelect.required = false;
        subKategoriSelect.value = '';
    }
}
```

---

## 📝 Data Status

### Check Command: `php artisan check:subkategori`

Output saat ini:
```
Checking sub_kategori in stock table...

Total Material Umum (habis_pakai): 2

Dengan sub_kategori: 0
Tanpa sub_kategori (NULL): 2

⚠️ Ada 2 barang Material Umum yang belum di-set sub_kategori!
Admin perlu mengatur sub_kategori (barang_habis_pakai atau barang_pinjam) 
di halaman master stok.
```

**Action Required:**
1. Admin login ke sistem
2. Buka halaman Master Stok
3. Edit setiap barang Material Umum yang belum memiliki sub_kategori
4. Pilih sub-kategori yang sesuai:
   - **Barang Habis Pakai:** untuk ATK, consumables
   - **Barang Pinjam:** untuk equipment yang bisa dipinjam

---

## 🧪 Testing Checklist

### ✅ Untuk Admin

- [ ] Buka form tambah stok baru
- [ ] Pilih kategori "Material Umum"
- [ ] Pastikan field Sub-Kategori muncul dan required
- [ ] Pilih sub-kategori "Barang Habis Pakai"
- [ ] Simpan dan verifikasi data tersimpan dengan benar
- [ ] Ulangi dengan sub-kategori "Barang Pinjam"
- [ ] Edit barang Material Umum yang sudah ada
- [ ] Set sub_kategori untuk barang yang NULL
- [ ] Verifikasi constraint database (coba input nilai invalid)

### ✅ Untuk User/Pegawai

- [ ] Login sebagai pegawai
- [ ] Buka menu "Pemakaian Saya" → "Ajukan Request Baru"
- [ ] Baca info box Material Umum
- [ ] Pilih sub-kategori "Barang Habis Pakai"
- [ ] Verifikasi: hanya barang habis pakai yang muncul di dropdown
- [ ] Verifikasi: section tanggal TIDAK muncul
- [ ] Isi qty, keperluan, penerima
- [ ] Submit request
- [ ] Verifikasi: success message "Permintaan barang habis pakai berhasil diajukan!"
- [ ] Cek halaman index: badge hijau "Barang Habis Pakai" muncul

- [ ] Ajukan request baru lagi
- [ ] Pilih sub-kategori "Barang Pinjam"
- [ ] Verifikasi: hanya barang pinjam yang muncul di dropdown
- [ ] Verifikasi: section tanggal MUNCUL dan required
- [ ] Isi tanggal pinjam dan tanggal kembali
- [ ] Submit request
- [ ] Verifikasi: success message "Peminjaman barang berhasil diajukan!"
- [ ] Cek halaman index: badge biru "Barang Pinjam" muncul

### ✅ Validasi Error

- [ ] Coba submit tanpa pilih sub-kategori
- [ ] Coba submit barang pinjam tanpa tanggal
- [ ] Coba submit dengan tanggal kembali < tanggal pinjam
- [ ] Verifikasi semua error message muncul dengan benar

---

## 🔐 Security & Data Integrity

### Database Level
- ✅ CHECK constraint memastikan hanya nilai valid yang bisa disimpan
- ✅ Migration aman: tidak mengubah data existing

### Application Level
- ✅ Validasi ketat di controller (required_if untuk Material Umum)
- ✅ Pengecekan kategori: pegawai tidak bisa request non-Material Umum
- ✅ Validasi sub_kategori sesuai dengan barang yang dipilih
- ✅ Conditional required untuk tanggal (hanya barang_pinjam)

### User Experience
- ✅ Info box menjelaskan perbedaan sub-kategori
- ✅ Form dinamis: hanya tampil field yang relevan
- ✅ Badge berwarna untuk identifikasi visual cepat
- ✅ Error message yang jelas dan informatif

---

## 📊 Mapping Tipe Request

### User Request → Database

| User Action | sub_kategori | tipe_request (DB) | Tanggal Required |
|-------------|-------------|-------------------|-----------------|
| Pilih "Barang Habis Pakai" | `barang_habis_pakai` | `pakai_habis_pakai` | ❌ Tidak |
| Pilih "Barang Pinjam" | `barang_pinjam` | `pinjam_material` | ✅ Ya |

### Database → Display Label

| tipe_request (DB) | Label Display |
|------------------|---------------|
| `pakai_habis_pakai` | Barang Habis Pakai |
| `pinjam_material` | Barang Pinjam |
| `pinjam_sewa` | Pinjam Aset Sewa (legacy) |

---

## 📁 File yang Diubah

### Backend
1. ✅ `database/migrations/2026_01_20_150000_add_sub_kategori_constraint_to_stock_table.php`
2. ✅ `app/Models/RequestBarang.php`
3. ✅ `app/Http/Controllers/User/PemakaianController.php`
4. ✅ `app/Http/Controllers/Admin/StokBarangController.php`
5. ✅ `app/Console/Commands/CheckSubKategori.php`

### Frontend
6. ✅ `resources/views/user/pemakaian/create.blade.php`
7. ✅ `resources/views/user/pemakaian/index.blade.php`
8. ✅ `resources/views/admin/stok-barang/create.blade.php`

### Model (Sudah Ada)
- ✅ `app/Models/Stock.php` - fillable sudah include `sub_kategori`

---

## 🚀 Next Steps

### Immediate (Required)
1. ⚠️ Admin harus mengatur sub_kategori untuk 2 barang Material Umum existing
2. ⚠️ Testing flow lengkap (admin create → user request → admin approve)

### Short Term (Recommended)
1. Create edit form untuk stok barang (saat ini hanya ada create form)
2. Add sub_kategori filter di halaman stok admin
3. Update laporan untuk menampilkan sub_kategori

### Long Term (Enhancement)
1. Dashboard statistics per sub-kategori
2. Export report dengan breakdown sub-kategori
3. Bulk update sub_kategori untuk data existing
4. History tracking perubahan sub_kategori

---

## 💡 Business Logic Summary

### Material Umum Hierarchy
```
Material Umum (kategori: habis_pakai)
├── Barang Habis Pakai (sub: barang_habis_pakai)
│   ├── Tipe: Permintaan
│   ├── Return: Tidak perlu dikembalikan
│   ├── Tanggal: Tidak diperlukan
│   └── Contoh: ATK, kertas, tinta
└── Barang Pinjam (sub: barang_pinjam)
    ├── Tipe: Peminjaman
    ├── Return: Harus dikembalikan
    ├── Tanggal: Wajib tanggal pinjam & kembali
    └── Contoh: Proyektor, kamera, tablet
```

### Access Control
```
PEGAWAI/USER:
✅ Dapat request: Material Umum (habis_pakai)
  ✅ Sub: Barang Habis Pakai
  ✅ Sub: Barang Pinjam
❌ Tidak dapat request: Aset Sewa, Aset Tetap

ADMIN:
✅ Manage semua kategori
✅ Set sub_kategori untuk Material Umum
✅ Approve/reject semua request
```

---

## ⚙️ Command Reference

```bash
# Check sub_kategori status
php artisan check:subkategori

# Clear all caches
php artisan optimize:clear

# Run specific migration
php artisan migrate --path=database/migrations/2026_01_20_150000_add_sub_kategori_constraint_to_stock_table.php

# Check migration status
php artisan migrate:status
```

---

## 📞 Support & Maintenance

**Developer Notes:**
- Implementasi mengikuti prinsip: "teliti dan benar" (careful and correct)
- Semua validasi berlapis: database constraint + application logic
- User experience diutamakan: form dinamis, info jelas, error handling baik
- Backward compatible: tidak merusak data existing, admin bisa update bertahap

**Maintenance:**
- Monitor sub_kategori NULL values via `check:subkategori` command
- Update documentation jika ada perubahan business logic
- Keep constraint in sync dengan application validation

---

✅ **Implementasi Selesai!**
📅 **Tanggal:** 20 Januari 2026
👨‍💻 **Developer:** GitHub Copilot (Expert Mode)
🎯 **Status:** Production Ready - Siap Testing
