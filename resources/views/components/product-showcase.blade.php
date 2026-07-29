@php
  use App\Models\Product;

  $products = Cache::store(config('cache.public_store', 'file'))->remember(
    'home_products',
    3600,
    fn () => Product::with('category')
      ->where('published', true)
      ->orderBy('sortOrder')
      ->take(8)
      ->get()
  );

  if ($products->isEmpty()) {
    $products = collect([
      (object) ['name' => 'Lanyard Tissue Premium', 'slug' => 'lanyard-tissue', 'shortDescription' => 'Permukaan halus dengan hasil cetak warna yang tajam.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => 'Mulai Rp 5.000'],
      (object) ['name' => 'Lanyard Polyester Standar', 'slug' => 'lanyard-polyester', 'shortDescription' => 'Pilihan ekonomis dan tahan lama untuk kebutuhan event.', 'featuredImage' => '/uploads/hero-lanyard-slider-02.webp', 'basePrice' => 'Mulai Rp 3.500'],
      (object) ['name' => 'Lanyard Nylon Premium', 'slug' => 'lanyard-nylon', 'shortDescription' => 'Material kuat dengan tampilan rapi untuk pemakaian rutin.', 'featuredImage' => '/uploads/aset-produk.webp', 'basePrice' => 'Mulai Rp 7.500'],
      (object) ['name' => 'ID Card Holder', 'slug' => 'id-card-holder', 'shortDescription' => 'Pelindung kartu identitas untuk kantor dan kegiatan.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => 'Mulai Rp 15.000'],
      (object) ['name' => 'Hook Premium', 'slug' => 'hook-premium', 'shortDescription' => 'Pilihan kait kuat untuk melengkapi lanyard custom.', 'featuredImage' => '/uploads/hero-lanyard-slider-02.webp', 'basePrice' => null],
      (object) ['name' => 'Aksesoris Lanyard', 'slug' => 'aksesoris', 'shortDescription' => 'Stopper, hook, dan aksesori sesuai kebutuhan penggunaan.', 'featuredImage' => '/uploads/aset-produk.webp', 'basePrice' => null],
      (object) ['name' => 'Lanyard Double Kait', 'slug' => 'double-kait', 'shortDescription' => 'Dua kait untuk kartu atau perlengkapan yang lebih stabil.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => 'Mulai Rp 8.500'],
      (object) ['name' => 'Lanyard Custom Print', 'slug' => 'custom-print', 'shortDescription' => 'Cetak desain khusus dengan warna sesuai identitas brand.', 'featuredImage' => '/uploads/hero-lanyard-slider-02.webp', 'basePrice' => 'Mulai Rp 4.500'],
    ]);
  }

  $asset = fn ($source) => \App\Support\AssetUrl::relative($source) ?: '/uploads/hero-lanyard-slider-01.webp';
  $plain = fn ($value) => trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)));
@endphp

<section class="home-products" id="products" aria-labelledby="home-products-title">
  <div class="home-products__container">
    <header class="home-products__header">
      <div>
        <h2 id="home-products-title">Pilihan Produk Unggulan</h2>
        <p>Temukan pilihan lanyard dan aksesori untuk kebutuhan kantor, event, sekolah, maupun komunitas.</p>
      </div>
      <a class="home-products__all" href="/produk">
        <span>Lihat semua produk</span>
        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
      </a>
    </header>

    <ul class="home-products__grid" aria-label="Produk unggulan Pusat Tali Lanyard">
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
                  sizes="(min-width: 1024px) 292px, 46vw"
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
                <span class="material-symbols-outlined home-products__arrow" aria-hidden="true">arrow_forward</span>
              </div>
            </div>
          </a>
        </li>
      @endforeach
    </ul>
  </div>
</section>
