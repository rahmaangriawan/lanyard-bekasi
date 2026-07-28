# Deploy CloudPanel lewat GitHub Actions

Workflow production berada di `.github/workflows/deploy.yml`. Push ke branch
`main` akan menjalankan test, build aset, membuat paket, mengirimnya melalui
SSH, menjalankan migration, membangun cache Laravel, dan memeriksa homepage.
Workflow juga dapat dijalankan manual melalui **Actions > Deploy Pusat Tali
Lanyard > Run workflow**.

## 1. Persiapan CloudPanel

1. Buat PHP Site untuk `pusattalilanyard.com` dengan PHP 8.3.
2. Document root harus mengarah ke:
   `/home/pusattalilanyard/htdocs/pusattalilanyard.com/public`
3. Pastikan user site dapat memakai `rsync`, `tar`, dan PHP CLI 8.3.
4. Buat database dan user database dari CloudPanel.
5. Di root aplikasi server, buat `.env` dari `.env.example`, lalu isi minimal:
   `APP_KEY`, koneksi database, URL production, dan kredensial admin bootstrap
   jika masih diperlukan.
6. Jangan menyimpan `.env` production di GitHub atau repository.

Jika path site atau binary PHP berbeda, gunakan GitHub Variables pada bagian
berikut.

## 2. GitHub Secrets

Buka **Repository > Settings > Secrets and variables > Actions > Secrets**,
lalu buat:

| Secret | Isi |
| --- | --- |
| `SSH_HOST` | IP atau hostname server CloudPanel |
| `SSH_PORT` | Port SSH, biasanya `22` |
| `SSH_USER` | User site CloudPanel, biasanya `pusattalilanyard` |
| `SSH_PASSWORD` | Password SSH user tersebut |

Disarankan membuat GitHub Environment bernama `production`, lalu menaruh
secrets di environment itu dan mengaktifkan approval jika diperlukan.

## 3. GitHub Variables opsional

Nilai default sudah sesuai dengan domain proyek. Buat variable hanya bila
konfigurasi server berbeda.

| Variable | Default |
| --- | --- |
| `DEPLOY_PATH` | `/home/pusattalilanyard/htdocs/pusattalilanyard.com` |
| `PHP_BIN` | `/usr/bin/php8.3` |
| `HEALTHCHECK_URL` | `https://pusattalilanyard.com/` |

## 4. Deploy pertama

Karena source belum tersedia pada server sebelum workflow pertama, buat folder
site melalui CloudPanel. Kemudian unggah `.env.example` dari komputer melalui
File Manager CloudPanel, simpan sebagai `.env` di root site, isi nilai
production, dan buat `APP_KEY` dengan perintah lokal:

```powershell
D:\laragon\bin\php\php-8.3.31-nts-Win32-vs16-x64\php.exe artisan key:generate --show
```

Salin hasilnya ke `APP_KEY` pada `.env` server. Setelah `.env` tersedia,
jalankan workflow secara manual. Workflow akan mengunggah source dan vendor,
kemudian menjalankan migration serta optimasi Laravel.

## 5. Aktifkan Laravel Scheduler

Submit sitemap ke Google Search Console dijalankan melalui Laravel Scheduler.
Tambahkan cron berikut untuk user site CloudPanel:

```cron
* * * * * cd /home/pusattalilanyard/htdocs/pusattalilanyard.com && /usr/bin/php8.3 artisan schedule:run >> /dev/null 2>&1
```

Jika `DEPLOY_PATH` atau `PHP_BIN` di GitHub Variables diubah, sesuaikan cron
dengan nilai yang sama. Command `seo:submit-sitemap` hanya mengirim ulang
sitemap ketika post, produk, atau halaman kota berubah.

## Perilaku penting

- `storage`, `public/storage`, dan upload CMS di `public/uploads` tidak dihapus.
- Gambar bawaan repository dan gambar artikel impor disinkronkan
  non-destruktif.
- File `.env` server tidak pernah ditimpa.
- Migration, build cache, pemeriksaan aset, atau health check yang gagal akan
  membuat workflow merah.
- Situs otomatis keluar dari maintenance mode jika proses server gagal.
- Paket deploy tidak membawa dependency development, test, `.env`, atau
  `node_modules`.
