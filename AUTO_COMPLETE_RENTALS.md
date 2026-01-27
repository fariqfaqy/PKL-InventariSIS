# Auto Complete Expired Rentals

## Fitur
Sistem otomatis menyelesaikan peminjaman (pinjam_material) yang sudah melewati tanggal pengembalian.

## Cara Kerja
1. **Scheduled Command**: `rentals:auto-complete` jalan setiap hari jam **00:05** (5 menit setelah midnight)
2. **Target**: Request dengan status `approved` dan `tipe_request` = `pinjam_material`
3. **Kondisi**: `tanggal_akhir_sewa < today`
4. **Aksi**:
   - Update status request: `approved` → `completed`
   - Update OutgoingTransaction: status `selesai`, set `tanggal_selesai`
   - Kembalikan stok: increment `stock` 
   - Update status kondisi: `tersedia`, keterangan: "Barang dikembalikan otomatis (expired)"
   - Auto-reject pending change requests
   - Tambah catatan admin tentang auto-completion

## Setup Cron Job

### Development (Local)
Jalankan Laravel Scheduler manually:
```bash
php artisan schedule:work
```

### Production (Server)
Tambahkan cron entry:
```bash
# Edit crontab
crontab -e

# Tambahkan baris ini (adjust path sesuai project location):
* * * * * cd /path/to/InventariSIS && php artisan schedule:run >> /dev/null 2>&1
```

Cron ini jalan setiap menit, tapi Laravel Scheduler akan handle kapan command sebenarnya dijalankan (00:05 daily).

## Manual Test
Test command secara manual:
```bash
php artisan rentals:auto-complete
```

Output contoh:
```
Checking for expired rental requests...
Found 2 expired rental(s). Processing...
✓ Completed: Request #15 - Laptop Dell (User: John Doe)
✓ Completed: Request #17 - Proyektor (User: Jane Smith)

=== Summary ===
Successfully completed: 2
```

## Log File
Output disimpan di: `storage/logs/auto-complete-rentals.log`

Check log:
```bash
tail -f storage/logs/auto-complete-rentals.log
```

## Benefit
- ✅ User tidak perlu manual mark as complete
- ✅ Stock otomatis kembali setelah tanggal expired
- ✅ Admin tidak perlu monitor manual
- ✅ Prevent stok stuck di status "sedang_dipakai"
- ✅ History tetap tercatat lengkap di catatan_admin

## Customization
Edit jam eksekusi di `routes/console.php`:
```php
// Ubah dari 00:05 ke jam lain, misal 01:00
Schedule::command('rentals:auto-complete')
    ->dailyAt('01:00')  // <- ubah di sini
    ->appendOutputTo(storage_path('logs/auto-complete-rentals.log'));
```

Pilihan schedule lain:
- `->hourly()` - Setiap jam
- `->daily()` - Setiap hari jam 00:00
- `->dailyAt('08:00')` - Setiap hari jam 08:00
- `->twiceDaily(1, 13)` - 2x sehari (jam 1 dan 13)
- `->weekdays()` - Hanya hari kerja
- `->cron('*/30 * * * *')` - Custom cron expression (setiap 30 menit)
