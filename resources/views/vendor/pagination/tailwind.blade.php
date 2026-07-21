@if ($paginator->hasPages())
  <nav class="flex flex-wrap items-center justify-end gap-2" role="navigation" aria-label="Pagination Navigation">
    @if ($paginator->onFirstPage())
      <span class="button button--outline button--neutral button--sm opacity-50" aria-disabled="true" aria-label="Sebelumnya">
        <span aria-hidden="true">&lsaquo;</span>
        <span>Sebelumnya</span>
      </span>
    @else
      <a class="button button--outline button--neutral button--sm" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">
        <span aria-hidden="true">&lsaquo;</span>
        <span>Sebelumnya</span>
      </a>
    @endif

    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="button button--outline button--neutral button--sm opacity-50" aria-disabled="true">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="button button--primary button--sm" aria-current="page">{{ $page }}</span>
          @else
            <a class="button button--outline button--neutral button--sm" href="{{ $url }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    @if ($paginator->hasMorePages())
      <a class="button button--outline button--neutral button--sm" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">
        <span>Berikutnya</span>
        <span aria-hidden="true">&rsaquo;</span>
      </a>
    @else
      <span class="button button--outline button--neutral button--sm opacity-50" aria-disabled="true" aria-label="Berikutnya">
        <span>Berikutnya</span>
        <span aria-hidden="true">&rsaquo;</span>
      </span>
    @endif
  </nav>
@endif
