<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

            // 🔐 DEVICE LIMIT: Session kaydet ve limit kontrolü (Tenant-aware, setting'den kontrol)
            if (tenant()) {
                $deviceService = app(DeviceService::class);
                $deviceService->registerSession($user); // DeviceService kendi içinde shouldRun() kontrol eder
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

        // 🔐 DEVICE LIMIT: Session kaydet ve limit kontrolü (Tenant-aware, setting'den kontrol)
        if (tenant()) {
            $deviceService = app(DeviceService::class);
            $deviceService->registerSession($user); // DeviceService kendi içinde shouldRun() kontrol eder
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
     * Logout - TAM ÇIKIŞ
     * Session, cookie, device kaydı hepsini temizler
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            // 🔐 DEVICE SERVICE: Session kaydını sil
            if ($user && tenant()) {
                $deviceService = app(DeviceService::class);
                $deviceService->unregisterSession($user);
            }

            Auth::logout();

            // Session invalidate et
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        } catch (\Exception $e) {
            \Log::warning('Logout error (ignored): ' . $e->getMessage());
        }

        // 🔥 Cookie'leri server-side expire et (HttpOnly için ZORUNLU)
        $sessionCookie = config('session.cookie', 'laravel_session');

        return response()->json([
            'success' => true,
            'message' => 'Çıkış yapıldı'
        ])
        ->withCookie(cookie()->forget($sessionCookie))
        ->withCookie(cookie()->forget('XSRF-TOKEN'));
    }

    /**
     * Check session validity (device limit polling)
     * Frontend her 30 saniyede bir çağırıyor
     *
     * 🔥 IMPORTANT: Bu endpoint Sanctum stateful auth kullanıyor.
     * Frontend'den Referer header gönderilmeli (EnsureFrontendRequestsAreStateful için)
     */
    public function checkSession(Request $request)
    {
        // Kullanıcı authenticated mi?
        // 🔥 Sanctum stateful auth: web guard öncelikli, sanctum fallback
        $user = auth('web')->user() ?? auth('sanctum')->user();

        if (!$user) {
            // 🔍 DEBUG: Neden authenticated değil? (sadece development'ta log)
            if (config('app.debug')) {
                \Log::debug('🔐 checkSession: not_authenticated', [
                    'has_referer' => $request->hasHeader('referer'),
                    'referer' => $request->header('referer'),
                    'has_session' => $request->hasSession(),
                    'session_id' => $request->hasSession() ? substr(session()->getId(), 0, 10) . '...' : 'N/A',
                ]);
            }

            return response()->json([
                'valid' => false,
                'reason' => 'not_authenticated'
            ]);
        }

        // Tenant varsa session kontrolü yap
        if (tenant()) {
            $deviceService = app(DeviceService::class);

            if ($deviceService->shouldRun()) {
                // 🔥 LIFO CHECK: Session DB'de var mı? (TAM EŞLEŞME)
                // Session sync KALDIRILDI - LIFO düzgün çalışsın diye
                // Her cihaz kendi session'ını tutuyor, farklı session = farklı cihaz
                if (!$deviceService->sessionExists($user)) {
                    // Cache'den silinme nedenini oku
                    $cookieToken = $request->cookie('mzb_login_token');
                    $deletedReason = null;

                    if ($cookieToken) {
                        $cacheKey = "session_deleted_reason:{$user->id}:{$cookieToken}";
                        $deletedReason = Cache::get($cacheKey);

                        // Cache'den okuduktan sonra sil (tek kullanımlık)
                        if ($deletedReason) {
                            Cache::forget($cacheKey);
                        }
                    }

                    // Reason'a göre mesaj belirle
                    // Oturum sadece 3 sebepten kapanır: LIFO, manuel logout, session expired
                    $message = match($deletedReason) {
                        'lifo' => 'Başka bir cihazdan giriş yapıldı.',
                        'manual_logout' => 'Oturumunuz kapatıldı.',
                        'admin_terminated' => 'Oturumunuz yönetici tarafından sonlandırıldı.',
                        default => 'Oturumunuz sonlandırıldı. Lütfen tekrar giriş yapın.',
                    };

                    \Log::info('🔐 checkSession: Session not found', [
                        'user_id' => $user->id,
                        'deleted_reason' => $deletedReason ?? 'unknown'
                    ]);

                    Auth::logout();

                    if ($request->hasSession()) {
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                    }

                    $sessionCookie = config('session.cookie', 'laravel_session');

                    return response()->json([
                        'valid' => false,
                        'reason' => 'session_terminated',
                        'message' => $message,
                    ])
                    ->withCookie(cookie()->forget($sessionCookie))
                    ->withCookie(cookie()->forget('XSRF-TOKEN'));
                }

                // Session var ve ID eşleşiyor - activity güncelle
                $deviceService->updateSessionActivity($user);
            }
        }

        return response()->json([
            'valid' => true,
            'user_id' => $user->id
        ]);
    }

    /**
     * Terminate a device session
     */
    public function terminateDevice(Request $request)
    {
        try {
            // 🔥 FIX: Hem web hem sanctum guard'ı kontrol et
            $user = auth('web')->user() ?? auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $request->validate([
                'session_id' => 'required|string'
            ]);

            // Tenant kontrolü (setting'den device limit aktif mi kontrol et)
            if (!tenant()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device limit feature not available'
                ], 400);
            }

            $deviceService = app(DeviceService::class);

            // DeviceService shouldRun() kontrolü yapar
            if (!$deviceService->shouldRun()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device limit feature not enabled for this tenant'
                ], 400);
            }

            \Log::info('🔐 terminateDevice ATTEMPT', [
                'session_id' => $request->session_id,
                'user_id' => $user->id,
                'tenant_id' => tenant()->id ?? null,
            ]);

            $result = $deviceService->terminateSession($request->session_id, $user);

            \Log::info('🔐 terminateDevice RESULT', [
                'session_id' => $request->session_id,
                'result' => $result,
            ]);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Device terminated' : 'Device not found'
            ]);
        } catch (\Exception $e) {
            \Log::error('🔐 terminateDevice ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => $request->session_id ?? 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active devices for user
     */
    public function getActiveDevices(Request $request)
    {
        // 🔥 FIX: Hem web hem sanctum guard'ı kontrol et
        $user = auth('web')->user() ?? auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Tenant kontrolü
        if (!tenant()) {
            return response()->json([
                'success' => true,
                'devices' => [],
                'device_limit' => 999
            ]);
        }

        $deviceService = app(DeviceService::class);
        $devices = $deviceService->getActiveDevices($user); // shouldRun() kontrol eder
        $deviceLimit = $deviceService->getDeviceLimit($user);

        // 🔐 DEBUG: Device limit hierarchy check
        \Log::info('🔐 getActiveDevices DEBUG', [
            'user_id' => $user->id,
            'user_device_limit_raw' => $user->device_limit,
            'calculated_device_limit' => $deviceLimit,
            'devices_count' => count($devices),
        ]);

        return response()->json([
            'success' => true,
            'devices' => $devices,
            'device_limit' => $deviceLimit
        ]);
    }

    /**
     * Check auth status
     */
    public function me(Request $request)
    {
        // 🔥 FIX: Hem web hem sanctum guard'ı kontrol et
        $user = auth('web')->user() ?? auth('sanctum')->user();

        if ($user) {

            // Active subscription bilgisini al
            // 🔥 FIX: ends_at -> current_period_end (doğru kolon adı)
            $activeSubscription = $user->subscriptions()
                ->where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('current_period_end')
                      ->orWhere('current_period_end', '>', now());
                })
                ->first();

            // Device limit bilgisi al (tenant-aware)
            $deviceLimit = 1; // Default fallback
            if (tenant()) {
                $deviceService = app(\Modules\Muzibu\App\Services\DeviceService::class);
                $deviceLimit = $deviceService->getDeviceLimit($user);
            }

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
                    'device_limit' => $deviceLimit,
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
