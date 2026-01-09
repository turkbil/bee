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
     * Legacy hash mi kontrol et (MD5 veya SHA1)
     * MD5 = 32 karakter hexadecimal string
     * SHA1 = 40 karakter hexadecimal string
     * bcrypt = 60 karakter, $2y$ ile başlar
     */
    public function isLegacyHash(string $hash): bool
    {
        $length = strlen($hash);
        return ($length === 32 || $length === 40) && ctype_xdigit($hash);
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

        // Hash tipine göre kontrol et
        $hashLength = strlen($storedHash);
        $isValid = false;

        if ($hashLength === 32) {
            // MD5 kontrolü
            $isValid = md5($plainPassword) === $storedHash;
        } elseif ($hashLength === 40) {
            // SHA1 kontrolü
            $isValid = sha1($plainPassword) === $storedHash;
        }

        if (!$isValid) {
            return false;
        }

        // ✅ Şifreyi bcrypt'e migrate et
        $user->password = Hash::make($plainPassword);
        $user->save();

        // Log tut (takip için)
        $hashType = strlen($storedHash) === 32 ? 'MD5' : 'SHA1';
        Log::channel('single')->info("Legacy {$hashType} password migrated to bcrypt", [
            'tenant_id' => tenant()->id,
            'user_id' => $user->getAuthIdentifier(),
            'user_email' => $user->email ?? 'N/A',
            'hash_type' => $hashType,
        ]);

        return true;
    }

    /**
     * Kaç kullanıcı hala legacy hash kullanıyor? (İstatistik)
     *
     * Kullanım: app(LegacyPasswordMigrationService::class)->getRemainingLegacyCount()
     */
    public function getRemainingLegacyCount(): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        return \App\Models\User::whereRaw('LENGTH(password) IN (32, 40)')->count();
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
        $md5Count = \App\Models\User::whereRaw('LENGTH(password) = 32')->count();
        $sha1Count = \App\Models\User::whereRaw('LENGTH(password) = 40')->count();
        $legacy = $md5Count + $sha1Count;
        $migrated = \App\Models\User::whereRaw('LENGTH(password) = 60')->count();

        return [
            'enabled' => true,
            'tenant_id' => tenant()->id,
            'total_users' => $total,
            'legacy_md5' => $md5Count,
            'legacy_sha1' => $sha1Count,
            'legacy_total' => $legacy,
            'migrated_bcrypt' => $migrated,
            'percentage' => $total > 0 ? round(($migrated / $total) * 100, 2) : 0,
            'ready_to_remove' => $legacy === 0,
        ];
    }
}
