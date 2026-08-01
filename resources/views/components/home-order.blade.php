@php
  $homeOrderSteps = [
    ['icon' => 'chat', 'title' => 'Konsultasi Pesanan', 'description' => 'Sampaikan jumlah, bahan, dan target waktu.'],
    ['icon' => 'design_services', 'title' => 'Kirim Desain', 'description' => 'Kirim logo atau minta bantuan desain dari tim kami.'],
    ['icon' => 'verified', 'title' => 'Setujui & Bayar', 'description' => 'Periksa mockup dan quotation sebelum produksi.'],
    ['icon' => 'local_shipping', 'title' => 'Produksi & Kirim', 'description' => 'Pesanan diperiksa, dikemas, lalu dikirim.'],
  ];
@endphp

<section class="home-order" id="pemesanan" aria-labelledby="home-order-title">
  <div class="home-shell">
    <header class="home-section-heading">
      <p>Cara Order Mudah</p>
      <h2 id="home-order-title">4 Langkah Mudah Memesan</h2>
    </header>

    <ol class="home-order__list">
      @foreach ($homeOrderSteps as $step)
        <li>
          <span class="home-order__number">{{ $loop->iteration }}</span>
          <span class="home-order__icon material-symbols-outlined" aria-hidden="true">{{ $step['icon'] }}</span>
          <h3>{{ $step['title'] }}</h3>
          <p>{{ $step['description'] }}</p>
        </li>
      @endforeach
    </ol>

    <a class="home-order__link" href="{{ route('ordering') }}">
      Lihat cara pemesanan lengkap
      <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
    </a>
  </div>
</section>
