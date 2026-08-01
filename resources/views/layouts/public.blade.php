<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- SEO Foundation: Base component, fallback to legacy yields --}}
    @php
      $__seoTitle = null;
      $__seoDesc = null;
      $__seoCanonical = null;
      if (View::hasSection('title')) { $__seoTitle = trim($__env->yieldContent('title')); }
      if (View::hasSection('meta_description')) { $__seoDesc = trim($__env->yieldContent('meta_description')); }
      if (View::hasSection('seo_title')) { $__seoTitle = trim($__env->yieldContent('seo_title')); }
      if (View::hasSection('seo_desc')) { $__seoDesc = trim($__env->yieldContent('seo_desc')); }
      if (View::hasSection('seo_canonical')) { $__seoCanonical = trim($__env->yieldContent('seo_canonical')); }
      $__seoNoIndex = false;
      if (View::hasSection('seo_noindex')) { $__seoNoIndex = true; }
      $__seoNoCanonical = false;
      if (View::hasSection('seo_no_canonical')) { $__seoNoCanonical = true; }
      $__seoType = 'website';
      if (View::hasSection('seo_type')) { $__seoType = trim($__env->yieldContent('seo_type')); if ($__seoType === '') $__seoType = 'website'; }
      $__seoOgImage = null;
      if (View::hasSection('seo_og_image')) { $__seoOgImage = trim($__env->yieldContent('seo_og_image')); }
      $__seoPublishedTime = View::hasSection('seo_published_time') ? trim($__env->yieldContent('seo_published_time')) : null;
      $__seoModifiedTime = View::hasSection('seo_modified_time') ? trim($__env->yieldContent('seo_modified_time')) : null;
    @endphp
    @include('components.seo', [
      'title' => $__seoTitle ?: null,
      'description' => $__seoDesc ?: null,
      'canonical' => $__seoNoCanonical ? false : ($__seoCanonical ?: null),
      'ogImage' => $__seoOgImage ?: null,
      'type' => $__seoType,
      'noindex' => $__seoNoIndex,
      'publishedTime' => $__seoPublishedTime,
      'modifiedTime' => $__seoModifiedTime,
    ])
    @stack('seo')

    {{-- Brand favicon --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/webp" sizes="1024x1024" href="/uploads/lanyard-bekasi-favicon.webp">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

    {{-- Web App Manifest --}}
    <link rel="manifest" href="/site.webmanifest">

    {{-- Preload Critical Fonts --}}
    <link rel="preload" href="/font-assets/geist-400" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/font-assets/geist-700" as="font" type="font/woff2" crossorigin>

    @if(request()->is('/'))
    <link rel="preload" href="/uploads/hero-lanyard-bekasi-custom-01.webp" as="image" type="image/webp" fetchpriority="high">
    @endif
    @if(request()->routeIs('ordering'))
    <link rel="preload" href="/uploads/hero-cara-pemesanan-lanyard-bekasi.webp" as="image" type="image/webp" fetchpriority="high">
    @endif

    {{-- Bundled CSS: Tailwind + colors + global + header + hero + fonts (minified) --}}
    @php
        $publicCache = \Illuminate\Support\Facades\Cache::store(config('cache.public_store', 'file'));
        $assetsVersion = $publicCache->remember('assets_version_hashes_v5', 86400, function () {
            $appCssPath = public_path('build/public/site.min.css');
            $publicSiteJsPath = public_path('build/public/site.min.js');
            return [
                'appCssV' => file_exists($appCssPath) ? filemtime($appCssPath) : 1,
                'publicSiteJsV' => file_exists($publicSiteJsPath) ? filemtime($publicSiteJsPath) : 1,
            ];
        });
        $appCssV = $assetsVersion['appCssV'];
        $publicSiteJsV = $assetsVersion['publicSiteJsV'];
    @endphp
    <link rel="stylesheet" href="/build/public/site.min.css?v={{ $appCssV }}">
    {{-- Scroll Reveal — Mode A Vanilla Light --}}

    {{-- Legacy sections support: blog show pakai @section('head') untuk article CSS (harus tetap dirender) --}}
    @hasSection('head')
      @yield('head')
    @endif
    @stack('head')

    <style>
      .bento-item { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
      .bento-item:hover { transform: translateY(-4px); }
      .glass { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
      .no-scrollbar::-webkit-scrollbar { display: none; }
      .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
      /* Optimization: Lazy render offscreen sections — disable for first hero to allow reveal */
      .lazy-section { content-visibility: auto; contain-intrinsic-size: auto 500px; }
      #hero { content-visibility: visible; }

      /* Reserve the final header geometry before the public bundle is parsed. */
      .site-header { height: 72px !important; min-height: 72px !important; }
      .site-header__logo {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 154px !important;
        height: 42px !important;
        min-width: 154px !important;
      }
      .site-header__logo img {
        aspect-ratio: 500 / 122 !important;
        width: 138px !important;
        height: auto !important;
        object-fit: contain !important;
      }
      .site-header__links {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
      }
      .site-header__hamburger {
        display: none !important;
        width: 44px !important;
        height: 44px !important;
      }

      @media (max-width: 767px) {
        .site-header { height: 68px !important; min-height: 68px !important; }
        .site-header__logo {
          width: 140px !important;
          height: 40px !important;
          min-width: 140px !important;
        }
        .site-header__logo img { width: 126px !important; }
        .site-header__links { display: none !important; }
        .site-header__hamburger { display: flex !important; }
      }

      /* Prevent Material Symbols text fallback layout shift */
      .material-symbols-outlined {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center;
        font-size: 24px !important;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-smoothing: antialiased;
        vertical-align: middle;
      }
    </style>

    @stack('schema')
</head>
<body class="bg-slate-50 text-slate-900">

    <x-header />

    <main>
        @yield('content')
    </main>

    <x-floating-whatsapp />

    @yield('scripts')
    <script src="/build/public/site.min.js?v={{ $publicSiteJsV }}" defer></script>
    {{-- Scroll Reveal Engine — Mode A --}}
</body>
</html>
