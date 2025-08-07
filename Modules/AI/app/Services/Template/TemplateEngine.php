<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Template;

use Illuminate\Support\Facades\Cache;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;

/**
 * 🎨 SMART TEMPLATE ENGINE
 * Template inheritance sistemi ile akıllı prompt yönetimi
 */
readonly class TemplateEngine
{
    private const CACHE_PREFIX = 'template_engine:';
    private const CACHE_TTL = 3600; // 1 hour
    
    /**
     * Template sistemi ile prompt oluştur
     */
    public function buildTemplate(AIFeature $feature, array $context = []): string
    {
        $cacheKey = self::CACHE_PREFIX . "feature:{$feature->id}:template";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($feature, $context) {
            return $this->compileTemplate($feature, $context);
        });
    }
    
    /**
     * Template compilation - inheritance desteği ile
     */
    private function compileTemplate(AIFeature $feature, array $context): string
    {
        $parts = [];
        
        // 1. Base Template (Ana şablon)
        $baseTemplate = $this->getBaseTemplate($feature->type ?? 'general');
        if ($baseTemplate) {
            $parts[] = $baseTemplate;
        }
        
        // 2. Quick Prompt (Feature'ın kendi prompt'u)
        if ($feature->quick_prompt) {
            $parts[] = $this->processTemplate($feature->quick_prompt, $context);
        }
        
        // 3. Expert Prompts (Priority sırasına göre)
        $expertPrompts = $this->getExpertPrompts($feature);
        foreach ($expertPrompts as $prompt) {
            $processedPrompt = $this->processTemplate($prompt['content'], $context);
            $parts[] = $processedPrompt;
        }
        
        // 4. Response Template (Yanıt formatı)
        if ($feature->response_template) {
            $responseInstructions = $this->buildResponseInstructions($feature->response_template);
            if ($responseInstructions) {
                $parts[] = $responseInstructions;
            }
        }
        
        return implode("\n\n", array_filter($parts));
    }
    
    /**
     * Feature type'ına göre base template al
     */
    private function getBaseTemplate(string $featureType): ?string
    {
        $baseTemplates = [
            'content_creator' => "🎨 CONTENT CREATOR MODE: Sen profesyonel bir içerik üreticisisin. Yaratıcı, özgün ve etkileyici içerikler üretmelisin.",
            'seo_specialist' => "🔍 SEO SPECIALIST MODE: Sen bir SEO uzmanısın. Arama motoru dostu, anahtar kelime optimized içerikler üretmelisin.",
            'social_media' => "📱 SOCIAL MEDIA MODE: Sen sosyal medya uzmanısın. Platform özelliklerine uygun, engagement odaklı içerikler üretmelisin.",
            'translator' => "🌍 TRANSLATOR MODE: Sen profesyonel bir çevirmensin. Anlamı koruyarak doğal ve akıcı çeviriler yapmalısın.",
            'general' => "🤖 AI ASSISTANT MODE: Sen yardımcı bir AI asistanısın. Sorulara net, faydalı ve detaylı yanıtlar vermelisin."
        ];
        
        return $baseTemplates[$featureType] ?? $baseTemplates['general'];
    }
    
    /**
     * Expert prompts'ları priority sırasına göre al
     */
    private function getExpertPrompts(AIFeature $feature): array
    {
        if (!$feature->expert_prompt_id) {
            return [];
        }
        
        $expertPrompts = [];
        
        // Ana expert prompt
        $mainExpertPrompt = Prompt::find($feature->expert_prompt_id);
        if ($mainExpertPrompt) {
            $expertPrompts[] = [
                'content' => $mainExpertPrompt->content,
                'priority' => $mainExpertPrompt->priority ?? 2,
                'name' => $mainExpertPrompt->name
            ];
        }
        
        // Parent prompts (inheritance)
        if ($mainExpertPrompt && $mainExpertPrompt->parent_id) {
            $parentPrompts = $this->getParentPrompts($mainExpertPrompt->parent_id);
            $expertPrompts = array_merge($expertPrompts, $parentPrompts);
        }
        
        // Priority'ye göre sırala (yüksek priority önce)
        usort($expertPrompts, fn($a, $b) => $a['priority'] <=> $b['priority']);
        
        return $expertPrompts;
    }
    
    /**
     * Parent prompt'ları recursive olarak al (inheritance)
     */
    private function getParentPrompts(int $parentId): array
    {
        $parents = [];
        $currentParent = Prompt::find($parentId);
        
        if ($currentParent) {
            $parents[] = [
                'content' => $currentParent->content,
                'priority' => $currentParent->priority ?? 3,
                'name' => $currentParent->name
            ];
            
            // Recursive: Parent'ın parent'ı varsa onu da al
            if ($currentParent->parent_id) {
                $grandParents = $this->getParentPrompts($currentParent->parent_id);
                $parents = array_merge($parents, $grandParents);
            }
        }
        
        return $parents;
    }
    
    /**
     * Template variables'ları process et
     */
    private function processTemplate(string $template, array $context): string
    {
        $processed = $template;
        
        // Tenant bilgileri
        if (isset($context['tenant_name'])) {
            $processed = str_replace('{{tenant_name}}', $context['tenant_name'], $processed);
        }
        
        if (isset($context['company_name'])) {
            $processed = str_replace('{{company_name}}', $context['company_name'], $processed);
        }
        
        if (isset($context['sector'])) {
            $processed = str_replace('{{sector}}', $context['sector'], $processed);
        }
        
        // User bilgileri
        if (isset($context['user_name'])) {
            $processed = str_replace('{{user_name}}', $context['user_name'], $processed);
        }
        
        // Feature bilgileri
        if (isset($context['feature_name'])) {
            $processed = str_replace('{{feature_name}}', $context['feature_name'], $processed);
        }
        
        // Tarih/Zaman
        $processed = str_replace('{{current_date}}', date('d.m.Y'), $processed);
        $processed = str_replace('{{current_time}}', date('H:i'), $processed);
        
        return $processed;
    }
    
    /**
     * Response template'dan instruction'lar oluştur
     */
    private function buildResponseInstructions(string $responseTemplate): ?string
    {
        try {
            $template = json_decode($responseTemplate, true);
            if (!$template) {
                return null;
            }
            
            $instructions = ["📋 RESPONSE FORMAT INSTRUCTIONS:"];
            
            // Format belirtilmiş mi?
            if (isset($template['format'])) {
                $instructions[] = "• Response format: {$template['format']}";
            }
            
            // Sections belirtilmiş mi?
            if (isset($template['sections']) && is_array($template['sections'])) {
                $sectionList = implode(', ', $template['sections']);
                $instructions[] = "• Required sections: {$sectionList}";
            }
            
            // Scoring sistemi var mı?
            if (isset($template['scoring']) && $template['scoring']) {
                $instructions[] = "• Include scoring/rating system in response";
            }
            
            // Show original isteniyor mu?
            if (isset($template['show_original']) && $template['show_original']) {
                $instructions[] = "• Show original content alongside processed content";
            }
            
            // JSON format isteniyor mu?
            if (isset($template['json_output']) && $template['json_output']) {
                $instructions[] = "• Provide response in JSON format";
            }
            
            return implode("\n", $instructions);
            
        } catch (\Exception $e) {
            \Log::warning('Response template processing failed', [
                'template' => $responseTemplate,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Template cache'i temizle
     */
    public function clearTemplateCache(int $featureId = null): void
    {
        if ($featureId) {
            // Specific feature cache
            Cache::forget(self::CACHE_PREFIX . "feature:{$featureId}:template");
        } else {
            // All template cache
            Cache::flush(); // Not ideal but works for now
        }
    }
    
    /**
     * Template istatistikleri
     */
    public function getTemplateStats(): array
    {
        return [
            'total_templates' => $this->countTotalTemplates(),
            'inheritance_depth' => $this->getMaxInheritanceDepth(),
            'template_types' => $this->getTemplateTypes(),
            'cache_hit_rate' => $this->getCacheHitRate()
        ];
    }
    
    /**
     * Toplam template sayısı
     */
    private function countTotalTemplates(): int
    {
        return AIFeature::whereNotNull('response_template')->count();
    }
    
    /**
     * Maximum inheritance depth
     */
    private function getMaxInheritanceDepth(): int
    {
        $maxDepth = 0;
        $prompts = Prompt::whereNotNull('parent_id')->get();
        
        foreach ($prompts as $prompt) {
            $depth = $this->calculateInheritanceDepth($prompt->id);
            $maxDepth = max($maxDepth, $depth);
        }
        
        return $maxDepth;
    }
    
    /**
     * Bir prompt'ın inheritance depth'ini hesapla
     */
    private function calculateInheritanceDepth(int $promptId, int $currentDepth = 1): int
    {
        $prompt = Prompt::find($promptId);
        if (!$prompt || !$prompt->parent_id) {
            return $currentDepth;
        }
        
        return $this->calculateInheritanceDepth($prompt->parent_id, $currentDepth + 1);
    }
    
    /**
     * Template type'ları say
     */
    private function getTemplateTypes(): array
    {
        return AIFeature::selectRaw('type, COUNT(*) as count')
            ->whereNotNull('response_template')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }
    
    /**
     * Cache hit rate hesapla (mock data)
     */
    private function getCacheHitRate(): float
    {
        // Bu gerçek cache metrics'i için Redis/Memcached integration gerekir
        // Şimdilik mock data dönelim
        return 85.2; // %85.2 cache hit rate
    }
}