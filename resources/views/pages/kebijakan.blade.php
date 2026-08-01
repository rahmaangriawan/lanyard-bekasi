@extends('layouts.public')

@section('title', 'Kebijakan Privasi | Lanyard Bekasi')
@section('meta_description', 'Pelajari cara Lanyard Bekasi mengumpulkan, menggunakan, menyimpan, dan melindungi data pribadi serta file desain pelanggan.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/kebijakan')
@section('seo_noindex', '1')

@push('head')
  <link rel="stylesheet" href="/css/service-pages.css?v={{ file_exists(public_path('css/service-pages.css')) ? filemtime(public_path('css/service-pages.css')) : 1 }}">
  <link rel="stylesheet" href="/css/privacy-index.css?v={{ file_exists(public_path('css/privacy-index.css')) ? filemtime(public_path('css/privacy-index.css')) : 1 }}">
@endpush

@section('content')
@php
  $privacyWhatsappUrl = 'https://wa.me/6282210200700?text='.rawurlencode('Halo Lanyard Bekasi, saya ingin bertanya mengenai kebijakan privasi dan data saya.');

  $privacyTrust = [
    ['icon' => 'verified', 'title' => 'Data Dilindungi', 'description' => 'Akses dibatasi sesuai kebutuhan'],
    ['icon' => 'design_services', 'title' => 'File untuk Produksi', 'description' => 'Digunakan sesuai tujuan pesanan'],
    ['icon' => 'delete', 'title' => 'Retensi Terbatas', 'description' => 'File desain disimpan 30 hari'],
    ['icon' => 'chat', 'title' => 'Permintaan Data', 'description' => 'Dapat diajukan kepada tim'],
  ];

  $securityMeasures = [
    ['icon' => 'lock', 'title' => 'Saluran Terenkripsi', 'description' => 'Komunikasi data website dilindungi melalui HTTPS.'],
    ['icon' => 'admin_panel_settings', 'title' => 'Akses Terbatas', 'description' => 'Data hanya diakses oleh staf yang menangani pesanan.'],
    ['icon' => 'inventory', 'title' => 'Proteksi File', 'description' => 'File desain disimpan dengan pembatasan akses.'],
    ['icon' => 'policy', 'title' => 'Evaluasi Berkala', 'description' => 'Praktik keamanan ditinjau dan disesuaikan secara berkala.'],
  ];
@endphp

<div class="info-page privacy-page">
  <section class="privacy-hero" id="privacy-hero" aria-labelledby="privacy-title">
    <div class="info-shell privacy-hero__grid">
      <div class="privacy-hero__content">
        <p class="info-eyebrow info-eyebrow--badge">Privasi Pelanggan</p>
        <h1 id="privacy-title">Kebijakan Privasi yang Jelas dan Transparan</h1>
        <p class="privacy-hero__lead">Ketahui data yang kami perlukan, tujuan penggunaannya, serta cara kami menjaga informasi dan file desain Anda.</p>
        <div class="info-actions">
          <a class="info-button info-button--primary" href="#isi-kebijakan">
            <span>Baca Kebijakan</span>
            <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
          </a>
          <a class="info-button info-button--secondary" href="{{ $privacyWhatsappUrl }}" target="_blank" rel="noopener noreferrer">
            <img src="/images/whatsapp.svg" alt="" width="19" height="19" aria-hidden="true">
            <span>Tanya Tim Kami</span>
          </a>
        </div>
        <p class="privacy-hero__updated">
          <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
          Terakhir diperbarui: Juli 2025
        </p>
      </div>

      <aside class="privacy-hero__summary" aria-label="Ringkasan komitmen privasi">
        <span class="privacy-hero__summary-icon material-symbols-outlined" aria-hidden="true">verified_user</span>
        <h2>Komitmen Kami</h2>
        <p>Data pelanggan digunakan untuk melayani pesanan dan komunikasi yang berkaitan dengan layanan.</p>
        <ul>
          <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>Data tidak dijual untuk pemasaran pihak ketiga.</span></li>
          <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>File desain tidak dijadikan sampel tanpa izin.</span></li>
          <li><span class="material-symbols-outlined" aria-hidden="true">check_circle</span><span>Permintaan penghapusan dapat diajukan kepada tim.</span></li>
        </ul>
      </aside>
    </div>
  </section>

  <section class="privacy-trust" aria-label="Ringkasan perlindungan data">
    <div class="info-shell">
      <ul class="info-trust__list">
        @foreach($privacyTrust as $item)
          <li>
            <span class="info-icon material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
            <span><strong>{{ $item['title'] }}</strong><small>{{ $item['description'] }}</small></span>
          </li>
        @endforeach
      </ul>
    </div>
  </section>

  <section class="privacy-content" id="isi-kebijakan" aria-labelledby="privacy-content-title">
    <div class="info-shell privacy-content__layout">
      <aside class="privacy-toc" aria-label="Daftar isi kebijakan privasi">
        <div class="privacy-toc__inner">
          <p>Daftar Isi</p>
          <nav>
            <a href="#pendahuluan">Pendahuluan</a>
            <a href="#data-dikumpulkan">Data yang dikumpulkan</a>
            <a href="#penggunaan-data">Penggunaan data</a>
            <a href="#file-desain">Perlindungan file desain</a>
            <a href="#keamanan-data">Keamanan data</a>
            <a href="#cookie">Cookie dan pelacakan</a>
            <a href="#pembaruan">Pembaruan kebijakan</a>
          </nav>
        </div>
      </aside>

      <div class="privacy-document">
        <header class="privacy-document__header">
          <h2 id="privacy-content-title">Bagaimana Kami Mengelola Informasi Anda</h2>
          <p>Penjelasan berikut berlaku untuk interaksi melalui website, WhatsApp, email, dan komunikasi langsung dengan Lanyard Bekasi.</p>
        </header>

        <article class="privacy-section" id="pendahuluan">
          <span class="privacy-section__marker" aria-hidden="true">01</span>
          <div>
            <h2>Pendahuluan</h2>
            <p>Selamat datang di Lanyard Bekasi. Kebijakan Privasi ini menjelaskan komitmen kami dalam menjaga kerahasiaan data dan informasi yang Anda berikan saat menggunakan layanan kami, baik melalui website, WhatsApp, email, maupun komunikasi langsung.</p>
            <p>Dengan menggunakan layanan Lanyard Bekasi, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan kebijakan ini. Jika Anda tidak setuju, mohon untuk tidak menggunakan layanan kami.</p>
          </div>
        </article>

        <article class="privacy-section" id="data-dikumpulkan">
          <span class="privacy-section__marker" aria-hidden="true">02</span>
          <div>
            <h2>Informasi yang Kami Kumpulkan</h2>
            <p>Dalam proses pemesanan lanyard custom, kami dapat mengumpulkan informasi berikut:</p>
            <ul class="privacy-list">
              <li><span class="material-symbols-outlined" aria-hidden="true">mail</span><span><strong>Data pribadi.</strong> Nama lengkap, nomor WhatsApp atau telepon, alamat email, dan alamat pengiriman.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">domain</span><span><strong>Data bisnis atau organisasi.</strong> Nama perusahaan, instansi, sekolah, atau komunitas yang tercantum dalam desain.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">image</span><span><strong>File desain.</strong> Logo, artwork, mockup, dan file lain yang dikirimkan untuk produksi.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">receipt_long</span><span><strong>Data pemesanan.</strong> Jenis bahan, kuantitas, warna, aksesori, dan riwayat transaksi.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">web</span><span><strong>Data teknis.</strong> Alamat IP, jenis browser, halaman yang dikunjungi, dan durasi kunjungan melalui cookie.</span></li>
            </ul>
          </div>
        </article>

        <article class="privacy-section" id="penggunaan-data">
          <span class="privacy-section__marker" aria-hidden="true">03</span>
          <div>
            <h2>Penggunaan Data</h2>
            <p>Informasi yang kami kumpulkan digunakan untuk:</p>
            <ul class="privacy-list privacy-list--compact">
              <li><span class="material-symbols-outlined" aria-hidden="true">arrow_right_alt</span><span>Memproses dan mengelola pesanan lanyard custom dari konsultasi hingga pengiriman.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">arrow_right_alt</span><span>Berkomunikasi terkait status pesanan, konfirmasi desain, dan estimasi produksi.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">arrow_right_alt</span><span>Mengirim informasi promo atau pembaruan layanan hanya jika Anda menyetujuinya.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">arrow_right_alt</span><span>Meningkatkan kualitas layanan dan pengalaman pengguna website.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">arrow_right_alt</span><span>Memenuhi kewajiban hukum yang berlaku di Republik Indonesia.</span></li>
            </ul>
            <div class="privacy-note">
              <span class="material-symbols-outlined" aria-hidden="true">verified_user</span>
              <p><strong>Komitmen kami.</strong> Kami tidak menjual, menyewakan, atau membagikan data pribadi Anda kepada pihak ketiga untuk pemasaran tanpa izin eksplisit.</p>
            </div>
          </div>
        </article>

        <article class="privacy-section" id="file-desain">
          <span class="privacy-section__marker" aria-hidden="true">04</span>
          <div>
            <h2>Perlindungan File Desain dan Kekayaan Intelektual</h2>
            <p>Kami memahami bahwa logo dan desain yang Anda kirimkan adalah aset berharga.</p>
            <ul class="privacy-list">
              <li><span class="material-symbols-outlined" aria-hidden="true">lock</span><span>File logo, artwork, dan mockup hanya digunakan untuk keperluan produksi pesanan Anda.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">lock</span><span>File tidak digunakan kembali, diperbanyak, atau dijadikan sampel tanpa izin tertulis.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">schedule</span><span>Setelah serah terima, file disimpan selama 30 hari untuk keperluan garansi lalu dihapus dari sistem.</span></li>
              <li><span class="material-symbols-outlined" aria-hidden="true">delete</span><span>Anda dapat meminta penghapusan file lebih awal melalui WhatsApp.</span></li>
            </ul>
          </div>
        </article>

        <article class="privacy-section" id="keamanan-data">
          <span class="privacy-section__marker" aria-hidden="true">05</span>
          <div>
            <h2>Keamanan Data</h2>
            <p>Kami menerapkan langkah teknis dan organisasional untuk mengurangi risiko akses, perubahan, pengungkapan, atau perusakan data tanpa izin.</p>
            <ul class="privacy-security">
              @foreach($securityMeasures as $item)
                <li>
                  <span class="material-symbols-outlined" aria-hidden="true">{{ $item['icon'] }}</span>
                  <h3>{{ $item['title'] }}</h3>
                  <p>{{ $item['description'] }}</p>
                </li>
              @endforeach
            </ul>
            <p>Meskipun kami berupaya maksimal, tidak ada metode transmisi data melalui internet yang sepenuhnya aman. Kami tidak dapat menjamin keamanan absolut, namun terus meningkatkan sistem proteksi yang digunakan.</p>
          </div>
        </article>

        <article class="privacy-section" id="cookie">
          <span class="privacy-section__marker" aria-hidden="true">06</span>
          <div>
            <h2>Cookie dan Teknologi Pelacakan</h2>
            <p>Website Lanyard Bekasi dapat menggunakan cookie untuk mengingat preferensi kunjungan, menganalisis trafik, dan memastikan fungsi esensial website berjalan dengan baik.</p>
            <p>Anda dapat menonaktifkan cookie melalui pengaturan browser. Beberapa fitur website mungkin tidak berfungsi optimal setelah cookie dinonaktifkan.</p>
          </div>
        </article>

        <article class="privacy-section" id="pembaruan">
          <span class="privacy-section__marker" aria-hidden="true">07</span>
          <div>
            <h2>Pembaruan Kebijakan Privasi</h2>
            <p>Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Setiap perubahan akan diumumkan melalui halaman ini dengan mencantumkan tanggal pembaruan di bagian atas.</p>
            <p>Kami mendorong Anda meninjau halaman ini secara berkala agar mengetahui bagaimana data dilindungi. Penggunaan layanan setelah perubahan berarti Anda menyetujui kebijakan yang telah diperbarui.</p>
          </div>
        </article>

      </div>
    </div>
  </section>

  <section class="info-cta privacy-cta" aria-labelledby="privacy-cta-title">
    <div class="info-shell">
      <div class="info-cta__panel">
        <div>
          <h2 id="privacy-cta-title">Masih Memiliki Pertanyaan tentang Data Anda?</h2>
          <p>Sampaikan pertanyaan atau permintaan Anda. Tim Lanyard Bekasi akan membantu memberikan informasi yang relevan.</p>
        </div>
        <a href="{{ $privacyWhatsappUrl }}" target="_blank" rel="noopener noreferrer">
          <img src="/images/whatsapp.svg" alt="" width="20" height="20" aria-hidden="true">
          <span>Hubungi via WhatsApp</span>
          <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </a>
      </div>
    </div>
  </section>
</div>

@include('components.footer')

@push('schema')
  @include('components.schema.privacy')
@endpush
@endsection
