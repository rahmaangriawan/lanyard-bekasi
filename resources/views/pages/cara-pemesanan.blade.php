@extends('layouts.public')

@section('title', 'Cara Pemesanan Lanyard Custom dalam 5 Langkah')
@section('meta_description', 'Pesan lanyard custom melalui lima langkah jelas, mulai dari memilih spesifikasi, mengirim logo, review desain dan quotation, pembayaran, hingga pengiriman.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/cara-pemesanan')
@section('seo_og_image', '/uploads/hero-cara-pemesanan-pusat-tali-lanyard.webp')

@push('head')
  <link rel="stylesheet" href="/css/ordering-index.css?v={{ file_exists(public_path('css/ordering-index.css')) ? filemtime(public_path('css/ordering-index.css')) : 1 }}">
@endpush

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text=Halo%20Pusat%20Tali%20Lanyard%2C%20saya%20mau%20konsultasi%20pemesanan%20lanyard%20custom.';
  $orderSteps = [
    ['title' => 'Pilih produk dan spesifikasi', 'description' => 'Tentukan jenis lanyard, bahan, ukuran, jumlah, dan aksesoris yang sesuai dengan kebutuhan Anda.'],
    ['title' => 'Kirim logo dan kebutuhan desain', 'description' => 'Kirim file logo, warna brand, teks, serta referensi desain kepada tim kami.'],
    ['title' => 'Review mockup dan quotation', 'description' => 'Periksa mockup digital serta rincian penawaran sebelum memberikan persetujuan.'],
    ['title' => 'Konfirmasi pembayaran', 'description' => 'Lakukan pembayaran sesuai quotation yang telah disetujui untuk memulai produksi.'],
    ['title' => 'Produksi dan pengiriman', 'description' => 'Pesanan diproduksi, diperiksa, dikemas, lalu dikirim ke alamat yang telah dikonfirmasi.'],
  ];
  $preparationItems = ['Jumlah pesanan', 'Ukuran dan bahan', 'File logo', 'Target waktu', 'Alamat pengiriman'];
@endphp

<div class="ordering-index">
  <section class="ordering-index__hero" aria-labelledby="ordering-title">
    <div class="ordering-index__container ordering-index__hero-grid">
      <div class="ordering-index__intro">
        <p class="ordering-index__eyebrow">Cara Pemesanan</p>
        <h1 id="ordering-title">Pesan Lanyard Custom dalam 5 Langkah</h1>
        <p>Mulai dari konsultasi dan persetujuan desain hingga produksi serta pengiriman ke alamat Anda.</p>
      </div>

      <div class="ordering-index__visual" aria-hidden="true">
        <div class="ordering-index__visual-frame">
          <img src="/uploads/hero-cara-pemesanan-pusat-tali-lanyard.webp" alt="" width="1254" height="1254" decoding="async" fetchpriority="high">
        </div>
      </div>
    </div>
  </section>

  <section class="ordering-index__process" aria-labelledby="ordering-process-title">
    <div class="ordering-index__container">
      <header class="ordering-index__section-header">
        <h2 id="ordering-process-title">Langkah Pemesanan</h2>
        <p>Ikuti alur berikut agar spesifikasi, desain, dan tujuan pengiriman dapat kami proses dengan tepat.</p>
      </header>

      <div class="ordering-index__content">
        <div class="ordering-index__timeline" role="list">
          @foreach($orderSteps as $index => $step)
            <article class="ordering-index__step" id="langkah-{{ $index + 1 }}" role="listitem">
              <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
              <div>
                <h3>{{ $step['title'] }}</h3>
                <p>{{ $step['description'] }}</p>
              </div>
            </article>
          @endforeach
        </div>

        <aside class="ordering-index__preparation" aria-labelledby="ordering-preparation-title">
          <p class="ordering-index__preparation-label">Sebelum Menghubungi Kami</p>
          <h2 id="ordering-preparation-title">Siapkan Detail Pesanan</h2>
          <p>Informasi berikut membantu tim kami memberikan rekomendasi dan quotation yang lebih sesuai.</p>

          <ul>
            @foreach($preparationItems as $item)
              <li>
                <span class="material-symbols-outlined" aria-hidden="true">check</span>
                <span>{{ $item }}</span>
              </li>
            @endforeach
          </ul>

          <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" data-button-motion>
            <span data-button-label>Mulai Konsultasi</span>
          </a>
        </aside>
      </div>
    </div>
  </section>
</div>

@include('components.footer')

@push('schema')
  @include('components.schema.ordering', ['orderSteps' => $orderSteps])
@endpush
@endsection
