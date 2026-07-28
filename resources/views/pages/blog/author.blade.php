@extends('layouts.public')

@php
  use App\Support\AssetUrl;
  use App\Support\ResponsiveImage;
  use Illuminate\Support\Str;

  $displayName = $author->name ?: 'Tim Pusat Tali Lanyard';
  $bioText = $author->bio;
  $avatar = AssetUrl::relative($author->avatar);
  $avatarSrcset = ResponsiveImage::srcset($avatar);
  $articleCount = $posts->total();
  $authorCssPath = public_path('css/blog-author.css');
  $authorCssVersion = file_exists($authorCssPath) ? filemtime($authorCssPath) : 1;
@endphp

@section('title', $displayName . ' - Penulis Pusat Tali Lanyard')
@section('meta_description', 'Lihat semua artikel yang ditulis oleh ' . $displayName . ' di blog Pusat Tali Lanyard. Tips desain lanyard custom, branding, event & panduan produksi Indonesia.')
@section('seo_canonical', config('app.url') . '/blog/penulis/' . $authorSlug)
@section('seo_og_image', $author->avatar ?: '/uploads/pusat-tali-lanyard-logo.webp')
@if(request()->integer('page', 1) > 1)
  @section('seo_noindex', '1')
@endif

@push('head')
  <link rel="stylesheet" href="/css/blog-author.css?v={{ $authorCssVersion }}">
@endpush
@push('schema')
  @include('components.schema.author')
@endpush

@section('content')
<main class="author-page">
  <section class="author-hero" aria-labelledby="author-page-title">
    <div class="author-container author-hero__layout">
      <div class="author-hero__content">
        <nav class="author-breadcrumb" aria-label="Breadcrumb">
          <a href="{{ route('blog.index') }}">Blog</a>
          <span aria-hidden="true">/</span>
          <span aria-current="page">{{ $displayName }}</span>
        </nav>
        <p class="author-eyebrow">Penulis</p>
        <h1 id="author-page-title">{{ $displayName }}</h1>
        @if($bioText)
          <p class="author-bio">{{ $bioText }}</p>
        @endif
        <div class="author-summary" aria-label="Ringkasan penulis">
          <strong>{{ $articleCount }}</strong>
          <span>{{ $articleCount === 1 ? 'artikel diterbitkan' : 'artikel diterbitkan' }}</span>
        </div>
      </div>

      <div class="author-portrait" aria-label="Foto {{ $displayName }}">
        <div class="author-portrait__frame">
          @if($avatar)
            <img src="{{ $avatar }}" @if($avatarSrcset) srcset="{{ $avatarSrcset }}" sizes="(min-width: 768px) 360px, 72vw" @endif alt="{{ $displayName }}, penulis Pusat Tali Lanyard" width="560" height="560" loading="eager" decoding="async">
          @else
            <span aria-hidden="true">{{ strtoupper(mb_substr($displayName, 0, 1)) }}</span>
          @endif
        </div>
        <p>Wawasan produksi, desain, dan penggunaan lanyard.</p>
      </div>
    </div>
  </section>

  <section class="author-articles" aria-labelledby="author-articles-title">
    <div class="author-container">
      <header class="author-section-heading">
        <div>
          <p class="author-eyebrow">Arsip Penulis</p>
          <h2 id="author-articles-title">Tulisan dari {{ $displayName }}</h2>
        </div>
        <p>{{ $articleCount }} {{ $articleCount === 1 ? 'artikel' : 'artikel' }}</p>
      </header>

      @if($posts->isEmpty())
        <div class="author-empty">
          <span class="material-symbols-outlined" aria-hidden="true">article</span>
          <h3>Belum ada artikel</h3>
          <p>Penulis ini belum menerbitkan artikel. Jelajahi arsip blog untuk menemukan wawasan lainnya.</p>
          <a href="{{ route('blog.index') }}" data-button-motion><span>Lihat Semua Artikel</span></a>
        </div>
      @else
        <div class="author-grid">
          @foreach($posts as $post)
            @php
              $postImage = AssetUrl::relative($post->featuredImage);
              $postSrcset = ResponsiveImage::srcset($postImage);
              $excerpt = $post->metaDescription ?: Str::limit(trim(strip_tags((string) $post->content)), 145);
            @endphp
            <article class="author-card">
              <a class="author-card__media" href="/blog/{{ $post->slug }}" aria-label="Baca {{ $post->title }}">
                @if($postImage)
                  <img src="{{ $postImage }}" @if($postSrcset) srcset="{{ $postSrcset }}" sizes="(min-width: 1024px) 400px, (min-width: 700px) 50vw, 100vw" @endif alt="{{ $post->title }}" width="640" height="400" loading="lazy" decoding="async">
                @else
                  <span class="author-card__fallback material-symbols-outlined" aria-hidden="true">article</span>
                @endif
              </a>
              <div class="author-card__content">
                <div class="author-card__meta">
                  <span>{{ $post->category->name ?? 'Wawasan Lanyard' }}</span>
                  <span aria-hidden="true">•</span>
                  <time datetime="{{ \Carbon\Carbon::parse($post->createdAt)->toDateString() }}">{{ \Carbon\Carbon::parse($post->createdAt)->translatedFormat('j M Y') }}</time>
                </div>
                <h3><a href="/blog/{{ $post->slug }}">{{ $post->title }}</a></h3>
                <p>{{ $excerpt }}</p>
                <a class="author-card__link" href="/blog/{{ $post->slug }}">Baca artikel<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
              </div>
            </article>
          @endforeach
        </div>

        @if($posts->hasPages())
          <div class="author-pagination">
            {{ $posts->links() }}
          </div>
        @endif
      @endif
    </div>
  </section>
</main>

@include('components.footer')
@endsection
