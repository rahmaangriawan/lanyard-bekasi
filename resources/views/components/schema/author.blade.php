@php
  $siteUrlRaw = config('app.url');
  if (empty($siteUrlRaw)) { $siteUrlRaw = url('/'); }
  $siteUrl = rtrim($siteUrlRaw, '/');
  // Force prod domain if localhost / 127 / local
  if (preg_match('/^(https?:\/\/)?(localhost|127\.0\.0\.1)/i', $siteUrl) || str_contains($siteUrl, '.local') || str_contains($siteUrl, ':800')) {
    $siteUrl = rtrim(config('app.prod_url', 'https://pusattalilanyard.com'), '/');
    if (empty($siteUrl) || $siteUrl === $siteUrlRaw) {
      $siteUrl = 'https://pusattalilanyard.com';
    }
  }

  // Author details derived from $author or fallback
  $authorName = isset($displayName) ? $displayName : 'Rama Angriawan';
  $authorSlugName = isset($authorSlug) ? $authorSlug : \Illuminate\Support\Str::slug($authorName);
  $canonicalUrl = $siteUrl . '/blog/penulis/' . $authorSlugName;
  $bioDesc = isset($bioText) ? $bioText : 'Penulis & spesialis lanyard custom di Indonesia. Berbagi tips desain, branding, event, dan panduan produksi lanyard berkualitas.';
  
  $authorAvatar = '';
  if (isset($author) && isset($author->avatar) && !empty($author->avatar)) {
    $authorAvatarRaw = $author->avatar;
    if (strpos($authorAvatarRaw, 'http') !== 0) {
      $authorAvatar = preg_replace('/^https?:\/\/[^\/]+/', $siteUrl, $authorAvatarRaw);
      if (strpos($authorAvatar, 'http') !== 0) {
        $authorAvatar = $siteUrl . '/' . ltrim($authorAvatarRaw, '/');
      }
    } else {
      $authorAvatar = preg_replace('/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?/', $siteUrl, $authorAvatarRaw);
    }
  } else {
    $authorAvatar = $siteUrl . '/uploads/pusat-tali-lanyard-logo.webp';
  }

  // Schema generation
  $personSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'ProfilePage',
    'mainEntity' => [
      '@type' => 'Person',
      '@id' => $canonicalUrl . '#person',
      'name' => $authorName,
      'url' => $canonicalUrl,
      'image' => $authorAvatar,
      'description' => $bioDesc,
      'worksFor' => [
        '@id' => $siteUrl . '/#organization'
      ],
      'sameAs' => [
        'https://id.linkedin.com/in/rama-angriawan-2460a0334',
        'https://www.facebook.com/razorcallahan424/',
        'https://www.instagram.com/rama_angr/',
        'https://github.com/rama-angriawan/'
      ]
    ]
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => 'Home',
        'item' => $siteUrl . '/'
      ],
      [
        '@type' => 'ListItem',
        'position' => 2,
        'name' => 'Blog',
        'item' => $siteUrl . '/blog'
      ],
      [
        '@type' => 'ListItem',
        'position' => 3,
        'name' => $authorName,
        'item' => $canonicalUrl
      ]
    ]
  ];

  $postsList = [];
  if (isset($posts) && $posts->count() > 0) {
    foreach ($posts as $index => $post) {
      $postUrl = $siteUrl . '/blog/' . $post->slug;
      $postsList[] = [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'url' => $postUrl
      ];
    }
  }

  if (!empty($postsList)) {
    $itemListSchema = [
      '@context' => 'https://schema.org',
      '@type' => 'ItemList',
      'itemListElement' => $postsList
    ];
  }
@endphp

<script type="application/ld+json">{!! json_encode($personSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@if(!empty($postsList))
<script type="application/ld+json">{!! json_encode($itemListSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endif
