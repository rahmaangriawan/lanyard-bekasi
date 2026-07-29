@php
  $siteUrlRaw = config('app.url');
  if (empty($siteUrlRaw)) { $siteUrlRaw = url('/'); }
  $siteUrl = rtrim($siteUrlRaw, '/');
  $logoUrl = $siteUrl . '/uploads/pusat-tali-lanyard-logo.webp';
  $logoHeaderUrl = $siteUrl . '/uploads/pusat-tali-lanyard-logo.webp';

  $orgSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    '@id' => $siteUrl . '/#organization',
    'name' => 'Pusat Tali Lanyard',
    'url' => $siteUrl . '/',
    'logo' => [
      '@type' => 'ImageObject',
      'url' => $logoUrl,
      'width' => 512,
      'height' => 512,
    ],
    'image' => $logoUrl,
    'telephone' => '+6282210200700',
    'email' => 'hello@pusattalilanyard.id',
    'description' => 'Cetak lanyard custom premium di Indonesia dengan pilihan bahan berkualitas, desain profesional, dan layanan pemesanan mudah. Bebas ongkir Indonesia Raya.',
    'sameAs' => [
      'https://wa.me/6282210200700',
    ],
    'address' => [
      '@type' => 'PostalAddress',
      'addressLocality' => 'Indonesia',
      'addressRegion' => 'Banten',
      'addressCountry' => 'ID',
    ],
  ];

  $localBusinessSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    '@id' => $siteUrl . '/#localbusiness',
    'name' => 'Pusat Tali Lanyard',
    'url' => $siteUrl . '/',
    'logo' => $logoUrl,
    'image' => $logoHeaderUrl,
    'telephone' => '+6282210200700',
    'priceRange' => 'Rp 1000 - Rp 50000',
    'description' => 'Cetak lanyard custom premium dengan pengiriman ke seluruh Indonesia dan cakupan layanan utama Jabodetabek.',
    'address' => [
      '@type' => 'PostalAddress',
      'streetAddress' => 'Jl. Sudirman No. 123',
      'addressLocality' => 'Indonesia',
      'addressRegion' => 'Banten',
      'postalCode' => '15111',
      'addressCountry' => 'ID',
    ],
    'geo' => [
      '@type' => 'GeoCoordinates',
      'latitude' => -6.1765,
      'longitude' => 106.63,
    ],
    'openingHours' => 'Mo-Sa 08:00-20:00',
    'areaServed' => [
      ['@type' => 'City', 'name' => 'Jakarta'],
      ['@type' => 'City', 'name' => 'Bogor'],
      ['@type' => 'City', 'name' => 'Depok'],
      ['@type' => 'City', 'name' => 'Tangerang'],
      ['@type' => 'City', 'name' => 'Bekasi'],
    ],
  ];

  $websiteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    '@id' => $siteUrl . '/#website',
    'url' => $siteUrl . '/',
    'name' => 'Pusat Tali Lanyard',
    'publisher' => ['@id' => $siteUrl . '/#organization'],
    'inLanguage' => 'id-ID',
    'potentialAction' => [
      [
        '@type' => 'SearchAction',
        'target' => [
          '@type' => 'EntryPoint',
          'urlTemplate' => $siteUrl . '/produk?q={search_term_string}',
        ],
        'query-input' => 'required name=search_term_string',
      ],
      [
        '@type' => 'SearchAction',
        'target' => [
          '@type' => 'EntryPoint',
          'urlTemplate' => $siteUrl . '/blog?q={search_term_string}',
        ],
        'query-input' => 'required name=search_term_string',
      ],
    ],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => 'Home',
        'item' => $siteUrl . '/',
      ],
    ],
  ];

  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
      [
        '@type' => 'Question',
        'name' => 'Bisa cetak logo sendiri di lanyard?',
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text' => 'Bisa. Anda dapat mengirimkan logo, warna brand, atau referensi desain kepada tim kami. Logo akan disesuaikan dengan ukuran lanyard agar hasil cetaknya tetap jelas dan proporsional.',
        ],
      ],
      [
        '@type' => 'Question',
        'name' => 'Berapa lama proses cetak lanyard custom?',
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text' => 'Waktu produksi standar sekitar 3–5 hari kerja setelah desain disetujui. Estimasi dapat menyesuaikan jumlah pesanan, bahan, tingkat kerumitan desain, dan antrean produksi saat pemesanan.',
        ],
      ],
      [
        '@type' => 'Question',
        'name' => 'Apakah bisa request contoh desain dulu?',
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text' => 'Bisa. Tim kami akan menyiapkan mockup digital berdasarkan logo, warna, dan kebutuhan Anda sebelum produksi dimulai. Produksi baru diproses setelah desain mendapatkan persetujuan.',
        ],
      ],
      [
        '@type' => 'Question',
        'name' => 'Aksesoris lanyard apa saja yang bisa dipilih?',
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text' => 'Tersedia pilihan hook plastik, hook metal, stopper silikon, dan ID Card holder. Tim kami dapat membantu memilih kombinasi aksesori yang sesuai dengan penggunaan dan anggaran Anda.',
        ],
      ],
    ],
  ];
  // Satu sumber data untuk FAQ yang terlihat dan JSON-LD agar tidak drift.
  $faqSchema = \App\Support\StructuredData::faqSchema(config('seo.faq', []));
@endphp
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($orgSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($localBusinessSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($websiteSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($breadcrumbSchema) !!}</script>
@if($faqSchema)
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($faqSchema) !!}</script>
@endif
