@extends('layouts.public')

@php
  $title = isset($product->metaTitle) && $product->metaTitle ? $product->metaTitle : (($product->name ?? 'Lanyard Custom Printing Full Color') . ' - Pusat Tali Lanyard');
  $desc = isset($product->metaDescription) && $product->metaDescription ? $product->metaDescription : (strip_tags($product->shortDescription ?? $product->description ?? 'Lanyard custom premium dengan bahan tissue, cetak full color, stopper hitam, dan kait besi.'));
  $imgOg = $product->featuredImage ?? '/uploads/pusat-tali-lanyard-logo.webp';
  $splideCssFile = public_path('vendor/splide/splide.min.css');
  $splideCssV = file_exists($splideCssFile) ? filemtime($splideCssFile) : 1;
@endphp

@section('title', $title)
@section('meta_description', $desc)
@section('seo_og_image', $imgOg)
@section('seo_type', 'product')
@section('seo_canonical', config('app.url') . '/produk/' . ($product->slug ?? ''))

@push('head')
<link rel="stylesheet" href="/vendor/splide/splide.min.css?v={{ $splideCssV }}" />
<style>
  .btn-whatsapp {
    background-color: #25D366;
    color: #ffffff;
    transition: background-color 0.2s ease-out, transform 150ms ease-out;
  }
  .btn-whatsapp:hover {
    background-color: #20ba5a;
  }
  .btn-whatsapp:active {
    transform: scale(0.98);
  }
  .color-star {
    color: #fbbf24;
  }
  /* Product Card - same as showcase */
  .product-card{
    display:flex; flex-direction:column;
    height:100%;
    border-radius:16px; background:#fff; border:1px solid #f1f5f9;
    overflow:hidden;
    transition: border-color 250ms ease-out, box-shadow 250ms ease-out, transform 250ms cubic-bezier(0.22,1,0.36,1);
    will-change: transform, box-shadow, border-color;
  }
  .product-card:hover{
    transform: translateY(-4px);
    border-color:#e2e8f0;
    box-shadow:0 16px 32px -16px rgba(15,23,42,0.16);
  }
  .product-card-imgwrap{
    position:relative; aspect-ratio:4/5; background:#fcfcfd; overflow:hidden;
    display:flex; align-items:center; justify-content:center; padding:22px;
  }
  .product-card-img{
    width:100%; height:100%; object-fit:contain;
    transition: transform 450ms cubic-bezier(0.22,1,0.36,1);
    will-change: transform;
    pointer-events:none;
  }
  .product-card:hover .product-card-img{ transform: scale(1.05); }
  .product-card:hover .product-card-title {
    color: var(--primary);
  }
  /* Related Products Splide - mobile slide, desktop grid 4 */
  #related-products{ content-visibility: visible; }
  #related-splide{
    overflow:visible;
  }
  #related-splide .splide__track{
    overflow:hidden;
    padding-bottom:8px;
  }
  .related-slide{
    width:260px;
  }
  @media(min-width:640px){
    .related-slide{ width:280px; }
  }
  /* Desktop >=768px: destroy splide -> grid 4 */
  @media(min-width:768px){
    #related-splide .splide__track{
      overflow:visible !important;
    }
    .related-list{
      display:grid !important;
      grid-template-columns:repeat(4, minmax(0,1fr)) !important;
      gap:24px !important;
      transform:none !important;
      width:100% !important;
    }
    .related-slide{
      width:auto !important;
      margin-right:0 !important;
    }
  }
  .toggle-desc-btn{
    font-size:11px; font-weight:700; letter-spacing:0.04em; text-transform:uppercase;
  }
</style>
@endpush

@push('schema')
  @include('components.schema.produk-detail')
@endpush

@section('content')
@php
  $imgHelper = fn ($src) => \App\Support\AssetUrl::relative($src);

  $featImg = $imgHelper($product->featuredImage);

  $prodName = $product->name ?? 'Lanyard Custom Printing Full Color';
  if ($prodName === 'Lanyard Tissue Premium') {
      $prodName = 'Lanyard Custom Printing Full Color';
  }
  
  $prodPrice = $product->basePrice ?? 'Rp12.900';
  if (str_contains($prodPrice, 'Mulai')) {
      $prodPrice = 'Rp12.900';
  }

  // Deskripsi logic - full vs 100 kata
  $dummyLongDesc = 'Lanyard custom premium berkualitas tinggi yang diproduksi menggunakan bahan polyester serat padat pilihan terbaik untuk menjamin kenyamanan maksimal saat dipakai sepanjang hari. Diproses menggunakan teknologi digital printing sublimasi generasi terbaru menghasilkan detail desain gambar super tajam, warna-warni yang cerah luar biasa, dan daya tahan warna yang sangat kokoh anti luntur meskipun dicuci berulang kali. Paket produk ini dilengkapi dengan aksesoris stopper plastik premium yang elastis nan kuat serta kait besi gantungan berlapis krom anti karat berstandar industri. Sangat ideal digunakan sebagai sarana tanda pengenal resmi karyawan kantor, kepanitiaan event seminar, gantungan kunci ID card mahasiswa, dan alat promosi branding perusahaan Anda secara profesional.';
  
  if (($product->slug ?? '') === 'lanyard-polyester') {
      $rawFull = $dummyLongDesc;
  } else {
      $rawFull = $product->description ?? $product->shortDescription ?? $dummyLongDesc;
  }
  
  $fullDesc = trim(strip_tags($rawFull));
  $words = $fullDesc === '' ? [] : preg_split('/\s+/', $fullDesc);
  $wordCount = count($words);
  $isLong = $wordCount > 35;
  if ($isLong) {
      $shortDesc = implode(' ', array_slice($words, 0, 35));
  } else {
      $shortDesc = $fullDesc;
  }

  $waNumber = \App\Models\Setting::where('key', 'wa_number')->value('value') ?? '6282210200700';
  $waMessage = urlencode('Halo Pusat Tali Lanyard, saya ingin bertanya tentang produk ' . $prodName . '. Apakah bisa dibantu?');
  $waLink = "https://wa.me/{$waNumber}?text={$waMessage}";

  // Related Products - 4 items
  $relatedProducts = collect();
  try {
      $curId = $product->id ?? null;
      $curSlug = $product->slug ?? null;
      $relatedProducts = \App\Models\Product::where('published', true)
          ->when($curId, fn($q) => $q->where('id', '!=', $curId))
          ->when($curSlug, fn($q) => $q->where('slug', '!=', $curSlug))
          ->orderBy('sortOrder')
          ->take(4)
          ->get();
  } catch (\Throwable $e) {
      $relatedProducts = collect();
  }

  if ($relatedProducts->isEmpty()) {
      $relatedProducts = collect([
          (object) ['name'=>'Lanyard Tissue Custom','slug'=>'lanyard-tissue','featuredImage'=>'/uploads/hero-lanyard-slider-01.webp'],
          (object) ['name'=>'Lanyard Polyester Premium','slug'=>'lanyard-polyester','featuredImage'=>'/uploads/hero-lanyard-slider-02.webp'],
          (object) ['name'=>'Lanyard Nylon High Quality','slug'=>'lanyard-nylon','featuredImage'=>'/uploads/aset-produk.webp'],
          (object) ['name'=>'ID Card Holder Minimalis','slug'=>'id-card-holder','featuredImage'=>'/uploads/hero-lanyard-slider-01.webp'],
      ]);
  }
@endphp

<div class="bg-white min-h-screen">
  {{-- Main Product Detail --}}
  <main class="mx-auto max-w-[1280px] px-6 lg:px-10 py-16 sm:py-24">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 items-start">
      
      {{-- Kolom Kiri: Gambar Utama Produk Besar --}}
      <div class="w-full bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm aspect-square relative flex items-center justify-center">
        @if($featImg)
          <img src="{{ $featImg }}" alt="{{ $prodName }} - E-commerce Pusat Tali Lanyard" class="w-full h-full object-cover" loading="eager" width="600" height="600" />
        @else
          <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-300">
            <span class="material-symbols-outlined text-6xl">inventory_2</span>
          </div>
        @endif
      </div>

      {{-- Kolom Kanan: Informasi Produk Rapi & Berurutan --}}
      <div class="flex flex-col justify-center py-2">
        {{-- 1. Kategori Produk Kecil --}}
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">
          Lanyard Custom
        </span>

        {{-- 2. Judul Produk Besar --}}
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 leading-tight mb-4">
          {{ $prodName }}
        </h1>

        {{-- 3. Rating Bintang & Jumlah Ulasan --}}
        <div class="flex items-center gap-2 mb-6 text-sm text-slate-500">
          <div class="flex color-star">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
          </div>
          <span class="font-medium text-slate-800">5.0</span>
          <span class="text-slate-300">|</span>
          <span>12 ulasan</span>
        </div>

        {{-- 4. Harga Produk --}}
        <div class="text-3xl font-bold text-slate-900 mb-6">
          {{ $prodPrice }}
        </div>

        {{-- 5. Deskripsi Singkat dengan Toggle Baca Selengkapnya --}}
        <div class="max-w-lg mb-8">
          <div id="desc-wrapper" class="text-slate-600 text-[15px] sm:text-base leading-relaxed">
            @if($isLong)
              <span id="desc-short">{{ $shortDesc }}...</span>
              <span id="desc-full" class="hidden">{{ $fullDesc }}</span>
            @else
              <span>{{ $fullDesc }}</span>
            @endif
          </div>

          @if($isLong)
            <button type="button" id="btn-toggle-desc" aria-expanded="false" onclick="toggleDescription()" class="toggle-desc-btn inline-flex items-center gap-1 mt-3 text-slate-800 hover:text-slate-900 transition-colors duration-150 group">
              <span id="btn-toggle-text">Baca Selengkapnya</span>
              <span id="btn-toggle-icon" class="material-symbols-outlined text-[18px] transition-transform duration-200">expand_more</span>
            </button>
          @endif
        </div>

        {{-- 6. Tombol Utama WhatsApp --}}
        <div>
          <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 btn-whatsapp font-bold text-base py-3 px-8 rounded-xl shadow-sm w-full sm:w-auto text-center">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.244-3.56c1.607.952 3.197 1.489 4.85 1.49h.005c5.385 0 9.764-4.374 9.767-9.758.002-2.607-1.013-5.059-2.859-6.908C16.22 3.413 13.758 2.4 11.146 2.4c-5.397 0-9.786 4.385-9.79 9.771-.001 1.702.461 3.326 1.34 4.81l-.997 3.637 3.725-.978zm11.392-7.533c-.305-.153-1.805-.89-2.084-.992-.28-.103-.483-.153-.686.153-.203.305-.785.992-.962 1.196-.177.203-.355.228-.66.076-.305-.153-1.287-.475-2.451-1.514-.906-.809-1.517-1.809-1.695-2.114-.177-.305-.019-.47.133-.621.137-.136.305-.355.457-.533.153-.177.203-.305.305-.508.102-.203.051-.381-.025-.533-.076-.153-.686-1.653-.94-2.261-.248-.599-.501-.518-.686-.527-.178-.008-.381-.01-.584-.01-.203 0-.534.076-.813.381-.28.305-1.068 1.042-1.068 2.541 0 1.5 1.093 2.946 1.245 3.149.153.203 2.15 3.284 5.21 4.604.727.314 1.293.502 1.734.643.73.232 1.393.199 1.918.121.585-.087 1.805-.738 2.06-1.45.253-.713.253-1.323.177-1.45-.076-.127-.28-.203-.585-.355z"/>
            </svg>
            <span class="text-white">Chat via WhatsApp</span>
          </a>
        </div>
      </div>
      
    </div>
  </main>

  {{-- Section Produk Terkait --}}
  <section id="related-products" class="bg-slate-50 border-t border-slate-100 py-16 sm:py-20">
    <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
      <div class="flex items-end justify-between gap-4 mb-10">
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
          Produk Terkait
        </h2>
        <a href="/produk" class="hidden md:inline-flex items-center gap-1.5 text-sm font-semibold text-slate-700 hover:text-slate-900 transition-colors">
          <span>Lihat semua</span>
          <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </a>
      </div>

      <div id="related-splide" class="splide" aria-label="Produk terkait bisa di-swipe">
        <div class="splide__track">
          <ul class="splide__list related-list">
            @foreach($relatedProducts as $rp)
              @php
                $rpImg = $imgHelper($rp->featuredImage ?? null) ?? '/uploads/hero-lanyard-slider-01.webp';
                $rpName = $rp->name ?? 'Lanyard Custom';
                $rpPrice = $rp->basePrice ?? 'Rp12.900';
                if (str_contains($rpPrice, 'Mulai')) {
                    $rpPrice = 'Rp12.900';
                }
              @endphp
              <li class="splide__slide related-slide">
                <a href="/produk/{{ $rp->slug }}" class="product-card group" draggable="false">
                  <div class="product-card-imgwrap">
                    <img src="{{ $rpImg }}" alt="{{ $rpName }}" class="product-card-img" width="400" height="500" loading="lazy" decoding="async" draggable="false" />
                  </div>
                  <div class="p-4 bg-white flex flex-col gap-1 border-t border-slate-100">
                    <h3 class="product-card-title font-semibold text-[15px] text-slate-800 leading-snug line-clamp-2 transition-colors">
                      {{ $rpName }}
                    </h3>
                    <div class="font-bold text-slate-900 mt-1">
                      {{ $rpPrice }}
                    </div>
                  </div>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </div>

      {{-- Mobile hint swipe --}}
      <div class="flex md:hidden items-center justify-center gap-1.5 mt-6 text-slate-400 select-none">
        <span class="material-symbols-outlined text-[16px]">swipe</span>
        <span class="text-[11px] font-medium tracking-wide uppercase">Geser untuk lihat lainnya</span>
      </div>
    </div>
  </section>
</div>

@include('components.footer')
@endsection

@section('scripts')
@php
  $splideJsFile = public_path('vendor/splide/splide.min.js');
  $splideJsV = file_exists($splideJsFile) ? filemtime($splideJsFile) : 1;
@endphp
<script src="/vendor/splide/splide.min.js?v={{ $splideJsV }}"></script>
<script>
// Toggle Deskripsi 100 kata -> buka tutup
function toggleDescription(){
  var shortEl = document.getElementById('desc-short');
  var fullEl = document.getElementById('desc-full');
  var btn = document.getElementById('btn-toggle-desc');
  var text = document.getElementById('btn-toggle-text');
  var icon = document.getElementById('btn-toggle-icon');
  if(!shortEl || !fullEl) return;
  var isHidden = fullEl.classList.contains('hidden');
  if(isHidden){
    shortEl.classList.add('hidden');
    fullEl.classList.remove('hidden');
    if(text) text.textContent = 'Tutup';
    if(icon) icon.style.transform = 'rotate(180deg)';
    if(btn) btn.setAttribute('aria-expanded','true');
  }else{
    fullEl.classList.add('hidden');
    shortEl.classList.remove('hidden');
    if(text) text.textContent = 'Baca Selengkapnya';
    if(icon) icon.style.transform = 'rotate(0deg)';
    if(btn) btn.setAttribute('aria-expanded','false');
  }
}

// Related Splide: mobile slide, desktop destroy jadi grid 4
(function(){
  var root = document.getElementById('related-splide');
  if(!root) return;
  function waitSplide(cb){
    if(typeof Splide !== 'undefined'){ cb(); return; }
    var t=0;
    (function tick(){
      if(typeof Splide !== 'undefined'){ cb(); return; }
      if(t++>80) return; requestAnimationFrame(tick);
    })();
  }
  waitSplide(function(){
    var splide = new Splide(root, {
      type: 'slide',
      autoWidth: true,
      gap: '16px',
      perMove: 1,
      focus: 0,
      trimSpace: false,
      drag: 'free',
      snap: true,
      flickPower: 600,
      dragMinThreshold: { mouse: 5, touch: 8 },
      arrows: false,
      pagination: false,
      keyboard: true,
      mediaQuery: 'min',
      breakpoints: {
        768: { destroy: true }
      }
    });
    splide.mount();
    // re-init on resize crossing breakpoint because destroy:true is one-way for some versions
    var mql = window.matchMedia('(min-width: 768px)');
    function handleMQ(e){
      if(e.matches){
        // desktop - if splide not destroyed, destroy
        try{ if(splide && splide.state && !splide.state.is(splide.state.IDLE)){} }catch(_){}
        // Splide will auto destroy via breakpoint, but ensure refresh
        if(document.querySelector('#related-splide.splide')){}
      }else{
        // mobile - if not mounted, remount
        if(!root.classList.contains('is-active')){
          try{ splide.mount(); }catch(_){}
        }
      }
    }
    // some splide versions handle destroy automatically; this listener helps re-mount
    if(mql.addEventListener){ mql.addEventListener('change', handleMQ); } else { mql.addListener(handleMQ); }
  });
})();
</script>
@endsection
