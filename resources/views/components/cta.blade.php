@php
  $ctaWhatsappUrl = 'https://wa.me/6282210200700?text=Halo%20Pusat%20Tali%20Lanyard%2C%20saya%20mau%20konsultasi%20harga%20lanyard%20custom.';
@endphp

<section class="home-cta" id="cta-section" aria-labelledby="home-cta-title">
  <div class="home-cta__container">
    <h2 id="home-cta-title">
      <span>Pesan Sekarang,</span>
      <strong>Harga Boleh Diadu!</strong>
    </h2>
    <p>Kami pastikan harga lanyard di tempat kami paling kompetitif di seluruh Indonesia dengan garansi kualitas terbaik.</p>
    <a
      class="home-cta__button"
      href="{{ $ctaWhatsappUrl }}"
      target="_blank"
      rel="noopener noreferrer"
      data-button-motion
    >
      <span class="material-symbols-outlined" aria-hidden="true">call</span>
      <span data-button-label>Hubungi Kami via WhatsApp</span>
    </a>
  </div>
</section>
