# RFID Attendance System

Sistem absensi berbasis RFID untuk manajemen kehadiran siswa dan guru di sekolah menggunakan CodeIgniter 4.

## Fitur Utama

- **Manajemen Siswa**: Tambah, edit, hapus data siswa dengan pendaftaran kartu RFID
- **Manajemen Perangkat**: Registrasi perangkat RFID dengan autentikasi token
- **Manajemen Sesi**: Buat sesi check-in dan check-out untuk pencatatan kehadiran
- **Pencatatan Kehadiran**: Otomatis via RFID atau manual input
- **Absensi Guru**: Pelacakan terpisah untuk kehadiran guru
- **Excel Import/Export**: Impor data siswa dan sesi, ekspor laporan kehadiran
- **API Perangkat**: Endpoint untuk perangkat RFID mengirim data scan
- **Panel Admin**: Dashboard admin dengan autentikasi

## Persyaratan Server

- PHP 8.1 atau lebih tinggi dengan ekstensi berikut:
  - intl
  - mbstring
  - json
  - mysqlnd
  - libcurl

- MySQL/MariaDB
- Web server (Apache/Nginx)

## Instalasi

1. Clone repository:
```bash
git clone <repository-url>
cd absensi-rfid
```

2. Install dependencies:
```bash
composer install
```

3. Setup environment:
```bash
cp env .env
```

4. Konfigurasi `.env` sesuai lingkungan Anda:
```ini
app.baseURL = 'http://localhost:8080/'
database.default.hostname = localhost
database.default.database = absensi_rfid
database.default.username = root
database.default.password = password
database.default.DBDriver = MySQLi
```

5. Konfigurasi web server untuk mengarah ke folder `public/`

6. Jalankan migrasi database:
```bash
spark migrate
```

7. Jalankan seeder (opsional):
```bash
spark db:seed Admin
spark db:seed Student
spark db:seed Session
spark db:seed DeviceRfid
```

## Struktur Database

### Tabel Utama

- **students**: Data siswa (id, nis, name, gender, classroom, rfid)
- **devices**: Perangkat RFID (id, name, token)
- **sessions**: Sesi absensi (id, name, mode, criterion_time)
- **att_records**: Catatan kehadiran (id, student_id, session_id, logged_at, device_id)
- **teachers_logs**: Log kehadiran guru (id, student_id, logged_at, device_id)
- **rfid_tmp**: Buffer data RFID terbaru
- **admins**: Data admin (id, username, password)

## API Endpoint

### RFID Device API

- **GET** `/rfid/read/{device_id}?token={token}&rfid={rfid}` - Kirim data scan RFID
- **GET** `/rfid/getCurrent` - Ambil data RFID terbaru

### Admin API

- **GET/POST** `/admin/*` - Manajemen admin dan dashboard
- **GET/POST** `/admin/siswa/*` - Manajemen siswa
- **GET/POST** `/admin/sesi/*` - Manajemen sesi absensi
- **GET/POST** `/admin/device/*` - Manajemen perangkat

### Excel API

- **POST** `/excel/importSiswa` - Impor data siswa dari Excel
- **POST** `/excel/importSesi` - Impor data sesi dari Excel
- **GET** `/excel/export` - Ekspor laporan kehadiran

## Penggunaan

### Mendaftarkan Perangkat RFID

1. Buka menu Device di panel admin
2. Tambah perangkat baru dengan nama dan token unik
3. Simpan token untuk konfigurasi di perangkat fisik

### Mendaftarkan Siswa

1. Tambah siswa secara manual melalui panel admin atau impor dari Excel
2. Set kartu RFID dengan menekan tombol "Set RFID" pada daftar siswa
3. Scan kartu RFID pada perangkat untuk mendaftarkan

### Membuat Sesi Absensi

1. Buka menu Sesi di panel admin
2. Tambah sesi baru dengan nama, mode (check-in/check-out), dan waktu
3. Impor batch sesi dari Excel untuk jadwal semester

### Melihat Kehadiran

1. Buka menu Presensi untuk melihat daftar kehadiran per sesi
2. Klik tombol daftar pada sesi untuk melihat detail
3. Tambah manual absensi jika diperlukan

### Ekspor Laporan

1. Buka menu Ekspor untuk mengunduh laporan kehadiran dalam format Excel
2. Laporan berisi semua data kehadiran siswa per sesi

## Template Excel

### Template Impor Siswa
- Kolom A: No
- Kolom B: NIS
- Kolom C: Nama
- Kolom D: Gender (L/P)
- Kolom E: Kelas (TJKT1, TJKT2, atau GURU)

### Template Impor Sesi
- Kolom A: No
- Kolom B: Tanggal (format YYYY-MM-DD)
- Kolom C: Waktu Check-In (format HH:mm)
- Kolom D: Waktu Check-Out (format HH:mm)

## Konfigurasi Timezone

Konfigurasi timezone di file `app/Config/App.php`:
```php
public $appTimezone = 'Asia/Jakarta';
```

## Testing

Jalankan PHPUnit untuk testing:
```bash
composer test
```

## Teknologi yang Digunakan

- **Backend**: PHP 8.1+, CodeIgniter 4.6
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, jQuery, DataTables
- **Excel**: PhpSpreadsheet 3.3
- **Icons**: Bootstrap Icons

## Kontribusi

Untuk berkontribusi, silakan fork repository dan buat pull request untuk fitur baru atau perbaikan bug.

## Lisensi

MIT License

## Dukungan

Untuk pertanyaan dan dukungan, silakan hubungi tim pengembang.
