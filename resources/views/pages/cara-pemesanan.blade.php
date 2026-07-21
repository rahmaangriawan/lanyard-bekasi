@extends('layouts.public')

@section('title', 'Cara Pemesanan Lanyard Custom di Indonesia - Mudah & Cepat')
@section('meta_description', 'Pesan lanyard custom di Indonesia hanya 5 langkah mudah: pilih produk, konsultasi desain, mockup gratis, bayar, produksi & kirim. Bebas ongkir Indonesia Raya.')
@section('seo_canonical', rtrim(config('app.url', url('/')), '/') . '/cara-pemesanan')

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text=Halo%20Lanyard%20Indonesia%2C%20saya%20mau%20minta%20quotation%20lanyard%20custom.';

  $orderSteps = [
    [
      'title' => 'Pilih produk lanyard',
      'description' => 'Pilih jenis lanyard, bahan, model, dan aksesoris yang sesuai dengan kebutuhan Anda.',
    ],
    [
      'title' => 'Konsultasi desain / kirim logo',
      'description' => 'Kirimkan logo, desain, atau ide Anda kepada tim kami untuk dibuatkan desain terbaik.',
    ],
    [
      'title' => 'Terima mockup & quotation',
      'description' => 'Kami akan mengirimkan mockup desain dan penawaran harga resmi untuk Anda setujui.',
    ],
    [
      'title' => 'Lakukan pembayaran',
      'description' => 'Setelah quotation disetujui, lakukan pembayaran sesuai dengan instruksi yang diberikan.',
    ],
    [
      'title' => 'Produksi & pengiriman',
      'description' => 'Lanyard akan kami produksi dengan standar kualitas terbaik lalu dikirimkan ke alamat Anda dengan aman.',
    ],
  ];
@endphp

{{-- Hero --}}
<section class="py-16 sm:py-20 border-b border-slate-200" style="background-color: #F4F6FA;">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
      <div class="min-w-0">
        <p class="inline-flex items-center gap-2 bg-slate-200/50 text-slate-700 px-4 py-2 rounded-full border border-slate-200 text-sm font-bold mb-6">
          <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">shopping_cart</span>
          Cara Pemesanan
        </p>

        <h1 class="font-bold leading-[1.08] text-slate-900 mb-6" style="font-size: clamp(2rem, 4vw, 3rem);">
          Mudah Pesan Lanyard Custom dalam Hitungan Menit
        </h1>

        <p class="text-slate-600 text-base md:text-lg leading-relaxed mb-8 max-w-xl">
          Ikuti langkah-langkah mudah berikut untuk mendapatkan lanyard custom berkualitas dari PusatTaliLanyard.com.
        </p>

        <a
          href="{{ $whatsappUrl }}"
          target="_blank"
          rel="noopener noreferrer"
          class="group relative overflow-hidden inline-flex items-center gap-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl px-6 py-4 font-semibold transition-all shadow-lg hover:shadow-xl active:scale-95 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-white/20 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px]"
        >
          <span class="material-symbols-outlined text-white text-lg relative z-10" style="font-variation-settings: 'FILL' 1;">chat</span>
          <span class="hero-btn-text relative z-10 inline-block">Minta Quotation</span>
        </a>
      </div>

      <div class="w-full flex justify-end">
        <img
          src="/uploads/cara-pemesanan-hero.webp"
          alt="Cara pemesanan lanyard custom Indonesia - 5 langkah mudah pesan lanyard premium"
          class="max-w-full h-auto"
          loading="eager"
          fetchpriority="high"
          decoding="sync"
          width="500"
          height="350"
        />
      </div>
    </div>
  </div>
</section>

{{-- Steps --}}
<section class="lazy-section bg-white py-16 sm:py-20">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="text-center mb-12">
      <h2 class="font-bold leading-[1.08] text-slate-900" style="font-size: clamp(2rem, 4vw, 2.75rem);">
        Langkah Pemesanan
      </h2>
      <div class="mx-auto mt-4 h-1 w-16 rounded-full bg-slate-300"></div>
    </div>

    <div class="mx-auto max-w-[750px] space-y-4">
      @foreach ($orderSteps as $index => $step)
      <article class="rounded-2xl border border-slate-200 bg-slate-50 shadow-[0_14px_34px_rgba(0,0,0,0.04)] overflow-hidden flex items-stretch min-h-[180px]">
        
        {{-- Number column --}}
        <div class="flex items-center justify-center px-6 border-r border-dashed border-slate-200 bg-white sm:w-[120px] shrink-0">
          <span class="grid place-items-center w-12 h-12 rounded-full bg-slate-900 text-xl font-extrabold text-white shadow-md">
            {{ $index + 1 }}
          </span>
        </div>

        {{-- Content --}}
        <div class="flex-1 p-6">
          <h3 class="text-xl font-bold text-slate-900 mb-2">
            {{ $step['title'] }}
          </h3>
          <p class="text-slate-600 leading-relaxed text-sm md:text-base">
            {{ $step['description'] }}
          </p>
        </div>

      </article>
      @endforeach
    </div>
  </div>
</section>

@include('components.cta')
@include('components.footer')
@endsection