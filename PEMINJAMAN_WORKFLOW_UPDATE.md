# 🔄 UPDATE WORKFLOW PEMINJAMAN MATERIAL UMUM

## 📋 Perubahan Workflow

### ❌ Workflow LAMA:
```
1. User request barang pinjam (pinjam_material)
2. Admin approve
   └─ ✅ Auto-create OutgoingTransaction
   └─ ✅ Auto-kurangi stok
   └─ Status: approved
3. User bisa complete/extend rental
```

### ✅ Workflow BARU:
```
1. User request barang pinjam (pinjam_material)
2. Admin approve
   └─ ❌ TIDAK create OutgoingTransaction
   └─ ❌ TIDAK kurangi stok
   └─ Status: approved
   └─ Muncul di "Peminjaman yang Menunggu Dikeluarkan"
3. Admin input barang keluar (manual)
   └─ Buka detail stok barang
   └─ Lihat daftar peminjaman pending
   └─ Klik "Keluarkan Barang"
   └─ ✅ Create OutgoingTransaction
   └─ ✅ Kurangi stok
4. User/Admin bisa complete/extend rental
```

---

## 🎯 Tujuan Perubahan

**Masalah Lama:**
- Stok langsung berkurang saat approve, padahal barang belum tentu sudah diambil
- Admin tidak punya kontrol kapan barang benar-benar keluar
- Tidak ada tracking peminjaman yang sudah disetujui tapi belum dikeluarkan

**Solusi Baru:**
- Approve hanya memberi persetujuan, TIDAK langsung kurangi stok
- Admin yang kontrol kapan barang benar-benar dikeluarkan
- Ada tracking peminjaman pending di detail stok barang
- Stok berkurang hanya saat admin input barang keluar

---

## 📍 Fitur yang Ditambahkan

### 1️⃣ **Section "Peminjaman yang Menunggu Dikeluarkan"**

**Lokasi:** Detail Stok Barang (khusus Material Umum - Barang Pinjam)

**Tampil Kapan:**
- Kategori: Material Umum
- Sub-kategori: Barang Pinjam
- Ada peminjaman dengan status `approved` yang belum ada OutgoingTransaction

**Informasi yang Ditampilkan:**
- Nama peminjam + divisi
- Qty yang diminta
- Periode peminjaman (tanggal mulai - akhir)
- Tanggal & petugas yang approve
- Tombol "Keluarkan Barang"

**Cara Menggunakan:**
1. Admin buka detail stok barang (Material Umum - Barang Pinjam)
2. Scroll ke section "Peminjaman yang Menunggu Dikeluarkan"
3. Lihat daftar peminjaman yang sudah disetujui
4. Klik "Keluarkan Barang" ketika barang benar-benar diberikan
5. Konfirmasi → Stok berkurang → Barang keluar tercatat

### 2️⃣ **Method `issueRental()` di StokBarangController**

**Fungsi:** Mencatat barang keluar dari peminjaman yang sudah disetujui

**Validasi:**
- Request harus tipe `pinjam_material`
- Status harus `approved`
- Belum ada OutgoingTransaction untuk request ini
- Stok harus cukup

**Proses:**
1. Lock request dan stock
2. Validate
3. Kurangi stok
4. Create OutgoingTransaction dengan status `sedang_dipakai`
5. Link ke RequestBarang via `id_request`

**Route:** `POST admin/stok-barang/issue-rental/{id_request}`

---

## 🔧 Perubahan Kode

### 1. PermintaanController@approve

**File:** `app/Http/Controllers/Admin/PermintaanController.php`

**Perubahan:**
```php
// SEBELUM: Auto-create OutgoingTransaction untuk semua request
if ($permintaan->tipe_request === 'pinjam_material') {
    Stock::decrement('stock', $qty);
    OutgoingTransaction::create([...]);
    $status = 'approved';
}

// SESUDAH: Hanya approve, TIDAK create OutgoingTransaction
if ($permintaan->tipe_request === 'pinjam_material') {
    // Hanya update status
    $permintaan->update(['status' => 'approved']);
    $message = 'Permintaan peminjaman disetujui! Silakan input barang keluar dari detail stok barang ketika barang benar-benar dikeluarkan.';
}
```

**Note:** Barang habis pakai tetap langsung create OutgoingTransaction dan kurangi stok.

### 2. StokBarangController@show

**File:** `app/Http/Controllers/Admin/StokBarangController.php`

**Penambahan:**
```php
// Load pending rentals
$pendingRentals = RequestBarang::with(['user.division'])
    ->where('idbarang', $idbarang)
    ->where('tipe_request', 'pinjam_material')
    ->where('status', 'approved')
    ->whereDoesntHave('outgoingTransaction')
    ->orderBy('tanggal_diproses', 'desc')
    ->get();

return view('admin.stok-barang.show', compact('stock', 'pendingRentals'));
```

### 3. StokBarangController@issueRental (BARU)

**Method baru untuk input barang keluar:**
```php
public function issueRental($id_request)
{
    DB::beginTransaction();
    try {
        // Validate dan lock
        $rental = RequestBarang::lockForUpdate()->findOrFail($id_request);
        
        // Validate belum ada transaksi
        if (OutgoingTransaction::where('id_request', $id_request)->exists()) {
            return error('Sudah dikeluarkan');
        }
        
        // Kurangi stok
        Stock::decrement('stock', $rental->qty);
        
        // Create OutgoingTransaction
        OutgoingTransaction::create([...]);
        
        DB::commit();
        return success();
    } catch (...) {
        DB::rollBack();
        return error();
    }
}
```

### 4. View: show.blade.php

**File:** `resources/views/admin/stok-barang/show.blade.php`

**Penambahan Section:**
```blade
@if($stock->kategori === 'material_umum' && $stock->sub_kategori === 'barang_pinjam' && $pendingRentals->count() > 0)
<div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
    <h3>Peminjaman yang Menunggu Dikeluarkan</h3>
    
    <!-- Info box -->
    <div class="bg-orange-50">
        Peminjaman sudah disetujui. Klik "Keluarkan Barang" untuk mencatat barang keluar.
    </div>
    
    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>Peminjam</th>
                <th>Qty</th>
                <th>Periode</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingRentals as $rental)
            <tr>
                <td>{{ $rental->user->name }}</td>
                <td>{{ $rental->qty }}</td>
                <td>{{ $rental->tanggal_mulai_sewa }} - {{ $rental->tanggal_akhir_sewa }}</td>
                <td>
                    <form action="{{ route('admin.stok-barang.issue-rental', $rental->id_request) }}" method="POST">
                        @csrf
                        <button type="submit">Keluarkan Barang</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
```

### 5. Routes

**File:** `routes/web.php`

**Penambahan:**
```php
Route::post('stok-barang/issue-rental/{id_request}', [StokBarangController::class, 'issueRental'])
    ->name('stok-barang.issue-rental');
```

---

## 📊 Alur Data

### Request Peminjaman Material Umum:

```
┌─────────────────────────┐
│ User Request            │
│ tipe: pinjam_material   │
│ status: pending         │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ Admin Approve           │ ← PERUBAHAN DI SINI
│ status: approved        │   TIDAK create OutgoingTransaction
│ OutgoingTransaction: ❌ │   TIDAK kurangi stok
│ Stok: TETAP             │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ Tampil di Detail Stok   │
│ "Pending Rentals"       │
│ - Nama peminjam         │
│ - Qty                   │
│ - Periode               │
│ - Tombol "Keluarkan"    │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ Admin Klik "Keluarkan"  │ ← MANUAL ACTION
│ StokBarangController@   │
│ issueRental()           │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ Create OutgoingTrans    │
│ Kurangi Stok            │
│ status: sedang_dipakai  │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ Barang Keluar Tercatat  │
│ Hilang dari Pending     │
│ Muncul di History       │
└─────────────────────────┘
```

---

## 🎨 Screenshot UI (Konseptual)

### Detail Stok Barang - Section Pending Rentals:

```
┌──────────────────────────────────────────────────────────┐
│ 🕐 Peminjaman yang Menunggu Dikeluarkan             [3]  │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ ℹ️ Peminjaman Sudah Disetujui                           │
│ Klik "Keluarkan Barang" untuk mencatat barang keluar    │
│ ketika barang benar-benar diberikan ke peminjam.        │
│ Stok akan berkurang saat barang dikeluarkan.            │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ No │ Peminjam      │ Qty    │ Periode      │ Aksi      │
├────┼───────────────┼────────┼──────────────┼───────────┤
│ 1  │ John Doe      │ 5 unit │ 01/02 - 05/02│ [Keluarkan]│
│    │ IT Division   │        │ (4 hari)     │            │
├────┼───────────────┼────────┼──────────────┼───────────┤
│ 2  │ Jane Smith    │ 3 unit │ 02/02 - 10/02│ [Keluarkan]│
│    │ Finance Div   │        │ (8 hari)     │            │
└────┴───────────────┴────────┴──────────────┴───────────┘
```

---

## 🔒 Validasi & Keamanan

### Validasi di issueRental():

✅ Request harus tipe `pinjam_material`
✅ Status harus `approved`
✅ Belum ada OutgoingTransaction (prevent double issue)
✅ Stok cukup untuk qty diminta
✅ Database locking untuk prevent race condition
✅ Transaction rollback jika error

### Error Messages:

| Kondisi | Pesan Error |
|---------|-------------|
| Sudah pernah dikeluarkan | "Barang sudah pernah dikeluarkan untuk peminjaman ini!" |
| Stok tidak cukup | "Stok tidak mencukupi! Stok tersedia: X, diminta: Y" |
| Tipe request salah | "Request not found" (404) |

---

## 📝 Migration Requirements

**Tidak ada migration baru!** Menggunakan struktur database yang sudah ada:

- `request_barang` → status `approved` tanpa OutgoingTransaction
- `outgoing_transactions` → dibuat manual oleh admin
- `stock` → berkurang saat admin issue rental

---

## ✅ Testing Checklist

### Test Scenario 1: Approve Peminjaman
- [ ] User request barang pinjam
- [ ] Admin approve
- [ ] Verify: Status = `approved`
- [ ] Verify: TIDAK ada OutgoingTransaction
- [ ] Verify: Stok TIDAK berkurang

### Test Scenario 2: Lihat Pending Rentals
- [ ] Buka detail stok barang (Material Umum - Barang Pinjam)
- [ ] Verify: Section "Peminjaman yang Menunggu Dikeluarkan" tampil
- [ ] Verify: Data peminjam, qty, periode tampil benar
- [ ] Verify: Tombol "Keluarkan Barang" ada

### Test Scenario 3: Issue Rental
- [ ] Klik "Keluarkan Barang"
- [ ] Konfirmasi
- [ ] Verify: OutgoingTransaction dibuat
- [ ] Verify: Stok berkurang sesuai qty
- [ ] Verify: Rental hilang dari pending list
- [ ] Verify: Muncul di history barang keluar

### Test Scenario 4: Validasi
- [ ] Coba issue rental 2x (harus error)
- [ ] Coba issue dengan stok tidak cukup (harus error)
- [ ] Approve peminjaman dengan stok tidak cukup untuk future issue

### Test Scenario 5: Barang Habis Pakai (tetap seperti dulu)
- [ ] User request barang habis pakai
- [ ] Admin approve
- [ ] Verify: Langsung create OutgoingTransaction
- [ ] Verify: Langsung kurangi stok
- [ ] Verify: Status = `completed`

---

## 🚨 Breaking Changes

### Impact pada Workflow Lama:

**TIDAK ADA** breaking changes untuk barang habis pakai.

**ADA** perubahan workflow untuk barang pinjam:
- Admin harus manual input barang keluar
- Stok tidak langsung berkurang saat approve

### Migration dari Workflow Lama:

Jika ada data peminjaman approved yang sudah ada sebelum update:
1. Peminjaman yang sudah ada OutgoingTransaction → tetap normal
2. Peminjaman approved tanpa OutgoingTransaction → muncul di pending rentals
3. Admin bisa issue manual atau biarkan (user bisa complete nanti)

---

## 💡 Tips Penggunaan

### Untuk Admin:

1. **Approve dengan Bijak**
   - Approve hanya memberi persetujuan
   - Cek stok sebelum approve (masih ada validasi)
   - Barang belum keluar sampai di-issue

2. **Monitor Pending Rentals**
   - Buka detail stok barang secara berkala
   - Lihat peminjaman yang menunggu
   - Issue segera saat barang diberikan

3. **Workflow Ideal**
   ```
   User request → Admin review & approve → 
   User datang ambil barang → Admin issue rental (klik "Keluarkan Barang") →
   Barang keluar tercatat + stok berkurang
   ```

### Untuk User:

Tidak ada perubahan dari sisi user. User tetap:
1. Request barang
2. Tunggu approval
3. Datang ambil barang (admin yang input)
4. Complete atau extend rental

---

## 📞 Support

**Jika ada pertanyaan:**
- Workflow baru terasa rumit? → Ini memberikan kontrol lebih ke admin
- Lupa issue rental? → Pending rentals akan selalu tampil di detail stok
- Stok tidak berkurang? → Normal, tunggu admin issue rental

---

**Created:** 2 Februari 2026  
**Version:** 2.0  
**Author:** GitHub Copilot
