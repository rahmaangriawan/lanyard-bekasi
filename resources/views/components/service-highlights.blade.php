{{-- Service Highlights — Interactive Deck (Premium SaaS, no icon/badge/title animation) --}}
<section class="lazy-section py-24 bg-white" id="services" data-sr="fade">
  <div class="px-6 lg:px-10 max-w-[1280px] mx-auto">
    <div class="text-center mb-16">
      <h2 class="font-bold tracking-tight text-slate-900 mx-auto" style="font-size: clamp(2rem, 4vw, 2.75rem); line-height:1.08; letter-spacing:-0.03em;">
        Layanan Cetak Sehari Jadi
      </h2>
    </div>

    @php
      $decks = [
        ['num'=>'01','title'=>'Same Day Service','summary'=>'Selesai kilat hitungan jam, tanpa kurangi kualitas.','detail'=>'Tim in-house + peralatan modern memungkinkan pesanan pagi selesai dan dikirim hari yang sama. Cocok untuk event dadakan atau konferensi.','badge'=>'FAST-TRACK','icon'=>'speed','stats'=>'4-6 Jam','stats_label'=>'Kecepatan produksi','features'=>['Selesai hitungan jam','Tim in-house','Prioritas pengerjaan','Konfirmasi real-time'],'cta'=>'Pesan Sekarang','cta_url'=>'/cara-pemesanan'],
        ['num'=>'02','title'=>'Bebas Ongkir','summary'=>'Gratis ongkir se-Indonesia tanpa minimum.','detail'=>'Gratis ongkir ke Karawaci, Cikokol, BSD, Gading Serpong, dan seluruh Indonesia untuk semua pesanan lanyard custom. Tanpa biaya tersembunyi.','badge'=>'ZERO COST','icon'=>'local_shipping','stats'=>'100% Indonesia','stats_label'=>'Cakupan area','features'=>['Gratis se-Indonesia','Tanpa minimum','Kirim hari yang sama','Lacak status'],'cta'=>'Cek Area','cta_url'=>'/area'],
        ['num'=>'03','title'=>'High Quality Print','summary'=>'Warna tajam, presisi 99% dan tahan lama.','detail'=>'Mesin digital resolusi tinggi menghasilkan warna presisi dan tahan pudar. Setiap lanyard melewati QC ketat sebelum kirim.','badge'=>'PREMIUM','icon'=>'high_quality','stats'=>'99% Akurat','stats_label'=>'Presisi warna','features'=>['Akurasi 99%','Tahan pudar','QC ketat','Bahan premium'],'cta'=>'Lihat Portofolio','cta_url'=>'#'],
        ['num'=>'04','title'=>'No Minimum Order','summary'=>'Pesan 1 pcs sampai ribuan, harga tetap bersaing.','detail'=>'Fleksibel dari sampel 1 pcs, 50 pcs komunitas, hingga ribuan pcs korporasi. Konsultasi gratis untuk hitung kebutuhan terbaik.','badge'=>'FLEKSIBEL','icon'=>'shopping_cart_off','stats'=>'Mulai Rp 1.000','stats_label'=>'Per pcs','features'=>['Mulai 1 pcs','Harga bersaing','Cocok UMKM','Konsultasi gratis'],'cta'=>'Daftar Harga','cta_url'=>'/daftar-harga'],
      ];
    @endphp

    <div id="deck-root" class="deck-layout">
      {{-- KIRI — Fase 3: no on-scroll stagger, cuma container masuk aja --}}
      <div class="flex flex-col gap-4" role="tablist" id="deck-nav" aria-label="Keunggulan layanan">
        @foreach($decks as $i => $d)
        <button type="button" role="tab" data-index="{{ $i }}" aria-selected="{{ $i===0 ? 'true':'false' }}"
          class="deck-nav group text-left flex w-full items-start bg-white rounded-2xl border border-slate-200">
          <div class="deck-icon-wrap">
            <div class="deck-icon-box">
              <span class="material-symbols-outlined deck-icon-glyph">{{ $d['icon'] }}</span>
            </div>
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
              <span class="text-[11px] font-bold tracking-widest text-slate-400">{{ $d['num'] }}</span>
              <span class="h-px w-6 bg-slate-200"></span>
              <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ $d['badge'] }}</span>
            </div>
            <h3 class="mt-2 text-[17px] font-bold leading-tight tracking-tight text-slate-900">{{ $d['title'] }}</h3>
            <p class="mt-1.5 text-[14px] leading-relaxed text-slate-500 line-clamp-2">{{ $d['summary'] }}</p>
          </div>
        </button>
        @endforeach
      </div>

      {{-- KANAN — Fase 3: no card reveal, internal deck anim tetep (checklist) --}}
      <div class="lg:sticky lg:top-28">
        <div class="bg-white rounded-[2rem] border border-slate-200 p-8 lg:p-10 shadow-[0_20px_40px_-20px_rgba(15,23,42,0.12)]">
          <div id="deck-display"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .deck-layout{ display:grid; grid-template-columns:1fr; gap:1.5rem; }
  @media(min-width:1024px){ .deck-layout{ grid-template-columns:1fr 1fr; align-items:start; gap:2rem; } }

  /* Card base */
  .deck-nav{
    position:relative;
    overflow:hidden;
    padding:24px;
    gap:0;
    background:#fff;
    border:1px solid #e2e8f0;
    border-radius:16px;
    transition: border-color 220ms ease-out, background-color 220ms ease-out, box-shadow 200ms ease-out, transform 200ms ease-out;
    will-change: transform, box-shadow, border-color;
  }
  .deck-nav:not(.is-active):hover{
    transform: translateY(-2px);
    border-color:#cbd5e1;
    box-shadow:0 12px 24px -12px rgba(15,23,42,0.18);
  }
  .deck-nav.is-active{
    background:#f8fafc;
    border-color:#0f172a;
    box-shadow:0 16px 32px -16px rgba(15,23,42,0.20);
  }

  /* 2. LEFT ACCENT 4px via ::before height animation */
  .deck-nav::before{
    content:'';
    position:absolute;
    left:0; top:0;
    width:4px;
    height:100%;
    background:#0f172a;
    transform-origin:top;
    transform:scaleY(0);
    transition: transform 250ms ease-out;
    will-change: transform;
    border-radius:0 2px 2px 0;
  }
  .deck-nav.is-active::before{ transform:scaleY(1); }

  /* 3. BACKGROUND SWEEP via ::after */
  .deck-nav::after{
    content:'';
    position:absolute;
    inset:0;
    background: linear-gradient(90deg, rgba(15,23,42,0.08) 0%, rgba(15,23,42,0) 62%);
    transform: translateX(-100%);
    opacity:0;
    pointer-events:none;
    will-change: transform, opacity;
  }
  .deck-nav.is-sweeping::after{
    animation: deckSweep 520ms cubic-bezier(0.22,1,0.36,1) forwards;
  }
  @keyframes deckSweep{
    0%{ transform:translateX(-100%); opacity:0; }
    15%{ opacity:1; }
    100%{ transform:translateX(0%); opacity:0; }
  }

  /* Icon box — static, no animation */
  .deck-icon-wrap{ flex-shrink:0; margin-right:18px; }
  .deck-icon-box{
    width:44px; height:44px;
    padding:7px;
    background:#EEF2F7;
    border:1px solid rgba(226,232,240,0.9);
    border-radius:8px;
    display:inline-flex; align-items:center; justify-content:center;
  }
  .deck-icon-glyph{
    font-size:20px !important;
    line-height:1;
    color:#334155;
    font-variation-settings:'FILL' 1, 'wght' 400;
  }

  /* Detail panel */
  #deck-display{
    min-height:420px;
    opacity:1;
    transform: translateY(0);
    will-change: opacity, transform;
  }

  .deck-detail-badge{
    display:inline-flex; align-items:center;
    border-radius:9999px;
    background:#0f172a; color:#fff;
    padding:6px 12px;
    font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.14em;
  }
  .deck-detail-icon-box{
    width:44px; height:44px; padding:7px;
    background:#F8FAFC;
    border:1px solid rgba(226,232,240,0.9);
    border-radius:8px;
    display:inline-flex; align-items:center; justify-content:center;
  }
  .deck-detail-icon-glyph{ font-size:20px !important; color:#334155; line-height:1; }

  .deck-features-grid{ display:grid; grid-template-columns:1fr; gap:20px; }
  @media(min-width:640px){ .deck-features-grid{ grid-template-columns:1fr 1fr; column-gap:32px; row-gap:20px; } }

  /* 5. CHECKLIST — only opacity + translateY */
  .deck-feature-item{
    display:flex; align-items:center; gap:12px;
    opacity:0;
    transform: translateY(8px);
    transition: opacity 280ms ease-out, transform 280ms cubic-bezier(0.22,1,0.36,1);
    will-change: opacity, transform;
  }
  .deck-feature-item.is-visible{ opacity:1; transform: translateY(0); }

  .deck-feature-box{
    width:36px; height:36px; padding:7px;
    background:#F8FAFC; border:1px solid rgba(226,232,240,0.8);
    border-radius:8px; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;
  }
  .deck-feature-glyph{ font-size:14px !important; color:#475569; }

  /* 6. CTA */
  .deck-cta{
    opacity:0;
    transform: scale(0.98);
    transition: opacity 250ms ease-out, transform 250ms cubic-bezier(0.22,1,0.36,1);
    will-change: opacity, transform;
  }
  .deck-cta.is-visible{ opacity:1; transform: scale(1); }
  .deck-cta-arrow{
    display:inline-block;
    transition: transform 250ms cubic-bezier(0.22,1,0.36,1);
    will-change: transform;
  }

  @media(prefers-reduced-motion:reduce){
    .deck-nav, .deck-nav::before, #deck-display, .deck-feature-item, .deck-cta, .deck-cta-arrow{
      transition:none !important; animation:none !important;
    }
  }
</style>

<script type="application/json" id="service-decks-data">@json($decks)</script>
