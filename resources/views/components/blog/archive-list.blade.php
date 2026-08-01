@props([
  'posts',
  'emptyTitle' => 'Artikel tidak ditemukan',
  'emptyText' => 'Belum ada artikel yang tersedia untuk arsip ini.',
])

@if($posts->isEmpty())
  <div class="blog-archive-empty">
    <span class="material-symbols-outlined" aria-hidden="true">article</span>
    <h2>{{ $emptyTitle }}</h2>
    <p>{{ $emptyText }}</p>
    <a href="{{ route('blog.index') }}">Lihat Semua Artikel</a>
  </div>
@else
  <ol class="blog-archive-list">
    @foreach($posts as $post)
      <li><x-blog.archive-card :post="$post" /></li>
    @endforeach
  </ol>
  <x-blog.archive-pagination :posts="$posts" />
@endif
