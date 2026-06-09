# Sistem Absensi Digital - SMK Taruna Bangsa

Platform absensi digital modern yang dirancang khusus untuk mempermudah pemantauan kehadiran siswa dan guru di SMK Taruna Bangsa. Dilengkapi dengan teknologi QR Code untuk akurasi dan kecepatan.

## Fitur Utama
- **Cepat & Akurat**: Menggunakan metode Scan QR Code.
- **Real-time Report**: Pantau data absensi secara langsung.
- **Mobile Friendly**: Bisa diakses dari berbagai perangkat.
- **Admin Dashboard**: Kelola data siswa, guru, jadwal, dan rekap absensi harian dengan mudah.

## Screenshots Aplikasi

### 1. Landing Page
![Landing Page](docs/images/landing-page.png)

### 2. Login Admin
![Login Admin](docs/images/login-admin.png)

### 3. Admin Dashboard
![Admin Dashboard](docs/images/admin-dashboard.png)

### 4. Rekap Absensi
![Rekap Absensi](docs/images/rekap-absensi.png)

### 5. Fitur Lainnya 1 (Silakan Ubah Judul)
![Fitur 1](docs/images/fitur-5.png)

### 6. Fitur Lainnya 2 (Silakan Ubah Judul)
![Fitur 2](docs/images/fitur-6.png)

### 7. Fitur Lainnya 3 (Silakan Ubah Judul)
![Fitur 3](docs/images/fitur-7.png)

## Instalasi (Development)

1. Clone repository ini:
   ```bash
   git clone <URL_REPOSITORY_ANDA>
   ```
2. Copy `.env.example` ke `.env` dan sesuaikan konfigurasi database:
   ```bash
   cp .env.example .env
   ```
3. Install dependensi Composer dan NPM:
   ```bash
   composer install
   npm install
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```
6. Jalankan server lokal:
   ```bash
   php artisan serve
   npm run dev
   ```

## Lisensi
Hak Cipta &copy; 2026 SMK Taruna Bangsa. All rights reserved.
