@extends('layouts.public')

@section('title', 'Area Pengiriman Lanyard Custom ke Seluruh Indonesia')
@section('meta_description', 'Pusat Tali Lanyard melayani pengiriman lanyard custom ke seluruh Indonesia dengan cakupan utama Jakarta, Bogor, Depok, Tangerang, dan Bekasi.')
@section('seo_og_image', '/uploads/hero-katalog-pusat-tali-lanyard.webp')

@push('head')
  <link rel="stylesheet" href="/css/area-index.css?v={{ file_exists(public_path('css/area-index.css')) ? filemtime(public_path('css/area-index.css')) : 1 }}">
@endpush

@section('content')
@php
  $primaryAreas = ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi'];
  $shippingSteps = [
    ['number' => '01', 'title' => 'Konsultasi kebutuhan', 'description' => 'Sampaikan jumlah, desain, bahan, dan lokasi tujuan kepada admin kami.'],
    ['number' => '02', 'title' => 'Produksi dan pengecekan', 'description' => 'Pesanan diproduksi sesuai persetujuan lalu diperiksa sebelum dikemas.'],
    ['number' => '03', 'title' => 'Pengiriman ke lokasi', 'description' => 'Pesanan dikirim melalui ekspedisi menuju alamat yang telah dikonfirmasi.'],
  ];
  $shippingWhatsapp = 'https://wa.me/6282210200700?text=Halo%20Pusat%20Tali%20Lanyard%2C%20saya%20mau%20konsultasi%20pengiriman%20lanyard%20custom.';
@endphp

<div class="area-index">
  <section class="area-index__hero" aria-labelledby="area-index-title">
    <div class="area-index__container area-index__hero-grid">
      <div class="area-index__intro">
        <p class="area-index__eyebrow">Area Pengiriman</p>
        <h1 id="area-index-title">Lanyard Custom, Dikirim ke Seluruh Indonesia</h1>
        <p>Kami melayani pengiriman nasional dengan cakupan utama Jabodetabek untuk kebutuhan perusahaan, sekolah, komunitas, dan event.</p>
      </div>

      <div class="area-index__map">
        <iframe
          src="https://www.google.com/maps?q=Indonesia&amp;z=4&amp;output=embed"
          title="Peta jangkauan pengiriman Pusat Tali Lanyard di Indonesia"
          loading="lazy"
          allowfullscreen=""
          referrerpolicy="strict-origin-when-cross-origin"
        ></iframe>
      </div>
    </div>
  </section>

  <section class="area-index__coverage" aria-labelledby="area-coverage-title">
    <div class="area-index__container">
      <header class="area-index__section-header">
        <h2 id="area-coverage-title">Cakupan Utama Jabodetabek</h2>
        <p>Lima wilayah utama untuk kebutuhan pengiriman lanyard custom dan perlengkapan identitas.</p>
      </header>

      <div class="area-index__area-grid" role="list" aria-label="Cakupan pengiriman utama Jabodetabek">
        @foreach($primaryAreas as $area)
          <article class="area-index__area" role="listitem">
            <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
            <div>
              <h3>{{ $area }}</h3>
              <p>Area pengiriman utama</p>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="area-index__process" aria-labelledby="area-process-title">
    <div class="area-index__container">
      <header class="area-index__section-header">
        <h2 id="area-process-title">Alur Pengiriman Pesanan</h2>
        <p>Proses ringkas dari konsultasi awal hingga pesanan dikirim ke lokasi Anda.</p>
      </header>

      <div class="area-index__steps" role="list">
        @foreach($shippingSteps as $step)
          <article class="area-index__step" role="listitem">
            <span>{{ $step['number'] }}</span>
            <h3>{{ $step['title'] }}</h3>
            <p>{{ $step['description'] }}</p>
          </article>
        @endforeach
      </div>

      <a class="area-index__cta" href="{{ $shippingWhatsapp }}" target="_blank" rel="noopener noreferrer" data-button-motion>
        <span data-button-label>Konsultasi Pengiriman</span>
      </a>
    </div>
  </section>

  @if($cityPages->isNotEmpty())
    <section class="area-index__cities" aria-labelledby="area-cities-title">
      <div class="area-index__container">
        <header class="area-index__section-header">
          <h2 id="area-cities-title">Informasi Layanan per Kota</h2>
          <p>Pilih kota untuk melihat informasi layanan dan pemesanan yang lebih spesifik.</p>
        </header>

        <div class="area-index__city-grid" role="list">
          @foreach($cityPages as $cityPage)
            <a href="{{ route('area.show', $cityPage->slug) }}" role="listitem">
              <span>{{ $cityPage->title }}</span>
              <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
            </a>
          @endforeach
        </div>
      </div>
    </section>
  @endif
</div>

@include('components.footer')

@push('schema')
  @include('components.schema.area')
@endpush
@endsection
