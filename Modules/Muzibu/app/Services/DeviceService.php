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
     *
     * Kontroller:
     * 1. Tenant var mı?
     * 2. Abonelik sistemi açık mı? (auth_subscription)
     * 3. Device limit aktif mi? (auth_device_limit_enabled)
     */
    public function shouldRun(): bool
    {
        $tenant = tenant();
        if (!$tenant) {
            return false;
        }

        // 1. Abonelik sistemi kapalıysa device limit de çalışmasın
        // auth_subscription = 0 (Kapalı) ise false döner
        if (!setting('auth_subscription', false)) {
            return false;
        }

        // 2. Device limit özelliği kapatılmışsa çalışmasın
        // auth_device_limit_enabled = false ise false döner
        // Varsayılan: true (abonelik açıksa device limit de aktif)
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
     * NOT: shouldRun() kontrolü YOK - logout her zaman session silmeli!
     */
    public function unregisterSession(User $user): void
    {
        // ⚠️ shouldRun() kontrolü KALDIRILDI!
        // Logout yapan kullanıcının session'ı HER ZAMAN silinmeli
        // (subscription kapalı olsa bile)

        $tenant = tenant();
        if (!$tenant) {
            return;
        }

        $sessionId = session()->getId();

        \Log::info('🔐 DeviceService::unregisterSession', [
            'user_id' => $user->id,
            'session_id' => $sessionId ? substr($sessionId, 0, 20) . '...' : 'NULL',
            'tenant_id' => $tenant->id,
        ]);

        if ($sessionId) {
            $deleted = DB::table($this->table)
                ->where('session_id', $sessionId)
                ->delete();

            \Log::info('🔐 DeviceService::unregisterSession - Deleted', [
                'user_id' => $user->id,
                'rows_deleted' => $deleted,
            ]);
        }

        // Log activity (sadece shouldRun true ise)
        if ($this->shouldRun()) {
            $this->logActivity($user, 'logout');
        }

        // Premium cache temizle
        \Cache::forget('user_' . $user->id . '_is_premium_tenant_' . $tenant->id);
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
            // Session tabloda yok - yeni login olmuş olabilir veya session regenerate edilmiş
            // Kullanıcı authenticated ise session'ı otomatik kaydet (self-healing)

            \Log::info('🔐 DeviceService: Session not in DB - auto-registering (self-healing)', [
                'current_session_id' => $sessionId,
                'user_id' => $user->id,
            ]);

            // Session'ı kaydet
            $this->registerSession($user);

            // Kayıt sonrası device limit kontrolü
            $limit = $this->getDeviceLimit($user);
            $count = $this->getActiveDeviceCount($user);

            // Limit asilmissa EN ESKI session'i sil (LIFO - son giren kalir)
            if ($count > $limit) {
                \Log::info('🔐 DeviceService: Device limit exceeded after auto-register, removing oldest', [
                    'user_id' => $user->id,
                    'limit' => $limit,
                    'count' => $count,
                ]);

                // En eski session'ı bul ve sil (mevcut session hariç)
                $oldestSession = DB::table($this->table)
                    ->where('user_id', $user->id)
                    ->where('session_id', '!=', $sessionId)
                    ->orderBy('last_activity', 'asc')
                    ->first();

                if ($oldestSession) {
                    $this->terminateSession($oldestSession->session_id, $user);
                }
            }

            return true; // Kullanıcıyı logout etme, session kaydedildi
        }

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

        // 1. Stale sessions temizle (30 dakika inaktif)
        $this->cleanupStaleSessions($user);

        // 2. Ghost sessions temizle (Redis'te olmayan DB kayitlari)
        $this->cleanupGhostSessions($user);

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
     * Ghost session'lari temizledikten sonra sayar
     */
    public function getActiveDeviceCount(User $user): int
    {
        if (!$this->shouldRun()) {
            return 0;
        }

        // Ghost sessions temizle (her count sorgusunda degil, sadece 5 dakikada bir)
        $cacheKey = 'ghost_cleanup_' . $user->id . '_' . tenant()->id;
        if (!\Cache::has($cacheKey)) {
            $this->cleanupGhostSessions($user);
            \Cache::put($cacheKey, true, now()->addMinutes(5));
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
     * Login sonrasi cagrilir - limit asilmissa en eski session(lar) silinir
     * LIFO MANTIGI: Son giren kalir, eski cihazlar cikarilir
     */
    public function handlePostLoginDeviceLimit(User $user): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $limit = $this->getDeviceLimit($user);
        $currentSessionId = session()->getId();

        // Mevcut session sayisini al
        $activeSessions = DB::table($this->table)
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'asc') // En eski once
            ->get();

        $activeCount = $activeSessions->count();

        \Log::info('🔐 handlePostLoginDeviceLimit - LIFO Check', [
            'user_id' => $user->id,
            'limit' => $limit,
            'active_count' => $activeCount,
            'current_session' => substr($currentSessionId, 0, 20) . '...',
        ]);

        // Limit asilmissa eski session'lari sil (LIFO - son giren kalir)
        if ($activeCount > $limit) {
            $sessionsToRemove = $activeCount - $limit;

            \Log::info('🔐 LIFO: Removing oldest sessions', [
                'user_id' => $user->id,
                'sessions_to_remove' => $sessionsToRemove,
            ]);

            // En eski session'lari bul (mevcut session haric)
            $oldSessions = $activeSessions
                ->filter(fn($s) => $s->session_id !== $currentSessionId)
                ->take($sessionsToRemove);

            foreach ($oldSessions as $oldSession) {
                \Log::info('🔐 LIFO: Terminating old session', [
                    'session_id' => substr($oldSession->session_id, 0, 20) . '...',
                    'device_name' => $oldSession->device_name,
                    'last_activity' => $oldSession->last_activity,
                ]);

                $this->terminateSession($oldSession->session_id, $user);
            }
        }

        // Premium cache'i temizle (login sonrasi fresh check icin)
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
        \Log::info('🔐 DeviceService::terminateSession START', [
            'session_id' => $sessionId,
            'actor_id' => $actor ? $actor->id : null,
            'shouldRun' => $this->shouldRun(),
        ]);

        if (!$this->shouldRun()) {
            \Log::warning('🔐 DeviceService::terminateSession - shouldRun() returned false');
            return false;
        }

        // Session var mı kontrol et
        $sessionExists = DB::table($this->table)->where('session_id', $sessionId)->exists();
        \Log::info('🔐 DeviceService::terminateSession - Session exists check', [
            'session_id' => $sessionId,
            'exists' => $sessionExists,
        ]);

        // Redis'ten session'i sil
        $this->invalidateRedisSession($sessionId);

        // Tablodan sil
        $deleteCount = DB::table($this->table)
            ->where('session_id', $sessionId)
            ->delete();

        $deleted = $deleteCount > 0;

        \Log::info('🔐 DeviceService::terminateSession COMPLETE', [
            'session_id' => $sessionId,
            'delete_count' => $deleteCount,
            'deleted' => $deleted,
        ]);

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
            // Dogru Redis key'i bul ve sil
            $key = $this->getRedisSessionKey($sessionId);
            if ($key) {
                Redis::del($key);
                \Log::info('🔐 Redis session invalidated', ['key' => $key]);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to invalidate Redis session: ' . $e->getMessage());
        }
    }

    /**
     * Check if Redis session exists
     * Ghost session tespiti icin kullanilir
     */
    protected function redisSessionExists(string $sessionId): bool
    {
        try {
            $key = $this->getRedisSessionKey($sessionId);
            return $key !== null;
        } catch (\Exception $e) {
            \Log::warning('Redis session check failed: ' . $e->getMessage());
            return true; // Hata durumunda session var kabul et (guvenli taraf)
        }
    }

    /**
     * Get the actual Redis key for a session
     * Farkli key pattern'leri dener ve var olani dondurur
     */
    protected function getRedisSessionKey(string $sessionId): ?string
    {
        try {
            $prefix = config('database.redis.options.prefix', 'laravel_database_');
            $sessionCookie = config('session.cookie', 'laravel_session');

            // Olasi key pattern'leri (oncelik sirasina gore)
            $patterns = [
                $prefix . $sessionCookie . ':' . $sessionId,
                $prefix . 'sessions:' . $sessionId,
                'laravel_database_' . $sessionCookie . ':' . $sessionId,
                'laravel:sessions:' . $sessionId,
            ];

            foreach ($patterns as $key) {
                // Redis::exists prefix ekliyor olabilir, connection uzerinden kontrol et
                $exists = Redis::connection()->exists($key);
                if ($exists) {
                    return $key;
                }
            }

            // Hicbiri bulunamadi - wildcard ara
            $wildcardKey = '*' . $sessionId . '*';
            $foundKeys = Redis::connection()->keys($wildcardKey);
            if (!empty($foundKeys)) {
                return $foundKeys[0];
            }

            return null;
        } catch (\Exception $e) {
            \Log::warning('Redis key lookup failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cleanup stale sessions (no activity for 30 minutes)
     */
    protected function cleanupStaleSessions(User $user): void
    {
        // 1. Zaman bazli temizlik (30 dakika inaktif)
        $staleByTime = DB::table($this->table)
            ->where('user_id', $user->id)
            ->where('last_activity', '<', now()->subMinutes(30))
            ->get();

        if ($staleByTime->count() > 0) {
            \Log::info('🔐 Cleaning stale sessions (time-based)', [
                'user_id' => $user->id,
                'count' => $staleByTime->count(),
            ]);

            DB::table($this->table)
                ->where('user_id', $user->id)
                ->where('last_activity', '<', now()->subMinutes(30))
                ->delete();
        }
    }

    /**
     * Cleanup ghost sessions (Redis'te olmayan DB kayitlari)
     * Tarayici kapatildiginda logout cagrilmaz - bu ghost session'lari temizler
     */
    public function cleanupGhostSessions(User $user): int
    {
        if (!$this->shouldRun()) {
            return 0;
        }

        $currentSessionId = session()->getId();
        $ghostCount = 0;

        // Kullanicinin tum session'larini al
        $sessions = DB::table($this->table)
            ->where('user_id', $user->id)
            ->get();

        foreach ($sessions as $session) {
            // Mevcut session'i atlama
            if ($session->session_id === $currentSessionId) {
                continue;
            }

            // Redis'te var mi kontrol et
            if (!$this->redisSessionExists($session->session_id)) {
                \Log::info('🔐 Ghost session found - removing', [
                    'user_id' => $user->id,
                    'session_id' => substr($session->session_id, 0, 20) . '...',
                    'device_name' => $session->device_name,
                    'last_activity' => $session->last_activity,
                ]);

                // DB'den sil
                DB::table($this->table)
                    ->where('id', $session->id)
                    ->delete();

                $ghostCount++;
            }
        }

        if ($ghostCount > 0) {
            \Log::info('🔐 Ghost sessions cleaned', [
                'user_id' => $user->id,
                'ghost_count' => $ghostCount,
            ]);
        }

        return $ghostCount;
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
     * Enforce device limit on plan change (downgrade/cancel/expire)
     * LIFO: Terminates oldest sessions to comply with new limit
     *
     * @param User $user
     * @param int|null $newLimit Yeni device limit (null ise getDeviceLimit() kullanılır)
     * @return int Number of terminated sessions
     */
    public function enforceDeviceLimitOnPlanChange(User $user, ?int $newLimit = null): int
    {
        if (!$this->shouldRun()) {
            return 0;
        }

        $limit = $newLimit ?? $this->getDeviceLimit($user);

        // Tüm aktif session'ları al (en eskiden yeniye)
        $activeSessions = DB::table($this->table)
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'asc')
            ->get();

        $activeCount = $activeSessions->count();

        // Limit aşılmadıysa hiçbir şey yapma
        if ($activeCount <= $limit) {
            return 0;
        }

        // Kaç session terminate edilecek?
        $sessionsToTerminate = $activeCount - $limit;

        \Log::info('🔐 Plan change: Enforcing device limit', [
            'user_id' => $user->id,
            'active_sessions' => $activeCount,
            'new_limit' => $limit,
            'sessions_to_terminate' => $sessionsToTerminate,
        ]);

        // En eski session'ları terminate et (LIFO - en yeni kalır)
        $terminated = 0;
        $oldSessions = $activeSessions->take($sessionsToTerminate);

        foreach ($oldSessions as $oldSession) {
            $result = $this->terminateSession($oldSession->session_id, $user);
            if ($result) {
                $terminated++;
            }
        }

        // Premium cache temizle
        \Cache::forget('user_' . $user->id . '_is_premium_tenant_' . tenant()->id);

        \Log::info('🔐 Plan change: Device limit enforced', [
            'user_id' => $user->id,
            'terminated_sessions' => $terminated,
        ]);

        return $terminated;
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
