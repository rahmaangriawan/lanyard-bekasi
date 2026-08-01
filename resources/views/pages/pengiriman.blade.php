@extends('layouts.public')

@section('title', 'Pengiriman Lanyard Custom Aman ke Seluruh Indonesia | Lanyard Bekasi')
@section('meta_description', 'Informasi pengiriman Lanyard Bekasi: gratis ongkir Jabodetabek, packing aman, nomor resi, estimasi nasional, dan bantuan klaim asuransi.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/pengiriman')
@section('seo_og_image', '/uploads/lanyard-bekasi-cta.webp')

@push('head')
  <link rel="stylesheet" href="/css/shipping-index.css?v={{ file_exists(public_path('css/shipping-index.css')) ? filemtime(public_path('css/shipping-index.css')) : 1 }}">
@endpush

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya mau konsultasi pengiriman lanyard custom.');

  $shippingTrust = [
    ['icon' => 'local_shipping', 'title' => 'Gratis Ongkir', 'description' => 'Area Jabodetabek'],
    ['icon' => 'inventory_2', 'title' => 'Packing Aman', 'description' => 'Diperiksa sebelum dikirim'],
    ['icon' => 'track_changes', 'title' => 'Nomor Resi', 'description' => 'Status mudah dipantau'],
    ['icon' => 'verified_user', 'title' => 'Bantuan Klaim', 'description' => 'Pendampingan bila dibutuhkan'],
  ];

  $shippingServices = [
    [
      'icon' => 'local_shipping',
      'title' => 'Jabodetabek',
      'description' => 'Gratis ongkir untuk pengiriman reguler ke Jakarta, Bogor, Depok, Tangerang, dan Bekasi sesuai ketentuan promo aktif.',
      'points' => ['Estimasi 1 sampai 2 hari kerja', 'Opsi prioritas berdasarkan konfirmasi tim', 'Cocok untuk kebutuhan kantor dan event'],
    ],
    [
      'icon' => 'flight_takeoff',
      'title' => 'Pengiriman Nasional',
      'description' => 'Pesanan dikirim ke seluruh Indonesia melalui ekspedisi terpercaya yang disesuaikan dengan tujuan dan kebutuhan Anda.',
      'points' => ['Estimasi umum 2 sampai 7 hari kerja', 'Pilihan ekspedisi menyesuaikan tujuan', 'Biaya dihitung dari berat dan dimensi paket'],
    ],
  ];

  $shippingSteps = [
    ['icon' => 'chat', 'title' => 'Konfirmasi alamat', 'description' => 'Tim memastikan nama penerima, alamat, nomor kontak, dan target waktu pengiriman.'],
    ['icon' => 'verified', 'title' => 'Quality control', 'description' => 'Jumlah, hasil cetak, aksesoris, dan kondisi produk diperiksa sebelum dikemas.'],
    ['icon' => 'inventory_2', 'title' => 'Packing aman', 'description' => 'Pesanan dilindungi dengan kemasan yang disesuaikan dengan jumlah dan jenis produk.'],
    ['icon' => 'local_shipping', 'title' => 'Ekspedisi dan resi', 'description' => 'Paket diserahkan ke ekspedisi dan nomor resi dibagikan agar statusnya dapat dipantau.'],
  ];

  $shippingEstimates = [
    ['area' => 'Jabodetabek', 'estimate' => '1 sampai 2 hari kerja', 'cost' => 'Gratis ongkir reguler'],
    ['area' => 'Pulau Jawa di luar Jabodetabek', 'estimate' => '2 sampai 4 hari kerja', 'cost' => 'Menyesuaikan tujuan dan berat'],
    ['area' => 'Luar Pulau Jawa', 'estimate' => '4 sampai 7 hari kerja', 'cost' => 'Menyesuaikan tujuan dan berat'],
    ['area' => 'Daerah terpencil dan 3T', 'estimate' => '7 sampai 14 hari kerja', 'cost' => 'Dikonfirmasi oleh tim'],
  ];

  $shippingFaqs = config('seo.shipping_faq', []);
@endphp

<div class="shipping-page">
  <section class="shipping-hero" id="shipping-hero" aria-labelledby="shipping-title">
    <div class="shipping-shell shipping-hero__grid">
      <div class="shipping-hero__content">
        <p class="shipping-eyebrow">Pengiriman Lanyard Custom</p>
        <h1 id="shipping-title">Pengiriman Aman dan Terpantau ke Seluruh Indonesia</h1>
        <p class="shipping-hero__lead">Pesanan diperiksa, dikemas dengan aman, lalu dikirim melalui ekspedisi yang sesuai dengan lokasi dan target waktu Anda.</p>
        <div class="shipping-hero__actions">
          <a class="shipping-button shipping-button--primary" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" data-button-motion>
            <img src="/images/whatsapp.svg" alt="" width="19" height="19" aria-hidden="true">
            <span data-button-label>Konsultasi Pengiriman</span>
          </a>
          <a class="shipping-button shipping-button--secondary" href="{{ route('area') }}" data-button-motion>
            <span data-button-label>Lihat Area Layanan</span>
            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
          </a>
        </div>
      </div>

      <div class="shipping-hero__visual">
        <div class="shipping-hero__image-frame">
          <img
            src="/uploads/lanyard-bekasi-cta.webp"
            alt="Contoh lanyard custom Lanyard Bekasi yang siap dikirim"
            width="941"
            height="1672"
            decoding="async"
            fetchpriority="high"
          >
        </div>
      </div>
    </div>
  </section>

  <section class="shipping-trust" aria-label="Kepastian layanan pengiriman">
    <div class="shipping-shell">
      <ul class="shipping-trust__list">
        @foreach($shippingTrust as $item)
          <li>
            <span class="shipping-trust__icon material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <span>
              <strong>{{ $item['title'] }}</strong>
              <small>{{ $item['description'] }}</small>
            </span>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="shipping-services" id="layanan-pengiriman" aria-labelledby="shipping-services-title">
    <div class="shipping-shell">
      <header class="shipping-heading">
        <p class="shipping-eyebrow">Pilihan Pengiriman</p>
        <h2 id="shipping-services-title">Layanan yang Menyesuaikan Tujuan Anda</h2>
        <p>Pilih pengiriman area Jabodetabek atau layanan nasional. Tim kami membantu menentukan opsi yang paling sesuai.</p>
      </header>

      <div class="shipping-services__grid">
        @foreach($shippingServices as $service)
          <article class="shipping-service-card">
            <span class="shipping-service-card__icon material-symbols-outlined" aria-hidden="true">{{ $service['icon'] }}</span>
            <h3>{{ $service['title'] }}</h3>
            <p>{{ $service['description'] }}</p>
            <ul>
              @foreach($service['points'] as $point)
                <li>
                  <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
                  <span>{{ $point }}</span>
                </li>
              @endforeach
            </ul>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="shipping-process" id="alur-pengiriman" aria-labelledby="shipping-process-title">
    <div class="shipping-shell">
      <header class="shipping-heading shipping-heading--center">
        <h2 id="shipping-process-title">Pesanan Ditangani dalam 4 Tahap</h2>
        <p>Setiap paket melewati pengecekan yang jelas sebelum diserahkan kepada ekspedisi.</p>
      </header>

      <ol class="shipping-process__list">
        @foreach($shippingSteps as $step)
          <li>
            <span class="shipping-process__icon material-symbols-outlined" aria-hidden="true">{{ $step['icon'] }}</span>
            <h3>{{ $step['title'] }}</h3>
            <p>{{ $step['description'] }}</p>
          </li>
        @endforeach
      </ol>
    </div>
  </section>

  <section class="shipping-estimates" id="estimasi-pengiriman" aria-labelledby="shipping-estimates-title">
    <div class="shipping-shell shipping-estimates__layout">
      <div class="shipping-estimates__intro">
        <h2 id="shipping-estimates-title">Estimasi Waktu Pengiriman</h2>
        <p>Estimasi dihitung setelah produksi selesai dan paket diterima ekspedisi. Hari libur serta kondisi operasional kurir dapat memengaruhi waktu tiba.</p>
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
          Cek estimasi alamat Anda
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      </div>

      <div class="shipping-estimates__table-wrap">
        <table>
          <caption class="sr-only">Estimasi waktu dan biaya pengiriman Lanyard Bekasi</caption>
          <thead>
            <tr>
              <th scope="col">Wilayah</th>
              <th scope="col">Estimasi</th>
              <th scope="col">Biaya</th>
            </tr>
          </thead>
          <tbody>
            @foreach($shippingEstimates as $estimate)
              <tr>
                <th scope="row" data-label="Wilayah">{{ $estimate['area'] }}</th>
                <td data-label="Estimasi">{{ $estimate['estimate'] }}</td>
                <td data-label="Biaya">{{ $estimate['cost'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <p class="shipping-estimates__note">
          Opsi prioritas dan Same Day bergantung pada kesiapan produksi, lokasi tujuan, serta konfirmasi tim.
        </p>
      </div>
    </div>
  </section>

  <section class="shipping-faq" id="faq-pengiriman" aria-labelledby="shipping-faq-title">
    <div class="shipping-shell">
      <header class="shipping-heading shipping-heading--center">
        <p class="shipping-eyebrow">Pertanyaan Pengiriman</p>
        <h2 id="shipping-faq-title">Hal yang Sering Ditanyakan</h2>
      </header>

      <div class="shipping-faq__list">
        @foreach($shippingFaqs as $faq)
          <details class="shipping-faq__item">
            <summary>
              <span>{{ $faq['question'] }}</span>
              <span class="shipping-faq__icon material-symbols-outlined" aria-hidden="true">expand_more</span>
            </summary>
            <div class="shipping-faq__answer">
              <p>{{ $faq['answer'] }}</p>
            </div>
          </details>
        @endforeach
      </div>
    </div>
  </section>

  <section class="shipping-cta" aria-labelledby="shipping-cta-title">
    <div class="shipping-shell">
      <div class="shipping-cta__panel">
        <div>
          <h2 id="shipping-cta-title">Butuh Estimasi untuk Alamat Anda?</h2>
          <p>Kirim lokasi tujuan dan jumlah pesanan. Tim kami akan membantu memilih opsi pengiriman yang paling sesuai.</p>
        </div>
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" data-button-motion>
          <img src="/images/whatsapp.svg" alt="" width="20" height="20" aria-hidden="true">
          <span data-button-label>Chat WhatsApp Sekarang</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      </div>
    </div>
  </section>
</div>

@include('components.footer')

@push('schema')
  @include('components.schema.shipping', ['shippingFaqs' => $shippingFaqs])
@endpush
@endsection
