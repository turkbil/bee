<?php

namespace App\Services\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Legacy MD5 Password Migration Service
 *
 * ╔══════════════════════════════════════════════════════════════════════════════╗
 * ║  ⚠️  GEÇİCİ SERVİS - Tüm kullanıcılar migrate olunca KALDIRILACAK!          ║
 * ╠══════════════════════════════════════════════════════════════════════════════╣
 * ║  Arama Terimleri: md5 muzibu eski şifreler migration bcrypt tenant-1001     ║
 * ║  Rehber: https://ixtif.com/readme/2026/01/05/md5-muzibu-eski-sifreler-migration/ ║
 * ╠══════════════════════════════════════════════════════════════════════════════╣
 * ║  KALDIRMA ADIMLARI:                                                          ║
 * ║  1. config/auth.php → driver: 'legacy' → 'eloquent' yap                     ║
 * ║  2. AuthServiceProvider.php → Auth::provider('legacy'...) bloğunu sil       ║
 * ║  3. Bu dosyayı sil: app/Services/Auth/LegacyPasswordMigrationService.php    ║
 * ║  4. Provider'ı sil: app/Auth/LegacyUserProvider.php                         ║
 * ║  5. Cache temizle: php artisan config:clear && php artisan cache:clear      ║
 * ╚══════════════════════════════════════════════════════════════════════════════╝
 *
 * @created 2026-01-05
 * @purpose Eski sistemden gelen MD5 hashli şifreleri Laravel bcrypt'e migrate eder
 * @affects Sadece Tenant 1001 (Muzibu) - Diğer tenant'lar ETKİLENMEZ
 */
class LegacyPasswordMigrationService
{
    /**
     * Migration aktif olan tenant'lar
     * Sadece bu tenant'larda MD5 → bcrypt dönüşümü yapılır
     */
    protected array $enabledTenants = [1001]; // Sadece Muzibu

    /**
     * Bu tenant için legacy migration aktif mi?
     */
    public function isEnabled(): bool
    {
        return tenant() && in_array(tenant()->id, $this->enabledTenants);
    }

    /**
     * MD5 hash mi kontrol et
     * MD5 = 32 karakter hexadecimal string
     * bcrypt = 60 karakter, $2y$ ile başlar
     */
    public function isLegacyHash(string $hash): bool
    {
        return strlen($hash) === 32 && ctype_xdigit($hash);
    }

    /**
     * Legacy şifre doğrula ve migrate et
     *
     * @param Authenticatable $user Giriş yapan kullanıcı
     * @param string $plainPassword Kullanıcının girdiği şifre (plain text)
     * @return bool|null true=başarılı giriş, false=yanlış şifre, null=legacy değil (normal auth'a bırak)
     */
    public function validateAndMigrate(Authenticatable $user, string $plainPassword): ?bool
    {
        // Tenant kontrolü - sadece aktif tenant'larda çalış
        if (!$this->isEnabled()) {
            return null;
        }

        $storedHash = $user->getAuthPassword();

        // Legacy hash değilse normal auth'a bırak
        if (!$this->isLegacyHash($storedHash)) {
            return null;
        }

        // MD5 kontrolü
        if (md5($plainPassword) !== $storedHash) {
            return false;
        }

        // ✅ Şifreyi bcrypt'e migrate et
        $user->password = Hash::make($plainPassword);
        $user->save();

        // Log tut (takip için)
        Log::channel('single')->info('Legacy MD5 password migrated to bcrypt', [
            'tenant_id' => tenant()->id,
            'user_id' => $user->getAuthIdentifier(),
            'user_email' => $user->email ?? 'N/A',
        ]);

        return true;
    }

    /**
     * Kaç kullanıcı hala MD5 kullanıyor? (İstatistik)
     *
     * Kullanım: app(LegacyPasswordMigrationService::class)->getRemainingLegacyCount()
     */
    public function getRemainingLegacyCount(): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        return \App\Models\User::whereRaw('LENGTH(password) = 32')->count();
    }

    /**
     * Migration istatistikleri
     *
     * Kullanım: app(LegacyPasswordMigrationService::class)->getStats()
     */
    public function getStats(): array
    {
        if (!$this->isEnabled()) {
            return ['enabled' => false];
        }

        $total = \App\Models\User::count();
        $legacy = \App\Models\User::whereRaw('LENGTH(password) = 32')->count();
        $migrated = \App\Models\User::whereRaw('LENGTH(password) > 32')->count();

        return [
            'enabled' => true,
            'tenant_id' => tenant()->id,
            'total_users' => $total,
            'legacy_md5' => $legacy,
            'migrated_bcrypt' => $migrated,
            'percentage' => $total > 0 ? round(($migrated / $total) * 100, 2) : 0,
            'ready_to_remove' => $legacy === 0,
        ];
    }
}
