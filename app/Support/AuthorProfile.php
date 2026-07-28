<?php

namespace App\Support;

use App\Models\AuthorSlugRedirect;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

final class AuthorProfile
{
    public const DEFAULT_BIO = 'Tim Pusat Tali Lanyard menulis panduan seputar lanyard custom, kebutuhan event, dan branding perusahaan.';

    public static function primaryAuthor(): ?User
    {
        return User::query()
            ->where('role', 'ADMIN')
            ->orderBy('id')
            ->first();
    }

    public static function assignUnownedPosts(User $author): int
    {
        return Post::query()
            ->whereNull('authorId')
            ->update(['authorId' => $author->id]);
    }

    public static function nextSlug(User $user, string $name): string
    {
        $base = Str::slug($name) ?: "penulis-{$user->id}";
        $slug = $base;
        $suffix = 2;

        while (self::slugIsTaken($slug, $user)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public static function slugIsTaken(string $slug, User $user): bool
    {
        return User::query()
            ->where('slug', $slug)
            ->whereKeyNot($user->getKey())
            ->exists()
            || AuthorSlugRedirect::query()
                ->where('slug', $slug)
                ->where('userId', '!=', $user->getKey())
                ->exists();
    }
}
