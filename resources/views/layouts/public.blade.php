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

    {{-- Bundled CSS: Tailwind + colors + global + header + hero + fonts (minified) --}}
    @php
        $publicCache = \Illuminate\Support\Facades\Cache::store(config('cache.public_store', 'file'));
        $assetsVersion = $publicCache->remember('assets_version_hashes_v4', 86400, function () {
            $appCssPath = public_path('css/app.min.css');
            $srCssPath = public_path('vendor/scroll/scroll-reveal.css');
            $srJsPath = public_path('vendor/scroll/scroll-reveal.js');
            $publicSiteJsPath = public_path('js/public-site.js');
            return [
                'appCssV' => file_exists($appCssPath) ? filemtime($appCssPath) : 1,
                'srCssV' => file_exists($srCssPath) ? filemtime($srCssPath) : 1,
                'srJsV' => file_exists($srJsPath) ? filemtime($srJsPath) : 1,
                'publicSiteJsV' => file_exists($publicSiteJsPath) ? filemtime($publicSiteJsPath) : 1,
            ];
        });
        $appCssV = $assetsVersion['appCssV'];
        $headerCssPath = public_path('css/header.css');
        $headerCssV = file_exists($headerCssPath) ? filemtime($headerCssPath) : 1;
        $footerCssPath = public_path('css/footer.css');
        $footerCssV = file_exists($footerCssPath) ? filemtime($footerCssPath) : 1;
        $floatingWhatsappCssPath = public_path('css/floating-whatsapp.css');
        $floatingWhatsappCssV = file_exists($floatingWhatsappCssPath) ? filemtime($floatingWhatsappCssPath) : 1;
        $floatingWhatsappJsPath = public_path('js/floating-whatsapp.js');
        $floatingWhatsappJsV = file_exists($floatingWhatsappJsPath) ? filemtime($floatingWhatsappJsPath) : 1;
        $buttonMotionCssPath = public_path('css/button-motion.css');
        $buttonMotionCssV = file_exists($buttonMotionCssPath) ? filemtime($buttonMotionCssPath) : 1;
        $buttonMotionJsPath = public_path('js/button-motion.js');
        $buttonMotionJsV = file_exists($buttonMotionJsPath) ? filemtime($buttonMotionJsPath) : 1;
        $srCssV = $assetsVersion['srCssV'];
        $srJsV = $assetsVersion['srJsV'];
        $publicSiteJsV = $assetsVersion['publicSiteJsV'];
        $inlinePublicCss = $publicCache->remember(
            'inline_public_css_v2_'.$appCssV.'_'.$srCssV,
            86400,
            fn () => (file_get_contents(public_path('css/app.min.css')) ?: '')
                .(file_get_contents(public_path('vendor/scroll/scroll-reveal.css')) ?: '')
        );
    @endphp
    <style data-public-critical-css>{!! $inlinePublicCss !!}</style>
    <link rel="stylesheet" href="/css/header.css?v={{ $headerCssV }}">
    <link rel="stylesheet" href="/css/footer.css?v={{ $footerCssV }}">
    <link rel="stylesheet" href="/css/floating-whatsapp.css?v={{ $floatingWhatsappCssV }}">
    <link rel="stylesheet" href="/css/button-motion.css?v={{ $buttonMotionCssV }}">
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
      /* Pre-allocate logo space to prevent CLS */
      .site-header__logo img { aspect-ratio: 600/223; object-fit: contain; }
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
    <script src="/js/public-site.js?v={{ $publicSiteJsV }}" defer></script>
    <script src="/js/button-motion.js?v={{ $buttonMotionJsV }}" defer></script>
    <script src="/js/floating-whatsapp.js?v={{ $floatingWhatsappJsV }}" defer></script>
    {{-- Scroll Reveal Engine — Mode A --}}
    <script src="/vendor/scroll/scroll-reveal.js?v={{ $srJsV }}" defer></script>
</body>
</html>
