@php
  $benefits = [
    [
      'icon' => 'sell',
      'title' => 'Harga Transparan',
      'description' => 'Estimasi harga jelas dan dapat disesuaikan dengan jumlah pesanan.',
    ],
    [
      'icon' => 'design_services',
      'title' => 'Desain Custom',
      'description' => 'Tim kami membantu menyiapkan desain sesuai identitas dan kebutuhan Anda.',
    ],
    [
      'icon' => 'palette',
      'title' => 'Kualitas Cetak',
      'description' => 'Hasil cetak tajam dengan material yang nyaman untuk digunakan.',
    ],
    [
      'icon' => 'local_shipping',
      'title' => 'Pengiriman Luas',
      'description' => 'Pesanan dikemas rapi dan dapat dikirim ke berbagai wilayah Indonesia.',
    ],
    [
      'icon' => 'verified_user',
      'title' => 'Garansi Kualitas',
      'description' => 'Setiap hasil produksi diperiksa sebelum dikemas dan dikirim.',
      'id' => 'garansi',
    ],
  ];
@endphp

<section class="home-benefits" id="layanan" aria-labelledby="home-benefits-title">
  <div class="home-shell">
    <header class="home-section-heading">
      <p>Kenapa Memilih Kami</p>
      <h2 id="home-benefits-title">Kualitas Terbaik, Harga Bersahabat</h2>
    </header>

    <div class="home-benefits__grid">
      @foreach ($benefits as $benefit)
        <article class="benefit-item" @if(!empty($benefit['id'])) id="{{ $benefit['id'] }}" @endif>
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
