@php
  $siteUrl = rtrim((string) config('app.url', url('/')), '/');
  if (str_contains($siteUrl, 'localhost') || str_contains($siteUrl, '127.0.0.1')) {
    $siteUrl = 'https://lanyardbekasi.com';
  }

  $canonicalUrl = $siteUrl . '/kebijakan';
  $pageSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    '@id' => $canonicalUrl . '/#webpage',
    'name' => 'Kebijakan Privasi Lanyard Bekasi',
    'description' => 'Informasi pengumpulan, penggunaan, penyimpanan, dan perlindungan data pribadi serta file desain pelanggan Lanyard Bekasi.',
    'url' => $canonicalUrl,
    'inLanguage' => 'id-ID',
    'dateModified' => '2025-07-01',
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
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Kebijakan Privasi', 'item' => $canonicalUrl],
    ],
  ];
@endphp
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($pageSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($breadcrumbSchema) !!}</script>
