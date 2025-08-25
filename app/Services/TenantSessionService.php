<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Stancl\Tenancy\Contracts\Tenant;

class TenantSessionService
{
    /**
     * Session key prefix sabitleri
     */
    public const PREFIX_TENANT = 'tenant';
    public const PREFIX_USER = 'user';
    public const PREFIX_LANGUAGE = 'lang';
    public const PREFIX_THEME = 'theme';
    public const PREFIX_MODULE = 'module';
    public const PREFIX_TEMP = 'temp';
    public const PREFIX_FLASH = 'flash';
    public const PREFIX_FORM = 'form';
    public const PREFIX_CART = 'cart';
    public const PREFIX_FILTER = 'filter';

    protected ?Tenant $tenant;
    protected string $tenantId;
    protected string $sessionDriver;

    public function __construct()
    {
        $this->tenant = tenant();
        $this->tenantId = $this->tenant ? $this->tenant->id : 'central';
        $this->sessionDriver = config('session.driver');
    }

    /**
     * Tenant için session yapılandırması
     */
    public function configureTenantSession(): void
    {
        $domain = Request::getHost();
        
        Config::set([
            // Cookie adı tenant'a özel
            'session.cookie' => 'laravel_session_' . $this->tenantId,
            'session.domain' => $domain,
            'session.path' => '/',
            'session.secure' => Request::isSecure(),
            'session.same_site' => 'lax',
            'session.lifetime' => 120, // 2 saat
            'session.expire_on_close' => false
        ]);

        // Driver'a göre özel ayarlar
        $this->configureByDriver();
    }

    /**
     * Driver'a göre session yapılandırması
     */
    protected function configureByDriver(): void
    {
        switch ($this->sessionDriver) {
            case 'database':
                $this->configureDatabaseSession();
                break;
            case 'redis':
                $this->configureRedisSession();
                break;
            case 'file':
                $this->configureFileSession();
                break;
        }
    }

    /**
     * Database session yapılandırması
     */
    protected function configureDatabaseSession(): void
    {
        Config::set([
            'session.connection' => 'tenant',
            'session.table' => 'sessions'
        ]);
    }

    /**
     * Redis session yapılandırması
     */
    protected function configureRedisSession(): void
    {
        $prefix = self::PREFIX_TENANT . ':' . $this->tenantId . ':' . self::PREFIX_TENANT . ':session:';
        
        Config::set([
            'session.connection' => 'default',
            'database.redis.options.prefix' => $prefix
        ]);
    }

    /**
     * File session yapılandırması
     */
    protected function configureFileSession(): void
    {
        $sessionPath = storage_path('framework/sessions/' . self::PREFIX_TENANT . '_' . $this->tenantId);
        
        // Dizin yoksa oluştur
        if (!File::exists($sessionPath)) {
            File::makeDirectory($sessionPath, 0755, true);
        }
        
        Config::set('session.files', $sessionPath);
    }

    /**
     * Session key oluştur
     */
    public function key(string $prefix, string $key): string
    {
        return implode(':', [
            self::PREFIX_TENANT,
            $this->tenantId,
            $prefix,
            $key
        ]);
    }

    /**
     * Session'a veri kaydet
     */
    public function put(string $prefix, string $key, $value): void
    {
        $sessionKey = $this->key($prefix, $key);
        Session::put($sessionKey, $value);
    }

    /**
     * Session'dan veri al
     */
    public function get(string $prefix, string $key, $default = null)
    {
        $sessionKey = $this->key($prefix, $key);
        return Session::get($sessionKey, $default);
    }

    /**
     * Session'dan veri sil
     */
    public function forget(string $prefix, string $key): void
    {
        $sessionKey = $this->key($prefix, $key);
        Session::forget($sessionKey);
    }

    /**
     * Session'da var mı kontrol et
     */
    public function has(string $prefix, string $key): bool
    {
        $sessionKey = $this->key($prefix, $key);
        return Session::has($sessionKey);
    }

    /**
     * Flash message kaydet
     */
    public function flash(string $type, string $message): void
    {
        $this->put(self::PREFIX_FLASH, $type, $message);
    }

    /**
     * Kullanıcı session helper
     */
    public function userSession(string $key, $value = null)
    {
        if ($value === null) {
            return $this->get(self::PREFIX_USER, $key);
        }
        
        $this->put(self::PREFIX_USER, $key, $value);
    }

    /**
     * Dil session helper
     */
    public function languageSession(string $key, $value = null)
    {
        if ($value === null) {
            return $this->get(self::PREFIX_LANGUAGE, $key);
        }
        
        $this->put(self::PREFIX_LANGUAGE, $key, $value);
    }

    /**
     * Modül session helper
     */
    public function moduleSession(string $module, string $key, $value = null)
    {
        $fullKey = $module . ':' . $key;
        
        if ($value === null) {
            return $this->get(self::PREFIX_MODULE, $fullKey);
        }
        
        $this->put(self::PREFIX_MODULE, $fullKey, $value);
    }

    /**
     * Form session helper (form verilerini geçici saklamak için)
     */
    public function formSession(string $formId, ?array $data = null)
    {
        if ($data === null) {
            return $this->get(self::PREFIX_FORM, $formId, []);
        }
        
        $this->put(self::PREFIX_FORM, $formId, $data);
    }

    /**
     * Cart session helper (e-ticaret için)
     */
    public function cartSession(?array $items = null)
    {
        if ($items === null) {
            return $this->get(self::PREFIX_CART, 'items', []);
        }
        
        $this->put(self::PREFIX_CART, 'items', $items);
    }

    /**
     * Filter session helper (listeleme filtreleri için)
     */
    public function filterSession(string $page, ?array $filters = null)
    {
        if ($filters === null) {
            return $this->get(self::PREFIX_FILTER, $page, []);
        }
        
        $this->put(self::PREFIX_FILTER, $page, $filters);
    }

    /**
     * Tenant session temizle
     */
    public function flushTenant(): void
    {
        $pattern = self::PREFIX_TENANT . ':' . $this->tenantId . ':*';
        
        // Session'daki tenant verilerini temizle
        $allSessionData = Session::all();
        foreach ($allSessionData as $key => $value) {
            if (str_starts_with($key, self::PREFIX_TENANT . ':' . $this->tenantId)) {
                Session::forget($key);
            }
        }
    }

    /**
     * Belirli prefix'e göre session temizle
     */
    public function flushByPrefix(string $prefix): void
    {
        $pattern = $this->key($prefix, '*');
        
        $allSessionData = Session::all();
        foreach ($allSessionData as $key => $value) {
            if (str_starts_with($key, self::PREFIX_TENANT . ':' . $this->tenantId . ':' . $prefix)) {
                Session::forget($key);
            }
        }
    }

    /**
     * Session istatistikleri
     */
    public function getStats(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'session_driver' => $this->sessionDriver,
            'session_cookie' => config('session.cookie'),
            'session_lifetime' => config('session.lifetime') . ' minutes',
            'session_domain' => config('session.domain'),
            'prefixes' => [
                'user' => self::PREFIX_TENANT . ':' . $this->tenantId . ':' . self::PREFIX_USER,
                'language' => self::PREFIX_TENANT . ':' . $this->tenantId . ':' . self::PREFIX_LANGUAGE,
                'module' => self::PREFIX_TENANT . ':' . $this->tenantId . ':' . self::PREFIX_MODULE,
                'form' => self::PREFIX_TENANT . ':' . $this->tenantId . ':' . self::PREFIX_FORM,
            ]
        ];
    }

    /**
     * Statik instance (helper için)
     */
    public static function instance(): self
    {
        return app(self::class);
    }
}