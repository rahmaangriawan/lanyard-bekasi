@extends('layouts.public')

@section('title', 'Daftar Harga Lanyard Custom Indonesia 2026 - Transparan')
@section('meta_description', 'Estimasi harga lanyard tissue, polyester, sablon full color & aksesoris di Indonesia. Transparan per kuantitas, makin banyak makin murah. Minta quotation resmi.')

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text=Halo%20Lanyard%20Indonesia%2C%20saya%20mau%20tanya%20harga%20lanyard%20custom.';

  $priceCategories = [
    [
      'name' => 'Lanyard Tissue Premium',
      'desc' => 'Bahan halus, warna tajam permanen, nyaman dipakai sepanjang hari.',
      'image' => '/uploads/hero-lanyard-slider-01.webp',
      'options' => [
        ['qty' => '1 – 99 pcs', 'price' => 'Rp 12.000 /pcs'],
        ['qty' => '100 – 499 pcs', 'price' => 'Rp 8.500 /pcs'],
        ['qty' => '500 – 999 pcs', 'price' => 'Rp 6.000 /pcs'],
        ['qty' => '1.000 – 4.999 pcs', 'price' => 'Rp 4.500 /pcs'],
        ['qty' => '5.000+ pcs', 'price' => 'Hubungi Kami'],
      ],
    ],
    [
      'name' => 'Lanyard Polyester Standar',
      'desc' => 'Ekonomis, tahan lama, cocok untuk event besar dan promosi.',
      'image' => '/uploads/hero-lanyard-slider-02.webp',
      'options' => [
        ['qty' => '1 – 99 pcs', 'price' => 'Rp 8.000 /pcs'],
        ['qty' => '100 – 499 pcs', 'price' => 'Rp 5.500 /pcs'],
        ['qty' => '500 – 999 pcs', 'price' => 'Rp 4.000 /pcs'],
        ['qty' => '1.000 – 4.999 pcs', 'price' => 'Rp 3.000 /pcs'],
        ['qty' => '5.000+ pcs', 'price' => 'Hubungi Kami'],
      ],
    ],
    [
      'name' => 'Lanyard Sablon Full Color',
      'desc' => 'Cetak sablon full color untuk desain kompleks dengan gradasi.',
      'image' => '/uploads/hero-lanyard-slider-01.webp',
      'options' => [
        ['qty' => '1 – 99 pcs', 'price' => 'Rp 14.000 /pcs'],
        ['qty' => '100 – 499 pcs', 'price' => 'Rp 10.000 /pcs'],
        ['qty' => '500 – 999 pcs', 'price' => 'Rp 7.500 /pcs'],
        ['qty' => '1.000 – 4.999 pcs', 'price' => 'Rp 5.500 /pcs'],
        ['qty' => '5.000+ pcs', 'price' => 'Hubungi Kami'],
      ],
    ],
    [
      'name' => 'Aksesoris & Hooks',
      'desc' => 'Hook plastik, metal, stainless steel, hingga stopper silikon.',
      'image' => '/uploads/hero-lanyard-slider-02.webp',
      'options' => [
        ['qty' => 'Hook Plastik', 'price' => 'Rp 500 /pcs'],
        ['qty' => 'Hook Metal', 'price' => 'Rp 1.500 /pcs'],
        ['qty' => 'Hook Stainless Steel', 'price' => 'Rp 3.000 /pcs'],
        ['qty' => 'Stopper Silikon', 'price' => 'Rp 300 /pcs'],
        ['qty' => 'ID Card Holder Kulit', 'price' => 'Rp 15.000 /pcs'],
      ],
    ],
  ];
@endphp

{{-- Hero --}}
<section class="py-16 sm:py-20 border-b border-slate-200" style="background-color: #F4F6FA;">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <p class="inline-flex items-center gap-2 bg-white text-slate-700 px-4 py-2 rounded-full border border-slate-200 text-sm font-bold mb-6">
      <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">receipt_long</span>
      Estimasi Harga
    </p>

    <h1 class="font-bold leading-[1.08] text-slate-900 mb-6" style="font-size: clamp(2rem, 4vw, 3rem);">
      Daftar Harga Lanyard Custom
    </h1>

    <p class="text-slate-600 text-base md:text-lg leading-relaxed mb-8 max-w-2xl">
      Transparansi harga adalah prioritas kami. Berikut estimasi harga lanyard custom berdasarkan jenis bahan dan kuantitas pemesanan.
    </p>

    <div class="flex flex-col sm:flex-row gap-4">
      <a
        href="{{ $whatsappUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        class="group relative overflow-hidden inline-flex items-center gap-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl px-6 py-4 font-semibold transition-all shadow-lg active:scale-95 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-white/20 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px]"
      >
        <span class="material-symbols-outlined text-white text-lg relative z-10" style="font-variation-settings: 'FILL' 1;">chat</span>
        <span class="hero-btn-text relative z-10 inline-block">Minta Quotation Resmi</span>
      </a>
    </div>
  </div>
</section>

{{-- Price Tables --}}
<section class="bg-white py-16 sm:py-20">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="space-y-12">
      @foreach ($priceCategories as $category)
      <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="grid md:grid-cols-[200px_1fr]">
          {{-- Left: Image --}}
          <div class="bg-slate-100 flex items-center justify-center p-6">
            <img src="{{ $category['image'] }}" alt="{{ $category['name'] }} - harga lanyard custom Indonesia transparan per kuantitas" class="w-full max-w-[160px] h-auto object-contain rounded-xl" loading="lazy" decoding="async" width="160" height="160" />
          </div>
          {{-- Right: Table --}}
          <div class="p-6 md:p-8">
            <h2 class="text-xl font-bold text-slate-900 mb-1">{{ $category['name'] }}</h2>
            <p class="text-sm text-slate-500 mb-6">{{ $category['desc'] }}</p>
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-slate-200">
                    <th class="text-left py-3 px-4 font-semibold text-slate-700 bg-slate-50 rounded-l-lg">Kuantitas / Varian</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-700 bg-slate-50 rounded-r-lg">Estimasi Harga</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  @foreach ($category['options'] as $option)
                  <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-4 text-slate-700 font-medium">{{ $option['qty'] }}</td>
                    <td class="py-3 px-4 text-right font-bold text-slate-900">{{ $option['price'] }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Disclaimer --}}
    <p class="mt-12 text-center text-sm text-slate-500 max-w-2xl mx-auto leading-relaxed">
      <span class="font-semibold text-slate-700">Catatan:</span> Harga di atas bersifat estimasi dan dapat berubah tergantung kompleksitas desain, pemilihan aksesoris tambahan, serta biaya pengiriman. 
      Hubungi kami untuk mendapatkan penawaran harga resmi yang disesuaikan dengan kebutuhan spesifik Anda.
    </p>
  </div>
</section>

{{-- CTA Banner --}}
<section class="py-16 sm:py-20" style="background-color: #F4F6FA;">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10 text-center">
    <h2 class="font-bold leading-[1.08] text-slate-900 mb-4" style="font-size: clamp(1.5rem, 3vw, 2.25rem);">
      Butuh Harga Khusus untuk Quantity Besar?
    </h2>
    <p class="text-slate-600 max-w-xl mx-auto mb-8">
      Kami berikan potongan harga spesial untuk pemesanan korporat, instansi pemerintah, dan event organizer.
    </p>
    <a
      href="{{ $whatsappUrl }}"
      target="_blank"
      rel="noopener noreferrer"
      class="group relative overflow-hidden inline-flex items-center gap-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl px-8 py-4 font-semibold transition-all shadow-lg active:scale-95 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-white/20 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px]"
    >
      <span class="material-symbols-outlined text-white text-lg relative z-10" style="font-variation-settings: 'FILL' 1;">chat</span>
      <span class="hero-btn-text relative z-10 inline-block">Konsultasi Harga Spesial</span>
    </a>
  </div>
</section>

@include('components.footer')
@endsection
