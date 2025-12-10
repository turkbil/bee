<?php

namespace Modules\Muzibu\App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
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

        // 🔥 LOGIN TOKEN: Her login için benzersiz token oluştur
        // Bu token session ID değişse bile aynı kalır
        $loginToken = bin2hex(random_bytes(32)); // 64 char hex

        // Önce bu session'ı sil (varsa) - temiz kayıt için
        DB::table($this->table)->where('session_id', $sessionId)->delete();

        // Yeni session kaydet
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

        // 🔥 LOGIN TOKEN: Cookie'ye kaydet (7 gün, HttpOnly)
        $cookieName = 'mzb_login_token';
        $cookieMinutes = 60 * 24 * 7; // 7 gün
        cookie()->queue(cookie($cookieName, $loginToken, $cookieMinutes, '/', null, true, true, false, 'Lax'));

        \Log::info('🔐 Session registered with login_token', [
            'user_id' => $user->id,
            'session_id' => substr($sessionId, 0, 20) . '...',
            'login_token' => substr($loginToken, 0, 16) . '...',
        ]);

        // LIFO: Limit aşıldıysa eski session'ları sil
        $this->enforceDeviceLimit($user, $sessionId);
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
            DB::table($this->table)
                ->where('session_id', $sessionId)
                ->delete();
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
            return true; // Sistem kapalıysa her zaman valid
        }

        // 1. Cookie'den login_token al
        $cookieToken = request()->cookie('mzb_login_token');

        \Log::debug('🔐 sessionExists: Checking with login_token', [
            'user_id' => $user->id,
            'cookie_token' => $cookieToken ? substr($cookieToken, 0, 16) . '...' : 'NULL',
        ]);

        // Cookie'de token yoksa → Session ID ile fallback
        if (!$cookieToken) {
            return $this->sessionExistsBySessionId($user);
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
     */
    protected function sessionExistsBySessionId(User $user): bool
    {
        $currentSessionId = $this->getCurrentSessionId();

        if (!$currentSessionId) {
            // Session ID alamıyoruz - kullanıcının session'ı var mı?
            $hasSession = DB::table($this->table)
                ->where('user_id', $user->id)
                ->exists();

            return $hasSession;
        }

        // Session ID ile kontrol
        $exists = DB::table($this->table)
            ->where('user_id', $user->id)
            ->where('session_id', $currentSessionId)
            ->exists();

        if ($exists) {
            return true;
        }

        // Tek session varsa sync yap (Livewire regenerate)
        $sessions = DB::table($this->table)
            ->where('user_id', $user->id)
            ->get();

        if ($sessions->count() === 1) {
            $session = $sessions->first();
            DB::table($this->table)
                ->where('id', $session->id)
                ->update([
                    'session_id' => $currentSessionId,
                    'last_activity' => now(),
                    'updated_at' => now(),
                ]);
            return true;
        }

        return false;
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

        // Sadece 60 dakikadan eski session'ları temizle (stale cleanup)
        DB::table($this->table)
            ->where('user_id', $user->id)
            ->where('last_activity', '<', now()->subMinutes(60))
            ->delete();

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

            // En eski session'ları sil (mevcut hariç)
            $oldSessions = $sessions
                ->filter(fn($s) => $s->session_id !== $currentSessionId)
                ->take($toRemove);

            foreach ($oldSessions as $old) {
                DB::table($this->table)
                    ->where('id', $old->id)
                    ->delete();

                \Log::info('🔐 LIFO: Old session removed', [
                    'user_id' => $user->id,
                    'session_id' => substr($old->session_id, 0, 20) . '...',
                ]);
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

        $deleted = DB::table($this->table)
            ->where('session_id', $sessionId)
            ->delete() > 0;

        if ($deleted) {
            \Log::info('🔐 Session terminated', [
                'session_id' => substr($sessionId, 0, 20) . '...',
                'by_user' => $actor ? $actor->id : null,
            ]);
        }

        return $deleted;
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

        return DB::table($this->table)
            ->where('user_id', $user->id)
            ->where('session_id', '!=', $currentSessionId)
            ->delete();
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
}
