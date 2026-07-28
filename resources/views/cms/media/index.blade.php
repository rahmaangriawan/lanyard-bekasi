@extends('layouts.meridian')

@section('title', 'Media Library')

@php
    $typeLabels = [
        'all' => 'Semua',
        'image' => 'Gambar',
        'pdf' => 'PDF',
        'other' => 'Lainnya',
    ];
    $formatBytes = function (?int $bytes): string {
        if (!$bytes) {
            return '0 KB';
        }
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return max(1, round($bytes / 1024)) . ' KB';
    };
    $displayName = function (string $filename): string {
        $name = pathinfo($filename, PATHINFO_FILENAME) ?: $filename;
        $name = preg_replace('/[-_]+/', ' ', $name) ?: $name;
        return \Illuminate\Support\Str::headline($name);
    };
@endphp

@push('head')
  <style>
    .media-dropzone {
      position: relative;
      min-height: 15rem;
      display: grid;
      place-items: center;
      border: 1px dashed color-mix(in srgb, var(--color-primary) 34%, var(--color-border));
      border-radius: var(--radius-2xl);
      background: color-mix(in srgb, var(--color-primary) 5%, var(--color-card));
      padding: 2.5rem;
      text-align: center;
      cursor: pointer;
      transition: border-color .18s ease, background-color .18s ease, transform .18s ease;
    }
    .media-dropzone:hover,
    .media-dropzone.is-dragging {
      border-color: var(--color-primary);
      background: color-mix(in srgb, var(--color-primary) 9%, var(--color-card));
      transform: translateY(-1px);
    }
    .media-dropzone.is-uploading {
      pointer-events: none;
      opacity: .8;
    }
    .media-dropzone input[type="file"] {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
      z-index: 2;
    }
    .media-dropzone__content {
      position: relative;
      z-index: 1;
      display: grid;
      justify-items: center;
      align-content: center;
      gap: .65rem;
      max-width: 32rem;
      margin-inline: auto;
    }
    .media-dropzone__content .media-card__icon {
      margin: 0;
    }
    .media-dropzone__title {
      margin: .35rem 0 0;
      color: var(--color-foreground);
      font-size: 1.25rem;
      font-weight: 800;
      line-height: 1.2;
    }
    .media-dropzone__copy {
      margin: 0;
      color: var(--color-muted-foreground);
      font-size: .9rem;
      line-height: 1.6;
    }
    .media-upload-loading {
      display: none;
      align-items: center;
      gap: .5rem;
      justify-content: center;
      margin-top: 1rem;
      color: var(--color-primary);
      font-weight: 700;
    }
    .media-dropzone.is-uploading .media-upload-loading {
      display: flex;
    }
    .media-spinner {
      width: 1rem;
      height: 1rem;
      border-radius: 999px;
      border: 2px solid color-mix(in srgb, var(--color-primary) 20%, transparent);
      border-top-color: var(--color-primary);
      animation: media-spin .8s linear infinite;
    }
    @keyframes media-spin {
      to { transform: rotate(360deg); }
    }
    .media-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(14rem, 1fr));
      gap: 1rem;
    }
    .media-card__preview {
      aspect-ratio: 4 / 3;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-background);
      display: grid;
      place-items: center;
      overflow: hidden;
    }
    .media-card__preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
    .media-card__icon {
      width: 3rem;
      height: 3rem;
      border-radius: 999px;
      display: grid;
      place-items: center;
      color: var(--meridian-accent);
      background: color-mix(in srgb, var(--meridian-accent) 12%, transparent);
    }
    .media-actions {
      display: flex;
      gap: .45rem;
      align-items: center;
    }
    .media-action-button {
      width: 2.15rem;
      height: 2.15rem;
      min-height: 2.15rem;
      padding: 0;
      display: inline-grid;
      place-items: center;
      border-radius: .75rem;
    }
    .media-library-footer {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .media-library-summary {
      margin: 0;
      color: var(--color-muted-foreground);
      font-size: .78rem;
      line-height: 1.45;
    }
    .media-pagination {
      display: grid;
      grid-template-columns: minmax(7.5rem, auto) auto minmax(7.5rem, auto);
      align-items: center;
      gap: .65rem;
      margin-inline-start: auto;
    }
    .media-pagination__button {
      min-height: 2.5rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      white-space: nowrap;
    }
    .media-pagination__button[aria-disabled="true"] {
      pointer-events: none;
      opacity: .42;
    }
    .media-pagination__status {
      min-width: 5rem;
      padding: .55rem .75rem;
      border: 1px solid var(--color-border);
      border-radius: .75rem;
      background: var(--color-surface-2);
      color: var(--color-muted-foreground);
      text-align: center;
      font-size: .78rem;
      line-height: 1;
      white-space: nowrap;
    }
    .media-pagination__status strong {
      color: var(--color-foreground);
      font-size: .9rem;
    }
    @media (max-width: 640px) {
      .media-library-footer {
        align-items: stretch;
      }
      .media-library-summary {
        width: 100%;
        text-align: center;
      }
      .media-pagination {
        width: 100%;
        grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
        margin-inline-start: 0;
      }
      .media-pagination__button {
        min-width: 0;
        padding-inline: .75rem;
      }
    }
  </style>
@endpush

@section('content')
  <header class="page__header">
    <div class="page__headline">
      <h1 class="page__title">Media Library</h1>
      <p class="page__description">{{ $readOnly ? 'Lihat, preview, dan salin URL aset visual untuk konten website.' : 'Unggah, preview, salin URL, dan hapus aset visual untuk konten website.' }}</p>
    </div>
  </header>

  <div class="page__body">
    @unless($readOnly)
    <section class="page__section">
      <form class="media-dropzone" method="post" action="{{ route('cms.store', 'media') }}" enctype="multipart/form-data" data-media-upload-form>
        @csrf
        <input type="file" name="file" accept="image/*,application/pdf" required data-media-file-input aria-label="Upload media" />
        <div class="media-dropzone__content">
          <div class="media-card__icon">
            @include('cms.partials.icon', ['name' => 'media'])
          </div>
          <h2 class="media-dropzone__title">Upload Media Baru</h2>
          <p class="media-dropzone__copy">Klik kotak ini atau seret gambar/PDF ke sini. Maksimal 5MB per file.</p>
          <div class="media-upload-loading" data-media-upload-loading>
            <span class="media-spinner"></span>
            <span>Mengupload file...</span>
          </div>
        </div>
      </form>
    </section>
    @endunless

    <section class="page__section">
      <div class="card">
        <div class="card__header flex-wrap gap-3">
          <form class="flex w-full flex-col gap-2 md:flex-row" method="get" action="{{ route('cms.index', 'media') }}">
            <div class="input-group md:w-72">
              <span class="input-group__text">@include('cms.partials.icon', ['name' => 'search'])</span>
              <input class="input" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari filename atau URL..." />
            </div>
            <select class="input md:w-40" name="file_type">
              @foreach($typeLabels as $value => $label)
                <option value="{{ $value }}" @selected($filters['file_type'] === $value)>{{ $label }}</option>
              @endforeach
            </select>
            <button class="button button--outline button--neutral" type="submit">Filter</button>
          </form>
        </div>

        <div class="card__body">
          <div class="media-grid">
            @forelse($items as $item)
              @php
                  $isImage = str_starts_with((string) $item->mimetype, 'image/');
                  $isPdf = $item->mimetype === 'application/pdf';
              @endphp
              <article class="card media-card">
                <div class="card__body">
                  <div class="media-card__preview">
                    @if($isImage)
                      <img src="{{ $item->thumbnail_src }}" alt="{{ $item->filename }}" loading="lazy" />
                    @else
                      <div class="text-center">
                        <div class="mx-auto media-card__icon">
                          @include('cms.partials.icon', ['name' => $isPdf ? 'file' : 'media'])
                        </div>
                        <div class="mt-3 text-sm font-bold">{{ $isPdf ? 'PDF' : 'FILE' }}</div>
                      </div>
                    @endif
                  </div>

                  <h2 class="mt-4 line-clamp-2 font-bold" title="{{ $item->filename }}">{{ $displayName($item->filename) }}</h2>
                  <div class="mt-2 flex flex-wrap gap-2">
                    <span class="badge badge--light">{{ $item->mimetype }}</span>
                    <span class="badge badge--light">{{ $formatBytes($item->size) }}</span>
                  </div>
                  <p class="mt-2 text-xs text-muted-foreground">{{ optional($item->createdAt)->translatedFormat('d M Y H:i') }}</p>

                  <div class="media-actions mt-4">
                    <button class="button button--sm button--primary media-action-button" type="button" data-copy-url="{{ $item->preview_url }}" title="Copy URL" aria-label="Copy URL {{ $item->filename }}">
                      <svg xmlns="http://www.w3.org/2000/svg" width="1.05em" height="1.05em" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M9 18q-.825 0-1.412-.587T7 16V4q0-.825.588-1.412T9 2h9q.825 0 1.413.588T20 4v12q0 .825-.587 1.413T18 18zm-4 4q-.825 0-1.412-.587T3 20V7h2v13h10v2z"/></svg>
                    </button>
                    <a class="button button--sm button--outline button--neutral media-action-button" href="{{ $item->preview_url }}" target="_blank" rel="noopener" title="Buka file" aria-label="Buka {{ $item->filename }}">
                      <svg xmlns="http://www.w3.org/2000/svg" width="1.05em" height="1.05em" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M14 3h7v7h-2V6.41l-9.29 9.3l-1.42-1.42l9.3-9.29H14z"/><path fill="currentColor" d="M5 5h6v2H5v12h12v-6h2v6q0 .825-.587 1.413T17 21H5q-.825 0-1.412-.587T3 19V7q0-.825.588-1.412T5 5"/></svg>
                    </a>
                    @unless($readOnly)
                      <form method="post" action="{{ route('cms.destroy', ['media', $item->id]) }}" onsubmit="return confirm('Hapus media ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="button button--sm button--outline button--danger media-action-button" type="submit" title="Hapus file" aria-label="Hapus {{ $item->filename }}">
                          <svg xmlns="http://www.w3.org/2000/svg" width="1.05em" height="1.05em" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M7 21q-.825 0-1.412-.587T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.587 1.413T17 21zm2-4h2V8H9zm4 0h2V8h-2z"/></svg>
                        </button>
                      </form>
                    @endunless
                  </div>
                </div>
              </article>
            @empty
              <div class="card">
                <div class="card__body text-center">
                  <div class="mx-auto media-card__icon">@include('cms.partials.icon', ['name' => 'media'])</div>
                  <h2 class="mt-4 text-lg font-bold">Belum ada media</h2>
                  <p class="mt-2 text-sm text-muted-foreground">Upload file pertama untuk mulai mengelola aset website.</p>
                </div>
              </div>
            @endforelse
          </div>
        </div>

        <div class="card__footer media-library-footer">
          <p class="media-library-summary">
            Menampilkan {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} media
          </p>

          @if($items->hasPages())
            <nav class="media-pagination" aria-label="Navigasi halaman Media Library">
              @if($items->onFirstPage())
                <span class="button button--outline button--neutral media-pagination__button" aria-disabled="true">
                  <span aria-hidden="true">&larr;</span>
                  <span>Sebelumnya</span>
                </span>
              @else
                <a class="button button--outline button--neutral media-pagination__button" href="{{ $items->previousPageUrl() }}" rel="prev">
                  <span aria-hidden="true">&larr;</span>
                  <span>Sebelumnya</span>
                </a>
              @endif

              <span class="media-pagination__status" aria-current="page">
                <strong>{{ $items->currentPage() }}</strong> / {{ $items->lastPage() }}
              </span>

              @if($items->hasMorePages())
                <a class="button button--primary media-pagination__button" href="{{ $items->nextPageUrl() }}" rel="next">
                  <span>Berikutnya</span>
                  <span aria-hidden="true">&rarr;</span>
                </a>
              @else
                <span class="button button--outline button--neutral media-pagination__button" aria-disabled="true">
                  <span>Berikutnya</span>
                  <span aria-hidden="true">&rarr;</span>
                </span>
              @endif
            </nav>
          @endif
        </div>
      </div>
    </section>
  </div>
@endsection

@push('scripts')
  <script>
    @unless($readOnly)
    document.querySelectorAll('[data-media-upload-form]').forEach(function (form) {
      var input = form.querySelector('[data-media-file-input]');

      function submitUpload() {
        if (!input || !input.files || input.files.length === 0) return;
        form.classList.add('is-uploading');
        form.submit();
      }

      input.addEventListener('change', submitUpload);

      ['dragenter', 'dragover'].forEach(function (eventName) {
        form.addEventListener(eventName, function (event) {
          event.preventDefault();
          form.classList.add('is-dragging');
        });
      });

      ['dragleave', 'drop'].forEach(function (eventName) {
        form.addEventListener(eventName, function (event) {
          event.preventDefault();
          form.classList.remove('is-dragging');
        });
      });

      form.addEventListener('drop', function (event) {
        if (!event.dataTransfer || !event.dataTransfer.files.length) return;
        input.files = event.dataTransfer.files;
        submitUpload();
      });

      form.addEventListener('submit', function () {
        form.classList.add('is-uploading');
      });
    });
    @endunless

    document.querySelectorAll('[data-copy-url]').forEach(function (button) {
      button.addEventListener('click', function () {
        var value = button.getAttribute('data-copy-url') || '';
        navigator.clipboard.writeText(value).then(function () {
          var original = button.innerHTML;
          button.textContent = 'OK';
          setTimeout(function () { button.innerHTML = original; }, 1200);
        });
      });
    });
  </script>
@endpush
