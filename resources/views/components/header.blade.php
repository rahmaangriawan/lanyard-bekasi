@php
  $headerLinks = [
    ['label' => 'Layanan', 'href' => route('services'), 'patterns' => ['layanan']],
    ['label' => 'Pengiriman', 'href' => route('shipping'), 'patterns' => ['pengiriman']],
    ['label' => 'Garansi', 'href' => route('warranty'), 'patterns' => ['garansi']],
    ['label' => 'FAQ', 'href' => route('faq'), 'patterns' => ['faq']],
    ['label' => 'Artikel', 'href' => route('blog.index'), 'patterns' => ['blog*', 'kategori/*', 'tag/*']],
  ];

  $quotationUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya mau konsultasi lanyard custom.');
@endphp

<div class="site-header__sentinel" id="site-header-sentinel" aria-hidden="true"></div>

<header class="site-header" id="site-header">
  <div class="site-header__surface">
    <nav class="site-header__nav" aria-label="Navigasi utama">
      <a href="{{ route('home') }}" class="site-header__logo" aria-label="Lanyard Bekasi, kembali ke beranda">
        <img
          src="/uploads/lanyard-bekasi-logo-160w.webp"
          srcset="/uploads/lanyard-bekasi-logo-160w.webp 160w, /uploads/lanyard-bekasi-logo-256w.webp 256w, /uploads/lanyard-bekasi-logo.webp 500w"
          sizes="126px"
          alt="Lanyard Bekasi"
          width="500"
          height="122"
          loading="eager"
          fetchpriority="high"
          decoding="async"
        />
      </a>

      <div class="site-header__links">
        @foreach ($headerLinks as $item)
          @php($isActive = !empty($item['patterns']) && request()->is(...$item['patterns']))
          <a
            href="{{ $item['href'] }}"
            class="site-header__link"
            @if ($isActive) aria-current="page" @endif
          >{{ $item['label'] }}</a>
        @endforeach
      </div>

      <a
        href="{{ $quotationUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        class="site-header__btn site-header__btn--primary site-header__desktop-cta"
      >
        <img src="/images/whatsapp.svg" alt="" width="18" height="18" loading="eager" decoding="async" aria-hidden="true">
        <span>Pesan Sekarang</span>
      </a>

      <button
        type="button"
        class="site-header__hamburger"
        id="menu-toggle"
        aria-label="Buka menu navigasi"
        aria-controls="mobile-menu"
        aria-expanded="false"
      >
        <span class="hamburger-line hamburger-line--top"></span>
        <span class="hamburger-line hamburger-line--middle"></span>
        <span class="hamburger-line hamburger-line--bottom"></span>
      </button>
    </nav>
  </div>

  <button class="site-header__backdrop" id="menu-backdrop" type="button" aria-label="Tutup menu" tabindex="-1"></button>

  <div class="site-header__mobile" id="mobile-menu" aria-hidden="true">
    <div class="site-header__mobile-inner">
      <nav class="site-header__mobile-links" aria-label="Navigasi seluler">
        @foreach ($headerLinks as $item)
          @php($isActive = !empty($item['patterns']) && request()->is(...$item['patterns']))
          <a
            href="{{ $item['href'] }}"
            class="site-header__link site-header__link--mobile"
            @if ($isActive) aria-current="page" @endif
          >
            <span>{{ $item['label'] }}</span>
            <span class="site-header__link-arrow" aria-hidden="true">&#8594;</span>
          </a>
        @endforeach
      </nav>

      <div class="site-header__mobile-contact">
        <p>Butuh rekomendasi bahan atau ukuran?</p>
        <a
          href="{{ $quotationUrl }}"
          target="_blank"
          rel="noopener noreferrer"
          class="site-header__btn site-header__btn--primary site-header__btn--full"
        >
          <img src="/images/whatsapp.svg" alt="" width="18" height="18" loading="lazy" decoding="async" aria-hidden="true">
          <span>Pesan via WhatsApp</span>
        </a>
      </div>
    </div>
  </div>
</header>
