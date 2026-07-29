@props(['items' => []])
@php
  $siteUrlRaw = config('app.url');
  if (empty($siteUrlRaw)) { $siteUrlRaw = url('/'); }
  $siteUrl = rtrim($siteUrlRaw, '/');

  $list = [];
  $pos = 1;
  foreach ($items as $it) {
    $name = isset($it['name']) ? $it['name'] : '';
    $url = isset($it['url']) ? $it['url'] : '';
    if (empty($name)) continue;
    $entry = [
      '@type' => 'ListItem',
      'position' => $pos,
      'name' => $name,
    ];
    if (!empty($url)) {
      // ensure absolute
      if (strpos($url, 'http') !== 0) {
        $url = $siteUrl . '/' . ltrim($url, '/');
      }
      $entry['item'] = $url;
    }
    $list[] = $entry;
    $pos++;
  }
  $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $list,
  ];
@endphp
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($schema) !!}</script>
