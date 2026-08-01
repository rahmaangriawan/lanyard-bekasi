<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogArchivePageTest extends TestCase
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

    public function test_blog_index_renders_minimal_archive_with_search_pagination_and_schema(): void
    {
        [$author, $category] = $this->createArchiveData(11);

        $response = $this->get('/blog')->assertOk();
        $html = $response->getContent();
        $xpath = $this->xpath($html);

        $this->assertSame(1, $xpath->query('//h1')->length);
        $this->assertSame(9, $xpath->query('//ol[contains(@class, "blog-archive-list")]/li')->length);
        $this->assertSame(9, $xpath->query('//article[contains(@class, "blog-archive-card")]')->length);

        $response
            ->assertSee('Panduan Praktis Seputar Lanyard Custom')
            ->assertSee('name="q"', false)
            ->assertSee('/css/blog-archive.css', false)
            ->assertSee('Butuh Lanyard yang Sesuai Kebutuhan Anda?')
            ->assertSee('page=2', false)
            ->assertDontSee('â', false);

        $schemas = $this->jsonLdFrom($html);
        $collection = collect($schemas)->firstWhere('@type', 'CollectionPage');
        $breadcrumb = collect($schemas)->firstWhere('@type', 'BreadcrumbList');

        $this->assertIsArray($collection);
        $this->assertSame('https://lanyardbekasi.com/blog', $collection['url']);
        $this->assertCount(9, $collection['mainEntity']['itemListElement']);
        $this->assertIsArray($breadcrumb);
        $this->assertSame('Blog', $breadcrumb['itemListElement'][1]['name']);
        $this->assertSame($author->name, Post::query()->first()->author->name);
        $this->assertSame($category->name, Post::query()->first()->category->name);
    }

    public function test_category_tag_and_author_use_the_shared_archive_system(): void
    {
        [$author, $category] = $this->createArchiveData(2);

        $pages = [
            '/kategori/'.$category->slug => 'Artikel dalam Kategori Ini',
            '/tag/bahan' => 'Artikel dengan Tag Ini',
            '/blog/penulis/'.$author->slug => 'Tulisan dari '.$author->name,
        ];

        foreach ($pages as $url => $heading) {
            $response = $this->get($url)->assertOk();
            $html = $response->getContent();
            $xpath = $this->xpath($html);

            $this->assertSame(1, $xpath->query('//h1')->length, $url);
            $this->assertSame(2, $xpath->query('//article[contains(@class, "blog-archive-card")]')->length, $url);
            $response
                ->assertSee($heading)
                ->assertSee('/css/blog-archive.css', false)
                ->assertSee('Butuh Lanyard yang Sesuai Kebutuhan Anda?')
                ->assertDontSee('â', false);
        }

        $this->get('/kategori/'.$category->slug)
            ->assertSee('<link rel="canonical" href="https://lanyardbekasi.com/kategori/'.$category->slug.'">', false)
            ->assertSee('CollectionPage', false)
            ->assertSee('BreadcrumbList', false);

        $this->get('/tag/bahan')
            ->assertSee('<link rel="canonical" href="https://lanyardbekasi.com/tag/bahan">', false)
            ->assertSee('CollectionPage', false)
            ->assertSee('BreadcrumbList', false);

        $this->get('/blog/penulis/'.$author->slug)
            ->assertSee('<link rel="canonical" href="https://lanyardbekasi.com/blog/penulis/'.$author->slug.'">', false)
            ->assertSee('ProfilePage', false)
            ->assertSee('BreadcrumbList', false);
    }

    public function test_empty_archive_has_a_clear_recovery_action(): void
    {
        $category = Category::query()->create([
            'name' => 'Belum Terisi',
            'slug' => 'belum-terisi',
            'type' => 'BLOG',
        ]);

        $response = $this->get('/kategori/'.$category->slug)->assertOk();
        $html = $response->getContent();

        $response
            ->assertSee('Belum ada artikel')
            ->assertSee('Lihat Semua Artikel')
            ->assertSee(route('blog.index'), false);

        $this->assertSame(1, $this->xpath($html)->query('//h1')->length);
        $this->assertSame(0, $this->xpath($html)->query('//article[contains(@class, "blog-archive-card")]')->length);
    }

    private function createArchiveData(int $count): array
    {
        $author = User::factory()->create([
            'name' => 'Penulis Bekasi',
            'slug' => 'penulis-bekasi',
            'bio' => 'Menulis panduan praktis seputar lanyard custom.',
            'role' => User::ROLE_ADMIN,
        ]);
        $category = Category::query()->create([
            'name' => 'Panduan',
            'slug' => 'panduan',
            'type' => 'BLOG',
        ]);

        foreach (range(1, $count) as $index) {
            Post::query()->create([
                'title' => 'Panduan Lanyard '.$index,
                'slug' => 'panduan-lanyard-'.$index,
                'content' => '<p>Isi panduan lanyard custom untuk kebutuhan pelanggan.</p>',
                'metaDescription' => 'Ringkasan artikel panduan lanyard '.$index.'.',
                'published' => true,
                'featuredImage' => '/uploads/testimoni-lanyard-bekasi.webp',
                'categoryId' => $category->id,
                'authorId' => $author->id,
                'tags' => 'Bahan, Produksi',
                'createdAt' => now()->subDays($index),
                'updatedAt' => now()->subDays($index),
            ]);
        }

        return [$author, $category];
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument();
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
