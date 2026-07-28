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
    $homeCssPath = public_path('build/public/home.min.css');
    $homeCssVersion = file_exists($homeCssPath) ? filemtime($homeCssPath) : 1;
    $homeJsPath = public_path('build/public/home.min.js');
    $homeJsVersion = file_exists($homeJsPath) ? filemtime($homeJsPath) : 1;
@endphp
<link rel="stylesheet" href="/build/public/home.min.css?v={{ $homeCssVersion }}">
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
  <script src="/build/public/home.min.js?v={{ $homeJsVersion }}" defer></script>
@endsection
