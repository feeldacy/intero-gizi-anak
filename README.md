# NutriTrack & HealthMap API

## Tentang Proyek

Ini adalah RESTful API berbasis Laravel yang digunakan sebagai backend untuk platform **NutriTrack & HealthMap**. API ini menangani proses pendaftaran admin, login/logout, manajemen data anak, pencatatan gizi, dan pemantauan status anak bergizi buruk di berbagai unit Posyandu.

## Teknologi yang Digunakan

- Laravel 11.0
- Laravel Sanctum (autentikasi token)
- Spatie Laravel Permission (role-based access control)
- MySQL / PostgreSQL (opsional)
- PHP 8.2

## Fitur Utama

### Autentikasi & Role

- Login admin (NutriTrack & HealthMap)
- Logout
- Registrasi admin berdasarkan peran

### Manajemen Data Anak

- Tambah, ubah, hapus, dan lihat data anak
- Monitoring data anak berdasarkan unit Posyandu

### NutriTrack

- Tambah dan update catatan gizi
- Lihat riwayat gizi anak
- Rekap gizi anak dalam satu unit Posyandu

### HealthMap

- Notifikasi data anak bergizi buruk
- Ringkasan data gizi anak
- Statistik data anak bergizi buruk per kecamatan

## Instalasi & Setup

```bash
# Clone repositori
git clone https://github.com/feeldacy/intero-gizi-anak.git
cd intero-gizi-anak

# Install dependensi
composer install

# Salin file environment
cp .env.example .env

# Generate key dan migrasi database
php artisan key:generate
php artisan migrate --seed

# Jalankan server lokal
php artisan serve
```

## Role & Hak Akses

| Role              | Akses Fitur                                     |
|-------------------|--------------------------------------------------|
| `nutritrackAdmin` | Login, CRUD catatan gizi, tambah/ubah data anak |
| `healthmapAdmin`  | Notifikasi data anak bergizi buruk, statistik gizi           |

## Ringkasan Endpoint API

### Autentikasi

- `POST /api/login`
- `POST /api/logout`

### Registrasi Admin

- `POST /api/register/nutritrack/admin`
- `POST /api/register/healthmap/admin`

### Data Anak

- `GET    /api/monitoring/child-data/get`
- `GET    /api/monitoring/child-data/show/{childId}`
- `POST   /api/monitoring/child-data/create`
- `PUT    /api/monitoring/child-data/update/{childId}`
- `DELETE /api/monitoring/child-data/delete/{childId}`

### Catatan Gizi (NutriTrack)

- `GET    /api/nutritrack/nutrition-record/`
- `GET    /api/nutritrack/nutrition-record/by-posyandu/{unitId}`
- `GET    /api/nutritrack/nutrition-record/child/{childId}`
- `GET    /api/nutritrack/nutrition-record/child-history/{childId}`
- `POST   /api/nutritrack/nutrition-record/create`
- `POST   /api/nutritrack/nutrition-record/update/{nutritionRecordId}`
- `DELETE /api/nutritrack/nutrition-record/delete/{nutritionRecordId}`

### HealthMap

- `GET /api/healthmap/malnutrition`
- `GET /api/healthmap/malnutrition/posyandu/{posyanduId}`
- `GET /api/healthmap/nutrition-record/summary`
- `GET /api/healthmap/malnutrition/kecamatan`

## Rencana Pengembangan

- Integrasi upload gambar anak & grafik pertumbuhan
- Validasi tambahan untuk data gizi
- Logging dan audit trail
- Dokumentasi Swagger / Postman
- Dashboard manajemen user (Admin Superuser)

## Kontribusi

Kontribusi sangat terbuka!  
Langkah kontribusi:

1. Fork repo ini
2. Buat branch baru (`git checkout -b fitur-anda`)
3. Commit perubahan (`git commit -m 'Menambahkan fitur ...'`)
4. Push ke branch Anda (`git push origin fitur-anda`)
5. Buat Pull Request

## Lisensi

Proyek ini dilisensikan dengan **MIT License**.

## Kontak

**Teknologi Rekayasa Perangkat Lunak**  
Departemen Teknik Elektro dan Informatika
Universitas Gadjah Mada
