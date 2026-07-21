@php
  $labels = $labels ?? [];
  $series = $series ?? [];
  $emptyText = $emptyText ?? 'Belum ada data untuk periode ini.';
    $chartWidth = 800;
    $chartHeight = 280;
    $paddingLeft = 44;
    $paddingRight = 18;
    $paddingTop = 18;
    $paddingBottom = 38;
    $plotWidth = $chartWidth - $paddingLeft - $paddingRight;
    $plotHeight = $chartHeight - $paddingTop - $paddingBottom;
    $allValues = collect($series)->flatMap(fn ($item) => $item['data'] ?? [])->map(fn ($value) => (float) $value);
    $maxValue = max(1, (float) ($allValues->max() ?? 1));
    $pointCount = max(1, count($labels));
    $dateIndexes = array_values(array_unique([0, (int) floor(($pointCount - 1) / 2), $pointCount - 1]));
    $formatDate = function ($value): string {
        try {
            return \Illuminate\Support\Carbon::parse($value)->translatedFormat('d M');
        } catch (\Throwable) {
            return (string) $value;
        }
    };
@endphp

@if($allValues->isEmpty())
  <div class="analytics-chart-empty">
    <p>{{ $emptyText }}</p>
  </div>
@else
  <div class="analytics-chart-wrap">
    <svg class="analytics-chart" viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" role="img" aria-label="Grafik tren 30 hari">
      @for($grid = 0; $grid <= 4; $grid++)
        @php
          $gridY = $paddingTop + (($plotHeight / 4) * $grid);
          $gridValue = (int) round($maxValue * (1 - ($grid / 4)));
        @endphp
        <line class="analytics-chart__grid" x1="{{ $paddingLeft }}" y1="{{ $gridY }}" x2="{{ $chartWidth - $paddingRight }}" y2="{{ $gridY }}" />
        <text class="analytics-chart__axis-label" x="{{ $paddingLeft - 9 }}" y="{{ $gridY + 4 }}" text-anchor="end">{{ number_format($gridValue, 0, ',', '.') }}</text>
      @endfor

      @foreach($series as $seriesIndex => $item)
        @php
          $values = array_values($item['data'] ?? []);
          $color = $item['color'] ?? 'var(--meridian-accent)';
          $points = collect($values)->map(function ($value, $index) use ($paddingLeft, $paddingTop, $plotWidth, $plotHeight, $pointCount, $maxValue) {
              $x = $paddingLeft + ($pointCount > 1 ? ($plotWidth * $index / ($pointCount - 1)) : $plotWidth / 2);
              $y = $paddingTop + $plotHeight - (($plotHeight * (float) $value) / $maxValue);
              return round($x, 2).','.round($y, 2);
          })->implode(' ');
          $areaPoints = $paddingLeft.','.($paddingTop + $plotHeight).' '.$points.' '.($chartWidth - $paddingRight).','.($paddingTop + $plotHeight);
        @endphp

        @if($seriesIndex === 0)
          <polygon points="{{ $areaPoints }}" fill="{{ $color }}" opacity=".08" />
        @endif
        <polyline class="analytics-chart__line" points="{{ $points }}" fill="none" stroke="{{ $color }}" />

        @foreach($values as $index => $value)
          @php
            $x = $paddingLeft + ($pointCount > 1 ? ($plotWidth * $index / ($pointCount - 1)) : $plotWidth / 2);
            $y = $paddingTop + $plotHeight - (($plotHeight * (float) $value) / $maxValue);
          @endphp
          <circle class="analytics-chart__point" cx="{{ $x }}" cy="{{ $y }}" r="3" fill="{{ $color }}">
            <title>{{ $labels[$index] ?? '' }}: {{ number_format((float) $value, 0, ',', '.') }} {{ $item['name'] ?? '' }}</title>
          </circle>
        @endforeach
      @endforeach

      @foreach($dateIndexes as $index)
        @php
          $x = $paddingLeft + ($pointCount > 1 ? ($plotWidth * $index / ($pointCount - 1)) : $plotWidth / 2);
        @endphp
        <text class="analytics-chart__date-label" x="{{ $x }}" y="{{ $chartHeight - 10 }}" text-anchor="middle">{{ $formatDate($labels[$index] ?? '') }}</text>
      @endforeach
    </svg>
  </div>
@endif
