@php
  $footerQuotationUrl = 'https://wa.me/6282210200700?text=Halo%20Pusat%20Tali%20Lanyard%2C%20saya%20mau%20minta%20quotation%20lanyard%20custom.';
@endphp

<footer class="site-footer" id="site-footer" aria-labelledby="site-footer-title">
  <h2 class="sr-only" id="site-footer-title">Informasi Pusat Tali Lanyard</h2>

  <div class="site-footer__container">
    <div class="site-footer__top">
      <div class="site-footer__identity">
        <a class="site-footer__logo" href="{{ route('home') }}" aria-label="Pusat Tali Lanyard, kembali ke beranda">
          <img
            src="/uploads/pusat-tali-lanyard-logo.webp"
            alt="Pusat Tali Lanyard"
            width="154"
            height="57"
            loading="lazy"
            decoding="async"
          />
        </a>

        <address class="site-footer__contact">
          <p>Jl. Manding Imogiri, Ponggok I, Trimulyo, Kec. Jetis, Kabupaten Bantul, Daerah Istimewa Yogyakarta 55781</p>
          <a href="mailto:hello@pusattalilanyard.com">hello<wbr>@pusattalilanyard.com</a>
          <a href="tel:+6282210200700">+62 822-1020-0700</a>
        </address>
      </div>

      <nav class="site-footer__nav" aria-label="Navigasi footer">
        <div class="site-footer__nav-group">
          <h3>Menu</h3>
          <ul>
            <li><a href="{{ route('home') }}">Beranda</a></li>
            <li><a href="{{ route('products.index') }}">Produk</a></li>
            <li><a href="{{ route('area') }}">Area Layanan</a></li>
            <li><a href="{{ route('ordering') }}">Cara Pemesanan</a></li>
          </ul>
        </div>

        <div class="site-footer__nav-group">
          <h3>Layanan</h3>
          <ul>
            <li><a href="{{ route('pricing') }}">Daftar Harga</a></li>
            <li><a href="{{ route('promo') }}">Promo</a></li>
            <li><a href="{{ route('shipping') }}">Pengiriman</a></li>
            <li><a href="{{ route('blog.index') }}">Blog</a></li>
          </ul>
        </div>

        <div class="site-footer__nav-group">
          <h3>Bantuan</h3>
          <ul>
            <li><a href="{{ $footerQuotationUrl }}" target="_blank" rel="noopener noreferrer">Hubungi Kami</a></li>
            <li><a href="{{ route('home') }}#faq">FAQ</a></li>
            <li><a href="{{ route('privacy') }}">Kebijakan Privasi</a></li>
            <li><a href="{{ route('terms') }}">Syarat &amp; Ketentuan</a></li>
          </ul>
        </div>
      </nav>
    </div>

    <div class="site-footer__cta-row">
      <span class="site-footer__rule" aria-hidden="true"></span>
      <a
        class="site-footer__cta"
        href="{{ $footerQuotationUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        data-button-motion
      >
        <span data-button-label>Minta Quotation</span>
        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
      </a>
    </div>

    <div class="site-footer__bottom">
      <div class="site-footer__summary">
        <p>Cetak lanyard custom untuk kantor, event, sekolah, dan komunitas dengan proses cepat serta hasil profesional.</p>
        <small>&copy; {{ date('Y') }} Pusat Tali Lanyard</small>
      </div>

      <nav class="site-footer__legal" aria-label="Informasi legal">
        <a href="{{ route('terms') }}">Syarat &amp; Ketentuan</a>
        <a href="{{ route('privacy') }}">Kebijakan Privasi</a>
      </nav>
    </div>
  </div>

  <div class="site-footer__wordmark" aria-hidden="true">PUSAT TALI LANYARD</div>
</footer>
