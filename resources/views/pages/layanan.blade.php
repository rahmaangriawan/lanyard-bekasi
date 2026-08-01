@extends('layouts.public')

@section('title', 'Layanan Cetak Lanyard Custom | Lanyard Bekasi')
@section('meta_description', 'Layanan cetak lanyard custom dengan konsultasi spesifikasi, bantuan desain, pilihan bahan dan aksesori, produksi, quality control, serta pengiriman.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/layanan')
@section('seo_og_image', '/uploads/hero-layanan-lanyard-bekasi.webp')

@push('head')
  <link rel="stylesheet" href="/css/service-pages.css?v={{ file_exists(public_path('css/service-pages.css')) ? filemtime(public_path('css/service-pages.css')) : 1 }}">
@endpush

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya mau konsultasi layanan lanyard custom.');

  $trustItems = [
    ['icon' => 'design_services', 'title' => 'Bantuan Desain', 'description' => 'Mockup sebelum produksi'],
    ['icon' => 'sell', 'title' => 'Pilihan Spesifikasi', 'description' => 'Sesuai fungsi dan anggaran'],
    ['icon' => 'verified', 'title' => 'Quality Control', 'description' => 'Diperiksa sebelum dikemas'],
    ['icon' => 'local_shipping', 'title' => 'Pengiriman Luas', 'description' => 'Jabodetabek dan nasional'],
  ];

  $capabilities = [
    ['icon' => 'forum', 'title' => 'Konsultasi dan Quotation', 'description' => 'Tim membantu merangkum jumlah, target penggunaan, bahan, aksesori, serta kebutuhan waktu sebelum menyiapkan penawaran.'],
    ['icon' => 'design_services', 'title' => 'Desain dan Mockup Digital', 'description' => 'Logo, warna brand, dan susunan informasi disiapkan menjadi mockup untuk diperiksa sebelum produksi dimulai.'],
    ['icon' => 'palette', 'title' => 'Bahan dan Aksesori', 'description' => 'Pilihan bahan, lebar tali, hook, stopper, dan holder disesuaikan dengan cara penggunaan dan anggaran.'],
    ['icon' => 'precision_manufacturing', 'title' => 'Produksi dan Quality Control', 'description' => 'Hasil cetak, jumlah, aksesori, serta kondisi produk diperiksa sebelum pesanan dikemas dan dikirim.'],
  ];

  $audiences = [
    ['icon' => 'business', 'title' => 'Perusahaan dan Kantor', 'description' => 'Lanyard untuk identitas karyawan, kartu akses, kegiatan internal, dan kebutuhan onboarding.'],
    ['icon' => 'school', 'title' => 'Sekolah dan Kampus', 'description' => 'Kebutuhan kartu pelajar, panitia, seminar, orientasi, dan kegiatan organisasi.'],
    ['icon' => 'send', 'title' => 'Event dan Komunitas', 'description' => 'Lanyard peserta, panitia, merchandise, promosi, dan identitas acara.'],
  ];

  $customizationItems = [
    ['icon' => 'palette', 'title' => 'Logo dan Warna', 'description' => 'Sesuaikan visual dengan identitas perusahaan, sekolah, atau acara.'],
    ['icon' => 'high_quality', 'title' => 'Lebar dan Bahan', 'description' => 'Pilih spesifikasi tali berdasarkan kenyamanan, tampilan, dan anggaran.'],
    ['icon' => 'inventory', 'title' => 'Hook dan Stopper', 'description' => 'Kombinasikan pengait dan stopper sesuai kebutuhan pemakaian.'],
    ['icon' => 'inventory_2', 'title' => 'ID Card Holder', 'description' => 'Lengkapi lanyard dengan holder yang sesuai dengan ukuran kartu.'],
  ];

  $serviceSteps = [
    ['icon' => 'chat', 'title' => 'Ceritakan kebutuhan', 'description' => 'Sampaikan jumlah, penggunaan, target waktu, dan anggaran.'],
    ['icon' => 'image', 'title' => 'Periksa mockup', 'description' => 'Tinjau desain dan quotation sebelum memberikan persetujuan.'],
    ['icon' => 'precision_manufacturing', 'title' => 'Produksi dan QC', 'description' => 'Pesanan diproduksi lalu diperiksa berdasarkan spesifikasi.'],
    ['icon' => 'inventory_2', 'title' => 'Packing dan kirim', 'description' => 'Produk dikemas dan dikirim ke alamat yang sudah dikonfirmasi.'],
  ];

  $serviceFaqs = config('seo.faq', []);
@endphp

<div class="info-page services-page">
  <section class="info-hero" id="services-hero" aria-labelledby="services-title">
    <div class="info-shell info-hero__grid">
      <div class="info-hero__content">
        <p class="info-eyebrow info-eyebrow--badge">Layanan Lanyard Custom</p>
        <h1 id="services-title">Layanan Cetak Lanyard Custom untuk Berbagai Kebutuhan</h1>
        <p class="info-hero__lead">Mulai dari konsultasi dan desain hingga produksi, quality control, dan pengiriman dalam satu alur yang jelas.</p>
        <div class="info-actions">
          <a class="info-button info-button--primary" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
            <img src="/images/whatsapp.svg" alt="" width="19" height="19" aria-hidden="true">
            <span>Konsultasi Layanan</span>
          </a>
          <a class="info-button info-button--secondary" href="{{ route('products.index') }}">
            <span>Lihat Produk</span>
            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
          </a>
        </div>
      </div>

      <div class="info-hero__visual info-hero__visual--landscape">
        <div class="info-hero__image-frame">
          <img
            src="/uploads/hero-layanan-lanyard-bekasi.webp"
            alt="Contoh hasil layanan cetak lanyard custom Lanyard Bekasi"
            width="1448"
            height="1086"
            decoding="async"
            fetchpriority="high"
          >
        </div>
      </div>
    </div>
  </section>

  <section class="info-trust" aria-label="Kepastian layanan Lanyard Bekasi">
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

  <section class="info-capabilities" id="kemampuan-layanan" aria-labelledby="capabilities-title">
    <div class="info-shell">
      <header class="info-heading">
        <p class="info-eyebrow">Yang Kami Kerjakan</p>
        <h2 id="capabilities-title">Pendampingan dari Spesifikasi sampai Pesanan Siap Dikirim</h2>
        <p>Setiap tahap dibuat mudah dipahami agar keputusan bahan, desain, dan jumlah pesanan tetap terkontrol.</p>
      </header>
      <ul class="info-feature-grid">
        @foreach($capabilities as $item)
          <li>
            <span class="info-feature-grid__icon material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <h3>{{ $item['title'] }}</h3>
            <p>{{ $item['description'] }}</p>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="info-audiences" aria-labelledby="audiences-title">
    <div class="info-shell">
      <header class="info-heading info-heading--center">
        <h2 id="audiences-title">Layanan yang Menyesuaikan Cara Anda Menggunakan Lanyard</h2>
        <p>Rekomendasi produk dan aksesori ditentukan berdasarkan lingkungan penggunaan, jumlah, dan kebutuhan identitas.</p>
      </header>
      <ul class="info-audience-grid">
        @foreach($audiences as $item)
          <li>
            <span class="material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <h3>{{ $item['title'] }}</h3>
            <p>{{ $item['description'] }}</p>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="info-customization" aria-labelledby="customization-title">
    <div class="info-shell info-customization__layout">
      <div class="info-customization__intro">
        <p class="info-eyebrow">Bisa Disesuaikan</p>
        <h2 id="customization-title">Detail Produk Mengikuti Fungsi dan Identitas Anda</h2>
        <p>Tim membantu menyusun kombinasi yang proporsional tanpa membuat Anda harus memahami seluruh istilah produksi.</p>
        <a href="{{ route('products.index') }}">Pelajari pilihan produk <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
      </div>
      <ul class="info-customization__list">
        @foreach($customizationItems as $item)
          <li>
            <span class="material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <div><h3>{{ $item['title'] }}</h3><p>{{ $item['description'] }}</p></div>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="info-process" aria-labelledby="service-process-title">
    <div class="info-shell">
      <header class="info-heading info-heading--center">
        <h2 id="service-process-title">Alur Kerja yang Ringkas dan Transparan</h2>
        <p>Persetujuan desain dan quotation selalu dilakukan sebelum produksi dimulai.</p>
      </header>
      <ol class="info-process__list">
        @foreach($serviceSteps as $step)
          <li>
            <span class="material-symbols-outlined" aria-hidden="true">{{ $step['icon'] }}</span>
            <h3>{{ $step['title'] }}</h3>
            <p>{{ $step['description'] }}</p>
          </li>
        @endforeach
      </ol>
      <a class="info-process__link" href="{{ route('ordering') }}">Lihat cara pemesanan lengkap <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
    </div>
  </section>

  <section class="info-faq" id="faq-layanan" aria-labelledby="service-faq-title">
    <div class="info-shell">
      <header class="info-heading info-heading--center">
        <p class="info-eyebrow">Pertanyaan Layanan</p>
        <h2 id="service-faq-title">Hal yang Sering Ditanyakan</h2>
      </header>
      <div class="info-faq__list">
        @foreach($serviceFaqs as $faq)
          <details class="info-faq__item">
            <summary><span>{{ $faq['question'] }}</span><span class="material-symbols-outlined" aria-hidden="true">expand_more</span></summary>
            <div><p>{{ $faq['answer'] }}</p></div>
          </details>
        @endforeach
      </div>
    </div>
  </section>

  <section class="info-cta" aria-labelledby="service-cta-title">
    <div class="info-shell">
      <div class="info-cta__panel">
        <div>
          <h2 id="service-cta-title">Mulai dari Kebutuhan, Bukan dari Istilah Teknis</h2>
          <p>Ceritakan jumlah dan penggunaan lanyard. Tim kami akan membantu menyusun rekomendasi yang sesuai.</p>
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
  @include('components.schema.services', ['serviceFaqs' => $serviceFaqs])
@endpush
@endsection
