<?php

namespace App\Auth;

use App\Services\Auth\LegacyPasswordMigrationService;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

/**
 * Legacy User Provider
 *
 * ╔══════════════════════════════════════════════════════════════════════════════╗
 * ║  ⚠️  GEÇİCİ PROVIDER - Tüm kullanıcılar migrate olunca KALDIRILACAK!        ║
 * ╠══════════════════════════════════════════════════════════════════════════════╣
 * ║  Arama Terimleri: md5 muzibu eski şifreler migration bcrypt tenant-1001     ║
 * ║  Rehber: https://ixtif.com/readme/2026/01/05/md5-muzibu-eski-sifreler-migration/ ║
 * ╠══════════════════════════════════════════════════════════════════════════════╣
 * ║  KALDIRMA ADIMLARI:                                                          ║
 * ║  1. config/auth.php → driver: 'legacy' → 'eloquent' yap                     ║
 * ║  2. AuthServiceProvider.php → Auth::provider('legacy'...) bloğunu sil       ║
 * ║  3. Service'i sil: app/Services/Auth/LegacyPasswordMigrationService.php     ║
 * ║  4. Bu dosyayı sil: app/Auth/LegacyUserProvider.php                         ║
 * ║  5. Cache temizle: php artisan config:clear && php artisan cache:clear      ║
 * ╚══════════════════════════════════════════════════════════════════════════════╝
 *
 * @created 2026-01-05
 * @purpose MD5 hashli şifreleri kontrol eder ve bcrypt'e migrate eder
 * @affects Sadece Tenant 1001 (Muzibu) - Diğer tenant'lar ETKİLENMEZ
 */
class LegacyUserProvider extends EloquentUserProvider
{
    /**
     * Kullanıcı şifresini doğrula
     *
     * Akış:
     * 1. LegacyPasswordMigrationService'i çağır
     * 2. Eğer legacy hash (MD5) ise → validate et, başarılıysa bcrypt'e migrate et
     * 3. Eğer legacy değilse → Normal Laravel auth kullan
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plainPassword = $credentials['password'];

        // Legacy migration servisini çağır
        $migrationService = app(LegacyPasswordMigrationService::class);
        $result = $migrationService->validateAndMigrate($user, $plainPassword);

        // null değilse sonucu döndür (true = giriş başarılı, false = yanlış şifre)
        if ($result !== null) {
            return $result;
        }

        // Legacy değil → Normal Laravel auth (bcrypt/argon2 kontrolü)
        return Hash::check($plainPassword, $user->getAuthPassword());
    }
}
