# Proyek Laravel Kazee

## Deskripsi

Proyek ini adalah aplikasi Laravel yang melibatkan dua tabel utama: `products` dan `transaksis`.

## Langkah-langkah untuk Memulai

1. **Clone Repository**

   Clone repository ke lokal Anda menggunakan perintah berikut:
   ```bash
   git clone https://gitlab.com/username/repository.git
   cd repository

2. **Buat File .env**

    Salin file .env.example menjadi .env dan sesuaikan konfigurasi database sesuai kebutuhan Anda. Jika nama database adalah kazee, pastikan konfigurasi di file .env mencakup nama database yang benar:

    cp .env.example .env

3. **Edit file .env dan pastikan konfigurasi database seperti berikut:**

    env
    Copy code

    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=kazee
    DB_USERNAME=root
    DB_PASSWORD=

4. **Instal Dependensi**

Jalankan perintah Composer untuk menginstal semua dependensi proyek:

    ```bash
    composer install
    php artisan migrate:refresh --seed

5. **Jalankan perintah berikut untuk menjalankan migrasi dan seeder:**

    ```bash
    php artisan serve

