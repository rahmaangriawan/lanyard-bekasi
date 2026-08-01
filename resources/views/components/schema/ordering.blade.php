@php
  $siteUrl = rtrim((string) config('app.url', url('/')), '/');
  if (str_contains($siteUrl, 'localhost') || str_contains($siteUrl, '127.0.0.1')) {
    $siteUrl = 'https://lanyardbekasi.com';
  }
  $canonicalUrl = $siteUrl . '/cara-pemesanan';

  $howToSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'HowTo',
    '@id' => $canonicalUrl . '/#howto',
    'name' => 'Cara Pemesanan Lanyard Custom dalam 6 Langkah',
    'description' => 'Alur pemesanan lanyard custom dari konsultasi kebutuhan hingga produksi, quality control, dan pengiriman.',
    'image' => $siteUrl . '/uploads/hero-cara-pemesanan-lanyard-bekasi.webp',
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

  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    '@id' => $canonicalUrl . '/#faq',
    'url' => $canonicalUrl,
    'mainEntity' => collect($orderFaqs ?? [])->map(fn ($faq) => [
      '@type' => 'Question',
      'name' => $faq['question'],
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => $faq['answer'],
      ],
    ])->values()->all(),
  ];
@endphp
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($howToSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($breadcrumbSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($faqSchema) !!}</script>
