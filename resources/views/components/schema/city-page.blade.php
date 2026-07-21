@php
  $siteUrlRaw = config('app.url');
  if (empty($siteUrlRaw)) { $siteUrlRaw = url('/'); }
  $siteUrl = rtrim($siteUrlRaw, '/');
  $cityUrl = $siteUrl.'/area/'.$cityPage->slug;
  $description = $cityPage->metaDescription ?: 'Layanan cetak lanyard custom untuk '.$cityPage->title.'.';

  $serviceSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => 'Cetak Lanyard Custom '.$cityPage->title,
    'serviceType' => 'Cetak Lanyard Custom',
    'url' => $cityUrl,
    'description' => $description,
    'provider' => ['@id' => $siteUrl.'/#organization'],
    'areaServed' => ['@type' => 'City', 'name' => $cityPage->title],
  ];

  $breadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $siteUrl.'/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Area Layanan', 'item' => $siteUrl.'/area'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => $cityPage->title, 'item' => $cityUrl],
    ],
  ];
@endphp
<script type="application/ld+json">{!! json_encode($serviceSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
