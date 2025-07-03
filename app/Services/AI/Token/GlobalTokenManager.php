<?php

namespace App\Services\AI\Token;

use App\Contracts\AI\TokenManagerInterface;
use App\Services\AITokenService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;

class GlobalTokenManager implements TokenManagerInterface
{
    protected AITokenService $aiTokenService;

    public function __construct()
    {
        $this->aiTokenService = new AITokenService();
    }

    public function canUseTokens(string $tenantId, int $tokensNeeded): bool
    {
        try {
            // Tenant'ın token bakiyesini kontrol et
            return $this->aiTokenService->canUseTokens($tokensNeeded);

        } catch (Exception $e) {
            Log::error('Token kontrolü başarısız', [
                'tenant_id' => $tenantId,
                'tokens_needed' => $tokensNeeded,
                'error' => $e->getMessage()
            ]);
            
            return true; // Hata durumunda izin ver
        }
    }

    public function recordTokenUsage(
        string $tenantId, 
        int $tokensUsed, 
        string $usageType = 'general',
        string $moduleContext = null,
        array $metadata = []
    ): bool {
        try {
            // Veritabanına token kullanımını kaydet
            \DB::table('ai_token_usage')->insert([
                'tenant_id' => $tenantId,
                'tokens_used' => $tokensUsed,
                'usage_type' => $usageType,
                'module_context' => $moduleContext,
                'metadata' => json_encode($metadata),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Token kullanımını kaydet
            $this->aiTokenService->recordUsage($tokensUsed, $usageType, $metadata);
            
            Log::info('Token kullanımı kaydedildi', [
                'tenant_id' => $tenantId,
                'tokens_used' => $tokensUsed,
                'usage_type' => $usageType,
                'module_context' => $moduleContext
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Token kullanımı kaydedilemedi', [
                'tenant_id' => $tenantId,
                'tokens_used' => $tokensUsed,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    public function getRemainingTokens(string $tenantId): int
    {
        try {
            // Satın alınan toplam token miktarı
            $totalPurchased = \DB::table('ai_token_purchases')
                ->where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->sum('token_amount');

            // Kullanılan toplam token miktarı
            $totalUsed = \DB::table('ai_token_usage')
                ->where('tenant_id', $tenantId)
                ->sum('tokens_used');

            $remaining = max(0, $totalPurchased - $totalUsed);
            
            // Eğer hiç satın alma yoksa varsayılan limit ver
            return $remaining > 0 ? $remaining : 0;
        } catch (Exception $e) {
            Log::error('Token remaining calculation error', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    public function getDailyUsage(string $tenantId): int
    {
        try {
            return \DB::table('ai_token_usage')
                ->where('tenant_id', $tenantId)
                ->whereDate('created_at', Carbon::today())
                ->sum('tokens_used');
        } catch (Exception $e) {
            Log::error('Daily usage calculation error', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    public function getMonthlyUsage(string $tenantId): int
    {
        try {
            return \DB::table('ai_token_usage')
                ->where('tenant_id', $tenantId)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('tokens_used');
        } catch (Exception $e) {
            Log::error('Monthly usage calculation error', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    public function getUsageHistory(string $tenantId, int $limit = 50): array
    {
        return [];
    }

    public function purchaseTokenPackage(string $tenantId, int $tokenAmount, float $price): bool
    {
        return true;
    }

    public function resetLimits(string $tenantId): bool
    {
        return true;
    }

    public function getMonthlyLimit(string $tenantId): int
    {
        try {
            // Satın alınan toplam token miktarı = limit
            $totalPurchased = \DB::table('ai_token_purchases')
                ->where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->sum('token_amount');

            // Eğer hiç satın alma yoksa 0 döndür
            return $totalPurchased > 0 ? $totalPurchased : 0;
        } catch (Exception $e) {
            Log::error('Monthly limit calculation error', ['error' => $e->getMessage()]);
            return 0;
        }
    }
}