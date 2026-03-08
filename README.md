# DSCMKids Website

Website sistem informasi Sekolah Minggu DSCMKids berbasis Laravel 11, dengan:

- Landing page premium (hero slider, grafik kehadiran bulat per kelas, galeri kegiatan).
- Admin panel CRUD untuk berita, informasi, konten section, dan media.
- Admin panel CRUD untuk slide header, portfolio guru, dan menu Live Streaming.
- Admin panel materi edukatif bertingkat + broadcast notifikasi.
- Integrasi database eksternal untuk metrik siswa, kehadiran, dan foto kegiatan.
- Renungan harian murid di landing page.
- Galeri khusus "Minggu Ini" dari foto selfie presensi (external DB).
- Testimonial publik + balasan admin.
- PWA support (installable) + sitemap SEO.

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

## Migrasi Wajib Setelah Update Fitur Baru

Jalankan:

```bash
php artisan migrate --force
```

Fitur yang butuh migrasi:
- testimonials + admin_reply
- learning_materials
- notification_broadcasts
- admin_activity_logs

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
SCHOOL_ATTENDANCE_STUDENT_ID_COLUMN=student_id
SCHOOL_ATTENDANCE_CLASS_COLUMN=class_name
SCHOOL_ATTENDANCE_PRESENT_VALUES=1,true,present,hadir
SCHOOL_CLASS_ROLLUP=PG,TKA,TKB,1,2,3,4,5,6

SCHOOL_GALLERY_TABLE=activity_photos
SCHOOL_GALLERY_TITLE_COLUMN=title
SCHOOL_GALLERY_PATH_COLUMN=file_path
SCHOOL_GALLERY_DATE_COLUMN=activity_date
SCHOOL_GALLERY_EVENT_COLUMN=event_name

# Galeri selfie presensi minggu berjalan
SCHOOL_WEEKLY_GALLERY_TABLE=attendances
SCHOOL_WEEKLY_GALLERY_PATH_COLUMN=selfie_path
SCHOOL_WEEKLY_GALLERY_DATE_COLUMN=attendance_date
SCHOOL_WEEKLY_GALLERY_STATUS_COLUMN=status
SCHOOL_WEEKLY_GALLERY_TITLE_COLUMN=
SCHOOL_WEEKLY_GALLERY_NAME_COLUMN=student_name
SCHOOL_WEEKLY_GALLERY_CLASS_COLUMN=class_name
SCHOOL_WEEKLY_GALLERY_ONLY_PRESENT=true
SCHOOL_WEEKLY_GALLERY_LIMIT=12
```

## Catatan Integrasi Galeri External

- Jika `file_path` berisi URL penuh (`http://` / `https://`) maka gambar dirender langsung.
- Jika external DB belum siap, landing page tetap hidup dengan fallback data lokal.

## SEO & Indexing

- `sitemap.xml` tersedia di `/sitemap.xml`
- `robots.txt` sudah menunjuk ke sitemap
- Meta OG/Twitter dan JSON-LD ditambahkan di halaman publik utama

## PWA

- Manifest: `/manifest.webmanifest`
- Service Worker: `/sw.js`

## Queue & Delivery

- Broadcast notifikasi sekarang diproses lewat queue background.
- Jika `QUEUE_CONNECTION=sync`, broadcast tetap berjalan langsung.
- Jika memakai `database` / worker queue, jalankan worker:

```bash
php artisan queue:work
```

## Broadcast Notifikasi (Opsional WhatsApp)

Set webhook (opsional) di `.env`:

```env
WHATSAPP_BROADCAST_WEBHOOK=
SCHOOL_DATA_CACHE_TTL=300
```

`SCHOOL_DATA_CACHE_TTL` mengurangi query berat ke external DB pada landing page.

## Keamanan Upload Media

- Admin media hanya menerima file: JPG, PNG, WEBP, GIF, MP4, dan PDF.
- Format aktif seperti SVG / HTML diblokir agar file publik tidak bisa dipakai untuk injeksi script.

## Struktur Utama

- Landing Controller: `app/Http/Controllers/LandingController.php`
- Service Analytics External: `app/Services/SchoolDataService.php`
- Landing View Premium: `resources/views/landing.blade.php`
- Admin CRUD Routes: `routes/web.php`
- Admin Hero Slide: `/admin/slides`
- Admin Portfolio Guru: `/admin/teachers`
- Admin Live Streaming: `/admin/livestream`
- Public List Berita: `/berita`
- Public Detail Berita: `/berita/{slug}`
- Public Detail Event Galeri: `/galeri/event/{eventSlug}`

## Fitur Publik Lanjutan

- Search berita server-side via query string: `/berita?q=kata-kunci`
- Highlight keyword otomatis pada hasil pencarian berita
- Galeri lightbox fullscreen dengan navigasi keyboard (`Esc`, `Left`, `Right`)
- Statistik event di halaman detail event galeri (jumlah foto, tanggal awal/terbaru)
- Layar theater live streaming YouTube di landing page

## Setup Live Streaming (Admin)

1. Buka admin: `Live Streaming`.
2. Isi judul, deskripsi, link YouTube, dan centang status live jika sedang siaran.
3. Simpan perubahan.

Alternatif manual tetap bisa lewat `Konten` dengan `section_key = livestream`.
Format `meta` JSON:

```json
{
  "youtube_url": "https://www.youtube.com/watch?v=YOUR_VIDEO_ID",
  "is_live": true
}
```

Mendukung format URL: `watch?v=...`, `youtu.be/...`, `shorts/...`, `live/...`, atau `embed/...`.
