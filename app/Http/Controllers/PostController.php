<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request): Response
    {
        $categorySlug = $request->query('categorie');

        $posts = Post::published()
            ->byCategory($categorySlug)
            ->with(['author:id,first_name,last_name', 'category:id,name,slug,color'])
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        $featured = Post::published()
            ->with(['author:id,first_name,last_name', 'category:id,name,slug,color'])
            ->orderByDesc('published_at')
            ->first();

        $categories = Category::where('type', 'blog')
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->get(['id', 'name', 'slug', 'color']);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'featured' => $featured,
            'categories' => $categories,
            'activeCategory' => $categorySlug,
        ]);
    }

    public function show(string $slug): Response
    {
        $post = Post::published()
            ->with(['author:id,first_name,last_name,avatar', 'category:id,name,slug,color'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Incrémenter les vues
        $post->increment('views_count');

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->with(['author:id,first_name,last_name', 'category:id,name,slug,color'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'related' => $related,
        ]);
    }
}
