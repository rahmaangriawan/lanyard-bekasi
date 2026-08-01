<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSeoMetadataTest extends TestCase
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

    public function test_article_json_ld_contains_visible_article_and_faq_data(): void
    {
        $post = $this->createPost([
            'content' => <<<'HTML'
                <p>Panduan memilih hook lanyard yang sesuai kebutuhan.</p>
                <h2>Apakah hook jempol mudah digunakan?</h2>
                <p>Ya, hook jempol dapat dibuka menggunakan ibu jari.</p>
                <h2>Pertanyaan tanpa jawaban?</h2>
                HTML,
        ]);

        $response = $this->get('/blog/'.$post->slug)->assertOk();
        $schemas = $this->jsonLdFrom($response->getContent());

        $article = collect($schemas)->firstWhere('@type', 'BlogPosting');
        $faq = collect($schemas)->firstWhere('@type', 'FAQPage');

        $this->assertIsArray($article);
        $this->assertSame($post->title, $article['headline']);
        $this->assertSame('https://lanyardbekasi.com/blog/'.$post->slug, $article['mainEntityOfPage']['@id']);
        $this->assertSame('Rama SEO', $article['author']['name']);
        $this->assertSame('https://lanyardbekasi.com/blog/penulis/rama-seo', $article['author']['url']);
        $this->assertSame('Tips Lanyard', $article['articleSection']);
        $this->assertNotEmpty($article['datePublished']);
        $this->assertNotEmpty($article['dateModified']);

        $this->assertIsArray($faq);
        $this->assertCount(1, $faq['mainEntity']);
        $this->assertSame('Apakah hook jempol mudah digunakan?', $faq['mainEntity'][0]['name']);
        $this->assertSame(
            'Ya, hook jempol dapat dibuka menggunakan ibu jari.',
            $faq['mainEntity'][0]['acceptedAnswer']['text'],
        );
    }

    public function test_dynamic_open_graph_image_is_a_cacheable_1200_by_630_png(): void
    {
        if (! function_exists('imagecreatetruecolor') || ! function_exists('imagettftext')) {
            $this->markTestSkipped('Ekstensi GD dengan dukungan FreeType diperlukan.');
        }

        $post = $this->createPost(['featuredImage' => null]);

        $response = $this->get('/og/blog/'.$post->slug.'.png')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $image = getimagesizefromstring($response->getContent());
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age=86400', $cacheControl);
        $this->assertStringContainsString('stale-while-revalidate=604800', $cacheControl);
        $this->assertIsArray($image);
        $this->assertSame(1200, $image[0]);
        $this->assertSame(630, $image[1]);
        $this->assertSame('image/png', $image['mime']);
    }

    private function createPost(array $overrides = []): Post
    {
        $author = User::factory()->create([
            'name' => 'Rama SEO',
            'slug' => 'rama-seo',
            'bio' => 'Penulis dan spesialis produk lanyard.',
            'role' => 'ADMIN',
        ]);
        $category = Category::query()->create([
            'name' => 'Tips Lanyard',
            'slug' => 'tips-lanyard',
            'type' => 'BLOG',
        ]);

        return Post::query()->create(array_merge([
            'title' => 'Panduan Hook Jempol Lanyard',
            'slug' => 'panduan-hook-jempol-lanyard',
            'content' => '<p>Isi artikel SEO.</p>',
            'metaDescription' => 'Panduan lengkap memilih hook jempol untuk kebutuhan lanyard.',
            'published' => true,
            'featuredImage' => null,
            'categoryId' => $category->id,
            'authorId' => $author->id,
            'createdAt' => now()->subDay(),
            'updatedAt' => now(),
        ], $overrides));
    }

    private function jsonLdFrom(string $html): array
    {
        preg_match_all(
            '#<script type="application/ld\+json">(.*?)</script>#s',
            $html,
            $matches,
        );

        return collect($matches[1] ?? [])
            ->map(fn (string $json): mixed => json_decode($json, true, flags: JSON_THROW_ON_ERROR))
            ->filter(fn (mixed $schema): bool => is_array($schema))
            ->values()
            ->all();
    }
}
