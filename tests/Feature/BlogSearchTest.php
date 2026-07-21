<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_search_treats_sql_wildcards_as_literal_characters(): void
    {
        $author = User::factory()->create();

        foreach ([
            ['title' => 'Promo_2026 Resmi', 'slug' => 'promo-2026-resmi'],
            ['title' => 'Promoa2026 Berbeda', 'slug' => 'promoa2026-berbeda'],
        ] as $post) {
            Post::query()->create([
                ...$post,
                'content' => 'Konten artikel',
                'published' => true,
                'authorId' => $author->id,
            ]);
        }

        $this->get('/blog?q=promo_2026')
            ->assertOk()
            ->assertSee('noindex, follow')
            ->assertSee('Promo_2026 Resmi')
            ->assertDontSee('Promoa2026 Berbeda');
    }

    public function test_paginated_blog_page_is_not_indexable(): void
    {
        $this->get('/blog?page=2')->assertOk()->assertSee('noindex, follow');
    }
}
