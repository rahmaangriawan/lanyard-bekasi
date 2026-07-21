<?php

use App\Http\Controllers\Cms\CmsResourceController;
use App\Http\Controllers\Cms\DashboardController;
use App\Http\Controllers\Cms\ProductSyncController;
use App\Http\Controllers\Cms\UtilityPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\AuthorSlugRedirect;
use App\Models\Category;
use App\Models\CityPage;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use App\Support\OpenGraphImage;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;

$serveLegacyPublicAsset = function (string $directory, string $path) {
    $rootPublic = dirname(base_path(), 2).DIRECTORY_SEPARATOR.'public';
    $bases = array_filter(array_unique([
        realpath(public_path($directory)),
        realpath($rootPublic.DIRECTORY_SEPARATOR.$directory),
    ]));

    foreach ($bases as $base) {
        $file = realpath($base.DIRECTORY_SEPARATOR.$path);

        if ($file && str_starts_with($file, $base) && is_file($file)) {
            $imageInfo = @getimagesize($file);
            $mimeType = is_array($imageInfo) && isset($imageInfo['mime'])
                ? (string) $imageInfo['mime']
                : (mime_content_type($file) ?: 'application/octet-stream');

            return response()->file($file, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }
    }

    abort(404);
};

$serveStorageAsset = function (string $path) {
    $rootPublic = dirname(base_path(), 2).DIRECTORY_SEPARATOR.'public';
    $bases = array_filter([
        realpath(storage_path('app/public')),
        realpath($rootPublic.DIRECTORY_SEPARATOR.'storage'),
    ]);

    foreach ($bases as $base) {
        $file = realpath($base.DIRECTORY_SEPARATOR.$path);

        if ($file && str_starts_with($file, $base) && is_file($file)) {
            return response()->file($file, [
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }
    }

    abort(404);
};

$statelessPublicMiddleware = [
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    ShareErrorsFromSession::class,
    ValidateCsrfToken::class,
    HandleInertiaRequests::class,
    AddLinkHeadersForPreloadedAssets::class,
];

Route::get('/', function () {
    return response()
        ->view('pages.home')
        ->header('Cache-Control', 'public, no-cache, must-revalidate');
})->withoutMiddleware($statelessPublicMiddleware)->name('home');

Route::get('/font-assets/{font}', function (string $font) {
    $path = match ($font) {
        'geist-400' => public_path('fonts/geist/Geist-400.woff2'),
        'geist-700' => public_path('fonts/geist/Geist-700.woff2'),
        'inter-latin' => public_path('fonts/inter/Inter-latin.woff2'),
        default => null,
    };

    abort_unless(is_string($path) && is_file($path), 404);

    return response()->file($path, [
        'Content-Type' => 'font/woff2',
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('font', '[a-z0-9-]+')->withoutMiddleware($statelessPublicMiddleware)->name('assets.fonts');

$sitemapResponse = static function (array $urls) {
    return response()
        ->view('components.sitemap', compact('urls'))
        ->header('Content-Type', 'application/xml; charset=UTF-8')
        ->header('Cache-Control', 'public, max-age=3600, must-revalidate');
};

$sitemapBaseUrl = static function (): string {
    $base = rtrim((string) config('app.url', url('/')), '/');

    return $base !== '' ? $base : url('/');
};

$cachedSitemapUrls = static function (string $key, callable $resolver): array {
    return Cache::store(config('cache.public_store', 'file'))
        ->remember($key, now()->addHour(), $resolver);
};

Route::get('/sitemap.xml', function () use ($sitemapBaseUrl) {
    $base = $sitemapBaseUrl();
    $pageLastModified = \Illuminate\Support\Carbon::createFromTimestamp(filemtime(base_path('routes/web.php')))->toAtomString();
    $lastModified = static fn (?string $value): ?string => $value ? \Illuminate\Support\Carbon::parse($value)->toAtomString() : null;
    $sitemaps = [
        ['loc' => $base.'/page-sitemap.xml', 'lastmod' => $pageLastModified],
        ['loc' => $base.'/product-sitemap.xml', 'lastmod' => $lastModified(Product::query()->where('published', true)->max('updatedAt'))],
        ['loc' => $base.'/post-sitemap.xml', 'lastmod' => $lastModified(Post::query()->where('published', true)->max('updatedAt'))],
        ['loc' => $base.'/city-sitemap.xml', 'lastmod' => $lastModified(CityPage::query()->where('published', true)->max('updatedAt'))],
    ];

    return response()
        ->view('components.sitemap-index', compact('sitemaps'))
        ->header('Content-Type', 'application/xml; charset=UTF-8')
        ->header('Cache-Control', 'public, max-age=3600, must-revalidate');
})->withoutMiddleware($statelessPublicMiddleware)->name('sitemap.index');

Route::get('/page-sitemap.xml', function () use ($sitemapBaseUrl, $sitemapResponse) {
    $base = $sitemapBaseUrl();
    $urls = collect([
        ['loc' => $base.'/', 'changefreq' => 'weekly', 'priority' => '1.0'],
        ['loc' => $base.'/produk', 'changefreq' => 'weekly', 'priority' => '0.9'],
        ['loc' => $base.'/area', 'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => $base.'/cara-pemesanan', 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => $base.'/daftar-harga', 'changefreq' => 'weekly', 'priority' => '0.8'],
        ['loc' => $base.'/promo', 'changefreq' => 'weekly', 'priority' => '0.7'],
        ['loc' => $base.'/pengiriman', 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => $base.'/blog', 'changefreq' => 'daily', 'priority' => '0.8'],
    ])->all();

    return $sitemapResponse($urls);
})->withoutMiddleware($statelessPublicMiddleware)->name('sitemap.pages');

Route::get('/product-sitemap.xml', function () use ($sitemapBaseUrl, $sitemapResponse, $cachedSitemapUrls) {
    $base = $sitemapBaseUrl();
    $urls = $cachedSitemapUrls('public_sitemap_products', function () use ($base): array {
        return Product::query()
            ->where('published', true)
            ->orderByDesc('updatedAt')
            ->get(['slug', 'updatedAt'])
            ->map(fn (Product $product): array => [
                'loc' => $base.'/produk/'.$product->slug,
                'lastmod' => $product->updatedAt,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ])
            ->all();
    });

    return $sitemapResponse($urls);
})->withoutMiddleware($statelessPublicMiddleware)->name('sitemap.products');

Route::get('/post-sitemap.xml', function () use ($sitemapBaseUrl, $sitemapResponse, $cachedSitemapUrls) {
    $base = $sitemapBaseUrl();
    $urls = $cachedSitemapUrls('public_sitemap_posts', function () use ($base): array {
        return Post::query()
            ->where('published', true)
            ->orderByDesc('updatedAt')
            ->get(['slug', 'updatedAt'])
            ->map(fn (Post $post): array => [
                'loc' => $base.'/blog/'.$post->slug,
                'lastmod' => $post->updatedAt,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ])
            ->all();
    });

    return $sitemapResponse($urls);
})->withoutMiddleware($statelessPublicMiddleware)->name('sitemap.posts');

Route::get('/og/blog/{slug}.png', function (string $slug) {
    $post = Post::query()->with('category')->where('published', true)->where('slug', $slug)->firstOrFail();
    $version = optional($post->updatedAt)->timestamp ?: 0;
    $bytes = Cache::store(config('cache.public_store', 'file'))->remember(
        'og_blog_'.sha1($post->slug.'|'.$version.'|'.$post->featuredImage),
        now()->addDay(),
        fn (): string => OpenGraphImage::render($post),
    );

    return response($bytes, 200, [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
        'ETag' => '"'.sha1($bytes).'"',
    ]);
})->where('slug', '[A-Za-z0-9-]+')->withoutMiddleware($statelessPublicMiddleware)->name('blog.og-image');

Route::get('/city-sitemap.xml', function () use ($sitemapBaseUrl, $sitemapResponse, $cachedSitemapUrls) {
    $base = $sitemapBaseUrl();
    $urls = $cachedSitemapUrls('public_sitemap_cities', function () use ($base): array {
        return CityPage::query()
            ->where('published', true)
            ->orderByDesc('updatedAt')
            ->get(['slug', 'updatedAt'])
            ->map(fn (CityPage $cityPage): array => [
                'loc' => $base.'/area/'.$cityPage->slug,
                'lastmod' => $cityPage->updatedAt,
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ])
            ->all();
    });

    return $sitemapResponse($urls);
})->withoutMiddleware($statelessPublicMiddleware)->name('sitemap.cities');

Route::get('/media/{path}', fn (string $path) => $serveLegacyPublicAsset('media', $path))->where('path', '.*');
Route::get('/uploads/{path}', fn (string $path) => $serveLegacyPublicAsset('uploads', $path))->where('path', '.*');
Route::get('/images/{path}', fn (string $path) => $serveLegacyPublicAsset('images', $path))->where('path', '.*');
Route::get('/storage/{path}', fn (string $path) => $serveStorageAsset($path))->where('path', '.*');

// Public pages
Route::view('/produk', 'pages.produk.index')->name('products.index');
Route::get('/produk/{slug}', function (string $slug) {
    $product = Product::query()
        ->with('category')
        ->where('published', true)
        ->where('slug', $slug)
        ->firstOrFail();

    return view('pages.produk.show', compact('product'));
})->name('products.show');

Route::get('/area', function () {
    $cityPages = CityPage::query()
        ->where('published', true)
        ->orderBy('title')
        ->get(['title', 'slug']);

    return view('pages.area', compact('cityPages'));
})->name('area');

Route::get('/area/{slug}', function (string $slug) {
    $cityPage = CityPage::query()
        ->where('published', true)
        ->where('slug', $slug)
        ->firstOrFail();

    return view('pages.area.show', compact('cityPage'));
})->name('area.show');
Route::view('/cara-pemesanan', 'pages.cara-pemesanan')->name('ordering');
Route::view('/daftar-harga', 'pages.daftar-harga')->name('pricing');
Route::view('/promo', 'pages.promo')->name('promo');
Route::view('/pengiriman', 'pages.pengiriman')->name('shipping');
Route::view('/kebijakan', 'pages.kebijakan')->name('privacy');
Route::view('/syarat-ketentuan', 'pages.syarat-ketentuan')->name('terms');

Route::view('/blog', 'pages.blog.index')->name('blog.index');
Route::get('/kategori/{slug}', function (string $slug) {
    $category = Category::query()
        ->where('type', 'BLOG')
        ->where('slug', $slug)
        ->firstOrFail();

    $posts = Post::query()
        ->with(['category', 'author'])
        ->where('published', true)
        ->where('categoryId', $category->id)
        ->orderByDesc('createdAt')
        ->paginate(9)
        ->withQueryString();

    return view('pages.blog.category', compact('category', 'posts'));
})->name('blog.category');

Route::get('/tag/{slug}', function (string $slug) {
    $matchingPosts = Post::query()
        ->with(['category', 'author'])
        ->where('published', true)
        ->whereNotNull('tags')
        ->orderByDesc('createdAt')
        ->get()
        ->filter(function (Post $post) use ($slug): bool {
            return collect(explode(',', (string) $post->tags))
                ->contains(fn (string $tag): bool => Str::slug(trim($tag)) === $slug);
        })
        ->values();

    $matchedTag = $matchingPosts
        ->flatMap(fn (Post $post) => explode(',', (string) $post->tags))
        ->map(fn (string $tag) => trim($tag))
        ->first(fn (string $tag): bool => Str::slug($tag) === $slug);
    $tagName = $matchedTag ?: Str::headline($slug);
    $page = LengthAwarePaginator::resolveCurrentPage();
    $perPage = 9;
    $posts = new LengthAwarePaginator(
        $matchingPosts->forPage($page, $perPage)->values(),
        $matchingPosts->count(),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()],
    );

    return view('pages.blog.tag', compact('tagName', 'posts'));
})->name('blog.tag');

Route::get('/blog/penulis/{slug}', function (string $slug) {
    $author = User::query()->where('slug', $slug)->first();

    if (! $author) {
        $redirect = AuthorSlugRedirect::query()->with('author')->where('slug', $slug)->first();
        abort_unless($redirect?->author, 404);

        return redirect('/blog/penulis/'.$redirect->author->slug, 301);
    }

    $authorSlug = $author->slug ?: Str::slug($author->name);
    $posts = Post::query()
        ->with(['category', 'author'])
        ->where('published', true)
        ->where('authorId', $author->id)
        ->orderByDesc('createdAt')
        ->paginate(12)
        ->withQueryString();

    return view('pages.blog.author', compact('author', 'authorSlug', 'posts'));
})->name('blog.author');

Route::get('/blog/{slug}', function (string $slug) {
    $post = Post::query()
        ->with(['category', 'author'])
        ->where('published', true)
        ->where('slug', $slug)
        ->firstOrFail();

    $comments = $post->comments()
        ->where('approved', true)
        ->orderByDesc('createdAt')
        ->get();
    $relatedPosts = Post::query()
        ->where('published', true)
        ->whereKeyNot($post->id)
        ->when($post->categoryId, fn ($query) => $query->where('categoryId', $post->categoryId))
        ->orderByDesc('createdAt')
        ->limit(4)
        ->get();

    return view('pages.blog.show', compact('post', 'comments', 'relatedPosts'));
})->name('blog.show');

Route::post('/blog/{slug}/comments', function (Request $request, string $slug) {
    $post = Post::query()
        ->where('published', true)
        ->where('slug', $slug)
        ->firstOrFail();
    $data = $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'email', 'max:190'],
        'content' => ['required', 'string', 'max:2000'],
    ]);

    Comment::query()->create([
        'postId' => $post->id,
        'name' => $data['name'],
        'email' => $data['email'],
        'content' => $data['content'],
        'approved' => false,
    ]);

    return redirect('/blog/'.$post->slug.'#comments')
        ->with('comment_sent', 'Komentar berhasil dikirim dan menunggu moderasi.');
})->middleware('throttle:5,1')->name('blog.comment');

Route::redirect('/dashboard', '/kawruh');
Route::get('/kawruh/login', fn () => redirect(Auth::check() ? '/dashboard' : '/login'));
Route::redirect('/login/blackout', '/login');

Route::middleware(['auth', 'verified', 'admin'])->prefix('kawruh')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/analytics', [UtilityPageController::class, 'analytics'])->name('cms.analytics');
    Route::get('/not-found-urls', [UtilityPageController::class, 'notFoundReport'])->name('cms.not-found-report');
    Route::redirect('/blog', '/kawruh/posts');
    Route::redirect('/categories-produk', '/kawruh/categories?type=PRODUCT');
    Route::redirect('/categories-blog', '/kawruh/categories?type=BLOG');
    Route::redirect('/cities', '/kawruh/city-pages');
    Route::redirect('/komentar', '/kawruh/comments');
    Route::redirect('/formulir', '/kawruh/inquiries');
    Route::redirect('/portofolio', '/kawruh/portfolio');
    Route::get('/profile', [UtilityPageController::class, 'profile'])->name('cms.profile');
    Route::get('/media-picker', [CmsResourceController::class, 'mediaPicker'])->name('cms.media-picker');
    Route::post('/products/sync', ProductSyncController::class)->name('cms.products.sync');
    Route::get('/{resource}', [CmsResourceController::class, 'index'])->name('cms.index');
    Route::get('/{resource}/create', [CmsResourceController::class, 'create'])->name('cms.create');
    Route::post('/{resource}/bulk', [CmsResourceController::class, 'bulk'])->name('cms.bulk');
    Route::post('/{resource}', [CmsResourceController::class, 'store'])->name('cms.store');
    Route::get('/{resource}/{id}/edit', [CmsResourceController::class, 'edit'])->name('cms.edit');
    Route::patch('/{resource}/{id}', [CmsResourceController::class, 'update'])->name('cms.update');
    Route::delete('/{resource}/{id}', [CmsResourceController::class, 'destroy'])->name('cms.destroy');
    Route::post('/{resource}/{id}/toggle', [CmsResourceController::class, 'toggle'])->name('cms.toggle');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Redirect URL artikel lama yang sebelumnya berada langsung di root domain.
Route::get('/{slug}', function (string $slug) {
    abort_unless(Post::query()->where('published', true)->where('slug', $slug)->exists(), 404);

    return redirect('/blog/'.$slug, 301);
})->where('slug', '[A-Za-z0-9-]+')->name('blog.legacy');
