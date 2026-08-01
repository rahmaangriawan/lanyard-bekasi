@php
  use App\Models\Product;

  $products = Cache::store(config('cache.public_store', 'file'))->remember(
    'home_products',
    3600,
    fn () => Product::with('category')
      ->where('published', true)
      ->orderBy('sortOrder')
      ->take(4)
      ->get()
  );

  if ($products->isEmpty()) {
    $products = collect([
      (object) ['name' => 'Lanyard Tissue Premium', 'slug' => 'lanyard-tissue', 'shortDescription' => 'Permukaan halus dengan hasil cetak warna yang tajam.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => 'Mulai Rp 5.000'],
      (object) ['name' => 'Lanyard Polyester Standar', 'slug' => 'lanyard-polyester', 'shortDescription' => 'Pilihan ekonomis dan tahan lama untuk kebutuhan event.', 'featuredImage' => '/uploads/hero-lanyard-slider-02.webp', 'basePrice' => 'Mulai Rp 3.500'],
      (object) ['name' => 'Lanyard Nylon Premium', 'slug' => 'lanyard-nylon', 'shortDescription' => 'Material kuat dengan tampilan rapi untuk pemakaian rutin.', 'featuredImage' => '/uploads/aset-produk.webp', 'basePrice' => 'Mulai Rp 7.500'],
      (object) ['name' => 'ID Card Holder', 'slug' => 'id-card-holder', 'shortDescription' => 'Pelindung kartu identitas untuk kantor dan kegiatan.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => 'Mulai Rp 15.000'],
    ]);
  }

  $asset = fn ($source) => \App\Support\AssetUrl::relative($source) ?: '/uploads/hero-lanyard-slider-01.webp';
  $plain = fn ($value) => trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)));
@endphp

<section class="home-products" id="products" aria-labelledby="home-products-title">
  <div class="home-shell">
    <header class="home-section-heading">
      <p>Katalog Produk</p>
      <h2 id="home-products-title">Pilihan Lanyard Custom Favorit</h2>
    </header>

    <ul class="home-products__grid" aria-label="Produk unggulan Lanyard Bekasi">
      @foreach ($products as $product)
        @php
          $image = $asset($product->featuredImage ?? null);
          $description = $plain($product->shortDescription ?? '');
        @endphp
        <li class="home-products__card">
          <a href="/produk/{{ $product->slug }}" aria-label="Lihat detail {{ $product->name }}">
            <div class="home-products__image-wrap">
              <img
                src="{{ $image }}"
                @if ($srcset = \App\Support\ResponsiveImage::srcset($image))
                  srcset="{{ $srcset }}"
                  sizes="(min-width: 1024px) 280px, (min-width: 640px) 46vw, 92vw"
                @endif
                alt="{{ $product->name }}"
                width="400"
                height="500"
                loading="lazy"
                decoding="async"
              />
            </div>
            <div class="home-products__body">
              <h3>{{ $product->name }}</h3>
              @if ($description !== '')
                <p>{{ $description }}</p>
              @endif
              <div class="home-products__footer">
                @if (!empty($product->basePrice))
                  <strong>{{ $product->basePrice }}</strong>
                @else
                  <span>Lihat detail</span>
                @endif
                <span>Detail <span class="material-symbols-outlined home-products__arrow" aria-hidden="true">arrow_forward</span></span>
              </div>
            </div>
          </a>
        </li>
      @endforeach
    </ul>

    <a class="home-products__all" href="{{ route('products.index') }}">
      <span>Lihat Semua Produk</span>
      <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
    </a>
  </div>
</section>
