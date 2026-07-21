@extends('layouts.public')

@section('title', 'Tag: ' . $tagName . ' - Blog Pusat Tali Lanyard')
@section('meta_description', 'Kumpulan artikel dengan tag ' . strtolower($tagName) . ' di Pusat Tali Lanyard. Temukan panduan, tips, dan insight terbaik dari tim kami.')
@section('seo_type', 'website')
@section('seo_og_image', '/uploads/pusat-tali-lanyard-logo.webp')
@if(request()->integer('page', 1) > 1)
  @section('seo_noindex', '1')
@endif

@section('content')
@php
  $page = request()->get('page', 1);
  $img = fn ($src) => \App\Support\AssetUrl::relative($src);
  $slugify = function($s) {
    $s = trim($s);
    $s = mb_strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/u', '-', $s);
    return trim($s, '-');
  };
@endphp

{{-- Hero Tag --}}
<section class="bg-white py-16 sm:py-24 border-b border-slate-200">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10 text-center">
    <nav class="flex items-center justify-center gap-2 text-[13px] font-medium text-slate-400 mb-6">
      <a href="/blog" class="hover:text-slate-800 transition-colors">Blog</a>
      <span class="text-slate-300">/</span>
      <span class="text-slate-700">Tag</span>
    </nav>
    
    <div class="mx-auto inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 mb-6 shadow-sm">
      <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">sell</span>
    </div>
    
    <h1 class="font-bold leading-[1.08] text-slate-900" style="font-size: clamp(2rem, 4vw, 2.75rem);">
      #{{ $tagName }}
    </h1>
    
    <p class="mt-4 max-w-2xl mx-auto text-base sm:text-[17px] leading-relaxed text-slate-600">
      Menampilkan <span class="font-semibold text-slate-800">{{ $posts->total() }} artikel</span> dengan tag <span class="font-semibold text-slate-800">{{ strtolower($tagName) }}</span>. Jelajahi panduan & inspirasi lanyard custom terbaik.
    </p>
    
    <div class="mt-6 flex flex-wrap justify-center gap-2">
      <a href="/blog" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-slate-200 bg-white text-[13px] font-bold text-slate-700 hover:bg-slate-50 transition">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Semua Artikel
      </a>
    </div>
  </div>
</section>

{{-- Blog Grid --}}
<section class="bg-slate-50 py-12 sm:py-16 min-h-[500px]">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    @if ($posts->isEmpty())
      <div class="text-center py-20">
        <span class="material-symbols-outlined text-6xl text-slate-300">label_off</span>
        <h2 class="mt-6 text-xl font-bold text-slate-900">Tidak ada artikel untuk tag ini</h2>
        <p class="mt-2 text-[15px] text-slate-600">Coba cari tag lain atau kembali ke halaman blog.</p>
        <a href="/blog" class="mt-8 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-900 text-white font-bold text-sm hover:bg-slate-800 transition-colors shadow-sm active:scale-[0.98]">
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>
          Kembali ke Blog
        </a>
      </div>
    @else
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      @foreach ($posts as $post)
      @php
        $textOnly = trim(strip_tags($post->content ?? ''));
        $wordCount = $textOnly ? count(preg_split('/\s+/', $textOnly)) : 0;
        $readingTime = max(1, (int) ceil($wordCount / 200));
        // Parse tags for each post
        $rawTags = $post->tags ?? '';
        $parsedTags = collect(explode(',', $rawTags))->map(fn($t) => trim($t))->filter()->take(3);
      @endphp
      <article class="group flex min-h-full flex-col rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md hover:border-slate-300">
        {{-- Image --}}
        <a href="/blog/{{ $post->slug }}" class="overflow-hidden rounded-[14px] bg-slate-100 block">
          @if($post->featuredImage)
          <img class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]" src="{{ $img($post->featuredImage) }}" alt="{{ $post->title }}" loading="lazy" decoding="async" width="360" height="270" />
          @else
          <div class="aspect-[4/3] w-full bg-slate-200 group-hover:scale-[1.03] transition-transform duration-500 flex items-center justify-center">
            <span class="material-symbols-outlined text-5xl text-slate-300">article</span>
          </div>
          @endif
        </a>

        {{-- Meta Row --}}
        <div class="mt-5 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            @if($post->author && $post->author->avatar)
            <img src="{{ $img($post->author->avatar) }}" alt="{{ $post->author->name }}" class="h-[30px] w-[30px] rounded-full object-cover shadow-sm bg-slate-100" width="30" height="30" loading="lazy" decoding="async" />
            @else
            <div class="grid h-[30px] w-[30px] place-items-center rounded-full bg-slate-800 text-[11px] font-extrabold text-white shadow-sm">
              {{ strtoupper(substr($post->author->name ?? 'Admin', 0, 1)) }}
            </div>
            @endif
            <span class="text-[13px] font-bold text-slate-800 line-clamp-1 max-w-[130px] sm:max-w-[150px]">{{ $post->author->name ?? 'Tim Lanyard' }}</span>
          </div>
          <div class="text-[12px] font-medium text-slate-500 whitespace-nowrap">
            <span>{{ \Carbon\Carbon::parse($post->createdAt)->translatedFormat('j M Y') }}</span>
            <span class="mx-1 text-slate-300">•</span>
            <span>{{ $readingTime }} mins</span>
          </div>
        </div>

        {{-- Body Content --}}
        <div class="mt-4 flex flex-1 flex-col">
          {{-- Title --}}
          <a href="/blog/{{ $post->slug }}" class="block mb-2.5">
            <h2 class="text-[1.1rem] font-bold leading-[1.35] text-slate-900 group-hover:text-slate-700 transition-colors line-clamp-2">
              {{ $post->title }}
            </h2>
          </a>

          {{-- Excerpt --}}
          <p class="text-[14px] leading-relaxed text-slate-500 line-clamp-2">
            {{ $post->metaDescription ?: 'Baca selengkapnya tentang topik ini di blog Pusat Tali Lanyard.' }}
          </p>

          {{-- Tags --}}
          @if($parsedTags->isNotEmpty())
          <div class="mt-4 flex flex-wrap gap-1.5">
            @foreach($parsedTags as $t)
              @php $tSlug = $slugify($t); @endphp
              <a href="/tag/{{ $tSlug }}" class="inline-flex px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-[11px] font-bold text-slate-600 hover:bg-amber-50 hover:border-amber-200 hover:text-amber-700 transition-colors">#{{ $t }}</a>
            @endforeach
          </div>
          @endif
        </div>
      </article>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if ($posts->hasPages())
    <div class="mt-12 flex justify-center">
      {{ $posts->links() }}
    </div>
    @endif
    @endif
  </div>
</section>

@include('components.cta')
@include('components.footer')
@endsection
