@extends('layouts.public')

@section('title', 'Syarat & Ketentuan Layanan Pusat Tali Lanyard')
@section('meta_description', 'Syarat ketentuan pemesanan lanyard custom Indonesia: hak cipta desain, approval mockup, toleransi warna, pembayaran DP 50%, garansi & kebijakan pembatalan.')
@section('seo_noindex', '1')

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text=Halo%20Lanyard%20Indonesia%2C%20saya%20mau%20bertanya%20soal%20syarat%20dan%20ketentuan%20pemesanan.';
@endphp

{{-- Hero --}}
<section class="py-16 sm:py-20 border-b border-slate-200" style="background-color: #F4F6FA;">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <p class="inline-flex items-center gap-2 bg-white text-slate-700 px-4 py-2 rounded-full border border-slate-200 text-sm font-bold mb-6">
      <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">gavel</span>
      Legal & Ketentuan
    </p>

    <h1 class="font-bold leading-[1.08] text-slate-900 mb-6" style="font-size: clamp(2rem, 4vw, 3rem);">
      Syarat & Ketentuan Layanan
    </h1>

    <p class="text-slate-600 text-base md:text-lg leading-relaxed mb-8 max-w-2xl">
      Syarat dan ketentuan ini mengatur penggunaan layanan dan proses pemesanan cetak lanyard custom di Pusat Tali Lanyard. Harap membacanya dengan saksama sebelum melakukan pemesanan.
    </p>

    <p class="text-sm text-slate-500">Terakhir diperbarui: Juli 2025</p>
  </div>
</section>

{{-- Content --}}
<section class="bg-white py-16 sm:py-20">
  <div class="mx-auto max-w-[1000px] px-6 lg:px-10">
    <div class="max-w-none space-y-12">

      {{-- 1. Ketentuan Umum --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">1. Ketentuan Umum</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Dengan melakukan pemesanan di Pusat Tali Lanyard, Anda (selanjutnya disebut "Klien" atau "Pemesan") setuju untuk terikat dan mematuhi seluruh Syarat dan Ketentuan yang tertulis di halaman ini. Ketentuan ini berlaku mengikat sebagai bentuk kesepakatan transaksi jual beli jasa cetak.
        </p>
      </div>

      {{-- 2. Hak Cipta & Hak Milik Desain --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">2. Hak Cipta & Orisinalitas Desain</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Dalam hal klien memberikan file desain siap cetak atau elemen desain (seperti logo, ikon, font):
        </p>
        <ul class="space-y-4 text-slate-600 text-lg leading-relaxed mb-4 ml-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">copyright</span>
            <span>Klien menjamin bahwa seluruh materi yang diserahkan tidak melanggar hak cipta, merek dagang, atau kekayaan intelektual pihak ketiga mana pun.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">gavel</span>
            <span>Pusat Tali Lanyard dibebaskan dari segala bentuk tuntutan hukum apabila terjadi pelanggaran hak cipta atas desain yang diberikan oleh klien.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">design_services</span>
            <span>Desain lanyard yang dibuatkan oleh tim Pusat Tali Lanyard (jika menggunakan layanan desain gratis/berbayar kami) sepenuhnya menjadi hak milik klien setelah transaksi selesai dan lunas.</span>
          </li>
        </ul>
      </div>

      {{-- 3. Persetujuan Desain (Mockup Approval) --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">3. Persetujuan Desain (Mockup Approval)</h2>
        <div class="bg-blue-50 border border-blue-100 p-5 rounded-xl mb-6">
          <p class="text-slate-700 text-lg leading-relaxed font-medium">
            Persetujuan desain (Mockup Approval) adalah tahap paling krusial sebelum masuk antrean produksi.
          </p>
        </div>
        <ul class="space-y-4 text-slate-600 text-lg leading-relaxed mb-4 ml-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">rule</span>
            <span>Tim kami akan selalu mengirimkan visualisasi desain final (mockup 2D/3D) untuk dikonfirmasi melalui WhatsApp atau Email sebelum masuk mesin cetak.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">rule</span>
            <span>Pemesan wajib memeriksa kembali seluruh detail, termasuk <strong class="text-slate-800 font-semibold">ejaan teks, posisi logo, jenis font, dan tata letak</strong>.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">rule</span>
            <span>Kesalahan cetak yang terjadi <strong class="text-slate-800 font-semibold">setelah</strong> mockup disetujui (ACC) sepenuhnya menjadi tanggung jawab pemesan, dan Pusat Tali Lanyard tidak menerima komplain cetak ulang untuk kasus ini.</span>
          </li>
        </ul>
      </div>

      {{-- 4. Produksi & Toleransi Warna --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">4. Proses Produksi & Toleransi Warna Cetak</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Kami menggunakan teknologi cetak sublimasi digital standar industri. Namun, mohon dipahami terkait hal teknis berikut:
        </p>
        <div class="grid sm:grid-cols-2 gap-4 mb-6">
          <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <span class="material-symbols-outlined text-slate-800 mb-2 block" style="font-size: 28px;">palette</span>
            <p class="text-slate-900 font-semibold mb-1">Toleransi Deviasi Warna (± 5-10%)</p>
            <p class="text-slate-600 text-base">Warna pada layar HP/Monitor (RGB) selalu sedikit berbeda dengan hasil cetak kain (CMYK). Penurunan akurasi hingga 10% adalah wajar dalam industri sublimasi.</p>
          </div>
          <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <span class="material-symbols-outlined text-slate-800 mb-2 block" style="font-size: 28px;">precision_manufacturing</span>
            <p class="text-slate-900 font-semibold mb-1">Toleransi Pemotongan & Jahitan</p>
            <p class="text-slate-600 text-base">Proses perakitan lanyard melibatkan jahitan dan pemotongan manual. Toleransi pergeseran jahitan sekitar 1-2 mm merupakan batas wajar produksi.</p>
          </div>
          <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <span class="material-symbols-outlined text-slate-800 mb-2 block" style="font-size: 28px;">schedule</span>
            <p class="text-slate-900 font-semibold mb-1">Estimasi Waktu Pengerjaan</p>
            <p class="text-slate-600 text-base">SLA (Service Level Agreement) waktu produksi dihitung sejak <strong class="text-slate-900">Pembayaran DP diterima</strong> dan <strong class="text-slate-900">Desain di-ACC</strong> (bukan dari saat chat pertama).</p>
          </div>
          <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <span class="material-symbols-outlined text-slate-800 mb-2 block" style="font-size: 28px;">inventory</span>
            <p class="text-slate-900 font-semibold mb-1">Proofing / Sampling</p>
            <p class="text-slate-600 text-base">Untuk order di atas 1.000 pcs, kami sarankan request cetak sample fisik 1 pcs (berbayar/gratis tergantung kesepakatan) untuk memastikan hasil akhir sesuai ekspektasi.</p>
          </div>
        </div>
      </div>

      {{-- 5. Sistem Pembayaran --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">5. Sistem Pembayaran</h2>
        <ul class="space-y-4 text-slate-600 text-lg leading-relaxed mb-6 ml-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">account_balance_wallet</span>
            <span><strong class="text-slate-800 font-semibold">Down Payment (DP):</strong> Pesanan baru akan diproses produksinya setelah pihak Klien membayarkan Uang Muka (DP) minimal 50% dari total invoice.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">account_balance_wallet</span>
            <span><strong class="text-slate-800 font-semibold">Pelunasan:</strong> Sisa tagihan 50% wajib dilunasi <strong class="text-slate-800">sebelum</strong> barang dikirimkan via ekspedisi atau diambil di workshop kami. Kami akan mengirimkan foto/video barang jadi sebagai bukti kesiapan barang.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">account_balance_wallet</span>
            <span><strong class="text-slate-800 font-semibold">Faktur / Invoice Resmi:</strong> Semua pembayaran diwajibkan melalui rekening bank resmi atas nama usaha/perusahaan kami, bukan perorangan yang tidak terafiliasi.</span>
          </li>
        </ul>
      </div>

      {{-- 6. Pengiriman --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">6. Pengiriman & Keterlambatan Logistik</h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-4">
          Pusat Tali Lanyard akan memastikan pesanan diserahkan ke pihak ekspedisi atau kurir tepat waktu sesuai dengan tenggat (deadline) kesepakatan produksi. Namun, mohon diperhatikan:
        </p>
        <ul class="space-y-4 text-slate-600 text-lg leading-relaxed mb-4 ml-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">local_shipping</span>
            <span>Setelah barang diserahkan ke pihak ketiga (JNE, TIKI, SiCepat, Gosend, Grab, Paxel, dll) beserta nomor resi, tanggung jawab pengiriman beralih ke perusahaan logistik tersebut.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">local_shipping</span>
            <span>Keterlambatan, kehilangan, atau kerusakan paket akibat kelalaian kurir logistik berada di luar kendali dan tanggung jawab kami, namun kami akan membantu proses follow-up dan klaim asuransi jika menggunakan asuransi pengiriman.</span>
          </li>
        </ul>
      </div>

      {{-- 7. Pembatalan & Garansi --}}
      <div class="group">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 pb-2 border-b-2 border-slate-100 group-hover:border-slate-300 transition-colors">7. Pembatalan & Garansi Komplain</h2>
        <div class="bg-slate-50 border-l-4 border-red-500 p-5 rounded-r-xl mb-6">
          <p class="text-slate-700 text-lg leading-relaxed">
            <strong class="text-slate-900 font-semibold">Kebijakan Pembatalan:</strong> Pesanan tidak dapat dibatalkan atau dikembalikan (refund) dan Down Payment (DP) hangus secara otomatis jika pesanan <strong class="text-red-700 font-semibold">sudah masuk tahap produksi (kain sudah dipotong atau dicetak)</strong>.
          </p>
        </div>
        <ul class="space-y-4 text-slate-600 text-lg leading-relaxed mb-4 ml-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">verified</span>
            <span><strong class="text-slate-800 font-semibold">Garansi Cetak Ulang:</strong> Kami memberikan garansi ganti baru 100% jika terjadi cacat produksi major (misal: warna sablon pudar ekstrem, tali putus, stopper rusak) yang murni kesalahan QC kami.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">verified</span>
            <span><strong class="text-slate-800 font-semibold">Batas Waktu Klaim:</strong> Segala bentuk komplain dan pengajuan garansi wajib dikirimkan dalam kurun waktu maksimal <strong class="text-slate-800">3x24 jam</strong> setelah status tracking kurir menunjukkan barang telah diterima.</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-slate-400 shrink-0 mt-1" style="font-size: 20px;">verified</span>
            <span>Diperlukan bukti berupa foto/video unboxing yang jelas untuk proses validasi klaim.</span>
          </li>
        </ul>
      </div>

    </div>
  </div>
</section>

@include('components.cta')
@include('components.footer')
@endsection