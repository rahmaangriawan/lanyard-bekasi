<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTaxonomySeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_taxonomy_pagination_is_not_indexable(): void
    {
        $author = User::factory()->create(['slug' => 'penulis-seo']);
        $category = Category::query()->create([
            'name' => 'Panduan',
            'slug' => 'panduan',
            'type' => 'BLOG',
        ]);
        $post = Post::query()->create([
            'title' => 'Artikel SEO',
            'slug' => 'artikel-seo',
            'content' => 'Isi artikel.',
            'published' => true,
            'categoryId' => $category->id,
            'authorId' => $author->id,
        ]);
        $post->forceFill(['tags' => 'SEO'])->save();

        $this->get('/kategori/panduan?page=2')->assertOk()->assertSee('noindex, follow');
        $this->get('/tag/seo?page=2')->assertOk()->assertSee('noindex, follow');
        $this->get('/blog/penulis/penulis-seo?page=2')->assertOk()->assertSee('noindex, follow');
    }
}
