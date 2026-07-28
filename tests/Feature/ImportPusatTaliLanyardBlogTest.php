<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportPusatTaliLanyardBlogTest extends TestCase
{
    use RefreshDatabase;

    private string $png;

    private User $author;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        $this->author = User::factory()->create([
            'name' => 'Penulis Dashboard',
            'slug' => 'penulis-dashboard',
            'role' => 'ADMIN',
        ]);
    }

    public function test_it_replaces_posts_from_paginated_api_and_localizes_images_without_toc(): void
    {
        $category = Category::query()->create(['name' => 'Lama', 'slug' => 'lama', 'type' => 'BLOG']);
        $old = Post::query()->create([
            'title' => 'Artikel Bogor Lama', 'slug' => 'artikel-bogor-lama', 'content' => '<p>Lama</p>',
            'published' => true, 'categoryId' => $category->id,
        ]);
        Comment::query()->create([
            'postId' => $old->id, 'name' => 'Komentator', 'email' => 'old@example.test',
            'content' => 'Komentar lama', 'approved' => true,
        ]);
        Media::query()->create([
            'filename' => 'lama.webp', 'filepath' => 'storage/uploads/blog-import/lama.webp',
            'mimetype' => 'image/webp', 'size' => 3, 'url' => '/storage/uploads/blog-import/lama.webp',
        ]);
        Storage::disk('public')->put('uploads/blog-import/lama.webp', 'old');

        $this->fakeWordPress();

        $this->assertSame(0, Artisan::call('blog:import-pusattalilanyard'), Artisan::output());

        $this->assertDatabaseCount('post', 2);
        $this->assertDatabaseMissing('post', ['slug' => 'artikel-bogor-lama']);
        $this->assertDatabaseCount('comment', 0);
        $this->assertDatabaseCount('media', 3);
        $this->assertDatabaseHas('post', ['slug' => 'artikel-satu', 'tags' => 'lanyard custom']);
        $this->assertDatabaseMissing('post', ['authorId' => null]);

        $first = Post::query()->where('slug', 'artikel-satu')->firstOrFail();
        $this->assertSame($this->author->id, $first->authorId);
        $this->assertStringContainsString('/blog/artikel-dua#rincian', $first->content);
        $this->assertStringContainsString('/storage/uploads/blog-import/artikel-satu-content-01.png', $first->content);
        $this->assertStringContainsString('Jangan sampai terjadi… “contoh” ukuran 15–20 mm.', $first->content);
        $this->assertStringNotContainsString('Daftar Isi', $first->content);
        $this->assertStringNotContainsString('table-of-contents', $first->content);
        $this->assertSame('/storage/uploads/blog-import/artikel-satu-featured.png', $first->featuredImage);

        Storage::disk('public')->assertMissing('uploads/blog-import/lama.webp');
        Storage::disk('public')->assertExists('uploads/blog-import/artikel-satu-content-01.png');
        Storage::disk('public')->assertExists('uploads/blog-import/artikel-satu-featured.png');
        Storage::disk('public')->assertExists('uploads/blog-import/artikel-dua-featured.png');

        $this->assertSame(0, Artisan::call('blog:import-pusattalilanyard'), Artisan::output());
        $this->assertDatabaseCount('post', 2);
        $this->assertDatabaseCount('media', 3);
    }

    public function test_image_failure_preserves_existing_posts_media_and_files(): void
    {
        $old = Post::query()->create([
            'title' => 'Tetap Ada', 'slug' => 'tetap-ada', 'content' => '<p>Aman</p>', 'published' => true,
        ]);
        Storage::disk('public')->put('uploads/blog-import/tetap.webp', 'old');
        $this->fakeWordPress(failImages: true);

        $this->artisan('blog:import-pusattalilanyard')->assertFailed();

        $this->assertDatabaseHas('post', ['id' => $old->id, 'slug' => 'tetap-ada']);
        Storage::disk('public')->assertExists('uploads/blog-import/tetap.webp');
    }

    public function test_dry_run_and_limit_do_not_mutate_state(): void
    {
        Post::query()->create([
            'title' => 'Tetap Ada', 'slug' => 'tetap-ada', 'content' => '<p>Aman</p>', 'published' => true,
        ]);
        $this->fakeWordPress();

        $this->assertSame(0, Artisan::call('blog:import-pusattalilanyard', ['--dry-run' => true, '--limit' => 1]), Artisan::output());

        $this->assertDatabaseCount('post', 1);
        $this->assertDatabaseHas('post', ['slug' => 'tetap-ada']);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_import_fails_before_fetching_when_no_admin_author_exists(): void
    {
        $this->author->delete();
        Http::preventStrayRequests();

        $this->artisan('blog:import-pusattalilanyard')
            ->expectsOutput('Import dibatalkan karena belum ada akun ADMIN yang dapat dijadikan penulis blog.')
            ->assertFailed();

        $this->assertDatabaseCount('post', 0);
    }

    public function test_keep_old_retains_existing_posts_and_files(): void
    {
        Post::query()->create([
            'title' => 'Tetap Ada', 'slug' => 'tetap-ada', 'content' => '<p>Aman</p>', 'published' => true,
        ]);
        Storage::disk('public')->put('uploads/blog-import/tetap.webp', 'old');
        $this->fakeWordPress();

        $this->assertSame(0, Artisan::call('blog:import-pusattalilanyard', ['--keep-old' => true, '--limit' => 1]), Artisan::output());

        $this->assertDatabaseHas('post', ['slug' => 'tetap-ada']);
        $this->assertDatabaseHas('post', ['slug' => 'artikel-satu']);
        Storage::disk('public')->assertExists('uploads/blog-import/tetap.webp');
        Storage::disk('public')->assertExists('uploads/blog-import/artikel-satu-featured.png');
    }

    private function fakeWordPress(bool $failImages = false): void
    {
        Http::fake(function (Request $request) use ($failImages) {
            $url = $request->url();

            if (str_contains($url, '/wp-json/wp/v2/posts')) {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
                $page = (int) ($query['page'] ?? 1);
                $post = $page === 1
                    ? $this->sourcePost(1, 'artikel-satu', 101, '<div class="elementor-table-of-contents">Daftar Isi</div><h2>Isi Utama</h2><p>Teks lengkap. Jangan sampai terjadi… “contoh” ukuran 15–20 mm.</p><p><a href="https://pusattalilanyard.com/artikel-dua/#rincian">Artikel kedua</a></p><img src="https://cdn.example.test/inline.png" alt="Contoh">', [9])
                    : $this->sourcePost(2, 'artikel-dua', 102, '<h2 id="rincian">Rincian</h2><p>Artikel kedua.</p>');

                return Http::response([$post], 200, [
                    'X-WP-Total' => '2',
                    'X-WP-TotalPages' => '2',
                ]);
            }

            if (str_contains($url, '/wp-json/wp/v2/tags')) {
                return Http::response([['id' => 9, 'name' => 'lanyard custom']]);
            }

            if (preg_match('~/wp-json/wp/v2/media/(101|102)~', $url, $match)) {
                return Http::response([
                    'id' => (int) $match[1],
                    'source_url' => 'https://cdn.example.test/featured-'.$match[1].'.png',
                    'mime_type' => 'image/png',
                ]);
            }

            if (str_contains($url, 'cdn.example.test')) {
                return $failImages
                    ? Http::response('bukan gambar', 200, ['Content-Type' => 'text/plain'])
                    : Http::response($this->png, 200, ['Content-Type' => 'image/png']);
            }

            return Http::response([], 404);
        });
    }

    /** @return array<string, mixed> */
    private function sourcePost(int $id, string $slug, int $featuredMedia, string $content, array $tags = []): array
    {
        return [
            'id' => $id,
            'date' => '2026-07-20T10:00:00',
            'modified' => '2026-07-21T10:00:00',
            'slug' => $slug,
            'link' => 'https://pusattalilanyard.com/'.$slug.'/',
            'title' => ['rendered' => ucwords(str_replace('-', ' ', $slug))],
            'content' => ['rendered' => $content],
            'excerpt' => ['rendered' => '<p>Ringkasan '.$slug.'</p>'],
            'featured_media' => $featuredMedia,
            'categories' => [13],
            'tags' => $tags,
        ];
    }
}
