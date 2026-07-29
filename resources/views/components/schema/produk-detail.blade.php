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

  $prodUrl = $siteUrl . '/produk/' . ($product->slug ?? '');
  $prodName = $product->name ?? 'Produk Lanyard';
  $prodDesc = strip_tags($product->shortDescription ?? $product->description ?? 'Lanyard custom premium Indonesia.');
  
  $featImg = '';
  if (!empty($product->featuredImage)) {
    $featImgRaw = $product->featuredImage;
    if (strpos($featImgRaw, 'http') !== 0) {
      $featImg = preg_replace('/^https?:\/\/[^\/]+/', $siteUrl, $featImgRaw);
      if (strpos($featImg, 'http') !== 0) {
        $featImg = $siteUrl . '/' . ltrim($featImgRaw, '/');
      }
    } else {
      $featImg = preg_replace('/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?/', $siteUrl, $featImgRaw);
    }
  } else {
    $featImg = $siteUrl . '/uploads/pusat-tali-lanyard-logo.webp';
  }

  $productSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $prodName,
    'description' => $prodDesc,
    'image' => [$featImg],
    'url' => $prodUrl,
  ];

  if (!empty($product->sku)) {
    $productSchema['sku'] = $product->sku;
  }
  if (!empty($product->category->name)) {
    $productSchema['category'] = $product->category->name;
  }

  if (!empty($product->basePrice)) {
    // Extract price number if possible
    $priceStr = preg_replace('/[^0-9]/', '', $product->basePrice);
    if (!empty($priceStr)) {
      $productSchema['offers'] = [
        '@type' => 'Offer',
        'url' => $prodUrl,
        'priceCurrency' => 'IDR',
        'price' => $priceStr,
        'availability' => 'https://schema.org/InStock',
        'seller' => [
          '@type' => 'Organization',
          'name' => 'Pusat Tali Lanyard'
        ]
      ];
    }
  }

  $breadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $siteUrl . '/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Produk', 'item' => $siteUrl . '/produk'],
      ['@type' => 'ListItem', 'position' => 3, 'name' => $prodName, 'item' => $prodUrl],
    ],
  ];
@endphp
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($productSchema) !!}</script>
<script type="application/ld+json">{!! \App\Support\StructuredData::encode($breadcrumb) !!}</script>
