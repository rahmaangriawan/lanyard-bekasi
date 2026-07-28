@php
  $siteUrl = rtrim((string) config('app.url', url('/')), '/');
  if (str_contains($siteUrl, 'localhost') || str_contains($siteUrl, '127.0.0.1')) {
    $siteUrl = 'https://pusattalilanyard.com';
  }
  $canonicalUrl = $siteUrl . '/area';
  $areaNames = ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi'];

  $serviceSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    '@id' => $canonicalUrl . '/#service',
    'name' => 'Pengiriman Lanyard Custom ke Seluruh Indonesia',
    'serviceType' => 'Produksi dan Pengiriman Lanyard Custom',
    'provider' => ['@id' => $siteUrl . '/#organization'],
    'areaServed' => collect($areaNames)->map(fn ($area) => [
      '@type' => 'City',
      'name' => $area,
    ])->all(),
    'description' => 'Pusat Tali Lanyard melayani produksi dan pengiriman lanyard custom ke seluruh Indonesia dengan cakupan utama Jabodetabek.',
    'url' => $canonicalUrl,
  ];

  $breadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $siteUrl . '/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Area Pengiriman', 'item' => $canonicalUrl],
    ],
  ];
@endphp
<script type="application/ld+json">{!! json_encode($serviceSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
