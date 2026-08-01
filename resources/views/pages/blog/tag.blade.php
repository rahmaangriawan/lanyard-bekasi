@extends('layouts.public')

@php
  $description = 'Kumpulan artikel dengan tag '.strtolower($tagName).' di Lanyard Bekasi untuk membantu Anda memahami lanyard custom.';
  $ogImage = $posts->first()?->featuredImage ?: '/uploads/lanyard-bekasi-logo.webp';
  $archiveCssPath = public_path('css/blog-archive.css');
  $archiveCssVersion = file_exists($archiveCssPath) ? filemtime($archiveCssPath) : 1;
  $tagSlug = request()->route('slug');
@endphp

@section('title', 'Tag: '.$tagName.' | Blog Lanyard Bekasi')
@section('meta_description', $description)
@section('seo_canonical', route('blog.tag', ['slug' => $tagSlug]))
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
    :name="'Tag '.$tagName.' | Blog Lanyard Bekasi'"
    :description="$description"
    :canonical-path="'/tag/'.$tagSlug"
    :breadcrumb-name="'#'.$tagName"
    :posts="$posts"
  />
@endpush

@section('content')
<div class="blog-archive-page">
  <section class="blog-archive-hero blog-archive-hero--compact" aria-labelledby="tag-page-title">
    <div class="blog-archive-shell">
      <nav class="blog-archive-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('blog.index') }}">Blog</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Tag</span>
      </nav>
      <p class="blog-archive-eyebrow">Tag Artikel</p>
      <h1 id="tag-page-title">#{{ $tagName }}</h1>
      <p>Artikel yang memiliki topik terkait {{ strtolower($tagName) }}.</p>
      <a class="blog-archive-back-link" href="{{ route('blog.index') }}">
        <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
        <span>Kembali ke semua artikel</span>
      </a>
    </div>
  </section>

  <section class="blog-archive-section" aria-labelledby="tag-listing-title">
    <div class="blog-archive-shell">
      <header class="blog-archive-heading">
        <div>
          <h2 id="tag-listing-title">Artikel dengan Tag Ini</h2>
          <p>Daftar artikel terbaru yang memakai tag {{ $tagName }}.</p>
        </div>
        <p class="blog-archive-heading__count">{{ $posts->total() }} artikel</p>
      </header>
      <x-blog.archive-list
        :posts="$posts"
        empty-title="Tidak ada artikel untuk tag ini"
        empty-text="Coba tag lain atau kembali ke halaman utama blog."
      />
    </div>
  </section>

  <x-blog.archive-cta />
</div>

@include('components.footer')
@endsection
