<?php

namespace App\Services;

use Spatie\ResponseCache\Hasher\DefaultHasher;
use Illuminate\Http\Request;

class AuthAwareHasher extends DefaultHasher
{
    public function getHashFor(Request $request): string
    {
        $baseHash = parent::getHashFor($request);
        
        // Deterministic hash components
        $components = [
            $baseHash,
            $this->getAuthComponent(),
            $this->getLocaleComponent(),
            $this->getRoleComponent() // Opsiyonel: role-based cache
        ];
        
        // SHA1 ile deterministic hash oluştur
        $finalHash = sha1(implode('|', array_filter($components)));
        
        // Debug log sadece gerektiğinde
        if (app()->environment(['local', 'staging']) && $request->has('debug_cache')) {
            \Log::debug('Cache hash generated', [
                'url' => $request->fullUrl(),
                'components' => $components,
                'final_hash' => $finalHash
            ]);
        }
        
        return $finalHash;
    }
    
    /**
     * Auth component hash'i oluştur
     */
    protected function getAuthComponent(): string
    {
        return auth()->check() ? 'auth_' . auth()->id() : 'guest';
    }
    
    /**
     * Locale component hash'i oluştur
     */
    protected function getLocaleComponent(): string
    {
        $locale = app()->getLocale();
        $tenantLocale = session('tenant_locale', $locale);
        
        return 'locale_' . $tenantLocale;
    }
    
    /**
     * Role component hash'i oluştur (opsiyonel)
     */
    protected function getRoleComponent(): ?string
    {
        if (!auth()->check()) {
            return null;
        }
        
        $user = auth()->user();
        
        // Role-based cache variation gerekiyorsa
        if (config('responsecache.role_based_cache', false)) {
            $roles = $user->getRoleNames()->sort()->toArray();
            return empty($roles) ? 'no_role' : 'roles_' . implode('_', $roles);
        }
        
        return null;
    }
}