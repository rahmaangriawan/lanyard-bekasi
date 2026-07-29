@php
  $testimonials = [
    [
      'name' => 'Anggi Putri',
      'role' => 'Admin Sekolah',
      'quote' => 'Kami pesan lanyard custom untuk kegiatan sekolah, hasilnya memuaskan. Cetakan jelas, tidak mudah luntur, dan pengerjaannya tepat waktu. Komunikasi juga enak, jadi tidak khawatir soal revisi desain.',
      'avatar' => '/uploads/testimoni-anggi-putri.webp',
      'width' => 2560,
      'height' => 1707,
    ],
    [
      'name' => 'Andi Pratama',
      'role' => 'Event Organizer',
      'quote' => 'Pelayanan cepat dan hasil lanyardnya rapi banget. Warna sesuai desain, bahan juga nyaman dipakai seharian. Timnya responsif dari awal sampai barang diterima. Cocok buat kebutuhan event skala kecil sampai besar.',
      'avatar' => '/uploads/testimoni-andi-pratama.webp',
      'width' => 2560,
      'height' => 1707,
    ],
    [
      'name' => 'Dimas Nugroho',
      'role' => 'Owner UMKM',
      'quote' => 'Sudah beberapa kali order lanyard di sini dan selalu puas. Kualitas konsisten, harga masuk akal, dan bisa request desain sesuai brand. Sangat direkomendasikan untuk kebutuhan kantor maupun promosi.',
      'avatar' => '/uploads/testimoni-dimas-nugroho.webp',
      'width' => 1000,
      'height' => 667,
    ],
  ];
@endphp

<section class="home-testimonials" id="testimoni" aria-labelledby="home-testimonials-title" data-testimonials>
  <div class="home-testimonials__container">
    <header class="home-testimonials__header">
      <div class="home-testimonials__stars" role="img" aria-label="Rating 5 dari 5">
        @for ($star = 0; $star < 5; $star++)
          <span aria-hidden="true">&#9733;</span>
        @endfor
      </div>
      <h2 id="home-testimonials-title">Cerita Pelanggan Pusat Tali Lanyard</h2>
      <p>Pengalaman nyata pelanggan yang mempercayakan kebutuhan lanyard custom kepada tim kami.</p>
    </header>

    <div
      class="home-testimonials__track"
      id="home-testimonials-track"
      role="region"
      aria-label="Testimoni pelanggan"
      tabindex="0"
      data-testimonials-track
    >
      @foreach ($testimonials as $testimonial)
        <article class="home-testimonials__card" data-testimonial-item>
          <blockquote>
            <p>“{{ $testimonial['quote'] }}”</p>
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
        </article>
      @endforeach
    </div>

    <div class="home-testimonials__dots" role="group" aria-label="Pilih testimoni">
      @foreach ($testimonials as $index => $testimonial)
        <button
          type="button"
          class="home-testimonials__dot{{ $index === 0 ? ' is-active' : '' }}"
          aria-label="Tampilkan testimoni {{ $testimonial['name'] }}"
          aria-controls="home-testimonials-track"
          aria-current="{{ $index === 0 ? 'true' : 'false' }}"
          data-testimonial-dot="{{ $index }}"
        ></button>
      @endforeach
    </div>
  </div>
</section>
