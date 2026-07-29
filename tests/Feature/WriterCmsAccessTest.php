<?php

namespace Tests\Feature;

use App\Models\AuthorSlugRedirect;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WriterCmsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_writer_login_redirects_to_posts(): void
    {
        $writer = User::factory()->create([
            'email' => 'writer-login@example.com',
            'password' => 'writer-password',
            'role' => User::ROLE_WRITER,
            'email_verified_at' => now(),
        ]);

        $this->post('/login', [
            'email' => $writer->email,
            'password' => 'writer-password',
        ])->assertRedirect('/kawruh/posts');
    }

    public function test_writer_only_sees_and_accesses_allowed_cms_resources(): void
    {
        $writer = User::factory()->create([
            'role' => User::ROLE_WRITER,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($writer)->get('/kawruh')
            ->assertRedirect('/kawruh/posts');

        $this->actingAs($writer)->get('/kawruh/posts')
            ->assertOk()
            ->assertSee('Pos')
            ->assertSee('Halaman')
            ->assertSee('Media Library')
            ->assertSee('Profil Saya')
            ->assertDontSee('CMS Settings')
            ->assertDontSee('Analitik')
            ->assertDontSee('/kawruh/writers', false);

        $this->actingAs($writer)->get('/kawruh/pages')->assertOk();
        $this->actingAs($writer)->get('/kawruh/media')
            ->assertOk()
            ->assertDontSee('Upload Media Baru');
        $this->actingAs($writer)->get('/kawruh/media-picker')->assertOk();

        foreach (['settings', 'products', 'categories', 'comments', 'orders', 'redirects'] as $resource) {
            $this->actingAs($writer)->get("/kawruh/{$resource}")->assertForbidden();
        }

        $this->actingAs($writer)->get('/kawruh/analytics')->assertForbidden();
        $this->actingAs($writer)->get('/kawruh/writers')->assertForbidden();
        $this->actingAs($writer)->post('/kawruh/media', [])->assertForbidden();
    }

    public function test_writer_can_manage_all_posts_and_pages_without_changing_authorship(): void
    {
        $admin = User::factory()->create([
            'slug' => 'administrator-utama',
            'role' => User::ROLE_ADMIN,
        ]);
        $writer = User::factory()->create([
            'slug' => 'writer',
            'role' => User::ROLE_WRITER,
            'email_verified_at' => now(),
        ]);
        $adminPost = Post::query()->create([
            'title' => 'Artikel Admin',
            'slug' => 'artikel-admin',
            'content' => '<p>Isi awal.</p>',
            'published' => true,
            'authorId' => $admin->id,
            'createdAt' => now(),
        ]);

        $this->actingAs($writer)->post('/kawruh/posts', [
            'title' => 'Artikel Writer',
            'slug' => 'artikel-writer',
            'content' => '<p>Isi Writer.</p>',
            'published' => '1',
            'authorId' => $admin->id,
        ])->assertRedirect('/kawruh/posts');

        $created = Post::query()->where('slug', 'artikel-writer')->firstOrFail();
        $this->assertSame($writer->id, $created->authorId);

        $this->actingAs($writer)->patch("/kawruh/posts/{$adminPost->id}", [
            'title' => 'Artikel Admin Diedit',
            'slug' => 'artikel-admin',
            'content' => '<p>Isi baru.</p>',
            'published' => '1',
            'authorId' => $writer->id,
        ])->assertRedirect('/kawruh/posts');

        $this->assertSame($admin->id, $adminPost->fresh()->authorId);

        $this->actingAs($writer)->post('/kawruh/pages', [
            'title' => 'Halaman Writer',
            'slug' => 'halaman-writer',
            'content' => '<p>Halaman.</p>',
            'published' => '1',
        ])->assertRedirect('/kawruh/pages');
        $this->assertDatabaseHas('page', ['slug' => 'halaman-writer', 'published' => true]);

        $this->actingAs($admin)->patch("/kawruh/posts/{$adminPost->id}", [
            'title' => 'Artikel Dialihkan',
            'slug' => 'artikel-admin',
            'content' => '<p>Isi.</p>',
            'published' => '1',
            'authorId' => $writer->id,
        ])->assertRedirect('/kawruh/posts');
        $this->assertSame($writer->id, $adminPost->fresh()->authorId);
    }

    public function test_admin_post_form_places_author_before_publish_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get('/kawruh/posts/create')
            ->assertOk()
            ->assertSeeInOrder([
                'for="authorId"',
                'for="published"',
            ], false);
    }

    public function test_admin_can_manage_writer_profiles_and_delete_with_reassignment(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Utama',
            'slug' => 'admin-utama',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)->post('/kawruh/writers', [
            'name' => 'Penulis Baru',
            'email' => 'writer@example.com',
            'bio' => 'Bio Writer',
            'avatar' => '/storage/uploads/writer.webp',
            'password' => 'WriterPassword123!',
            'password_confirmation' => 'WriterPassword123!',
            'role' => User::ROLE_ADMIN,
        ])->assertRedirect('/kawruh/writers');

        $writer = User::query()->where('email', 'writer@example.com')->firstOrFail();
        $this->assertTrue($writer->isWriter());
        $this->assertNotNull($writer->email_verified_at);
        $this->assertSame('penulis-baru', $writer->slug);

        $this->actingAs($admin)->patch("/kawruh/writers/{$writer->id}", [
            'name' => 'Penulis Diperbarui',
            'email' => 'writer@example.com',
            'bio' => 'Bio baru',
            'avatar' => null,
            'password' => 'WriterPassword456!',
            'password_confirmation' => 'WriterPassword456!',
            'role' => User::ROLE_ADMIN,
        ])->assertRedirect('/kawruh/writers');

        $writer->refresh();
        $this->assertTrue($writer->isWriter());
        $this->assertTrue(Hash::check('WriterPassword456!', $writer->password));
        $this->assertSame('penulis-diperbarui', $writer->slug);

        $post = Post::query()->create([
            'title' => 'Artikel Writer',
            'slug' => 'artikel-writer-hapus',
            'content' => '<p>Isi.</p>',
            'published' => true,
            'authorId' => $writer->id,
            'createdAt' => now(),
        ]);
        AuthorSlugRedirect::query()->create([
            'slug' => 'slug-writer-lama',
            'userId' => $writer->id,
        ]);

        $this->actingAs($admin)->delete("/kawruh/writers/{$writer->id}", [
            'replacement_author_id' => $admin->id,
        ])->assertRedirect('/kawruh/writers');

        $this->assertModelMissing($writer);
        $this->assertSame($admin->id, $post->fresh()->authorId);
        $this->get('/blog/penulis/penulis-diperbarui')
            ->assertStatus(301)
            ->assertRedirect('/blog/penulis/admin-utama');
        $this->get('/blog/penulis/slug-writer-lama')
            ->assertStatus(301)
            ->assertRedirect('/blog/penulis/admin-utama');
    }

    public function test_writer_api_access_matrix_is_enforced(): void
    {
        $writer = User::factory()->create([
            'role' => User::ROLE_WRITER,
            'email_verified_at' => now(),
        ]);
        Media::query()->create([
            'filename' => 'contoh.webp',
            'filepath' => 'uploads/contoh.webp',
            'mimetype' => 'image/webp',
            'size' => 100,
            'url' => '/storage/uploads/contoh.webp',
        ]);

        Sanctum::actingAs($writer);

        $this->getJson('/api/cms/posts')->assertOk();
        $this->getJson('/api/cms/pages')->assertOk();
        $this->getJson('/api/cms/media')->assertOk();
        $this->getJson('/api/cms/settings')->assertForbidden();
        $this->postJson('/api/cms/media', [])->assertForbidden();

        $this->postJson('/api/cms/posts', [
            'title' => 'API Writer',
            'slug' => 'api-writer',
            'content' => '<p>Isi.</p>',
            'published' => true,
            'authorId' => 999999,
        ])->assertUnprocessable();

        $response = $this->postJson('/api/cms/posts', [
            'title' => 'API Writer',
            'slug' => 'api-writer',
            'content' => '<p>Isi.</p>',
            'published' => true,
        ])->assertCreated();

        $this->assertSame($writer->id, $response->json('post.authorId'));
    }
}
