# Sistem Absensi Fingerprint (Laravel)

Proyek ini adalah aplikasi web untuk manajemen absensi berbasis fingerprint, mencakup:
- Data `siswa`
- Data `staff`
- Log kehadiran harian
- Integrasi API untuk simpan template fingerprint dan kirim log absensi
- Export laporan ke Excel

## Stack

- PHP `^8.2`
- Laravel `^12`
- MySQL/MariaDB (via migration Laravel)
- Tailwind + Vite
- PhpSpreadsheet (export Excel)

## Fitur Utama

- Dashboard ringkasan kehadiran harian siswa.
- Manajemen siswa: tambah, edit, daftar, filter (kelas/tahun/tanggal/pencarian), jam masuk-pulang, export Excel.
- Manajemen staff: tambah, edit, daftar, filter tanggal/nama, jam masuk-pulang, export harian, dan rekap bulanan Excel.
- Halaman kehadiran: statistik terdaftar/masuk/pulang dan tabel log absensi per tanggal.
- API fingerprint: simpan template, ambil template untuk sinkronisasi device, simpan log kehadiran, kirim notifikasi WhatsApp.

## Aturan Absensi di Sistem

- `Masuk`: jam `00:00:00` sampai `08:59:59`
- `Pulang`: jam `09:00:00` sampai `23:59:59`
- Satu orang hanya bisa 1 kali `Masuk` dan 1 kali `Pulang` per hari.

## Endpoint API

Prefix default: `/api`

1. `GET /api/fingerprint-templates`
Mengambil daftar template fingerprint. Query opsional: `per_page` (default `10`), `page` (default `1`).

2. `POST /api/fingerprint`
Menyimpan template fingerprint baru. Body: `template_hex` (string, wajib). Response sukses berisi ID template yang dipakai.

3. `POST /api/log-kehadiran`
Menyimpan log kehadiran. Body: `fingerprint_id` (integer, wajib, harus ada di `fingerprint_templates.id`).
Response: `201` jika berhasil, `400` jika sudah absen pada rentang waktu yang sama (masuk/pulang).

## Struktur Data Inti

- `fingerprint_templates`
`id` (primary key, unsigned small int, rentang 1-300), `hex_data` (text)

- `siswas`
`nis`, `tahun`, `nama`, `kelas`, `alamat`, `nomor_ortu`, `jenis_kelamin`, `id_template`

- `staffs`
`nama`, `jabatan`, `nomor_telepon`, `jenis_kelamin`, `id_template`

- `log_kehadirans`
`fingerprint_id`, `check_in`

Relasi dilakukan via `id_template` (siswa/staff) ke `fingerprint_id` (log).

## Instalasi Lokal

1. Install dependency backend:
```bash
composer install
```

2. Install dependency frontend:
```bash
npm install
```

3. Siapkan environment:
```bash
copy .env.example .env
php artisan key:generate
```

4. Atur koneksi database di `.env`, lalu migrasi:
```bash
php artisan migrate
```

5. Jalankan aplikasi:
```bash
php artisan serve
npm run dev
```

## Catatan Penting

- Integrasi Fonnte API key saat ini di-hardcode pada `LogKehadiranController`. Disarankan dipindah ke `.env`.
- Nomor tujuan WhatsApp untuk staff saat ini bersifat statis pada controller.
- Beberapa route web (`/siswa`, `/staff`, `/kehadiran`) belum dibungkus middleware `auth` di `routes/web.php`, jadi dokumentasi ini mengikuti perilaku kode saat ini.

## Pengujian

```bash
php artisan test
```
