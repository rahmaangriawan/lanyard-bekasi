@extends('layouts.public')

@php
  $page = $posts->currentPage();
  $total = $posts->total();
  $latestPost = $posts->first();
  $defaultDescription = 'Artikel seputar produksi lanyard, desain, branding, material, dan kebutuhan event untuk membantu Anda memilih produk yang tepat.';
  $ogImage = $latestPost?->featuredImage ?: '/uploads/lanyard-bekasi-logo.webp';
  $archiveCssPath = public_path('css/blog-archive.css');
  $archiveCssVersion = file_exists($archiveCssPath) ? filemtime($archiveCssPath) : 1;
@endphp

@section('title', 'Blog dan Wawasan Lanyard | Lanyard Bekasi')
@section('meta_description', $defaultDescription)
@section('seo_canonical', route('blog.index'))
@section('seo_og_image', $ogImage)
@if($search !== '' || $page > 1)
  @section('seo_noindex', '1')
@endif

@push('head')
  <link rel="stylesheet" href="/css/blog-archive.css?v={{ $archiveCssVersion }}">
@endpush
@push('schema')
  @include('components.schema.blog-index', ['posts' => $posts])
@endpush

@section('content')
<div class="blog-archive-page">
  <section class="blog-archive-hero" aria-labelledby="blog-page-title">
    <div class="blog-archive-shell blog-archive-hero__layout">
      <div class="blog-archive-hero__copy">
        <p class="blog-archive-eyebrow">Blog &amp; Wawasan</p>
        <h1 id="blog-page-title">Panduan Praktis Seputar Lanyard Custom</h1>
        <p>Pelajari bahan, aksesori, desain, dan proses produksi sebelum menentukan lanyard yang tepat.</p>
      </div>

      <div class="blog-archive-search-panel">
        <form class="blog-archive-search" role="search" method="GET" action="{{ route('blog.index') }}">
          <label for="blog-search-input">Cari dari {{ $total }} artikel</label>
          <div class="blog-archive-search__control">
            <input id="blog-search-input" type="search" name="q" value="{{ $search }}" maxlength="255" placeholder="Cari bahan, aksesori, atau desain" autocomplete="off">
            <button type="submit">Cari</button>
          </div>
        </form>
        @if($search !== '')
          <div class="blog-archive-search__status">
            <p>{{ $total }} hasil untuk “{{ $search }}”</p>
            <a href="{{ route('blog.index') }}">Hapus pencarian</a>
          </div>
        @else
          <p class="blog-archive-search__hint">Arsip diperbarui berdasarkan artikel terbaru yang diterbitkan.</p>
        @endif
      </div>
    </div>
  </section>

  <section class="blog-archive-section" aria-labelledby="blog-listing-title">
    <div class="blog-archive-shell">
      <header class="blog-archive-heading">
        <div>
          <h2 id="blog-listing-title">{{ $search !== '' ? 'Hasil Pencarian' : 'Artikel Terbaru' }}</h2>
          <p>{{ $search !== '' ? 'Artikel yang sesuai dengan kata kunci Anda.' : 'Wawasan terbaru dari tim Lanyard Bekasi.' }}</p>
        </div>
        <p class="blog-archive-heading__count">{{ $total }} artikel</p>
      </header>

      <x-blog.archive-list
        :posts="$posts"
        empty-title="Artikel tidak ditemukan"
        empty-text="Coba gunakan kata kunci yang lebih umum atau kembali ke seluruh arsip blog."
      />
    </div>
  </section>

  <x-blog.archive-cta />
</div>

@include('components.footer')
@endsection
