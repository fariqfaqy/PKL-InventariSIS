# 📖 DOKUMENTASI ALUR BISNIS ASET SEWA

## 📋 Konsep Dasar

**Aset Sewa** adalah barang yang disewakan kepada user tertentu dengan karakteristik:
- ✅ **1 Kode Barang = 1 Item Fisik Unik** (tidak bisa digabung seperti Material Umum)
- ✅ **Qty SELALU = 1** per aset sewa
- ✅ **Langsung diassign ke user** saat input barang masuk
- ✅ **Punya tanggal mulai & akhir pakai**
- ✅ **Tracking status kondisi** (tersedia/digunakan/rusak/hilang)

---

## 🔄 ALUR LENGKAP ASET SEWA

### 1️⃣ **BARANG MASUK (Input Aset Sewa Baru)**

**Lokasi:** `BarangMasukController@store`

**Input Form:**
```
- Kode Barang: "LAPTOP-001" (UNIK - tidak boleh duplikat)
- Nama: "Laptop Lenovo ThinkPad X1"
- Kategori: "aset_sewa"
- User: "John Doe" (WAJIB dipilih)
- Tanggal Mulai Pakai: 2026-01-15
- Tanggal Akhir Pakai: 2026-12-15
- Qty: otomatis dipaksa = 1
```

**Proses di Sistem:**

1. **Validasi Kode Unik:**
   ```php
   if ($actualKategori === 'aset_sewa') {
       if ($stock) {
           // TOLAK - kode sudah ada!
           return back()->with('error', 'Kode sudah terdaftar...');
       }
   }
   ```

2. **Buat Stock Baru:**
   ```php
   Stock::create([
       'kodebarang' => 'LAPTOP-001',
       'stock' => 1,  // SELALU 1
       'kategori' => 'aset_sewa',
       'status_kondisi' => 'digunakan',  // Langsung dipakai
   ]);
   ```

3. **Buat IncomingTransaction:**
   ```php
   IncomingTransaction::create([
       'idbarang' => $stock->idbarang,
       'qty' => 1,
       'tanggal' => now(),
   ]);
   ```

4. **Otomatis Buat OutgoingTransaction (Assignment):**
   ```php
   OutgoingTransaction::create([
       'idbarang' => $stock->idbarang,
       'user_id' => $validated['user_id'],
       'kategori' => 'aset_sewa',
       'status' => 'sedang_dipakai',
       'tanggal_mulai_pakai' => '2026-01-15',
       'tanggal_akhir_pakai' => '2026-12-15',
   ]);
   ```

---

### 2️⃣ **STATUS KONDISI ASET SEWA**

| Status | Keterangan |
|--------|------------|
| `tersedia` | Tidak sedang dipakai, siap diassign ke user lain |
| `digunakan` | Sedang dipakai oleh user tertentu |
| `diperbaiki` | Dalam perbaikan/maintenance |
| `rusak` | Kondisi rusak, tidak bisa dipakai |
| `hilang` | Barang hilang |

**Update Status:**
- Otomatis berubah saat assignment/return
- Bisa diubah manual oleh admin

---

### 3️⃣ **RETURN ASET SEWA**

**Lokasi:** `BarangKeluarController@return` (belum ada? perlu dibuat)

**Proses:**
1. User/Admin melakukan return
2. Update `OutgoingTransaction`:
   ```php
   $keluar->status = 'selesai';
   $keluar->tanggal_selesai = now();
   ```
3. Update `Stock`:
   ```php
   $stock->status_kondisi = 'tersedia';
   ```

---

## 🗄️ STRUKTUR DATABASE

### **Table: stock**

**Field Penting untuk Aset Sewa:**
```sql
idbarang              BIGINT PRIMARY KEY
kodebarang            VARCHAR(255) UNIQUE  -- ⚠️ HARUS UNIK!
kategori              ENUM('aset_sewa', 'material_umum', 'aset_tetap')
stock                 INTEGER DEFAULT 0    -- SELALU 1 untuk aset_sewa
status_kondisi        ENUM('tersedia', 'digunakan', 'diperbaiki', 'rusak', 'hilang')
durasi_sewa           INTEGER NULL         -- Durasi sewa dalam bulan
tanggal_mulai_sewa    DATE NULL
tanggal_akhir_sewa    DATE NULL
```

**Constraints:**
```sql
-- Unique kodebarang
ALTER TABLE stock ADD CONSTRAINT stock_kodebarang_unique UNIQUE(kodebarang);

-- Aset sewa harus stock = 1
ALTER TABLE stock ADD CONSTRAINT check_aset_sewa_qty 
CHECK (kategori != 'aset_sewa' OR stock = 1);

-- Index untuk performa
CREATE INDEX idx_stock_kategori_status ON stock(kategori, status_kondisi);
```

---

### **Table: keluar (OutgoingTransaction)**

**Field untuk Tracking Aset Sewa:**
```sql
idkeluar              BIGINT PRIMARY KEY
idbarang              BIGINT FOREIGN KEY -> stock.idbarang
user_id               BIGINT FOREIGN KEY -> users.id
kategori              VARCHAR -- 'aset_sewa'
status                ENUM('sedang_dipakai', 'selesai')
tanggal_mulai_pakai   DATE NULL
tanggal_akhir_pakai   DATE NULL
tanggal_selesai       TIMESTAMP NULL  -- Kapan dikembalikan
qty                   INTEGER         -- SELALU 1 untuk aset_sewa
```

**Foreign Keys:**
```sql
keluar_idbarang_foreign: idbarang -> stock.idbarang (cascade delete)
keluar_user_id_foreign: user_id -> users.id (set null on delete)
```

---

### **Table: masuk (IncomingTransaction)**

**Field Standard:**
```sql
idmasuk               BIGINT PRIMARY KEY
idbarang              BIGINT FOREIGN KEY -> stock.idbarang
qty                   INTEGER  -- SELALU 1 untuk aset_sewa
tanggal               TIMESTAMP
keterangan            TEXT
```

---

## ✅ VALIDASI & CONSTRAINT

### **1. Di Application Level (Laravel)**

**BarangMasukController:**
```php
// Validasi: aset_sewa WAJIB punya user_id
'user_id' => 'required_if:kategori,aset_sewa|exists:users,id'

// Validasi: tanggal akhir >= tanggal mulai
'tanggal_akhir_pakai' => 'after_or_equal:tanggal_mulai_pakai'

// Force qty = 1
if ($kategori === 'aset_sewa') {
    $validated['qty'] = 1;
}

// Tolak jika kode sudah ada
if ($stock && $kategori === 'aset_sewa') {
    return back()->with('error', 'Kode sudah terdaftar...');
}
```

---

### **2. Di Database Level (PostgreSQL)**

**Constraint yang sudah diterapkan:**
```sql
-- ✅ Kode barang UNIQUE
stock_kodebarang_unique

-- ✅ Qty aset_sewa harus = 1
check_aset_sewa_qty

-- ✅ Foreign key cascade
keluar_idbarang_foreign (ON DELETE CASCADE)
masuk_idbarang_foreign (ON DELETE CASCADE)

-- ✅ Index untuk performa
idx_stock_kategori_status
```

---

## 🔍 QUERY PENTING

### **Cek Aset Sewa yang Sedang Dipakai:**
```sql
SELECT 
    s.kodebarang,
    s.namabarang,
    u.name as user_name,
    k.tanggal_mulai_pakai,
    k.tanggal_akhir_pakai,
    s.status_kondisi
FROM stock s
JOIN keluar k ON s.idbarang = k.idbarang
JOIN users u ON k.user_id = u.id
WHERE s.kategori = 'aset_sewa' 
  AND k.status = 'sedang_dipakai'
ORDER BY k.tanggal_mulai_pakai DESC;
```

### **Cek Aset Sewa yang Tersedia:**
```sql
SELECT 
    kodebarang,
    namabarang,
    status_kondisi,
    rack
FROM stock
WHERE kategori = 'aset_sewa' 
  AND status_kondisi = 'tersedia'
ORDER BY namabarang;
```

### **History Aset Sewa per User:**
```sql
SELECT 
    s.kodebarang,
    s.namabarang,
    k.tanggal_mulai_pakai,
    k.tanggal_akhir_pakai,
    k.tanggal_selesai,
    k.status
FROM keluar k
JOIN stock s ON k.idbarang = s.idbarang
WHERE k.user_id = :user_id 
  AND k.kategori = 'aset_sewa'
ORDER BY k.tanggal_mulai_pakai DESC;
```

---

## 🐛 BUG YANG SUDAH DIPERBAIKI

### ❌ **SEBELUM:**
```php
// SALAH - bisa bikin duplikat!
if ($actualKategori === 'aset_sewa') {
    $stock = null; // Force create new
}
```

**Dampak:** Bisa membuat 2 stock dengan kodebarang yang sama!

### ✅ **SESUDAH:**
```php
// BENAR - tolak jika kode sudah ada
if ($actualKategori === 'aset_sewa') {
    if ($stock) {
        return back()->with('error', 'Kode sudah terdaftar...');
    }
    $stock = null; // Force create new only if not exists
}
```

---

## 📊 FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────┐
│         ADMIN INPUT ASET SEWA BARU                  │
└─────────────┬───────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────┐
│  Validasi: Kode LAPTOP-001 sudah ada?               │
│  • Sudah ada → TOLAK ❌                             │
│  • Belum ada → Lanjut ✅                            │
└─────────────┬───────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────┐
│  1. Buat Stock Baru                                 │
│     - kodebarang: LAPTOP-001                        │
│     - stock: 1                                      │
│     - status_kondisi: 'digunakan'                   │
└─────────────┬───────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────┐
│  2. Buat IncomingTransaction                        │
│     - Catat barang masuk                            │
└─────────────┬───────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────┐
│  3. Otomatis Buat OutgoingTransaction               │
│     - Assign ke user John Doe                       │
│     - status: 'sedang_dipakai'                      │
│     - tanggal_mulai_pakai: 2026-01-15               │
│     - tanggal_akhir_pakai: 2026-12-15               │
└─────────────┬───────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────┐
│  SELESAI ✅                                         │
│  User John Doe sekarang menggunakan LAPTOP-001      │
└─────────────────────────────────────────────────────┘
```

---

## 🎯 KESIMPULAN

**Aset Sewa** adalah barang inventaris dengan karakteristik khusus:

1. ✅ **Kode UNIQUE** - Tidak boleh duplikat
2. ✅ **Qty = 1** - Selalu satu item per kode
3. ✅ **Auto-assign** - Langsung diassign ke user saat input
4. ✅ **Tracking lengkap** - User, tanggal mulai/akhir, status kondisi
5. ✅ **Database constraint** - Validasi di level aplikasi & database

**Database sudah optimal** dengan:
- Foreign keys yang benar
- Index untuk performa
- Constraint untuk data integrity
- Unique constraint pada kodebarang

---

## 📝 TODO (Opsional)

- [ ] Buat fitur **Return Aset Sewa** (if not exists)
- [ ] Buat notifikasi **aset sewa akan berakhir**
- [ ] Dashboard **tracking aset sewa per user**
- [ ] Report **history aset sewa**
- [ ] Validasi **aset tidak bisa dihapus jika sedang dipakai**

---

**Tanggal Dibuat:** 29 Januari 2026  
**Versi:** 1.0  
**Status:** ✅ Production Ready
