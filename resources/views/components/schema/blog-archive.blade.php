@props([
  'name',
  'description',
  'canonicalPath',
  'breadcrumbName',
  'posts',
])

@php
  $siteUrl = rtrim((string) config('app.url', 'https://lanyardbekasi.com'), '/');
  if ($siteUrl === '' || str_contains($siteUrl, 'localhost') || str_contains($siteUrl, '127.0.0.1')) {
    $siteUrl = 'https://lanyardbekasi.com';
  }

  $canonicalUrl = $siteUrl.'/'.ltrim($canonicalPath, '/');
  $firstPosition = method_exists($posts, 'firstItem') && $posts->firstItem() ? $posts->firstItem() : 1;
  $items = [];

  foreach ($posts as $index => $post) {
    $items[] = [
      '@type' => 'ListItem',
      'position' => $firstPosition + $index,
      'url' => $siteUrl.'/blog/'.$post->slug,
      'name' => $post->title,
    ];
  }

  $collection = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $name,
    'description' => $description,
    'url' => $canonicalUrl,
    'inLanguage' => 'id-ID',
    'isPartOf' => ['@id' => $siteUrl.'/#website'],
    'publisher' => ['@id' => $siteUrl.'/#organization'],
  ];

  if ($items !== []) {
    $collection['mainEntity'] = [
      '@type' => 'ItemList',
      'numberOfItems' => count($items),
      'itemListElement' => $items,
    ];
  }

  $breadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $siteUrl.'/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $siteUrl.'/blog'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => $breadcrumbName, 'item' => $canonicalUrl],
    ],
  ];
@endphp

<script type="application/ld+json">{!! \App\Support\StructuredData::encode($collection) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($breadcrumb) !!}</script>
