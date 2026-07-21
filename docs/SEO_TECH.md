# SEO Tech

## Article dan FAQ schema

- Artikel published menghasilkan `BlogPosting`, `BreadcrumbList`, dan metadata tanggal Open Graph.
- Heading artikel `H2-H4` yang berakhir dengan tanda tanya akan menjadi `FAQPage` bila mempunyai jawaban yang terlihat sebelum heading berikutnya.
- FAQ homepage berasal dari `config/seo.php`, sumber yang sama dengan accordion yang terlihat.
- Structured data membantu Google memahami halaman, tetapi tidak menjamin rich result.

## Dynamic Open Graph

- URL gambar artikel: `/og/blog/{slug}.png`.
- Gambar dibuat otomatis pada ukuran 1200x630 dari featured image, judul, dan kategori artikel.
- Hasil dirender sebagai PNG, dicache 24 jam, dan versi cache berubah saat artikel diperbarui.
- Server membutuhkan ekstensi PHP GD dan font Geist di `public/fonts/geist`.

## Sitemap dan Search Console

- `/sitemap.xml` adalah sitemap index untuk halaman, produk, artikel, dan area.
- Perubahan Post, Product, atau CityPage menghapus cache sitemap terkait dan menandai `seo_sitemap_dirty`.
- Scheduler menjalankan `php artisan seo:submit-sitemap` setiap 10 menit. Command hanya submit ketika sitemap berubah.
- Submit manual: `php artisan seo:submit-sitemap --force`.
- Google Search Console Site URL dan Service Account JSON harus tersedia di CMS Settings. Service account harus menjadi user pada property Search Console.
- Server produksi wajib menjalankan Laravel scheduler (`php artisan schedule:run` setiap menit).

Google telah menghentikan endpoint ping sitemap anonim. Implementasi ini memakai Google Search Console Sitemaps API dengan scope `webmasters`.

## Canonical URL

- Canonical selalu memakai domain dari `APP_URL`, tanpa query string dan trailing slash (kecuali homepage).
- Request publik dengan trailing slash, slash ganda, huruf path tidak konsisten, atau parameter tracking (`utm_*`, `gclid`, `fbclid`, `msclkid`) diarahkan permanen ke URL bersih.
- Query fungsional seperti `q` dan `page` tetap dipertahankan pada redirect.
- API, CMS, asset, dan sitemap dikecualikan dari normalisasi.

## Pemeriksaan produksi

1. Pastikan `APP_URL=https://pusattalilanyard.com`.
2. Pastikan scheduler aktif dan kredensial Search Console terisi.
3. Buka `/sitemap.xml` dan satu child sitemap.
4. Uji satu artikel di Rich Results Test dan validator Open Graph.
5. Pastikan URL bertanda UTM mengarah 301 ke URL bersih.

