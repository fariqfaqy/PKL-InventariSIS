# Setup & Development Guide

## 🔧 KEBUTUHAN SISTEM

### Software Requirements
- **PHP**: 8.2 atau lebih tinggi
- **Composer**: Latest version
- **Node.js & NPM**: v18+ (untuk asset compilation)
- **Database**: MySQL 8.0+ / PostgreSQL 13+ / SQLite
- **Web Server**: Apache/Nginx (production) atau PHP built-in server (development)

### PHP Extensions Required
```
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GD atau Imagick (untuk image processing)
- ZIP
```

### Cek PHP Extensions
```bash
php -m
```

---

## 🚀 INSTALASI PROJECT

### 1. Clone & Setup
```bash
# Sudah ada di folder ini
cd "c:\Users\ASUS\Documents\PKL PLN\InventariSIS"

# Install PHP dependencies
composer install

# Install Node dependencies (jika menggunakan Vite/Mix)
npm install
```

### 2. Environment Configuration
```bash
# Copy .env file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Setup

#### Konfigurasi .env untuk MySQL
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventarisis
DB_USERNAME=root
DB_PASSWORD=
```

#### Atau SQLite untuk Development
```env
DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite
```

### 4. Migration & Seeding
```bash
# Jalankan migrations
php artisan migrate

# Jalankan seeders (nanti dibuat)
php artisan db:seed
```

---

## 👥 PEMBAGIAN TUGAS (Saran)

### Developer 1 - Backend Focus
- [ ] Authentication & Authorization
- [ ] User Management
- [ ] Item Management
- [ ] Category Management
- [ ] Location Management
- [ ] API Development
- [ ] Database design & migrations

### Developer 2 - Frontend & Feature Focus
- [ ] UI/UX Design
- [ ] Borrowing System
- [ ] Dashboard & Statistics
- [ ] Reports & Export
- [ ] Notifications
- [ ] Search & Filter Features

### Bersama
- [ ] Database Schema Design
- [ ] Testing
- [ ] Deployment
- [ ] Documentation

---

## 📦 PACKAGE YANG DIBUTUHKAN

### Authentication & Authorization
```bash
# Laravel Breeze (Recommended untuk starter)
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
php artisan migrate

# Atau Laravel Jetstream (lebih lengkap)
# composer require laravel/jetstream
# php artisan jetstream:install livewire
```

### UI & Frontend
```bash
# Jika belum ada Tailwind (breeze sudah include)
npm install -D tailwindcss postcss autoprefixer

# Icons
npm install @heroicons/vue
# atau
npm install lucide-react
```

### Form & Validation
```bash
# Form Request Validation (sudah built-in Laravel)

# Laravel Collective Forms (optional)
composer require laravelcollective/html
```

### Export & Reports
```bash
# Export Excel
composer require maatwebsite/excel

# PDF Generation
composer require barryvdh/laravel-dompdf
```

### Image Upload & Processing
```bash
# Image Intervention
composer require intervention/image
```

### Notifications
```bash
# Email (sudah built-in)
# Queue untuk background jobs
# Setup queue driver di .env: QUEUE_CONNECTION=database
php artisan queue:table
php artisan migrate
```

### Development Tools
```bash
# Laravel Debugbar
composer require barryvdh/laravel-debugbar --dev

# IDE Helper
composer require --dev barryvdh/laravel-ide-helper

# Laravel Telescope (monitoring)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Testing
```bash
# PHPUnit (sudah included)
# Pest (modern testing)
composer require pestphp/pest --dev --with-all-dependencies
composer require pestphp/pest-plugin-laravel --dev
php artisan pest:install
```

---

## 🛠️ DEVELOPMENT WORKFLOW

### 1. Jalankan Development Server
```bash
# Laravel Server
php artisan serve
# Akses: http://localhost:8000

# Jika pakai Vite (frontend assets)
npm run dev
```

### 2. Queue Worker (untuk background jobs)
```bash
php artisan queue:work
```

### 3. Watch Logs
```bash
# Terminal baru
tail -f storage/logs/laravel.log
```

---

## 📝 STRUKTUR PROJECT

```
InventariSIS/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   ├── User/           # User controllers
│   │   │   └── Auth/           # Authentication
│   │   ├── Middleware/
│   │   └── Requests/           # Form requests
│   ├── Models/
│   ├── Policies/               # Authorization policies
│   └── Services/               # Business logic
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── admin/              # Admin views
│   │   ├── user/               # User views
│   │   ├── layouts/
│   │   └── components/
│   ├── js/
│   └── css/
├── routes/
│   ├── web.php                 # Web routes
│   └── api.php                 # API routes
├── public/
│   └── storage/                # Symlink ke storage
└── storage/
    └── app/
        └── public/
            └── uploads/        # Upload files
```

---

## 🔑 FIRST STEPS - CHECKLIST

### Setup Awal
- [ ] Install Composer dependencies
- [ ] Install NPM dependencies
- [ ] Setup .env file
- [ ] Generate application key
- [ ] Configure database
- [ ] Run migrations

### Authentication
- [ ] Install Laravel Breeze/Jetstream
- [ ] Customize authentication views
- [ ] Add role field to users table
- [ ] Create middleware untuk role

### Database Design
- [ ] Buat ERD (Entity Relationship Diagram)
- [ ] Buat migrations untuk semua tables
- [ ] Buat models dengan relationships
- [ ] Buat factories untuk testing
- [ ] Buat seeders untuk dummy data

### Layout & Components
- [ ] Setup admin layout
- [ ] Setup user layout
- [ ] Buat reusable components
- [ ] Setup navigation menu
- [ ] Setup dashboard structure

---

## 🧪 TESTING

### Run Tests
```bash
# PHPUnit
php artisan test

# Pest
php artisan test --pest

# Dengan coverage
php artisan test --coverage
```

### Testing Best Practices
- Buat test untuk setiap feature
- Test authentication & authorization
- Test CRUD operations
- Test business logic
- Test validations

---

## 📚 RESOURCES & DOKUMENTASI

### Laravel Documentation
- https://laravel.com/docs
- https://laracasts.com (video tutorials)

### UI Components
- https://tailwindui.com
- https://flowbite.com
- https://daisyui.com

### Icons
- https://heroicons.com
- https://lucide.dev

---

## 🤝 GIT WORKFLOW

### Branch Strategy
```bash
# Main branches
main/master     # Production
development     # Development

# Feature branches
feature/auth
feature/items-crud
feature/borrowing-system

# Bugfix branches
bugfix/login-issue
```

### Commit Convention
```
feat: Add user authentication
fix: Fix borrowing date validation
docs: Update README
style: Format code
refactor: Refactor item service
test: Add borrowing tests
```

### Workflow
```bash
# Buat branch baru
git checkout -b feature/nama-fitur

# Commit changes
git add .
git commit -m "feat: Add item management"

# Push ke remote
git push origin feature/nama-fitur

# Merge ke development
git checkout development
git merge feature/nama-fitur
```

---

## ⚠️ TROUBLESHOOTING

### Common Issues

#### 1. Class not found
```bash
composer dump-autoload
```

#### 2. Permission denied (storage/logs)
```bash
# Windows
icacls "storage" /grant Everyone:(OI)(CI)F /T
icacls "bootstrap/cache" /grant Everyone:(OI)(CI)F /T
```

#### 3. Vite not running
```bash
npm install
npm run dev
```

#### 4. Database connection error
- Cek credentials di .env
- Pastikan MySQL service running
- Cek port yang digunakan

---

## 📞 CONTACT & SUPPORT

- Developer 1: [Email/Phone]
- Developer 2: [Email/Phone]
- Supervisor: [Email/Phone]

---

**Happy Coding! 🚀**
