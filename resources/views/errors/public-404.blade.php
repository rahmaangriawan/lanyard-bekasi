@extends('layouts.public')

@section('title', 'Halaman Tidak Ditemukan | Pusat Tali Lanyard')
@section('meta_description', 'Halaman yang Anda cari tidak tersedia. Kunjungi beranda atau lihat pilihan produk lanyard custom kami.')
@section('seo_noindex', '1')
@section('seo_no_canonical', '1')

@push('head')
  <style>
    .public-not-found {
      min-height: 62vh;
      background: #fff;
      padding: 4rem 1.25rem 6rem;
    }

    .public-not-found__inner {
      width: min(100%, 42rem);
      margin: 0 auto;
    }

    .public-not-found__eyebrow {
      margin: 0 0 1rem;
      color: #1d4ed8;
      font-size: .875rem;
      font-weight: 700;
      letter-spacing: .08em;
    }

    .public-not-found__title {
      max-width: 40rem;
      margin: 0;
      color: #0f172a;
      font-size: 2.25rem;
      font-weight: 800;
      letter-spacing: -.035em;
      line-height: 1.12;
    }

    .public-not-found__description {
      max-width: 40rem;
      margin: 1.25rem 0 0;
      color: #475569;
      font-size: 1rem;
      line-height: 1.75;
    }

    .public-not-found__actions {
      display: flex;
      flex-wrap: wrap;
      gap: .75rem;
      margin-top: 2rem;
    }

    .public-not-found__action {
      display: inline-flex;
      min-height: 2.75rem;
      align-items: center;
      justify-content: center;
      border-radius: .75rem;
      padding: 0 1.25rem;
      font-size: .875rem;
      font-weight: 700;
      text-decoration: none;
      transition: background-color .2s ease, border-color .2s ease;
    }

    .public-not-found__action:focus-visible {
      outline: 2px solid #1e293b;
      outline-offset: 2px;
    }

    .public-not-found__action--primary {
      background: #1e293b;
      color: #fff;
    }

    .public-not-found__action--primary:hover { background: #334155; }

    .public-not-found__action--secondary {
      border: 1px solid #cbd5e1;
      color: #1e293b;
    }

    .public-not-found__action--secondary:hover {
      border-color: #94a3b8;
      background: #f8fafc;
    }

    @media (min-width: 640px) {
      .public-not-found { padding-top: 6rem; }
      .public-not-found__title { font-size: 3rem; }
      .public-not-found__description { font-size: 1.125rem; }
    }
  </style>
@endpush

@section('content')
  <section class="public-not-found">
    <div class="public-not-found__inner">
      <p class="public-not-found__eyebrow">ERROR 404</p>
      <h1 class="public-not-found__title">Halaman tidak ditemukan</h1>
      <p class="public-not-found__description">
        Alamat yang Anda buka mungkin sudah berubah, dihapus, atau belum tersedia. Anda dapat kembali ke beranda atau melihat pilihan produk kami.
      </p>
      <div class="public-not-found__actions">
        <a href="{{ route('home') }}" class="public-not-found__action public-not-found__action--primary">Kembali ke Beranda</a>
        <a href="{{ route('products.index') }}" class="public-not-found__action public-not-found__action--secondary">Lihat Produk</a>
      </div>
    </div>
  </section>

  @include('components.footer')
@endsection
