@props([
  'post',
  'variant' => 'default',
])

@php
  use App\Support\AssetUrl;
  use App\Support\ResponsiveImage;
  use Illuminate\Support\Str;

  $postImage = AssetUrl::relative($post->featuredImage);
  $postSrcset = ResponsiveImage::srcset($postImage);
  $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags((string) $post->content)));
  $excerpt = $post->metaDescription ?: Str::limit($plainContent, 165);
  $wordCount = $plainContent === '' ? 0 : count(preg_split('/\s+/', $plainContent));
  $readingTime = max(1, (int) ceil($wordCount / 200));
  $authorName = $post->author?->name ?: 'Tim Lanyard Bekasi';
  $authorSlug = $post->author?->slug;
  $postUrl = route('blog.show', ['slug' => $post->slug]);
@endphp

<article class="blog-archive-card blog-archive-card--{{ $variant }}">
  <a class="blog-archive-card__media" href="{{ $postUrl }}" aria-label="Baca {{ $post->title }}">
    @if($postImage)
      <img
        src="{{ $postImage }}"
        @if($postSrcset) srcset="{{ $postSrcset }}" sizes="(min-width: 1024px) 300px, (min-width: 700px) 38vw, 100vw" @endif
        alt="{{ $post->title }}"
        width="640"
        height="400"
        loading="lazy"
        decoding="async"
      >
    @else
      <span class="blog-archive-card__fallback material-symbols-outlined" aria-hidden="true">article</span>
    @endif
  </a>

  <div class="blog-archive-card__content">
    <div class="blog-archive-card__meta">
      <time datetime="{{ \Carbon\Carbon::parse($post->createdAt)->toDateString() }}">
        {{ \Carbon\Carbon::parse($post->createdAt)->translatedFormat('j M Y') }}
      </time>
      <span aria-hidden="true">|</span>
      <span>{{ $readingTime }} menit baca</span>
    </div>
    <h3><a href="{{ $postUrl }}">{{ $post->title }}</a></h3>
    <p>{{ $excerpt ?: 'Baca wawasan terbaru dari Lanyard Bekasi.' }}</p>
    <div class="blog-archive-card__footer">
      @if($authorSlug)
        <a class="blog-archive-card__author" href="{{ route('blog.author', ['slug' => $authorSlug]) }}">{{ $authorName }}</a>
      @else
        <span class="blog-archive-card__author">{{ $authorName }}</span>
      @endif
      <a class="blog-archive-card__link" href="{{ $postUrl }}" aria-label="Baca artikel {{ $post->title }}">
        <span>Baca artikel</span>
        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
      </a>
    </div>
  </div>
</article>
