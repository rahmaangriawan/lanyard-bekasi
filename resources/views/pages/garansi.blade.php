@extends('layouts.public')

@section('title', 'Garansi Kualitas Lanyard Custom | Lanyard Bekasi')
@section('meta_description', 'Informasi garansi kualitas Lanyard Bekasi, cakupan cacat produksi, batas klaim 3x24 jam, bukti foto atau video, validasi, dan bantuan tim.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/garansi')
@section('seo_og_image', '/uploads/hero-garansi-lanyard-bekasi.webp')

@push('head')
  <link rel="stylesheet" href="/css/service-pages.css?v={{ file_exists(public_path('css/service-pages.css')) ? filemtime(public_path('css/service-pages.css')) : 1 }}">
@endpush

@section('content')
@php
  $claimWhatsappUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya ingin mengajukan klaim garansi untuk pesanan lanyard.');

  $warrantyTrust = [
    ['icon' => 'verified', 'title' => 'QC Sebelum Kirim', 'description' => 'Produk diperiksa lebih dahulu'],
    ['icon' => 'schedule', 'title' => 'Maksimal 3x24 Jam', 'description' => 'Sejak paket diterima'],
    ['icon' => 'photo_library', 'title' => 'Bukti Foto atau Video', 'description' => 'Termasuk dokumentasi unboxing'],
    ['icon' => 'policy', 'title' => 'Validasi oleh Tim', 'description' => 'Ditinjau berdasarkan bukti'],
  ];

  $coveredItems = [
    'Hasil cetak pudar ekstrem yang terbukti berasal dari proses produksi.',
    'Tali putus atau kerusakan material major akibat kesalahan produksi.',
    'Stopper atau aksesori rusak yang terbukti lolos dari pemeriksaan QC.',
  ];

  $excludedItems = [
    'Kesalahan isi atau desain pada mockup yang sudah disetujui.',
    'Perbedaan warna dalam toleransi produksi 5 sampai 10 persen.',
    'Pergeseran jahitan atau pemotongan dalam toleransi 1 sampai 2 mm.',
    'Keterlambatan, kehilangan, atau kerusakan akibat pihak kurir.',
    'Klaim yang diajukan melewati batas maksimal 3x24 jam.',
  ];

  $qualityChecks = [
    ['icon' => 'palette', 'title' => 'Hasil Cetak', 'description' => 'Ketajaman, keterbacaan, dan kondisi visual diperiksa sebelum packing.'],
    ['icon' => 'format_list_bulleted', 'title' => 'Jumlah Pesanan', 'description' => 'Jumlah produk disesuaikan dengan rincian pesanan yang diproses.'],
    ['icon' => 'inventory', 'title' => 'Aksesori', 'description' => 'Hook, stopper, dan holder diperiksa pemasangan serta kondisinya.'],
    ['icon' => 'inventory_2', 'title' => 'Kondisi Produk', 'description' => 'Produk diperiksa kembali sebelum dilindungi dengan kemasan.'],
  ];

  $claimSteps = [
    ['icon' => 'chat', 'title' => 'Hubungi tim', 'description' => 'Sampaikan kendala maksimal 3x24 jam sejak paket tercatat diterima.'],
    ['icon' => 'receipt_long', 'title' => 'Kirim detail pesanan', 'description' => 'Sertakan nomor atau identitas pesanan agar data dapat dicocokkan.'],
    ['icon' => 'image', 'title' => 'Lampirkan bukti', 'description' => 'Kirim foto dan video unboxing yang memperlihatkan kondisi produk dengan jelas.'],
    ['icon' => 'policy', 'title' => 'Proses validasi', 'description' => 'Tim meninjau bukti dan memberikan penyelesaian sesuai hasil pemeriksaan.'],
  ];

  $warrantyFaqs = config('seo.warranty_faq', []);
@endphp

<div class="info-page warranty-page">
  <section class="info-hero warranty-hero" id="warranty-hero" aria-labelledby="warranty-title">
    <div class="info-shell info-hero__grid">
      <div class="info-hero__content">
        <p class="info-eyebrow info-eyebrow--badge">Garansi Kualitas</p>
        <h1 id="warranty-title">Garansi yang Jelas untuk Hasil Produksi Anda</h1>
        <p class="info-hero__lead">Cakupan, batas waktu, bukti, dan proses validasi dijelaskan sejak awal agar pengajuan klaim mudah dipahami.</p>
        <div class="info-actions">
          <a class="info-button info-button--primary" href="{{ $claimWhatsappUrl }}" target="_blank" rel="noopener noreferrer">
            <img src="/images/whatsapp.svg" alt="" width="19" height="19" aria-hidden="true">
            <span>Ajukan Klaim Garansi</span>
          </a>
          <a class="info-button info-button--secondary" href="{{ route('terms') }}">
            <span>Baca Syarat Lengkap</span>
            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
          </a>
        </div>
      </div>

      <div class="info-hero__visual info-hero__visual--landscape">
        <div class="info-hero__image-frame">
          <img
            src="/uploads/hero-garansi-lanyard-bekasi.webp"
            alt="Contoh hasil produksi lanyard yang melalui quality control"
            width="1448"
            height="1086"
            decoding="async"
            fetchpriority="high"
          >
        </div>
      </div>
    </div>
  </section>

  <section class="info-trust" aria-label="Ketentuan utama garansi">
    <div class="info-shell">
      <ul class="info-trust__list">
        @foreach($warrantyTrust as $item)
          <li>
            <span class="info-icon material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <span><strong>{{ $item['title'] }}</strong><small>{{ $item['description'] }}</small></span>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="warranty-coverage" id="cakupan-garansi" aria-labelledby="coverage-title">
    <div class="info-shell">
      <header class="info-heading">
        <p class="info-eyebrow">Cakupan Garansi</p>
        <h2 id="coverage-title">Bedakan Cacat Produksi dan Kondisi di Luar Cakupan</h2>
        <p>Penilaian dilakukan berdasarkan spesifikasi yang disetujui, toleransi produksi, dokumentasi, dan hasil validasi tim.</p>
      </header>
      <div class="warranty-coverage__grid">
        <article class="warranty-coverage__panel warranty-coverage__panel--covered">
          <span class="material-symbols-outlined" aria-hidden="true">verified_user</span>
          <h3>Termasuk Garansi Produksi</h3>
          <ul>
            @foreach($coveredItems as $item)
              <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>{{ $item }}</span></li>
            @endforeach
          </ul>
        </article>
        <article class="warranty-coverage__panel warranty-coverage__panel--excluded">
          <span class="material-symbols-outlined" aria-hidden="true">info</span>
          <h3>Tidak Termasuk Garansi Produksi</h3>
          <ul>
            @foreach($excludedItems as $item)
              <li><span class="material-symbols-outlined" aria-hidden="true">label_off</span><span>{{ $item }}</span></li>
            @endforeach
          </ul>
        </article>
      </div>
    </div>
  </section>

  <section class="warranty-quality" aria-labelledby="quality-title">
    <div class="info-shell">
      <header class="info-heading info-heading--center">
        <h2 id="quality-title">Pemeriksaan Dilakukan Sebelum Pesanan Dikemas</h2>
        <p>Quality control membantu menemukan masalah produksi sebelum produk diserahkan kepada ekspedisi.</p>
      </header>
      <ul class="info-feature-grid">
        @foreach($qualityChecks as $item)
          <li>
            <span class="info-feature-grid__icon material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <h3>{{ $item['title'] }}</h3>
            <p>{{ $item['description'] }}</p>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="info-process warranty-claim" id="alur-klaim" aria-labelledby="claim-title">
    <div class="info-shell">
      <header class="info-heading info-heading--center">
        <p class="info-eyebrow">Alur Klaim</p>
        <h2 id="claim-title">Siapkan Bukti agar Validasi Lebih Terarah</h2>
        <p>Pengajuan tidak memakai angka dekoratif. Setiap kartu menunjukkan informasi yang perlu dilakukan.</p>
      </header>
      <ol class="info-process__list">
        @foreach($claimSteps as $step)
          <li>
            <span class="material-symbols-outlined" aria-hidden="true">{{ $step['icon'] }}</span>
            <h3>{{ $step['title'] }}</h3>
            <p>{{ $step['description'] }}</p>
          </li>
        @endforeach
      </ol>
      <div class="warranty-claim__notice">
        <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
        <p><strong>Jangan menunda dokumentasi.</strong> Foto dan video unboxing perlu menunjukkan kondisi paket serta produk dengan jelas dalam batas maksimal 3x24 jam.</p>
      </div>
    </div>
  </section>

  <section class="info-faq" id="faq-garansi" aria-labelledby="warranty-faq-title">
    <div class="info-shell">
      <header class="info-heading info-heading--center">
        <p class="info-eyebrow">Pertanyaan Garansi</p>
        <h2 id="warranty-faq-title">Hal yang Perlu Diketahui Sebelum Mengajukan Klaim</h2>
      </header>
      <div class="info-faq__list">
        @foreach($warrantyFaqs as $faq)
          <details class="info-faq__item">
            <summary><span>{{ $faq['question'] }}</span><span class="material-symbols-outlined" aria-hidden="true">expand_more</span></summary>
            <div><p>{{ $faq['answer'] }}</p></div>
          </details>
        @endforeach
      </div>
    </div>
  </section>

  <section class="info-cta" aria-labelledby="warranty-cta-title">
    <div class="info-shell">
      <div class="info-cta__panel">
        <div>
          <h2 id="warranty-cta-title">Menemukan Kendala pada Pesanan Anda?</h2>
          <p>Hubungi tim maksimal 3x24 jam dan siapkan detail pesanan beserta dokumentasi unboxing.</p>
        </div>
        <a href="{{ $claimWhatsappUrl }}" target="_blank" rel="noopener noreferrer">
          <img src="/images/whatsapp.svg" alt="" width="20" height="20" aria-hidden="true">
          <span>Ajukan Klaim via WhatsApp</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      </div>
    </div>
  </section>
</div>

@include('components.footer')

@push('schema')
  @include('components.schema.warranty', ['warrantyFaqs' => $warrantyFaqs])
@endpush
@endsection
