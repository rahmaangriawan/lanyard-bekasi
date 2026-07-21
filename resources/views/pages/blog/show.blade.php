@extends('layouts.public')

@push('head')
<style>
  /* Browser default blue link colors for single post content */
  .article-content a {
    color: #0000EE !important;
    text-decoration: underline !important;
  }
  .article-content a:hover {
    color: #551A8B !important;
  }
</style>
@endpush

@section('title', $post->metaTitle ?: $post->title . ' - Pusat Tali Lanyard')
@section('meta_description', $post->metaDescription)
@section('seo_type', 'article')
@section('seo_og_image', route('blog.og-image', ['slug' => $post->slug]))
@section('seo_published_time', optional($post->createdAt)->toAtomString())
@section('seo_modified_time', optional($post->updatedAt)->toAtomString())

@section('content')
@php
  $img = fn ($src) => \App\Support\AssetUrl::relative($src);
  
  // Bersihkan hardcoded domain pada img src di dalam konten (contoh: localhost:8000 atau domain lama)
  $cleanContent = \App\Support\AssetUrl::html($post->content ?? '');

  // Hapus widget "Bagikan artikel" bawaan dari hasil scraping
  $cleanContent = preg_replace('/<div[^>]*>\s*<p[^>]*>\s*Bagikan artikel\s*<\/p>[\s\S]*?<button[^>]*>[\s\S]*?<\/button>\s*<\/div>\s*<\/div>/i', '', $cleanContent);

  $wordCount = $cleanContent ? count(preg_split('/\s+/', strip_tags($cleanContent))) : 0;
  $readingTime = max(1, (int) ceil($wordCount / 200));

  // Pembuatan Daftar Isi (TOC) — hanya H2 & H3
  $processedContent = \App\Support\AutoLinkSeo::apply($cleanContent);
  $toc = [];
  preg_match_all('/<h([2-6])([^>]*)>(.*?)<\/h\1>/is', $cleanContent, $matches, PREG_SET_ORDER);

  if (count($matches) >= 2) {
      $index = 0;
      $processedContent = preg_replace_callback('/<h([2-6])([^>]*)>(.*?)<\/h\1>/is', function($match) use (&$toc, &$index) {
          $level = (int) $match[1];
          $attrs = $match[2];
          $text = trim(html_entity_decode(strip_tags($match[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
          if ($text === '') return $match[0];

          // Gunakan ID yang sudah ada bila tersedia, lalu pastikan tetap unik.
          preg_match('/\sid=["\']([^"\']+)["\']/i', $attrs, $idMatch);
          $slug = \Illuminate\Support\Str::slug($idMatch[1] ?? $text);
          $slug = $slug !== '' ? $slug : 'bagian-'.($index + 1);
          $baseSlug = $slug;
          $counter = 1;
          while (in_array($slug, array_column($toc, 'slug'), true)) {
              $slug = $baseSlug.'-'.$counter++;
          }

          $attrs = preg_replace('/\s+id=["\'][^"\']*["\']/i', '', $attrs);

          $toc[] = [
              'level' => $level,
              'text' => $text,
              'slug' => $slug
          ];

          $index++;

          return '<h'.$level.' id="'.e($slug).'"'.$attrs.'>'.$match[3].'</h'.$level.'>';
      }, $processedContent);

      // Build TOC HTML — collapsible details
      $tocTree = [];
      $tocStack = [];

      foreach ($toc as $tocItem) {
          while (!empty($tocStack) && $tocStack[array_key_last($tocStack)]['level'] >= $tocItem['level']) {
              array_pop($tocStack);
          }

          $node = ['item' => $tocItem, 'children' => []];

          if (empty($tocStack)) {
              $tocTree[] = $node;
              $nodeIndex = array_key_last($tocTree);
              $tocStack[] = ['level' => $tocItem['level'], 'node' => &$tocTree[$nodeIndex]];
          } else {
              $parentIndex = array_key_last($tocStack);
              $parent = &$tocStack[$parentIndex]['node'];
              $parent['children'][] = $node;
              $nodeIndex = array_key_last($parent['children']);
              $tocStack[] = ['level' => $tocItem['level'], 'node' => &$parent['children'][$nodeIndex]];
              unset($parent);
          }
      }

      $renderToc = function (array $nodes, int $depth = 0) use (&$renderToc): string {
          $html = '<ol class="toc-list" data-depth="'.$depth.'">';

          foreach ($nodes as $node) {
              $item = $node['item'];
              $html .= '<li class="toc-item">';
              $html .= '<a href="#'.e($item['slug']).'" class="toc-link">';
              $html .= '<span class="toc-marker" aria-hidden="true"></span>';
              $html .= '<span>'.e($item['text']).'</span>';
              $html .= '</a>';

              if (!empty($node['children'])) {
                  $html .= $renderToc($node['children'], $depth + 1);
              }

              $html .= '</li>';
          }

          return $html.'</ol>';
      };

      $tocHtml = '<details class="toc-container" open>';
      $tocHtml .= '<summary class="toc-summary">';
      $tocHtml .= '<span class="toc-summary__label">';
      $tocHtml .= '<span class="material-symbols-outlined" aria-hidden="true">format_list_bulleted</span>';
      $tocHtml .= '<span>Daftar Isi</span>';
      $tocHtml .= '</span>';
      $tocHtml .= '<span class="material-symbols-outlined toc-summary__chevron" aria-hidden="true">expand_more</span>';
      $tocHtml .= '</summary>';
      $tocHtml .= '<nav class="toc-nav" aria-label="Daftar isi artikel">'.$renderToc($tocTree).'</nav>';
      $tocHtml .= '</details>';

      if (preg_match('/<h[2-6]\b/i', $processedContent, $firstHeading, PREG_OFFSET_CAPTURE)) {
          $processedContent = substr_replace($processedContent, $tocHtml, $firstHeading[0][1], 0);
      }
  }
@endphp

{{-- Article Header --}}
<section class="bg-white pt-16 sm:pt-20 pb-0">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="max-w-[720px] mx-auto text-center">
      {{-- Breadcrumb --}}
      <nav class="flex items-center justify-center gap-2 text-[13px] font-medium text-slate-400 mb-5">
        <a href="/blog" class="hover:text-slate-800 transition-colors">Blog</a>
        @if($post->category)
        <span class="text-slate-300">/</span>
        <a href="/kategori/{{ $post->category->slug }}" class="hover:text-slate-800 transition-colors">{{ $post->category->name }}</a>
        @endif
      </nav>

      {{-- Title --}}
      <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 leading-[1.15] mb-4">
        {{ $post->title }}
      </h1>

      {{-- Meta line: date · category · read time --}}
      <div class="flex flex-wrap items-center justify-center pb-6 text-slate-500" style="column-gap: 14px; row-gap: 8px; font-size: 14px; line-height: 20px;">
        <span class="inline-flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" class="text-slate-400 shrink-0" fill="none" aria-hidden="true">
            <g fill="none">
              <path fill="currentColor" fill-opacity=".25" d="M3 10c0-1.886 0-2.828.586-3.414S5.114 6 7 6h10c1.886 0 2.828 0 3.414.586S21 8.114 21 10z" />
              <rect width="18" height="15" x="3" y="6" stroke="currentColor" stroke-width="1.2" rx="2" />
              <path stroke="currentColor" stroke-linecap="round" stroke-width="1.2" d="M7 3v3m10-3v3" />
              <rect width="4" height="2" x="7" y="12" fill="currentColor" rx=".5" />
              <rect width="4" height="2" x="7" y="16" fill="currentColor" rx=".5" />
              <rect width="4" height="2" x="13" y="12" fill="currentColor" rx=".5" />
              <rect width="4" height="2" x="13" y="16" fill="currentColor" rx=".5" />
            </g>
          </svg>
          <span class="whitespace-nowrap">{{ \Carbon\Carbon::parse($post->createdAt)->translatedFormat('j F Y') }}</span>
        </span>
        @if($post->category)
        <span class="text-slate-300 select-none" aria-hidden="true">·</span>
        <span class="inline-flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" class="text-slate-400 shrink-0" fill="none" aria-hidden="true">
            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
              <polygon points="7.25 14.25 1.75 8.75 8.75 1.75 14.25 1.75 14.25 7.25" />
              <circle cx="11" cy="5" r=".5" fill="currentColor" />
            </g>
          </svg>
          <a href="/kategori/{{ $post->category->slug }}" class="hover:text-slate-800 transition-colors whitespace-nowrap">{{ $post->category->name }}</a>
        </span>
        @endif
        <span class="text-slate-300 select-none" aria-hidden="true">·</span>
        <span class="inline-flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16" class="text-slate-400 shrink-0" fill="none" aria-hidden="true">
            <path fill="currentColor" d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0m0 469.3c-117.8 0-213.3-95.5-213.3-213.3S138.2 42.7 256 42.7S469.3 138.2 469.3 256S373.8 469.3 256 469.3m21.3-256v-128h-42.7V320l128-128l-32-32z" />
          </svg>
          <span class="whitespace-nowrap">{{ $readingTime }} menit baca</span>
        </span>
      </div>
    </div>
  </div>
</section>

{{-- Featured Image --}}
@if($post->featuredImage)
<section class="bg-white pt-10">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <figure>
      <img
        src="{{ $img($post->featuredImage) }}"
        alt="{{ $post->title }} - Lanyard custom Indonesia {{ isset($post->category->name) ? $post->category->name : 'artikel' }}"
        class="w-full rounded-3xl shadow-sm border border-slate-200/50 aspect-video object-cover"
        loading="lazy"
        decoding="async"
        width="1200"
        height="675"
      />
    </figure>
  </div>
</section>
@endif

{{-- Main Content + Sidebar --}}
<section class="bg-white py-14 sm:py-16">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-12">

      {{-- LEFT COLUMN --}}
      <div>
        {{-- Article Content --}}
        <article class="article-content text-[17px] text-slate-600 leading-[1.8]">
          {!! $processedContent !!}
        </article>

        {{-- Tags --}}
        @php
          $rawTags = $post->tags ?? '';
          $parsedTags = collect(explode(',', $rawTags))->map(fn($t) => trim($t))->filter();
          $slugify = function($s) {
            $s = trim($s);
            $s = mb_strtolower($s);
            $s = preg_replace('/[^a-z0-9]+/u', '-', $s);
            return trim($s, '-');
          };
        @endphp
        @if($parsedTags->isNotEmpty())
        <div class="mt-8 flex flex-wrap gap-2 items-center">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mr-1">Tags:</span>
          @foreach($parsedTags as $t)
            @php $tSlug = $slugify($t); @endphp
            <a href="/tag/{{ $tSlug }}" class="inline-flex px-3.5 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-amber-50 hover:border-amber-200 hover:text-amber-700 transition">
              #{{ $t }}
            </a>
          @endforeach
        </div>
        @endif

        {{-- Author Card --}}
        @php
          $authorDisplayName = 'Rama Angriawan';
          $authorAvatarUrl = null;
          $authorBioText = 'Penulis & spesialis lanyard custom di Indonesia. Berbagi tips desain, branding, event, dan panduan produksi lanyard berkualitas. Temukan artikel terbaru seputar lanyard custom Indonesia.';
          if (isset($post->author) && $post->author) {
            $authorDisplayName = $post->author->name ?: 'Rama Angriawan';
            try { if (!empty($post->author->avatar)) { $authorAvatarUrl = $img($post->author->avatar); } } catch(\Throwable $e) {}
            try { $b = $post->author->bio; if (!empty($b)) { $authorBioText = $b; } } catch (\Throwable $e) {}
          }
          // Override generic Admin to Rama
          if (stripos($authorDisplayName, 'Admin') !== false || strtolower(trim($authorDisplayName)) === 'admin tes') {
            $authorDisplayName = 'Rama Angriawan';
          }
          
          $authorSlug = \Illuminate\Support\Str::slug($authorDisplayName);
        @endphp
        <div class="mt-16 border-t border-slate-100 pt-10">
          <div class="flex flex-col sm:flex-row items-start gap-5 bg-slate-50 border border-slate-200 rounded-2xl p-6 sm:p-8">
            <a href="/blog/penulis/{{ $authorSlug }}" class="shrink-0 group/a" aria-label="Lihat semua artikel oleh {{ $authorDisplayName }}">
              @if($authorAvatarUrl)
                <img src="{{ $authorAvatarUrl }}" alt="{{ $authorDisplayName }} — penulis Pusat Tali Lanyard" class="w-16 h-16 rounded-full object-cover shrink-0 bg-slate-100 transition-opacity group-hover/a:opacity-80" width="64" height="64" loading="lazy" decoding="async" />
              @else
                <div class="w-16 h-16 rounded-full bg-slate-900 text-white text-xl font-bold flex items-center justify-center shrink-0 transition-opacity group-hover/a:opacity-80" aria-hidden="true">
                  {{ strtoupper(mb_substr($authorDisplayName, 0, 1)) }}
                </div>
              @endif
            </a>
            <div class="flex-1 min-w-0">
              <div class="flex flex-wrap items-center gap-2">
                <a href="/blog/penulis/{{ $authorSlug }}" class="text-base font-bold text-slate-900 hover:text-slate-700 transition-colors inline-flex items-center gap-1.5 group/link" aria-label="Archive penulis {{ $authorDisplayName }}">
                  <span class="group-hover/link:underline decoration-slate-300 underline-offset-4">{{ $authorDisplayName }}</span>
                </a>
              </div>
              <p class="text-sm text-slate-500 leading-relaxed mt-2">
                {{ $authorBioText }}
              </p>
            </div>
          </div>
        </div>

        {{-- Share Widget --}}
        <div class="mt-12 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-t border-slate-100 pt-8">
          <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Suka artikel ini?</p>
            <p class="text-sm font-medium text-slate-700">Bagikan ke jaringan Anda</p>
          </div>
          <div class="flex items-center gap-2.5">
            <a href="https://api.whatsapp.com/send?text={{ rawurlencode($post->title . ' ' . url()->current()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center bg-slate-50 border border-slate-200/80 rounded-xl w-11 h-11 text-slate-600 hover:bg-[#25D366] hover:text-white hover:border-[#25D366] hover:shadow-sm transition-all duration-300" aria-label="Bagikan via WhatsApp">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center bg-slate-50 border border-slate-200/80 rounded-xl w-11 h-11 text-slate-600 hover:bg-[#1877F2] hover:text-white hover:border-[#1877F2] hover:shadow-sm transition-all duration-300" aria-label="Bagikan via Facebook">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center w-11 h-11 text-slate-400 hover:text-slate-900 transition-colors duration-300" aria-label="Bagikan via X/Twitter">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
          </div>
        </div>

        {{-- Comments Section --}}
        <div class="mt-10 pt-10 border-t border-slate-100">
          <div class="mb-6">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-1">Diskusi</p>
            <h3 class="text-lg font-bold text-slate-900">{{ $comments->count() }} Komentar</h3>
          </div>

        @if(session('comment_sent'))
        <div class="bg-emerald-50 border border-emerald-200/60 text-emerald-700 rounded-xl px-4 py-3 mb-6 text-sm font-medium flex items-center gap-2">
          <span class="material-symbols-outlined text-emerald-500 shrink-0 text-base" style="font-variation-settings: 'FILL' 1;">check_circle</span>
          {{ session('comment_sent') }}
        </div>
        @endif

        {{-- Comment Form --}}
        <form action="{{ route('blog.comment', ['slug' => $post->slug]) }}" method="POST" class="mb-8">
          @csrf
          <div class="bg-slate-50/70 border border-slate-200/60 rounded-2xl p-4 sm:p-5 space-y-3">
            <p class="text-sm font-bold text-slate-800">Tinggalkan Balasan</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <input type="text" name="name" id="name" required value="{{ old('name') }}"
                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:border-slate-400 focus:ring-1 focus:ring-slate-300"
                placeholder="Nama lengkap">
              <input type="email" name="email" id="email" required value="{{ old('email') }}"
                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:border-slate-400 focus:ring-1 focus:ring-slate-300"
                placeholder="email@domain.com">
            </div>
            @error('name')<p class="text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror
            @error('email')<p class="text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror
            <textarea name="content" id="content" rows="3" required
              class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:border-slate-400 focus:ring-1 focus:ring-slate-300 resize-none"
              placeholder="Tulis pendapat atau pertanyaan Anda..."></textarea>
            @error('content')<p class="text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror
            <div class="flex items-center justify-between pt-1">
              <p class="text-[11px] text-slate-400">Akan dimoderasi sebelum tayang.</p>
              <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition-all hover:bg-slate-800 active:scale-[0.97]">
                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 0;">send</span>
                Kirim
              </button>
            </div>
          </div>
        </form>

        {{-- Comments List --}}
        @if($comments->isEmpty())
        <div class="flex items-center gap-3 py-8">
          <span class="material-symbols-outlined text-slate-300 text-2xl">chat_bubble_outline</span>
          <div>
            <p class="text-sm font-semibold text-slate-500">Belum ada diskusi</p>
            <p class="text-xs text-slate-400">Jadilah yang pertama berkomentar.</p>
          </div>
        </div>
        @else
        <div class="space-y-5">
          @foreach($comments as $comment)
          <div class="flex gap-3">
            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200/60 text-slate-500 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5 select-none">
              {{ strtoupper(mb_substr($comment->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-baseline gap-2 mb-1">
                <p class="text-sm font-bold text-slate-900">{{ $comment->name }}</p>
                <p class="text-[11px] text-slate-400">{{ $comment->createdAt->translatedFormat('j F Y') }}</p>
              </div>
              <p class="text-[14px] text-slate-600 leading-relaxed whitespace-pre-line">
                {{ $comment->content }}
              </p>
            </div>
          </div>
          @endforeach
        </div>
        @endif
        </div>
      </div>

      {{-- RIGHT COLUMN — Sidebar --}}
      <aside class="space-y-8 lg:sticky lg:top-28 lg:self-start">
        @if($relatedPosts->isNotEmpty())
        {{-- Related Posts --}}
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-6">
            Artikel Terkait
          </h3>
          <div class="space-y-5">
            @foreach($relatedPosts as $related)
            @php
              $relImg = $img($related->featuredImage);
            @endphp
            <a href="/blog/{{ $related->slug }}" class="group/rel flex items-start gap-4">
              <div class="w-[72px] h-[54px] shrink-0 rounded-xl overflow-hidden bg-slate-200">
                @if($relImg)
                <img src="{{ $relImg }}" alt="{{ $related->title }} - panduan lanyard custom Indonesia" class="w-full h-full object-cover group-hover/rel:scale-105 transition-transform duration-300" loading="lazy" decoding="async" width="72" height="54" />
                @else
                <div class="w-full h-full flex items-center justify-center">
                  <span class="material-symbols-outlined text-slate-300 text-xl">article</span>
                </div>
                @endif
              </div>
              <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-900 leading-snug group-hover/rel:text-slate-700 transition-colors line-clamp-2">
                  {{ $related->title }}
                </p>
                <p class="text-xs text-slate-400 mt-1">
                  {{ \Carbon\Carbon::parse($related->createdAt)->translatedFormat('j M Y') }}
                </p>
              </div>
            </a>
            @endforeach
          </div>
        </div>
        @endif

        {{-- CTA Card --}}
        <div class="bg-slate-900 rounded-2xl p-6 text-center">
          <span class="material-symbols-outlined text-white/80 text-3xl mb-4 block" style="font-variation-settings: 'FILL' 1;">forum</span>
          <h3 class="text-base font-bold text-white mb-2">Konsultasi Gratis</h3>
          <p class="text-sm text-white/70 leading-relaxed mb-6">
            Dapatkan mockup desain dan penawaran harga terbaik untuk kebutuhan lanyard custom Anda.
          </p>
          <a
            href="https://wa.me/6282210200700?text=Halo%20Lanyard%20Indonesia%2C%20saya%20mau%20konsultasi%20lanyard%20custom."
            target="_blank"
            rel="noopener noreferrer"
            class="group relative overflow-hidden inline-flex items-center gap-2 bg-white text-slate-900 rounded-xl px-5 py-3 text-sm font-bold w-full justify-center transition-all active:scale-[0.98] before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-slate-800 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px] hover:text-white"
          >
            <span class="material-symbols-outlined text-lg relative z-10" style="font-variation-settings: 'FILL' 1;">chat</span>
            <span class="hero-btn-text relative z-10 inline-block">Chat WhatsApp</span>
          </a>
        </div>
      </aside>

    </div>
  </div>
</section>

@include('components.footer')

@push('schema')
@include('components.schema.blog-post')
@endpush
@endsection

@section('head')
<style>
  .article-content .toc-container {
    margin: 2rem 0;
    padding: 1.25rem 1.35rem;
    border: 1px solid #DDE3EA;
    border-radius: 1rem;
    background: #F8FAFC;
  }

  .article-content .toc-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    color: #253141;
    cursor: pointer;
    list-style: none;
    user-select: none;
  }

  .article-content .toc-summary::-webkit-details-marker { display: none; }

  .article-content .toc-summary__label {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    font-size: .9rem;
    font-weight: 750;
  }

  .article-content .toc-summary__label .material-symbols-outlined,
  .article-content .toc-summary__chevron {
    font-size: 1.15rem;
    color: #64748B;
  }

  .article-content .toc-summary__chevron { transition: transform .2s ease; }
  .article-content .toc-container[open] .toc-summary__chevron { transform: rotate(180deg); }
  .article-content .toc-nav { margin-top: 1rem; }

  .article-content .toc-list {
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .article-content .toc-list .toc-list {
    margin: .3rem 0 .25rem .45rem;
    padding-left: 1rem;
    border-left: 1px solid #D7DEE7;
  }

  .article-content .toc-item {
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .article-content .toc-link,
  .article-content .toc-link:hover {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    padding: .3rem 0;
    color: #52647A !important;
    font-size: .925rem;
    font-weight: 500;
    line-height: 1.5;
    text-decoration: none !important;
  }

  .article-content .toc-list[data-depth="0"] > .toc-item > .toc-link {
    color: #253141 !important;
    font-weight: 700;
  }

  .article-content .toc-link:hover { color: #0F172A !important; }

  .article-content .toc-marker {
    width: .38rem;
    height: .38rem;
    margin-top: .52rem;
    flex: 0 0 auto;
    border-radius: 999px;
    background: #94A3B8;
  }

  .article-content .toc-list .toc-list .toc-marker {
    width: .32rem;
    height: .32rem;
    border: 1px solid #94A3B8;
    background: transparent;
  }

  /* Article content typography — clean prose without @tailwindcss/typography */
  .article-content h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #253141;
    margin-top: 2.5rem;
    margin-bottom: 0.75rem;
    line-height: 1.3;
    letter-spacing: -0.02em;
  }

  .article-content h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #253141;
    margin-top: 2rem;
    margin-bottom: 0.5rem;
    line-height: 1.35;
  }

  .article-content h4,
  .article-content h5,
  .article-content h6 {
    color: #253141;
    margin-top: 1.65rem;
    margin-bottom: .5rem;
    font-weight: 650;
    line-height: 1.4;
  }

  .article-content h4 { font-size: 1.1rem; }
  .article-content h5,
  .article-content h6 { font-size: 1rem; }

  .article-content p {
    margin-bottom: 1.25rem;
    color: #536B86;
  }

  .article-content p:last-child {
    margin-bottom: 0;
  }

  .article-content ul,
  .article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 1.75rem;
  }

  .article-content li > ul,
  .article-content li > ol {
    margin-top: .45rem;
    margin-bottom: .55rem;
    padding-left: 1.35rem;
  }

  .article-content ul > li {
    list-style-type: disc;
    margin-bottom: 0.375rem;
    padding-left: 0.375rem;
  }

  .article-content ul ul > li { list-style-type: circle; }
  .article-content ul ul ul > li { list-style-type: square; }

  .article-content ol > li {
    list-style-type: decimal;
    margin-bottom: 0.375rem;
    padding-left: 0.375rem;
  }

  .article-content ol ol > li { list-style-type: lower-alpha; }
  .article-content ol ol ol > li { list-style-type: lower-roman; }

  .article-content li::marker {
    color: #98A8BA;
  }

  .article-content blockquote {
    border-left: 4px solid #3D4E67;
    background-color: #FAFAFA;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 0 0.75rem 0.75rem 0;
  }

  .article-content blockquote p {
    color: #576E8F;
    font-style: italic;
    margin-bottom: 0;
  }

  .article-content img {
    border-radius: 1rem;
    width: 100%;
    height: auto;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid #DDE0E3;
  }

  .article-content a {
    color: #3D4E67;
    font-weight: 500;
    text-decoration: underline;
    text-decoration-color: #CBD0D7;
    text-underline-offset: 2px;
    transition: all 0.2s ease;
  }

  .article-content a:hover {
    text-decoration-color: #3D4E67;
  }

  .article-content hr {
    border: 0;
    height: 1px;
    background-color: #DDE0E3;
    margin: 2rem 0;
  }

  .article-content figure {
    margin-bottom: 1.5rem;
  }

  .article-content figcaption {
    font-size: 0.875rem;
    color: #98A8BA;
    text-align: center;
    margin-top: 0.5rem;
  }

  .article-content table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
    font-size: 0.9375rem;
  }

  .article-content th {
    background-color: #FAFAFA;
    font-weight: 600;
    color: #253141;
    text-align: left;
    padding: 0.75rem 1rem;
    border-bottom: 2px solid #DDE0E3;
  }

  .article-content td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #EFF0F1;
    color: #536B86;
  }

  .article-content iframe {
    width: 100%;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid #DDE0E3;
  }

  @media (max-width: 640px) {
    .article-content { font-size: 1rem; }
    .article-content h2 { font-size: 1.375rem; }
    .article-content .toc-container { padding: 1.1rem; }
    .article-content .toc-list .toc-list { padding-left: .85rem; }
    .article-content ul,
    .article-content ol { padding-left: 1.5rem; }
  }

  @media (prefers-reduced-motion: reduce) {
    .article-content .toc-summary__chevron { transition: none; }
  }
</style>
@endsection
