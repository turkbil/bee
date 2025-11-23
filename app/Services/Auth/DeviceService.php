<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeviceService
{
    /**
     * Get active sessions for user
     */
    public function getActiveSessions(User $user): array
    {
        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => date('Y-m-d H:i:s', $session->last_activity),
                    'is_current' => $session->id === session()->getId(),
                ];
            })
            ->toArray();
    }

    /**
     * Check if user has exceeded device limit
     */
    public function checkDeviceLimit(User $user): bool
    {
        $limit = $user->getDeviceLimit();
        $activeCount = $this->getActiveSessionCount($user);

        return $activeCount < $limit;
    }

    /**
     * Get active session count
     */
    public function getActiveSessionCount(User $user): int
    {
        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->count();
    }

    /**
     * Terminate a specific session
     */
    public function terminateSession(string $sessionId): bool
    {
        return DB::table('sessions')
            ->where('id', $sessionId)
            ->delete() > 0;
    }

    /**
     * Terminate oldest session for user
     */
    public function terminateOldestSession(User $user): bool
    {
        $oldest = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'asc')
            ->first();

        if ($oldest) {
            return $this->terminateSession($oldest->id);
        }

        return false;
    }

    /**
     * Terminate all sessions except current
     */
    public function terminateOtherSessions(User $user): int
    {
        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->delete();
    }

    /**
     * Terminate all sessions for user
     */
    public function terminateAllSessions(User $user): int
    {
        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();
    }
}
