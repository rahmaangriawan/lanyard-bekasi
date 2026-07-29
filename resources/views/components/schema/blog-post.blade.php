@php
  $siteUrlRaw = config('app.url');
  if (empty($siteUrlRaw)) { $siteUrlRaw = url('/'); }
  $siteUrl = rtrim($siteUrlRaw, '/');
  // Force prod domain if localhost / 127 / local
  if (preg_match('/^(https?:\/\/)?(localhost|127\.0\.0\.1)/i', $siteUrl) || str_contains($siteUrl, '.local') || str_contains($siteUrl, ':800')) {
    $siteUrl = 'https://pusattalilanyard.com';
  }

  $title = isset($post->title) ? $post->title : 'Artikel Pusat Tali Lanyard';
  $descRaw = isset($post->metaDescription) ? $post->metaDescription : '';
  if (empty($descRaw)) {
    $descRaw = strip_tags(isset($post->content) ? $post->content : '');
    if (function_exists('mb_substr')) {
      $descRaw = mb_substr($descRaw, 0, 160);
    } else {
      $descRaw = substr($descRaw, 0, 160);
    }
  }
  $desc = $descRaw;

  $canonicalUrl = $siteUrl . '/blog/' . (isset($post->slug) ? $post->slug : '');

  $featuredImageRaw = isset($post->featuredImage) ? $post->featuredImage : '';
  if (!empty($featuredImageRaw) && strpos($featuredImageRaw, 'http') !== 0) {
    $featuredImage = $siteUrl . '/' . ltrim($featuredImageRaw, '/');
    // replace old domain if present in path cleaning
    $featuredImage = preg_replace('/^https?:\/\/[^\/]+/', $siteUrl, $featuredImageRaw);
    if (strpos($featuredImage, 'http') !== 0) {
      $featuredImage = $siteUrl . '/' . ltrim($featuredImageRaw, '/');
    }
  } elseif (!empty($featuredImageRaw)) {
    $featuredImage = preg_replace('/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?/', $siteUrl, $featuredImageRaw);
  } else {
    $featuredImage = $siteUrl . '/uploads/pusat-tali-lanyard-logo.webp';
  }

  $authorName = isset($post->author) && !empty($post->author?->name)
    ? $post->author->name
    : 'Tim Pusat Tali Lanyard';
  $authorSlug = $post->author?->slug ?: \Illuminate\Support\Str::slug($authorName);

  $datePub = null;
  $dateMod = null;
  if (isset($post->createdAt) && $post->createdAt) {
    try { $datePub = \Carbon\Carbon::parse($post->createdAt)->toAtomString(); } catch (\Exception $e) { $datePub = null; }
  }
  if (isset($post->updatedAt) && $post->updatedAt) {
    try { $dateMod = \Carbon\Carbon::parse($post->updatedAt)->toAtomString(); } catch (\Exception $e) { $dateMod = null; }
  }
  if (empty($dateMod)) { $dateMod = $datePub; }

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $title,
    'description' => $desc,
    'url' => $canonicalUrl,
    'mainEntityOfPage' => [
      '@type' => 'WebPage',
      '@id' => $canonicalUrl,
    ],
    'image' => [$featuredImage],
    'author' => [
      '@type' => 'Person',
      'name' => $authorName,
      'url' => $siteUrl . '/blog/penulis/' . $authorSlug,
    ],
    'publisher' => [
      '@type' => 'Organization',
      '@id' => $siteUrl . '/#organization',
      'name' => 'Pusat Tali Lanyard',
      'logo' => [
        '@type' => 'ImageObject',
        'url' => $siteUrl . '/uploads/pusat-tali-lanyard-logo.webp',
      ],
    ],
    'inLanguage' => 'id-ID',
  ];
  $authorDescription = $post->author ? $post->author->bio : \App\Support\AuthorProfile::DEFAULT_BIO;
  if (!empty($authorDescription)) {
    $articleSchema['author']['description'] = $authorDescription;
  }
  if (!empty($post->author?->avatar)) {
    $authorAvatarRaw = \App\Support\AssetUrl::relative($post->author->avatar);
    $authorAvatar = str_starts_with((string) $authorAvatarRaw, 'http')
      ? preg_replace('/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?/', $siteUrl, $authorAvatarRaw)
      : $siteUrl . '/' . ltrim((string) $authorAvatarRaw, '/');
    $articleSchema['author']['image'] = $authorAvatar;
  }
  if ($datePub) $articleSchema['datePublished'] = $datePub;
  if ($dateMod) $articleSchema['dateModified'] = $dateMod;

  if (isset($post->category) && isset($post->category->name)) {
    $articleSchema['articleSection'] = $post->category->name;
  }

  $plainArticle = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($post->content ?? ''))));
  $articleSchema['wordCount'] = $plainArticle === '' ? 0 : count(preg_split('/\s+/u', $plainArticle));
  $articleFaqSchema = \App\Support\StructuredData::faqSchema(
    \App\Support\StructuredData::extractFaqs((string) ($post->content ?? ''))
  );

  $breadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $siteUrl . '/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $siteUrl . '/blog'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => $title, 'item' => $canonicalUrl],
    ],
  ];
@endphp
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($articleSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($breadcrumb) !!}</script>
@if($articleFaqSchema)
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($articleFaqSchema) !!}</script>
@endif
