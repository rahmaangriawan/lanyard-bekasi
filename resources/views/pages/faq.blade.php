@extends('layouts.public')

@section('title', 'FAQ Lanyard Custom | Pusat Bantuan Lanyard Bekasi')
@section('meta_description', 'Temukan jawaban seputar produk dan layanan lanyard custom, proses pengiriman, serta garansi produksi Lanyard Bekasi.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/faq')
@section('seo_og_image', '/uploads/hero-faq-lanyard-bekasi.webp')

@push('head')
  <link rel="stylesheet" href="/css/service-pages.css?v={{ file_exists(public_path('css/service-pages.css')) ? filemtime(public_path('css/service-pages.css')) : 1 }}">
  <link rel="stylesheet" href="/css/faq-index.css?v={{ file_exists(public_path('css/faq-index.css')) ? filemtime(public_path('css/faq-index.css')) : 1 }}">
@endpush

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya masih punya pertanyaan tentang lanyard custom.');

  $faqGroups = [
    [
      'id' => 'produk-layanan',
      'icon' => 'inventory_2',
      'label' => 'Produk & Layanan',
      'title' => 'Produk dan Layanan Lanyard',
      'description' => 'Informasi dasar sebelum memilih bahan, desain, aksesori, dan proses produksi.',
      'link' => route('services'),
      'link_label' => 'Pelajari Layanan',
      'items' => config('seo.faq', []),
    ],
    [
      'id' => 'pengiriman',
      'icon' => 'local_shipping',
      'label' => 'Pengiriman',
      'title' => 'Pengiriman dan Pelacakan',
      'description' => 'Ketahui ketentuan ongkir, estimasi, resi, dan bantuan saat ada kendala kurir.',
      'link' => route('shipping'),
      'link_label' => 'Lihat Informasi Pengiriman',
      'items' => config('seo.shipping_faq', []),
    ],
    [
      'id' => 'garansi',
      'icon' => 'verified_user',
      'label' => 'Garansi',
      'title' => 'Garansi dan Pengajuan Klaim',
      'description' => 'Pahami cakupan, batas waktu, bukti yang dibutuhkan, dan proses validasi klaim.',
      'link' => route('warranty'),
      'link_label' => 'Baca Kebijakan Garansi',
      'items' => config('seo.warranty_faq', []),
    ],
  ];

  $allFaqs = collect($faqGroups)->flatMap(fn ($group) => $group['items'])->values();
@endphp

<div class="info-page faq-page">
  <section class="info-hero faq-hero" id="faq-hero" aria-labelledby="faq-title">
    <div class="info-shell info-hero__grid">
      <div class="info-hero__content">
        <p class="info-eyebrow info-eyebrow--badge">Pusat Bantuan Lanyard Bekasi</p>
        <h1 id="faq-title">Jawaban untuk Pertanyaan Seputar Lanyard Custom</h1>
        <p class="info-hero__lead">Temukan jawaban tentang produk, proses pemesanan, pengiriman, dan garansi sebelum menghubungi tim kami.</p>
        <div class="info-actions">
          <a class="info-button info-button--primary" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
            <img src="/images/whatsapp.svg" alt="" width="19" height="19" aria-hidden="true">
            <span>Tanya via WhatsApp</span>
          </a>
          <a class="info-button info-button--secondary" href="{{ route('products.index') }}">
            <span>Lihat Produk</span>
            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
          </a>
        </div>
      </div>

      <div class="info-hero__visual info-hero__visual--landscape faq-hero__visual">
        <div class="info-hero__image-frame">
          <img
            src="/uploads/hero-faq-lanyard-bekasi.webp"
            alt="Lanyard custom Lanyard Bekasi"
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

  <nav class="faq-topics" id="topik-faq" aria-labelledby="faq-topics-title">
    <div class="info-shell">
      <header class="info-heading info-heading--center">
        <p class="info-eyebrow">Pilih Topik</p>
        <h2 id="faq-topics-title">Langsung ke Jawaban yang Anda Butuhkan</h2>
      </header>
      <ul class="faq-topics__list">
        @foreach($faqGroups as $group)
          <li>
            <a href="#{{ $group['id'] }}">
              <span class="faq-topics__icon material-symbols-outlined" aria-hidden="true">{{ $group['icon'] }}</span>
              <span>
                <strong>{{ $group['label'] }}</strong>
                <small>4 pertanyaan</small>
              </span>
              <span class="material-symbols-outlined faq-topics__arrow" aria-hidden="true">arrow_forward</span>
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </nav>

  @foreach($faqGroups as $groupIndex => $group)
    <section
      class="faq-category {{ $groupIndex % 2 === 1 ? 'faq-category--soft' : '' }}"
      id="{{ $group['id'] }}"
      aria-labelledby="{{ $group['id'] }}-title"
    >
      <div class="info-shell faq-category__layout">
        <header class="faq-category__intro">
          <span class="faq-category__icon material-symbols-outlined" aria-hidden="true">{{ $group['icon'] }}</span>
          <p class="faq-category__label">{{ $group['label'] }}</p>
          <h2 id="{{ $group['id'] }}-title">{{ $group['title'] }}</h2>
          <p>{{ $group['description'] }}</p>
          <a href="{{ $group['link'] }}">
            {{ $group['link_label'] }}
            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
          </a>
        </header>

        <div class="faq-category__list">
          @foreach($group['items'] as $faqIndex => $faq)
            @php
              $questionId = 'faq-'.$group['id'].'-question-'.$faqIndex;
              $answerId = 'faq-'.$group['id'].'-answer-'.$faqIndex;
            @endphp
            <details class="info-faq__item">
              <summary id="{{ $questionId }}" aria-controls="{{ $answerId }}">
                <span>{{ $faq['question'] }}</span>
                <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
              </summary>
              <div id="{{ $answerId }}" aria-labelledby="{{ $questionId }}">
                <p>{{ $faq['answer'] }}</p>
              </div>
            </details>
          @endforeach
        </div>
      </div>
    </section>
  @endforeach

  <section class="info-cta faq-cta" aria-labelledby="faq-cta-title">
    <div class="info-shell">
      <div class="info-cta__panel">
        <div>
          <h2 id="faq-cta-title">Masih Ada yang Ingin Ditanyakan?</h2>
          <p>Ceritakan kebutuhan atau kendala Anda. Tim kami akan membantu memberikan jawaban yang lebih spesifik.</p>
        </div>
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
          <img src="/images/whatsapp.svg" alt="" width="20" height="20" aria-hidden="true">
          <span>Tanya Tim Lanyard Bekasi</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      </div>
    </div>
  </section>
</div>

@include('components.footer')

@push('schema')
  @include('components.schema.faq', ['allFaqs' => $allFaqs])
@endpush
@endsection
