<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::orderByDesc('created_at');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'role'       => 'required|in:admin,super_admin',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        if ($user->role === 'super_admin') {
            return response()->json(['message' => 'Impossible de modifier le rôle d\'un super administrateur.'], 403);
        }

        $request->validate([
            'role' => 'required|in:admin,client',
        ]);

        $user->update(['role' => $request->role]);

        return response()->json($user);
    }
}
