# SIPUS-Del — Sistem Informasi Perpustakaan Kampus Del

SIPUS-Del merupakan aplikasi Sistem Informasi Perpustakaan Kampus Del yang dikembangkan menggunakan Laravel. Aplikasi ini digunakan untuk mengelola data buku, kategori, pengguna, serta transaksi peminjaman dan pengembalian buku.

Pada pengembangan Week 9, SIPUS-Del dikembangkan lebih lanjut dengan menerapkan Web Services berbasis REST API dan SOAP agar sistem dapat berkomunikasi dan bertukar data dengan aplikasi lain.

## Identitas Mahasiswa

| Keterangan | Identitas |
|---|---|
| Nama | Pedro Simangunsong |
| NIM | 12S24011 |
| Kelas | [Isi kelas] |
| Program Studi | Sistem Informasi |
| Institusi | Institut Teknologi Del |

## 1. Fitur Aplikasi

Fitur utama SIPUS-Del meliputi:

- Pengelolaan data buku.
- Pengelolaan kategori buku.
- Pencarian dan penyaringan katalog buku.
- Pagination pada daftar buku.
- Pengelolaan transaksi peminjaman dan pengembalian.
- Autentikasi pengguna.
- REST API untuk pertukaran data dengan aplikasi lain.
- SOAP Web Service untuk pemeriksaan status bebas pustaka.
- Integrasi dengan layanan API eksternal.

## 2. Analisis Kebutuhan dan Kelayakan Web Services

### 2.1 Analisis Kebutuhan

Pengembangan Web Services pada SIPUS-Del dilakukan untuk memenuhi kebutuhan integrasi sistem perpustakaan dengan aplikasi lain. Sistem tidak hanya digunakan melalui antarmuka web, tetapi juga perlu menyediakan layanan yang dapat diakses oleh aplikasi eksternal.

Dalam penerapannya, SIPUS-Del memiliki dua peran utama:

| Peran | Penjelasan |
|---|---|
| Service Provider | SIPUS-Del menyediakan layanan berupa data katalog buku, kategori, autentikasi, serta transaksi peminjaman dan pengembalian melalui REST API. SIPUS-Del juga menyediakan layanan SOAP untuk pemeriksaan status bebas pustaka. |
| Service Consumer | SIPUS-Del bertindak sebagai pengguna layanan eksternal ketika mengambil informasi tambahan buku dari OpenLibrary dan ketika melakukan latihan konsumsi REST API GitHub. |

### 2.2 Analisis Kelayakan

Penerapan Web Services pada SIPUS-Del dinilai layak karena dapat mempermudah pertukaran data, mendukung integrasi dengan aplikasi lain, dan mengurangi ketergantungan terhadap antarmuka web.

Teknologi REST dipilih untuk kebutuhan aplikasi Mobile dan Kiosk karena menggunakan HTTP method seperti GET, POST, PUT, PATCH, dan DELETE. REST juga mendukung format JSON yang relatif ringan sehingga sesuai untuk komunikasi data antara aplikasi.

Sementara itu, SOAP digunakan untuk kebutuhan integrasi dengan sistem legacy, khususnya layanan pemeriksaan status bebas pustaka. SOAP menggunakan format XML dan memiliki kontrak layanan WSDL sehingga struktur komunikasi antara sistem dapat dijelaskan secara lebih terstruktur.

### 2.3 Perbandingan REST dan SOAP

| Aspek | REST | SOAP |
|---|---|---|
| Format data | Umumnya JSON | XML |
| Komunikasi | HTTP method | Pesan SOAP melalui HTTP |
| Kontrak | Dapat menggunakan OpenAPI | Menggunakan WSDL |
| Penggunaan | Mobile, Kiosk, dan aplikasi web | Integrasi sistem legacy |
| Kelebihan | Ringan dan sederhana | Struktur pesan dan kontrak lebih terdefinisi |

Berdasarkan kebutuhan tersebut, REST digunakan sebagai layanan utama untuk aplikasi Mobile dan Kiosk, sedangkan SOAP digunakan untuk mendukung integrasi dengan sistem administrasi yudisium.

## 3. Teknologi yang Digunakan

- PHP 8.4
- Laravel 13
- MySQL / SQLite
- Laravel Sanctum
- Eloquent ORM
- REST API
- SOAP XML dan WSDL
- OpenAPI 3.0
- Thunder Client
- Swagger Editor
- Composer

## 4. Dokumentasi Endpoint Web Services

Base URL untuk pengujian lokal:

`http://127.0.0.1:8000`

### 4.1 SOAP Web Service

SOAP digunakan untuk memeriksa apakah mahasiswa masih memiliki peminjaman buku yang belum dikembalikan.

| Method | URI | Auth | Deskripsi | Respons |
|---|---|---|---|---|
| GET | `/soap/sipus.wsdl` | Tidak | Menampilkan kontrak layanan WSDL | 200 OK |
| POST | `/soap/sipus` | Tidak | Memeriksa status bebas pustaka berdasarkan NIM | 200 OK / 400 Bad Request |

Operasi SOAP yang digunakan adalah `CheckClearanceStatus` dengan parameter `nim_peminjam`.

Sistem menghitung jumlah peminjaman dengan status `dipinjam`. Jika jumlah peminjaman aktif adalah nol, maka nilai `is_cleared` adalah `true`. Jika masih terdapat peminjaman aktif, nilainya adalah `false`.

Jika NIM kosong atau XML tidak valid, sistem mengembalikan respons SOAP Fault.

### 4.2 REST API Authentication

| Method | URI | Auth | Deskripsi | Respons |
|---|---|---|---|---|
| POST | `/api/v1/auth/login` | Tidak | Login dan penerbitan Bearer Token | 200 OK |
| GET | `/api/v1/auth/me` | Bearer Token | Melihat informasi pengguna yang sedang login | 200 OK |
| POST | `/api/v1/auth/logout` | Bearer Token | Mencabut token aktif | 200 OK |

Autentikasi menggunakan Laravel Sanctum dengan mekanisme Bearer Token. Token digunakan pada header setiap permintaan yang membutuhkan autentikasi.

Contoh header:

`Authorization: Bearer <token>`

### 4.3 REST API Katalog Buku

| Method | URI | Auth | Deskripsi | Respons |
|---|---|---|---|---|
| GET | `/api/v1/bukus` | Tidak | Menampilkan katalog buku dengan pagination dan filter | 200 OK |
| GET | `/api/v1/bukus/{id}` | Tidak | Menampilkan detail buku | 200 OK / 404 Not Found |
| POST | `/api/v1/bukus` | Bearer Token | Menambahkan buku baru | 201 Created |
| PUT | `/api/v1/bukus/{id}` | Bearer Token | Memperbarui seluruh data buku | 200 OK |
| PATCH | `/api/v1/bukus/{id}` | Bearer Token | Memperbarui sebagian data buku | 200 OK |
| DELETE | `/api/v1/bukus/{id}` | Bearer Token | Menghapus data buku | 204 No Content / 409 Conflict |

Endpoint katalog mendukung beberapa parameter:

- `search`: mencari buku berdasarkan judul, penulis, atau ISBN.
- `kategori_id`: menyaring buku berdasarkan kategori.
- `tersedia=1`: menampilkan buku dengan stok lebih dari nol.
- `sort`: mengatur pengurutan data.
- `per_page`: mengatur jumlah data per halaman.
- `page`: menentukan halaman yang ditampilkan.

Contoh permintaan:

`GET /api/v1/bukus?per_page=5&page=1`

Penambahan buku menggunakan metode POST akan menghasilkan status `201 Created` dan menyertakan header `Location` yang menunjukkan alamat data buku yang baru dibuat.

Perbedaan PUT dan PATCH terletak pada cakupan pembaruan. PUT digunakan untuk mengganti seluruh atribut data, sedangkan PATCH digunakan untuk memperbarui atribut tertentu saja.

### 4.4 REST API Peminjaman dan Pengembalian

| Method | URI | Auth | Deskripsi | Respons |
|---|---|---|---|---|
| GET | `/api/v1/peminjamans` | Bearer Token | Menampilkan daftar transaksi peminjaman | 200 OK |
| POST | `/api/v1/peminjamans` | Bearer Token | Membuat transaksi peminjaman baru | 201 Created / 409 Conflict / 422 Unprocessable Content |
| PATCH | `/api/v1/peminjamans/{id}/kembalikan` | Bearer Token | Memproses pengembalian buku | 200 OK / 409 Conflict |

Pembuatan transaksi peminjaman menggunakan `DB::transaction()` dan `lockForUpdate()` untuk menjaga konsistensi data stok buku ketika terjadi transaksi.

Jika stok buku habis, sistem menolak peminjaman. Sistem juga menolak peminjaman buku yang sama oleh mahasiswa yang masih memiliki transaksi aktif.

Ketika buku berhasil dikembalikan, status peminjaman diperbarui dan stok buku ditambahkan kembali.

Filter daftar peminjaman:

- `status`: menyaring berdasarkan status peminjaman.
- `nim`: menyaring berdasarkan NIM peminjam.

### 4.5 REST API Consumer

SIPUS-Del juga mempelajari penerapan REST API Consumer, yaitu ketika aplikasi mengirim permintaan ke layanan eksternal dan menggunakan data yang diterima.

Salah satu layanan yang digunakan dalam skenario praktikum adalah OpenLibrary untuk memperoleh metadata tambahan buku berdasarkan ISBN.

Contoh layanan OpenLibrary:

`https://openlibrary.org/api/books?bibkeys=ISBN:{isbn}&format=json&jscmd=data`

Laravel menyediakan HTTP Client melalui facade `Http` untuk melakukan permintaan ke layanan eksternal.

Selain itu, dilakukan latihan konsumsi GitHub REST API melalui endpoint:

`PATCH https://api.github.com/user`

Permintaan tersebut digunakan untuk memahami penggunaan HTTP header, autentikasi Personal Access Token, dan metode PATCH pada layanan eksternal.

**Catatan keamanan:** Personal Access Token GitHub tidak boleh dimasukkan ke dalam README, kode sumber, maupun repository.

## 5. HATEOAS dan Penanganan Error

### 5.1 HATEOAS

SIPUS-Del menerapkan HATEOAS dengan menyediakan tautan aksi pada properti `_links` di dalam respons JSON.

Pada data buku, tautan `pinjam_buku` hanya tersedia ketika stok buku lebih dari nol.

Pada data peminjaman, tautan `kembalikan_buku` hanya tersedia ketika status peminjaman masih `dipinjam`.

Dengan demikian, aplikasi konsumen dapat mengetahui tindakan yang tersedia berdasarkan kondisi data dari server.

### 5.2 RFC 7807

Penanganan kesalahan REST API menggunakan format Problem Details for HTTP APIs berdasarkan RFC 7807.

Struktur respons kesalahan mencakup:

- `type`: jenis atau identitas kesalahan.
- `title`: judul kesalahan.
- `status`: kode status HTTP.
- `detail`: penjelasan kesalahan.
- `instance`: alamat permintaan yang mengalami kesalahan.

Format ini digunakan agar respons kesalahan API lebih konsisten dan mudah dipahami oleh aplikasi konsumen.

## 6. Dokumentasi Kontrak API

Dokumentasi kontrak dan koleksi pengujian disimpan pada folder `docs/`.

| File | Keterangan |
|---|---|
| `docs/openapi-sipus-v1.yaml` | Dokumentasi kontrak REST API menggunakan OpenAPI 3.0 |
| `docs/SIPUS-Del-API-v1.postman_collection.json` | Koleksi permintaan API untuk pengujian |

Dokumen OpenAPI dapat dibuka menggunakan Swagger Editor untuk melihat struktur endpoint, parameter, autentikasi, dan respons API.

Koleksi API dapat diimpor ke aplikasi API Client untuk menjalankan pengujian endpoint.

## 7. Panduan Instalasi dan Menjalankan Aplikasi

### 7.1 Persyaratan

Pastikan perangkat telah memiliki:

- PHP 8.2 atau versi yang lebih baru.
- Composer.
- MySQL atau SQLite.
- Node.js dan NPM jika diperlukan untuk pengelolaan aset frontend.

### 7.2 Langkah Instalasi

Clone repository:

```bash
git clone https://github.com/PedroSimangunsong/ppw-2026-week5-12S24011.git
```

Masuk ke direktori proyek:

```bash
cd sipus-del
```

Instal dependensi:

```bash
composer install
```

Salin file konfigurasi:

```bash
cp .env.example .env
```

Buat application key:

```bash
php artisan key:generate
```

Atur konfigurasi database pada file `.env` sesuai database yang digunakan.

Jalankan migrasi dan seeder pada database baru:

```bash
php artisan migrate:fresh --seed
```

**Perhatian:** perintah `migrate:fresh` menghapus seluruh tabel dan data pada database yang sedang digunakan. Gunakan hanya pada database pengembangan yang memang boleh di-reset.

Jalankan aplikasi:

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

`http://127.0.0.1:8000`

### 7.3 Akun Pengujian

Akun uji yang dicantumkan dalam modul praktikum:

| Keterangan | Nilai |
|---|---|
| Email | `pustakawan@del.ac.id` |
| Password | `password123` |

Akun tersebut digunakan apabila tersedia pada seeder proyek. Pastikan akun yang digunakan sesuai dengan data pengguna pada database lokal.

## 8. Ringkasan Pengujian

Pengujian dilakukan menggunakan Thunder Client untuk memeriksa komunikasi antara client dan server.

Skenario pengujian meliputi:

1. Login API dan penerbitan Bearer Token.
2. Pengambilan katalog buku dengan pagination dan filter.
3. Penambahan buku baru dan pemeriksaan header Location.
4. Pembaruan data menggunakan PUT dan PATCH.
5. Pengujian transaksi peminjaman buku berhasil.
6. Pengujian penolakan peminjaman ketika stok habis.
7. Pemeriksaan kontrak WSDL dan komunikasi SOAP.
8. Pengujian SOAP Fault ketika NIM kosong.
9. Pengujian konsumsi REST API eksternal.
10. Pemeriksaan dokumentasi OpenAPI pada Swagger Editor.

Bukti pengujian praktikum disertakan dalam dokumen laporan Word.

## 9. Pengelolaan Git

Pengembangan Week 9 dilakukan pada branch:

`week9-rest-api`

Branch tersebut digunakan untuk memisahkan pengembangan REST API dan SOAP dari versi proyek sebelumnya.

Contoh perintah Git:

```bash
git checkout week9-rest-api
git add .
git commit -m "docs: complete SIPUS-Del Week 9 API documentation"
git push origin week9-rest-api
```

**Catatan:** file `.env`, password, dan Personal Access Token tidak boleh dimasukkan ke repository.

## 10. Pengembang

**Pedro Simangunsong**
NIM: 12S24011
Institut Teknologi Del

Proyek ini dikembangkan sebagai bagian dari praktikum Pengembangan dan Pemrograman Web (PPW), dengan studi kasus Sistem Informasi Perpustakaan Kampus Del (SIPUS-Del).
