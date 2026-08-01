@php
  use App\Models\Post;
  use Illuminate\Support\Facades\Cache;

  $posts = Cache::store(config('cache.public_store', 'file'))->remember('home_posts', 3600, function () {
    return Post::with('category')
      ->where('published', true)
      ->orderBy('createdAt', 'desc')
      ->take(3)
      ->get();
  });

  if ($posts->isEmpty()) {
    $posts = collect([
      (object) [
        'title' => 'Panduan Memilih Bahan Lanyard untuk Kebutuhan Kantor',
        'slug' => 'panduan-memilih-bahan-lanyard',
        'featuredImage' => '/uploads/galeri-lanyard-01.webp',
        'category' => (object) ['name' => 'Panduan'],
        'createdAt' => now()->subDays(3),
        'content' => 'Kenali karakter setiap bahan agar lanyard nyaman digunakan dan sesuai dengan kebutuhan perusahaan.',
      ],
      (object) [
        'title' => 'Tips Menyiapkan Desain Lanyard Custom yang Terlihat Rapi',
        'slug' => 'tips-desain-lanyard-custom',
        'featuredImage' => '/uploads/galeri-lanyard-03.webp',
        'category' => (object) ['name' => 'Tips'],
        'createdAt' => now()->subDays(7),
        'content' => 'Gunakan file logo berkualitas dan kombinasi warna yang tepat untuk mendapatkan hasil cetak yang tajam.',
      ],
      (object) [
        'title' => 'Lanyard dan ID Card untuk Identitas Tim yang Konsisten',
        'slug' => 'lanyard-id-card-identitas-tim',
        'featuredImage' => '/uploads/galeri-lanyard-05.webp',
        'category' => (object) ['name' => 'Inspirasi'],
        'createdAt' => now()->subDays(12),
        'content' => 'Padukan lanyard dan holder ID card agar identitas tim mudah dikenali dalam kegiatan sehari-hari.',
      ],
    ]);
  }

  $asset = fn ($source) => \App\Support\AssetUrl::relative($source) ?: '/uploads/galeri-lanyard-01.webp';
  $formatDate = function ($value): string {
    try {
      return \Carbon\Carbon::parse($value)->locale('id')->translatedFormat('d F Y');
    } catch (\Throwable) {
      return '';
    }
  };
@endphp

<section class="home-blog" id="blog" aria-labelledby="home-blog-title">
  <div class="home-shell">
    <header class="home-blog__header">
      <div class="home-section-heading home-section-heading--left">
        <p>Artikel Terbaru</p>
        <h2 id="home-blog-title">Panduan Seputar Lanyard</h2>
      </div>
      <a href="{{ route('blog.index') }}">
        Lihat Semua Artikel
        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
      </a>
    </header>

    <div class="home-blog__grid">
      @foreach ($posts as $post)
        @php
          $image = $asset($post->featuredImage ?? null);
          $excerpt = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($post->content ?? ''))));
          $category = trim((string) ($post->category->name ?? 'Artikel')) ?: 'Artikel';
        @endphp
        <article class="home-blog__card">
          <a href="/blog/{{ $post->slug }}">
            <img
              src="{{ $image }}"
              @if ($srcset = \App\Support\ResponsiveImage::srcset($image))
                srcset="{{ $srcset }}"
                sizes="(min-width: 1024px) 360px, (min-width: 640px) 45vw, 92vw"
              @endif
              alt="{{ $post->title }}"
              width="640"
              height="400"
              loading="lazy"
              decoding="async"
            >
            <div class="home-blog__body">
              <span>{{ $category }}</span>
              <h3>{{ $post->title }}</h3>
              @if ($excerpt !== '')
                <p>{{ \Illuminate\Support\Str::limit($excerpt, 105) }}</p>
              @endif
              <small>{{ $formatDate($post->createdAt ?? $post->created_at ?? null) }}</small>
            </div>
          </a>
        </article>
      @endforeach
    </div>
  </div>
</section>
