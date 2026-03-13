<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ─────────────────────────────────────────────
    // LOGIN — for owners and managers
    // Email + Password
    // POST /api/auth/login
    // ─────────────────────────────────────────────
    public function login(Request $request)
    {
        // Step 1 — Validate incoming data
        // If validation fails → Laravel automatically
        // returns 422 with error messages
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Step 2 — Find user by email
        $user = User::where('email', $request->email)
                    ->whereNotNull('email')
                    ->first();

        // Step 3 — Check if user exists
        // Check if password is correct
        // Hash::check compares plain password
        // against the bcrypt hash in database
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
            // 401 = Unauthorized
        }

        // Step 4 — Check if user account is active
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Compte désactivé. Contactez votre administrateur.'
            ], 403);
            // 403 = Forbidden
        }

        // Step 5 — Check if company is active
        // Load the company relationship
        $company = $user->company;

        if ($company) {
            // Company exists → check status
            if ($company->status === 'suspended') {
                return response()->json([
                    'message' => 'Abonnement suspendu. Contactez le support.'
                ], 403);
            }

            // Trial expired check
            if ($company->trialExpired()) {
                return response()->json([
                    'message' => 'Période d\'essai expirée. Veuillez souscrire un abonnement.'
                ], 403);
            }
        }

        // Step 6 — Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Step 7 — Delete old tokens (optional but clean)
        // One active token per user at a time
        $user->tokens()->delete();

        // Step 8 — Create new Sanctum token
        // 'pos-token' is just the token name (label)
        $token = $user->createToken('pos-token')->plainTextToken;

        // Step 9 — Return token + user data
        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'company_id' => $user->company_id,
                'company'    => $company ? [
                    'id'       => $company->id,
                    'name'     => $company->name,
                    'slug'     => $company->slug,
                    'plan'     => $company->plan,
                    'currency' => $company->currency,
                    'logo'     => $company->logo,
                ] : null,
            ],
        ], 200);
    }

    // ─────────────────────────────────────────────
    // PIN LOGIN — for waiters
    // company_id + pin_code
    // POST /api/auth/pin-login
    // ─────────────────────────────────────────────
    public function pinLogin(Request $request)
    {
        // Step 1 — Validate
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'pin_code'   => 'required|string|min:4|max:6',
        ]);

        // Step 2 — Find waiter by company + PIN
        $user = User::where('company_id', $request->company_id)
                    ->where('pin_code', $request->pin_code)
                    ->where('pin_active', true)
                    ->where('is_active', true)
                    ->first();

        // Step 3 — Wrong PIN or user not found
        if (!$user) {
            return response()->json([
                'message' => 'PIN incorrect ou compte désactivé'
            ], 401);
        }

        // Step 4 — Check company status
        $company = $user->company;

        if ($company->status === 'suspended') {
            return response()->json([
                'message' => 'Abonnement suspendu. Contactez le support.'
            ], 403);
        }

        // Step 5 — Update last login
        $user->update(['last_login_at' => now()]);

        // Step 6 — Create token
        // Delete old tokens first for this user
        $user->tokens()->delete();
        $token = $user->createToken('pos-token')->plainTextToken;

        // Step 7 — Return token + user data
        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->name,
                'role'       => $user->role,
                'company_id' => $user->company_id,
                'company'    => [
                    'id'       => $company->id,
                    'name'     => $company->name,
                    'slug'     => $company->slug,
                    'currency' => $company->currency,
                    'logo'     => $company->logo,
                ],
            ],
        ], 200);
    }

    // ─────────────────────────────────────────────
    // ME — get current logged in user
    // Requires: valid token in header
    // GET /api/auth/me
    // ─────────────────────────────────────────────
    public function me(Request $request)
    {
        // $request->user() = the authenticated user
        // Sanctum already identified them from token
        $user = $request->user();

        // Load company relationship
        $user->load('company');

        return response()->json([
            'user' => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'phone'        => $user->phone,
                'role'         => $user->role,
                'company_id'   => $user->company_id,
                'last_login_at'=> $user->last_login_at,
                'company'      => $user->company ? [
                    'id'       => $user->company->id,
                    'name'     => $user->company->name,
                    'slug'     => $user->company->slug,
                    'plan'     => $user->company->plan,
                    'status'   => $user->company->status,
                    'currency' => $user->company->currency,
                    'logo'     => $user->company->logo,
                ] : null,
            ],
        ], 200);
    }

    // ─────────────────────────────────────────────
    // LOGOUT — destroy current token
    // Requires: valid token in header
    // POST /api/auth/logout
    // ─────────────────────────────────────────────
    public function logout(Request $request)
    {
    // Delete all tokens for this user
    $request->user()->tokens()->delete();

    return response()->json([
        'message' => 'Déconnecté avec succès'
    ], 200);
    }

}