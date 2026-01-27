# ✅ IMPLEMENTATION CHECKLIST
# Database Migration Consolidation

## 📋 PRE-IMPLEMENTATION

### 1. Backup
- [ ] Backup database PostgreSQL
  ```powershell
  pg_dump -U postgres -h localhost -p 5433 inventarisis > backup_$(Get-Date -Format "yyyyMMdd_HHmmss").sql
  ```
- [ ] Backup folder migrations
  ```powershell
  Copy-Item -Path "database\migrations" -Destination "database\migrations_backup_manual" -Recurse
  ```
- [ ] Screenshot current database schema (optional)
- [ ] Commit semua perubahan ke Git (jika menggunakan version control)

### 2. Environment Check
- [ ] PostgreSQL service running
  ```powershell
  # Check service
  Get-Service | Where-Object {$_.Name -like "*postgre*"}
  
  # Start service (adjust name sesuai instalasi Anda)
  # Common names: postgresql-x64-16, PostgreSQL16, postgresql
  Start-Service postgresql-x64-16
  ```
- [ ] Database connection test
  ```powershell
  php artisan migrate:status
  ```
- [ ] No pending changes/uncommitted work

### 3. Konfirmasi Data
- [ ] Database masih development/testing (tidak ada data production penting)
- [ ] Atau: Semua data penting sudah di-backup
- [ ] Atau: Data bisa di-recreate dari seeder

---

## 🔧 IMPLEMENTATION STEPS

### Step 1: Stop Application
- [ ] Stop Laravel development server (jika running)
  ```powershell
  # Tekan Ctrl+C pada terminal yang menjalankan php artisan serve
  ```
- [ ] Close browser yang mengakses aplikasi
- [ ] Pastikan tidak ada proses yang mengakses database

### Step 2: Jalankan Consolidation Script
- [ ] Buka PowerShell sebagai Administrator
- [ ] Navigate ke project folder
  ```powershell
  cd "C:\Users\ASUS\Documents\PKL PLN\InventariSIS"
  ```
- [ ] Jalankan script konsolidasi
  ```powershell
  .\consolidate_migrations.ps1
  ```
- [ ] Konfirmasi dengan ketik "yes" saat diminta
- [ ] Tunggu hingga selesai
- [ ] Verifikasi output: harus ada "KONSOLIDASI SELESAI!"

### Step 3: Fresh Migration
- [ ] Reset database (HAPUS SEMUA DATA!)
  ```powershell
  php artisan migrate:fresh
  ```
- [ ] Verifikasi tidak ada error
- [ ] Check output: harus ada "Migration table created successfully"

### Step 4: Verify Schema
- [ ] Check tabel yang terbuat
  ```powershell
  php artisan migrate:status
  ```
  
  Expected output: Semua migration harus berstatus "Ran"
  
- [ ] Verify jumlah tabel di database
  ```sql
  # Connect ke PostgreSQL
  psql -U postgres -h localhost -p 5433 -d inventarisis
  
  # List semua tabel
  \dt
  
  # Expected tables:
  # - users
  # - cache
  # - cache_locks
  # - jobs
  # - job_batches
  # - failed_jobs
  # - sessions
  # - stock
  # - masuk
  # - keluar
  # - log
  # - rack_assignments
  # - request_barang
  # - divisions
  # - migrations
  ```

- [ ] Verify kolom di tabel stock
  ```sql
  \d stock
  
  # Expected columns:
  # - idbarang, namabarang, deskripsi, kodebarang, penginput
  # - stock, status_kondisi, keterangan_kondisi, tanggal_update_kondisi
  # - image, rack
  # - kategori, sub_kategori, jenis, merek, tipe
  # - durasi_sewa, tanggal_mulai_sewa, tanggal_akhir_sewa
  # - created_at, updated_at
  ```

### Step 5: Seed Database (Optional)
- [ ] Run seeder untuk data awal
  ```powershell
  php artisan db:seed
  ```
- [ ] Atau: Import data dari backup (jika perlu)
  ```powershell
  # Restore specific tables jika perlu
  ```

---

## 🧪 TESTING

### Database Testing
- [ ] Connect ke database via psql atau pgAdmin
- [ ] Check semua tabel exists
- [ ] Check foreign keys terpasang dengan benar
  ```sql
  # Check foreign keys
  SELECT
      tc.table_name, 
      kcu.column_name, 
      ccu.table_name AS foreign_table_name,
      ccu.column_name AS foreign_column_name 
  FROM information_schema.table_constraints AS tc 
  JOIN information_schema.key_column_usage AS kcu
    ON tc.constraint_name = kcu.constraint_name
  JOIN information_schema.constraint_column_usage AS ccu
    ON ccu.constraint_name = tc.constraint_name
  WHERE tc.constraint_type = 'FOREIGN KEY';
  ```
- [ ] Check constraints terpasang
  ```sql
  # Check constraints
  SELECT con.*
  FROM pg_catalog.pg_constraint con
  INNER JOIN pg_catalog.pg_class rel ON rel.oid = con.conrelid
  WHERE rel.relname = 'stock';
  ```

### Application Testing
- [ ] Start Laravel server
  ```powershell
  php artisan serve
  ```
- [ ] Access login page: http://localhost:8000
- [ ] Test login (buat user baru dulu jika perlu)
- [ ] Test Admin Features:
  - [ ] Dashboard loads correctly
  - [ ] Create stok barang
  - [ ] Create barang masuk
  - [ ] Create barang keluar
  - [ ] Create user
  - [ ] Create division
- [ ] Test User Features:
  - [ ] Dashboard loads correctly
  - [ ] View stok barang
  - [ ] Create request barang
  - [ ] View request history
- [ ] Check for any errors in:
  - [ ] Browser console
  - [ ] Laravel log: `storage/logs/laravel.log`

### CRUD Testing Matrix

| Feature | Create | Read | Update | Delete | Status |
|---------|--------|------|--------|--------|--------|
| Stok Barang | [ ] | [ ] | [ ] | [ ] | |
| Barang Masuk | [ ] | [ ] | [ ] | [ ] | |
| Barang Keluar | [ ] | [ ] | [ ] | [ ] | |
| Request Barang | [ ] | [ ] | [ ] | [ ] | |
| Users | [ ] | [ ] | [ ] | [ ] | |
| Divisions | [ ] | [ ] | [ ] | [ ] | |

---

## 🔍 TROUBLESHOOTING

### Error: "could not connect to server"
**Solution:**
```powershell
# Start PostgreSQL service
net start postgresql-x64-16
# Atau check service name dulu
Get-Service | Where-Object {$_.Name -like "*postgre*"}
```

### Error: "SQLSTATE[42P07]: Duplicate table"
**Solution:**
```powershell
# Drop all tables and start fresh
php artisan migrate:fresh
```

### Error: "Class 'xxx' not found"
**Solution:**
```powershell
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload
```

### Error: Foreign key constraint fails
**Solution:**
- Check urutan migration files (must be in correct order)
- Make sure parent tables created before child tables

### Migration stuck / timeout
**Solution:**
```powershell
# Increase PHP timeout
# Edit php.ini:
# max_execution_time = 300
# Atau jalankan dengan:
php -d max_execution_time=300 artisan migrate:fresh
```

---

## ✅ POST-IMPLEMENTATION

### Cleanup
- [ ] Hapus folder migrations_consolidated (jika tidak diperlukan)
  ```powershell
  Remove-Item -Path "database\migrations_consolidated" -Recurse -Force
  ```
- [ ] Keep backup folder untuk berjaga-jaga
- [ ] Update dokumentasi project (jika ada)
- [ ] Commit perubahan ke Git
  ```powershell
  git add database/migrations
  git commit -m "Consolidate database migrations from 32 to 10 files"
  git push
  ```

### Documentation Update
- [ ] Update README.md jika perlu
- [ ] Inform team members (jika ada)
- [ ] Update deployment documentation
- [ ] Note down new migration count untuk reference

### Performance Check
- [ ] Measure migration time
  ```powershell
  Measure-Command { php artisan migrate:fresh }
  ```
- [ ] Compare dengan waktu migration sebelumnya
- [ ] Document improvement (should be faster)

---

## 📊 SUCCESS CRITERIA

Konsolidasi dianggap berhasil jika:
- [x] Total migration files: 10 files
- [x] `php artisan migrate:fresh` berjalan tanpa error
- [x] `php artisan migrate:status` menunjukkan semua migration "Ran"
- [x] Semua tabel database terbuat dengan benar
- [x] Semua foreign keys dan constraints berfungsi
- [x] Aplikasi berjalan normal (login, CRUD, dll)
- [x] Tidak ada error di `storage/logs/laravel.log`
- [x] Migration time lebih cepat dari sebelumnya

---

## 🎉 COMPLETION

Jika semua checklist di atas sudah ✅, maka:

**SELAMAT! Database Anda sudah ter-konsolidasi dengan sukses!** 🎊

Benefits yang Anda dapatkan:
- ✅ 68% fewer migration files
- ✅ Lebih mudah di-maintain
- ✅ Lebih cepat di-deploy
- ✅ Schema lebih jelas dan terdokumentasi

---

## 📝 NOTES

Tanggal implementasi: ___________________  
Waktu mulai: ___________________  
Waktu selesai: ___________________  
Total durasi: ___________________  

Migration time:
- Before: ___________________
- After: ___________________
- Improvement: ___________________

Issues encountered (if any):
_____________________________________________
_____________________________________________
_____________________________________________

Resolution:
_____________________________________________
_____________________________________________
_____________________________________________
