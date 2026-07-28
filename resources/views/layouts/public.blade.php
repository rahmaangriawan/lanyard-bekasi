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
    <link rel="icon" type="image/webp" href="/uploads/pusat-tali-lanyard-favicon.webp">
    <link rel="shortcut icon" type="image/webp" href="/uploads/pusat-tali-lanyard-favicon.webp">
    <link rel="apple-touch-icon" href="/uploads/pusat-tali-lanyard-favicon.webp">

    {{-- Web App Manifest --}}
    <link rel="manifest" href="/site.webmanifest">

    {{-- Preload Critical Fonts --}}
    <link rel="preload" href="/font-assets/geist-400" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/font-assets/geist-700" as="font" type="font/woff2" crossorigin>

    @if(request()->is('/'))
    <link rel="preload" href="/uploads/hero-pusat-tali-lanyard-office.webp" as="image" fetchpriority="high">
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

      /* Disable ALL layout transitions in header to prevent CLS */
      .site-header,
      .site-header *,
      .site-header *::before,
      .site-header *::after {
        transition: none !important;
        transition-duration: 0s !important;
        animation-duration: 0s !important;
      }

      /* Force strict structural layouts for Header to prevent any CLS */
      .site-header {
        height: 76px !important;
        min-height: 76px !important;
      }
      .site-header__logo {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 118px !important;
        height: 44px !important;
        min-width: 118px !important;
        flex: 0 0 118px !important;
      }
      .site-header__logo img {
        aspect-ratio: 118/44 !important;
        width: 118px !important;
        height: 44px !important;
        object-fit: contain !important;
      }
      .site-header__links {
        display: flex !important; /* Force show in desktop immediately to prevent CLS */
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        min-width: 320px !important; /* Reserve enough space to absorb font-swap width differences */
        flex: 1 1 auto !important;
      }
      .site-header__hamburger {
        display: none !important; /* Hide by default in desktop to prevent app.min.css default flex layout shift */
        width: 44px !important;
        height: 44px !important;
        flex: 0 0 44px !important;
      }
      .site-header__actions {
        display: flex !important; /* Force show in desktop immediately to prevent CLS */
        width: 146px !important;
        height: 44px !important;
        flex: 0 0 146px !important;
        min-width: 146px !important;
      }
      .site-header__btn {
        width: 146px !important;
        height: 44px !important;
        min-height: 44px !important;
        padding: 0 !important;
      }

      /* Mobile Overrides (< 1023px) */
      @media (max-width: 1023px) {
        .site-header {
          height: 72px !important;
          min-height: 72px !important;
        }
        .site-header__logo {
          width: 118px !important;
          height: 44px !important;
          min-width: 118px !important;
          flex: 0 0 118px !important;
        }
        .site-header__logo img {
          width: 118px !important;
          height: 44px !important;
        }
        .site-header__links {
          display: none !important; /* Hide links in mobile view */
        }
        .site-header__hamburger {
          display: flex !important; /* Show hamburger only in mobile view */
        }
        .site-header__actions {
          display: none !important;
        }
      }

      #client-logos-section { min-height: 198px; }
      .logo-marquee { min-height: 32px; }
      /* Prevent Material Symbols text fallback layout shift */
      .material-symbols-outlined {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center;
        width: 24px;
        height: 24px;
        overflow: hidden;
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
      /* Custom size overrides for specific icons to prevent scaling shift */
      .ptl-hero__cta .material-symbols-outlined { width: 19px; height: 19px; font-size: 19px !important; }
      .ptl-hero__trust .material-symbols-outlined { width: 20px; height: 20px; font-size: 20px !important; }
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
