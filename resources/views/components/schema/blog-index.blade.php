@php
  $siteUrlRaw = config('app.url');
  if (empty($siteUrlRaw)) { $siteUrlRaw = url('/'); }
  $siteUrl = rtrim($siteUrlRaw, '/');

  $collectionSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'Blog & Wawasan Pusat Tali Lanyard',
    'description' => 'Artikel seputar produksi lanyard, desain, branding, material, dan kebutuhan event untuk membantu pelanggan memilih produk yang tepat.',
    'url' => $siteUrl . '/blog',
    'inLanguage' => 'id-ID',
    'isPartOf' => ['@id' => $siteUrl . '/#website'],
    'publisher' => ['@id' => $siteUrl . '/#organization'],
  ];

  if (isset($posts) && count($posts) > 0) {
    $itemList = [];
    $position = 1;

    foreach ($posts as $post) {
      $slug = isset($post->slug) ? $post->slug : '';
      $title = isset($post->title) ? $post->title : '';
      if (empty($slug) || empty($title)) continue;

      $itemList[] = [
        '@type' => 'ListItem',
        'position' => $position,
        'url' => $siteUrl . '/blog/' . $slug,
        'name' => $title,
      ];
      $position++;
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
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $siteUrl . '/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $siteUrl . '/blog'],
    ],
  ];
@endphp
<script type="application/ld+json">{!! json_encode($collectionSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
