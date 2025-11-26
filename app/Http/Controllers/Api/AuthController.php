<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            // Basic validation without Laravel Validator (to avoid tenant context issues)
            if (!$request->has('name') || !$request->has('email') || !$request->has('password')) {
                return response()->json([
                    'message' => 'Name, email and password are required',
                ], 422);
            }

            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'message' => 'Invalid email format',
                ], 422);
            }

            if (strlen($request->password) < 8) {
                return response()->json([
                    'message' => 'Password must be at least 8 characters',
                ], 422);
            }

            // Check if user already exists
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'message' => 'User already exists',
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            // Basic validation without Laravel Validator (to avoid tenant context issues)
            if (!$request->has('email') || !$request->has('password')) {
                return response()->json([
                    'message' => 'Email and password are required',
                ], 422);
            }

            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'message' => 'Invalid email format',
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }

            if (!$user->is_active) {
                return response()->json([
                    'message' => 'Account is not active',
                ], 403);
            }

            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            // Session-based auth için (web) - session varsa temizle
            if ($request->hasSession()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            // Token-based auth için (API/mobile)
            $user = $request->user('sanctum');
            if ($user && method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);
        } catch (\Exception $e) {
            // Hata olsa bile logout başarılı say
            return response()->json([
                'success' => true,
                'message' => 'Logged out',
            ]);
        }
    }
}