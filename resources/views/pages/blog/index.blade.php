@extends('layouts.public')

@section('title', 'Blog Pusat Tali Lanyard — Tips, Insight & Update')

@if(request()->filled('q') || request()->integer('page', 1) > 1)
  @section('seo_noindex', '1')
@endif

@section('content')
@php
  use App\Models\Post;
  use Illuminate\Support\Facades\Cache;

  $search = mb_substr(trim((string) request('q', '')), 0, 255);
  $escapedSearch = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);
  $page = max(1, (int) request('page', 1));
  $perPage = 9;

  $query = Post::with('category', 'author')
    ->where('published', true);

  if ($search !== '') {
    $query->where(function ($q) use ($escapedSearch) {
      $term = "%{$escapedSearch}%";

      $q->whereRaw("title LIKE ? ESCAPE '!'", [$term])
        ->orWhereRaw("metaDescription LIKE ? ESCAPE '!'", [$term])
        ->orWhereRaw("content LIKE ? ESCAPE '!'", [$term]);
    });
  }

  $query->orderBy('createdAt', 'desc');
  $total = $query->count();
  $posts = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
  $hasMore = ($page * $perPage) < $total;
  $nextPage = $hasMore ? $page + 1 : null;

  // Fallback dummy data
  if ($posts->isEmpty() && !$search) {
    $posts = collect([
      (object) [
        'title' => 'Pentingnya Lanyard untuk Keamanan Event Skala Besar',
        'slug' => 'pentingnya-lanyard-event',
        'metaDescription' => 'Lanyard bukan sekadar aksesoris, tapi instrumen keamanan penting untuk identifikasi peserta di event besar. Simak panduan lengkapnya.',
        'featuredImage' => null,
        'category' => (object) ['name' => 'Events'],
        'author' => (object) ['name' => 'Tim Pusat Tali Lanyard'],
        'createdAt' => now()->subDays(2),
      ],
      (object) [
        'title' => 'Tips Mendesain Lanyard yang Elegan untuk Brand Perusahaan',
        'slug' => 'tips-mendesain-lanyard',
        'metaDescription' => 'Pemilihan warna dan font yang tepat sangat krusial dalam mencerminkan identitas brand perusahaan Anda.',
        'featuredImage' => null,
        'category' => (object) ['name' => 'Branding'],
        'author' => (object) ['name' => 'Tim Pusat Tali Lanyard'],
        'createdAt' => now()->subDays(5),
      ],
      (object) [
        'title' => 'Tissue vs Polyester: Mana Bahan Lanyard Terbaik?',
        'slug' => 'tissue-vs-polyester',
        'metaDescription' => 'Membedah perbedaan kualitas, kenyamanan, dan harga antara material tissue dan polyester untuk lanyard custom.',
        'featuredImage' => null,
        'category' => (object) ['name' => 'Material'],
        'author' => (object) ['name' => 'Tim Pusat Tali Lanyard'],
        'createdAt' => now()->subDays(7),
      ],
      (object) [
        'title' => '5 Jenis Hook Lanyard yang Wajib Anda Ketahui',
        'slug' => 'jenis-hook-lanyard',
        'metaDescription' => 'Dari hook plastik standar hingga stainless steel premium, ini dia pilihan hook terbaik untuk lanyard Anda.',
        'featuredImage' => null,
        'category' => (object) ['name' => 'Aksesoris'],
        'author' => (object) ['name' => 'Tim Pusat Tali Lanyard'],
        'createdAt' => now()->subDays(9),
      ],
      (object) [
        'title' => 'Panduan Lengkap Sablon Lanyard: Teknik & Hasil Terbaik',
        'slug' => 'panduan-sablon-lanyard',
        'metaDescription' => 'Teknik sablon yang tepat menghasilkan warna tajam dan tahan lama untuk lanyard promosi perusahaan Anda.',
        'featuredImage' => null,
        'category' => (object) ['name' => 'Produksi'],
        'author' => (object) ['name' => 'Tim Pusat Tali Lanyard'],
        'createdAt' => now()->subDays(12),
      ],
      (object) [
        'title' => 'Mengapa Perusahaan Multinasional Memilih Lanyard Custom?',
        'slug' => 'lanyard-custom-korporat',
        'metaDescription' => 'Lanyard custom jadi bagian dari strategi branding korporat. Pelajari alasannya di sini.',
        'featuredImage' => null,
        'category' => (object) ['name' => 'Korporat'],
        'author' => (object) ['name' => 'Tim Pusat Tali Lanyard'],
        'createdAt' => now()->subDays(15),
      ],
      (object) [
        'title' => 'Ukuran Standar Tali ID Card: Panduan Lengkap',
        'slug' => 'ukuran-tali-id-card',
        'metaDescription' => 'Berapa ukuran ideal tali ID Card? Simak rekomendasi ukuran standar untuk berbagai kebutuhan.',
        'featuredImage' => null,
        'category' => (object) ['name' => 'Tips'],
        'author' => (object) ['name' => 'Tim Pusat Tali Lanyard'],
        'createdAt' => now()->subDays(18),
      ],
      (object) [
        'title' => 'Cetak Lanyard Custom untuk Seminar Kampus: Budget-Friendly',
        'slug' => 'lanyard-seminar-kampus',
        'metaDescription' => 'Tips hemat cetak lanyard custom untuk acara kampus tanpa mengorbankan kualitas.',
        'featuredImage' => null,
        'category' => (object) ['name' => 'Edukasi'],
        'author' => (object) ['name' => 'Tim Pusat Tali Lanyard'],
        'createdAt' => now()->subDays(21),
      ],
      (object) [
        'title' => 'Perbandingan Harga Lanyard Custom di Jabodetabek 2025',
        'slug' => 'perbandingan-harga-lanyard',
        'metaDescription' => 'Kami bandingkan harga cetak lanyard custom dari berbagai vendor di Jabodetabek. Simak hasilnya!',
        'featuredImage' => null,
        'category' => (object) ['name' => 'Review'],
        'author' => (object) ['name' => 'Tim Pusat Tali Lanyard'],
        'createdAt' => now()->subDays(25),
      ],
    ]);
    $total = $posts->count();
    $hasMore = false;
  }

  $img = fn ($src) => \App\Support\AssetUrl::relative($src);
@endphp

{{-- Hero --}}
<section class="bg-white py-16 sm:py-20 border-b border-slate-200">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    <p class="text-xs font-extrabold uppercase tracking-normal text-slate-600">Blog</p>
    <h1 class="mt-4 font-bold leading-[1.08] text-slate-900" style="font-size: clamp(2rem, 4vw, 2.75rem);">Update & Insight Lanyard</h1>
    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">Artikel terbaru seputar lanyard custom, tips branding, tren event, dan panduan cetak untuk kebutuhan profesional Anda.</p>

    {{-- Search --}}
    <form class="mt-8 flex max-w-2xl gap-3" method="GET" action="/blog">
      <input
        class="min-h-12 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-600 focus:ring-2 focus:ring-slate-200"
        type="search"
        name="q"
        value="{{ $search }}"
        maxlength="255"
        placeholder="Cari artikel..."
        autocomplete="off"
      />
      <button class="min-h-12 shrink-0 rounded-xl bg-slate-800 px-5 text-sm font-bold text-white transition hover:bg-slate-900 active:scale-95" type="submit">Cari</button>
      @if ($search)
      <a href="/blog" class="min-h-12 shrink-0 flex items-center rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Hapus</a>
      @endif
    </form>

    <p class="mt-4 text-sm text-slate-500">
      @if($search)
        @if($total > 0)
        Menampilkan {{ $posts->count() }} hasil untuk "<strong>{{ $search }}</strong>"
        @else
        Tidak ada artikel yang cocok untuk "<strong>{{ $search }}</strong>"
        @endif
      @else
        {{ $total > 0 ? "Menampilkan {$posts->count()} dari {$total} artikel" : 'Belum ada artikel tersedia' }}
      @endif
    </p>
  </div>
</section>

{{-- Blog Grid --}}
<section class="bg-slate-50 py-12 sm:py-16">
  <div class="mx-auto max-w-[1280px] px-6 lg:px-10">
    @if ($posts->isEmpty())
      <div class="text-center py-20">
        <span class="material-symbols-outlined text-6xl text-slate-300">article</span>
        <h2 class="mt-6 text-xl font-bold text-slate-900">Artikel tidak ditemukan</h2>
        <p class="mt-2 text-sm text-slate-600">Coba gunakan kata kunci yang berbeda.</p>
      </div>
    @else
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      @foreach ($posts as $index => $post)
      @php
        $textOnly = trim(strip_tags($post->content ?? ''));
        $wordCount = $textOnly ? count(preg_split('/\s+/', $textOnly)) : 0;
        $readingTime = max(1, (int) ceil($wordCount / 200));
      @endphp
      <article class="group flex min-h-full flex-col rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md hover:border-slate-300">
        {{-- Image --}}
        <a href="/blog/{{ $post->slug }}" class="overflow-hidden rounded-[14px] bg-slate-100 block">
          @if($post->featuredImage)
          <img class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]" src="{{ $img($post->featuredImage) }}" alt="{{ $post->title }}" loading="lazy" decoding="async" width="360" height="270" />
          @else
          <div class="aspect-[4/3] w-full bg-slate-200 group-hover:scale-[1.03] transition-transform duration-500 flex items-center justify-center">
            <span class="material-symbols-outlined text-5xl text-slate-300">article</span>
          </div>
          @endif
        </a>

        {{-- Meta Row: Author Avatar, Name, Date, Reading Time --}}
        <div class="mt-5 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            @if(!empty($post->author->avatar))
            <img src="{{ $img($post->author->avatar) }}" alt="{{ $post->author->name }}" class="h-[30px] w-[30px] rounded-full object-cover shadow-sm bg-slate-100" width="30" height="30" loading="lazy" decoding="async" />
            @else
            <div class="grid h-[30px] w-[30px] place-items-center rounded-full bg-slate-800 text-[11px] font-extrabold text-white shadow-sm">
              {{ strtoupper(substr($post->author->name ?? 'Admin', 0, 1)) }}
            </div>
            @endif
            <span class="text-[13px] font-bold text-slate-800 line-clamp-1 max-w-[130px] sm:max-w-[150px]">{{ $post->author->name ?? 'Tim Lanyard' }}</span>
          </div>
          <div class="text-[12px] font-medium text-slate-500 whitespace-nowrap">
            <span>{{ \Carbon\Carbon::parse($post->createdAt)->translatedFormat('j M Y') }}</span>
            <span class="mx-1 text-slate-300">•</span>
            <span>{{ $readingTime }} mins</span>
          </div>
        </div>

        {{-- Body Content --}}
        <div class="mt-4 flex flex-1 flex-col">
          {{-- Title --}}
          <a href="/blog/{{ $post->slug }}" class="block mb-2.5">
            <h2 class="text-[1.1rem] font-bold leading-[1.35] text-slate-900 group-hover:text-slate-700 transition-colors line-clamp-2">
              {{ $post->title }}
            </h2>
          </a>

          {{-- Excerpt --}}
          <p class="text-[14px] leading-relaxed text-slate-500 line-clamp-2">
            {{ $post->metaDescription ?: 'Baca selengkapnya tentang topik ini di blog Pusat Tali Lanyard.' }}
          </p>
        </div>
      </article>
      @endforeach
    </div>

    {{-- Pagination / Load More --}}
    @if ($hasMore)
    <div class="mt-12 text-center">
      <a
        href="?{{ http_build_query(['q' => $search, 'page' => $nextPage]) }}"
        class="inline-flex min-h-12 items-center rounded-xl border border-slate-800 px-6 text-sm font-bold text-slate-800 transition hover:bg-slate-800 hover:text-white active:scale-95"
      >
        Muat lebih banyak
      </a>
    </div>
    @endif
    @endif
  </div>
</section>

@include('components.cta')
@include('components.footer')
@endsection
