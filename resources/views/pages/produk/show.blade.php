@extends('layouts.public')

@php
  use App\Support\AssetUrl;
  use App\Support\ResponsiveImage;
  use Illuminate\Support\Str;

  $normalizeCopy = static function ($value): string {
      $plain = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
      $plain = str_replace(
          ['IndoLanyard', 'Pusat Tali Lanyard', 'PusatTaliLanyard', '—', '–'],
          ['Lanyard Bekasi', 'Lanyard Bekasi', 'Lanyard Bekasi', '-', '-'],
          $plain
      );

      return trim((string) preg_replace('/\s+/u', ' ', $plain));
  };

  $parseProductList = static function ($value) use ($normalizeCopy): array {
      if (is_array($value)) {
          $items = $value;
      } else {
          $raw = trim((string) $value);
          $decoded = $raw !== '' ? json_decode($raw, true) : null;
          $items = is_array($decoded) ? $decoded : preg_split('/(?:\r\n|\r|\n|;|\|)+/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
      }

      $normalized = [];
      foreach ((array) $items as $key => $item) {
          if (is_array($item)) {
              $item = implode(': ', array_filter(array_map($normalizeCopy, $item)));
          } elseif (! is_int($key) && ! ctype_digit((string) $key)) {
              $item = $normalizeCopy($key).': '.$normalizeCopy($item);
          } else {
              $item = $normalizeCopy($item);
          }

          if ($item !== '') {
              $normalized[] = $item;
          }
      }

      return array_values(array_unique($normalized));
  };

  $productName = $normalizeCopy($product->name ?: 'Lanyard Custom');
  $categoryName = $normalizeCopy($product->category?->name ?: 'Lanyard Custom');
  $sku = $normalizeCopy($product->sku);
  $price = $normalizeCopy($product->basePrice) ?: 'Minta quotation';
  $minimumOrder = $normalizeCopy($product->minOrder);
  $description = $normalizeCopy($product->description ?: $product->shortDescription);
  $description = $description ?: 'Informasi bahan, ukuran, aksesori, dan kebutuhan produksi dapat dikonsultasikan langsung dengan tim Lanyard Bekasi.';
  $descriptionHtml = Str::markdown($description, [
      'html_input' => 'strip',
      'allow_unsafe_links' => false,
  ]);
  $shortDescription = $normalizeCopy($product->shortDescription) ?: Str::limit($description, 150, '');
  $specifications = $parseProductList($product->specs);
  $accessories = $parseProductList($product->accessories);

  $hasFeaturedImage = filled($product->featuredImage);
  $heroImage = AssetUrl::relative($product->featuredImage, '/uploads/aset-produk.webp');
  $heroSrcset = ResponsiveImage::srcset($heroImage);
  $heroAlt = $hasFeaturedImage ? 'Foto '.$productName : 'Contoh hasil lanyard custom Lanyard Bekasi';

  $waNumber = \App\Models\Setting::query()->where('key', 'wa_number')->value('value') ?: '6282210200700';
  $waMessage = rawurlencode('Halo Lanyard Bekasi, saya ingin konsultasi tentang produk '.$productName.'. Mohon bantuannya.');
  $waLink = "https://wa.me/{$waNumber}?text={$waMessage}";

  $title = filled($product->metaTitle) ? $normalizeCopy($product->metaTitle) : $productName.' | Lanyard Bekasi';
  $metaDescription = filled($product->metaDescription)
      ? $normalizeCopy($product->metaDescription)
      : Str::limit($shortDescription, 155, '');

  $trustItems = [
      ['title' => 'Bantuan Desain', 'description' => 'Mockup sebelum produksi'],
      ['title' => 'Spesifikasi Fleksibel', 'description' => 'Bahan dan aksesori custom'],
      ['title' => 'Quality Control', 'description' => 'Produk diperiksa sebelum kirim'],
      ['title' => 'Pengiriman Terpantau', 'description' => 'Nomor resi dibagikan'],
  ];

  $orderSteps = [
      ['title' => 'Konsultasi', 'description' => 'Sampaikan jumlah dan kebutuhan Anda.'],
      ['title' => 'Review desain', 'description' => 'Periksa mockup serta spesifikasi.'],
      ['title' => 'Konfirmasi', 'description' => 'Setujui quotation sebelum produksi.'],
      ['title' => 'Produksi & kirim', 'description' => 'Produk diperiksa lalu dikirim.'],
  ];
@endphp

@section('title', $title)
@section('meta_description', $metaDescription)
@section('seo_og_image', $heroImage)
@section('seo_type', 'product')
@section('seo_canonical', config('app.url').'/produk/'.$product->slug)

@push('head')
  <link rel="preload" href="{{ $heroImage }}" as="image" fetchpriority="high">
  <link rel="stylesheet" href="/css/product-detail.css?v={{ file_exists(public_path('css/product-detail.css')) ? filemtime(public_path('css/product-detail.css')) : 1 }}">
@endpush

@push('schema')
  @include('components.schema.produk-detail', ['product' => $product])
@endpush

@section('content')
<div class="product-detail">
  <section class="product-detail__hero" aria-labelledby="product-detail-title">
    <div class="product-detail__shell">
      <nav class="product-detail__breadcrumb" aria-label="Breadcrumb">
        <ol>
          <li><a href="{{ route('home') }}">Beranda</a></li>
          <li><a href="{{ route('products.index') }}">Produk</a></li>
          <li aria-current="page">{{ $productName }}</li>
        </ol>
      </nav>

      <div class="product-detail__marketplace">
        <div class="product-detail__gallery">
          <figure class="product-detail__media">
            <img
              src="{{ $heroImage }}"
              @if($heroSrcset) srcset="{{ $heroSrcset }}" sizes="(max-width: 767px) 100vw, 560px" @endif
              alt="{{ $heroAlt }}"
              width="1200"
              height="900"
              fetchpriority="high"
              decoding="async"
            >
          </figure>
          <div class="product-detail__media-note">
            <span aria-hidden="true">✓</span>
            <p>Visual produk mengikuti data yang tersedia. Warna hasil cetak dapat memiliki toleransi produksi.</p>
          </div>
        </div>

        <article class="product-detail__summary">
          <div class="product-detail__summary-meta">
            <span>{{ $categoryName }}</span>
            @if($sku)<span>SKU {{ $sku }}</span>@endif
          </div>

          <h1 id="product-detail-title">{{ $productName }}</h1>
          <p class="product-detail__summary-copy">{{ $shortDescription }}</p>

          <div class="product-detail__price">
            <span>Harga mulai</span>
            <strong>{{ $price }}</strong>
            <small>Harga akhir menyesuaikan jumlah dan spesifikasi</small>
          </div>

          <dl class="product-detail__quick-info">
            @if($minimumOrder)
              <div>
                <dt>Minimum pesanan</dt>
                <dd>{{ $minimumOrder }}</dd>
              </div>
            @endif
            <div>
              <dt>Jenis pesanan</dt>
              <dd>Custom sesuai kebutuhan</dd>
            </div>
            <div>
              <dt>Konfirmasi desain</dt>
              <dd>Mockup sebelum produksi</dd>
            </div>
          </dl>

          <ul class="product-detail__selling-points">
            <li><span aria-hidden="true">✓</span> Konsultasi bahan dan ukuran</li>
            <li><span aria-hidden="true">✓</span> Bantuan desain dan mockup</li>
            <li><span aria-hidden="true">✓</span> Quality control sebelum kirim</li>
          </ul>

          <div class="product-detail__summary-actions">
            <a class="product-detail__button product-detail__button--primary" href="{{ $waLink }}" target="_blank" rel="noopener">
              Pesan via WhatsApp
            </a>
            <a class="product-detail__button product-detail__button--secondary" href="{{ route('ordering') }}">
              Cara Pemesanan <span aria-hidden="true">→</span>
            </a>
          </div>

          <p class="product-detail__response-note">Tim kami akan membantu mengecek spesifikasi dan estimasi pesanan Anda.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="product-detail__trust" aria-labelledby="product-trust-title">
    <div class="product-detail__shell">
      <h2 class="sr-only" id="product-trust-title">Kepastian layanan Lanyard Bekasi</h2>
      <ul>
        @foreach($trustItems as $item)
          <li>
            <span class="product-detail__trust-check" aria-hidden="true">✓</span>
            <span>
              <strong>{{ $item['title'] }}</strong>
              <small>{{ $item['description'] }}</small>
            </span>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="product-detail__dossier" aria-labelledby="product-dossier-title">
    <div class="product-detail__shell product-detail__dossier-layout">
      <div class="product-detail__dossier-content">
        <article id="deskripsi-produk" class="product-detail__description">
          <span class="product-detail__eyebrow">Informasi Produk</span>
          <h2 id="product-dossier-title">Deskripsi Produk</h2>
          <div class="product-detail__description-copy">{!! $descriptionHtml !!}</div>
        </article>

        @if($specifications || $accessories)
          <div class="product-detail__spec-groups">
            @if($specifications)
              <section id="spesifikasi-produk" class="product-detail__spec-group">
                <h3>Spesifikasi</h3>
                <ul>
                  @foreach($specifications as $specification)
                    <li><span aria-hidden="true">✓</span><span>{{ $specification }}</span></li>
                  @endforeach
                </ul>
              </section>
            @endif

            @if($accessories)
              <section id="aksesori-produk" class="product-detail__spec-group">
                <h3>Aksesori</h3>
                <ul>
                  @foreach($accessories as $accessory)
                    <li><span aria-hidden="true">✓</span><span>{{ $accessory }}</span></li>
                  @endforeach
                </ul>
              </section>
            @endif
          </div>
        @endif
      </div>

      <aside class="product-detail__buy-card" aria-label="Konsultasi produk">
        <span>Butuh produk ini?</span>
        <strong>{{ $price }}</strong>
        <p>Kirim jumlah pesanan dan kebutuhan Anda untuk mendapatkan quotation.</p>
        <a href="{{ $waLink }}" target="_blank" rel="noopener">Konsultasi Sekarang</a>
        <ul>
          <li>✓ Respons langsung dari tim</li>
          <li>✓ Tidak ada pembayaran di halaman ini</li>
          <li>✓ Spesifikasi dikonfirmasi dahulu</li>
        </ul>
      </aside>
    </div>
  </section>

  <section id="alur-pemesanan" class="product-detail__process" aria-labelledby="product-process-title">
    <div class="product-detail__shell">
      <div class="product-detail__section-heading">
        <span class="product-detail__eyebrow">Proses Pemesanan</span>
        <h2 id="product-process-title">Pesan Custom dalam 4 Tahap</h2>
        <p>Alur ringkas dari konsultasi sampai produk dikirim.</p>
      </div>

      <ol>
        @foreach($orderSteps as $index => $step)
          <li>
            <span class="product-detail__step-number">{{ $index + 1 }}</span>
            <h3>{{ $step['title'] }}</h3>
            <p>{{ $step['description'] }}</p>
          </li>
        @endforeach
      </ol>
    </div>
  </section>

  @if($relatedProducts->isNotEmpty())
    <section class="product-detail__related" aria-labelledby="related-products-title">
      <div class="product-detail__shell">
        <div class="product-detail__section-heading product-detail__section-heading--row">
          <div>
            <span class="product-detail__eyebrow">Rekomendasi</span>
            <h2 id="related-products-title">Produk Serupa</h2>
          </div>
          <a href="{{ route('products.index') }}">Lihat semua produk →</a>
        </div>

        <ul class="product-detail__related-list">
          @foreach($relatedProducts as $relatedProduct)
            @php
              $relatedName = $normalizeCopy($relatedProduct->name ?: 'Lanyard Custom');
              $relatedImage = AssetUrl::relative($relatedProduct->featuredImage, '/uploads/aset-produk.webp');
              $relatedAlt = filled($relatedProduct->featuredImage) ? 'Foto '.$relatedName : 'Contoh hasil lanyard custom Lanyard Bekasi';
              $relatedPrice = $normalizeCopy($relatedProduct->basePrice) ?: 'Minta quotation';
              $relatedCategory = $normalizeCopy($relatedProduct->category?->name ?: 'Lanyard Custom');
            @endphp
            <li>
              <a href="{{ route('products.show', $relatedProduct->slug) }}">
                <figure>
                  <img src="{{ $relatedImage }}" alt="{{ $relatedAlt }}" width="480" height="360" loading="lazy" decoding="async">
                </figure>
                <div>
                  <span>{{ $relatedCategory }}</span>
                  <h3>{{ $relatedName }}</h3>
                  <strong>{{ $relatedPrice }}</strong>
                  <small>Lihat produk <span aria-hidden="true">→</span></small>
                </div>
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </section>
  @endif

  <section class="product-detail__cta" aria-labelledby="product-cta-title">
    <div class="product-detail__shell">
      <div class="product-detail__cta-panel">
        <div>
          <span>Butuh bantuan memilih?</span>
          <h2 id="product-cta-title">Konsultasikan Produk Sebelum Memesan</h2>
          <p>Tim kami membantu menentukan bahan, ukuran, aksesori, dan jumlah yang sesuai.</p>
        </div>
        <a href="{{ $waLink }}" target="_blank" rel="noopener">Chat WhatsApp Sekarang →</a>
      </div>
    </div>
  </section>
</div>

@include('components.footer')
@endsection
