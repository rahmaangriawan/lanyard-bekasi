<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\AuthorSlugRedirect;
use App\Models\Post;
use App\Models\User;
use App\Support\AuthorProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class WriterController extends Controller
{
    public function index(): View
    {
        return view('cms.writers.index', [
            'writers' => User::query()
                ->where('role', User::ROLE_WRITER)
                ->withCount('posts')
                ->orderBy('name')
                ->paginate(20),
            'replacementAuthors' => $this->authors(),
        ]);
    }

    public function create(): View
    {
        return view('cms.writers.form', [
            'writer' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data): void {
            $writer = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'bio' => $data['bio'] ?? null,
                'avatar' => $data['avatar'] ?? null,
                'password' => $data['password'],
                'role' => User::ROLE_WRITER,
                'email_verified_at' => now(),
            ]);
            $writer->slug = AuthorProfile::nextSlug($writer, $writer->name);
            $writer->save();
        });

        return redirect()->route('cms.writers.index')->with('success', 'Profil Writer berhasil ditambahkan.');
    }

    public function edit(User $writer): View
    {
        $this->assertWriter($writer);

        return view('cms.writers.form', compact('writer'));
    }

    public function update(Request $request, User $writer): RedirectResponse
    {
        $this->assertWriter($writer);
        $data = $this->validated($request, $writer);

        DB::transaction(function () use ($writer, $data): void {
            $previousSlug = $writer->slug;
            $writer->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'bio' => $data['bio'] ?? null,
                'avatar' => $data['avatar'] ?? null,
                'role' => User::ROLE_WRITER,
                'email_verified_at' => $writer->email_verified_at ?? now(),
            ]);

            if (! empty($data['password'])) {
                $writer->password = $data['password'];
                $writer->remember_token = null;
            }

            $nextSlug = AuthorProfile::nextSlug($writer, $writer->name);
            $writer->slug = $nextSlug;
            $writer->save();

            AuthorSlugRedirect::query()
                ->where('userId', $writer->id)
                ->where('slug', $nextSlug)
                ->delete();

            if ($previousSlug && $previousSlug !== $nextSlug) {
                AuthorSlugRedirect::updateOrCreate(
                    ['slug' => $previousSlug],
                    ['userId' => $writer->id],
                );
            }
        });

        return redirect()->route('cms.writers.index')->with('success', 'Profil Writer berhasil diperbarui.');
    }

    public function destroy(Request $request, User $writer): RedirectResponse
    {
        $this->assertWriter($writer);
        $data = $request->validate([
            'replacement_author_id' => ['required', 'integer', Rule::exists('user', 'id')],
        ]);

        $replacement = User::query()
            ->whereKey($data['replacement_author_id'])
            ->whereKeyNot($writer->id)
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_WRITER])
            ->firstOrFail();

        DB::transaction(function () use ($writer, $replacement): void {
            Post::query()->where('authorId', $writer->id)->update(['authorId' => $replacement->id]);
            AuthorSlugRedirect::query()
                ->where('userId', $writer->id)
                ->update(['userId' => $replacement->id]);

            if ($writer->slug && $writer->slug !== $replacement->slug) {
                AuthorSlugRedirect::updateOrCreate(
                    ['slug' => $writer->slug],
                    ['userId' => $replacement->id],
                );
            }

            $writer->delete();
        });

        return redirect()->route('cms.writers.index')->with('success', 'Writer dihapus dan artikelnya sudah dialihkan.');
    }

    private function validated(Request $request, ?User $writer = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($writer?->id),
            ],
            'bio' => ['nullable', 'string', 'max:600'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'password' => [
                $writer ? 'nullable' : 'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);
    }

    private function assertWriter(User $writer): void
    {
        abort_unless($writer->isWriter(), 404);
    }

    private function authors()
    {
        return User::query()
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_WRITER])
            ->orderByRaw("CASE WHEN role = ? THEN 0 ELSE 1 END", [User::ROLE_ADMIN])
            ->orderBy('name')
            ->get(['id', 'name', 'role']);
    }
}
