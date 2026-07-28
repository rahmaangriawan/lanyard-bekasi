@php
  $headerLinks = [
    ['label' => 'Beranda', 'href' => route('home'), 'patterns' => ['/']],
    ['label' => 'Produk', 'href' => route('products.index'), 'patterns' => ['produk', 'produk/*']],
    ['label' => 'Area', 'href' => route('area'), 'patterns' => ['area', 'area/*']],
    ['label' => 'Pemesanan', 'href' => route('ordering'), 'patterns' => ['cara-pemesanan']],
    ['label' => 'Blog', 'href' => route('blog.index'), 'patterns' => ['blog', 'blog/*', 'kategori/*', 'tag/*']],
  ];

  $quotationUrl = 'https://wa.me/6282210200700?text=Halo%20Pusat%20Tali%20Lanyard%2C%20saya%20mau%20minta%20quotation%20lanyard%20custom.';
@endphp

<div class="site-header__sentinel" id="site-header-sentinel" aria-hidden="true"></div>

<header class="site-header" id="site-header">
  <div class="site-header__surface">
    <nav class="site-header__nav" aria-label="Navigasi utama">
      <a href="{{ route('home') }}" class="site-header__logo" style="width: 118px; height: 44px; flex: 0 0 118px; min-width: 118px; display: inline-flex;" aria-label="Pusat Tali Lanyard, kembali ke beranda">
        <img
          src="/uploads/pusat-tali-lanyard-logo.webp"
          alt="Pusat Tali Lanyard"
          width="118"
          height="44"
          style="width: 118px; height: 44px; aspect-ratio: 118/44; object-fit: contain;"
          loading="eager"
          fetchpriority="high"
          decoding="async"
        />
      </a>

      <div class="site-header__links">
        @foreach ($headerLinks as $item)
          @php($isActive = request()->is(...$item['patterns']))
          <a
            href="{{ $item['href'] }}"
            class="site-header__link"
            @if ($isActive) aria-current="page" @endif
          >{{ $item['label'] }}</a>
        @endforeach
      </div>

      <div class="site-header__actions" style="width: 146px; height: 44px; flex: 0 0 146px; min-width: 146px; display: flex;">
        <a
          href="{{ $quotationUrl }}"
          target="_blank"
          rel="noopener noreferrer"
          class="site-header__btn site-header__btn--primary"
          style="width: 146px; height: 44px; min-height: 44px; padding: 0; display: inline-flex;"
        >Minta Quotation</a>
      </div>

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
          @php($isActive = request()->is(...$item['patterns']))
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
        >Minta Quotation</a>
      </div>
    </div>
  </div>
</header>
