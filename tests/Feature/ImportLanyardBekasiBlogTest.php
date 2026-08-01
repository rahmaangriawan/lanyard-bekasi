<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportLanyardBekasiBlogTest extends TestCase
{
    use RefreshDatabase;

    private string $png;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        User::factory()->create(['role' => 'ADMIN', 'name' => 'Lanyard Bekasi']);
    }

    public function test_dry_run_discovers_paginated_articles_without_mutating_posts_or_files(): void
    {
        Post::query()->create([
            'title' => 'Artikel lama',
            'slug' => 'artikel-lama',
            'content' => '<p>Lama</p>',
            'published' => true,
        ]);
        $this->fakeLiveBlog();

        $this->assertSame(0, Artisan::call('blog:import-lanyardbekasi', ['--dry-run' => true]), Artisan::output());
        $this->assertDatabaseHas('post', ['slug' => 'artikel-lama']);
        $this->assertDatabaseCount('post', 1);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_replace_removes_old_blog_content_and_only_prunes_article_media(): void
    {
        $category = Category::query()->create(['name' => 'Lama', 'slug' => 'lama', 'type' => 'BLOG']);
        $old = Post::query()->create([
            'title' => 'Artikel lama',
            'slug' => 'artikel-lama',
            'content' => '<p><img src="/storage/uploads/blog-import/lama-featured.png"></p>',
            'featuredImage' => '/storage/uploads/blog-import/lama-featured.png',
            'published' => true,
            'categoryId' => $category->id,
        ]);
        Comment::query()->create([
            'postId' => $old->id,
            'name' => 'Komentator',
            'email' => 'comment@example.test',
            'content' => 'Lama',
            'approved' => true,
        ]);
        Media::query()->create([
            'filename' => 'lama-featured.png',
            'filepath' => 'storage/uploads/blog-import/lama-featured.png',
            'mimetype' => 'image/png',
            'size' => strlen($this->png),
            'url' => '/storage/uploads/blog-import/lama-featured.png',
        ]);
        Storage::disk('public')->put('uploads/blog-import/lama-featured.png', $this->png);
        Storage::disk('public')->put('uploads/shared-theme.png', $this->png);

        $this->fakeLiveBlog();

        $this->assertSame(0, Artisan::call('blog:import-lanyardbekasi'), Artisan::output());
        $this->assertDatabaseMissing('post', ['slug' => 'artikel-lama']);
        $this->assertDatabaseHas('post', ['slug' => 'artikel-live-satu', 'categoryId' => Category::query()->where('slug', 'panduan-lanyard')->value('id')]);
        $this->assertDatabaseHas('post', ['slug' => 'artikel-live-dua']);
        $this->assertDatabaseCount('post', 2);
        $this->assertDatabaseCount('comment', 0);
        Storage::disk('public')->assertMissing('uploads/blog-import/lama-featured.png');
        Storage::disk('public')->assertExists('uploads/blog-import/artikel-live-satu-featured.png');
        Storage::disk('public')->assertExists('uploads/shared-theme.png');
        $content = (string) Post::query()->where('slug', 'artikel-live-satu')->value('content');
        $this->assertStringContainsString('/storage/uploads/blog-import/artikel-live-satu-inline-a40156dede.png', $content);
        $this->assertStringContainsString('<h2>Subheading</h2>', $content);
        $this->assertStringContainsString('<h3>Question</h3>', $content);
        $this->assertStringNotContainsString('<script>', $content);
    }

    private function fakeLiveBlog(): void
    {
        Http::fake(function (Request $request) {
            $url = $request->url();
            if (str_contains($url, '/blog?page=1')) {
                return Http::response($this->archiveHtml([
                    ['slug' => 'artikel-live-satu', 'title' => 'Artikel Live Satu', 'category' => 'Panduan Lanyard', 'categorySlug' => 'panduan-lanyard', 'image' => '/media/1'],
                ]), 200, ['Content-Type' => 'text/html']);
            }
            if (str_contains($url, '/blog?page=2')) {
                return Http::response($this->archiveHtml([
                    ['slug' => 'artikel-live-dua', 'title' => 'Artikel Live Dua', 'category' => '', 'categorySlug' => '', 'image' => '/media/2'],
                ]), 200, ['Content-Type' => 'text/html']);
            }
            if (str_contains($url, '/artikel-live-satu')) {
                return Http::response($this->articleHtml('Artikel Live Satu', '/media/1', '<h1>Subheading</h1><h2>Question</h2><p>Isi <strong>tebal</strong>.</p><script>alert(1)</script><p><img src="/storage/101/inline.png" alt="Inline"></p>'), 200, ['Content-Type' => 'text/html']);
            }
            if (str_contains($url, '/artikel-live-dua')) {
                return Http::response($this->articleHtml('Artikel Live Dua', '/media/2', '<h2>Rincian</h2><p>Isi kedua.</p>'), 200, ['Content-Type' => 'text/html']);
            }
            if (preg_match('~/media/[12]$~', $url)) {
                return Http::response($this->png, 200, ['Content-Type' => 'image/png']);
            }
            if (str_contains($url, '/storage/101/inline.png')) {
                return Http::response($this->png, 200, ['Content-Type' => 'image/png']);
            }

            return Http::response([], 404);
        });
    }

    private function archiveHtml(array $articles): string
    {
        $cards = collect($articles)->map(function (array $article): string {
            $category = $article['category'] !== ''
                ? '<a href="/category/'.$article['categorySlug'].'">'.$article['category'].'</a>'
                : '';
            return '<article><a href="/'.$article['slug'].'"><h2>'.$article['title'].'</h2><img src="'.$article['image'].'"></a><time datetime="2026-05-21T08:00:00+00:00">21 Mei 2026</time>'.$category.'</article>';
        })->implode('');

        return '<main>'.$cards.'<a href="/blog?page=2">2</a></main>';
    }

    private function articleHtml(string $title, string $featured, string $content): string
    {
        return '<main><article><header><h1>'.$title.'</h1><time datetime="2026-05-21T08:00:00+00:00">21 Mei 2026</time></header><figure><img src="'.$featured.'"></figure><div class="prose">'.$content.'</div></article></main>';
    }
}
