# Safety Walk and Talk

Sistem pelaporan masalah safety dan 5S untuk lingkungan produksi Siemens.

## Fitur Utama

- Pelaporan masalah dengan foto
- Penugasan otomatis ke supervisor
- Tracking status penyelesaian
- Filter dan pencarian laporan
- Riwayat laporan lengkap
- Notifikasi email
- Responsive design

## Tech Stack

- Laravel 10
- MySQL 8
- Bootstrap 5
- DataTables
- SweetAlert2

## Instalasi

1. Clone repository
```bash
git clone https://github.com/rasyadocad/walkandtalk.git
```

2. Install dependencies
```bash 
composer install
npm install
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations
```bash
php artisan migrate --seed
```

5. Start development server
```bash
php artisan serve
```
