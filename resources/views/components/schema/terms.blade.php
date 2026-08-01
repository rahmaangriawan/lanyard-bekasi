@php
  $siteUrl = rtrim((string) config('app.url', url('/')), '/');
  if (str_contains($siteUrl, 'localhost') || str_contains($siteUrl, '127.0.0.1')) {
    $siteUrl = 'https://lanyardbekasi.com';
  }

  $canonicalUrl = $siteUrl . '/syarat-ketentuan';
  $pageSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    '@id' => $canonicalUrl . '/#webpage',
    'name' => 'Syarat dan Ketentuan Pemesanan Lanyard Bekasi',
    'description' => 'Ketentuan persetujuan mockup, pembayaran, toleransi produksi, pengiriman, pembatalan, dan garansi pemesanan Lanyard Bekasi.',
    'url' => $canonicalUrl,
    'inLanguage' => 'id-ID',
    'dateModified' => '2026-07-31',
    'isPartOf' => [
      '@type' => 'WebSite',
      'name' => 'Lanyard Bekasi',
      'url' => $siteUrl,
    ],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $siteUrl . '/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Syarat dan Ketentuan', 'item' => $canonicalUrl],
    ],
  ];
@endphp
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($pageSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($breadcrumbSchema) !!}</script>
