@extends('layouts.public')

@php
  $isAdminFallback = (stripos($author->name ?? '', 'Admin') !== false || strtolower(trim($author->name ?? '')) === 'admin tes');
  $displayName = $isAdminFallback ? 'Rama Angriawan' : ($author->name ?: 'Rama Angriawan');
@endphp
@section('title', $displayName . ' - Penulis Pusat Tali Lanyard')
@section('meta_description', 'Lihat semua artikel yang ditulis oleh ' . $displayName . ' di blog Pusat Tali Lanyard. Tips desain lanyard custom, branding, event & panduan produksi Indonesia.')
@section('seo_canonical', config('app.url') . '/blog/penulis/' . $authorSlug)
@section('seo_og_image', $author->avatar ?: '/uploads/pusat-tali-lanyard-logo.webp')
@if(request()->integer('page', 1) > 1)
  @section('seo_noindex', '1')
@endif

@push('schema')
  @include('components.schema.author')
@endpush

@section('content')
@php
  $img = fn ($src) => \App\Support\AssetUrl::relative($src);
  $displayName = isset($displayName) ? $displayName : ($author->name ?: 'Rama Angriawan');
  $isAdminFallback = isset($isAdminFallback) ? $isAdminFallback : false;
  try { $bioRaw = $author->bio; } catch (\Throwable $e) { $bioRaw = null; }
  $bioText = $bioRaw ?: 'Penulis & spesialis lanyard custom di Indonesia. Berbagi tips desain, branding, event, dan panduan produksi lanyard berkualitas. Fokus pada kebutuhan kantor, komunitas, sekolah, dan instansi di Indonesia Raya.';
  if ($isAdminFallback) {
    $bioText = 'Rama Angriawan — Penulis & spesialis lanyard custom di Indonesia. Berbagi tips desain yang bikin orang saling nengok, panduan branding, event, dan produksi lanyard berkualitas untuk kebutuhan kantor, komunitas, sekolah, dan instansi.';
  }
@endphp

{{-- Author Profile --}}
<section class="bg-white pt-16 sm:pt-20 pb-10">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="max-w-[720px] mx-auto text-center">
      {{-- Breadcrumb --}}
      <nav class="flex items-center justify-center gap-2 text-[13px] font-medium text-slate-400 mb-8">
        <a href="/blog" class="hover:text-slate-800 transition-colors">Blog</a>
        <span class="text-slate-300">/</span>
        <span class="text-slate-700">{{ $displayName }}</span>
      </nav>

      {{-- Avatar --}}
      <div class="mb-4">
        @if($author->avatar)
        <img src="{{ $img($author->avatar) }}" alt="{{ $displayName }} — penulis Pusat Tali Lanyard" class="rounded-full object-cover mx-auto bg-slate-100 shadow-sm" style="width: 80px; height: 80px;" width="80" height="80" loading="lazy" decoding="async" />
        @else
        <div class="rounded-full bg-slate-900 text-white text-2xl font-bold flex items-center justify-center mx-auto shadow-sm select-none" aria-hidden="true" style="width: 80px; height: 80px;">
          {{ strtoupper(mb_substr($displayName, 0, 1)) }}
        </div>
        @endif
      </div>

      <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 mb-4">{{ $displayName }}</h1>
      <p class="text-slate-600 text-[15px] leading-6 max-w-[60ch] mx-auto">
        {{ $bioText }}
      </p>
    </div>
  </div>
</section>

{{-- Author Posts --}}
<section class="bg-white py-12 border-t border-slate-100">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="mb-8 text-center sm:text-left">
      <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ $posts->total() }} Tulisan</h2>
    </div>

    @if($posts->isEmpty())
      <div class="py-12 bg-slate-50 rounded-2xl text-center border border-slate-100 max-w-[800px] mx-auto">
        <p class="text-[15px] font-semibold text-slate-500">Belum ada artikel</p>
        <p class="text-[13px] text-slate-400 mt-1">Penulis ini belum menerbitkan artikel.</p>
      </div>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($posts as $post)
        @php
          $thumb = $img($post->featuredImage);
        @endphp
        <a href="/blog/{{ $post->slug }}" class="group/p flex flex-col h-full bg-white rounded-2xl border border-slate-200/60 overflow-hidden hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 hover:-translate-y-1">
          <div class="aspect-[16/10] w-full bg-slate-100 overflow-hidden">
            @if($thumb)
            <img src="{{ $thumb }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover/p:scale-105 transition-transform duration-500" loading="lazy" decoding="async" width="400" height="250" />
            @else
            <div class="w-full h-full bg-slate-50"></div>
            @endif
          </div>
          <div class="p-5 flex flex-col flex-1">
            <div class="flex items-center gap-2 mb-3 text-[11px] font-semibold tracking-wide text-slate-500 uppercase">
              @if($post->category)
              <span class="text-slate-900">{{ $post->category->name }}</span>
              <span class="text-slate-300">·</span>
              @endif
              <span>{{ \Carbon\Carbon::parse($post->createdAt)->translatedFormat('j M Y') }}</span>
            </div>
            <h3 class="text-[15px] md:text-base font-bold tracking-tight text-slate-900 leading-[1.4] group-hover/p:text-slate-700 transition-colors line-clamp-3 mb-2">
              {{ $post->title }}
            </h3>
          </div>
        </a>
        @endforeach
      </div>

      {{-- Pagination --}}
      @if($posts->hasPages())
        <div class="mt-12 flex justify-center">
          {{ $posts->links() }}
        </div>
      @endif
    @endif
  </div>
</section>

@include('components.footer')
@endsection
