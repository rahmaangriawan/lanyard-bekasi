# PLAN — SEO Audit & On-Page Foundation: Meta Tags, Canonical, Schema Markup untuk Pusat Tali Lanyard (Laravel Blade Inertia)

> Project: `apps/backend` Laravel 12 Blade (public) + Inertia (kawruh admin)
> Homepage aktif: `resources/views/pages/home.blade.php` -> `layouts/public.blade.php`
> Next.js `pusattalilanyard/src` & Astro `apps/frontend` diabaikan per instruksi.

---

## Fase 0 — Audit Existing (30 menit)

**Check manual via curl `http://127.0.0.1:8005/` per route:**

- `/`, `/produk`, `/area`, `/cara-pemesanan`, `/daftar-harga`, `/promo`, `/blog`, `/blog/{slug}`
- Title: cek duplikat, length >60 char, fallback generic "Pusat Tali Lanyard"
- Meta desc: cuma `@yield('meta_description')`, tidak ada di semua page. Homepage pakai default 1 kalimat.
- Canonical: TIDAK ADA
- OG/Twitter: TIDAK ADA
- Robots meta: TIDAK ADA
- `public/robots.txt`: isi hanya `User-agent: * Disallow:` kosongan, no sitemap directive.
- Sitemap: no route `/sitemap.xml`, cek `/sitemap.xml` 404.
- Images: hero sudah alt, trust baru `/images/trust/trust-1..5.webp` alt masih "Lanyard custom" generic, perlu keyword.
- Headings: home ada 1 H1 OK, tapi title/desc ukuran token identik sudah Fase 3.
- Lighthouse SEO score prediksi ~82-88 karena missing canonical/OG.

**Output audit:** file `storage/logs/seo-audit-{{date}}.md` list temuan per URL.

---

## Fase 1 — Base SEO Component (45 menit)

Buat `resources/views/components/seo.blade.php`:

```blade
@props([
  'title' => null,
  'description' => null,
  'canonical' => null,
  'ogImage' => null,
  'type' => 'website', // website|article
  'noindex' => false,
  'publishedTime' => null,
  'modifiedTime' => null,
])
@php
  $siteUrl = rtrim(config('app.url', url('/')), '/');
  $defaultTitle = 'Pusat Tali Lanyard | Cetak Lanyard Custom Cepat & Berkualitas';
  $defaultDesc = 'Cetak lanyard custom premium di Indonesia dengan pilihan bahan berkualitas, desain profesional, dan layanan pemesanan mudah. Bebas ongkir Indonesia Raya.';
  $finalTitle = $title ?: $defaultTitle;
  $finalDesc = $description ?: $defaultDesc;
  $finalCanonical = $canonical ?: rtrim(url()->current(), '/');
  // strip ?q search dari canonical blog/produk
  $finalCanonical = strtok($finalCanonical, '?');
  $finalImage = $ogImage ? url($ogImage) : url('/uploads/pusat-tali-lanyard-logo.webp');
@endphp
<title>{{ $finalTitle }}</title>
<meta name="description" content="{{ Str::limit($finalDesc, 160) }}">
<link rel="canonical" href="{{ $finalCanonical }}">
<meta name="robots" content="{{ $noindex ? 'noindex, follow' : 'index, follow, max-image-preview:large' }}">
<meta name="author" content="Pusat Tali Lanyard">
<meta name="theme-color" content="#0f172a">

{{-- Open Graph --}}
<meta property="og:site_name" content="Pusat Tali Lanyard">
<meta property="og:locale" content="id_ID">
<meta property="og:title" content="{{ $finalTitle }}">
<meta property="og:description" content="{{ Str::limit($finalDesc, 160) }}">
<meta property="og:url" content="{{ $finalCanonical }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:image" content="{{ $finalImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $finalTitle }}">
<meta name="twitter:description" content="{{ Str::limit($finalDesc, 160) }}">
<meta name="twitter:image" content="{{ $finalImage }}">

{{-- Preconnect perf --}}
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
```

Integrasi di `layouts/public.blade.php`:

- Hapus `<title>@yield` dan meta description lama.
- Ganti dengan `@include('components.seo')` + `@stack('seo')` untuk tambahan per-page.
- Tambah `@stack('schema')` sebelum `</head>`.

Defaults diambil dari `@yield('seo_title')`, `@yield('seo_desc')` biar backward compatible.

---

## Fase 2 — Canonical, Robots, Sitemap (60 menit)

**Canonical Rules:**
- `url()->current()` trim trailing slash.
- Blog search `?q=` dan `?page=` → canonical tanpa query (kecuali paginasi? SEO best: noindex page >1 atau canonical ke base + noindex follow).
- Produk `?q=` search → canonical stripped.

**Robots.txt:**
- Ganti static `public/robots.txt` jadi route dinamis atau update file:
```
User-agent: *
Allow: /
Disallow: /kawruh/
Disallow: /api/
Disallow: /login/
Disallow: /storage/
Disallow: /*?q=
Disallow: /*?preview-*

Sitemap: https://pusattalilanyard.com/sitemap.xml
Host: https://pusattalilanyard.com
```

**Sitemap.xml:**

Tambah di `routes/web.php`:
```php
Route::get('/sitemap.xml', function () {
  $cacheKey = 'sitemap_xml';
  return Cache::remember($cacheKey, 3600, function () {
    $base = rtrim(config('app.url'), '/');
    $urls = collect([
      ['loc' => '/', 'lastmod' => now(), 'priority' => '1.0', 'freq' => 'daily'],
      ['loc' => '/produk', 'lastmod' => now(), 'priority' => '0.9'],
      ['loc' => '/area', 'lastmod' => now(), 'priority' => '0.8'],
      ['loc' => '/cara-pemesanan', 'lastmod' => now(), 'priority' => '0.7'],
      ['loc' => '/blog', 'lastmod' => now(), 'priority' => '0.8'],
    ]);
    $products = \App\Models\Product::where('published', true)->orderBy('updatedAt','desc')->get(['slug','updatedAt']);
    $posts = \App\Models\Post::where('published', true)->orderBy('updatedAt','desc')->get(['slug','updatedAt']);
    // merge...
    return response()->view('components.sitemap', compact('urls','products','posts','base'))->header('Content-Type','text/xml');
  });
});
```

View `components/sitemap.blade.php` generate `<urlset>`.

---

## Fase 3 — Schema.org JSON-LD (90 menit)

Struktur: buat `resources/views/components/schema/*.blade.php` atau inline via `@push('schema')`.

**Homepage (`pages/home.blade.php`):**

1. `Organization` + `LocalBusiness`
```json
{
  "@type": "LocalBusiness",
  "@id": "https://pusattalilanyard.com/#organization",
  "name": "Pusat Tali Lanyard",
  "url": "https://pusattalilanyard.com",
  "logo": ".../logo.webp",
  "image": ".../logo.webp",
  "telephone": "+6282210200700",
  "address": {
    "PostalAddress": "Indonesia, Banten, ID"
  },
  "sameAs": ["https://wa.me/6282210200700", "https://www.instagram.com/..."],
  "priceRange": "Rp 1000 - Rp 50000"
}
```

2. `WebSite` dengan `SearchAction` target `/produk?q={search}` dan `/blog?q={search}`

3. `BreadcrumbList` Home

**Blog Index:** `CollectionPage` + `ItemList` 3 posts

**Blog Show (`pages/blog/show.blade.php`):**
- `Article` (headline, description, author User->name, datePublished createdAt, dateModified updatedAt, image featuredImage, publisher org)
- `BreadcrumbList` Home > Blog > {title}
- `FAQPage` jika ada FAQ di artikel? Reuse 5 FAQ homepage.

**Produk Index:** `CollectionPage` + `ItemList` produk 8 items

**Area Jangkauan / Locations:** `ServiceArea` + `Offer` Bebas Ongkir + `PostalAddress` Indonesia Raya

**FAQ Section:** `FAQPage` dari 5 faqs existing di `components/faq.blade.php`

Implementasi:
```blade
@push('schema')
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
```

---

## Fase 4 — On-Page Fixes (45 menit)

- Setiap page 1 H1 only (home sudah OK).
- Title ≤60 char, desc 150-160 char. Buat helper `seoTitle($post)` trim.
- Alt text: trust images `alt="Lanyard custom {{ area }} di Indonesia - {{ company }}"` biar variasi keyword.
- Internal linking: pastikan footer & CTA punya anchor ke /produk, /area.
- Add `aria-label` di slider hint.
- Ensure `content-visibility: visible` untuk LCP sections (#hero, #trusted, #products, #locations...) — sudah Fase 3 done.

---

## Fase 5 — Validation (30 menit)

- `php artisan view:clear && php artisan config:clear`
- `curl -s http://127.0.0.1:8005/ | grep -i canonical` → harus ada
- `curl -s http://127.0.0.1:8005/blog | grep og:`
- `curl /sitemap.xml` → valid XML 200
- `curl /robots.txt` → contains sitemap
- Rich Results Test: https://search.google.com/test/rich-results → test Article, FAQPage, LocalBusiness
- Lighthouse CI: SEO ≥95, Best Practices ≥95

---

## File Yang Disentuh

- `resources/views/layouts/public.blade.php` (ganti head meta)
- `resources/views/components/seo.blade.php` (baru)
- `resources/views/components/sitemap.blade.php` (baru)
- `resources/views/components/schema/home.blade.php`, `blog.blade.php`, `faq.blade.php` (baru)
- `routes/web.php` (tambah sitemap route)
- `public/robots.txt` (update)
- `resources/views/pages/home.blade.php`, `pages/blog/show.blade.php`, `pages/blog/index.blade.php` (inject schema)
- `resources/views/components/testimonials.blade.php` alt improvement
- `public/vendor/scroll/scroll-reveal.css` & `.js` (sudah Fase 4.2 fix hero+trust+loc)

## Estimasi Total: 4.5 jam

## Catatan Pitfall Blade

- Jangan pakai `??` atau `?->` di dalam `@php` → ParseError PHP 8.2. Pakai `isset()`.
- `filemtime()` di layout sudah di-wrap `file_exists()` — jangan ubah.
- Splide wheel hijack harus cek `closest('.product-splide')` hover pause — sudah.
- `line-clamp` di title sudah dihapus Fase 2 biar no `...`.

