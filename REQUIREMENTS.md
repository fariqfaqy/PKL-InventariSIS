# Requirements & Fitur Sistem

## 🔐 ROLE: ADMIN

### 1. Manajemen User
- [ ] Tambah user baru
- [ ] Edit data user
- [ ] Hapus user
- [ ] Lihat daftar semua user
- [ ] Assign role (admin/user)
- [ ] Reset password user
- [ ] Aktifkan/Nonaktifkan akun user

### 2. Manajemen Kategori Barang
- [ ] Tambah kategori baru
- [ ] Edit kategori
- [ ] Hapus kategori
- [ ] Lihat daftar kategori

### 3. Manajemen Barang/Inventaris
- [ ] Tambah barang baru
- [ ] Edit detail barang
- [ ] Hapus barang
- [ ] Lihat semua daftar barang
- [ ] Upload foto barang
- [ ] Set stok barang
- [ ] Set lokasi penyimpanan
- [ ] Set kondisi barang (Baik/Rusak/Dalam Perbaikan)

### 4. Manajemen Peminjaman
- [ ] Approve/Reject pengajuan peminjaman
- [ ] Lihat history semua peminjaman
- [ ] Update status peminjaman
- [ ] Perpanjang masa peminjaman
- [ ] Tandai barang dikembalikan
- [ ] Catat kondisi barang saat dikembalikan
- [ ] Beri sanksi jika terlambat

### 5. Manajemen Lokasi/Ruangan
- [ ] Tambah lokasi penyimpanan
- [ ] Edit lokasi
- [ ] Hapus lokasi
- [ ] Assign barang ke lokasi

### 6. Laporan & Monitoring
- [ ] Dashboard dengan statistik lengkap
- [ ] Laporan barang tersedia
- [ ] Laporan barang dipinjam
- [ ] Laporan barang rusak
- [ ] Laporan peminjaman per periode
- [ ] Laporan per kategori
- [ ] Export laporan (Excel/PDF)
- [ ] Grafik statistik inventaris

### 7. Pengaturan Sistem
- [ ] Setting general aplikasi
- [ ] Setting durasi peminjaman default
- [ ] Setting denda keterlambatan
- [ ] Setting notifikasi
- [ ] Backup database
- [ ] Activity log

---

## 👤 ROLE: USER

### 1. Autentikasi
- [ ] Login
- [ ] Logout
- [ ] Edit profil sendiri
- [ ] Ganti password
- [ ] Lupa password (reset via email)

### 2. Browse Barang
- [ ] Lihat daftar barang tersedia
- [ ] Search barang
- [ ] Filter berdasarkan kategori
- [ ] Filter berdasarkan lokasi
- [ ] Lihat detail barang
- [ ] Lihat ketersediaan barang

### 3. Peminjaman
- [ ] Ajukan peminjaman barang
- [ ] Lihat status pengajuan peminjaman
- [ ] Lihat history peminjaman sendiri
- [ ] Perpanjang peminjaman (request)
- [ ] Notifikasi saat pengajuan disetujui/ditolak
- [ ] Notifikasi reminder pengembalian

### 4. Dashboard User
- [ ] Lihat barang yang sedang dipinjam
- [ ] Lihat tanggal pengembalian
- [ ] Lihat denda (jika ada)

---

## 📊 FITUR UMUM

### Notifikasi
- [ ] Email notification
- [ ] In-app notification
- [ ] Notifikasi peminjaman baru (ke admin)
- [ ] Notifikasi approval/rejection (ke user)
- [ ] Reminder pengembalian (H-1 dan hari H)
- [ ] Notifikasi keterlambatan

### Security
- [ ] Authentication (Laravel Breeze/Jetstream)
- [ ] Authorization (Gates & Policies)
- [ ] CSRF Protection
- [ ] XSS Prevention
- [ ] SQL Injection Prevention
- [ ] Rate Limiting

### UI/UX
- [ ] Responsive design (mobile-friendly)
- [ ] Loading states
- [ ] Error handling
- [ ] Success messages
- [ ] Confirmation dialogs
- [ ] Pagination
- [ ] Sorting & Filtering

---

## 🗄️ ENTITAS DATABASE

### Users
- id, name, email, password, role, phone, address, avatar, is_active

### Categories
- id, name, description, slug

### Items (Barang)
- id, category_id, name, code, description, quantity, available_quantity, location_id, condition, photo, purchase_date, price

### Locations
- id, name, description, building, floor

### Borrowings (Peminjaman)
- id, user_id, item_id, quantity, borrow_date, return_date, actual_return_date, status, notes, approved_by, approved_at

### Borrowing_Items (Detail Peminjaman)
- id, borrowing_id, item_id, quantity, condition_before, condition_after

### Notifications
- id, user_id, title, message, type, read_at

### Activity_Logs
- id, user_id, action, model, model_id, details

---

## 📅 PRIORITAS DEVELOPMENT

### Sprint 1 - Foundation (Week 1)
1. Setup project & database
2. Authentication system
3. Migration & seeding
4. Dashboard layout

### Sprint 2 - Admin Features (Week 2-3)
1. CRUD User
2. CRUD Kategori
3. CRUD Barang
4. CRUD Lokasi

### Sprint 3 - Borrowing System (Week 3-4)
1. Form pengajuan peminjaman
2. Approval system
3. Return system
4. History peminjaman

### Sprint 4 - Advanced Features (Week 5)
1. Notifikasi system
2. Laporan & export
3. Dashboard statistics
4. Search & filter

### Sprint 5 - Polish & Testing (Week 6)
1. UI/UX improvements
2. Testing
3. Bug fixes
4. Documentation
