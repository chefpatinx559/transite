<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Post::with([
            'author:id,first_name,last_name',
            'category:id,name,slug,color',
        ])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where('title', 'like', "%{$request->q}%");
        }

        return response()->json($query->paginate(15));
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json($post->load([
            'author:id,first_name,last_name',
            'category:id,name,slug,color',
        ]));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category_id'  => 'nullable|exists:categories,id',
            'excerpt'      => 'nullable|string',
            'content'      => 'required|string',
            'reading_time' => 'nullable|integer|min:1',
            'status'       => 'nullable|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'cover_image'  => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        } else {
            unset($validated['cover_image']);
        }

        $validated['slug']    = $this->uniqueSlug(Str::slug($validated['title']));
        $validated['user_id'] = $request->user()->id;
        $validated['status']  = $validated['status'] ?? 'draft';

        $post = Post::create($validated);

        return response()->json($post->load([
            'author:id,first_name,last_name',
            'category:id,name,slug,color',
        ]), 201);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category_id'  => 'nullable|exists:categories,id',
            'excerpt'      => 'nullable|string',
            'content'      => 'required|string',
            'reading_time' => 'nullable|integer|min:1',
            'status'       => 'nullable|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'cover_image'  => 'nullable|image|max:3072',
            'remove_cover' => 'nullable|string',
        ]);

        if ($request->hasFile('cover_image')) {
            // Supprimer l'ancienne image
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        } elseif ($request->input('remove_cover') === '1') {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $validated['cover_image'] = null;
        } else {
            unset($validated['cover_image']);
        }

        unset($validated['remove_cover']);

        if ($validated['title'] !== $post->title) {
            $validated['slug'] = $this->uniqueSlug(Str::slug($validated['title']), $post->id);
        }

        $post->update($validated);

        return response()->json($post->load([
            'author:id,first_name,last_name',
            'category:id,name,slug,color',
        ]));
    }

    public function destroy(Post $post): JsonResponse
    {
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }
        $post->delete();

        return response()->json(null, 204);
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
