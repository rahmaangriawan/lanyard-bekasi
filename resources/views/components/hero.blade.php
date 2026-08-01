@php
  $heroQuotationUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya mau pesan lanyard custom.');
@endphp

<section id="hero" class="ptl-hero" aria-labelledby="hero-title">
  <div class="ptl-hero__container">
    <div class="ptl-hero__content">
      <p class="ptl-hero__eyebrow">Vendor Lanyard Bekasi Terpercaya</p>
      <h1 id="hero-title" class="ptl-hero__title">
        <span>Print Lanyard</span>
        <span>Custom <strong>Murah</strong></span>
      </h1>

      <p class="ptl-hero__lead">Kualitas premium, harga bersahabat, dan proses pemesanan yang mudah.</p>
      <p class="ptl-hero__description">
        Cetak lanyard custom untuk perusahaan, event, sekolah, dan komunitas. Konsultasi desain langsung dengan tim kami.
      </p>

      <div class="ptl-hero__actions">
        <a
          href="{{ $heroQuotationUrl }}"
          target="_blank"
          rel="noopener noreferrer"
          class="ptl-hero__cta ptl-hero__cta--primary"
        >
          <img src="/images/whatsapp.svg" alt="" width="20" height="20" loading="eager" decoding="async" aria-hidden="true">
          <span>Pesan via WhatsApp</span>
        </a>
        <a href="{{ route('products.index') }}" class="ptl-hero__cta ptl-hero__cta--secondary">
          <span>Lihat Produk</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      </div>
    </div>

    <div class="ptl-hero__visual" aria-label="Contoh hasil cetak lanyard custom">
      <div class="ptl-hero__shape" aria-hidden="true"></div>
      <figure class="ptl-hero__product ptl-hero__product--back">
        <img
          src="/uploads/hero-lanyard-bekasi-custom-02.webp"
          alt="Contoh lanyard custom biru dan putih Lanyard Bekasi"
          width="941"
          height="1672"
          loading="eager"
          decoding="async"
        >
      </figure>
      <figure class="ptl-hero__product ptl-hero__product--front">
        <img
          src="/uploads/hero-lanyard-bekasi-custom-01.webp"
          alt="Contoh lanyard custom hijau dan hitam Lanyard Bekasi"
          width="940"
          height="1672"
          loading="eager"
          fetchpriority="high"
          decoding="async"
        >
      </figure>
      <div class="ptl-hero__price">
        <span>Harga mulai</span>
        <strong>Rp 3.000</strong>
        <small>/pcs</small>
      </div>
    </div>
  </div>
</section>
