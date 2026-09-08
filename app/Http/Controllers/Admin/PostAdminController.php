<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostAdminController extends Controller
{
    /**
     * Liste paginée des articles.
     */
    public function index(): Response
    {
        $posts = Post::with(['author', 'category'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create(): Response
    {
        $categories = Category::where('type', 'blog')->orderBy('name')->get();

        return Inertia::render('Admin/Posts/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Enregistre un nouvel article.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'cover_image' => 'nullable|string|max:255',
            'reading_time' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
        ]);

        $validated['slug']    = $this->uniqueSlug(Str::slug($validated['title']));
        $validated['user_id'] = $request->user()->id;

        Post::create($validated);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Article créé avec succès.');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Post $post): Response
    {
        $categories = Category::where('type', 'blog')->orderBy('name')->get();

        return Inertia::render('Admin/Posts/Edit', [
            'post' => $post->load(['author', 'category']),
            'categories' => $categories,
        ]);
    }

    /**
     * Met à jour un article.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'cover_image' => 'nullable|string|max:255',
            'reading_time' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
        ]);

        $post->update($validated);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Article mis à jour.');
    }

    /**
     * Supprime un article.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Article supprimé.');
    }

    private function uniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $original = $slug;
        $i = 1;
        while (Post::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }
        return $slug;
    }
}
