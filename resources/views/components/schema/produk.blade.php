@php
  $siteUrlRaw = config('app.url');
  if (empty($siteUrlRaw)) { $siteUrlRaw = url('/'); }
  $siteUrl = rtrim($siteUrlRaw, '/');

  $collectionSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'Produk Lanyard Custom Indonesia',
    'description' => 'Temukan model lanyard berdasarkan nama, SKU, spesifikasi, atau kebutuhan aksesoris. Cetak lanyard custom premium di Indonesia.',
    'url' => $siteUrl . '/produk',
    'inLanguage' => 'id-ID',
    'isPartOf' => ['@id' => $siteUrl . '/#website'],
  ];

  if (isset($products) && count($products) > 0) {
    $itemList = [];
    $pos = 1;
    foreach ($products as $prod) {
      $name = isset($prod->name) ? $prod->name : '';
      $slug = isset($prod->slug) ? $prod->slug : '';
      if (empty($name)) continue;
      $url = $siteUrl . '/produk';
      if (!empty($slug)) $url .= '/' . $slug;
      $itemList[] = [
        '@type' => 'ListItem',
        'position' => $pos,
        'name' => $name,
        'url' => $url,
      ];
      $pos++;
      if ($pos > 20) break;
    }
    if (!empty($itemList)) {
      $collectionSchema['mainEntity'] = [
        '@type' => 'ItemList',
        'numberOfItems' => count($itemList),
        'itemListElement' => $itemList,
      ];
    }
  }

  $breadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $siteUrl . '/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Produk', 'item' => $siteUrl . '/produk'],
    ],
  ];

  $offerCatalog = [
    '@context' => 'https://schema.org',
    '@type' => 'OfferCatalog',
    'name' => 'Katalog Lanyard Custom Indonesia',
    'url' => $siteUrl . '/produk',
  ];
@endphp
<script type="application/ld+json">{!! json_encode($collectionSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($offerCatalog, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
