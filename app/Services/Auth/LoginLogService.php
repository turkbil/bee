<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LoginLogService
{
    /**
     * Log successful login
     */
    public function logSuccess(User $user, Request $request): void
    {
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'action' => 'login_success',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'location' => $this->getLocation($request->ip()),
            ])
            ->log('Giriş yapıldı');
    }

    /**
     * Log failed login attempt
     */
    public function logFailure(string $email, string $reason, Request $request): void
    {
        activity()
            ->withProperties([
                'action' => 'login_failed',
                'email' => $email,
                'reason' => $reason,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Giriş başarısız: ' . $reason);
    }

    /**
     * Log logout
     */
    public function logLogout(User $user, Request $request): void
    {
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'action' => 'logout',
                'ip' => $request->ip(),
            ])
            ->log('Çıkış yapıldı');
    }

    /**
     * Get login history for user
     */
    public function getHistory(User $user, int $limit = 20): array
    {
        return Activity::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->whereIn('properties->action', ['login_success', 'logout', 'login_failed'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($activity) {
                return [
                    'action' => $activity->properties['action'] ?? 'unknown',
                    'ip' => $activity->properties['ip'] ?? null,
                    'user_agent' => $activity->properties['user_agent'] ?? null,
                    'location' => $activity->properties['location'] ?? null,
                    'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    /**
     * Get location from IP (basic implementation)
     */
    protected function getLocation(string $ip): ?string
    {
        // For now, just return null
        // Can be enhanced with IP geolocation service
        return null;
    }
}
