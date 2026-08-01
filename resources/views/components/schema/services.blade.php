@php
  $siteUrl = rtrim((string) config('app.url', url('/')), '/');
  if (str_contains($siteUrl, 'localhost') || str_contains($siteUrl, '127.0.0.1')) {
    $siteUrl = 'https://lanyardbekasi.com';
  }

  $canonicalUrl = $siteUrl . '/layanan';
  $serviceSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    '@id' => $canonicalUrl . '/#service',
    'name' => 'Layanan Cetak Lanyard Custom Lanyard Bekasi',
    'description' => 'Konsultasi spesifikasi, desain dan mockup, pemilihan bahan serta aksesori, produksi, quality control, dan pengiriman lanyard custom.',
    'url' => $canonicalUrl,
    'serviceType' => 'Cetak lanyard custom',
    'areaServed' => [
      '@type' => 'Country',
      'name' => 'Indonesia',
    ],
    'provider' => [
      '@type' => 'Organization',
      'name' => 'Lanyard Bekasi',
      'url' => $siteUrl,
    ],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $siteUrl . '/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Layanan', 'item' => $canonicalUrl],
    ],
  ];

  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    '@id' => $canonicalUrl . '/#faq',
    'url' => $canonicalUrl,
    'mainEntity' => collect($serviceFaqs)->map(fn ($faq) => [
      '@type' => 'Question',
      'name' => $faq['question'],
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => $faq['answer'],
      ],
    ])->values()->all(),
  ];
@endphp
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($serviceSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($breadcrumbSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($faqSchema) !!}</script>
