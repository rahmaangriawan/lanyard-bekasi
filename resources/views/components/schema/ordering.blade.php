@php
  $siteUrl = rtrim((string) config('app.url', url('/')), '/');
  if (str_contains($siteUrl, 'localhost') || str_contains($siteUrl, '127.0.0.1')) {
    $siteUrl = 'https://pusattalilanyard.com';
  }
  $canonicalUrl = $siteUrl . '/cara-pemesanan';

  $howToSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'HowTo',
    '@id' => $canonicalUrl . '/#howto',
    'name' => 'Cara Pemesanan Lanyard Custom dalam 5 Langkah',
    'description' => 'Alur pemesanan lanyard custom dari pemilihan spesifikasi hingga produksi dan pengiriman.',
    'step' => collect($orderSteps)->values()->map(fn ($step, $index) => [
      '@type' => 'HowToStep',
      'position' => $index + 1,
      'name' => $step['title'],
      'text' => $step['description'],
      'url' => $canonicalUrl . '#langkah-' . ($index + 1),
    ])->all(),
    'url' => $canonicalUrl,
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $siteUrl . '/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Cara Pemesanan', 'item' => $canonicalUrl],
    ],
  ];
@endphp
<script type="application/ld+json">{!! json_encode($howToSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
