@php
  $siteUrl = rtrim((string) config('app.url', url('/')), '/');
  if (str_contains($siteUrl, 'localhost') || str_contains($siteUrl, '127.0.0.1')) {
    $siteUrl = 'https://lanyardbekasi.com';
  }

  $canonicalUrl = $siteUrl . '/garansi';
  $pageSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    '@id' => $canonicalUrl . '/#webpage',
    'name' => 'Garansi Kualitas Lanyard Custom',
    'description' => 'Cakupan garansi produksi, batas waktu pengajuan klaim, bukti yang diperlukan, dan proses validasi Lanyard Bekasi.',
    'url' => $canonicalUrl,
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
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Garansi', 'item' => $canonicalUrl],
    ],
  ];

  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    '@id' => $canonicalUrl . '/#faq',
    'url' => $canonicalUrl,
    'mainEntity' => collect($warrantyFaqs)->map(fn ($faq) => [
      '@type' => 'Question',
      'name' => $faq['question'],
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => $faq['answer'],
      ],
    ])->values()->all(),
  ];
@endphp
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($pageSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($breadcrumbSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($faqSchema) !!}</script>
