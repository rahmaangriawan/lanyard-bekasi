@extends('layouts.public')

@section('title', 'Info Pengiriman Pusat Tali Lanyard - Gratis Ongkir & Tracking')
@section('meta_description', 'Pengiriman lanyard custom Indonesia: gratis ongkir Indonesia Raya, Same Day Service, kirim nasional JNE J&T, packing premium, resi tracking & garansi aman.')

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text=Halo%20Lanyard%20Indonesia%2C%20saya%20mau%20tanya%20info%20pengiriman%20lanyard.';
@endphp

{{-- Hero --}}
<section class="py-16 sm:py-20 border-b border-slate-200" style="background-color: #F4F6FA;">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <p class="inline-flex items-center gap-2 bg-white text-slate-700 px-4 py-2 rounded-full border border-slate-200 text-sm font-bold mb-6">
      <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">local_shipping</span>
      Info Pengiriman
    </p>

    <h1 class="font-bold leading-[1.08] text-slate-900 mb-6" style="font-size: clamp(2rem, 4vw, 3rem);">
      Pengiriman Cepat & Aman ke Seluruh Indonesia
    </h1>

    <p class="text-slate-600 text-base md:text-lg leading-relaxed mb-8 max-w-2xl">
      Kami berkomitmen mengirimkan pesanan lanyard custom Anda tepat waktu dan dalam kondisi sempurna. 
      Berikut informasi lengkap mengenai layanan pengiriman Pusat Tali Lanyard.
    </p>
  </div>
</section>

{{-- Shipping Info Stacked List --}}
<section class="bg-white py-16 sm:py-20">
  <div class="mx-auto max-w-[900px] px-6 lg:px-10">
    <div class="divide-y divide-slate-100 mb-16">
      {{-- Item: Gratis Ongkir --}}
      <div class="flex flex-col sm:flex-row items-start gap-6 py-8 first:pt-0 last:pb-0 group">
        <div class="w-14 h-14 bg-slate-100 text-slate-800 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors">
          <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">local_shipping</span>
        </div>
        <div class="space-y-1">
          <h3 class="text-xl font-bold text-slate-900">Gratis Ongkir Indonesia Raya</h3>
          <p class="text-slate-600 text-base md:text-lg leading-relaxed">
            Bebas biaya pengiriman untuk seluruh wilayah Kota Indonesia, Indonesia Selatan, dan Kabupaten Indonesia. Tanpa minimum transaksi.
          </p>
        </div>
      </div>

      {{-- Item: Same Day Service --}}
      <div class="flex flex-col sm:flex-row items-start gap-6 py-8 group">
        <div class="w-14 h-14 bg-slate-100 text-slate-800 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors">
          <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">bolt</span>
        </div>
        <div class="space-y-1">
          <h3 class="text-xl font-bold text-slate-900">Same Day Service</h3>
          <p class="text-slate-600 text-base md:text-lg leading-relaxed">
            Pesanan selesai di hari yang sama jika desain disetujui sebelum jam 11.00 WIB. Khusus area Indonesia Raya, barang sampai di hari yang sama.
          </p>
        </div>
      </div>

      {{-- Item: Nasional --}}
      <div class="flex flex-col sm:flex-row items-start gap-6 py-8 group">
        <div class="w-14 h-14 bg-slate-100 text-slate-800 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors">
          <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">flight_takeoff</span>
        </div>
        <div class="space-y-1">
          <h3 class="text-xl font-bold text-slate-900">Pengiriman Nasional</h3>
          <p class="text-slate-600 text-base md:text-lg leading-relaxed">
            Melayani pengiriman ke seluruh Indonesia via ekspedisi terpercaya seperti JNE, TIKI, SiCepat, dan J&T dengan estimasi pengiriman 2–7 hari kerja.
          </p>
        </div>
      </div>

      {{-- Item: Packing Aman --}}
      <div class="flex flex-col sm:flex-row items-start gap-6 py-8 group">
        <div class="w-14 h-14 bg-slate-100 text-slate-800 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors">
          <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
        </div>
        <div class="space-y-1">
          <h3 class="text-xl font-bold text-slate-900">Packing Premium</h3>
          <p class="text-slate-600 text-base md:text-lg leading-relaxed">
            Setiap pesanan dikemas dengan plastik wrapping dan bubble wrap untuk mencegah kerusakan selama perjalanan. Khusus partai besar menggunakan kardus khusus.
          </p>
        </div>
      </div>

      {{-- Item: Tracking --}}
      <div class="flex flex-col sm:flex-row items-start gap-6 py-8 group">
        <div class="w-14 h-14 bg-slate-100 text-slate-800 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors">
          <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">track_changes</span>
        </div>
        <div class="space-y-1">
          <h3 class="text-xl font-bold text-slate-900">Nomor Resi Tracking</h3>
          <p class="text-slate-600 text-base md:text-lg leading-relaxed">
            Kami akan memberikan nomor resi pengiriman setelah pesanan dikirim. Anda dapat memantau status pengiriman secara real-time melalui website ekspedisi.
          </p>
        </div>
      </div>

      {{-- Item: Asuransi --}}
      <div class="flex flex-col sm:flex-row items-start gap-6 py-8 last:pb-0 group">
        <div class="w-14 h-14 bg-slate-100 text-slate-800 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors">
          <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">verified_user</span>
        </div>
        <div class="space-y-1">
          <h3 class="text-xl font-bold text-slate-900">Garansi Aman</h3>
          <p class="text-slate-600 text-base md:text-lg leading-relaxed">
            Jika barang rusak atau hilang saat pengiriman, kami akan membantu proses klaim asuransi dan mengganti/mencetak ulang pesanan Anda.
          </p>
        </div>
      </div>
    </div>

    {{-- Timeline Table --}}
    <h2 class="font-bold leading-[1.08] text-slate-900 mb-8 text-center" style="font-size: clamp(1.5rem, 3vw, 2rem);">
      Estimasi Waktu Pengiriman
    </h2>
    <div class="overflow-x-auto max-w-3xl mx-auto">
      <table class="w-full text-sm rounded-2xl overflow-hidden shadow-sm border border-slate-200">
        <thead>
          <tr class="bg-slate-800 text-white">
            <th class="text-left py-4 px-6 font-semibold">Wilayah</th>
            <th class="text-left py-4 px-6 font-semibold">Estimasi</th>
            <th class="text-left py-4 px-6 font-semibold">Biaya</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-4 px-6 font-medium text-slate-800">Indonesia Raya (Kota, Kab, Tangsel)</td>
            <td class="py-4 px-6 text-slate-600">Same Day – 1 Hari</td>
            <td class="py-4 px-6 text-slate-600"><span class="font-bold text-green-600">GRATIS</span></td>
          </tr>
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-4 px-6 font-medium text-slate-800">Jabodetabek</td>
            <td class="py-4 px-6 text-slate-600">1 – 2 Hari</td>
            <td class="py-4 px-6 text-slate-600">Mulai Rp 10.000</td>
          </tr>
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-4 px-6 font-medium text-slate-800">Pulau Jawa (Non Jabodetabek)</td>
            <td class="py-4 px-6 text-slate-600">2 – 4 Hari</td>
            <td class="py-4 px-6 text-slate-600">Mulai Rp 15.000</td>
          </tr>
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-4 px-6 font-medium text-slate-800">Luar Pulau Jawa</td>
            <td class="py-4 px-6 text-slate-600">4 – 7 Hari</td>
            <td class="py-4 px-6 text-slate-600">Mulai Rp 25.000</td>
          </tr>
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-4 px-6 font-medium text-slate-800">Daerah Terpencil / 3T</td>
            <td class="py-4 px-6 text-slate-600">7 – 14 Hari</td>
            <td class="py-4 px-6 text-slate-600">Menyesuaikan</td>
          </tr>
        </tbody>
      </table>
    </div>

    {{-- Notes --}}
    <p class="mt-8 text-center text-sm text-slate-500 max-w-2xl mx-auto leading-relaxed">
      <span class="font-semibold text-slate-700">Catatan:</span> Estimasi waktu di atas berlaku untuk hari kerja. 
      Pengiriman Same Day Service hanya berlaku untuk area Indonesia Raya dengan konfirmasi desain sebelum jam 11.00 WIB. 
      Biaya pengiriman dapat berubah tergantung berat dan dimensi paket.
    </p>
  </div>
</section>

{{-- CTA Banner --}}
<section class="py-16 sm:py-20" style="background-color: #F4F6FA;">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10 text-center">
    <h2 class="font-bold leading-[1.08] text-slate-900 mb-4" style="font-size: clamp(1.5rem, 3vw, 2.25rem);">
      Siap Kirim Pesanan Lanyard Anda?
    </h2>
    <p class="text-slate-600 max-w-xl mx-auto mb-8">
      Konsultasikan kebutuhan pengiriman dengan tim kami. Kami siap membantu memilih opsi pengiriman terbaik untuk Anda.
    </p>
    <a
      href="{{ $whatsappUrl }}"
      target="_blank"
      rel="noopener noreferrer"
      class="group relative overflow-hidden inline-flex items-center gap-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl px-8 py-4 font-semibold transition-all shadow-lg active:scale-95 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-white/20 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px]"
    >
      <span class="material-symbols-outlined text-white text-lg relative z-10" style="font-variation-settings: 'FILL' 1;">chat</span>
      <span class="hero-btn-text relative z-10 inline-block">Konsultasi Pengiriman</span>
    </a>
  </div>
</section>

@include('components.footer')
@endsection
