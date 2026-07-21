@extends('layouts.meridian')

@section('title', $title)

@php
    $gaDaily = collect($gaData['daily'] ?? [])->filter(fn ($row) => filled($row['date'] ?? null))->sortBy('date')->values();
    $gscDaily = collect($gscData['daily'] ?? [])->filter(fn ($row) => filled($row['date'] ?? null))->sortBy('date')->values();
    $topPages = collect($gaData['topPages'] ?? [])->take(8);
    $maxPageViews = max(1, (int) ($topPages->max('views') ?? 1));
@endphp

@push('head')
  <style>
    .analytics-connection-bar {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: .65rem;
      padding: .8rem 1rem;
      border: 1px solid var(--color-border);
      border-radius: var(--radius-lg);
      background: var(--color-surface-2);
    }
    .analytics-connection {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      min-width: 0;
      color: var(--color-muted-foreground);
      font-size: .78rem;
    }
    .analytics-connection + .analytics-connection {
      padding-inline-start: .65rem;
      border-inline-start: 1px solid var(--color-border);
    }
    .analytics-connection__state {
      flex: 0 0 auto;
      padding: .28rem .5rem;
      border-radius: 999px;
      color: var(--color-success-emphasis);
      background: color-mix(in srgb, var(--color-success) 12%, transparent);
      font-weight: 800;
    }
    .analytics-connection__state.is-warning {
      color: var(--color-warning-emphasis);
      background: color-mix(in srgb, var(--color-warning) 14%, transparent);
    }
    .analytics-connection__value {
      max-width: 18rem;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .analytics-kpis {
      display: grid;
      grid-template-columns: repeat(6, minmax(0, 1fr));
      border: 1px solid var(--color-border);
      border-radius: var(--radius-lg);
      background: var(--color-surface);
      overflow: hidden;
    }
    .analytics-kpi {
      min-width: 0;
      padding: 1.15rem 1.2rem;
    }
    .analytics-kpi + .analytics-kpi {
      border-inline-start: 1px solid var(--color-border);
    }
    .analytics-kpi__source {
      color: var(--color-muted-foreground);
      font-size: .68rem;
      font-weight: 700;
    }
    .analytics-kpi__value {
      margin: .35rem 0 .2rem;
      color: var(--color-foreground);
      font-size: clamp(1.5rem, 2.4vw, 2.15rem);
      font-weight: 400;
      line-height: 1;
      letter-spacing: -.035em;
    }
    .analytics-kpi__label {
      color: var(--color-muted-foreground);
      font-size: .78rem;
      line-height: 1.3;
    }
    .analytics-layout {
      display: grid;
      grid-template-columns: minmax(0, 1.65fr) minmax(18rem, .75fr);
      gap: 1rem;
      align-items: stretch;
    }
    .analytics-chart-card .card__header {
      align-items: flex-start;
    }
    .analytics-chart-card .card__body {
      padding-top: .25rem;
    }
    .analytics-chart-title {
      margin: 0;
      color: var(--color-foreground);
      font-size: 1rem;
      font-weight: 800;
    }
    .analytics-chart-subtitle {
      margin: .25rem 0 0;
      color: var(--color-muted-foreground);
      font-size: .78rem;
    }
    .analytics-legend {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: .8rem;
      margin-inline-start: auto;
      color: var(--color-muted-foreground);
      font-size: .72rem;
    }
    .analytics-legend__item {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
    }
    .analytics-legend__line {
      width: 1.4rem;
      height: 2px;
      border-radius: 999px;
      background: var(--legend-color);
    }
    .analytics-chart-wrap {
      width: 100%;
      min-height: 18rem;
    }
    .analytics-chart {
      width: 100%;
      height: auto;
      display: block;
      overflow: visible;
    }
    .analytics-chart__grid {
      stroke: var(--color-border);
      stroke-width: 1;
    }
    .analytics-chart__axis-label,
    .analytics-chart__date-label {
      fill: var(--color-muted-foreground);
      font-family: inherit;
      font-size: 10px;
    }
    .analytics-chart__line {
      stroke-width: 2.5;
      stroke-linecap: round;
      stroke-linejoin: round;
      vector-effect: non-scaling-stroke;
    }
    .analytics-chart__point {
      stroke: var(--color-surface);
      stroke-width: 2;
      opacity: .8;
    }
    .analytics-chart-empty {
      min-height: 18rem;
      display: grid;
      place-items: center;
      color: var(--color-muted-foreground);
      text-align: center;
    }
    .analytics-page-list {
      display: grid;
      gap: 1rem;
    }
    .analytics-page-item {
      display: grid;
      gap: .5rem;
    }
    .analytics-page-item__top {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      gap: .75rem;
    }
    .analytics-page-item__path {
      min-width: 0;
      overflow: hidden;
      color: var(--color-foreground);
      font-size: .8rem;
      font-weight: 700;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .analytics-page-item__value {
      flex: 0 0 auto;
      color: var(--color-muted-foreground);
      font-size: .75rem;
      font-variant-numeric: tabular-nums;
    }
    .analytics-page-item__track {
      height: .3rem;
      border-radius: 999px;
      background: var(--color-surface-2);
      overflow: hidden;
    }
    .analytics-page-item__bar {
      height: 100%;
      min-width: .3rem;
      border-radius: inherit;
      background: var(--meridian-accent);
    }
    @media (max-width: 1100px) {
      .analytics-kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); }
      .analytics-kpi:nth-child(4) { border-inline-start: 0; border-top: 1px solid var(--color-border); }
      .analytics-kpi:nth-child(n + 5) { border-top: 1px solid var(--color-border); }
      .analytics-layout { grid-template-columns: 1fr; }
    }
    @media (max-width: 680px) {
      .analytics-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .analytics-kpi:nth-child(odd) { border-inline-start: 0; }
      .analytics-kpi:nth-child(n + 3) { border-top: 1px solid var(--color-border); }
      .analytics-connection { width: 100%; }
      .analytics-connection + .analytics-connection { padding-inline-start: 0; border-inline-start: 0; }
      .analytics-legend { width: 100%; margin-inline-start: 0; }
      .analytics-chart-wrap, .analytics-chart-empty { min-height: 13rem; }
    }
  </style>
@endpush

@section('content')
  <header class="page__header">
    <div class="page__headline">
      <h1 class="page__title">{{ $title }}</h1>
      <p class="page__description">{{ $description }}</p>
    </div>
    <div class="page__actions">
      <a class="button button--outline button--neutral" href="{{ route('cms.index', 'settings') }}">Buka Settings</a>
    </div>
  </header>

  <div class="page__body">
    @if(!empty($integrationErrors))
      <div class="alert alert--danger" role="alert">
        <strong>Sebagian data belum dapat dibaca.</strong>
        <ul class="mt-2">
          @foreach($integrationErrors as $integrationError)
            <li>{{ $integrationError }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="analytics-connection-bar" aria-label="Status integrasi analitik">
      @foreach($connections as $connection)
        <div class="analytics-connection">
          <span class="analytics-connection__state {{ ($connection['status'] ?? '') === 'Aktif' ? '' : 'is-warning' }}">{{ $connection['status'] }}</span>
          <strong>{{ $connection['label'] }}</strong>
          <span class="analytics-connection__value" title="{{ $connection['value'] }}">{{ $connection['value'] }}</span>
        </div>
      @endforeach
    </div>

    @if(!empty($metrics))
      <section class="analytics-kpis" aria-label="Ringkasan metrik 30 hari">
        @foreach($metrics as $metric)
          <div class="analytics-kpi">
            <span class="analytics-kpi__source">{{ $metric['source'] }}</span>
            <p class="analytics-kpi__value">{{ $metric['value'] }}</p>
            <span class="analytics-kpi__label">{{ $metric['label'] }}</span>
          </div>
        @endforeach
      </section>
    @endif

    <section class="analytics-layout">
      <article class="card analytics-chart-card">
        <div class="card__header">
          <div>
            <h2 class="analytics-chart-title">Traffic Website</h2>
            <p class="analytics-chart-subtitle">Pengguna aktif dan page views dari GA4 selama 30 hari.</p>
          </div>
          <div class="analytics-legend" aria-label="Legenda grafik GA4">
            <span class="analytics-legend__item"><span class="analytics-legend__line" style="--legend-color: var(--meridian-accent)"></span>Pengguna aktif</span>
            <span class="analytics-legend__item"><span class="analytics-legend__line" style="--legend-color: var(--color-success)"></span>Page views</span>
          </div>
        </div>
        <div class="card__body">
          @include('cms.components.analytics-line-chart', [
            'labels' => $gaDaily->pluck('date')->all(),
            'series' => [
              ['name' => 'pengguna', 'color' => 'var(--meridian-accent)', 'data' => $gaDaily->pluck('activeUsers')->all()],
              ['name' => 'page views', 'color' => 'var(--color-success)', 'data' => $gaDaily->pluck('pageViews')->all()],
            ],
            'emptyText' => 'Data tren GA4 belum tersedia.',
          ])
        </div>
      </article>

      <article class="card">
        <div class="card__header">
          <div>
            <h2 class="analytics-chart-title">Halaman Teratas</h2>
            <p class="analytics-chart-subtitle">Urutan berdasarkan page views GA4.</p>
          </div>
        </div>
        <div class="card__body">
          @if($topPages->isEmpty())
            <div class="analytics-chart-empty"><p>Belum ada data halaman teratas.</p></div>
          @else
            <div class="analytics-page-list">
              @foreach($topPages as $page)
                <div class="analytics-page-item">
                  <div class="analytics-page-item__top">
                    <span class="analytics-page-item__path" title="{{ $page['title'] ?: $page['path'] }}">{{ $page['path'] ?: '/' }}</span>
                    <span class="analytics-page-item__value">{{ number_format($page['views'] ?? 0, 0, ',', '.') }}</span>
                  </div>
                  <div class="analytics-page-item__track" aria-hidden="true">
                    <div class="analytics-page-item__bar" style="width: {{ max(2, (($page['views'] ?? 0) / $maxPageViews) * 100) }}%"></div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </article>
    </section>

    <section class="page__section">
      <article class="card analytics-chart-card">
        <div class="card__header">
          <div>
            <h2 class="analytics-chart-title">Performa Pencarian Organik</h2>
            <p class="analytics-chart-subtitle">Klik dan impresi harian dari Google Search Console.</p>
          </div>
          <div class="analytics-legend" aria-label="Legenda grafik Search Console">
            <span class="analytics-legend__item"><span class="analytics-legend__line" style="--legend-color: var(--meridian-accent)"></span>Klik</span>
            <span class="analytics-legend__item"><span class="analytics-legend__line" style="--legend-color: var(--color-warning-emphasis)"></span>Impresi</span>
          </div>
        </div>
        <div class="card__body">
          @include('cms.components.analytics-line-chart', [
            'labels' => $gscDaily->pluck('date')->all(),
            'series' => [
              ['name' => 'klik', 'color' => 'var(--meridian-accent)', 'data' => $gscDaily->pluck('clicks')->all()],
              ['name' => 'impresi', 'color' => 'var(--color-warning-emphasis)', 'data' => $gscDaily->pluck('impressions')->all()],
            ],
            'emptyText' => 'Data tren Search Console belum tersedia.',
          ])
        </div>
      </article>
    </section>
  </div>
@endsection
