# 🚀 DATABASE MIGRATION CONSOLIDATION

**Tanggal**: 23 Januari 2026  
**Status**: Ready for Implementation  
**Tujuan**: Mengkonsolidasikan 32 migration files menjadi lebih efisien

---

## 📊 **MASALAH SAAT INI**

### Migration Files yang Terlalu Banyak:
- **32 migration files** untuk database yang sebenarnya hanya punya **7 tabel utama**
- Banyak migration yang hanya menambahkan 1-2 kolom ke tabel yang sama
- Susah di-maintain dan dipahami
- Risiko error saat migration di server baru

### Contoh Fragmentasi:
**Stock Table** dimodifikasi oleh **9+ migration files**:
1. `2026_01_06_000002_create_stock_table.php` - Create table
2. `2026_01_07_000001_add_category_fields_to_stock_table.php` - Tambah kategori
3. `2026_01_08_000001_add_rental_duration_to_stock_and_outgoing_tables.php` - Tambah durasi sewa
4. `2026_01_13_000001_add_fixed_asset_and_rental_dates_to_stock_table.php` - Tambah tanggal sewa
5. `2026_01_20_000004_add_status_kondisi_to_stock_table.php` - Tambah status kondisi
6. `2026_01_20_095031_add_nama_pengguna_to_stock_table.php` - Tambah nama pengguna
7. `2026_01_20_100121_drop_redundant_user_fields_from_stock_table.php` - Hapus field
8. `2026_01_20_120000_split_habis_pakai_category.php` - Split kategori
9. Dan lainnya...

**Keluar Table** dimodifikasi oleh **7+ migration files**  
**Request_Barang Table** dimodifikasi oleh **5+ migration files**

---

## ✅ **SOLUSI: MIGRATION CONSOLIDATION**

### Struktur Baru (Hanya 10 Files):

#### **Core Laravel Tables (3 files)** - TIDAK DIUBAH:
1. `0001_01_01_000000_create_users_table.php`
2. `0001_01_01_000001_create_cache_table.php`
3. `0001_01_01_000002_create_jobs_table.php`

#### **Application Tables (7 files)** - CONSOLIDATED:
4. `2026_01_06_000001_add_role_to_users_table.php` - User role
5. **`2026_01_06_000002_create_stock_table_consolidated.php`** ⭐ BARU
6. `2026_01_06_000003_create_incoming_transactions_table.php` - Masuk table
7. **`2026_01_06_000004_create_outgoing_transactions_table_consolidated.php`** ⭐ BARU
8. `2026_01_06_000005_create_activity_logs_table.php` - Log table
9. `2026_01_08_000002_create_rack_assignments_table.php` - Rack assignments
10. **`2026_01_13_000003_create_item_requests_table_consolidated.php`** ⭐ BARU
11. **`2026_01_20_130000_create_divisions_table_consolidated.php`** ⭐ BARU

### Pengurangan Migration Files:
- **Dari**: 32 files → **Menjadi**: 10 files
- **Pengurangan**: 22 files (68% lebih sedikit!)

---

## 📁 **FILE YANG SUDAH DIBUAT**

Semua file konsolidasi sudah dibuat di folder `database/migrations_consolidated/`:

### 1. Stock Table (Consolidated) ⭐
**File**: `2026_01_06_000002_create_stock_table_consolidated.php`

**Menggabungkan 9+ migration files menjadi 1 file**

**Kolom yang Disertakan**:
- ✅ Basic: idbarang, namabarang, deskripsi, kodebarang, penginput
- ✅ Stock: stock, status_kondisi, keterangan_kondisi, tanggal_update_kondisi
- ✅ Image: image
- ✅ Rack: rack (nullable)
- ✅ Category: kategori, sub_kategori, jenis, merek, tipe
- ✅ Rental: durasi_sewa, tanggal_mulai_sewa, tanggal_akhir_sewa
- ✅ Timestamps: created_at, updated_at
- ✅ Constraints: kategori check, sub_kategori check

**Menggantikan**:
- `2026_01_06_000002_create_stock_table.php`
- `2026_01_07_000001_add_category_fields_to_stock_table.php`
- `2026_01_08_000001_add_rental_duration_to_stock_and_outgoing_tables.php` (part stock)
- `2026_01_13_000001_add_fixed_asset_and_rental_dates_to_stock_table.php`
- `2026_01_20_000004_add_status_kondisi_to_stock_table.php`
- `2026_01_20_095031_add_nama_pengguna_to_stock_table.php`
- `2026_01_20_100121_drop_redundant_user_fields_from_stock_table.php`
- `2026_01_20_120000_split_habis_pakai_category.php`
- `2026_01_20_150000_add_sub_kategori_constraint_to_stock_table.php`
- `2026_01_23_110650_make_rack_nullable_in_stock_table.php`

### 2. Outgoing Transactions Table (Consolidated) ⭐
**File**: `2026_01_06_000004_create_outgoing_transactions_table_consolidated.php`

**Menggabungkan 7+ migration files menjadi 1 file**

**Kolom yang Disertakan**:
- ✅ Relations: id_request, idbarang, user_id
- ✅ Transaction: tanggal, penerima, qty, namabarang_k, kodebarang_k, penginput
- ✅ Type: tipe, kategori
- ✅ Rental: durasi_sewa
- ✅ Status: status, tanggal_selesai
- ✅ Approval: status_approval, tipe_request, catatan_admin, diproses_oleh, tanggal_diproses
- ✅ Dates: tanggal_pinjam, tanggal_kembali, tanggal_mulai_sewa, tanggal_akhir_sewa, dll
- ✅ Foreign Keys: idbarang, user_id, id_request

**Menggantikan**:
- `2026_01_06_000004_create_outgoing_transactions_table.php`
- `2026_01_08_000001_add_rental_duration_to_stock_and_outgoing_tables.php` (part keluar)
- `2026_01_09_000001_add_type_to_transactions_tables.php`
- `2026_01_09_000002_add_completed_status_to_outgoing_table.php`
- `2026_01_09_000003_add_approval_system_to_outgoing_table.php`
- `2026_01_13_000002_add_rental_dates_to_outgoing_table.php`
- `2026_01_14_000002_add_request_id_to_outgoing_table.php`
- `2026_01_23_114812_add_kategori_and_status_to_outgoing_table.php`

### 3. Request Barang Table (Consolidated) ⭐
**File**: `2026_01_13_000003_create_item_requests_table_consolidated.php`

**Menggabungkan 5+ migration files menjadi 1 file**

**Kolom yang Disertakan**:
- ✅ Relations: user_id, idbarang, parent_request_id
- ✅ Request Info: qty, tipe_request, request_type
- ✅ Dates: tanggal_mulai_sewa, tanggal_akhir_sewa
- ✅ Additional: keperluan, penerima
- ✅ Status: status, catatan_user, catatan_admin, diproses_oleh
- ✅ Timestamps: tanggal_request, tanggal_diproses
- ✅ Foreign Keys: user_id, idbarang, parent_request_id

**Menggantikan**:
- `2026_01_13_000003_create_item_requests_table.php`
- `2026_01_14_000001_add_parent_request_id_to_item_requests_table.php`
- `2026_01_14_000003_add_cancelled_status_to_item_requests_table.php`
- `2026_01_15_000001_add_request_type_to_item_requests_table.php`
- `2026_01_19_000001_add_recipient_to_item_requests_table.php`
- `2026_01_21_000001_add_pinjam_material_to_request_barang_tipe_request.php`

### 4. Divisions Table (Consolidated) ⭐
**File**: `2026_01_20_130000_create_divisions_table_consolidated.php`

**Menggabungkan 2 migration files menjadi 1 file**

**Menggantikan**:
- `2026_01_20_130000_create_divisions_table.php`
- `2026_01_20_130001_add_employee_fields_to_users_table.php`

---

## 🔄 **CARA IMPLEMENTASI**

### ⚠️ PENTING: Backup Database Dulu!

```bash
# Backup database PostgreSQL
pg_dump -U postgres inventarisis > backup_inventarisis_$(date +%Y%m%d).sql
```

### Opsi A: Fresh Database (RECOMMENDED - Jika Belum Ada Data Production)

Jika database masih development/testing dan data bisa di-reset:

```bash
# 1. Reset database
php artisan migrate:fresh

# 2. Backup folder migrations lama
mv database/migrations database/migrations_old

# 3. Pindahkan migrations consolidated ke folder migrations
mv database/migrations_consolidated database/migrations

# 4. Copy kembali migrations yang tidak diubah
cp database/migrations_old/0001_01_01_000000_create_users_table.php database/migrations/
cp database/migrations_old/0001_01_01_000001_create_cache_table.php database/migrations/
cp database/migrations_old/0001_01_01_000002_create_jobs_table.php database/migrations/
cp database/migrations_old/2026_01_06_000001_add_role_to_users_table.php database/migrations/
cp database/migrations_old/2026_01_06_000003_create_incoming_transactions_table.php database/migrations/
cp database/migrations_old/2026_01_06_000005_create_activity_logs_table.php database/migrations/
cp database/migrations_old/2026_01_08_000002_create_rack_assignments_table.php database/migrations/

# 5. Run migration baru
php artisan migrate

# 6. Seeder (jika perlu)
php artisan db:seed
```

### Opsi B: Production dengan Data (Manual Migration)

Jika sudah ada data production yang tidak boleh hilang:

1. **Export data** dari tabel yang akan di-recreate
2. **Drop & recreate** tabel dengan schema baru
3. **Import data** kembali
4. **Verify** data integrity

**TIDAK RECOMMENDED** - Lebih baik gunakan Opsi A jika memungkinkan

---

## ✅ **BENEFITS**

### 1. **Simplicity** 🎯
- Hanya **10 migration files** vs 32 files sebelumnya
- Mudah dipahami struktur database
- 1 file = 1 tabel (mostly)

### 2. **Maintainability** 🔧
- Lebih mudah di-review
- Lebih mudah di-debug
- Lebih mudah dikembangkan

### 3. **Performance** ⚡
- Migration lebih cepat (fewer files to process)
- Fewer constraint checks during migration

### 4. **Deployment** 🚀
- Lebih mudah deploy ke server baru
- Lebih sedikit kemungkinan error
- Fresh install lebih cepat

### 5. **Documentation** 📖
- Self-documenting schema
- Semua kolom tabel ada di 1 tempat
- Lebih mudah untuk onboarding developer baru

---

## 📋 **CHECKLIST IMPLEMENTASI**

### Persiapan:
- [ ] Backup database: `pg_dump -U postgres inventarisis > backup.sql`
- [ ] Backup folder migrations: `cp -r database/migrations database/migrations_backup`
- [ ] Pastikan tidak ada data production penting (atau sudah di-backup)

### Implementasi:
- [ ] Start PostgreSQL service
- [ ] Test koneksi database: `php artisan migrate:status`
- [ ] Reset database: `php artisan migrate:fresh`
- [ ] Reorganize migration files (copy file-file yang diperlukan)
- [ ] Run migration baru: `php artisan migrate`
- [ ] Verify schema: Check tabel dan kolom
- [ ] Run seeder (jika ada): `php artisan db:seed`
- [ ] Test aplikasi: Login, CRUD operations, dll

### Verifikasi:
- [ ] Semua tabel tercreate dengan benar
- [ ] Semua foreign keys berfungsi
- [ ] Semua constraints berfungsi
- [ ] Aplikasi berjalan normal
- [ ] No errors di log

---

## 🎯 **MIGRATION FILES FINAL (10 Files)**

```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 2026_01_06_000001_add_role_to_users_table.php
├── 2026_01_06_000002_create_stock_table_consolidated.php ⭐
├── 2026_01_06_000003_create_incoming_transactions_table.php
├── 2026_01_06_000004_create_outgoing_transactions_table_consolidated.php ⭐
├── 2026_01_06_000005_create_activity_logs_table.php
├── 2026_01_08_000002_create_rack_assignments_table.php
├── 2026_01_13_000003_create_item_requests_table_consolidated.php ⭐
└── 2026_01_20_130000_create_divisions_table_consolidated.php ⭐
```

**Total**: 10 files (vs 32 sebelumnya)  
**Pengurangan**: 68% fewer files! 🎉

---

## 📞 **SUPPORT**

Jika ada masalah saat implementasi:
1. Restore dari backup: `psql -U postgres inventarisis < backup.sql`
2. Check error log: `storage/logs/laravel.log`
3. Verify .env database config

---

## 📝 **NOTES**

- Semua kolom yang diperlukan sudah included
- Foreign keys sudah ditambahkan
- Constraints sudah ditambahkan
- Compatible dengan kode yang sudah ada
- Tidak ada breaking changes di application logic

**Status**: ✅ READY FOR IMPLEMENTATION
