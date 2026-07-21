@extends('layouts.meridian')

@section('title', 'Laporan 404')

@push('head')
  <style>
    .not-found-report-footer {
      align-items: center;
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: space-between;
    }
    .not-found-report-summary {
      margin: 0;
      color: var(--color-muted-foreground);
      font-size: .78rem;
      line-height: 1.45;
    }
    .not-found-pagination {
      display: grid;
      grid-template-columns: minmax(7.5rem, auto) auto minmax(7.5rem, auto);
      align-items: center;
      gap: .65rem;
    }
    .not-found-pagination__button {
      display: inline-flex;
      min-height: 2.5rem;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      white-space: nowrap;
    }
    .not-found-pagination__button[aria-disabled="true"] {
      pointer-events: none;
      opacity: .42;
    }
    .not-found-pagination__status {
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
    .not-found-pagination__status strong {
      color: var(--color-foreground);
      font-size: .9rem;
    }
    @media (max-width: 640px) {
      .not-found-report-footer { align-items: stretch; }
      .not-found-report-summary { width: 100%; text-align: center; }
      .not-found-pagination {
        width: 100%;
        grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
      }
      .not-found-pagination__button { min-width: 0; padding-inline: .75rem; }
    }
  </style>
@endpush

@section('content')
  <header class="page__header">
    <div class="page__headline">
      <h1 class="page__title">Laporan URL 404</h1>
      <p class="page__description">URL halaman publik yang tidak ditemukan. Data disimpan maksimal 90 hari dan dibatasi satu catatan per URL per menit.</p>
    </div>
  </header>

  <div class="page__body">
    <section class="page__section">
      <div class="card">
        <div class="card__header flex-wrap gap-3">
          <form class="flex flex-wrap items-end gap-3" method="get" action="{{ route('cms.not-found-report') }}">
            <div>
              <label class="form-label" for="days">Periode</label>
              <select id="days" class="input" name="days">
                @foreach([7 => '7 hari', 30 => '30 hari', 90 => '90 hari'] as $value => $label)
                  <option value="{{ $value }}" @selected($days === $value)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="form-label" for="q">Cari URL</label>
              <input id="q" class="input" type="search" name="q" value="{{ $search }}" maxlength="512" placeholder="Contoh: /blog/artikel-lama" />
            </div>
            <button class="button button--primary" type="submit">Terapkan</button>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table--hover table--align-middle">
            <thead class="table__head--alt">
              <tr>
                <th scope="col">URL</th>
                <th scope="col" class="text-end">Kejadian</th>
                <th scope="col">Pertama terlihat</th>
                <th scope="col">Terakhir terlihat</th>
                <th scope="col" class="text-end"><span class="sr-only">Aksi</span></th>
              </tr>
            </thead>
            <tbody>
              @forelse($items as $item)
                <tr>
                  <td><code class="text-sm font-semibold">{{ $item->path }}</code></td>
                  <td class="text-end font-bold">{{ number_format($item->occurrences, 0, ',', '.') }}</td>
                  <td>{{ \Illuminate\Support\Carbon::parse($item->firstSeenAt)->translatedFormat('d M Y H:i') }}</td>
                  <td>{{ \Illuminate\Support\Carbon::parse($item->lastSeenAt)->translatedFormat('d M Y H:i') }}</td>
                  <td class="text-end">
                    <a class="button button--sm button--outline button--neutral" href="{{ route('cms.create', 'redirects') }}?sourcePath={{ urlencode($item->path) }}">Buat Redirect</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-10 text-center text-muted-foreground">Belum ada URL 404 yang tercatat pada periode ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card__footer not-found-report-footer">
          <p class="not-found-report-summary">
            Menampilkan {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} URL
          </p>

          @if($items->hasPages())
            <nav class="not-found-pagination" aria-label="Navigasi halaman laporan URL 404">
              @if($items->onFirstPage())
                <span class="button button--outline button--neutral not-found-pagination__button" aria-disabled="true">
                  <span aria-hidden="true">&larr;</span>
                  <span>Sebelumnya</span>
                </span>
              @else
                <a class="button button--outline button--neutral not-found-pagination__button" href="{{ $items->previousPageUrl() }}" rel="prev">
                  <span aria-hidden="true">&larr;</span>
                  <span>Sebelumnya</span>
                </a>
              @endif

              <span class="not-found-pagination__status" aria-current="page">
                <strong>{{ $items->currentPage() }}</strong> / {{ $items->lastPage() }}
              </span>

              @if($items->hasMorePages())
                <a class="button button--primary not-found-pagination__button" href="{{ $items->nextPageUrl() }}" rel="next">
                  <span>Berikutnya</span>
                  <span aria-hidden="true">&rarr;</span>
                </a>
              @else
                <span class="button button--outline button--neutral not-found-pagination__button" aria-disabled="true">
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
