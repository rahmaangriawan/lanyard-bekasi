@extends('layouts.public')

@section('title', 'Cara Pemesanan Lanyard Custom dalam 6 Langkah')
@section('meta_description', 'Pesan lanyard custom melalui enam langkah jelas, mulai dari konsultasi, memilih spesifikasi, mengirim desain, review quotation, pembayaran, hingga pengiriman.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/cara-pemesanan')
@section('seo_og_image', '/uploads/hero-cara-pemesanan-lanyard-bekasi.webp')

@push('head')
  <link rel="stylesheet" href="/css/service-pages.css?v={{ file_exists(public_path('css/service-pages.css')) ? filemtime(public_path('css/service-pages.css')) : 1 }}">
  <link rel="stylesheet" href="/css/ordering-index.css?v={{ file_exists(public_path('css/ordering-index.css')) ? filemtime(public_path('css/ordering-index.css')) : 1 }}">
@endpush

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya mau konsultasi pemesanan lanyard custom.');

  $trustItems = [
    ['icon' => 'forum', 'title' => 'Konsultasi Spesifikasi', 'description' => 'Dibantu memilih kebutuhan'],
    ['icon' => 'design_services', 'title' => 'Mockup Digital', 'description' => 'Diperiksa sebelum produksi'],
    ['icon' => 'verified', 'title' => 'Quality Control', 'description' => 'Hasil diperiksa sebelum kirim'],
    ['icon' => 'local_shipping', 'title' => 'Resi Pengiriman', 'description' => 'Status paket dapat dipantau'],
  ];

  $orderSteps = [
    ['icon' => 'chat', 'title' => 'Konsultasikan kebutuhan', 'description' => 'Sampaikan penggunaan lanyard, jumlah pesanan, target waktu, dan kisaran kebutuhan Anda.'],
    ['icon' => 'sell', 'title' => 'Pilih produk dan spesifikasi', 'description' => 'Tentukan bahan, lebar tali, aksesori, ukuran, dan detail produk bersama tim kami.'],
    ['icon' => 'palette', 'title' => 'Kirim logo dan referensi desain', 'description' => 'Kirim logo, warna brand, teks, atau referensi visual yang ingin digunakan pada lanyard.'],
    ['icon' => 'verified', 'title' => 'Review mockup dan quotation', 'description' => 'Periksa tampilan desain, rincian spesifikasi, jumlah, serta penawaran sebelum menyetujui pesanan.'],
    ['icon' => 'receipt_long', 'title' => 'Konfirmasi dan pembayaran', 'description' => 'Berikan persetujuan akhir lalu lakukan pembayaran sesuai quotation yang telah disepakati.'],
    ['icon' => 'local_shipping', 'title' => 'Produksi, quality control, dan pengiriman', 'description' => 'Pesanan diproduksi, diperiksa, dikemas, lalu dikirim ke alamat yang telah dikonfirmasi.'],
  ];

  $preparationItems = [
    'Jumlah pesanan',
    'Ukuran dan bahan',
    'Logo atau referensi desain',
    'Target waktu',
    'Alamat pengiriman',
  ];

  $orderFaqs = config('seo.faq', []);
@endphp

<div class="info-page ordering-page">
  <section class="info-hero ordering-hero" id="ordering-hero" aria-labelledby="ordering-title">
    <div class="info-shell info-hero__grid">
      <div class="info-hero__content">
        <p class="info-eyebrow info-eyebrow--badge">Cara Pemesanan Lanyard Custom</p>
        <h1 id="ordering-title">Pesan Lanyard Custom dengan Alur yang Jelas</h1>
        <p class="info-hero__lead">Mulai dari konsultasi dan persetujuan desain hingga produksi, quality control, serta pengiriman dalam enam tahap yang mudah dipahami.</p>
        <div class="info-actions">
          <a class="info-button info-button--primary" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
            <img src="/images/whatsapp.svg" alt="" width="19" height="19" aria-hidden="true">
            <span>Mulai Konsultasi</span>
          </a>
          <a class="info-button info-button--secondary" href="{{ route('products.index') }}">
            <span>Lihat Produk</span>
            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
          </a>
        </div>
      </div>

      <div class="info-hero__visual info-hero__visual--landscape ordering-hero__visual">
        <div class="info-hero__image-frame">
          <img
            src="/uploads/hero-cara-pemesanan-lanyard-bekasi.webp"
            alt="Contoh penggunaan lanyard dan ID card Lanyard Bekasi di lingkungan kantor"
            width="1448"
            height="1086"
            loading="eager"
            fetchpriority="high"
            decoding="async"
          >
        </div>
      </div>
    </div>
  </section>

  <section class="info-trust ordering-trust" id="ordering-trust" aria-label="Kepastian proses pemesanan Lanyard Bekasi">
    <div class="info-shell">
      <ul class="info-trust__list">
        @foreach($trustItems as $item)
          <li>
            <span class="info-icon material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <span><strong>{{ $item['title'] }}</strong><small>{{ $item['description'] }}</small></span>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="ordering-process" id="ordering-process" aria-labelledby="ordering-process-title">
    <div class="info-shell">
      <header class="info-heading">
        <p class="info-eyebrow">Alur Pemesanan</p>
        <h2 id="ordering-process-title">Enam Tahap dari Konsultasi sampai Pesanan Dikirim</h2>
        <p>Setiap spesifikasi, desain, dan quotation diperiksa terlebih dahulu agar produksi berjalan sesuai persetujuan.</p>
      </header>

      <div class="ordering-process__layout">
        <ol class="ordering-process__list" aria-label="Enam tahap pemesanan lanyard custom">
          @foreach($orderSteps as $index => $step)
            <li id="langkah-{{ $index + 1 }}">
              <span class="ordering-process__icon material-symbols-outlined" aria-hidden="true">{{ $step['icon'] }}</span>
              <div>
                <h3>{{ $step['title'] }}</h3>
                <p>{{ $step['description'] }}</p>
              </div>
            </li>
          @endforeach
        </ol>

        <aside class="ordering-preparation" aria-labelledby="ordering-preparation-title">
          <p class="info-eyebrow">Sebelum Menghubungi Kami</p>
          <h3 id="ordering-preparation-title">Siapkan Detail Pesanan</h3>
          <p>Informasi ini membantu tim memberikan rekomendasi dan quotation yang lebih sesuai sejak awal.</p>
          <ul>
            @foreach($preparationItems as $item)
              <li>
                <span class="material-symbols-outlined" aria-hidden="true">check</span>
                <span>{{ $item }}</span>
              </li>
            @endforeach
          </ul>
          <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
            <img src="/images/whatsapp.svg" alt="" width="19" height="19" aria-hidden="true">
            <span>Mulai Konsultasi</span>
          </a>
        </aside>
      </div>
    </div>
  </section>

  <section class="info-faq ordering-faq" id="ordering-faq" aria-labelledby="ordering-faq-title">
    <div class="info-shell">
      <header class="info-heading info-heading--center">
        <p class="info-eyebrow">Pertanyaan Pemesanan</p>
        <h2 id="ordering-faq-title">Hal yang Sering Ditanyakan Sebelum Produksi</h2>
      </header>
      <div class="info-faq__list">
        @foreach($orderFaqs as $faq)
          <details class="info-faq__item">
            <summary>
              <span>{{ $faq['question'] }}</span>
              <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
            </summary>
            <div><p>{{ $faq['answer'] }}</p></div>
          </details>
        @endforeach
      </div>
    </div>
  </section>

  <section class="info-cta ordering-cta" id="ordering-cta" aria-labelledby="ordering-cta-title">
    <div class="info-shell">
      <div class="info-cta__panel">
        <div>
          <h2 id="ordering-cta-title">Siap Memulai Pesanan Lanyard Custom?</h2>
          <p>Ceritakan jumlah, penggunaan, dan target waktu Anda. Tim kami akan membantu menyusun spesifikasi yang sesuai.</p>
        </div>
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
          <img src="/images/whatsapp.svg" alt="" width="20" height="20" aria-hidden="true">
          <span>Chat WhatsApp Sekarang</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      </div>
    </div>
  </section>
</div>

@include('components.footer')

@push('schema')
  @include('components.schema.ordering', ['orderSteps' => $orderSteps, 'orderFaqs' => $orderFaqs])
@endpush
@endsection
