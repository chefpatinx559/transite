<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\FormationRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormationApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Formation::withCount('registrations')->orderByDesc('created_at');

        if ($request->filled('q')) {
            $query->where('title', 'like', "%{$request->q}%");
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return response()->json($query->paginate(15));
    }

    public function show(Formation $formation): JsonResponse
    {
        return response()->json($formation->loadCount('registrations'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'description'      => 'nullable|string',
            'type'             => 'required|in:online,presentielle',
            'is_free'          => 'required|in:0,1',
            'price'            => 'required_if:is_free,0|numeric|min:0',
            'duration'         => 'nullable|string|max:100',
            'instructor_name'  => 'nullable|string|max:150',
            'date_start'       => 'nullable|date',
            'date_end'         => 'nullable|date|after_or_equal:date_start',
            'location'         => 'nullable|string|max:255',
            'platform_link'    => 'nullable|url|max:500',
            'max_participants' => 'nullable|integer|min:1',
            'is_published'     => 'nullable|in:0,1',
            'cover_image'      => 'nullable|image|max:3072',
        ]);

        $validated['slug']        = $this->uniqueSlug(Str::slug($validated['title']));
        $validated['is_free']     = (bool) $validated['is_free'];
        $validated['is_published']= (bool) ($validated['is_published'] ?? false);
        $validated['price']       = $validated['is_free'] ? 0 : ($validated['price'] ?? 0);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('formations', 'public');
        } else {
            unset($validated['cover_image']);
        }

        $formation = Formation::create($validated);

        return response()->json($formation, 201);
    }

    public function update(Request $request, Formation $formation): JsonResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'description'      => 'nullable|string',
            'type'             => 'required|in:online,presentielle',
            'is_free'          => 'required|in:0,1',
            'price'            => 'required_if:is_free,0|numeric|min:0',
            'duration'         => 'nullable|string|max:100',
            'instructor_name'  => 'nullable|string|max:150',
            'date_start'       => 'nullable|date',
            'date_end'         => 'nullable|date|after_or_equal:date_start',
            'location'         => 'nullable|string|max:255',
            'platform_link'    => 'nullable|url|max:500',
            'max_participants' => 'nullable|integer|min:1',
            'is_published'     => 'nullable|in:0,1',
            'cover_image'      => 'nullable|image|max:3072',
            'remove_cover'     => 'nullable|string',
        ]);

        $validated['is_free']      = (bool) $validated['is_free'];
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['price']        = $validated['is_free'] ? 0 : ($validated['price'] ?? 0);

        if ($request->hasFile('cover_image')) {
            if ($formation->cover_image) Storage::disk('public')->delete($formation->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('formations', 'public');
        } elseif ($request->input('remove_cover') === '1') {
            if ($formation->cover_image) Storage::disk('public')->delete($formation->cover_image);
            $validated['cover_image'] = null;
        } else {
            unset($validated['cover_image']);
        }

        unset($validated['remove_cover']);

        if ($validated['title'] !== $formation->title) {
            $validated['slug'] = $this->uniqueSlug(Str::slug($validated['title']), $formation->id);
        }

        $formation->update($validated);

        return response()->json($formation);
    }

    public function destroy(Formation $formation): JsonResponse
    {
        if ($formation->cover_image) Storage::disk('public')->delete($formation->cover_image);
        $formation->delete();

        return response()->json(null, 204);
    }

    public function toggle(Request $request, Formation $formation): JsonResponse
    {
        $formation->update(['is_published' => $request->boolean('is_published')]);

        return response()->json($formation);
    }

    // ── Inscriptions ──────────────────────────────────────────────────────

    public function registrations(Request $request, Formation $formation): JsonResponse
    {
        $query = $formation->registrations()->orderByDesc('registered_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        return response()->json($query->paginate(20));
    }

    public function updateRegistration(Request $request, Formation $formation, FormationRegistration $registration): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $registration->update(['status' => $request->status]);

        return response()->json($registration);
    }

    private function uniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $original = $slug;
        $i = 1;
        while (Formation::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }
        return $slug;
    }
}
