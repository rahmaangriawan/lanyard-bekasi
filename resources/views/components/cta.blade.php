@php
  $ctaWhatsappUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya mau konsultasi harga lanyard custom.');
@endphp

<section class="home-cta" id="cta-section" aria-labelledby="home-cta-title">
  <div class="home-cta__container">
    <div class="home-cta__image" aria-hidden="true">
      <img src="/uploads/lanyard-bekasi-cta.webp" alt="" width="941" height="1672" loading="lazy" decoding="async">
    </div>
    <div class="home-cta__copy">
      <h2 id="home-cta-title">Butuh Lanyard Custom dengan Harga Terbaik?</h2>
      <p>Konsultasikan kebutuhan Anda untuk mendapatkan rekomendasi bahan, desain, dan jumlah yang sesuai.</p>
    </div>
    <div class="home-cta__action">
      <a
        class="home-cta__button"
        href="{{ $ctaWhatsappUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        data-button-motion
      >
        <img src="/images/whatsapp.svg" alt="" width="20" height="20" loading="lazy" decoding="async" aria-hidden="true">
        <span data-button-label>Chat WhatsApp Sekarang</span>
        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
      </a>
      <ul aria-label="Keuntungan pemesanan">
        <li><span class="material-symbols-outlined" aria-hidden="true">verified</span> Kualitas terkontrol</li>
        <li><span class="material-symbols-outlined" aria-hidden="true">design_services</span> Bantuan desain</li>
        <li><span class="material-symbols-outlined" aria-hidden="true">local_shipping</span> Pengiriman luas</li>
      </ul>
    </div>
  </div>
</section>
