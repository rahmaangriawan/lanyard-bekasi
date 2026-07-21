@php
  $siteUrlRaw = config('app.url');
  if (empty($siteUrlRaw)) { $siteUrlRaw = url('/'); }
  $siteUrl = rtrim($siteUrlRaw, '/');

  $collectionSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'Blog Pusat Tali Lanyard — Tips, Insight & Update',
    'description' => 'Artikel terbaru seputar lanyard custom, tips branding, tren event, dan panduan cetak untuk kebutuhan profesional.',
    'url' => $siteUrl . '/blog',
    'inLanguage' => 'id-ID',
    'isPartOf' => ['@id' => $siteUrl . '/#website'],
    'publisher' => ['@id' => $siteUrl . '/#organization'],
  ];

  if (isset($posts) && count($posts) > 0) {
    $itemList = [];
    $pos = 1;
    foreach ($posts as $p) {
      // guard
      $slug = isset($p->slug) ? $p->slug : '';
      $title = isset($p->title) ? $p->title : '';
      if (empty($slug) || empty($title)) continue;
      $itemList[] = [
        '@type' => 'ListItem',
        'position' => $pos,
        'url' => $siteUrl . '/blog/' . $slug,
        'name' => $title,
      ];
      $pos++;
      if ($pos > 10) break;
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
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $siteUrl . '/blog'],
    ],
  ];
@endphp
<script type="application/ld+json">{!! json_encode($collectionSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
