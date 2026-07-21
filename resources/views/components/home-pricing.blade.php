@php
  $pricingItems = [
    [
      'name' => 'Lanyard Tissue Premium',
      'description' => 'Bahan halus dengan warna cetak tajam.',
      'featured' => true,
      'prices' => [
        ['label' => '1 – 99 pcs', 'value' => 'Rp 12.000 /pcs'],
        ['label' => '100 – 499 pcs', 'value' => 'Rp 8.500 /pcs'],
        ['label' => '500 – 999 pcs', 'value' => 'Rp 6.000 /pcs'],
        ['label' => '1.000 – 4.999 pcs', 'value' => 'Rp 4.500 /pcs'],
        ['label' => '5.000+ pcs', 'value' => 'Hubungi Kami'],
      ],
    ],
    [
      'name' => 'Lanyard Polyester Standar',
      'description' => 'Ekonomis dan tahan lama untuk event besar.',
      'featured' => false,
      'prices' => [
        ['label' => '1 – 99 pcs', 'value' => 'Rp 8.000 /pcs'],
        ['label' => '100 – 499 pcs', 'value' => 'Rp 5.500 /pcs'],
        ['label' => '500 – 999 pcs', 'value' => 'Rp 4.000 /pcs'],
        ['label' => '1.000 – 4.999 pcs', 'value' => 'Rp 3.000 /pcs'],
        ['label' => '5.000+ pcs', 'value' => 'Hubungi Kami'],
      ],
    ],
    [
      'name' => 'Lanyard Sablon Full Color',
      'description' => 'Cocok untuk desain kompleks dan bergradasi.',
      'featured' => false,
      'prices' => [
        ['label' => '1 – 99 pcs', 'value' => 'Rp 14.000 /pcs'],
        ['label' => '100 – 499 pcs', 'value' => 'Rp 10.000 /pcs'],
        ['label' => '500 – 999 pcs', 'value' => 'Rp 7.500 /pcs'],
        ['label' => '1.000 – 4.999 pcs', 'value' => 'Rp 5.500 /pcs'],
        ['label' => '5.000+ pcs', 'value' => 'Hubungi Kami'],
      ],
    ],
    [
      'name' => 'Aksesoris & Hooks',
      'description' => 'Pilihan pelengkap untuk kebutuhan lanyard Anda.',
      'featured' => false,
      'prices' => [
        ['label' => 'Hook Plastik', 'value' => 'Rp 500 /pcs'],
        ['label' => 'Hook Metal', 'value' => 'Rp 1.500 /pcs'],
        ['label' => 'Hook Stainless', 'value' => 'Rp 3.000 /pcs'],
        ['label' => 'Stopper Silikon', 'value' => 'Rp 300 /pcs'],
        ['label' => 'ID Card Holder Kulit', 'value' => 'Rp 15.000 /pcs'],
      ],
    ],
  ];
@endphp

<section class="home-pricing" aria-labelledby="home-pricing-title">
  <div class="home-pricing__container">
    <header class="home-pricing__intro" data-sr="fade">
      <p class="home-pricing__eyebrow">Harga transparan</p>
      <h2 id="home-pricing-title">Daftar Harga Lanyard</h2>
      <p>Transparan dan kompetitif. Pilih ukuran dan kuantitas sesuai kebutuhan event atau perusahaan Anda.</p>
    </header>

    <div class="home-pricing__grid" aria-label="Pilihan estimasi harga lanyard">
      @foreach ($pricingItems as $item)
        @php
          $itemWhatsappUrl = 'https://wa.me/6282210200700?text=' . rawurlencode(
            'Halo Pusat Tali Lanyard, saya mau konsultasi harga ' . $item['name'] . '.'
          );
        @endphp

        <article class="price-card{{ $item['featured'] ? ' price-card--featured' : '' }}" data-sr="fade" data-sr-delay="{{ $loop->index * 70 }}">
          <div class="price-card__head">
            @if ($item['featured'])
              <span class="price-card__badge">Terlaris</span>
            @endif
            <h3>{{ $item['name'] }}</h3>
            <p>{{ $item['description'] }}</p>
          </div>

          <dl class="price-card__list">
            @foreach ($item['prices'] as $price)
              <div class="price-card__row">
                <dt>{{ $price['label'] }}</dt>
                <dd>{{ $price['value'] }}</dd>
              </div>
            @endforeach
          </dl>

          <div class="price-card__footer">
            <a
              href="{{ $itemWhatsappUrl }}"
              target="_blank"
              rel="noopener noreferrer"
              class="price-card__cta"
              data-button-motion
              aria-label="Hubungi kami tentang {{ $item['name'] }}"
            ><span data-button-label>Hubungi Kami</span></a>
          </div>
        </article>
      @endforeach
    </div>

    <div class="home-pricing__bottom" data-sr="fade">
      <p>Harga merupakan estimasi dan dapat menyesuaikan desain serta aksesori yang dipilih.</p>
      <a href="{{ route('pricing') }}" class="home-pricing__more">
        <span>Lihat daftar harga lengkap</span>
        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
      </a>
    </div>
  </div>
</section>
