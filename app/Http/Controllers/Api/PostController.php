<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index()
    {
        return ['success' => true, 'posts' => Post::with(['category:id,name,slug', 'author:id,name,slug'])->latest('updatedAt')->paginate(50)];
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if (! $request->user()->isAdmin() || empty($data['authorId'])) {
            $data['authorId'] = $request->user()->id;
        }
        $post = Post::create($data);

        return response()->json(['success' => true, 'post' => $post], 201);
    }

    public function show(Post $post)
    {
        return ['success' => true, 'post' => $post->load('category:id,name,slug')];
    }

    public function update(Request $request, Post $post)
    {
        $post->update($this->validated($request, $post->id));

        return ['success' => true, 'post' => $post->refresh()];
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return ['success' => true];
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:post,slug'.($ignoreId ? ','.$ignoreId : '')],
            'content' => ['required', 'string'],
            'published' => ['nullable', 'boolean'],
            'featuredImage' => ['nullable', 'string', 'max:255'],
            'categoryId' => ['nullable', 'integer', 'exists:category,id'],
            'createdAt' => ['nullable', 'date'],
            'metaTitle' => ['nullable', 'string', 'max:255'],
            'metaDescription' => ['nullable', 'string'],
            'authorId' => $request->user()?->isAdmin()
                ? [
                    'nullable',
                    'integer',
                    Rule::exists('user', 'id')->where(fn ($query) => $query->whereIn('role', [User::ROLE_ADMIN, User::ROLE_WRITER])),
                ]
                : ['prohibited'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['published'] = (bool) ($data['published'] ?? false);
        $data['content'] = HtmlSanitizer::clean($data['content']);

        return $data;
    }
}
