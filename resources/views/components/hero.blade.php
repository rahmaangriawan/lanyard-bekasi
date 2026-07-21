@php
  $heroQuotationUrl = 'https://wa.me/6282210200700?text=Halo%20Pusat%20Tali%20Lanyard%2C%20saya%20mau%20minta%20quotation%20lanyard%20custom.';
@endphp

<section id="hero" class="ptl-hero" aria-labelledby="hero-title">
  <img
    class="ptl-hero__media"
    src="/uploads/hero-pusat-tali-lanyard-office.webp"
    alt=""
    width="1024"
    height="572"
    loading="eager"
    fetchpriority="high"
    decoding="async"
    aria-hidden="true"
  />

  <div class="ptl-hero__overlay" aria-hidden="true"></div>

  <div class="ptl-hero__container">
    <div class="ptl-hero__content">
      <h1 id="hero-title" class="ptl-hero__title">
        Kirim Logo, Terima Lanyard Siap Pakai
      </h1>

      <p class="ptl-hero__description">
        Satu-satunya tempat yang menyediakan cetak tali lanyard dan ID Card terlengkap.
        Kami siap memberikan pelayanan dan kualitas terbaik untuk kebutuhan Anda.
      </p>

      <div class="ptl-hero__actions">
        <a
          href="{{ $heroQuotationUrl }}"
          target="_blank"
          rel="noopener noreferrer"
          class="ptl-hero__cta"
        >
          <span>Minta Quotation</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      </div>

      <p class="ptl-hero__trust">
        <span class="material-symbols-outlined" aria-hidden="true">verified</span>
        <span>Tanpa minimum order dan bebas ongkir ke seluruh Indonesia.</span>
      </p>
    </div>
  </div>
</section>
