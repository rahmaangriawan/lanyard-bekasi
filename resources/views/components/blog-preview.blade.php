{{-- Blog Preview — Editorial Showcase Premium (Linear/Stripe/Vercel/Apple) --}}
@php
  use App\Models\Post;
  use Illuminate\Support\Facades\Cache;

  $posts = Cache::store(config('cache.public_store', 'file'))->remember('home_posts', 3600, function () {
    return Post::with('category')->where('published', true)->orderBy('createdAt', 'desc')->take(3)->get();
  });

  if ($posts->isEmpty()) {
    $posts = collect([
      (object) ['title' => 'Pentingnya Lanyard untuk Keamanan Event Skala Besar yang Profesional','slug' => 'pentingnya-lanyard-event','featuredImage' => null,'category' => (object) ['name' => 'ARTIKEL'],'createdAt' => now()->subDays(3),'readingTime' => 5,'content'=>''],
      (object) ['title' => 'Tips Mendesain Lanyard Elegan yang Mencerminkan Identitas Brand','slug' => 'tips-mendesain-lanyard','featuredImage' => null,'category' => (object) ['name' => 'GUIDE'],'createdAt' => now()->subDays(8),'readingTime' => 4,'content'=>''],
      (object) ['title' => 'Material Tissue vs Polyester: Mana yang Terbaik untuk Kebutuhan Anda','slug' => 'tissue-vs-polyester','featuredImage' => null,'category' => (object) ['name' => 'NEWS'],'createdAt' => now()->subDays(14),'readingTime' => 6,'content'=>''],
    ]);
  }

  $img = fn ($src) => \App\Support\AssetUrl::relative($src);

  $badge = function($name){
    $name = strtoupper(trim($name));
    if ($name === '') { $name = 'ARTIKEL'; }
    $map = ['EVENT'=>'NEWS','EVENTS'=>'NEWS','BRANDING'=>'GUIDE','MATERIAL'=>'TIPS','UPDATE'=>'ARTIKEL','BERITA'=>'NEWS','TIPS'=>'TIPS','PANDUAN'=>'GUIDE','ARTIKEL'=>'ARTIKEL','GUIDE'=>'GUIDE','NEWS'=>'NEWS'];
    if (isset($map[$name])) { return $map[$name]; }
    return $name;
  };

  $formatDate = function($date){
    if (empty($date)) { return null; }
    try{
      if ($date instanceof \Carbon\Carbon) { $d = $date; } else { $d = \Carbon\Carbon::parse($date); }
      return $d->locale('id')->translatedFormat('d F Y');
    } catch (\Throwable $e) { return null; }
  };
@endphp

<section class="lazy-section blog-editorial bg-white" id="blog" data-sr="fade" style="padding-top: clamp(72px, 8vw, 112px); padding-bottom: clamp(72px, 8vw, 112px);">
  <div class="px-6 lg:px-10 max-w-[1280px] mx-auto">

    <div class="blog-editorial-header">
      <div class="blog-editorial-header-left">
        <h2 class="blog-editorial-title">Update &amp; Insight</h2>
        <p class="blog-editorial-desc">Panduan, tips, dan cerita di balik proses produksi lanyard premium kami.</p>
      </div>
      <a href="/blog" class="blog-editorial-link group">
        <span>Lihat Semua Artikel</span>
        <span class="blog-editorial-link-arrow" aria-hidden="true">
          <span class="material-symbols-outlined">arrow_forward</span>
        </span>
      </a>
    </div>

    <div class="blog-editorial-grid">
      @foreach ($posts as $post)
        @php
          $catName = 'ARTIKEL';
          if (isset($post->category) && isset($post->category->name) && !empty($post->category->name)) {
            $catName = $post->category->name;
          }
          $cat = $badge($catName);

          $rawDate = null;
          if (isset($post->createdAt)) { $rawDate = $post->createdAt; }
          elseif (isset($post->created_at)) { $rawDate = $post->created_at; }
          $dateLabel = $formatDate($rawDate);

          $feat = null;
          if (isset($post->featuredImage)) { $feat = $post->featuredImage; }
          $image = $img($feat);

          $content = '';
          if (isset($post->content)) { $content = $post->content; }
          $wordCount = 0;
          if (!empty($content)) {
            $plain = strip_tags($content);
            $wordCount = str_word_count($plain);
          }
          $calcRt = 0;
          if ($wordCount > 0) { $calcRt = (int) ceil($wordCount / 200); }
          $fallbackRt = $calcRt > 0 ? $calcRt : 5;
          $rt = $fallbackRt;
          if (isset($post->readingTime) && !empty($post->readingTime)) { $rt = (int) $post->readingTime; }
          if ($rt < 1) { $rt = 1; }

          $slug = '';
          if (isset($post->slug)) { $slug = $post->slug; }
          $title = '';
          if (isset($post->title)) { $title = $post->title; }
        @endphp
        <article class="blog-editorial-item">
          <a href="/blog/{{ $slug }}" class="blog-editorial-card">
            <div class="blog-editorial-thumb">
              @if($image)
                <img alt="{{ $title }} - Lanyard custom Indonesia {{ $cat }}" class="blog-editorial-img" src="{{ $image }}" @if($srcset = \App\Support\ResponsiveImage::srcset($image)) srcset="{{ $srcset }}" sizes="(min-width: 1024px) 405px, (min-width: 700px) 50vw, 100vw" @endif loading="lazy" decoding="async" width="640" height="400" />
              @else
                <div class="blog-editorial-img-fallback" aria-hidden="true">
                  <span class="material-symbols-outlined">photo_library</span>
                </div>
              @endif
              <div class="blog-editorial-gradient" aria-hidden="true"></div>
              <span class="blog-editorial-badge">{{ $cat }}</span>
              <span class="blog-editorial-corner" aria-hidden="true">
                <span class="material-symbols-outlined">arrow_outward</span>
              </span>
            </div>
            <div class="blog-editorial-body">
              <h3 class="blog-editorial-posttitle">{{ $title }}</h3>
              <div class="blog-editorial-meta">
                @if($dateLabel)<span>{{ $dateLabel }}</span><span class="blog-editorial-dot">·</span>@endif
                <span>{{ $rt }} menit baca</span>
              </div>
            </div>
          </a>
        </article>
      @endforeach
    </div>

  </div>
</section>

<style>
  .blog-editorial{ /* padding handled inline Fase 4 */ }
  .blog-editorial-header{ display:flex; flex-direction:column; gap:1.25rem; margin-bottom:3.25rem; }
  @media(min-width:900px){
    .blog-editorial-header{ flex-direction:row; justify-content:space-between; align-items:flex-end; gap:2rem; margin-bottom:3.5rem; }
  }
  .blog-editorial-title{ font-size:clamp(2rem,4vw,2.75rem); font-weight:700; line-height:1.08; letter-spacing:-0.03em; color:#0f172a; }
  .blog-editorial-desc{ margin-top:0.75rem; font-size:16px; line-height:1.6; letter-spacing:-0.01em; color:#64748b; max-width:36rem; }
  .blog-editorial-link{ display:inline-flex; align-items:center; gap:0.5rem; font-size:14px; font-weight:600; letter-spacing:-0.01em; color:#334155; white-space:nowrap; transition:color 200ms ease-out; text-decoration:none; }
  .blog-editorial-link:hover{ color:#0f172a; }
  .blog-editorial-link-arrow{ display:inline-flex; width:26px; height:26px; align-items:center; justify-content:center; border-radius:9999px; background:#f8fafc; border:1px solid #f1f5f9; transition:transform 250ms cubic-bezier(0.22,1,0.36,1), background-color 220ms ease-out, border-color 220ms ease-out; }
  .blog-editorial-link-arrow .material-symbols-outlined{ font-size:18px; line-height:1; }
  .blog-editorial-link:hover .blog-editorial-link-arrow{ transform:translateX(6px); background:#0f172a; border-color:#0f172a; color:#fff; }
  .blog-editorial-grid{ display:grid; grid-template-columns:1fr; gap:2rem 1.5rem; }
  @media(min-width:700px){ .blog-editorial-grid{ grid-template-columns:repeat(2,minmax(0,1fr)); gap:2.25rem 1.5rem; } }
  @media(min-width:1024px){ .blog-editorial-grid{ grid-template-columns:repeat(3,minmax(0,1fr)); gap:2.5rem 2rem; } }
  .blog-editorial-item{ min-width:0; }
  .blog-editorial-card{ display:flex; flex-direction:column; gap:0; text-decoration:none; outline:none; }
  .blog-editorial-thumb{ position:relative; aspect-ratio:16/10; border-radius:22px; overflow:hidden; background:#f8fafc; isolation:isolate; transform:translateZ(0); transition:transform 250ms cubic-bezier(0.22,1,0.36,1), box-shadow 250ms ease-out; will-change:transform; }
  @media(min-width:1024px){ .blog-editorial-thumb{ border-radius:24px; } }
  .blog-editorial-img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transform:scale(1); transition:transform 600ms cubic-bezier(0.22,1,0.36,1); will-change:transform; }
  .blog-editorial-img-fallback{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:radial-gradient(120% 120% at 20% 10%, #f1f5f9 0%, #e2e8f0 55%, #cbd5e1 100%); color:#94a3b8; }
  .blog-editorial-img-fallback .material-symbols-outlined{ font-size:40px; }
  .blog-editorial-gradient{ position:absolute; inset:0; z-index:1; background:linear-gradient(180deg,rgba(2,6,23,0.04) 0%,rgba(2,6,23,0) 38%), linear-gradient(0deg,rgba(2,6,23,0.42) 0%,rgba(2,6,23,0.08) 44%,rgba(2,6,23,0) 78%); transition:opacity 250ms ease-out; pointer-events:none; }
  .blog-editorial-badge{ position:absolute; top:14px; left:14px; z-index:2; display:inline-flex; align-items:center; height:26px; padding:0 10px; border-radius:9999px; background:rgba(255,255,255,0.92); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.7); font-size:10.5px; font-weight:700; letter-spacing:0.08em; color:#0f172a; box-shadow:0 4px 16px -8px rgba(15,23,42,0.22); }
  .blog-editorial-corner{ position:absolute; right:12px; bottom:12px; z-index:2; width:34px; height:34px; border-radius:9999px; display:inline-flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.92); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.75); color:#0f172a; opacity:0; transform:translateY(6px) scale(0.96); transition:opacity 220ms ease-out, transform 250ms cubic-bezier(0.22,1,0.36,1), background-color 220ms ease-out, color 220ms ease-out, border-color 220ms ease-out; box-shadow:0 8px 20px -12px rgba(15,23,42,0.28); }
  .blog-editorial-corner .material-symbols-outlined{ font-size:18px; }
  .blog-editorial-body{ padding:14px 2px 0 2px; display:flex; flex-direction:column; gap:8px; min-width:0; }
  .blog-editorial-posttitle{ font-size:17px; line-height:1.32; font-weight:630; letter-spacing:-0.022em; color:#0f172a; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:calc(1.32em * 2); transition:color 200ms ease-out, transform 250ms cubic-bezier(0.22,1,0.36,1); will-change:transform,color; }
  .blog-editorial-meta{ display:flex; flex-wrap:wrap; align-items:center; gap:6px; font-size:12.5px; line-height:1.5; letter-spacing:-0.01em; color:#94a3b8; }
  .blog-editorial-dot{ opacity:0.7; }
  .blog-editorial-card:hover .blog-editorial-thumb{ transform:translateY(-4px); box-shadow:0 18px 40px -20px rgba(15,23,42,0.28), 0 8px 18px -14px rgba(15,23,42,0.14); }
  .blog-editorial-card:hover .blog-editorial-img{ transform:scale(1.05); }
  .blog-editorial-card:hover .blog-editorial-gradient{ opacity:1; background:linear-gradient(180deg,rgba(2,6,23,0.06) 0%,rgba(2,6,23,0) 40%), linear-gradient(0deg,rgba(2,6,23,0.56) 0%,rgba(2,6,23,0.14) 48%,rgba(2,6,23,0) 82%); }
  .blog-editorial-card:hover .blog-editorial-posttitle{ color:#1e293b; transform:translateY(-1px); }
  .blog-editorial-card:hover .blog-editorial-corner{ opacity:1; transform:translateY(0) scale(1); background:#0f172a; color:#fff; border-color:#0f172a; }
  @media(prefers-reduced-motion:reduce){
    .blog-editorial-thumb,.blog-editorial-img,.blog-editorial-gradient,.blog-editorial-corner,.blog-editorial-posttitle,.blog-editorial-link-arrow{ transition:none !important; }
    .blog-editorial-card:hover .blog-editorial-thumb{ transform:none !important; }
    .blog-editorial-card:hover .blog-editorial-img{ transform:none !important; }
  }
</style>
