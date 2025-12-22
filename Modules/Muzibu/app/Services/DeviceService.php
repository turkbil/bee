<?php

namespace Modules\Muzibu\App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Jenssegers\Agent\Agent;

/**
 * DeviceService - Basit Session Takip Sistemi
 *
 * SADECE user_active_sessions tablosu ile çalışır.
 * Redis kontrolü YOK - session valid ise DB'de kalır.
 *
 * @package Modules\Muzibu\App\Services
 */
class DeviceService
{
    protected string $table = 'user_active_sessions';

    /**
     * Servis çalışmalı mı?
     */
    public function shouldRun(): bool
    {
        $tenant = tenant();
        if (!$tenant) {
            return false;
        }

        // Abonelik sistemi kapalıysa device limit de çalışmasın
        if (!setting('auth_subscription', false)) {
            return false;
        }

        // Device limit özelliği kapatılmışsa çalışmasın (auth_device setting key)
        return (bool) setting('auth_device', false);
    }

    /**
     * Yeni session kaydet (login sonrası)
     *
     * MANTIK:
     * 1. Cookie'de mzb_login_token var mı kontrol et
     * 2. Varsa ve DB'de eşleşiyorsa → AYNI TARAYICI, sadece güncelle
     * 3. Yoksa veya eşleşmiyorsa → FARKLI TARAYICI, LIFO uygula
     */
    public function registerSession(User $user): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $sessionId = session()->getId();
        if (!$sessionId) {
            return;
        }

        $agent = new Agent();
        $lifetime = (int) setting('auth_session_lifetime', 525600); // Varsayılan 1 yıl (panelden override)
        $cookieName = 'mzb_login_token';

        // 🔥 1. AYNI TARAYICI MI? Cookie kontrolü
        $existingToken = request()->cookie($cookieName);

        if ($existingToken) {
            $existingSession = DB::table($this->table)
                ->where('user_id', $user->id)
                ->where('login_token', $existingToken)
                ->first();

            if ($existingSession) {
                // ✅ AYNI TARAYICI - Mevcut kaydı güncelle, YENİ TOKEN OLUŞTURMA!
                DB::table($this->table)
                    ->where('id', $existingSession->id)
                    ->update([
                        'session_id' => $sessionId,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'last_activity' => now(),
                        'updated_at' => now(),
                    ]);

                // Cookie süresini uzat
                cookie()->queue(cookie($cookieName, $existingToken, $lifetime, '/', null, true, true, false, 'Lax'));

                \Log::info('🔐 AYNI TARAYICI: Session güncellendi (yeni token oluşturulmadı)', [
                    'user_id' => $user->id,
                    'session_id' => substr($sessionId, 0, 20) . '...',
                    'login_token' => substr($existingToken, 0, 16) . '...',
                ]);

                return; // İŞLEM BİTTİ - Yeni session oluşturma!
            }
        }

        // 🔥 2. FARKLI TARAYICI - DISTRIBUTED LOCK + LIFO
        $lock = Cache::lock("user_login:{$user->id}", 10);

        if (!$lock->get()) {
            \Log::warning('🔐 LOCK: Başka login işlemi devam ediyor', ['user_id' => $user->id]);
            // Lock alınamadı - yine de devam et (ama log'la)
        }

        try {
            $limit = $this->getDeviceLimit($user);
            $existingSessions = DB::table($this->table)
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'asc') // En eski önce
                ->get();

            // Limit aşıldıysa sadece fazla olan kadar session sil (LIFO - en eski önce)
            $existingCount = $existingSessions->count();
            $overLimit = max(0, $existingCount - $limit + 1); // yeni cihaz için yer aç

            if ($overLimit > 0) {
                $sessionsToRemove = $existingSessions->take($overLimit);

                foreach ($sessionsToRemove as $oldSession) {
                    $this->terminateSessionAtomicByRow($oldSession, 'lifo', $user);
                }
            }

            // 🔥 3. YENİ SESSION OLUŞTUR
            $loginToken = bin2hex(random_bytes(32)); // 64 char hex

            DB::table($this->table)->insert([
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'login_token' => $loginToken,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_type' => $this->getDeviceType($agent),
                'device_name' => $this->getDeviceName($agent),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'last_activity' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Cookie oluştur
            cookie()->queue(cookie($cookieName, $loginToken, $lifetime, '/', null, true, true, false, 'Lax'));

            \Log::info('🔐 YENİ TARAYICI: Session oluşturuldu', [
                'user_id' => $user->id,
                'session_id' => substr($sessionId, 0, 20) . '...',
                'login_token' => substr($loginToken, 0, 16) . '...',
            ]);

        } finally {
            $lock->release();
        }
    }

    /**
     * Limit aşıldı mı kontrol et (silmeden)
     */
    public function isDeviceLimitExceeded(User $user): bool
    {
        if (!$this->shouldRun()) {
            return false;
        }

        $limit = $this->getDeviceLimit($user);
        $count = DB::table($this->table)
            ->where('user_id', $user->id)
            ->count();

        return $count > $limit;
    }

    /**
     * Session sil (logout)
     */
    public function unregisterSession(User $user): void
    {
        $tenant = tenant();
        if (!$tenant) {
            return;
        }

        $sessionId = session()->getId();
        if ($sessionId) {
            $session = DB::table($this->table)
                ->where('session_id', $sessionId)
                ->first();

            if ($session) {
                $this->terminateSessionAtomicByRow($session, 'manual_logout', $user);
            }
        }
    }

    /**
     * Session activity güncelle
     *
     * 🔥 ÖNEMLİ: Session ID değişirse (Livewire regenerate) DB'yi güncelle
     * Ama SADECE bu kullanıcının tek bir session'ı varsa!
     * Birden fazla session varsa → Bu bir LIFO durumu, güncelleme yapma
     */
    public function updateSessionActivity(User $user): bool
    {
        if (!$this->shouldRun()) {
            return true;
        }

        $currentSessionId = $this->getCurrentSessionId();
        if (!$currentSessionId) {
            return false;
        }

        // Önce mevcut session ID ile güncellemeyi dene
        $updated = DB::table($this->table)
            ->where('session_id', $currentSessionId)
            ->where('user_id', $user->id)
            ->update(['last_activity' => now()]);

        if ($updated > 0) {
            return true; // Session bulundu ve güncellendi
        }

        // Session bulunamadı - Livewire regenerate olmuş olabilir
        // SADECE tek session varsa güncelle (aynı sekme/cihaz)
        $existingSession = DB::table($this->table)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSession) {
            // Tek session var - Livewire regenerate, session ID'yi güncelle
            $sessionCount = DB::table($this->table)
                ->where('user_id', $user->id)
                ->count();

            if ($sessionCount === 1) {
                \Log::info('🔐 updateSessionActivity: Session ID regenerated (Livewire), updating', [
                    'user_id' => $user->id,
                    'old_session' => substr($existingSession->session_id, 0, 20) . '...',
                    'new_session' => substr($currentSessionId, 0, 20) . '...',
                ]);

                DB::table($this->table)
                    ->where('id', $existingSession->id)
                    ->update([
                        'session_id' => $currentSessionId,
                        'last_activity' => now(),
                        'updated_at' => now(),
                    ]);

                return true;
            }
        }

        return false;
    }

    /**
     * Session DB'de var mı? (polling için)
     *
     * 🔥 LOGIN TOKEN YAKLAŞIMI:
     * Session ID Livewire tarafından değişebilir, ama login_token sabit kalır.
     * Cookie'deki login_token ile DB'deki login_token eşleşirse = geçerli session
     *
     * LIFO ile çalışması:
     * - Tab A login → token_A oluşur, cookie'ye ve DB'ye kaydedilir
     * - Tab B login → token_B oluşur, LIFO token_A'yı DB'den siler
     * - Tab A polling → cookie'de token_A var ama DB'de YOK → LOGOUT
     */
    public function sessionExists(User $user): bool
    {
        if (!$this->shouldRun()) {
            \Log::info('🔐 sessionExists: shouldRun() = FALSE, returning TRUE');
            return true; // Sistem kapalıysa her zaman valid
        }

        // 1. Cookie'den login_token al
        $cookieToken = request()->cookie('mzb_login_token');

        \Log::info('🔐 sessionExists: Starting check', [
            'user_id' => $user->id,
            'session_id' => substr($this->getCurrentSessionId() ?: 'NULL', 0, 20) . '...',
            'cookie_token' => $cookieToken ? substr($cookieToken, 0, 16) . '...' : 'NULL',
        ]);

        // Cookie'de token yoksa → tek session varsa cookie'yi re-issue et, yoksa invalid
        if (!$cookieToken) {
            $currentSessionId = $this->getCurrentSessionId();

            $sessionQuery = DB::table($this->table)->where('user_id', $user->id);
            $sessionCount = (clone $sessionQuery)->count();

            if ($sessionCount === 1) {
                $session = $sessionQuery->first();
                if ($session && $session->login_token) {
                    $lifetime = (int) setting('auth_session_lifetime', 525600);
                    cookie()->queue(cookie('mzb_login_token', $session->login_token, $lifetime, '/', null, true, true, false, 'Lax'));

                    // Eğer session_id farklıysa güncelle
                    if ($currentSessionId && $session->session_id !== $currentSessionId) {
                        DB::table($this->table)
                            ->where('id', $session->id)
                            ->update([
                                'session_id' => $currentSessionId,
                                'updated_at' => now(),
                                'last_activity' => now(),
                            ]);
                    }

                    \Log::info('🔐 sessionExists: Cookie yoktu, tek kayıt bulundu ve yeniden yazıldı', [
                        'user_id' => $user->id,
                        'session_id' => $currentSessionId ? substr($currentSessionId, 0, 20) . '...' : 'NULL',
                    ]);

                    return true;
                }
            }

            \Log::warning('🔐 sessionExists: No login_token cookie, returning FALSE (device limit enforced)', [
                'user_id' => $user->id,
            ]);
            return false;
        }

        // 2. Login token ile DB'de kontrol et
        $session = DB::table($this->table)
            ->where('user_id', $user->id)
            ->where('login_token', $cookieToken)
            ->first();

        if ($session) {
            // Token eşleşti - session geçerli
            // Session ID değiştiyse güncelle (Livewire regenerate)
            $currentSessionId = $this->getCurrentSessionId();
            if ($currentSessionId && $session->session_id !== $currentSessionId) {
                DB::table($this->table)
                    ->where('id', $session->id)
                    ->update([
                        'session_id' => $currentSessionId,
                        'last_activity' => now(),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table($this->table)
                    ->where('id', $session->id)
                    ->update(['last_activity' => now()]);
            }
            return true;
        }

        // 3. Token eşleşmiyor = LIFO tarafından silindi → LOGOUT
        \Log::info('🔐 sessionExists: Login token not found in DB - LIFO kicked', [
            'user_id' => $user->id,
            'cookie_token' => substr($cookieToken, 0, 16) . '...',
        ]);

        return false;
    }

    /**
     * Session ID ile kontrol (login_token yoksa fallback)
     *
     * 🔥 FIX: Livewire session regenerate sorunu için daha toleranslı kontrol
     * Session ID değişebilir (Livewire), önemli olan kullanıcının EN AZ BİR session'ı olması
     */
    protected function sessionExistsBySessionId(User $user): bool
    {
        $currentSessionId = $this->getCurrentSessionId();

        \Log::info('🔐 sessionExistsBySessionId: Checking', [
            'user_id' => $user->id,
            'current_session_id' => $currentSessionId ? substr($currentSessionId, 0, 20) . '...' : 'NULL',
        ]);

        // 🔥 FIX: Cookie yoksa, kullanıcının EN AZ BİR session'ı varsa TRUE döndür
        // Session ID eşleşmesi zorunlu DEĞİL çünkü Livewire regenerate yapabilir
        $userSession = DB::table($this->table)
            ->where('user_id', $user->id)
            ->first();

        if (!$userSession) {
            \Log::info('🔐 sessionExistsBySessionId: No session found for user', [
                'user_id' => $user->id,
            ]);
            return false;
        }

        // Session var - activity güncelle ve session_id sync et
        $updateData = ['last_activity' => now()];

        // Session ID varsa ve farklıysa güncelle (Livewire regenerate)
        if ($currentSessionId && $userSession->session_id !== $currentSessionId) {
            $updateData['session_id'] = $currentSessionId;
            $updateData['updated_at'] = now();
            \Log::info('🔐 sessionExistsBySessionId: Session ID updated (Livewire regenerate)', [
                'old_session' => substr($userSession->session_id, 0, 20) . '...',
                'new_session' => substr($currentSessionId, 0, 20) . '...',
            ]);
        }

        DB::table($this->table)
            ->where('id', $userSession->id)
            ->update($updateData);

        return true;
    }

    /**
     * Mevcut session ID'yi al (session veya cookie'den)
     *
     * API context'te session()->getId() boş dönebilir.
     * Cookie'den doğrudan session ID'yi çıkarmalıyız.
     *
     * Laravel session cookie formatı (decrypt sonrası):
     * - Simple: "SESSION_ID" (40 char)
     * - With HMAC: "HMAC_HASH|SESSION_ID" (hash|40 char)
     */
    protected function getCurrentSessionId(): ?string
    {
        // 🔍 DEBUG: Her source'u logla
        $sources = [];

        // 1. Önce session()->getId() dene (web context - en güvenilir)
        $sessionId = session()->getId();
        $sources['session_helper'] = $sessionId ?: 'EMPTY';
        if ($sessionId && $sessionId !== '' && strlen($sessionId) === 40) {
            \Log::debug('🔐 getCurrentSessionId: Using session() helper', [
                'session_id' => substr($sessionId, 0, 20) . '...',
            ]);
            return $sessionId;
        }

        // 2. Request session'dan ID al (API with session middleware)
        if (request()->hasSession()) {
            $reqSessionId = request()->session()->getId();
            $sources['request_session'] = $reqSessionId ?: 'EMPTY';
            if ($reqSessionId && $reqSessionId !== '' && strlen($reqSessionId) === 40) {
                \Log::debug('🔐 getCurrentSessionId: Using request()->session()', [
                    'session_id' => substr($reqSessionId, 0, 20) . '...',
                ]);
                return $reqSessionId;
            }
        } else {
            $sources['request_session'] = 'NO_SESSION';
        }

        // 3. Cookie'den parse et (fallback)
        $cookieName = config('session.cookie', 'laravel_session');
        $cookie = request()->cookie($cookieName);
        $sources['cookie_name'] = $cookieName;
        $sources['cookie_exists'] = $cookie ? 'YES' : 'NO';

        if ($cookie) {
            // Cookie zaten 40 char alphanumeric ise session ID'dir
            if (strlen($cookie) === 40 && ctype_alnum($cookie)) {
                \Log::debug('🔐 getCurrentSessionId: Using raw cookie (40 char)', [
                    'session_id' => substr($cookie, 0, 20) . '...',
                ]);
                return $cookie;
            }

            try {
                // Encrypted cookie - decrypt et
                $decrypted = decrypt($cookie, false);

                if ($decrypted) {
                    // Format: "HMAC_HASH|SESSION_ID" olabilir
                    if (str_contains($decrypted, '|')) {
                        $parts = explode('|', $decrypted);
                        $lastPart = end($parts);
                        if (strlen($lastPart) === 40 && ctype_alnum($lastPart)) {
                            \Log::debug('🔐 getCurrentSessionId: Using decrypted cookie (HMAC format)', [
                                'session_id' => substr($lastPart, 0, 20) . '...',
                            ]);
                            return $lastPart;
                        }
                    }

                    // Simple format - 40 char
                    if (strlen($decrypted) === 40 && ctype_alnum($decrypted)) {
                        \Log::debug('🔐 getCurrentSessionId: Using decrypted cookie (simple format)', [
                            'session_id' => substr($decrypted, 0, 20) . '...',
                        ]);
                        return $decrypted;
                    }
                }
            } catch (\Exception $e) {
                // Decrypt başarısız - log et
                $sources['cookie_decrypt_error'] = $e->getMessage();
            }
        }

        // 🔥 FALLBACK FAILED - Hiçbir source session ID bulamadı
        \Log::warning('🔐 getCurrentSessionId: ALL SOURCES FAILED', $sources);

        return null;
    }

    /**
     * Kullanıcının aktif cihazlarını getir
     */
    public function getActiveDevices(User $user): array
    {
        if (!$this->shouldRun()) {
            return [];
        }

        $currentSessionId = session()->getId();

        // NOT: Inactivity cleanup KALDIRILDI
        // Oturum sadece şu durumlarda kapanır:
        // 1. LIFO (başka cihazdan giriş)
        // 2. Manuel logout
        // 3. Session expired (Laravel native)

        return DB::table($this->table)
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                return [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'ip_address' => $session->ip_address,
                    'device_type' => $session->device_type,
                    'device_name' => $session->device_name,
                    'browser' => $session->browser,
                    'platform' => $session->platform,
                    'last_activity' => $session->last_activity,
                    'last_activity_human' => $this->getHumanTime(strtotime($session->last_activity)),
                    'is_current' => $session->session_id === $currentSessionId,
                ];
            })
            ->toArray();
    }

    /**
     * Aktif cihaz sayısı
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
     * Device limit al (3-tier: User > Plan > Setting)
     */
    public function getDeviceLimit(User $user): int
    {
        if (!$this->shouldRun()) {
            return 999;
        }

        // 1. User override
        if ($user->device_limit !== null && $user->device_limit > 0) {
            return $user->device_limit;
        }

        // 2. Subscription Plan
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

        return 1;
    }

    /**
     * LIFO: Limit aşıldıysa eski session'ları sil
     */
    protected function enforceDeviceLimit(User $user, string $currentSessionId): void
    {
        $limit = $this->getDeviceLimit($user);

        $sessions = DB::table($this->table)
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'asc') // En eski önce
            ->get();

        $count = $sessions->count();

        if ($count > $limit) {
            $toRemove = $count - $limit;

            // Yeni session bilgisini al (kick eden)
            $newSession = $sessions->firstWhere('session_id', $currentSessionId);

            // En eski session'ları sil (mevcut hariç)
            $oldSessions = $sessions
                ->filter(fn($s) => $s->session_id !== $currentSessionId)
                ->take($toRemove);

            foreach ($oldSessions as $old) {
                // 🔐 DETAYLI LOG - Session Termination
                $this->logSessionTermination($user, $newSession, $old, 'lifo');

                $this->terminateSessionAtomicByRow($old, 'lifo', $user);
            }
        }
    }

    /**
     * Belirli session'ı sonlandır
     */
    public function terminateSession(string $sessionId, User $actor = null): bool
    {
        if (!$this->shouldRun()) {
            return false;
        }

        $session = DB::table($this->table)
            ->where('session_id', $sessionId)
            ->first();

        return $session
            ? $this->terminateSessionAtomicByRow($session, 'manual_logout', $actor)
            : false;
    }

    /**
     * Birden fazla session'ı toplu sonlandır (login device selection modal için)
     *
     * @param array $sessionIds Sonlandırılacak session ID'leri
     * @param User|null $actor İşlemi yapan kullanıcı
     * @return int Silinen session sayısı
     */
    public function terminateSessions(array $sessionIds, User $actor = null): int
    {
        if (!$this->shouldRun() || empty($sessionIds)) {
            return 0;
        }

        $deletedCount = 0;

        foreach ($sessionIds as $sessionId) {
            $session = DB::table($this->table)
                ->where('session_id', $sessionId)
                ->first();

            if ($session && $this->terminateSessionAtomicByRow($session, 'manual_logout', $actor)) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Diğer tüm session'ları sonlandır
     */
    public function terminateOtherSessions(User $user): int
    {
        if (!$this->shouldRun()) {
            return 0;
        }

        $currentSessionId = session()->getId();

        $sessions = DB::table($this->table)
            ->where('user_id', $user->id)
            ->where('session_id', '!=', $currentSessionId)
            ->get();

        $deleted = 0;

        foreach ($sessions as $session) {
            if ($this->terminateSessionAtomicByRow($session, 'manual_logout', $user)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Device type belirle
     */
    protected function getDeviceType(Agent $agent): string
    {
        if ($agent->isMobile()) return 'mobile';
        if ($agent->isTablet()) return 'tablet';
        return 'desktop';
    }

    /**
     * Device name oluştur
     */
    protected function getDeviceName(Agent $agent): string
    {
        return $agent->platform() . ' - ' . $agent->browser();
    }

    /**
     * İnsan okunabilir zaman
     */
    protected function getHumanTime(int $timestamp): string
    {
        $diff = time() - $timestamp;

        if ($diff < 60) return 'Az önce';
        if ($diff < 3600) return floor($diff / 60) . ' dakika önce';
        if ($diff < 86400) return floor($diff / 3600) . ' saat önce';
        return floor($diff / 86400) . ' gün önce';
    }

    /**
     * Login sonrası device limit kontrolü
     * AuthController'dan çağrılır - registerSession zaten enforceDeviceLimit yapar
     * Bu metod ekstra kontrol için (boş bırakılabilir)
     */
    public function handlePostLoginDeviceLimit(User $user): void
    {
        // registerSession içinde enforceDeviceLimit zaten çağrılıyor
        // Bu metod gerekirse ekstra işlemler için kullanılabilir
        // Şu an için no-op
    }

    /**
     * Session termination'ı logla
     *
     * @param User $user
     * @param object|null $newSession (kick eden session)
     * @param object $oldSession (kick edilen session)
     * @param string $reason (lifo, manual, timeout, logout, admin)
     */
    private function logSessionTermination($user, $newSession, $oldSession, $reason)
    {
        $logData = [
            'reason' => $reason,
            'user_email' => $user->email,
            'kicked_session' => [
                'email' => $user->email,
                'ip' => $oldSession->ip_address ?? 'N/A',
                'device' => $oldSession->device ?? 'N/A',
                'browser' => $oldSession->browser ?? 'N/A',
                'platform' => $oldSession->platform ?? 'N/A',
                'session_opened_at' => $oldSession->created_at ?? 'N/A',
            ],
            'new_session' => $newSession ? [
                'email' => $user->email,
                'ip' => $newSession->ip_address ?? 'N/A',
                'device' => $newSession->device ?? 'N/A',
                'browser' => $newSession->browser ?? 'N/A',
                'platform' => $newSession->platform ?? 'N/A',
                'session_opened_at' => $newSession->created_at ?? 'N/A',
            ] : null,
            'terminated_at' => now()->toDateTimeString(),
        ];

        Log::channel('session-terminations')->info('Session Terminated', $logData);
    }

    /**
     * Tek bir session kaydını (DB + Redis) atomik şekilde sonlandır
     */
    protected function terminateSessionAtomicByRow(object $session, string $reason, ?User $actor = null): bool
    {
        $this->markDeletionReason((int) $session->user_id, $session->login_token ?? null, $reason);
        $this->deleteRedisSession($session->session_id ?? null);

        $deleted = DB::table($this->table)
            ->where('id', $session->id)
            ->delete() > 0;

        if ($deleted) {
            \Log::info('🔐 Session terminated (atomic)', [
                'session_id' => $session->session_id ? substr($session->session_id, 0, 20) . '...' : 'N/A',
                'reason' => $reason,
                'by_user' => $actor?->id,
            ]);
        }

        return $deleted;
    }

    protected function markDeletionReason(int $userId, ?string $loginToken, string $reason): void
    {
        if (!$loginToken) {
            return;
        }

        Cache::put("session_deleted_reason:{$userId}:{$loginToken}", $reason, 300);
    }

    protected function deleteRedisSession(?string $sessionId): void
    {
        if (!$sessionId) {
            return;
        }

        try {
            // Use Laravel session handler for driver-agnostic delete
            app('session')->getHandler()->destroy($sessionId);
        } catch (\Throwable $e) {
            \Log::warning('Session handler destroy failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
        }

        foreach ($this->buildRedisSessionKeys($sessionId) as $key) {
            try {
                Redis::del($key);
            } catch (\Throwable $e) {
                \Log::warning('Redis session delete failed', [
                    'key' => $key,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function buildRedisSessionKeys(string $sessionId): array
    {
        $keys = [];

        if ($sessionId !== '') {
            $prefix = config('session.prefix', 'laravel_session');
            if ($prefix) {
                $keys[] = "{$prefix}:{$sessionId}";
            }

            // Prefixsiz raw key'i de dene (geri uyumluluk)
            $keys[] = $sessionId;
        }

        return array_values(array_unique($keys));
    }
}
