@extends('layouts.public')

@php
  use App\Support\AssetUrl;
  use App\Support\ResponsiveImage;

  $displayName = $author->name ?: 'Tim Lanyard Bekasi';
  $bioText = $author->bio;
  $avatar = AssetUrl::relative($author->avatar);
  $avatarSrcset = ResponsiveImage::srcset($avatar);
  $articleCount = $posts->total();
  $archiveCssPath = public_path('css/blog-archive.css');
  $archiveCssVersion = file_exists($archiveCssPath) ? filemtime($archiveCssPath) : 1;
@endphp

@section('title', $displayName.' | Penulis Lanyard Bekasi')
@section('meta_description', 'Lihat artikel yang ditulis oleh '.$displayName.' di blog Lanyard Bekasi tentang desain, produksi, dan penggunaan lanyard custom.')
@section('seo_canonical', route('blog.author', ['slug' => $authorSlug]))
@section('seo_og_image', $author->avatar ?: '/uploads/lanyard-bekasi-logo.webp')
@if($posts->currentPage() > 1)
  @section('seo_noindex', '1')
@endif

@push('head')
  <link rel="stylesheet" href="/css/blog-archive.css?v={{ $archiveCssVersion }}">
@endpush
@push('schema')
  @include('components.schema.author')
@endpush

@section('content')
<div class="blog-archive-page">
  <section class="blog-author-hero" aria-labelledby="author-page-title">
    <div class="blog-archive-shell blog-author-hero__layout">
      <div class="blog-author-hero__copy">
        <nav class="blog-archive-breadcrumb" aria-label="Breadcrumb">
          <a href="{{ route('blog.index') }}">Blog</a>
          <span aria-hidden="true">/</span>
          <span aria-current="page">Penulis</span>
        </nav>
        <p class="blog-archive-eyebrow">Profil Penulis</p>
        <h1 id="author-page-title">{{ $displayName }}</h1>
        @if($bioText)
          <p>{{ $bioText }}</p>
        @else
          <p>Berbagi wawasan tentang produksi, desain, dan penggunaan lanyard custom.</p>
        @endif
        <div class="blog-author-hero__count">
          <strong>{{ $articleCount }}</strong>
          <span>artikel diterbitkan</span>
        </div>
      </div>

      <div class="blog-author-portrait" aria-label="Foto {{ $displayName }}">
        @if($avatar)
          <img src="{{ $avatar }}" @if($avatarSrcset) srcset="{{ $avatarSrcset }}" sizes="(min-width: 768px) 240px, 180px" @endif alt="{{ $displayName }}, penulis Lanyard Bekasi" width="560" height="560" loading="eager" decoding="async">
        @else
          <span aria-hidden="true">{{ strtoupper(mb_substr($displayName, 0, 1)) }}</span>
        @endif
      </div>
    </div>
  </section>

  <section class="blog-archive-section" aria-labelledby="author-listing-title">
    <div class="blog-archive-shell">
      <header class="blog-archive-heading">
        <div>
          <h2 id="author-listing-title">Tulisan dari {{ $displayName }}</h2>
          <p>Artikel terbaru yang diterbitkan oleh penulis ini.</p>
        </div>
        <p class="blog-archive-heading__count">{{ $articleCount }} artikel</p>
      </header>
      <x-blog.archive-list
        :posts="$posts"
        empty-title="Belum ada artikel"
        empty-text="Penulis ini belum menerbitkan artikel. Jelajahi arsip blog untuk menemukan wawasan lainnya."
      />
    </div>
  </section>

  <x-blog.archive-cta />
</div>

@include('components.footer')
@endsection
