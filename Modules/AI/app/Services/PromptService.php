<?php
namespace Modules\AI\App\Services;

use Modules\AI\App\Models\Prompt;
use App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
     * @return bool
     */
    public function createPrompt(array $data): bool
    {
        // Tenant ID yoksa false döndür
        if ($this->tenantId === null) {
            return false;
        }
        
        $success = false;
        
        try {
            TenantHelpers::central(function () use ($data, &$success) {
                // Eğer yeni prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
                if (isset($data['is_default']) && $data['is_default']) {
                    Prompt::where('tenant_id', $this->tenantId)
                        ->where('is_default', true)
                        ->update(['is_default' => false]);
                }
                
                $prompt = new Prompt();
                $prompt->tenant_id = $this->tenantId;
                $prompt->name = $data['name'];
                $prompt->content = $data['content'];
                $prompt->is_default = $data['is_default'] ?? false;
                $success = $prompt->save();
                
                return $success;
            });
            
            // Önbelleği temizle
            if ($success) {
                $this->clearCache();
            }
            
            return $success;
        } catch (\Exception $e) {
            Log::error('Prompt eklenirken hata oluştu: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Prompt güncelle
     *
     * @param Prompt $prompt
     * @param array $data
     * @return bool
     */
    public function updatePrompt(Prompt $prompt, array $data): bool
    {
        if ($this->tenantId === null || $prompt->tenant_id != $this->tenantId) {
            return false;
        }
        
        $success = false;
        
        try {
            TenantHelpers::central(function () use ($prompt, $data, &$success) {
                // Eğer güncellenecek prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
                if (isset($data['is_default']) && $data['is_default'] && !$prompt->is_default) {
                    Prompt::where('tenant_id', $this->tenantId)
                        ->where('is_default', true)
                        ->update(['is_default' => false]);
                }
                
                $prompt->name = $data['name'];
                $prompt->content = $data['content'];
                $prompt->is_default = $data['is_default'] ?? false;
                $success = $prompt->save();
                
                return $success;
            });
            
            // Önbelleği temizle
            if ($success) {
                $this->clearCache();
            }
            
            return $success;
        } catch (\Exception $e) {
            Log::error('Prompt güncellenirken hata oluştu: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Prompt sil
     *
     * @param Prompt $prompt
     * @return bool
     */
    public function deletePrompt(Prompt $prompt): bool
    {
        // Tenant ID yoksa veya prompt bu tenant'a ait değilse false döndür
        if ($this->tenantId === null || $prompt->tenant_id != $this->tenantId) {
            return false;
        }
        
        $success = false;
        
        try {
            TenantHelpers::central(function () use ($prompt, &$success) {
                // Varsayılan prompt siliniyorsa, işlemi engelle
                if ($prompt->is_default) {
                    return false;
                }
                
                $success = $prompt->delete();
                return $success;
            });
            
            // Önbelleği temizle
            if ($success) {
                $this->clearCache();
            }
            
            return $success;
        } catch (\Exception $e) {
            Log::error('Prompt silinirken hata oluştu: ' . $e->getMessage());
            return false;
        }
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