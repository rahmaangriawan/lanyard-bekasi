@php
  $testimonials = [
    [
      'name' => 'Anggi Putri',
      'role' => 'Admin Sekolah',
      'quote' => 'Kami pesan lanyard custom untuk kegiatan sekolah, hasilnya memuaskan. Cetakan jelas, tidak mudah luntur, dan pengerjaannya tepat waktu.',
      'avatar' => '/uploads/testimoni-anggi-putri.webp',
      'width' => 2560,
      'height' => 1707,
      'productImage' => '/uploads/testimonial-lanyard-anggi.webp',
      'productAlt' => 'Contoh hasil lanyard custom merah dan putih',
      'productWidth' => 1200,
      'productHeight' => 801,
    ],
    [
      'name' => 'Andi Pratama',
      'role' => 'Event Organizer',
      'quote' => 'Pelayanan cepat dan hasil lanyardnya rapi. Warna sesuai desain, bahan nyaman dipakai, dan timnya responsif sampai barang diterima.',
      'avatar' => '/uploads/testimoni-andi-pratama.webp',
      'width' => 2560,
      'height' => 1707,
      'productImage' => '/uploads/testimonial-lanyard-andi.webp',
      'productAlt' => 'Contoh hasil lanyard custom hitam dengan aksen hijau dan biru',
      'productWidth' => 1200,
      'productHeight' => 801,
    ],
    [
      'name' => 'Dimas Nugroho',
      'role' => 'Owner UMKM',
      'quote' => 'Kualitas selalu konsisten, harga masuk akal, dan desain bisa disesuaikan dengan brand. Cocok untuk kebutuhan kantor maupun promosi.',
      'avatar' => '/uploads/testimoni-dimas-nugroho.webp',
      'width' => 1000,
      'height' => 667,
      'productImage' => '/uploads/testimonial-lanyard-dimas.webp',
      'productAlt' => 'Contoh hasil lanyard custom cokelat bermotif',
      'productWidth' => 1200,
      'productHeight' => 801,
    ],
  ];
@endphp

<section class="home-testimonials" id="testimoni" aria-labelledby="home-testimonials-title" data-testimonials>
  <div class="home-shell">
    <header class="home-section-heading">
      <p>Apa Kata Mereka</p>
      <h2 id="home-testimonials-title">Cerita Pelanggan Kami</h2>
    </header>

    <div class="home-testimonials__carousel">
      <div class="home-testimonials__viewport">
        <div
          class="home-testimonials__track"
          id="home-testimonials-track"
          role="region"
          aria-label="Testimoni pelanggan"
          tabindex="0"
          data-testimonials-track
        >
          @foreach ($testimonials as $testimonial)
            <article
              class="home-testimonials__card"
              aria-label="Testimoni {{ $testimonial['name'] }}"
              data-testimonial-item
              data-testimonial-name="{{ $testimonial['name'] }}"
            >
              <figure class="home-testimonials__media">
                <img
                  class="home-testimonials__media-image"
                  src="{{ $testimonial['productImage'] }}"
                  alt="{{ $testimonial['productAlt'] }}"
                  width="{{ $testimonial['productWidth'] }}"
                  height="{{ $testimonial['productHeight'] }}"
                  loading="lazy"
                  decoding="async"
                />
              </figure>

              <div class="home-testimonials__body">
                <span class="home-testimonials__quote-mark" aria-hidden="true">&ldquo;</span>
                <div class="home-testimonials__stars" role="img" aria-label="Rating 5 dari 5">
                  @for ($star = 0; $star < 5; $star++)
                    <span aria-hidden="true">&#9733;</span>
                  @endfor
                </div>
                <blockquote>
                  <p>&ldquo;{{ $testimonial['quote'] }}&rdquo;</p>
                </blockquote>
                <footer class="home-testimonials__person">
                  <img
                    src="{{ $testimonial['avatar'] }}"
                    alt="Foto {{ $testimonial['name'] }}"
                    width="{{ $testimonial['width'] }}"
                    height="{{ $testimonial['height'] }}"
                    loading="lazy"
                    decoding="async"
                  />
                  <div>
                    <cite>{{ $testimonial['name'] }}</cite>
                    <span>{{ $testimonial['role'] }}</span>
                  </div>
                </footer>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </div>

    <p class="home-testimonials__status sr-only" aria-live="polite" aria-atomic="true" data-testimonials-status>
      Testimoni 1 dari {{ count($testimonials) }}: {{ $testimonials[0]['name'] }}
    </p>
  </div>
</section>
