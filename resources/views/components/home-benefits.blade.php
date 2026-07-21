@php
  $benefits = [
    [
      'icon' => 'schedule',
      'title' => 'Tepat Waktu',
      'description' => 'Jadwal produksi mengikuti kesepakatan dan kebutuhan deadline Anda.',
    ],
    [
      'icon' => 'palette',
      'title' => 'Akurasi Warna 99%',
      'description' => 'Teknologi cetak modern menghasilkan warna yang tajam, jernih, dan detail.',
    ],
    [
      'icon' => 'design_services',
      'title' => 'Desain Custom',
      'description' => 'Tim kami membantu mewujudkan desain yang sesuai dengan identitas Anda.',
    ],
    [
      'icon' => 'shopping_cart',
      'title' => 'Pemesanan Mudah',
      'description' => 'Konsultasi dan pemesanan praktis dengan bantuan admin yang responsif.',
    ],
  ];
@endphp

<section class="home-benefits" aria-labelledby="home-benefits-title">
  <div class="home-benefits__container">
    <header class="home-benefits__header">
      <h2 id="home-benefits-title">Kenapa Memilih Pusat Tali Lanyard?</h2>
      <p>Produksi berkualitas, proses yang jelas, dan layanan yang memudahkan kebutuhan lanyard Anda.</p>
    </header>

    <div class="home-benefits__grid">
      @foreach ($benefits as $benefit)
        <article class="benefit-item">
          <span class="benefit-item__icon" aria-hidden="true">
            <span class="material-symbols-outlined">{{ $benefit['icon'] }}</span>
          </span>
          <h3>{{ $benefit['title'] }}</h3>
          <p>{{ $benefit['description'] }}</p>
        </article>
      @endforeach
    </div>
  </div>
</section>
