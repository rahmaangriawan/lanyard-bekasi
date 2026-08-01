# Deploy CloudPanel Lanyard Bekasi

Workflow deploy berada di `.github/workflows/deploy.yml` dan memakai repository:

`https://github.com/rahmaangriawan/lanyard-bekasi.git`

Workflow hanya mengirim source code, dependency production, dan asset hasil build. Semua file `*.sql`, `.env`, test, dan cache dikeluarkan dari artifact deploy.

## GitHub Actions

Buat environment `production`, lalu tambahkan secrets berikut:

- `SSH_HOST`: hostname atau IP server CloudPanel.
- `SSH_PORT`: port SSH, biasanya `22`.
- `SSH_USER`: user site CloudPanel.
- `SSH_PASSWORD`: password SSH user tersebut.

Tambahkan repository variables berikut bila default tidak sesuai:

- `DEPLOY_PATH`: contoh `/home/lanyardbekasi/htdocs/lanyardbekasi.com`.
- `PHP_BIN`: contoh `/usr/bin/php8.3`.
- `HEALTHCHECK_URL`: contoh `https://lanyardbekasi.com/`.

> Untuk keamanan lebih baik, password SSH dapat diganti ke deploy key pada iterasi berikutnya. Workflow saat ini mengikuti pola workflow lama yang memakai `sshpass`.

## Environment CloudPanel

Salin `.env.example` menjadi `.env` di root aplikasi CloudPanel, lalu isi minimal:

```dotenv
APP_NAME="Lanyard Bekasi"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://lanyardbekasi.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lanyard_bekasi
DB_USERNAME=ISI_USER_DATABASE_CLOUDPANEL
DB_PASSWORD=ISI_PASSWORD_DATABASE_CLOUDPANEL

CMS_ADMIN_NAME=Administrator
CMS_ADMIN_EMAIL=ISI_EMAIL_ADMIN
CMS_ADMIN_PASSWORD=ISI_PASSWORD_ADMIN_AWAL

SESSION_DRIVER=file
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
CACHE_STORE=file

PUBLIC_SITE_URL=https://lanyardbekasi.com
MEDIA_UPLOAD_DIR=../../public/uploads
```

Jangan commit `.env` production atau nilai `APP_KEY`, password database, password admin, dan secret API.

## Import database manual

Untuk import lengkap ke database baru, gunakan:

`database/lanyard-bekasi-cloudpanel-full.sql`

Dump ini berisi struktur tabel, index, migration history Laravel, produk, halaman, setting, kategori, post blog, media, portfolio, dan akun user. File dihasilkan langsung dari database lokal `lanyard_bekasi`, tidak memakai `CREATE DATABASE`, sehingga dapat di-import ke database CloudPanel bernama `kahanan` atau nama lain yang sudah dipilih di phpMyAdmin.

Urutan import lengkap:

1. Buat database dan user MySQL di CloudPanel.
2. Pastikan `.env` memakai nama database CloudPanel tersebut.
3. Import `database/lanyard-bekasi-cloudpanel-full.sql` melalui phpMyAdmin atau MySQL CLI.
4. Deploy aplikasi dari GitHub. `artisan migrate` akan membaca migration history dari dump dan tidak mengulang migration yang sudah ada.
5. Jalankan `php8.3 artisan storage:link`, `php8.3 artisan optimize:clear`, lalu `php8.3 artisan optimize`.

Dump ini bersifat replacement dump karena menggunakan `DROP TABLE IF EXISTS`. Pakai pada database baru atau backup database lama terlebih dahulu.

Dump content-only `database/lanyard-bekasi-cloudpanel-content.sql` tetap tersedia untuk skenario alternatif: deploy kode, jalankan migration, lalu import data publik tanpa mengganti struktur tabel.

Full dump dapat berisi hash akun user dan setting terenkripsi dari server lokal. Jangan commit atau mengirimkannya ke GitHub. Jika ingin mempertahankan setting terenkripsi seperti koneksi Google, gunakan `APP_KEY` yang sama secara aman di server atau isi ulang secret tersebut setelah import.

Contoh MySQL CLI:

```bash
mysql -u USER_DATABASE -p -h 127.0.0.1 kahanan < database/lanyard-bekasi-cloudpanel-full.sql
```

Setelah import, cek `/`, `/blog`, `/produk`, dan `/kawruh` sebelum membuka akses publik penuh.
