<?php
namespace Modules\AI\App\Services;

use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PromptService
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Yapılandırma
    }

    /**
     * Tüm promptları getir
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPrompts()
    {
        $cacheKey = "ai_prompts";
        
        try {
            return Cache::remember($cacheKey, now()->addMinutes(30), function () {
                return Prompt::orderBy('is_default', 'desc')
                    ->orderBy('is_common', 'desc')
                    ->orderBy('name')
                    ->get();
            });
        } catch (\Exception $e) {
            Log::error('Promptları getirirken hata: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Varsayılan prompt'u getir
     *
     * @return Prompt|null
     */
    public function getDefaultPrompt(): ?Prompt
    {
        $cacheKey = "ai_default_prompt";
        
        try {
            return Cache::remember($cacheKey, now()->addMinutes(30), function () {
                return Prompt::where('is_default', true)->first();
            });
        } catch (\Exception $e) {
            Log::error('Varsayılan promptu getirirken hata: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ortak özellikler promptunu getir
     *
     * @return Prompt|null
     */
    public function getCommonPrompt(): ?Prompt
    {
        $cacheKey = "ai_common_prompt";
        
        try {
            return Cache::remember($cacheKey, now()->addMinutes(30), function () {
                return Prompt::where('is_common', true)->first();
            });
        } catch (\Exception $e) {
            Log::error('Ortak özellikler promptunu getirirken hata: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Prompt oluştur
     *
     * @param array $data
     * @return bool
     */
    public function createPrompt(array $data): bool
    {
        $success = false;
        
        try {
            // Eğer yeni prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
            if (isset($data['is_default']) && $data['is_default']) {
                Prompt::where('is_default', true)
                    ->update(['is_default' => false]);
            }
            
            // Eğer yeni prompt ortak özellikler olarak işaretlendiyse, diğer ortak özellikleri kaldır
            if (isset($data['is_common']) && $data['is_common']) {
                Prompt::where('is_common', true)
                    ->update(['is_common' => false]);
            }
            
            $prompt = new Prompt();
            $prompt->name = $data['name'];
            $prompt->content = $data['content'];
            $prompt->is_default = $data['is_default'] ?? false;
            $prompt->is_system = $data['is_system'] ?? false;
            $prompt->is_common = $data['is_common'] ?? false;
            $success = $prompt->save();
            
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
        // Sistem promptu kontrolü
        if ($prompt->is_system && !$prompt->is_common) {
            return false;
        }
        
        $success = false;
        
        try {
            // Eğer güncellenecek prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
            if (isset($data['is_default']) && $data['is_default'] && !$prompt->is_default) {
                Prompt::where('is_default', true)
                    ->update(['is_default' => false]);
            }
            
            // Eğer güncellenecek prompt ortak özellikler olarak işaretlendiyse, diğer ortak özellikleri kaldır
            if (isset($data['is_common']) && $data['is_common'] && !$prompt->is_common) {
                Prompt::where('is_common', true)
                    ->update(['is_common' => false]);
            }
            
            $prompt->name = $data['name'];
            $prompt->content = $data['content'];
            $prompt->is_default = $data['is_default'] ?? false;
            $prompt->is_common = $data['is_common'] ?? false;
            $success = $prompt->save();
            
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
        // Sistem promptu, varsayılan prompt veya ortak özellikler kontrolü
        if ($prompt->is_system || $prompt->is_default || $prompt->is_common) {
            return false;
        }
        
        $success = false;
        
        try {
            $success = $prompt->delete();
            
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
        Cache::forget("ai_prompts");
        Cache::forget("ai_default_prompt");
        Cache::forget("ai_common_prompt");
    }
}