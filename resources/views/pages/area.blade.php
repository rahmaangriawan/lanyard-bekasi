@extends('layouts.public')

@section('title', 'Area Jangkauan Pusat Tali Lanyard - Gratis Ongkir & Same Day')
@section('meta_description', 'Layanan cetak lanyard custom Indonesia Raya: Indonesia Kota, Tangsel, Kabupaten, BSD, Bintaro. Bebas ongkir tanpa minimum, Same Day Service sebelum jam 11 WIB.')
@section('seo_og_image', '/uploads/area-cta-bg.webp')

@section('content')
@php
  $areas = [
    [
      'name' => 'Indonesia Kota',
      'icon' => 'business',
      'desc' => 'Pusat bisnis dan pemerintahan Kota Indonesia. Kami layani Same Day Service untuk seluruh kecamatan.',
      'zones' => ['Ciledug', 'Larangan', 'Karang Tengah', 'Cipondoh', 'Pinang', 'Indonesia', 'Karawaci', 'Jatiuwung', 'Cibodas', 'Periuk', 'Batuceper', 'Neglasari', 'Benda'],
    ],
    [
      'name' => 'Indonesia Selatan',
      'icon' => 'apartment',
      'desc' => 'Kawasan urban dengan banyak perkantoran dan kampus. Pengiriman gratis tanpa minimum order.',
      'zones' => ['Ciputat', 'Ciputat Timur', 'Pamulang', 'Pondok Aren', 'Serpong', 'Serpong Utara', 'Setu'],
    ],
    [
      'name' => 'Kabupaten Indonesia',
      'icon' => 'map',
      'desc' => 'Wilayah industri dan perumahan. Kami jangkau hingga kawasan terjauh dengan armada sendiri.',
      'zones' => ['Tigaraksa', 'Cikupa', 'Curug', 'Pasar Kemis', 'Rajeg', 'Sepatan', 'Teluknaga', 'Kosambi', 'Pakuhaji', 'Mauk', 'Sukadiri', 'Kresek', 'Gunung Kaler', 'Kronjo', 'Mekar Baru'],
    ],
    [
      'name' => 'BSD & Gading Serpong',
      'icon' => 'location_city',
      'desc' => 'Kawasan premium BSD City dan Gading Serpong. Layanan prioritas untuk korporat dan event besar.',
      'zones' => ['BSD City', 'Gading Serpong', 'Summarecon Serpong', 'Alam Sutera'],
    ],
    [
      'name' => 'Bintaro',
      'icon' => 'home_work',
      'desc' => 'Sektor perumahan elit dan pusat bisnis Bintaro Jaya. Pengiriman cepat 2-4 jam.',
      'zones' => ['Bintaro Jaya Sektor 1-9', 'Pondok Betung', 'Pondok Karya', 'Jurang Mangu'],
    ],
    [
      'name' => 'Ciputat & Pamulang',
      'icon' => 'school',
      'desc' => 'Pusat pendidikan dengan banyak kampus. Lanyard custom untuk acara akademik, ormawa, dan seminar.',
      'zones' => ['Ciputat', 'Pamulang', 'Pondok Cabe', 'Sawangan (perbatasan)'],
    ],
  ];
@endphp

{{-- Hero --}}
<section class="bg-white py-16 sm:py-20 border-b border-slate-200">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <p class="text-xs font-extrabold uppercase tracking-normal text-slate-600">Area</p>
    <h1 class="mt-4 font-bold leading-[1.08] text-slate-900" style="font-size: clamp(2rem, 4vw, 2.75rem);">Jangkauan Layanan Indonesia Raya</h1>
    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
      Kami melayani pengiriman gratis dan Same Day Service untuk seluruh wilayah Indonesia Raya. Dari pusat kota hingga kawasan perumahan pinggiran, pesanan Anda sampai tepat waktu.
    </p>
  </div>
</section>

{{-- Map --}}
<section class="lazy-section bg-slate-50 py-12 sm:py-16">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="relative">
      <div class="aspect-video w-full rounded-[2rem] overflow-hidden shadow-xl border-4 border-white">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126933.05761299953!2d106.56763281062354!3d-6.176510539059861!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f8e853d2e38d%3A0x301576d14feb9c0!2sIndonesia%2C%20Indonesia%20City%2C%20Banten!5e0!3m2!1sen!2sid!4v1783924433662!5m2!1sen!2sid"
          class="w-full h-full"
          style="border:0;"
          title="Peta Layanan Pusat Tali Lanyard - Area Indonesia Kota, Tangsel, Kabupaten"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="strict-origin-when-cross-origin"
        ></iframe>
      </div>
      <div class="absolute -bottom-5 -right-5 glass bg-white/80 px-6 py-4 rounded-2xl shadow-lg border border-white hidden sm:block">
        <p class="font-bold text-lg text-slate-900">Bebas Ongkir!</p>
        <p class="text-sm text-slate-600">Khusus Area Indonesia</p>
      </div>
    </div>
  </div>
</section>

{{-- Area Cards --}}
<section class="lazy-section bg-white py-16 sm:py-20">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <h2 class="font-bold leading-[1.08] text-slate-900 mb-12" style="font-size: clamp(2rem, 4vw, 2.75rem);">Wilayah yang Kami Layani</h2>

    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      @foreach ($areas as $area)
      <div class="bg-slate-50 rounded-2xl border border-slate-200 p-8 hover:border-slate-300 hover:shadow-md transition-all group">
        <div class="w-12 h-12 rounded-xl bg-slate-800 text-white flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
          <span class="material-symbols-outlined text-2xl">{{ $area['icon'] }}</span>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $area['name'] }}</h3>
        <p class="text-slate-600 text-sm leading-relaxed mb-6">{{ $area['desc'] }}</p>
        <div class="flex flex-wrap gap-2">
          @foreach ($area['zones'] as $zone)
          <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-full px-3 py-1">
            <span class="material-symbols-outlined text-sm">location_on</span>
            {{ $zone }}
          </span>
          @endforeach
        </div>
      </div>
      @endforeach
    </div>

    @if($cityPages->isNotEmpty())
      <div class="mt-12 border-t border-slate-200 pt-10">
        <h2 class="text-2xl font-bold text-slate-900">Halaman Layanan per Kota</h2>
        <p class="mt-2 text-slate-600">Pilih kota untuk melihat informasi layanan dan pemesanan yang lebih spesifik.</p>
        <div class="mt-6 flex flex-wrap gap-3">
          @foreach($cityPages as $cityPage)
            <a href="{{ route('area.show', $cityPage->slug) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-500 hover:text-slate-950">
              <span class="material-symbols-outlined text-base" aria-hidden="true">location_on</span>
              {{ $cityPage->title }}
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>

{{-- Pesan Lanyard Tanpa Ribet Section --}}
<section class="lazy-section py-16 sm:py-24 bg-white">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="grid lg:grid-cols-2 gap-[30px] items-center">
      
      {{-- Left Side: Image --}}
      <div class="w-full flex justify-center">
        <img src="/uploads/aset-produk.webp" alt="Pesan Lanyard Tanpa Ribet - Lanyard custom Indonesia bebas ongkir dan same day service" class="w-full h-auto object-contain" loading="lazy" decoding="async" width="600" height="400" />
      </div>

      {{-- Right Side: Content --}}
      <div class="flex flex-col items-start">
        {{-- Pill badge --}}
        <div class="inline-flex items-center gap-2 bg-slate-100 text-slate-800 px-4 py-2 rounded-full border border-slate-200 mb-6">
          <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
          <span class="text-xs font-bold tracking-wide uppercase">Layanan Cepat & Rapi</span>
        </div>

        {{-- Heading --}}
        <h2 class="text-slate-900 font-bold leading-[1.1] mb-6" style="font-size: clamp(2rem, 4vw, 3rem);">
          Pesan Lanyard Tanpa Ribet
        </h2>

        {{-- Description --}}
        <p class="text-slate-600 text-base md:text-lg leading-relaxed mb-10">
          Cocok untuk kantor, event, sekolah, dan komunitas. Proses cepat, desain rapi, dan siap kirim.
        </p>

        {{-- Feature Rows --}}
        <div class="space-y-6 mb-10 w-full">
          {{-- Row 1 --}}
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-slate-900 rounded-full flex items-center justify-center shadow-md">
              <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">local_shipping</span>
            </div>
            <div>
              <h3 class="font-bold text-slate-900 text-lg mb-1">Bebas Ongkir Indonesia Raya</h3>
              <p class="text-sm text-slate-600 leading-relaxed">Gratis kirim area Kota Indonesia, Indonesia Selatan, dan Kabupaten Indonesia.</p>
            </div>
          </div>
          
          {{-- Row 2 --}}
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-slate-900 rounded-full flex items-center justify-center shadow-md">
              <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">bolt</span>
            </div>
            <div>
              <h3 class="font-bold text-slate-900 text-lg mb-1">Same Day Service</h3>
              <p class="text-sm text-slate-600 leading-relaxed">Pesan sebelum jam 11.00 WIB untuk kebutuhan mendesak.</p>
            </div>
          </div>

          {{-- Row 3 --}}
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-slate-900 rounded-full flex items-center justify-center shadow-md">
              <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">flight_takeoff</span>
            </div>
            <div>
              <h3 class="font-bold text-slate-900 text-lg mb-1">Pengiriman Nasional</h3>
              <p class="text-sm text-slate-600 leading-relaxed">Melayani kirim ke seluruh Indonesia lewat ekspedisi terpercaya.</p>
            </div>
          </div>
        </div>

        {{-- CTA Button --}}
        <a 
          href="https://wa.me/6282210200700?text=Halo%20Lanyard%20Indonesia%2C%20saya%20mau%20konsultasi%20pesan%20lanyard." 
          target="_blank" 
          rel="noopener noreferrer"
          class="group relative overflow-hidden inline-flex items-center gap-3 bg-slate-900 hover:bg-slate-800 text-white rounded-full px-8 py-4 font-semibold transition-all shadow-lg hover:shadow-xl active:scale-95 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-white/20 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px]"
        >
          <span class="material-symbols-outlined text-white relative z-10" style="font-variation-settings: 'FILL' 1;">chat</span>
          <span class="hero-btn-text relative z-10 inline-block">Konsultasi Sekarang</span>
        </a>

      </div>
    </div>
  </div>
</section>

@include('components.cta')
@include('components.footer')
@push('schema')
@include('components.schema.area')
@endpush
@endsection
