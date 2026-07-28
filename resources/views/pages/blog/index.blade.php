@extends('layouts.public')

@php
  use App\Models\Post;
  use App\Support\AssetUrl;
  use App\Support\ResponsiveImage;

  $search = mb_substr(trim((string) request('q', '')), 0, 255);
  $escapedSearch = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);
  $page = max(1, (int) request('page', 1));
  $perPage = 9;
  $query = Post::with('category', 'author')->where('published', true);

  if ($search !== '') {
    $query->where(function ($q) use ($escapedSearch) {
      $term = "%{$escapedSearch}%";
      $q->whereRaw("title LIKE ? ESCAPE '!'", [$term])
        ->orWhereRaw("metaDescription LIKE ? ESCAPE '!'", [$term])
        ->orWhereRaw("content LIKE ? ESCAPE '!'", [$term]);
    });
  }

  $query->orderBy('createdAt', 'desc');
  $total = $query->count();
  $posts = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
  $hasPrevious = $page > 1;
  $hasNext = ($page * $perPage) < $total;
  $latestPost = $posts->first();
  $gridPosts = $posts;
  $imageUrl = fn ($source) => AssetUrl::relative($source);
  $readingTime = static function ($post): int {
    $text = trim(strip_tags((string) ($post->content ?? '')));
    $words = $text === '' ? 0 : count(preg_split('/\s+/', $text));
    return max(1, (int) ceil($words / 200));
  };
  $defaultDescription = 'Artikel seputar produksi lanyard, desain, branding, material, dan kebutuhan event untuk membantu Anda memilih produk yang tepat.';
  $ogImage = $latestPost?->featuredImage ?: '/uploads/pusat-tali-lanyard-logo.webp';
  $blogCssPath = public_path('css/blog-index.css');
  $blogCssVersion = file_exists($blogCssPath) ? filemtime($blogCssPath) : 1;
@endphp

@section('title', 'Blog dan Wawasan Lanyard | Pusat Tali Lanyard')
@section('meta_description', $defaultDescription)
@section('seo_canonical', route('blog.index'))
@section('seo_og_image', $ogImage)
@if($search !== '' || $page > 1)
  @section('seo_noindex', '1')
@endif

@push('head')
  <link rel="stylesheet" href="/css/blog-index.css?v={{ $blogCssVersion }}">
@endpush
@push('schema')
  @include('components.schema.blog-index', ['posts' => $posts])
@endpush

@section('content')
<section class="blog-hero" aria-labelledby="blog-page-title">
  <div class="blog-container blog-hero__layout">
    <div class="blog-hero__copy">
      <p class="blog-eyebrow">Blog &amp; Wawasan</p>
      <h1 id="blog-page-title">Artikel &amp; Insight Lanyard untuk Bisnis dan Event</h1>
      <p>Temukan panduan produksi, desain, branding, material, dan kebutuhan event untuk membantu Anda memilih lanyard yang tepat.</p>
    </div>
    <div class="blog-search-panel">
      <p class="blog-search-panel__label">Cari topik yang Anda butuhkan</p>
      <form class="blog-search" role="search" method="GET" action="{{ route('blog.index') }}">
        <label class="sr-only" for="blog-search-input">Cari artikel</label>
        <input id="blog-search-input" type="search" name="q" value="{{ $search }}" maxlength="255" placeholder="Contoh: bahan lanyard" autocomplete="off">
        <button type="submit" data-button-motion><span>Cari Artikel</span></button>
      </form>
      @if($search !== '')
        <div class="blog-search-panel__status">
          <p>{{ $total }} hasil untuk “{{ $search }}”</p>
          <a href="{{ route('blog.index') }}">Hapus pencarian</a>
        </div>
      @else
        <p class="blog-search-panel__status">{{ $total }} artikel untuk menambah wawasan Anda.</p>
      @endif
    </div>
  </div>
</section>

<section class="blog-listing" aria-labelledby="blog-listing-title">
  <div class="blog-container">
    @if($posts->isEmpty())
      <div class="blog-empty">
        <span class="material-symbols-outlined" aria-hidden="true">article</span>
        <h2 id="blog-listing-title">Artikel tidak ditemukan</h2>
        <p>Coba gunakan kata kunci yang lebih umum atau lihat kembali seluruh artikel kami.</p>
        <a href="{{ route('blog.index') }}" data-button-motion><span>Lihat Semua Artikel</span></a>
      </div>
    @else
      <div class="blog-section-heading{{ $search !== '' ? ' blog-section-heading--search' : '' }}">
        <p class="blog-eyebrow">{{ $search !== '' ? 'Hasil Pencarian' : 'Wawasan Terbaru' }}</p>
        <h2 id="blog-listing-title">{{ $search !== '' ? 'Artikel yang Ditemukan' : 'Artikel Terbaru' }}</h2>
      </div>

      @if($gridPosts->isNotEmpty())
        <div class="blog-grid">
          @foreach($gridPosts as $post)
            @php
              $postImage = $imageUrl($post->featuredImage);
              $postSrcset = ResponsiveImage::srcset($postImage);
            @endphp
            <article class="blog-card">
              <a class="blog-card__media" href="/blog/{{ $post->slug }}" aria-label="Baca {{ $post->title }}">
                @if($postImage)
                  <img src="{{ $postImage }}" @if($postSrcset) srcset="{{ $postSrcset }}" sizes="(min-width: 1024px) 400px, (min-width: 700px) 50vw, 100vw" @endif alt="{{ $post->title }}" width="640" height="400" loading="lazy" decoding="async">
                @else
                  <span class="blog-image-fallback material-symbols-outlined" aria-hidden="true">article</span>
                @endif
              </a>
              <div class="blog-card__content">
                <p class="blog-category">{{ $post->category->name ?? 'Wawasan Lanyard' }}</p>
                <h3><a href="/blog/{{ $post->slug }}">{{ $post->title }}</a></h3>
                <p class="blog-excerpt">{{ $post->metaDescription ?: 'Baca insight terbaru dari Pusat Tali Lanyard.' }}</p>
                <div class="blog-card__footer">
                  <div class="blog-meta">
                    <time datetime="{{ \Carbon\Carbon::parse($post->createdAt)->toDateString() }}">{{ \Carbon\Carbon::parse($post->createdAt)->translatedFormat('j M Y') }}</time><span aria-hidden="true">•</span><span>{{ $readingTime($post) }} menit</span>
                  </div>
                  <a class="blog-card__arrow" href="/blog/{{ $post->slug }}" aria-label="Baca artikel {{ $post->title }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      @endif

      @if($hasPrevious || $hasNext)
        <nav class="blog-pagination" aria-label="Navigasi halaman artikel">
          @if($hasPrevious)
            <a href="?{{ http_build_query(['q' => $search, 'page' => $page - 1]) }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>Artikel sebelumnya</a>
          @else <span></span> @endif
          <span>Halaman {{ $page }}</span>
          @if($hasNext)
            <a href="?{{ http_build_query(['q' => $search, 'page' => $page + 1]) }}">Artikel berikutnya<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
          @else <span></span> @endif
        </nav>
      @endif
    @endif
  </div>
</section>

@include('components.footer')
@endsection
