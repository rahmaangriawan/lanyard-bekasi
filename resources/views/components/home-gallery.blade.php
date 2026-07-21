@php
  $galleryItems = [
    [
      'src' => '/uploads/galeri-lanyard-01.webp',
      'alt' => 'Lanyard custom BUMN dan Pelindo berwarna biru putih',
      'width' => 2048,
      'height' => 1368,
    ],
    [
      'src' => '/uploads/galeri-lanyard-02.webp',
      'alt' => 'Lanyard custom full color dengan desain kreatif',
      'width' => 2048,
      'height' => 2048,
    ],
    [
      'src' => '/uploads/galeri-lanyard-03.webp',
      'alt' => 'Lanyard custom Maritime Advance berwarna cokelat',
      'width' => 2048,
      'height' => 1368,
    ],
    [
      'src' => '/uploads/galeri-lanyard-04.webp',
      'alt' => 'Lanyard custom BTS berwarna ungu',
      'width' => 2048,
      'height' => 1368,
    ],
    [
      'src' => '/uploads/galeri-lanyard-05.webp',
      'alt' => 'Lanyard custom University of Liverpool berwarna biru',
      'width' => 2048,
      'height' => 1368,
    ],
  ];
@endphp

<section class="home-gallery" aria-labelledby="home-gallery-title" data-gallery>
  <div class="home-gallery__container">
    <header class="home-gallery__header">
      <h2 id="home-gallery-title">Galeri Hasil Produksi</h2>
      <p>Lihat detail warna, cetakan, dan hasil akhir lanyard custom yang telah kami kerjakan.</p>
    </header>

    <div
      class="home-gallery__track"
      id="home-gallery-track"
      role="region"
      aria-label="Galeri hasil produksi lanyard"
      tabindex="0"
      data-gallery-track
    >
      @foreach ($galleryItems as $item)
        <figure class="home-gallery__item" data-gallery-item>
          <img
            src="{{ $item['src'] }}"
            alt="{{ $item['alt'] }}"
            width="{{ $item['width'] }}"
            height="{{ $item['height'] }}"
            loading="lazy"
            decoding="async"
          />
        </figure>
      @endforeach
    </div>

    <div class="home-gallery__controls" aria-label="Kontrol galeri">
      <button type="button" class="home-gallery__button" data-gallery-prev aria-label="Foto sebelumnya" aria-controls="home-gallery-track">
        <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
      </button>
      <span class="home-gallery__status" aria-live="polite" aria-atomic="true">
        <strong data-gallery-current>1</strong>
        <span aria-hidden="true">/</span>
        <span data-gallery-total>{{ count($galleryItems) }}</span>
      </span>
      <button type="button" class="home-gallery__button" data-gallery-next aria-label="Foto berikutnya" aria-controls="home-gallery-track">
        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
      </button>
    </div>
  </div>
</section>
