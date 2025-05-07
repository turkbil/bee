<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\Prompt;
use App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Cache;

class PromptService
{
    protected $tenantId;

    /**
     * Constructor
     *
     * @param int|null $tenantId
     */
    public function __construct(?int $tenantId = null)
    {
        $this->tenantId = $tenantId;
    }

    /**
     * Tüm promptları getir
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPrompts()
    {
        // Tenant ID yoksa boş koleksiyon döndür
        if ($this->tenantId === null) {
            return collect();
        }
        
        $cacheKey = "ai_prompts_tenant_{$this->tenantId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return TenantHelpers::central(function () {
                return Prompt::where('tenant_id', $this->tenantId)
                    ->orderBy('is_default', 'desc')
                    ->orderBy('name')
                    ->get();
            });
        });
    }

    /**
     * Varsayılan prompt'u getir
     *
     * @return Prompt|null
     */
    public function getDefaultPrompt(): ?Prompt
    {
        // Tenant ID yoksa null döndür
        if ($this->tenantId === null) {
            return null;
        }
        
        $cacheKey = "ai_default_prompt_tenant_{$this->tenantId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return TenantHelpers::central(function () {
                return Prompt::where('tenant_id', $this->tenantId)
                    ->where('is_default', true)
                    ->first();
            });
        });
    }

    /**
     * Prompt oluştur
     *
     * @param array $data
     * @return Prompt|null
     */
    public function createPrompt(array $data): ?Prompt
    {
        // Tenant ID yoksa null döndür
        if ($this->tenantId === null) {
            return null;
        }
        
        $prompt = TenantHelpers::central(function () use ($data) {
            // Eğer yeni prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
            if (isset($data['is_default']) && $data['is_default']) {
                Prompt::where('tenant_id', $this->tenantId)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
            
            return Prompt::create(array_merge($data, ['tenant_id' => $this->tenantId]));
        });
        
        // Önbelleği temizle
        $this->clearCache();
        
        return $prompt;
    }

    /**
     * Prompt güncelle
     *
     * @param Prompt $prompt
     * @param array $data
     * @return Prompt
     */
    public function updatePrompt(Prompt $prompt, array $data): Prompt
    {
        TenantHelpers::central(function () use ($prompt, $data) {
            // Eğer güncellenecek prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
            if (isset($data['is_default']) && $data['is_default'] && !$prompt->is_default) {
                Prompt::where('tenant_id', $this->tenantId)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
            
            $prompt->update($data);
        });
        
        // Önbelleği temizle
        $this->clearCache();
        
        return $prompt;
    }

    /**
     * Prompt sil
     *
     * @param Prompt $prompt
     * @return bool
     */
    public function deletePrompt(Prompt $prompt): bool
    {
        // Tenant ID yoksa false döndür
        if ($this->tenantId === null) {
            return false;
        }
        
        $result = TenantHelpers::central(function () use ($prompt) {
            // Varsayılan prompt siliniyorsa, işlemi engelle
            if ($prompt->is_default) {
                return false;
            }
            
            return $prompt->delete();
        });
        
        // Önbelleği temizle
        $this->clearCache();
        
        return $result;
    }

    /**
     * Önbelleği temizle
     *
     * @return void
     */
    protected function clearCache(): void
    {
        if ($this->tenantId === null) {
            return;
        }
        
        Cache::forget("ai_prompts_tenant_{$this->tenantId}");
        Cache::forget("ai_default_prompt_tenant_{$this->tenantId}");
    }
}