@extends('layouts.public')

@php($safeContent = \App\Support\HtmlSanitizer::clean($cityPage->content ?? ''))

@section('title', $cityPage->metaTitle ?: $cityPage->title.' | Lanyard Bekasi')
@section('meta_description', $cityPage->metaDescription ?: 'Layanan cetak lanyard custom untuk '.$cityPage->title.'. Konsultasi kebutuhan event, kantor, sekolah, dan komunitas Anda.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/').'/area/'.$cityPage->slug)
@if($cityPage->featuredImage)
  @section('seo_og_image', $cityPage->featuredImage)
@endif

@section('content')
  <section class="bg-white py-16 sm:py-20 border-b border-slate-200">
    <div class="mx-auto max-w-[960px] px-6 lg:px-10">
      <a href="{{ route('area') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-slate-950">
        <span class="material-symbols-outlined text-base" aria-hidden="true">arrow_back</span>
        Semua area layanan
      </a>
      <nav class="mt-8 text-sm text-slate-500" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
          <li><a href="{{ route('home') }}" class="hover:text-slate-900">Beranda</a></li>
          <li aria-hidden="true">/</li>
          <li><a href="{{ route('area') }}" class="hover:text-slate-900">Area Layanan</a></li>
          <li aria-hidden="true">/</li>
          <li class="font-medium text-slate-700" aria-current="page">{{ $cityPage->title }}</li>
        </ol>
      </nav>
      <p class="mt-6 text-xs font-extrabold uppercase tracking-normal text-slate-600">Area layanan</p>
      <h1 class="mt-4 font-bold leading-[1.08] text-slate-900" style="font-size: clamp(2rem, 4vw, 3.25rem);">{{ $cityPage->title }}</h1>
      <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600">Layanan cetak lanyard custom untuk kebutuhan kantor, event, sekolah, dan komunitas di area ini.</p>
    </div>
  </section>

  <article class="bg-slate-50 py-12 sm:py-16">
    <div class="mx-auto max-w-[960px] px-6 lg:px-10">
      @if($cityPage->featuredImage)
        <img src="{{ $cityPage->featuredImage }}" alt="{{ $cityPage->title }} - layanan lanyard custom Indonesia" class="mb-10 aspect-[16/8] w-full rounded-2xl object-cover shadow-sm" width="960" height="480" loading="eager" fetchpriority="high" decoding="async" />
      @endif
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10 prose prose-slate max-w-none">
        {!! $safeContent !!}
      </div>
    </div>
  </article>

  @include('components.cta')
  @include('components.footer')
  @push('schema')
    @include('components.schema.city-page')
  @endpush
@endsection
