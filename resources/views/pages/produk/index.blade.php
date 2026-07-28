@extends('layouts.public')

@section('title', 'Produk Lanyard Custom Indonesia')
@section('meta_description', 'Temukan lanyard custom, ID card holder, dan aksesoris untuk kebutuhan perusahaan, sekolah, komunitas, dan event Anda.')
@section('seo_og_image', '/uploads/aset-produk.webp')

@php $produkHasSearch = !empty(request('q')) || (int) request('page', 1) > 1; @endphp
@if($produkHasSearch)
  @section('seo_noindex', '1')
@endif

@push('head')
  <link rel="stylesheet" href="/css/product-index.css?v={{ file_exists(public_path('css/product-index.css')) ? filemtime(public_path('css/product-index.css')) : 1 }}">
@endpush

@section('content')
@php
  use App\Models\Product;

  $search = trim((string) request('q', ''));
  $page = max(1, (int) request('page', 1));
  $perPage = 9;
  $query = Product::query()->with('category')->where('published', true);

  if ($search !== '') {
    $escapedSearch = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);
    $query->where(function ($builder) use ($escapedSearch) {
      $pattern = "%{$escapedSearch}%";
      $builder->whereRaw('name LIKE ? ESCAPE \'!\'', [$pattern])
        ->orWhereRaw('sku LIKE ? ESCAPE \'!\'', [$pattern])
        ->orWhereRaw('shortDescription LIKE ? ESCAPE \'!\'', [$pattern])
        ->orWhereRaw('specs LIKE ? ESCAPE \'!\'', [$pattern]);
    });
  }

  $query->orderBy('sortOrder')->orderByDesc('createdAt');
  $total = $query->count();
  $products = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
  $nextPage = ($page * $perPage) < $total ? $page + 1 : null;

  if ($products->isEmpty() && $search === '') {
    $products = collect([
      (object) ['name' => 'Lanyard Tissue Premium', 'slug' => 'lanyard-tissue', 'sku' => 'LT-001', 'shortDescription' => 'Permukaan halus dengan warna cetak yang tajam.', 'featuredImage' => '/uploads/aset-produk.webp', 'basePrice' => 'Mulai Rp 5.000', 'category' => (object) ['name' => 'Lanyard Tissue']],
      (object) ['name' => 'Lanyard Polyester Standar', 'slug' => 'lanyard-polyester', 'sku' => 'LP-001', 'shortDescription' => 'Pilihan ekonomis dan tahan lama untuk event.', 'featuredImage' => '/uploads/hero-lanyard-slider-02.webp', 'basePrice' => 'Mulai Rp 3.500', 'category' => (object) ['name' => 'Lanyard Polyester']],
      (object) ['name' => 'Lanyard Sablon Full Color', 'slug' => 'lanyard-sablon', 'sku' => 'LS-001', 'shortDescription' => 'Cetak penuh untuk desain brand yang kompleks.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => 'Mulai Rp 4.500', 'category' => (object) ['name' => 'Lanyard Sablon']],
      (object) ['name' => 'ID Card Holder Premium', 'slug' => 'id-card-holder', 'sku' => 'IC-001', 'shortDescription' => 'Holder rapi untuk kebutuhan kerja dan acara.', 'featuredImage' => '/uploads/aset-produk.webp', 'basePrice' => 'Mulai Rp 15.000', 'category' => (object) ['name' => 'ID Card']],
      (object) ['name' => 'Hook Metal Lanyard', 'slug' => 'hook-metal', 'sku' => 'AH-001', 'shortDescription' => 'Hook kuat dengan tampilan bersih dan profesional.', 'featuredImage' => '/uploads/hero-lanyard-slider-02.webp', 'basePrice' => null, 'category' => (object) ['name' => 'Aksesoris']],
      (object) ['name' => 'Stopper Silikon', 'slug' => 'stopper-silikon', 'sku' => 'AS-001', 'shortDescription' => 'Stopper fleksibel untuk detail lanyard custom.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => null, 'category' => (object) ['name' => 'Aksesoris']],
    ]);
    $total = $products->count();
    $nextPage = null;
  }

  $asset = fn ($value) => \App\Support\AssetUrl::relative($value, '/uploads/aset-produk.webp');
  $plain = fn ($value) => trim((string) preg_replace('/\s+/u', ' ', strip_tags((string) $value)));
@endphp

<div class="product-index">
  <section class="product-index__hero" aria-labelledby="product-index-title">
    <div class="product-index__hero-inner">
      <div class="product-index__intro">
        <p class="product-index__eyebrow">Katalog Produk</p>
        <h1 id="product-index-title">Lanyard Custom Sesuai Kebutuhan</h1>
        <p>Pilih bahan, ukuran, dan aksesoris yang sesuai untuk perusahaan, sekolah, komunitas, atau event.</p>

        <form class="product-index__search" method="GET" action="{{ route('products.index') }}" role="search">
          <label for="product-search">Cari produk</label>
          <div class="product-index__search-row">
            <div class="product-index__input-wrap">
              <input id="product-search" type="search" name="q" value="{{ $search }}" placeholder="Nama produk, SKU, atau spesifikasi" autocomplete="off">
            </div>
            <button type="submit" data-button-motion><span>Cari Produk</span></button>
          </div>
          @if($search !== '')
            <a class="product-index__clear" href="{{ route('products.index') }}">Hapus pencarian</a>
          @endif
        </form>
      </div>

      <div class="product-index__hero-visual" aria-hidden="true">
        <div class="product-index__visual-frame">
          <img src="/uploads/hero-katalog-pusat-tali-lanyard.webp" alt="" width="1254" height="1254" decoding="async" fetchpriority="high">
        </div>
        <p>Lanyard custom<br><strong>untuk identitas yang lebih profesional.</strong></p>
      </div>
    </div>
  </section>

  <section class="product-index__catalog" aria-labelledby="product-catalog-title">
    <div class="product-index__container">
      <header class="product-index__catalog-header">
        <div>
          <h2 id="product-catalog-title">Katalog Lanyard dan Aksesoris</h2>
          <p>
            @if($search !== '')
              {{ $total }} hasil untuk “{{ $search }}”
            @else
              {{ $total }} pilihan produk siap disesuaikan dengan desain Anda.
            @endif
          </p>
        </div>
        <a href="{{ route('pricing') }}">Lihat daftar harga <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
      </header>

      @if($products->isEmpty())
        <div class="product-index__empty">
          <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
          <h2>Produk belum ditemukan</h2>
          <p>Coba gunakan nama produk, SKU, atau spesifikasi yang lebih singkat.</p>
          <a href="{{ route('products.index') }}" data-button-motion><span>Lihat Semua Produk</span></a>
        </div>
      @else
        <div class="product-index__grid" role="list" aria-label="Daftar produk Pusat Tali Lanyard">
          @foreach($products as $product)
            @php
              $image = $asset($product->featuredImage ?? null);
              $srcset = \App\Support\ResponsiveImage::srcset($image);
              $description = $plain($product->shortDescription ?? '');
            @endphp
            <article class="product-index__card" role="listitem">
              <a href="{{ route('products.show', ['slug' => $product->slug]) }}" aria-label="Lihat detail {{ $product->name }}">
                <div class="product-index__image">
                  <img src="{{ $image }}" @if($srcset) srcset="{{ $srcset }}" sizes="(max-width: 767px) 50vw, (max-width: 1023px) 50vw, 33vw" @endif alt="{{ $product->name }}" width="640" height="640" loading="lazy" decoding="async">
                </div>
                <div class="product-index__card-body">
                  <div class="product-index__meta">
                    <span>{{ $product->category->name ?? 'Lanyard Custom' }}</span>
                    @if(!empty($product->sku))<span>{{ $product->sku }}</span>@endif
                  </div>
                  <h3>{{ $product->name }}</h3>
                  <p>{{ $description ?: 'Produk custom berkualitas untuk kebutuhan identitas dan promosi Anda.' }}</p>
                  <div class="product-index__card-footer">
                    <strong>{{ !empty($product->basePrice) ? $product->basePrice : 'Minta quotation' }}</strong>
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                  </div>
                </div>
              </a>
            </article>
          @endforeach
        </div>

        @if($nextPage)
          <div class="product-index__more">
            <a href="?{{ http_build_query(['q' => $search ?: null, 'page' => $nextPage]) }}" data-button-motion><span>Muat Produk Lainnya</span></a>
          </div>
        @endif
      @endif
    </div>
  </section>
</div>

@include('components.footer')

@push('schema')
  @include('components.schema.produk')
@endpush
@endsection
