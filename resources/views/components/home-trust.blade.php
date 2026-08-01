@php
  $trustItems = [
    ['icon' => 'verified', 'value' => '1.259+', 'label' => 'Lembaga & Event'],
    ['icon' => 'sell', 'value' => 'Harga Transparan', 'label' => 'Sesuai kebutuhan'],
    ['icon' => 'local_shipping', 'value' => 'Kirim Indonesia', 'label' => 'Jangkauan luas'],
    ['icon' => 'verified_user', 'value' => 'Garansi Kualitas', 'label' => 'Hasil terkontrol'],
  ];
@endphp

<section class="home-trust" aria-label="Keunggulan utama Lanyard Bekasi">
  <div class="home-trust__panel">
    <ul class="home-trust__list">
      @foreach ($trustItems as $item)
        <li>
          <span class="home-trust__icon material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
          <span>
            <strong>{{ $item['value'] }}</strong>
            <small>{{ $item['label'] }}</small>
          </span>
        </li>
      @endforeach
    </ul>
  </div>
</section>
