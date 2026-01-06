# InventariSIS - Sistem Informasi Inventaris

Proyek ini adalah sistem informasi manajemen inventaris yang dikembangkan untuk PKL PLN.

## Tim Pengembang
- Developer 1: [Nama Anda]
- Developer 2: [Nama Teman]

## Deskripsi Proyek
Aplikasi web untuk mengelola inventaris barang dengan pemisahan hak akses antara Admin dan User.

## Teknologi
- **Framework**: Laravel 12.44.0
- **PHP**: 8.2+
- **Database**: MySQL/PostgreSQL (production), SQLite (development)
- **Frontend**: Blade Templates, TailwindCSS (optional)

## Role Pengguna
1. **Admin** - Pengelola sistem dengan akses penuh
2. **User** - Pengguna dengan akses terbatas

## Quick Start
```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

## Dokumentasi Lengkap
- [Requirements & Fitur](REQUIREMENTS.md)
- [Setup Development](SETUP.md)
- [Database Schema](docs/DATABASE.md)

## License
Proprietary - PKL PLN Project

---

## About Laravel

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
