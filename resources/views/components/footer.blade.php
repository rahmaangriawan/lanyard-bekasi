<footer class="site-footer" id="site-footer" aria-labelledby="site-footer-title">
  <h2 class="sr-only" id="site-footer-title">Informasi Lanyard Bekasi</h2>

  <div class="site-footer__container">
    <div class="site-footer__grid">
      <div class="site-footer__brand">
        <a class="site-footer__logo" href="{{ route('home') }}" aria-label="Lanyard Bekasi, kembali ke beranda">
          <img
            src="/uploads/lanyard-bekasi-logo.webp"
            alt="Lanyard Bekasi"
            width="154"
            height="38"
            loading="lazy"
            decoding="async"
          />
        </a>
        <p>Vendor lanyard custom untuk kebutuhan kantor, event, sekolah, dan komunitas dengan proses yang jelas.</p>
      </div>

      <nav class="site-footer__nav" aria-label="Navigasi footer">
        <div class="site-footer__nav-group">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="{{ route('services') }}">Layanan</a></li>
            <li><a href="{{ route('products.index') }}">Produk</a></li>
            <li><a href="{{ route('shipping') }}">Pengiriman</a></li>
            <li><a href="{{ route('warranty') }}">Garansi</a></li>
            <li><a href="{{ route('faq') }}">FAQ</a></li>
            <li><a href="{{ route('ordering') }}">Cara Pemesanan</a></li>
          </ul>
        </div>

        <div class="site-footer__nav-group">
          <h3>Legal</h3>
          <ul>
            <li><a href="{{ route('privacy') }}">Kebijakan Privasi</a></li>
            <li><a href="{{ route('terms') }}">Syarat &amp; Ketentuan</a></li>
          </ul>
        </div>

        <div class="site-footer__contact-wrap">
          <h3>Kontak Kami</h3>
          <address class="site-footer__contact">
            <a href="tel:+6282210200700">
              <span class="material-symbols-outlined" aria-hidden="true">call</span>
              <span>+62 822-1020-0700</span>
            </a>
            <a href="mailto:info@lanyardbekasi.com">
              <span class="material-symbols-outlined" aria-hidden="true">mail</span>
              <span>info<wbr>@lanyardbekasi.com</span>
            </a>
            <p>
              <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
              <span>Bekasi, Indonesia</span>
            </p>
          </address>
        </div>
      </nav>
    </div>

    <div class="site-footer__bottom">
      <small>&copy; {{ date('Y') }} Lanyard Bekasi. Seluruh hak dilindungi.</small>
    </div>
  </div>
</footer>
