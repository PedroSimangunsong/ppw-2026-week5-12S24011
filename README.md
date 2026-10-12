# SIPUS-Del - Sistem Informasi Perpustakaan

SIPUS-Del merupakan aplikasi berbasis web yang digunakan untuk mengelola data buku dan kategori buku perpustakaan. Proyek ini dikembangkan sebagai bagian dari praktikum Pemrograman dan Pengembangan Web (PPW) di Institut Teknologi Del.

## Fitur Utama

- Menampilkan daftar buku dan detail buku.
- Menambahkan, mengubah, dan menghapus data buku.
- Mengelola kategori buku.
- Mencari buku berdasarkan judul, ISBN, atau penulis.
- Memfilter buku berdasarkan kategori.
- Memvalidasi data sebelum disimpan.
- Menampilkan jumlah buku pada setiap kategori.
- Menggunakan pagination untuk daftar buku.
- Mencatat query SQL melalui query audit listener.

## Teknologi yang Digunakan

- PHP
- Laravel
- SQLite
- Blade
- Bootstrap 5
- Composer

## Instalasi dan Menjalankan Aplikasi

1. Clone repository:

   ```bash
   git clone URL_REPOSITORY
   cd NAMA_FOLDER_PROYEK
   ```

2. Instal dependensi:

   ```bash
   composer install
   ```

3. Salin konfigurasi lingkungan:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Buat application key:

   ```bash
   php artisan key:generate
   ```

5. Pastikan file database SQLite tersedia di `database/database.sqlite` dan konfigurasi `.env` menggunakan:

   ```dotenv
   DB_CONNECTION=sqlite
   ```

6. Jalankan migration:

   ```bash
   php artisan migrate
   ```

7. Untuk mengisi database baru dengan data awal, jalankan seeder satu kali:

   ```bash
   php artisan db:seed
   ```

8. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

9. Buka `http://127.0.0.1:8000` melalui browser.

## Struktur Data

**Kategori:** menyimpan kode dan nama kategori buku.

**Buku:** menyimpan ISBN, judul, penulis, penerbit, tahun terbit, kategori, stok, dan sinopsis.

Setiap buku terhubung dengan satu kategori melalui relasi database.

## Catatan

- File `.env` tidak disertakan dalam repository karena berisi konfigurasi lokal.
- File database SQLite lokal tidak disertakan dalam repository.
- Seeder digunakan untuk menyiapkan data awal pada database baru. Menjalankannya kembali dapat menambahkan data buku baru.

## Pengembang

Proyek praktikum Pemrograman dan Pengembangan Web (PPW) — Institut Teknologi Del.
