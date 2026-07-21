@extends('layouts.public')

@section('title', 'Promo Lanyard Custom Indonesia - Diskon & Bundling Hemat')
@section('meta_description', 'Promo cetak lanyard custom Indonesia: diskon 20% pelanggan baru, gratis ongkir Jabodetabek, paket hemat corporate, bundling ID card holder. Terbatas!')

@section('content')
@php
  $whatsappUrl = 'https://wa.me/6282210200700?text=Halo%20Lanyard%20Indonesia%2C%20saya%20mau%20info%20promo%20terbaru.';

  $promos = [
    [
      'title' => 'Diskon 20% untuk Pemesanan Pertama',
      'subtitle' => 'Khusus Pelanggan Baru',
      'desc' => 'Dapatkan potongan harga 20% untuk pemesanan lanyard custom pertama Anda. Minimal order 100 pcs, berlaku untuk semua jenis bahan.',
      'image' => '/uploads/promo-diskon-20-lanyard-custom.webp',
      'color' => 'from-slate-800 to-slate-900',
      'highlight' => 'DISKON 20%',
    ],
    [
      'title' => 'Gratis Ongkir Jabodetabek',
      'subtitle' => 'Tanpa Minimum Order',
      'desc' => 'Bebas ongkir ke seluruh area Jabodetabek tanpa minimal transaksi. Hemat pengiriman untuk kebutuhan lanyard Anda.',
      'image' => '/uploads/promo-gratis-ongkir-jabodetabek.webp',
      'color' => 'from-slate-700 to-slate-800',
      'highlight' => 'GRATIS ONGKIR',
    ],
    [
      'title' => 'Paket Hemat Corporate',
      'subtitle' => 'Untuk Perusahaan & Instansi',
      'desc' => 'Harga khusus untuk pemesanan korporat di atas 1.000 pcs. Termasuk gratis desain dan mockup 3D.',
      'image' => '/uploads/promo-paket-hemat-corporate.webp',
      'color' => 'from-slate-900 to-slate-950',
      'highlight' => 'HARGA KORPORAT',
    ],
    [
      'title' => 'Special Bundling ID Card Holder',
      'subtitle' => 'Lanyard + Holder = Lebih Hemat',
      'desc' => 'Beli paket lanyard + ID Card holder dan dapatkan diskon tambahan 15%. Cocok untuk orientasi karyawan baru.',
      'image' => '/uploads/promo-special-bundling-holder.webp',
      'color' => 'from-slate-800 to-slate-900',
      'highlight' => 'BUNDLING HEMAT',
    ],
    [
      'title' => 'Diskon Volume Besar',
      'subtitle' => 'Makin Banyak Makin Murah',
      'desc' => 'Special rate untuk pemesanan di atas 5.000 pcs. Harga nego, kualitas tetap premium.',
      'image' => '/uploads/promo-special-diskon-volume.webp',
      'color' => 'from-slate-700 to-slate-900',
      'highlight' => 'HARGA GROSIR',
    ],
  ];
@endphp

{{-- Hero --}}
<section class="py-16 sm:py-20 border-b border-slate-200" style="background-color: #F4F6FA;">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <p class="inline-flex items-center gap-2 bg-white text-slate-700 px-4 py-2 rounded-full border border-slate-200 text-sm font-bold mb-6">
      <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">sell</span>
      Promo Terbatas
    </p>

    <h1 class="font-bold leading-[1.08] text-slate-900 mb-6" style="font-size: clamp(2rem, 4vw, 3rem);">
      Promo & Penawaran Spesial
    </h1>

    <p class="text-slate-600 text-base md:text-lg leading-relaxed mb-8 max-w-2xl">
      Manfaatkan berbagai promo menarik untuk kebutuhan cetak lanyard custom, ID card, dan aksesoris. Penawaran terbatas, jangan sampai terlewat!
    </p>
  </div>
</section>

{{-- Promo Cards --}}
<section class="bg-white py-16 sm:py-20">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <div class="space-y-8">
      @foreach ($promos as $index => $promo)
      <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-r {{ $promo['color'] }} shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="grid md:grid-cols-[1fr_380px] items-center">
          {{-- Content --}}
          <div class="p-8 md:p-10 lg:p-12 flex flex-col justify-center z-10">
            <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 px-3 py-1.5 rounded-full backdrop-blur-sm mb-6 w-fit">
              <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="text-white text-sm">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" fill-rule="evenodd" d="M15.602 2.4c-.22-.22-.497-.21-.648-.19c-.144.02-.316.068-.48.115l-.125.035l-.103-.064a3.4 3.4 0 0 0-.42-.24c-.149-.064-.4-.138-.666.001c-.26.137-.345.381-.38.537a4 4 0 0 0-.06.487l-.01.127v.01l-.112.093c-.133.11-.272.223-.37.33c-.104.114-.264.327-.223.625c.042.307.265.466.404.544c.126.07.291.13.446.188l.109.04l.04.109c.057.154.118.32.188.446c.077.14.237.362.544.404c.297.04.51-.12.624-.224c.108-.098.221-.236.331-.37l.092-.111h.01l.128-.01c.168-.014.345-.028.487-.06c.155-.035.4-.12.537-.38c.139-.265.065-.518 0-.667a3.4 3.4 0 0 0-.24-.42l-.064-.102l.036-.126a3.5 3.5 0 0 0 .114-.479c.02-.152.03-.428-.19-.648m-.9.9l-.078.022l-.02.006a1 1 0 0 1-.397.047a.9.9 0 0 1-.37-.155l-.018-.011l-.1-.064l-.011.141l-.002.022a1 1 0 0 1-.092.384a1 1 0 0 1-.263.294L13.334 4l-.09.075l.076.029l.02.006c.071.026.23.082.35.202s.176.278.201.35l.007.02l.029.077l.075-.091l.013-.017c.051-.063.151-.188.295-.263a1 1 0 0 1 .384-.092l.022-.002l.14-.01l-.063-.102l-.011-.017a.9.9 0 0 1-.155-.37a1 1 0 0 1 .047-.397l.006-.02z" clip-rule="evenodd" />
                <path fill="currentColor" d="M10.435 3.17a.75.75 0 1 0-1.28-.782A2.15 2.15 0 0 0 9.47 5.03l.097.098a.76.76 0 0 1 .19.756a.75.75 0 0 0 1.436.434a2.26 2.26 0 0 0-.565-2.25l-.098-.098a.65.65 0 0 1-.095-.8m-2.749.77a.536.536 0 1 0-.758.76a.536.536 0 0 0 .758-.76m10.592 1.664a.75.75 0 0 0-1.471-.294l-.144.72c-.093.463-.427.84-.874.99a2.82 2.82 0 0 0-1.871 2.119l-.144.72a.75.75 0 0 0 1.47.294l.145-.72c.092-.463.426-.84.874-.99a2.82 2.82 0 0 0 1.87-2.12z" />
                <path fill="currentColor" fill-rule="evenodd" d="M22.524 8.842c.255.554.083 1.183-.384 1.65c-.271.27-.634.393-.957.451c-.331.059-.68.06-.987.04a7 7 0 0 1-1.045-.147l-.046-.01l-.024-.006l-.02-.005l-.006-.001h-.002l.12-.486l-.12.485a.5.5 0 0 1-.366-.365v-.003l-.002-.006l-.005-.02a5 5 0 0 1-.064-.317a7 7 0 0 1-.1-.798a4.2 4.2 0 0 1 .041-.986c.058-.324.18-.687.451-.958c.467-.467 1.096-.64 1.65-.383c.42.194.694.586.796 1.069c.483.103.875.376 1.07.796m-2.928 1.062c.178.031.413.065.664.081c.263.017.525.013.747-.027c.23-.04.361-.109.425-.173c.243-.243.225-.435.184-.524c-.045-.096-.204-.262-.614-.261a.5.5 0 0 1-.502-.502c.002-.41-.165-.57-.261-.614c-.089-.04-.281-.06-.524.184c-.064.064-.133.195-.173.425a3.2 3.2 0 0 0-.027.747c.016.25.05.486.081.664" clip-rule="evenodd" />
                <path fill="currentColor" d="M20.33 13.73a1.01 1.01 0 0 1 1.079-.17l.291.127a.75.75 0 0 0 .601-1.375l-.291-.127a2.51 2.51 0 0 0-2.674.422c-.326.29-.801.338-1.179.12l-.212-.123a.75.75 0 0 0-.75 1.299l.212.122a2.51 2.51 0 0 0 2.924-.296m-1.273 1.584a.537.537 0 1 1 .759.76a.537.537 0 0 1-.759-.76m-6.142-8.156a.536.536 0 1 0-.759.759a.536.536 0 0 0 .759-.76m4.241 3.001a.536.536 0 1 1 .759.759a.536.536 0 0 1-.759-.76" />
                <path fill="currentColor" fill-rule="evenodd" d="M9.639 6.885c.586.434 1.26 1.11 2.08 1.928l3.467 3.467c.819.82 1.494 1.494 1.928 2.08c.45.606.766 1.279.583 2.054c-.183.776-.767 1.236-1.44 1.577c-.651.33-1.556.632-2.654.998l-5.19 1.73c-1.33.443-2.41.803-3.247.952c-.844.15-1.7.135-2.337-.501c-.636-.636-.651-1.493-.501-2.338c.149-.836.509-1.916.952-3.246l1.73-5.19c.366-1.098.668-2.003.998-2.654c.34-.673.801-1.257 1.577-1.44s1.448.134 2.054.583M8.746 8.09l-.052-.038l-.036.2a50 50 0 0 0-.302 1.882c-.213 1.515-.392 3.347-.3 4.751c.055.85.237 1.95.411 2.857a49 49 0 0 0 .265 1.29l2.635-.88c-.07-.205-.152-.47-.235-.777c-.199-.74-.405-1.758-.405-2.82a11 11 0 0 1 .406-2.864a11 11 0 0 1 .329-1.014l-.76-.76c-.874-.873-1.47-1.467-1.956-1.827m-2.333 2.84a58 58 0 0 1 .541-1.577l-.083.573c-.216 1.533-.415 3.492-.312 5.057c.062.948.259 2.123.435 3.04a51 51 0 0 0 .308 1.484c-1.041.344-1.808.582-2.398.687c-.721.128-.92.009-1.014-.085s-.213-.293-.085-1.014c.127-.715.45-1.69.919-3.096zm6.656 6.656l-.279.093a12 12 0 0 1-.365-1.345a9 9 0 0 1-.198-1.779a9.5 9.5 0 0 1 .418-2.695l1.436 1.437c.874.874 1.468 1.47 1.828 1.956c.354.478.359.686.328.817s-.128.315-.658.583c-.54.274-1.338.542-2.51.933" clip-rule="evenodd" />
              </svg>
              <span class="text-white text-xs font-bold tracking-wide uppercase">{{ $promo['highlight'] }}</span>
            </div>
            
            <span class="text-white/80 text-sm font-semibold mb-2 tracking-wide uppercase">{{ $promo['subtitle'] }}</span>
            <h2 class="text-white font-bold text-2xl md:text-3xl mb-4 leading-tight">{{ $promo['title'] }}</h2>
            <p class="text-white/90 text-base leading-relaxed mb-8 max-w-lg">{{ $promo['desc'] }}</p>
            
            <a
              href="{{ $whatsappUrl }}"
              target="_blank"
              rel="noopener noreferrer"
              class="group/btn relative overflow-hidden inline-flex items-center gap-3 bg-white text-slate-900 rounded-xl px-6 py-3.5 font-bold transition-all shadow-lg active:scale-95 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-slate-800 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px] hover:text-white w-fit"
            >
              <span class="material-symbols-outlined text-lg relative z-10 transition duration-300 group-hover/btn:text-white" style="font-variation-settings: 'FILL' 1;">chat</span>
              <span class="hero-btn-text relative z-10 inline-block">Ambil Promo Ini</span>
            </a>
          </div>
          
          {{-- Image --}}
          <div class="hidden md:flex items-center justify-center p-4 h-full">
            <img src="{{ $promo['image'] }}" alt="{{ $promo['title'] }} - promo lanyard custom Indonesia {{ $promo['highlight'] }}" class="h-full w-full object-cover rounded-xl shadow-lg group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async" width="380" height="420" />
          </div>
        </div>
        
        {{-- Decorative element --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Bottom CTA --}}
<section class="py-16 sm:py-20 border-t border-slate-200" style="background-color: #F4F6FA;">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10 text-center">
    <h2 class="font-bold leading-[1.08] text-slate-900 mb-4" style="font-size: clamp(1.5rem, 3vw, 2.25rem);">
      Promo Tidak Berlaku Selamanya!
    </h2>
    <p class="text-slate-600 max-w-xl mx-auto mb-8">
      Segera hubungi kami dan klaim penawaran spesial untuk kebutuhan lanyard custom Anda sebelum kehabisan.
    </p>
    <a
      href="{{ $whatsappUrl }}"
      target="_blank"
      rel="noopener noreferrer"
      class="group relative overflow-hidden inline-flex items-center gap-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl px-8 py-4 font-semibold transition-all shadow-lg active:scale-95 before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-0 before:w-0 before:rounded-full before:bg-white/20 before:duration-700 before:ease-out hover:before:h-[800px] hover:before:w-[800px]"
    >
      <span class="material-symbols-outlined text-white text-lg relative z-10" style="font-variation-settings: 'FILL' 1;">chat</span>
      <span class="hero-btn-text relative z-10 inline-block">Klaim Promo Sekarang</span>
    </a>
  </div>
</section>

@include('components.footer')
@endsection
