<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authentifie un administrateur et retourne un token Sanctum.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        if (! in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json([
                'message' => 'Accès refusé. Vous n\'avez pas les droits administrateur.',
            ], 403);
        }

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'user'  => [
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
                'role'       => $user->role,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Révoque le token courant (déconnexion).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    /**
     * Retourne l'utilisateur authentifié.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'         => $user->id,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'role'       => $user->role,
        ]);
    }
}
