@props(['posts'])

@if($posts->hasPages())
  <nav class="blog-archive-pagination" aria-label="Navigasi halaman artikel">
    <div>
      @if($posts->onFirstPage())
        <span class="blog-archive-pagination__link is-disabled" aria-disabled="true">
          <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
          <span>Sebelumnya</span>
        </span>
      @else
        <a class="blog-archive-pagination__link" href="{{ $posts->previousPageUrl() }}" rel="prev">
          <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
          <span>Sebelumnya</span>
        </a>
      @endif
    </div>

    <p>Halaman <strong>{{ $posts->currentPage() }}</strong> dari {{ $posts->lastPage() }}</p>

    <div>
      @if($posts->hasMorePages())
        <a class="blog-archive-pagination__link" href="{{ $posts->nextPageUrl() }}" rel="next">
          <span>Berikutnya</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      @else
        <span class="blog-archive-pagination__link is-disabled" aria-disabled="true">
          <span>Berikutnya</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </span>
      @endif
    </div>
  </nav>
@endif
