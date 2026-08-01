@extends('layouts.public')

@section('title', 'Syarat dan Ketentuan Pemesanan | Lanyard Bekasi')
@section('meta_description', 'Pelajari syarat pemesanan Lanyard Bekasi, persetujuan mockup, DP 50%, toleransi produksi, pengiriman, pembatalan, dan pengajuan garansi.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/syarat-ketentuan')
@section('seo_noindex', '1')

@push('head')
  <link rel="stylesheet" href="/css/service-pages.css?v={{ file_exists(public_path('css/service-pages.css')) ? filemtime(public_path('css/service-pages.css')) : 1 }}">
  <link rel="stylesheet" href="/css/terms-index.css?v={{ file_exists(public_path('css/terms-index.css')) ? filemtime(public_path('css/terms-index.css')) : 1 }}">
@endpush

@section('content')
@php
  $termsWhatsappUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya ingin berkonsultasi mengenai syarat pemesanan lanyard custom.');

  $termsTrust = [
    ['icon' => 'design_services', 'title' => 'Mockup Disetujui', 'description' => 'Diperiksa sebelum produksi'],
    ['icon' => 'receipt_long', 'title' => 'DP Minimal 50%', 'description' => 'Produksi dimulai setelah pembayaran'],
    ['icon' => 'palette', 'title' => 'Toleransi Dijelaskan', 'description' => 'Warna dan jahitan memiliki batas'],
    ['icon' => 'verified', 'title' => 'Klaim 3x24 Jam', 'description' => 'Disertai dokumentasi unboxing'],
  ];

  $termsHighlights = [
    ['icon' => 'design_services', 'title' => 'Periksa Mockup', 'description' => 'Pastikan ejaan, logo, warna, font, dan tata letak sudah sesuai sebelum memberikan persetujuan.'],
    ['icon' => 'receipt_long', 'title' => 'Pahami Pembayaran', 'description' => 'Produksi dimulai setelah DP minimal 50% diterima dan pesanan dilunasi sebelum dikirim.'],
    ['icon' => 'palette', 'title' => 'Kenali Toleransi', 'description' => 'Perbedaan warna 5 sampai 10 persen dan pergeseran jahitan 1 sampai 2 mm termasuk batas produksi.'],
    ['icon' => 'verified', 'title' => 'Simpan Bukti', 'description' => 'Dokumentasikan unboxing dan ajukan kendala maksimal 3x24 jam setelah paket diterima.'],
  ];

  $productionTerms = [
    ['icon' => 'palette', 'title' => 'Toleransi Warna', 'description' => 'Tampilan RGB pada layar dapat berbeda dengan hasil pada kain. Deviasi 5 sampai 10 persen merupakan toleransi produksi sublimasi.'],
    ['icon' => 'design_services', 'title' => 'Pemotongan dan Jahitan', 'description' => 'Perakitan melibatkan proses manual. Pergeseran jahitan atau pemotongan 1 sampai 2 mm termasuk batas wajar produksi.'],
    ['icon' => 'schedule', 'title' => 'Estimasi Produksi', 'description' => 'Waktu produksi dihitung setelah DP diterima dan mockup disetujui, bukan sejak percakapan pertama.'],
    ['icon' => 'inventory', 'title' => 'Proofing atau Sampling', 'description' => 'Untuk pesanan di atas 1.000 pcs, sample fisik dapat disepakati terlebih dahulu untuk meninjau hasil.'],
  ];
@endphp

<div class="info-page terms-page">
  <section class="terms-hero" id="terms-hero" aria-labelledby="terms-title">
    <div class="info-shell terms-hero__grid">
      <div class="terms-hero__content">
        <p class="info-eyebrow info-eyebrow--badge">Syarat Pemesanan Lanyard</p>
        <h1 id="terms-title">Syarat yang Jelas Sebelum Pesanan Diproduksi</h1>
        <p class="terms-hero__lead">Pahami persetujuan desain, pembayaran, toleransi produksi, pengiriman, dan garansi sebelum menyetujui pesanan.</p>
        <div class="info-actions">
          <a class="info-button info-button--primary" href="{{ $termsWhatsappUrl }}" target="_blank" rel="noopener noreferrer">
            <img src="/images/whatsapp.svg" alt="" width="19" height="19" aria-hidden="true">
            <span>Mulai Konsultasi</span>
          </a>
          <a class="info-button info-button--secondary" href="{{ route('ordering') }}">
            <span>Cara Pemesanan</span>
            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
          </a>
        </div>
        <p class="terms-hero__updated">
          <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
          Terakhir diperbarui: 31 Juli 2026
        </p>
      </div>

      <aside class="terms-hero__summary" aria-label="Ringkasan syarat utama">
        <span class="terms-hero__summary-icon material-symbols-outlined" aria-hidden="true">verified_user</span>
        <h2>Empat Hal Penting</h2>
        <p>Pastikan poin berikut dipahami sebelum pesanan masuk antrean produksi.</p>
        <ul>
          <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>Produksi dimulai setelah mockup disetujui.</span></li>
          <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>DP minimal 50% diperlukan untuk memproses pesanan.</span></li>
          <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>Hasil produksi memiliki toleransi teknis.</span></li>
          <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>Klaim wajib diajukan maksimal 3x24 jam.</span></li>
        </ul>
      </aside>
    </div>
  </section>

  <section class="terms-trust" aria-label="Ringkasan ketentuan pemesanan">
    <div class="info-shell">
      <ul class="info-trust__list">
        @foreach($termsTrust as $item)
          <li>
            <span class="info-icon material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <span><strong>{{ $item['title'] }}</strong><small>{{ $item['description'] }}</small></span>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="terms-overview" id="ringkasan-syarat" aria-labelledby="terms-overview-title">
    <div class="info-shell">
      <header class="info-heading">
        <p class="info-eyebrow">Sebelum Memesan</p>
        <h2 id="terms-overview-title">Periksa Empat Tahap yang Paling Menentukan</h2>
        <p>Ringkasan ini membantu Anda memahami keputusan penting sebelum membaca ketentuan lengkap.</p>
      </header>
      <ul class="terms-overview__grid">
        @foreach($termsHighlights as $item)
          <li>
            <span class="material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <h3>{{ $item['title'] }}</h3>
            <p>{{ $item['description'] }}</p>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="terms-content" id="isi-ketentuan" aria-labelledby="terms-content-title">
    <div class="info-shell terms-content__layout">
      <aside class="terms-toc" aria-label="Daftar isi syarat dan ketentuan">
        <div class="terms-toc__inner">
          <p>Daftar Isi</p>
          <nav>
            <a href="#ketentuan-umum">Ketentuan umum</a>
            <a href="#hak-cipta">Hak cipta dan desain</a>
            <a href="#persetujuan-mockup">Persetujuan mockup</a>
            <a href="#produksi-toleransi">Produksi dan toleransi</a>
            <a href="#pembayaran">Sistem pembayaran</a>
            <a href="#ketentuan-pengiriman">Pengiriman</a>
            <a href="#pembatalan-garansi">Pembatalan dan garansi</a>
          </nav>
        </div>
      </aside>

      <div class="terms-document">
        <header class="terms-document__header">
          <h2 id="terms-content-title">Ketentuan Lengkap Pemesanan Lanyard Custom</h2>
          <p>Ketentuan berikut menjadi dasar kesepakatan antara Lanyard Bekasi dan pemesan selama proses desain, produksi, pembayaran, serta pengiriman.</p>
        </header>

        <article class="terms-section" id="ketentuan-umum">
          <span class="terms-section__marker" aria-hidden="true">01</span>
          <div>
            <h2>Ketentuan Umum</h2>
            <p>Dengan melakukan pemesanan di Lanyard Bekasi, Anda sebagai klien atau pemesan setuju untuk terikat dan mematuhi seluruh Syarat dan Ketentuan di halaman ini. Ketentuan berlaku sebagai kesepakatan transaksi jasa cetak.</p>
          </div>
        </article>

        <article class="terms-section" id="hak-cipta">
          <span class="terms-section__marker" aria-hidden="true">02</span>
          <div>
            <h2>Hak Cipta dan Orisinalitas Desain</h2>
            <p>Jika klien memberikan file siap cetak atau elemen seperti logo, ikon, dan font, ketentuan berikut berlaku:</p>
            <ul class="terms-list">
              <li><span class="material-symbols-outlined" aria-hidden="true">image</span><span>Klien menjamin materi yang diserahkan tidak melanggar hak cipta, merek dagang, atau kekayaan intelektual pihak lain.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">policy</span><span>Lanyard Bekasi dibebaskan dari tuntutan hukum yang muncul akibat pelanggaran atas desain yang diberikan klien.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">design_services</span><span>Desain yang dibuat oleh tim Lanyard Bekasi menjadi hak milik klien setelah transaksi selesai dan lunas.</span></li>
            </ul>
          </div>
        </article>

        <article class="terms-section" id="persetujuan-mockup">
          <span class="terms-section__marker" aria-hidden="true">03</span>
          <div>
            <h2>Persetujuan Desain dan Mockup</h2>
            <div class="terms-note">
              <span class="material-symbols-outlined" aria-hidden="true">verified</span>
              <p><strong>Persetujuan mockup adalah tahap penting.</strong> Pesanan belum masuk antrean produksi sebelum desain final disetujui.</p>
            </div>
            <ul class="terms-list terms-list--compact">
              <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>Tim mengirim visualisasi desain final melalui WhatsApp atau email sebelum produksi.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>Pemesan wajib memeriksa ejaan, posisi logo, jenis font, warna, dan tata letak.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>Kesalahan isi atau tata letak pada mockup yang sudah disetujui menjadi tanggung jawab pemesan dan tidak termasuk garansi produksi.</span></li>
            </ul>
          </div>
        </article>

        <article class="terms-section" id="produksi-toleransi">
          <span class="terms-section__marker" aria-hidden="true">04</span>
          <div>
            <h2>Proses Produksi dan Toleransi</h2>
            <p>Produksi menggunakan cetak sublimasi digital dan proses perakitan yang sebagian dilakukan secara manual.</p>
            <ul class="terms-production">
              @foreach($productionTerms as $item)
                <li>
                  <span class="material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
                  <h3>{{ $item['title'] }}</h3>
                  <p>{{ $item['description'] }}</p>
                </li>
              @endforeach
            </ul>
          </div>
        </article>

        <article class="terms-section" id="pembayaran">
          <span class="terms-section__marker" aria-hidden="true">05</span>
          <div>
            <h2>Sistem Pembayaran</h2>
            <ul class="terms-list">
              <li><span class="material-symbols-outlined" aria-hidden="true">receipt_long</span><span><strong>Down Payment.</strong> Pesanan diproses setelah klien membayar DP minimal 50% dari total invoice.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">verified</span><span><strong>Pelunasan.</strong> Sisa tagihan wajib dilunasi sebelum barang dikirim atau diambil. Tim dapat mengirim foto atau video barang jadi sebagai bukti kesiapan.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">mail</span><span><strong>Invoice resmi.</strong> Pembayaran dilakukan melalui rekening resmi usaha atau perusahaan yang diinformasikan dalam invoice.</span></li>
            </ul>
          </div>
        </article>

        <article class="terms-section" id="ketentuan-pengiriman">
          <span class="terms-section__marker" aria-hidden="true">06</span>
          <div>
            <h2>Pengiriman dan Logistik</h2>
            <p>Lanyard Bekasi menyerahkan pesanan kepada ekspedisi atau kurir setelah produksi, quality control, pengemasan, dan pelunasan selesai.</p>
            <ul class="terms-list terms-list--compact">
              <li><span class="material-symbols-outlined" aria-hidden="true">local_shipping</span><span>Nomor resi dibagikan setelah paket diserahkan kepada ekspedisi agar status pengiriman dapat dipantau.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">local_shipping</span><span>Keterlambatan, kehilangan, atau kerusakan oleh kurir berada di luar proses produksi. Tim membantu follow-up dan klaim asuransi apabila pengiriman menggunakan asuransi.</span></li>
            </ul>
            <a class="terms-inline-link" href="{{ route('shipping') }}">
              <span>Lihat informasi pengiriman</span>
              <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
            </a>
          </div>
        </article>

        <article class="terms-section" id="pembatalan-garansi">
          <span class="terms-section__marker" aria-hidden="true">07</span>
          <div>
            <h2>Pembatalan dan Garansi</h2>
            <div class="terms-note terms-note--warning">
              <span class="material-symbols-outlined" aria-hidden="true">info</span>
              <p><strong>Kebijakan pembatalan.</strong> Pesanan tidak dapat dibatalkan atau dikembalikan dan DP dinyatakan hangus setelah produksi dimulai, termasuk ketika kain sudah dipotong atau dicetak.</p>
            </div>
            <ul class="terms-list terms-list--compact">
              <li><span class="material-symbols-outlined" aria-hidden="true">verified</span><span>Cacat produksi major seperti cetak pudar ekstrem, tali putus, atau stopper rusak dapat diajukan untuk diperiksa.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">schedule</span><span>Komplain wajib diajukan maksimal 3x24 jam setelah status pengiriman menunjukkan paket diterima.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">image</span><span>Pengajuan harus disertai foto dan video unboxing yang memperlihatkan kondisi paket serta produk dengan jelas.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">policy</span><span>Tim menentukan penyelesaian berdasarkan bukti, spesifikasi yang disetujui, toleransi produksi, dan hasil validasi.</span></li>
            </ul>
            <a class="terms-inline-link" href="{{ route('warranty') }}">
              <span>Baca cakupan garansi</span>
              <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
            </a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section class="info-cta terms-cta" id="terms-cta" aria-labelledby="terms-cta-title">
    <div class="info-shell">
      <div class="info-cta__panel">
        <div>
          <h2 id="terms-cta-title">Siap Membahas Spesifikasi Pesanan Anda?</h2>
          <p>Konsultasikan jumlah, bahan, aksesori, desain, dan target waktu agar quotation dapat disiapkan dengan jelas.</p>
        </div>
        <a href="{{ $termsWhatsappUrl }}" target="_blank" rel="noopener noreferrer">
          <img src="/images/whatsapp.svg" alt="" width="20" height="20" aria-hidden="true">
          <span>Konsultasi via WhatsApp</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      </div>
    </div>
  </section>
</div>

@include('components.footer')

@push('schema')
  @include('components.schema.terms')
@endpush
@endsection
