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
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // 🔐 DEVICE SERVICE: Session kaydını sil (Tenant-aware, setting'den kontrol)
        if ($user && tenant()) {
            $deviceService = app(DeviceService::class);
            $deviceService->unregisterSession($user); // DeviceService kendi içinde shouldRun() kontrol eder
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Çıkış yapıldı'
        ]);
    }

    /**
     * Check session validity (device limit polling)
     * Frontend her 30 saniyede bir cagiriyor
     */
    public function checkSession(Request $request)
    {
        $sessionId = session()->getId();

        // 🔍 DEBUG: Session ve auth durumunu logla
        \Log::info('🔐 checkSession DEBUG', [
            'session_id' => substr($sessionId, 0, 20) . '...',
            'has_session' => $request->hasSession(),
            'web_user' => auth('web')->user()?->email,
            'sanctum_user' => auth('sanctum')->user()?->email,
            'auth_check' => \Auth::check(),
            'cookies' => array_keys($request->cookies->all()),
        ]);

        // 🔥 FIX: Hem web hem sanctum guard'ı kontrol et
        // Session-based login (web) veya token-based login (sanctum)
        $user = auth('web')->user() ?? auth('sanctum')->user();

        if (!$user) {
            \Log::warning('🔐 checkSession - NOT AUTHENTICATED', [
                'session_id' => substr($sessionId, 0, 20) . '...',
            ]);
            return response()->json([
                'valid' => false,
                'reason' => 'not_authenticated'
            ]);
        }

        // 🔐 DEVICE SERVICE: Session validity kontrol et (Tenant-aware)
        if (tenant()) {
            $deviceService = app(DeviceService::class);

            if ($deviceService->shouldRun()) {
                // 1. 🔐 ÖNCE: Session DB'de var mi kontrol et (LIFO ile silinmis olabilir)
                $sessionExists = \DB::table('user_active_sessions')
                    ->where('session_id', $sessionId)
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$sessionExists) {
                    \Log::info('🔐 Session terminated (LIFO) - forcing logout', [
                        'user_id' => $user->id,
                        'session_id' => substr($sessionId, 0, 20) . '...',
                    ]);

                    // Session silinmis - kullaniciyi logout et
                    // 🔥 FIX: Hem web hem sanctum guard'ını logout et
                    auth('web')->logout();
                    if ($request->hasSession()) {
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                    }

                    return response()->json([
                        'valid' => false,
                        'reason' => 'session_terminated',
                        'message' => 'Baska bir cihazdan giris yapildi.'
                    ]);
                }

                // 2. Session var - activity guncelle
                $deviceService->updateSessionActivity($user);

                // 3. Device limit kontrolu (normalde LIFO ile asim olmamali)
                $deviceLimit = $deviceService->getDeviceLimit($user);
                $activeDevices = $deviceService->getActiveDeviceCount($user);

                if ($activeDevices > $deviceLimit) {
                    \Log::warning('🔐 Device limit exceeded during poll (unexpected)', [
                        'user_id' => $user->id,
                        'device_limit' => $deviceLimit,
                        'active_devices' => $activeDevices,
                    ]);

                    return response()->json([
                        'valid' => false,
                        'reason' => 'device_limit_exceeded',
                        'device_limit' => $deviceLimit,
                        'active_devices' => $activeDevices,
                        'show_device_modal' => true
                    ]);
                }
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
