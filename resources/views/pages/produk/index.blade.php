@extends('layouts.public')

@section('title', 'Produk Lanyard Custom Indonesia')
@section('meta_description', 'Temukan model lanyard berdasarkan nama, SKU, spesifikasi, atau kebutuhan aksesoris. Pilihan lengkap lanyard custom premium di Indonesia dengan harga terjangkau.')
@section('seo_og_image', '/uploads/hero-lanyard-slider-01.webp')
@php $produkHasSearch = !empty(request('q')) || (int)request('page', 1) > 1; @endphp
@if($produkHasSearch)
@section('seo_noindex', '1')
@endif

@section('content')
@php
  use App\Models\Product;

  $search = request('q');
  $page = max(1, (int) request('page', 1));
  $perPage = 6;

  $query = Product::with('category')->where('published', true);
  if ($search) {
    $query->where(function ($q) use ($search) {
      $q->where('name', 'like', "%{$search}%")
        ->orWhere('sku', 'like', "%{$search}%")
        ->orWhere('shortDescription', 'like', "%{$search}%")
        ->orWhere('specs', 'like', "%{$search}%");
    });
  }
  $query->orderBy('sortOrder')->orderBy('createdAt', 'desc');
  $total = $query->count();
  $products = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
  $hasMore = ($page * $perPage) < $total;
  $nextPage = $hasMore ? $page + 1 : null;

  // Fallback dummy products if empty
  if ($products->isEmpty() && !$search) {
    $products = collect([
      (object) ['name' => 'Lanyard Tissue Premium', 'slug' => 'lanyard-tissue', 'sku' => 'LT-001', 'shortDescription' => 'Permukaan halus, warna tajam permanen.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => 'Mulai Rp 5.000', 'category' => (object) ['name' => 'Tissue']],
      (object) ['name' => 'Lanyard Polyester Standar', 'slug' => 'lanyard-polyester', 'sku' => 'LP-001', 'shortDescription' => 'Ekonomis dan tahan lama untuk event besar.', 'featuredImage' => '/uploads/hero-lanyard-slider-02.webp', 'basePrice' => 'Mulai Rp 3.500', 'category' => (object) ['name' => 'Polyester']],
      (object) ['name' => 'Aksesoris Hook Plastik', 'slug' => 'hook-plastik', 'sku' => 'AH-001', 'shortDescription' => 'Hook plastik berkualitas tinggi.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => null, 'category' => (object) ['name' => 'Aksesoris']],
      (object) ['name' => 'ID Card Holder Kulit', 'slug' => 'id-card-kulit', 'sku' => 'IC-001', 'shortDescription' => 'Casing ID Card kulit premium.', 'featuredImage' => '/uploads/hero-lanyard-slider-02.webp', 'basePrice' => 'Mulai Rp 15.000', 'category' => (object) ['name' => 'Holder']],
      (object) ['name' => 'Lanyard Sablon Full Color', 'slug' => 'lanyard-sablon', 'sku' => 'LS-001', 'shortDescription' => 'Cetak sablon full color untuk desain kompleks.', 'featuredImage' => '/uploads/hero-lanyard-slider-01.webp', 'basePrice' => 'Mulai Rp 4.500', 'category' => (object) ['name' => 'Sablon']],
      (object) ['name' => 'Aksesoris Stopper Silikon', 'slug' => 'stopper-silikon', 'sku' => 'AS-001', 'shortDescription' => 'Stopper silikon elastis dan awet.', 'featuredImage' => '/uploads/hero-lanyard-slider-02.webp', 'basePrice' => null, 'category' => (object) ['name' => 'Aksesoris']],
    ]);
    $total = $products->count();
    $hasMore = false;
  }

  // Helper: resolve image URL
  $img = fn ($src) => \App\Support\AssetUrl::relative($src);

  // Helper: strip HTML for plain text excerpt
  $plain = fn($v) => trim(str_replace('  ', ' ', strip_tags((string) ($v ?? ''))));
@endphp

{{-- Hero mini --}}
<section class="bg-white py-16 sm:py-20 border-b border-slate-200">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <p class="text-xs font-extrabold uppercase tracking-normal text-slate-600">Produk</p>
    <h1 class="mt-4 font-bold leading-[1.08] text-slate-900" style="font-size: clamp(2rem, 4vw, 2.75rem);">Pilihan Lanyard Custom</h1>
    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">Temukan model lanyard berdasarkan nama, SKU, spesifikasi, atau kebutuhan aksesoris.</p>

    {{-- Search form --}}
    <form class="mt-8 flex max-w-2xl gap-3" method="GET" action="/produk">
      <input
        class="min-h-12 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-600 focus:ring-2 focus:ring-slate-200"
        type="search"
        name="q"
        value="{{ $search }}"
        placeholder="Cari nama produk, SKU, atau spesifikasi..."
        autocomplete="off"
      />
      <button class="group relative overflow-hidden min-h-12 shrink-0 rounded-xl bg-slate-800 px-5 text-sm font-bold text-white transition-all hover:bg-slate-900 active:scale-95 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-white/20 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px]" type="submit"><span class="hero-btn-text relative z-10 inline-block">Cari</span></button>
      @if ($search)
      <a href="/produk" class="group relative overflow-hidden min-h-12 shrink-0 flex items-center rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-600 transition-all before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-slate-800 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px] active:scale-[0.98] hover:text-white hover:bg-slate-800 hover:border-slate-800"><span class="hero-btn-text relative z-10 inline-block">Hapus</span></a>
      @endif
    </form>

    <p class="mt-4 text-sm text-slate-500">
      @if($search)
        @if($total > 0)
        Menampilkan {{ $products->count() }} hasil untuk "<strong>{{ $search }}</strong>"
        @else
        Tidak ada produk yang cocok untuk "<strong>{{ $search }}</strong>"
        @endif
      @else
        {{ $total > 0 ? "Menampilkan {$products->count()} dari {$total} produk" : 'Belum ada produk tersedia' }}
      @endif
    </p>
  </div>
</section>

{{-- Product Grid --}}
<section class="bg-slate-50 py-12 sm:py-16">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    @if ($products->isEmpty())
      <div class="text-center py-20">
        <span class="material-symbols-outlined text-6xl text-slate-300">inventory_2</span>
        <h2 class="mt-6 text-xl font-bold text-slate-900">Produk tidak ditemukan</h2>
        <p class="mt-2 text-sm text-slate-600">Coba gunakan nama, SKU, atau kata kunci yang berbeda.</p>
      </div>
    @else
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
      @foreach ($products as $product)
      <a href="/produk/{{ $product->slug }}" class="group flex min-h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:border-slate-400 hover:shadow-md">
        {{-- Image --}}
        <div class="overflow-hidden bg-slate-100">
          @if($product->featuredImage)
          <img class="aspect-[16/10] w-full object-cover transition duration-300 group-hover:scale-[1.02]" src="{{ $img($product->featuredImage) }}" alt="{{ $product->name }} - Lanyard custom Indonesia premium" loading="lazy" decoding="async" width="360" height="225" />
          @else
          <div class="aspect-[16/10] w-full bg-slate-200 flex items-center justify-center">
            <span class="material-symbols-outlined text-4xl text-slate-400">image</span>
          </div>
          @endif
        </div>
        {{-- Body --}}
        <div class="flex flex-1 flex-col p-5">
          <div class="flex items-center justify-between gap-3 text-xs font-bold text-slate-600">
            <span>{{ $product->category->name ?? 'Lanyard Custom' }}</span>
            @if($product->sku)
            <span>{{ $product->sku }}</span>
            @endif
          </div>
          <h2 class="mt-3 text-xl font-bold leading-snug text-slate-900 group-hover:text-slate-700 transition-colors">{{ $product->name }}</h2>
          <p class="mt-3 text-sm leading-6 text-slate-600 line-clamp-3">{{ $plain($product->shortDescription) ?: 'Lanyard custom berkualitas untuk kebutuhan brand dan kegiatan Anda.' }}</p>
          @if($product->basePrice)
          <p class="mt-auto pt-5 text-sm font-bold text-slate-800">{{ $product->basePrice }}</p>
          @endif
        </div>
      </a>
      @endforeach
    </div>

    {{-- Load More --}}
    @if ($hasMore)
    <div class="mt-12 text-center">
      <a
        href="?{{ http_build_query(['q' => $search, 'page' => $nextPage]) }}"
        class="group relative overflow-hidden inline-flex min-h-12 items-center rounded-xl border border-slate-800 px-6 text-sm font-bold text-slate-800 transition-all hover:bg-slate-800 hover:text-white active:scale-95 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-slate-800 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px]"
      >
        <span class="hero-btn-text relative z-10 inline-block">Muat lebih banyak</span>
      </a>
    </div>
    @endif
    @endif
  </div>
</section>

{{-- CTA + Footer --}}
@include('components.cta')
@include('components.footer')
@push('schema')
@include('components.schema.produk')
@endpush
@endsection
