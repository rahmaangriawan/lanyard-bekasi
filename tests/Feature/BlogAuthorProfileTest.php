<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BlogAuthorProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_views_and_schema_use_the_dashboard_profile_verbatim(): void
    {
        $author = User::factory()->create([
            'name' => 'Admin',
            'slug' => 'admin',
            'bio' => 'Bio yang diatur dari dashboard.',
            'avatar' => '/storage/uploads/profil-admin.webp',
            'role' => 'ADMIN',
        ]);
        $post = Post::query()->create([
            'title' => 'Artikel Profil Dinamis',
            'slug' => 'artikel-profil-dinamis',
            'content' => '<p>Isi artikel.</p>',
            'published' => true,
            'authorId' => $author->id,
            'createdAt' => now(),
        ]);

        $this->get('/blog/'.$post->slug)
            ->assertOk()
            ->assertSee('Admin')
            ->assertSee('Bio yang diatur dari dashboard.')
            ->assertSee('/blog/penulis/admin', false)
            ->assertSee('/storage/uploads/profil-admin.webp', false)
            ->assertDontSee('Rama Angriawan')
            ->assertDontSee('Tim Lanyard Bekasi');

        $this->get('/blog/penulis/admin')
            ->assertOk()
            ->assertSee('Admin')
            ->assertSee('Bio yang diatur dari dashboard.')
            ->assertSee('/storage/uploads/profil-admin.webp', false)
            ->assertDontSee('Rama Angriawan');
    }

    public function test_profile_update_changes_public_author_data_and_redirects_the_old_slug(): void
    {
        $author = User::factory()->create([
            'name' => 'Nama Lama',
            'slug' => 'nama-lama',
            'bio' => 'Bio lama.',
            'role' => 'ADMIN',
        ]);
        $post = Post::query()->create([
            'title' => 'Artikel Penulis',
            'slug' => 'artikel-penulis',
            'content' => '<p>Isi artikel.</p>',
            'published' => true,
            'authorId' => $author->id,
            'createdAt' => now(),
        ]);

        $this->actingAs($author)->patch('/profile', [
            'name' => 'Nama Baru',
            'email' => $author->email,
            'bio' => 'Bio baru dari dashboard.',
            'avatar' => '/storage/uploads/avatar-baru.webp',
        ])->assertSessionHasNoErrors();

        $this->get('/blog/'.$post->slug)
            ->assertOk()
            ->assertSee('Nama Baru')
            ->assertSee('Bio baru dari dashboard.')
            ->assertSee('/blog/penulis/nama-baru', false)
            ->assertSee('/storage/uploads/avatar-baru.webp', false);

        $this->get('/blog/penulis/nama-lama')
            ->assertRedirect('/blog/penulis/nama-baru')
            ->assertStatus(301);
    }

    public function test_administrator_alias_redirects_to_the_primary_admin_active_slug(): void
    {
        $author = User::factory()->create([
            'name' => 'Admin Utama',
            'slug' => 'admin-utama',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->get('/blog/penulis/administrator')
            ->assertStatus(301)
            ->assertRedirect('/blog/penulis/admin-utama');

        $this->actingAs($author)->patch('/profile', [
            'name' => 'Nama Admin Baru',
            'email' => $author->email,
            'bio' => null,
            'avatar' => null,
        ])->assertSessionHasNoErrors();

        $this->get('/blog/penulis/administrator')
            ->assertStatus(301)
            ->assertRedirect('/blog/penulis/nama-admin-baru');
    }

    public function test_unowned_posts_can_be_backfilled_to_the_primary_admin(): void
    {
        $author = User::factory()->create(['role' => 'ADMIN']);
        $post = Post::query()->create([
            'title' => 'Artikel Lama',
            'slug' => 'artikel-lama',
            'content' => '<p>Isi.</p>',
            'published' => true,
        ]);

        $this->assertSame(1, \App\Support\AuthorProfile::assignUnownedPosts($author));
        $this->assertSame($author->id, $post->fresh()->authorId);
    }

    public function test_initial_import_backfills_existing_content_when_sql_import_is_skipped(): void
    {
        $author = User::factory()->create(['role' => 'ADMIN']);
        foreach (range(1, 6) as $index) {
            Post::query()->create([
                'title' => 'Artikel '.$index,
                'slug' => 'artikel-'.$index,
                'content' => '<p>Isi.</p>',
                'published' => true,
            ]);
        }

        $exitCode = Artisan::call('content:import-initial');
        $output = Artisan::output();
        $this->assertSame(0, $exitCode, $output);
        $this->assertSame(6, Post::query()->where('authorId', $author->id)->count());
        $this->assertStringContainsString('import skipped', $output);
    }
}
