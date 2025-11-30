<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class SetRememberMeCookieDuration
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event): void
    {
        // Remember me işaretlenmemişse hiçbir şey yapma
        if (!$event->remember) {
            return;
        }

        // Settings Management'ten remember me süresini al
        // Varsayılan: 43200 dakika (30 gün = 1 ay)
        $rememberDuration = (int) setting('remember_me_duration', 43200);

        // Guard instance'ını al ($event->guard string olarak guard adını döndürür: 'web')
        $guard = Auth::guard($event->guard);

        // Remember me cookie'sinin adını al
        $recallerName = $guard->getRecallerName();

        // Mevcut cookie'yi al
        $recallerValue = request()->cookies->get($recallerName);

        // Yeni süre ile cookie'yi yeniden oluştur
        if ($recallerValue) {
            Cookie::queue(
                Cookie::make(
                    $recallerName,
                    $recallerValue,
                    $rememberDuration,
                    config('session.path'),
                    config('session.domain'),
                    config('session.secure'),
                    config('session.http_only'),
                    false,
                    config('session.same_site')
                )
            );
        }
    }
}
