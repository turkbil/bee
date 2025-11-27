<?php

namespace Modules\Muzibu\App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class DeviceService
{
    /**
     * Check if this service should run (Tenant 1001 only)
     */
    protected function shouldRun(): bool
    {
        $tenant = tenant();
        return $tenant && $tenant->id == 1001;
    }

    /**
     * Get active sessions for user
     */
    public function getActiveDevices(User $user): array
    {
        if (!$this->shouldRun()) {
            return [];
        }

        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $agent = new Agent();
                $agent->setUserAgent($session->user_agent);

                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'device_type' => $this->getDeviceType($agent),
                    'device_name' => $this->getDeviceName($agent),
                    'browser' => $agent->browser(),
                    'platform' => $agent->platform(),
                    'last_activity' => date('Y-m-d H:i:s', $session->last_activity),
                    'last_activity_human' => $this->getHumanTime($session->last_activity),
                    'is_current' => $session->id === session()->getId(),
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

        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->count();
    }

    /**
     * Check if user has exceeded device limit
     */
    public function checkDeviceLimit(User $user): bool
    {
        if (!$this->shouldRun()) {
            return true; // Diğer tenant'lar etkilenmez
        }

        $limit = $this->getDeviceLimit($user);
        $activeCount = $this->getActiveDeviceCount($user);

        return $activeCount < $limit;
    }

    /**
     * Get device limit for user
     */
    public function getDeviceLimit(User $user): int
    {
        if (!$this->shouldRun()) {
            return 999; // Unlimited for other tenants
        }

        // TODO: Subscription plan entegrasyonu (gelecekte)
        // $subscription = $user->subscription;
        // if ($subscription && $subscription->plan) {
        //     return $subscription->plan->device_limit ?: 1;
        // }

        // User'ın kendi device_limit'i (null veya 0 ise 1 döndür)
        return $user->device_limit ?: 1;
    }

    /**
     * Terminate a specific session
     */
    public function terminateSession(string $sessionId, User $actor = null, ?User $targetUser = null): bool
    {
        if (!$this->shouldRun()) {
            return false;
        }

        $deleted = DB::table('sessions')
            ->where('id', $sessionId)
            ->delete() > 0;

        if ($deleted && $actor) {
            $this->logActivity($actor, 'device_force_logout', [
                'session_id' => $sessionId,
                'target_user_id' => $targetUser?->id,
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

        $oldest = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'asc')
            ->first();

        if ($oldest) {
            return $this->terminateSession($oldest->id, $user);
        }

        return false;
    }

    /**
     * Handle device limit after login (terminate oldest sessions if limit exceeded)
     * Login SONRASI çağrılmalı - yeni session oluşturulduktan SONRA
     */
    public function handlePostLoginDeviceLimit(User $user): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $limit = $this->getDeviceLimit($user);
        $activeCount = $this->getActiveDeviceCount($user);

        // Limit aşıldıysa, eski session'ları temizle
        while ($activeCount > $limit) {
            // En eski session'ı terminate et (CURRENT session hariç)
            $oldest = DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', session()->getId()) // Mevcut session'ı koruma
                ->orderBy('last_activity', 'asc')
                ->first();

            if ($oldest) {
                $this->terminateSession($oldest->id, $user);
                $activeCount--;

                \Log::info('🔐 POST-LOGIN: Eski session terminate edildi', [
                    'user_id' => $user->id,
                    'terminated_session' => $oldest->id,
                    'current_session' => session()->getId(),
                    'remaining_sessions' => $activeCount,
                    'limit' => $limit,
                ]);
            } else {
                break; // Daha session yoksa dur
            }
        }
    }

    /**
     * Terminate all sessions except current
     */
    public function terminateOtherSessions(User $user): int
    {
        if (!$this->shouldRun()) {
            return 0;
        }

        $count = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
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

        $count = DB::table('sessions')
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
     * Log user activity using Spatie Activity Log
     */
    public function logActivity(User $user, string $action, array $details = []): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $agent = new Agent();
        $messages = [
            'device_logout' => 'Cihazdan çıkış yapıldı',
            'device_force_logout' => 'Başka cihaz zorla kapatıldı',
            'device_logout_all' => 'Tüm cihazlardan çıkış yapıldı',
            'login' => 'Giriş yapıldı',
            'logout' => 'Çıkış yapıldı',
        ];

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
                'target_user_id' => $details['target_user_id'] ?? null,
                'details' => $details,
            ])
            ->log($messages[$action] ?? $action);
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
            return 'Az önce';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' dakika önce';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' saat önce';
        } else {
            $days = floor($diff / 86400);
            return $days . ' gün önce';
        }
    }
}
