# DSCMKids Website

Website sistem informasi Sekolah Minggu DSCMKids berbasis Laravel 11, dengan:

- Landing page premium (analytics, grafik kehadiran, galeri kegiatan).
- Admin panel CRUD untuk berita, informasi, konten section, dan media.
- Integrasi database eksternal untuk metrik siswa, kehadiran, dan foto kegiatan.

## Requirement

- PHP 8.2+
- Composer
- MySQL/MariaDB (untuk koneksi external analytics)

## Setup Local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Admin Default

- URL: `/admin/login`
- Email: `admin@dscmkids.org`
- Password: `password123`

## Konfigurasi Database External

Set di `.env`:

```env
EXTERNAL_DB_CONNECTION=mysql
EXTERNAL_DB_HOST=127.0.0.1
EXTERNAL_DB_PORT=3306
EXTERNAL_DB_DATABASE=external_school
EXTERNAL_DB_USERNAME=root
EXTERNAL_DB_PASSWORD=

SCHOOL_DATA_CONNECTION=external
SCHOOL_STUDENTS_TABLE=students
SCHOOL_STUDENTS_ACTIVE_COLUMN=is_active
SCHOOL_STUDENTS_CLASS_COLUMN=class_name

SCHOOL_ATTENDANCE_TABLE=attendances
SCHOOL_ATTENDANCE_DATE_COLUMN=attendance_date
SCHOOL_ATTENDANCE_STATUS_COLUMN=status
SCHOOL_ATTENDANCE_PRESENT_VALUES=1,true,present,hadir

SCHOOL_GALLERY_TABLE=activity_photos
SCHOOL_GALLERY_TITLE_COLUMN=title
SCHOOL_GALLERY_PATH_COLUMN=file_path
SCHOOL_GALLERY_DATE_COLUMN=activity_date
```

## Catatan Integrasi Galeri External

- Jika `file_path` berisi URL penuh (`http://` / `https://`) maka gambar dirender langsung.
- Jika external DB belum siap, landing page tetap hidup dengan fallback data lokal.

## Struktur Utama

- Landing Controller: `app/Http/Controllers/LandingController.php`
- Service Analytics External: `app/Services/SchoolDataService.php`
- Landing View Premium: `resources/views/landing.blade.php`
- Admin CRUD Routes: `routes/web.php`
