<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogShowDesignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'app.url' => 'https://lanyardbekasi.com',
            'cache.public_store' => 'array',
        ]);
    }

    public function test_single_post_renders_editorial_structure_and_markdown_safely(): void
    {
        [$post, $author, $category] = $this->createPost();

        foreach ([
            [
                'title' => 'Artikel Terkait Satu',
                'slug' => 'artikel-terkait-satu',
                'content' => '<p>Ringkasan artikel terkait.</p>',
                'metaDescription' => 'Ringkasan artikel terkait.',
                'published' => true,
                'featuredImage' => '/uploads/testimoni-lanyard-bekasi.webp',
                'categoryId' => $category->id,
                'authorId' => $author->id,
                'createdAt' => now()->subHours(2),
                'updatedAt' => now()->subHours(2),
            ],
            [
                'title' => 'Artikel Terkait Dua',
                'slug' => 'artikel-terkait-dua',
                'content' => '<p>Ringkasan artikel terkait.</p>',
                'metaDescription' => 'Ringkasan artikel terkait.',
                'published' => true,
                'featuredImage' => null,
                'categoryId' => $category->id,
                'authorId' => $author->id,
                'createdAt' => now()->subHours(3),
                'updatedAt' => now()->subHours(3),
            ],
        ] as $relatedData) {
            Post::query()->create($relatedData);
        }

        $otherCategory = Category::query()->create([
            'name' => 'Bisnis',
            'slug' => 'bisnis',
            'type' => 'BLOG',
        ]);
        Post::query()->create([
            'title' => 'Artikel Kategori Lain',
            'slug' => 'artikel-kategori-lain',
            'content' => '<p>Tidak tampil di rekomendasi kategori Panduan.</p>',
            'metaDescription' => 'Artikel kategori lain.',
            'published' => true,
            'featuredImage' => null,
            'categoryId' => $otherCategory->id,
            'authorId' => $author->id,
            'createdAt' => now()->subHours(4),
            'updatedAt' => now()->subHours(4),
        ]);

        $response = $this->get('/blog/'.$post->slug)->assertOk();
        $html = $response->getContent();
        $xpath = $this->xpath($html);

        $this->assertSame(1, $xpath->query('//article[contains(@class, "article-page")]//h1')->length);
        $this->assertSame(1, $xpath->query('//figure[contains(@class, "article-hero-media")]//img[@width="720"][@height="450"]')->length);
        $this->assertSame(1, $xpath->query('//aside[contains(@class, "article-sidebar")]')->length);
        $this->assertSame(1, $xpath->query('//details[contains(@class, "article-toc")]')->length);
        $this->assertSame(0, $xpath->query('//details[contains(@class, "article-toc")][@open]')->length);
        $this->assertSame(2, $xpath->query('//details[contains(@class, "article-toc")]//a[starts-with(@href, "#")]')->length);
        $this->assertSame(4, $xpath->query('//nav[contains(@class, "article-share--sidebar")]//a[contains(@class, "share-button")]')->length);
        $this->assertSame(1, $xpath->query('//section[contains(@class, "article-related-sidebar")]')->length);
        $this->assertSame(2, $xpath->query('//section[contains(@class, "article-related-sidebar")]//article[contains(@class, "blog-archive-card--sidebar")]')->length);
        $this->assertSame(2, $xpath->query('//article[contains(@class, "blog-archive-card--compact")]')->length);

        $response
            ->assertSee('/css/blog-show.css', false)
            ->assertSee('/css/blog-archive.css', false)
            ->assertSee('share-threads.svg', false)
            ->assertSee('https://www.threads.net/intent/post', false)
            ->assertSee('aria-label="Bagikan ke Threads"', false)
            ->assertSee('<strong>bold markdown</strong>', false)
            ->assertSee('/blog/penulis/'.$author->slug, false)
            ->assertDontSee('Artikel Kategori Lain', false)
            ->assertDontSee('javascript:', false)
            ->assertDontSee('Ã¢', false);

        $schemas = $this->jsonLdFrom($html);
        $this->assertIsArray(collect($schemas)->firstWhere('@type', 'BlogPosting'));
        $this->assertIsArray(collect($schemas)->firstWhere('@type', 'BreadcrumbList'));
    }

    private function createPost(): array
    {
        $author = User::factory()->create([
            'name' => 'Penulis Lanyard',
            'slug' => 'penulis-lanyard',
            'bio' => 'Menulis panduan praktis seputar lanyard custom.',
            'role' => User::ROLE_ADMIN,
        ]);
        $category = Category::query()->create([
            'name' => 'Panduan',
            'slug' => 'panduan',
            'type' => 'BLOG',
        ]);
        $post = Post::query()->create([
            'title' => 'Panduan Safety Breakaway',
            'slug' => 'panduan-safety-breakaway',
            'content' => <<<'HTML'
                <p>Konten **bold markdown** dengan <a href="javascript:alert(1)">tautan tidak aman</a>.</p>
                <h2>Bagian Penting</h2>
                <p>Isi artikel yang sudah disanitasi.</p>
                <h2>Bagian Berikutnya</h2>
                <p>Isi tambahan untuk daftar isi.</p>
                HTML,
            'metaDescription' => 'Panduan safety breakaway untuk kebutuhan lanyard custom.',
            'published' => true,
            'featuredImage' => '/uploads/testimoni-lanyard-bekasi.webp',
            'categoryId' => $category->id,
            'authorId' => $author->id,
            'tags' => 'Safety, Lanyard',
            'createdAt' => now()->subDay(),
            'updatedAt' => now(),
        ]);

        return [$post, $author, $category];
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        @$document->loadHTML($html);

        return new DOMXPath($document);
    }

    private function jsonLdFrom(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

        return collect($matches[1] ?? [])
            ->map(fn (string $json): mixed => json_decode($json, true, flags: JSON_THROW_ON_ERROR))
            ->filter(fn (mixed $schema): bool => is_array($schema))
            ->values()
            ->all();
    }
}
