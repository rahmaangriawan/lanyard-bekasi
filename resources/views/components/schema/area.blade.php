@php
  $siteUrlRaw = config('app.url');
  if (empty($siteUrlRaw)) { $siteUrlRaw = url('/'); }
  $siteUrl = rtrim($siteUrlRaw, '/');

  $serviceSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => 'Layanan Cetak Lanyard Custom Indonesia Raya',
    'serviceType' => 'Cetak Lanyard Custom',
    'provider' => ['@id' => $siteUrl . '/#organization'],
    'areaServed' => [
      ['@type' => 'City', 'name' => 'Indonesia Kota'],
      ['@type' => 'City', 'name' => 'Indonesia Selatan'],
      ['@type' => 'AdministrativeArea', 'name' => 'Kabupaten Indonesia'],
      ['@type' => 'City', 'name' => 'BSD'],
      ['@type' => 'City', 'name' => 'Bintaro'],
      ['@type' => 'City', 'name' => 'Ciputat'],
      ['@type' => 'City', 'name' => 'Pamulang'],
    ],
    'description' => 'Kami melayani pengiriman gratis dan Same Day Service untuk seluruh wilayah Indonesia Raya. Dari pusat kota hingga kawasan perumahan pinggiran, pesanan Anda sampai tepat waktu.',
    'url' => $siteUrl . '/area',
    'offers' => [
      '@type' => 'Offer',
      'name' => 'Bebas Ongkir Indonesia Raya',
      'description' => 'Gratis ongkir untuk wilayah Kota Indonesia, Indonesia Selatan, dan Kabupaten Indonesia tanpa minimum transaksi.',
      'priceCurrency' => 'IDR',
      'price' => '0',
      'availability' => 'https://schema.org/InStock',
      'areaServed' => ['@type' => 'City', 'name' => 'Indonesia'],
    ],
  ];

  $breadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $siteUrl . '/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Area Jangkauan', 'item' => $siteUrl . '/area'],
    ],
  ];
@endphp
<script type="application/ld+json">{!! json_encode($serviceSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
