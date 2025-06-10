# 🪑 UKIRE Backend - Furniture E-Commerce

<!-- Dynamic Badges -->
![PHP Version](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4?style=for-the-badge&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)
![FilamentPHP](https://img.shields.io/badge/Filament-v3-F59E0B?style=for-the-badge)
![Database](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql)
![License](https://img.shields.io/github/license/alfrendrapratama/UkireBackend?style=for-the-badge)

UKIRE Backend adalah tulang punggung dari aplikasi e-commerce furniture ukir **UKIRE**. Proyek ini bertanggung jawab untuk menyediakan API RESTful untuk komunikasi dengan frontend, serta panel admin untuk pengelolaan data produk, kategori, pesanan, dan pengguna.

---

## 📜 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#️-teknologi-yang-digunakan)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Panduan Instalasi](#-panduan-instalasi--menjalankan-lokal)
- [Mengakses Aplikasi](#️-mengakses-aplikasi)
- [Status & Masalah Diketahui](#️-status-terkini--masalah-diketahui)
- [Kontribusi](#-kontribusi)
- [Lisensi](#️-lisensi)

---

## ✨ Fitur Utama

- ✅ **Manajemen Produk**: API untuk mengelola data ukiran kayu (Produk).
- ✅ **Manajemen Kategori**: API untuk mengelola kategori produk.
- ✅ **Panel Admin**: Interface untuk CRUD (Create, Read, Update, Delete) data melalui Filament.
- ⏳ **(Fitur Selanjutnya)**: Autentikasi Pengguna, Keranjang Belanja, Pemesanan, dll.

---

## 🛠️ Teknologi yang Digunakan

- **Framework**: [Laravel 11](https://laravel.com/)
- **Panel Admin**: [FilamentPHP v3](https://filamentphp.com/)
- **Database**: [MySQL](https://www.mysql.com/)
- **Bahasa Pemrograman**: PHP (Disarankan v8.2 atau v8.3)
- **Manajemen Dependensi**: [Composer](https://getcomposer.org/)

---

## 📋 Persyaratan Sistem

Pastikan Anda memiliki hal-hal berikut terinstal di sistem Anda:

- **PHP**: Versi `8.2` atau `8.3` (untuk kompatibilitas dan menghindari masalah `httpd.exe Entry Point Not Found`).
- **Composer**: Untuk manajemen dependensi PHP.
- **MySQL Server**: Versi `8.0` atau lebih tinggi.
- **Laragon**: (Direkomendasikan) Lingkungan pengembangan lokal yang sudah terintegrasi.
- **Git**: Untuk mengelola versi kode.

---

## 🚀 Panduan Instalasi & Menjalankan Lokal

Ikuti langkah-langkah di bawah ini untuk menyiapkan dan menjalankan proyek di lingkungan lokal Anda.

#### 1. Clone Repositori

```bash
# Ganti dengan URL repositori Anda
git clone [https://github.com/alfrendrapratama/UkireBackend.git](https://github.com/alfrendrapratama/UkireBackend.git)
cd UkireBackend
```

#### 2. Konfigurasi Laragon (Direkomendasikan)

1.  Buka Laragon Control Panel.
2.  Klik **"Stop All"**.
3.  Masuk ke **Menu -> Preferensi -> Layanan & Port**.
4.  Pastikan hanya satu web server yang tercentang (disarankan **Apache**).
5.  Pastikan port MySQL sesuai dengan `.env` Anda (misal: `3307`).
6.  Klik **"Start All"**.

> **Penting**: Pastikan domain virtual host (`ukirebackend.test`) sudah terdaftar dengan benar di Laragon.

#### 3. Instal Dependensi Composer

Untuk memastikan instalasi yang bersih, hapus direktori `vendor` dan file `composer.lock` jika ada.

```bash
# Untuk Windows (Command Prompt)
rmdir /s /q vendor
del composer.lock

# atau untuk Git Bash / WSL / Linux / macOS
rm -rf vendor && rm composer.lock
```

Kemudian, instal ulang semua dependensi:

```bash
composer install
```

#### 4. Konfigurasi Environment (`.env`)

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Buat kunci aplikasi Laravel:

```bash
php artisan key:generate
```

Buka file `.env` dan pastikan konfigurasi database sudah benar:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307    # SESUAIKAN DENGAN PORT MYSQL DI LARAGON ANDA
DB_DATABASE=ukire_db
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Perbaikan `pdo_mysql` (Jika Error `could not find driver`)

Jika Anda mengalami error ini saat migrasi:
1.  Jalankan `php --ini` di terminal untuk menemukan lokasi file `php.ini`.
2.  Buka file `php.ini` tersebut, cari baris `;extension=pdo_mysql` atau `;extension=php_pdo_mysql.dll`.
3.  Hapus tanda titik koma (`;`) di depannya untuk mengaktifkan ekstensi.
4.  Restart Laragon.

#### 6. Buat Database MySQL

Pastikan Anda sudah membuat database bernama `ukire_db` di server MySQL Anda (misalnya melalui MySQL Workbench atau HeidiSQL di Laragon).

#### 7. Jalankan Migrasi Database

Perintah ini akan menghapus semua tabel lama (jika ada) dan membuat struktur tabel baru dari file migrasi.

```bash
php artisan migrate:fresh
```

#### 8. Buat Pengguna Admin Filament

```bash
php artisan make:filament-user
```
Ikuti instruksi di terminal untuk membuat user admin (email dan password ini akan digunakan untuk login ke dashboard).

#### 9. Bersihkan Cache

Untuk memastikan semua konfigurasi baru termuat dengan benar:

```bash
php artisan optimize:clear
composer dump-autoload
```

#### 10. Jalankan Server

-   **Opsi 1: Menggunakan Laragon (Direkomendasikan)**
    Jika Apache/Nginx sudah berjalan di Laragon, Anda bisa langsung mengakses aplikasi melalui domain virtual host yang Anda atur.

-   **Opsi 2: Menggunakan Server Bawaan Laravel**
    Pastikan Apache/Nginx di Laragon sudah di-**STOP**.

    ```bash
    php artisan serve --port=8000
    ```

---

## 🖥️ Mengakses Aplikasi

#### Dashboard Admin Filament

- **Via Laragon**: `http://ukirebackend.test/admin`
- **Via `php artisan serve`**: `http://127.0.0.1:8000/admin` (sesuaikan port jika Anda menggunakan port lain)

Login dengan email dan password yang Anda buat pada langkah 8.

#### Endpoint API Dasar

- **Daftar Kategori**: `http://ukirebackend.test/api/categories`
- **Daftar Produk**: `http://ukirebackend.test/api/products`
- **Detail Produk**: `http://ukirebackend.test/api/products/{id_produk}`

---

## ⚠️ Status Terkini & Masalah Diketahui

-   **API Dasar**: Endpoint `/api/categories` dan `/api/products` sudah diimplementasikan.
-   **Filament Admin**: Dashboard untuk manajemen Kategori dan Produk sudah berfungsi.
-   **Masalah `404 Not Found` pada API**: Terkadang muncul saat mengakses via `ukirebackend.test/api/...`. Ini kemungkinan masalah konfigurasi `mod_rewrite` di Apache/Nginx. ***Workaround***: Gunakan `php artisan serve` untuk sementara.
-   **Error `httpd.exe - Entry Point Not Found`**: Terjadi jika menggunakan PHP 8.4 dengan Apache di Laragon. **Solusi**: Ganti versi PHP ke `8.2` atau `8.3` di Laragon.
-   **Komponen `->mask()` di Filament**: Telah dihapus sementara dari input harga produk karena menyebabkan error. Input harga tetap `numeric` dan memiliki prefix.

---

## 🤝 Kontribusi

Kami sangat terbuka untuk kontribusi! Silakan fork repositori ini dan ajukan *Pull Request*.

1.  **Fork** repositori ini.
2.  Buat **Branch** baru (`git checkout -b fitur/nama-fitur-baru`).
3.  **Commit** perubahan Anda (`git commit -m 'Menambahkan fitur X'`).
4.  **Push** ke branch Anda (`git push origin fitur/nama-fitur-baru`).
5.  Buka **Pull Request**.

---

## ©️ Lisensi

© 2025 UKIRE. Hak Cipta Dilindungi Undang-Undang.

Didistribusikan di bawah Lisensi MIT. Lihat `LICENSE` untuk informasi lebih lanjut.
