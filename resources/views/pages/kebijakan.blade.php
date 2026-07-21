@extends('layouts.public')

@section('title', 'Kebijakan Privasi Pusat Tali Lanyard - Keamanan Data')
@section('meta_description', 'Kebijakan privasi Pusat Tali Lanyard: komitmen menjaga data pribadi, file desain, transaksi aman. Enkripsi HTTPS, akses terbatas, penghapusan file 30 hari.')
@section('seo_noindex', '1')

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text=Halo%20Lanyard%20Indonesia%2C%20saya%20mau%20tanya%20soal%20kebijakan%20privasi.';
@endphp

{{-- Hero --}}
<section class="py-16 sm:py-20 border-b border-slate-200" style="background-color: #F4F6FA;">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <p class="inline-flex items-center gap-2 bg-white text-slate-700 px-4 py-2 rounded-full border border-slate-200 text-sm font-bold mb-6">
      <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">shield</span>
      Privasi Anda
    </p>

    <h1 class="font-bold leading-[1.08] text-slate-900 mb-6" style="font-size: clamp(2rem, 4vw, 3rem);">
      Kebijakan Privasi
    </h1>

    <p class="text-slate-600 text-base md:text-lg leading-relaxed mb-8 max-w-2xl">
      Kami di Pusat Tali Lanyard menghargai privasi Anda. Halaman ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data pelanggan selama proses pemesanan lanyard custom.
    </p>

    <p class="text-sm text-slate-500">Terakhir diperbarui: Juli 2025</p>
  </div>
</section>

{{-- Content --}}
<section class="bg-white py-16 sm:py-20">
  <div class="mx-auto max-w-[1000px] px-6 lg:px-10">
    <div class="max-w-none space-y-12">

      {{-- 1. Pendahuluan --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">1. Pendahuluan</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Selamat datang di Pusat Tali Lanyard. Kebijakan Privasi ini menjelaskan komitmen kami dalam menjaga kerahasiaan data dan informasi yang Anda berikan saat menggunakan layanan kami, baik melalui website, WhatsApp, email, maupun komunikasi langsung.
        </p>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Dengan menggunakan layanan Pusat Tali Lanyard, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan kebijakan ini. Jika Anda tidak setuju, mohon untuk tidak menggunakan layanan kami.
        </p>
      </div>

      {{-- 2. Informasi yang Dikumpulkan --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">2. Informasi yang Kami Kumpulkan</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Dalam proses pemesanan lanyard custom, kami dapat mengumpulkan informasi berikut:
        </p>
        <ul class="space-y-4 text-slate-600 text-lg leading-relaxed mb-4 ml-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">check_circle</span>
            <span><strong class="text-slate-800 font-semibold">Data Pribadi:</strong> Nama lengkap, nomor WhatsApp/telepon, alamat email, dan alamat pengiriman yang Anda berikan saat memesan.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">check_circle</span>
            <span><strong class="text-slate-800 font-semibold">Data Bisnis/Organisasi:</strong> Nama perusahaan, instansi, sekolah, atau komunitas yang tercantum dalam desain lanyard.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">check_circle</span>
            <span><strong class="text-slate-800 font-semibold">File Desain:</strong> Logo, artwork, mockup, dan file desain yang Anda kirimkan untuk keperluan produksi lanyard.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">check_circle</span>
            <span><strong class="text-slate-800 font-semibold">Data Pemesanan:</strong> Detail pesanan seperti jenis bahan, kuantitas, warna, aksesoris, dan riwayat transaksi.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">check_circle</span>
            <span><strong class="text-slate-800 font-semibold">Data Teknis:</strong> Alamat IP, jenis browser, halaman yang dikunjungi, dan durasi kunjungan (melalui cookie).</span>
          </li>
        </ul>
      </div>

      {{-- 3. Penggunaan Data --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">3. Penggunaan Data</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Informasi yang kami kumpulkan digunakan untuk:
        </p>
        <ul class="space-y-4 text-slate-600 text-lg leading-relaxed mb-6 ml-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">arrow_right_alt</span>
            <span>Memproses dan mengelola pesanan lanyard custom Anda dari awal hingga pengiriman.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">arrow_right_alt</span>
            <span>Berkomunikasi terkait status pesanan, konfirmasi desain, dan estimasi waktu produksi.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">arrow_right_alt</span>
            <span>Mengirimkan informasi promo, penawaran spesial, atau update layanan (hanya jika Anda menyetujui).</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">arrow_right_alt</span>
            <span>Meningkatkan kualitas layanan dan pengalaman pengguna di website kami.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">arrow_right_alt</span>
            <span>Memenuhi kewajiban hukum yang berlaku di wilayah Republik Indonesia.</span>
          </li>
        </ul>
        <div class="bg-slate-50 border-l-4 border-slate-800 p-5 rounded-r-xl">
          <p class="text-slate-700 text-lg leading-relaxed">
            <strong class="text-slate-900 font-semibold">Komitmen Kami:</strong> Kami tidak pernah menjual, menyewakan, atau membagikan data pribadi Anda kepada pihak ketiga untuk tujuan pemasaran tanpa izin eksplisit dari Anda.
          </p>
        </div>
      </div>

      {{-- 4. Perlindungan File Desain --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">4. Perlindungan File Desain & Kekayaan Intelektual</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Kami memahami bahwa logo dan desain yang Anda kirimkan adalah aset berharga. Berikut kebijakan kami:
        </p>
        <ul class="space-y-4 text-slate-600 text-lg leading-relaxed mb-4 ml-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">lock</span>
            <span>Semua file desain (logo, artwork, mockup) hanya digunakan untuk keperluan produksi pesanan Anda.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">lock</span>
            <span>File desain Anda <strong class="text-slate-800 font-semibold">tidak akan digunakan kembali, diperbanyak, atau dijadikan sampel</strong> tanpa izin tertulis.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">lock</span>
            <span>Setelah pesanan selesai dan serah terima dilakukan, file desain tetap kami simpan selama 30 hari untuk keperluan garansi, kemudian dihapus secara permanen dari sistem kami.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">lock</span>
            <span>Anda dapat meminta penghapusan file desain lebih awal dengan menghubungi kami melalui WhatsApp.</span>
          </li>
        </ul>
      </div>

      {{-- 5. Keamanan Data --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">5. Keamanan Data</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Kami menerapkan langkah-langkah keamanan teknis dan organisasional untuk melindungi data Anda dari akses tidak sah, perubahan, pengungkapan, atau perusakan:
        </p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <span class="material-symbols-outlined text-slate-800 mb-2 block" style="font-size: 28px;">enhanced_encryption</span>
            <p class="text-slate-600 text-base">Komunikasi data melalui saluran terenkripsi (HTTPS).</p>
          </div>
          <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <span class="material-symbols-outlined text-slate-800 mb-2 block" style="font-size: 28px;">admin_panel_settings</span>
            <p class="text-slate-600 text-base">Akses terbatas ke data pelanggan hanya untuk staf yang menangani langsung pesanan.</p>
          </div>
          <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <span class="material-symbols-outlined text-slate-800 mb-2 block" style="font-size: 28px;">dns</span>
            <p class="text-slate-600 text-base">Penyimpanan file desain di server dengan proteksi akses berlapis.</p>
          </div>
          <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <span class="material-symbols-outlined text-slate-800 mb-2 block" style="font-size: 28px;">policy</span>
            <p class="text-slate-600 text-base">Peninjauan rutin terhadap praktik dan prosedur keamanan kami.</p>
          </div>
        </div>
        <p class="text-slate-600 text-lg leading-relaxed">
          Meskipun kami berupaya maksimal, tidak ada metode transmisi data melalui internet yang 100% aman. Kami tidak dapat menjamin keamanan absolut, namun kami berkomitmen untuk terus meningkatkan sistem proteksi kami.
        </p>
      </div>

      {{-- 6. Cookie & Pelacakan --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">6. Cookie & Teknologi Pelacakan</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Website Pusat Tali Lanyard menggunakan cookie untuk:
        </p>
        <ul class="space-y-4 text-slate-600 text-lg leading-relaxed mb-4 ml-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">cookie</span>
            <span>Mengingat preferensi kunjungan Anda (seperti pengaturan tampilan).</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">analytics</span>
            <span>Menganalisis trafik website untuk meningkatkan pengalaman pengguna (Google Analytics).</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">web</span>
            <span>Memastikan semua fungsi esensial website berjalan dengan baik.</span>
          </li>
        </ul>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Anda dapat menonaktifkan cookie melalui pengaturan browser Anda kapan saja. Namun, beberapa fitur website mungkin tidak berfungsi optimal tanpa cookie.
        </p>
      </div>

      {{-- 7. Pembaruan Kebijakan --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">7. Pembaruan Kebijakan Privasi</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Setiap perubahan akan diumumkan melalui halaman ini dengan mencantumkan tanggal pembaruan di bagian atas.
        </p>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Kami mendorong Anda untuk meninjau halaman ini secara berkala agar selalu mengetahui bagaimana kami melindungi data Anda. Penggunaan layanan kami setelah perubahan kebijakan berarti Anda menyetujui kebijakan yang telah diperbarui.
        </p>
      </div>

      {{-- 8. Kontak --}}
      <div class="bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 rounded-3xl p-8 md:p-10 shadow-sm mt-16">
        <h2 class="text-2xl font-bold text-slate-900 mb-4">Pertanyaan Seputar Privasi?</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-8 max-w-2xl">
          Jika Anda memiliki pertanyaan, keberatan, atau permintaan terkait data pribadi Anda, tim kami siap membantu Anda kapan saja:
        </p>
        <div class="grid sm:grid-cols-2 gap-6 text-slate-700">
          <a href="mailto:hello@pusattalilanyard.id" class="flex items-center gap-4 p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow group/contact">
            <div class="w-12 h-12 bg-slate-100 text-slate-800 rounded-full flex items-center justify-center group-hover/contact:bg-slate-800 group-hover/contact:text-white transition-colors">
              <span class="material-symbols-outlined">mail</span>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-900 mb-0.5">Email Kami</p>
              <p class="text-slate-600 text-sm">hello@pusattalilanyard.id</p>
            </div>
          </a>
          <a href="tel:+6282210200700" class="flex items-center gap-4 p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow group/contact">
            <div class="w-12 h-12 bg-slate-100 text-slate-800 rounded-full flex items-center justify-center group-hover/contact:bg-slate-800 group-hover/contact:text-white transition-colors">
              <span class="material-symbols-outlined">phone</span>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-900 mb-0.5">Telepon / WhatsApp</p>
              <p class="text-slate-600 text-sm">+62 822-1020-0700</p>
            </div>
          </a>
          <div class="sm:col-span-2 flex items-start gap-4 p-4 bg-white rounded-2xl shadow-sm">
            <div class="w-12 h-12 bg-slate-100 text-slate-800 rounded-full flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined">pin_drop</span>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-900 mb-0.5">Alamat Kantor</p>
              <p class="text-slate-600 text-sm leading-relaxed">Jl. Sudirman No. 123, Indonesia Kota, Banten 15111</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

@include('components.cta')
@include('components.footer')
@endsection
