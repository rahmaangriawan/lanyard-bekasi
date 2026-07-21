@extends('layouts.public')

@php
    $homeSeoSettings = \Illuminate\Support\Facades\Cache::store(config('cache.public_store', 'file'))->remember('home_seo_settings', 3600, function () {
        return \App\Models\Setting::whereIn('key', ['seo_meta_title', 'seo_meta_description'])->pluck('value', 'key')->toArray();
    });
    $homeTitle = $homeSeoSettings['seo_meta_title'] ?? 'Pusat Tali Lanyard | Cetak Lanyard Custom Cepat & Berkualitas';
    $homeDesc = $homeSeoSettings['seo_meta_description'] ?? 'Cetak lanyard custom premium di Indonesia dengan pilihan bahan berkualitas, desain profesional, dan layanan pemesanan mudah. Bebas ongkir Indonesia Raya.';
@endphp
@section('title', $homeTitle)
@section('meta_description', $homeDesc)
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/')
@section('seo_og_image', '/uploads/pusat-tali-lanyard-logo.webp')

@push('head')
@php
    $heroCssPath = public_path('css/hero.css');
    $heroCssVersion = file_exists($heroCssPath) ? filemtime($heroCssPath) : 1;
    $homePricingCssPath = public_path('css/home-pricing.css');
    $homePricingCssVersion = file_exists($homePricingCssPath) ? filemtime($homePricingCssPath) : 1;
    $homepageCssPath = public_path('css/homepage.css');
    $homepageCssVersion = file_exists($homepageCssPath) ? filemtime($homepageCssPath) : 1;
    $homeAboutCssPath = public_path('css/home-about.css');
    $homeAboutCssVersion = file_exists($homeAboutCssPath) ? filemtime($homeAboutCssPath) : 1;
    $homeBenefitsCssPath = public_path('css/home-benefits.css');
    $homeBenefitsCssVersion = file_exists($homeBenefitsCssPath) ? filemtime($homeBenefitsCssPath) : 1;
    $homeGalleryCssPath = public_path('css/home-gallery.css');
    $homeGalleryCssVersion = file_exists($homeGalleryCssPath) ? filemtime($homeGalleryCssPath) : 1;
    $homeGalleryJsPath = public_path('js/home-gallery.js');
    $homeGalleryJsVersion = file_exists($homeGalleryJsPath) ? filemtime($homeGalleryJsPath) : 1;
    $homeTestimonialsCssPath = public_path('css/home-testimonials.css');
    $homeTestimonialsCssVersion = file_exists($homeTestimonialsCssPath) ? filemtime($homeTestimonialsCssPath) : 1;
    $homeTestimonialsJsPath = public_path('js/home-testimonials.js');
    $homeTestimonialsJsVersion = file_exists($homeTestimonialsJsPath) ? filemtime($homeTestimonialsJsPath) : 1;
    $homeFaqCssPath = public_path('css/home-faq.css');
    $homeFaqCssVersion = file_exists($homeFaqCssPath) ? filemtime($homeFaqCssPath) : 1;
    $homeCtaCssPath = public_path('css/home-cta.css');
    $homeCtaCssVersion = file_exists($homeCtaCssPath) ? filemtime($homeCtaCssPath) : 1;
    $homeProductsCssPath = public_path('css/home-products.css');
    $homeProductsCssVersion = file_exists($homeProductsCssPath) ? filemtime($homeProductsCssPath) : 1;
    $homeLocationsCssPath = public_path('css/home-locations.css');
    $homeLocationsCssVersion = file_exists($homeLocationsCssPath) ? filemtime($homeLocationsCssPath) : 1;
@endphp
<link rel="stylesheet" href="/css/hero.css?v={{ $heroCssVersion }}">
<link rel="stylesheet" href="/css/home-pricing.css?v={{ $homePricingCssVersion }}">
<link rel="stylesheet" href="/css/homepage.css?v={{ $homepageCssVersion }}">
<link rel="stylesheet" href="/css/home-about.css?v={{ $homeAboutCssVersion }}">
<link rel="stylesheet" href="/css/home-benefits.css?v={{ $homeBenefitsCssVersion }}">
<link rel="stylesheet" href="/css/home-gallery.css?v={{ $homeGalleryCssVersion }}">
<link rel="stylesheet" href="/css/home-testimonials.css?v={{ $homeTestimonialsCssVersion }}">
<link rel="stylesheet" href="/css/home-faq.css?v={{ $homeFaqCssVersion }}">
<link rel="stylesheet" href="/css/home-cta.css?v={{ $homeCtaCssVersion }}">
<link rel="stylesheet" href="/css/home-products.css?v={{ $homeProductsCssVersion }}">
<link rel="stylesheet" href="/css/home-locations.css?v={{ $homeLocationsCssVersion }}">
@endpush

@push('schema')
@include('components.schema.home')
@endpush

@section('content')
  <div class="homepage-content">
    @include('components.hero')
    @include('components.client-logos')
    @include('components.home-pricing')
    @include('components.home-about')
    @include('components.home-benefits')
    @include('components.home-gallery')
    @include('components.testimonials')
    @include('components.product-showcase')
    @include('components.service-locations')
    @include('components.faq')
    @include('components.blog-preview')
    @include('components.cta')
  </div>
  @include('components.footer')
@endsection

@section('scripts')
  <script src="/js/home-gallery.js?v={{ $homeGalleryJsVersion }}" defer></script>
  <script src="/js/home-testimonials.js?v={{ $homeTestimonialsJsVersion }}" defer></script>
@endsection
