@extends('layouts.public')

@php
  use App\Support\AssetUrl;
  use App\Support\AuthorProfile;
  use App\Support\AutoLinkSeo;
  use App\Support\BlogContent;
  use App\Support\ResponsiveImage;
  use Illuminate\Support\Str;

  $imageUrl = fn ($source) => AssetUrl::relative($source);
  $cleanContent = BlogContent::render($post->content ?? '');
  $cleanContent = preg_replace('/<div[^>]*>\s*<p[^>]*>\s*Bagikan artikel\s*<\/p>[\s\S]*?<button[^>]*>[\s\S]*?<\/button>\s*<\/div>\s*<\/div>/i', '', $cleanContent);
  $processedContent = AutoLinkSeo::apply($cleanContent);
  $plainContent = trim(preg_replace('/\s+/u', ' ', strip_tags($cleanContent)));
  $wordCount = $plainContent === '' ? 0 : count(preg_split('/\s+/u', $plainContent));
  $readingTime = max(1, (int) ceil($wordCount / 200));

  $toc = [];
  $tocHtml = '';
  preg_match_all('/<h([2-6])([^>]*)>(.*?)<\/h\1>/is', $processedContent, $headingMatches, PREG_SET_ORDER);

  if (count($headingMatches) >= 2) {
    $usedSlugs = [];
    $processedContent = preg_replace_callback('/<h([2-6])([^>]*)>(.*?)<\/h\1>/is', function ($match) use (&$toc, &$usedSlugs) {
      $level = (int) $match[1];
      $attributes = $match[2];
      $text = trim(html_entity_decode(strip_tags($match[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
      if ($text === '') return $match[0];

      preg_match('/\sid=["\']([^"\']+)["\']/i', $attributes, $idMatch);
      $baseSlug = Str::slug($idMatch[1] ?? $text) ?: 'bagian-'.(count($toc) + 1);
      $slug = $baseSlug;
      $counter = 2;
      while (in_array($slug, $usedSlugs, true)) $slug = $baseSlug.'-'.$counter++;
      $usedSlugs[] = $slug;
      $attributes = preg_replace('/\s+id=["\'][^"\']*["\']/i', '', $attributes);
      $toc[] = ['level' => $level, 'text' => $text, 'slug' => $slug];

      return '<h'.$level.' id="'.e($slug).'"'.$attributes.'>'.$match[3].'</h'.$level.'>';
    }, $processedContent);

    $tocHtml = '<details class="article-toc">';
    $tocHtml .= '<summary><span>Daftar isi</span><span class="material-symbols-outlined" aria-hidden="true">expand_more</span></summary>';
    $tocHtml .= '<nav aria-label="Daftar isi artikel"><ol>';
    foreach ($toc as $item) {
      $tocHtml .= '<li class="toc-level-'.$item['level'].'"><a href="#'.e($item['slug']).'">'.e($item['text']).'</a></li>';
    }
    $tocHtml .= '</ol></nav></details>';

  }

  $featuredImage = $imageUrl($post->featuredImage);
  $featuredSrcset = ResponsiveImage::srcset($featuredImage);
  $rawTags = (string) ($post->tags ?? '');
  $parsedTags = collect(explode(',', $rawTags))->map(fn ($tag) => trim($tag))->filter()->unique();

  $authorName = $post->author?->name ?: 'Tim Lanyard Bekasi';
  $authorAvatar = $imageUrl($post->author?->avatar);
  $authorBio = $post->author ? $post->author->bio : AuthorProfile::DEFAULT_BIO;
  $authorSlug = $post->author?->slug ?: Str::slug($authorName);
  $blogShowCssPath = public_path('css/blog-show.css');
  $blogShowCssVersion = file_exists($blogShowCssPath) ? filemtime($blogShowCssPath) : 1;
  $blogArchiveCssPath = public_path('css/blog-archive.css');
  $blogArchiveCssVersion = file_exists($blogArchiveCssPath) ? filemtime($blogArchiveCssPath) : 1;
@endphp

@section('title', $post->metaTitle ?: $post->title . ' | Lanyard Bekasi')
@section('meta_description', $post->metaDescription)
@section('seo_type', 'article')
@section('seo_canonical', route('blog.show', ['slug' => $post->slug]))
@section('seo_og_image', route('blog.og-image', ['slug' => $post->slug]))
@section('seo_published_time', optional($post->createdAt)->toAtomString())
@section('seo_modified_time', optional($post->updatedAt)->toAtomString())

@push('head')
  <link rel="stylesheet" href="/css/blog-show.css?v={{ $blogShowCssVersion }}">
  <link rel="stylesheet" href="/css/blog-archive.css?v={{ $blogArchiveCssVersion }}">
@endpush
@push('schema')
  @include('components.schema.blog-post')
@endpush

@section('content')
<article class="article-page">
  <header class="article-header">
    <div class="article-intro-container{{ $featuredImage ? '' : ' article-intro-container--text-only' }}">
      <div class="article-title-column">
        <nav class="article-breadcrumb" aria-label="Breadcrumb">
          <a href="{{ route('blog.index') }}">Blog</a>
          <span aria-hidden="true">/</span>
          @if($post->category)
            <a href="/kategori/{{ $post->category->slug }}">{{ $post->category->name }}</a>
          @else
            <span>Artikel</span>
          @endif
        </nav>

        <p class="article-eyebrow">{{ $post->category->name ?? 'Wawasan Lanyard' }}</p>
        <h1>{{ $post->title }}</h1>
        <div class="article-meta">
          <span>{{ $authorName }}</span>
          <span aria-hidden="true">|</span>
          <time datetime="{{ \Carbon\Carbon::parse($post->createdAt)->toDateString() }}">{{ \Carbon\Carbon::parse($post->createdAt)->translatedFormat('j F Y') }}</time>
          <span aria-hidden="true">|</span>
          <span>{{ $readingTime }} menit baca</span>
        </div>
      </div>

      @if($featuredImage)
        <figure class="article-hero-media">
          <img
            src="{{ $featuredImage }}"
            @if($featuredSrcset) srcset="{{ $featuredSrcset }}" sizes="(min-width: 1024px) 560px, calc(100vw - 32px)" @endif
            alt="{{ $post->title }}"
            width="720"
            height="450"
            fetchpriority="high"
            decoding="async"
          >
        </figure>
      @endif
    </div>
  </header>

  <div class="article-wide-container article-body-wrap">
    <aside class="article-sidebar" aria-label="Navigasi dan berbagi artikel">
      <div class="article-sidebar__inner">
        @if($tocHtml !== '')
          {!! $tocHtml !!}
        @endif

        @include('components.article-share', ['class' => 'article-share--sidebar'])

        @if($relatedPosts->isNotEmpty())
          <section class="article-related-sidebar" aria-labelledby="article-related-sidebar-title">
            <div class="article-related-sidebar__heading">
              <h2 id="article-related-sidebar-title">Artikel terkait</h2>
            </div>
            <div class="article-related-sidebar__list">
              @foreach($relatedPosts as $related)
                <x-blog.archive-card :post="$related" variant="sidebar" />
              @endforeach
            </div>
          </section>
        @endif
      </div>
    </aside>

    <div class="article-main-column">
    <div class="article-content">
      {!! $processedContent !!}
    </div>

    @include('components.article-share', ['class' => 'article-share--mobile'])

    @if($parsedTags->isNotEmpty())
      <nav class="article-tags" aria-label="Tag artikel">
        <span>Topik</span>
        <div>
          @foreach($parsedTags as $tag)
            <a href="/tag/{{ Str::slug($tag) }}">{{ $tag }}</a>
          @endforeach
        </div>
      </nav>
    @endif

    <section class="article-author" aria-labelledby="article-author-title">
      <a href="/blog/penulis/{{ $authorSlug }}" aria-label="Lihat artikel oleh {{ $authorName }}">
        @if($authorAvatar)
          <img src="{{ $authorAvatar }}" alt="{{ $authorName }}" width="56" height="56" loading="lazy" decoding="async">
        @else
          <span aria-hidden="true">{{ strtoupper(mb_substr($authorName, 0, 1)) }}</span>
        @endif
      </a>
      <div>
        <p>Ditulis oleh</p>
        <h2 id="article-author-title"><a href="/blog/penulis/{{ $authorSlug }}">{{ $authorName }}</a></h2>
        @if($authorBio)<p>{{ $authorBio }}</p>@endif
      </div>
    </section>

    <section class="article-comments" id="comments" aria-labelledby="comments-title">
      <div class="article-section-heading">
        <p>Diskusi</p>
        <h2 id="comments-title">{{ $comments->count() }} Komentar</h2>
      </div>

      @if(session('comment_sent'))
        <div class="comment-notice" role="status">{{ session('comment_sent') }}</div>
      @endif

      <form class="comment-form" action="{{ route('blog.comment', ['slug' => $post->slug]) }}" method="POST">
        @csrf
        <h3>Tinggalkan balasan</h3>
        <div class="comment-form__row">
          <div>
            <label for="name">Nama</label>
            <input id="name" type="text" name="name" required maxlength="100" value="{{ old('name') }}" autocomplete="name">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
          </div>
          <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" required maxlength="190" value="{{ old('email') }}" autocomplete="email">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
          </div>
        </div>
        <div>
          <label for="content">Komentar</label>
          <textarea id="content" name="content" rows="5" required maxlength="2000">{{ old('content') }}</textarea>
          @error('content')<p class="field-error">{{ $message }}</p>@enderror
        </div>
        <div class="comment-form__footer">
          <p>Komentar akan ditinjau sebelum ditampilkan.</p>
          <button type="submit" data-button-motion><span>Kirim Komentar</span></button>
        </div>
      </form>

      @if($comments->isEmpty())
        <div class="comments-empty">
          <p>Belum ada diskusi.</p>
          <span>Jadilah yang pertama membagikan pendapat atau pertanyaan.</span>
        </div>
      @else
        <div class="comments-list">
          @foreach($comments as $comment)
            <article class="comment-item">
              <span class="comment-avatar" aria-hidden="true">{{ strtoupper(mb_substr($comment->name, 0, 1)) }}</span>
              <div>
                <header><strong>{{ $comment->name }}</strong><time datetime="{{ $comment->createdAt->toDateString() }}">{{ $comment->createdAt->translatedFormat('j F Y') }}</time></header>
                <p>{{ $comment->content }}</p>
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </section>
    </div>
  </div>

  @if($relatedPosts->isNotEmpty())
    <section class="related-articles related-articles--bottom" aria-labelledby="related-title">
      <div class="article-wide-container">
        <div class="article-section-heading">
          <p>Baca Berikutnya</p>
          <h2 id="related-title">Artikel Terkait</h2>
        </div>
        <div class="related-grid">
          @foreach($relatedPosts as $related)
            <x-blog.archive-card :post="$related" variant="compact" />
          @endforeach
        </div>
      </div>
    </section>
  @endif
</article>

@include('components.footer')
@endsection
