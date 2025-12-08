<?php

namespace Modules\Muzibu\App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Jenssegers\Agent\Agent;

/**
 * DeviceService - Redis Session + user_active_sessions Hybrid System
 *
 * Redis session driver ile birlikte calisir.
 * user_active_sessions tablosu device tracking icin kullanilir.
 * Login/logout event'lerinde bu tablo guncellenir.
 *
 * @package Modules\Muzibu\App\Services
 */
class DeviceService
{
    protected string $table = 'user_active_sessions';

    /**
     * Check if this service should run
     * Device limit özelliği aktif olan tüm tenant'lar için çalışır
     */
    protected function shouldRun(): bool
    {
        $tenant = tenant();
        if (!$tenant) {
            return false;
        }

        // Setting'den device limit aktif mi kontrol et
        // Varsayılan: true (tüm tenant'lar için aktif)
        return (bool) setting('auth_device_limit_enabled', true);
    }

    /**
     * Register a new session for user (called on login)
     */
    public function registerSession(User $user): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $sessionId = session()->getId();
        if (!$sessionId) {
            \Log::warning('DeviceService::registerSession - No session ID');
            return;
        }

        $agent = new Agent();
        $currentIp = request()->ip();
        $currentUserAgent = request()->userAgent();

        \Log::info('🔐 DeviceService::registerSession - START', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'device_name' => $this->getDeviceName($agent),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'ip' => $currentIp,
        ]);

        // ❌ IP + User Agent kontrolünü KALDIRDIK!
        // Sebep: Aynı bilgisayardan farklı tarayıcılarla giriş yapılınca
        // her iki tarayıcı da aynı IP'ye sahip olduğu için birbirini siliyordu!
        // Şimdi sadece session_id benzersiz olacak, her tarayıcı ayrı device sayılır

        // Mevcut session varsa guncelle, yoksa ekle
        DB::table($this->table)->updateOrInsert(
            ['session_id' => $sessionId],
            [
                'user_id' => $user->id,
                'ip_address' => $currentIp,
                'user_agent' => $currentUserAgent,
                'device_type' => $this->getDeviceType($agent),
                'device_name' => $this->getDeviceName($agent),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'last_activity' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        \Log::info('🔐 DeviceService::registerSession - COMPLETE', [
            'session_id' => $sessionId,
            'user_id' => $user->id,
        ]);

        // Device limit kontrolu ve cleanup
        $this->handlePostLoginDeviceLimit($user);

        // Log activity
        $this->logActivity($user, 'login');
    }

    /**
     * Unregister session (called on logout)
     */
    public function unregisterSession(User $user): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $sessionId = session()->getId();
        if ($sessionId) {
            DB::table($this->table)
                ->where('session_id', $sessionId)
                ->delete();
        }

        // Log activity
        $this->logActivity($user, 'logout');

        // Premium cache temizle
        \Cache::forget('user_' . $user->id . '_is_premium_tenant_' . tenant()->id);
    }

    /**
     * Update session activity (called periodically - session polling)
     */
    public function updateSessionActivity(User $user): bool
    {
        if (!$this->shouldRun()) {
            return true;
        }

        $sessionId = session()->getId();
        if (!$sessionId) {
            \Log::warning('🔐 DeviceService: No session ID available');
            return false;
        }

        // Session kaydi var mi kontrol et
        $session = DB::table($this->table)
            ->where('session_id', $sessionId)
            ->where('user_id', $user->id)
            ->first();

        if (!$session) {
            // Debug: Kayitli session'lari listele
            $allSessions = DB::table($this->table)
                ->where('user_id', $user->id)
                ->get(['session_id', 'device_name', 'browser', 'last_activity'])
                ->toArray();

            \Log::warning('🔐 DeviceService: Session not found in DB!', [
                'current_session_id' => $sessionId,
                'user_id' => $user->id,
                'registered_sessions_count' => count($allSessions),
                'registered_sessions' => $allSessions,
            ]);

            // Session kaydi yok - baska cihazdan cikarilmis olabilir
            return false;
        }

        \Log::info('🔐 DeviceService: Session found - updating activity', [
            'session_id' => $sessionId,
            'user_id' => $user->id,
            'device_name' => $session->device_name ?? 'unknown',
        ]);

        // Activity guncelle
        DB::table($this->table)
            ->where('session_id', $sessionId)
            ->update(['last_activity' => now()]);

        return true;
    }

    /**
     * Get active sessions for user
     */
    public function getActiveDevices(User $user): array
    {
        if (!$this->shouldRun()) {
            return [];
        }

        // 30 dakikadan eski session'lari temizle (stale sessions)
        $this->cleanupStaleSessions($user);

        return DB::table($this->table)
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'device_type' => $session->device_type,
                    'device_name' => $session->device_name,
                    'browser' => $session->browser,
                    'platform' => $session->platform,
                    'last_activity' => $session->last_activity,
                    'last_activity_human' => $this->getHumanTime(strtotime($session->last_activity)),
                    'is_current' => $session->session_id === session()->getId(),
                ];
            })
            ->toArray();
    }

    /**
     * Get active session count
     */
    public function getActiveDeviceCount(User $user): int
    {
        if (!$this->shouldRun()) {
            return 0;
        }

        return DB::table($this->table)
            ->where('user_id', $user->id)
            ->count();
    }

    /**
     * Check if user has exceeded device limit
     */
    public function checkDeviceLimit(User $user): bool
    {
        if (!$this->shouldRun()) {
            return true;
        }

        $limit = $this->getDeviceLimit($user);
        $activeCount = $this->getActiveDeviceCount($user);

        return $activeCount < $limit;
    }

    /**
     * Get device limit for user
     * 3-Seviyeli Hierarchy: 1) User device_limit (VIP/Ban), 2) Subscription Plan, 3) Settings fallback
     */
    public function getDeviceLimit(User $user): int
    {
        if (!$this->shouldRun()) {
            return 999;
        }

        // 1. User override (VIP/Test/Ban durumlari)
        if ($user->device_limit !== null && $user->device_limit > 0) {
            return $user->device_limit;
        }

        // 2. Subscription Plan device_limit
        $subscription = $user->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->where(function($q) {
                $q->whereNull('current_period_end')
                  ->orWhere('current_period_end', '>', now());
            })
            ->with('plan')
            ->first();

        if ($subscription && $subscription->plan && $subscription->plan->device_limit) {
            return (int) $subscription->plan->device_limit;
        }

        // 3. Tenant setting fallback
        if (function_exists('setting') && setting('auth_device_limit')) {
            return (int) setting('auth_device_limit');
        }

        // 4. Ultimate fallback: 1 cihaz
        return 1;
    }

    /**
     * Handle device limit after successful login
     * Login sonrasi cagrilir - limit asildiydısa en eski session silinir
     */
    public function handlePostLoginDeviceLimit(User $user): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        // 🚨 DEVICE LIMIT: Login sonrası OTOMATİK SİLME YAPMA!
        // Sadece frontend session polling device limit modal gösterecek
        // Kullanıcı hangi cihazı çıkaracağını SEÇMELİ

        // Premium cache'i temizle (login sonrası fresh check için)
        \Cache::forget('user_' . $user->id . '_is_premium_tenant_' . tenant()->id);
    }

    /**
     * Check device limit BEFORE login
     * SADECE bilgilendirme amacli - login'i engellemez
     * Limit asimdaysa modal gosterilir, kullanici secer
     */
    public function checkDeviceLimitBeforeLogin(User $user): array
    {
        if (!$this->shouldRun()) {
            return ['can_login' => true, 'devices' => [], 'limit' => 999];
        }

        $limit = $this->getDeviceLimit($user);
        $currentSessionId = session()->getId();

        // Mevcut session'lari al
        $sessions = DB::table($this->table)
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get();

        // Bu cihaz zaten kayitli mi?
        $isCurrentDeviceRegistered = $sessions->contains('session_id', $currentSessionId);

        // Kayitli degilse ve limit doluysa
        $currentCount = $sessions->count();
        $canLogin = $isCurrentDeviceRegistered || $currentCount < $limit;

        return [
            'can_login' => $canLogin,
            'devices' => $sessions->map(function($s) use ($currentSessionId) {
                return [
                    'id' => $s->id,
                    'session_id' => $s->session_id,
                    'device_name' => $s->device_name,
                    'browser' => $s->browser,
                    'platform' => $s->platform,
                    'ip_address' => $s->ip_address,
                    'last_activity' => $s->last_activity,
                    'is_current' => $s->session_id === $currentSessionId,
                ];
            })->toArray(),
            'limit' => $limit,
            'current_count' => $currentCount,
        ];
    }

    /**
     * Terminate a specific session by ID
     */
    public function terminateSessionById(int $sessionDbId, User $actor = null): bool
    {
        if (!$this->shouldRun()) {
            return false;
        }

        $session = DB::table($this->table)->where('id', $sessionDbId)->first();
        if (!$session) {
            return false;
        }

        // Redis'ten de session'i sil
        $this->invalidateRedisSession($session->session_id);

        // Tablodan sil
        $deleted = DB::table($this->table)
            ->where('id', $sessionDbId)
            ->delete() > 0;

        if ($deleted && $actor) {
            $this->logActivity($actor, 'device_force_logout', [
                'target_session_id' => $session->session_id,
                'target_user_id' => $session->user_id,
            ]);
        }

        return $deleted;
    }

    /**
     * Terminate session by session_id string
     */
    public function terminateSession(string $sessionId, User $actor = null): bool
    {
        if (!$this->shouldRun()) {
            return false;
        }

        // Redis'ten session'i sil
        $this->invalidateRedisSession($sessionId);

        // Tablodan sil
        $deleted = DB::table($this->table)
            ->where('session_id', $sessionId)
            ->delete() > 0;

        if ($deleted && $actor) {
            $this->logActivity($actor, 'device_force_logout', [
                'session_id' => $sessionId,
            ]);
        }

        return $deleted;
    }

    /**
     * Terminate oldest session for user
     */
    public function terminateOldestSession(User $user): bool
    {
        if (!$this->shouldRun()) {
            return false;
        }

        $oldest = DB::table($this->table)
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'asc')
            ->first();

        if ($oldest) {
            return $this->terminateSession($oldest->session_id, $user);
        }

        return false;
    }

    /**
     * Terminate all sessions except current
     */
    public function terminateOtherSessions(User $user): int
    {
        if (!$this->shouldRun()) {
            return 0;
        }

        $currentSessionId = session()->getId();

        // Silinecek session'lari al
        $sessions = DB::table($this->table)
            ->where('user_id', $user->id)
            ->where('session_id', '!=', $currentSessionId)
            ->get();

        // Redis'ten sil
        foreach ($sessions as $session) {
            $this->invalidateRedisSession($session->session_id);
        }

        // Tablodan sil
        $count = DB::table($this->table)
            ->where('user_id', $user->id)
            ->where('session_id', '!=', $currentSessionId)
            ->delete();

        if ($count > 0) {
            $this->logActivity($user, 'device_logout_all', [
                'devices_logged_out' => $count,
            ]);
        }

        return $count;
    }

    /**
     * Terminate all sessions for user
     */
    public function terminateAllSessions(User $user): int
    {
        if (!$this->shouldRun()) {
            return 0;
        }

        // Silinecek session'lari al
        $sessions = DB::table($this->table)
            ->where('user_id', $user->id)
            ->get();

        // Redis'ten sil
        foreach ($sessions as $session) {
            $this->invalidateRedisSession($session->session_id);
        }

        // Tablodan sil
        $count = DB::table($this->table)
            ->where('user_id', $user->id)
            ->delete();

        if ($count > 0) {
            $this->logActivity($user, 'device_logout_all', [
                'devices_logged_out' => $count,
                'include_current' => true,
            ]);
        }

        return $count;
    }

    /**
     * Invalidate Redis session
     */
    protected function invalidateRedisSession(string $sessionId): void
    {
        try {
            $prefix = config('database.redis.options.prefix', 'laravel_database_');
            $sessionPrefix = config('session.cookie', 'laravel_session');

            // Redis key patterns
            $keys = [
                $prefix . $sessionPrefix . ':' . $sessionId,
                $prefix . 'sessions:' . $sessionId,
                'laravel:' . $sessionId,
            ];

            foreach ($keys as $key) {
                Redis::del($key);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to invalidate Redis session: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup stale sessions (no activity for 30 minutes)
     */
    protected function cleanupStaleSessions(User $user): void
    {
        DB::table($this->table)
            ->where('user_id', $user->id)
            ->where('last_activity', '<', now()->subMinutes(30))
            ->delete();
    }

    /**
     * Log user activity using Spatie Activity Log
     */
    public function logActivity(User $user, string $action, array $details = []): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $agent = new Agent();
        $messages = [
            'device_logout' => 'Cihazdan cikis yapildi',
            'device_force_logout' => 'Baska cihaz zorla kapatildi',
            'device_logout_all' => 'Tum cihazlardan cikis yapildi',
            'login' => 'Giris yapildi',
            'logout' => 'Cikis yapildi',
        ];

        try {
            activity()
                ->causedBy($user)
                ->inLog('device_management')
                ->withProperties([
                    'action' => $action,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'device_type' => $this->getDeviceType($agent),
                    'device_name' => $this->getDeviceName($agent),
                    'session_id' => session()->getId(),
                    'details' => $details,
                ])
                ->log($messages[$action] ?? $action);
        } catch (\Exception $e) {
            \Log::warning('Activity log failed: ' . $e->getMessage());
        }
    }

    /**
     * Get device type from agent
     */
    protected function getDeviceType(Agent $agent): string
    {
        if ($agent->isMobile()) {
            return 'mobile';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Get device name from agent
     */
    protected function getDeviceName(Agent $agent): string
    {
        $browser = $agent->browser();
        $platform = $agent->platform();

        if ($agent->isDesktop()) {
            return "{$platform} - {$browser}";
        }

        $device = $agent->device();
        return $device ?: "{$platform} - {$browser}";
    }

    /**
     * Get human readable time difference
     */
    protected function getHumanTime(int $timestamp): string
    {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Az once';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' dakika once';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' saat once';
        } else {
            $days = floor($diff / 86400);
            return $days . ' gun once';
        }
    }
}
