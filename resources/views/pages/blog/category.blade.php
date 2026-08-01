@extends('layouts.public')

@php
  $description = 'Kumpulan artikel seputar '.strtolower($category->name).' dari Lanyard Bekasi untuk membantu Anda memahami produk dan proses produksi lanyard custom.';
  $ogImage = $posts->first()?->featuredImage ?: '/uploads/lanyard-bekasi-logo.webp';
  $archiveCssPath = public_path('css/blog-archive.css');
  $archiveCssVersion = file_exists($archiveCssPath) ? filemtime($archiveCssPath) : 1;
@endphp

@section('title', 'Kategori: '.$category->name.' | Blog Lanyard Bekasi')
@section('meta_description', $description)
@section('seo_canonical', route('blog.category', ['slug' => $category->slug]))
@section('seo_type', 'website')
@section('seo_og_image', $ogImage)
@if($posts->currentPage() > 1)
  @section('seo_noindex', '1')
@endif

@push('head')
  <link rel="stylesheet" href="/css/blog-archive.css?v={{ $archiveCssVersion }}">
@endpush
@push('schema')
  <x-schema.blog-archive
    :name="'Kategori '.$category->name.' | Blog Lanyard Bekasi'"
    :description="$description"
    :canonical-path="'/kategori/'.$category->slug"
    :breadcrumb-name="$category->name"
    :posts="$posts"
  />
@endpush

@section('content')
<div class="blog-archive-page">
  <section class="blog-archive-hero blog-archive-hero--compact" aria-labelledby="category-page-title">
    <div class="blog-archive-shell">
      <nav class="blog-archive-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('blog.index') }}">Blog</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Kategori</span>
      </nav>
      <p class="blog-archive-eyebrow">Kategori Artikel</p>
      <h1 id="category-page-title">{{ $category->name }}</h1>
      <p>Kumpulan panduan dan wawasan yang membahas {{ strtolower($category->name) }}.</p>
      <a class="blog-archive-back-link" href="{{ route('blog.index') }}">
        <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
        <span>Kembali ke semua artikel</span>
      </a>
    </div>
  </section>

  <section class="blog-archive-section" aria-labelledby="category-listing-title">
    <div class="blog-archive-shell">
      <header class="blog-archive-heading">
        <div>
          <h2 id="category-listing-title">Artikel dalam Kategori Ini</h2>
          <p>Disusun dari artikel terbaru agar mudah dipindai.</p>
        </div>
        <p class="blog-archive-heading__count">{{ $posts->total() }} artikel</p>
      </header>
      <x-blog.archive-list
        :posts="$posts"
        empty-title="Belum ada artikel"
        empty-text="Belum ada artikel yang diterbitkan dalam kategori ini."
      />
    </div>
  </section>

  <x-blog.archive-cta />
</div>

@include('components.footer')
@endsection
