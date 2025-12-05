<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Modules\Muzibu\App\Services\DeviceService;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean'
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 🔐 DEVICE LIMIT: Limit aşıldıysa eski cihazdan çıkar (Tenant 1001 only)
            if (tenant() && tenant()->id == 1001) {
                $deviceService = app(DeviceService::class);
                $deviceService->handlePostLoginDeviceLimit($user);
            }

            return response()->json([
                'success' => true,
                'message' => 'Giriş başarılı',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_premium' => $user->isPremium(), // 👑 Premium status (subscription tablosundan kontrol)
                ],
                'csrf_token' => csrf_token(), // 🔐 Yeni CSRF token (session regenerate sonrası)
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['E-posta veya şifre hatalı.'],
        ]);
    }

    /**
     * Register
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'company' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Auto login after register
        Auth::login($user, true); // Remember me = true
        $request->session()->regenerate();

        // 🔐 DEVICE LIMIT: Limit aşıldıysa eski cihazdan çıkar (Tenant 1001 only)
        if (tenant() && tenant()->id == 1001) {
            $deviceService = app(DeviceService::class);
            $deviceService->handlePostLoginDeviceLimit($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hesabınız oluşturuldu! 7 günlük deneme başladı.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_premium' => $user->isPremium(), // 👑 Premium status (subscription tablosundan kontrol)
            ],
            'csrf_token' => csrf_token(), // 🔐 Yeni CSRF token (session regenerate sonrası)
        ]);
    }

    /**
     * Check if email exists
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json([
            'exists' => $exists,
            'available' => !$exists
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Çıkış yapıldı'
        ]);
    }

    /**
     * Check auth status
     */
    public function me(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Active subscription bilgisini al
            $activeSubscription = $user->subscriptions()
                ->where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
                })
                ->first();

            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_premium' => $user->isPremium(),
                    'trial_ends_at' => $activeSubscription && $activeSubscription->trial_ends_at
                        ? $activeSubscription->trial_ends_at->toIso8601String()
                        : null,
                ]
            ]);
        }

        return response()->json([
            'authenticated' => false
        ]);
    }

    /**
     * Forgot Password - Send reset link
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Laravel's built-in password reset
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Şifre sıfırlama linki e-postanıza gönderildi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bu e-posta adresiyle kayıtlı kullanıcı bulunamadı.'
        ], 404);
    }

    /**
     * Reset Password - Update new password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Şifreniz başarıyla değiştirildi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Şifre sıfırlama linki geçersiz veya süresi dolmuş.'
        ], 400);
    }
}
