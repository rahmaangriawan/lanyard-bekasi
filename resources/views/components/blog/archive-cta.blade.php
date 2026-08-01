@php
  $blogWhatsappUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya sudah membaca blog dan ingin konsultasi produk lanyard custom.');
@endphp

<section class="blog-archive-cta" aria-labelledby="blog-archive-cta-title">
  <div class="blog-archive-shell">
    <div class="blog-archive-cta__panel">
      <div>
        <h2 id="blog-archive-cta-title">Butuh Lanyard yang Sesuai Kebutuhan Anda?</h2>
        <p>Gunakan panduan di blog atau konsultasikan bahan, ukuran, dan jumlah pesanan langsung dengan tim kami.</p>
      </div>
      <div class="blog-archive-cta__actions">
        <a class="blog-archive-button blog-archive-button--light" href="{{ route('products.index') }}">
          <span>Lihat Produk</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
        <a class="blog-archive-button blog-archive-button--outline" href="{{ $blogWhatsappUrl }}" target="_blank" rel="noopener noreferrer">
          <img src="/images/whatsapp.svg" alt="" width="20" height="20" aria-hidden="true">
          <span>Chat WhatsApp</span>
        </a>
      </div>
    </div>
  </div>
</section>
