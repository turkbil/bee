<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\FormBuilder;

use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Cache;

readonly class PromptMapper
{
    public function __construct(
        private UniversalInputManager $inputManager
    ) {}
    
    /**
     * User input'larını final prompt'a çevir
     */
    public function buildFinalPrompt(int $featureId, array $userInputs): string
    {
        $feature = AIFeature::findOrFail($featureId);
        
        // 1. Quick Prompt (Feature'ın ne yapacağı)
        $promptParts = [];
        
        if ($feature->hasQuickPrompt()) {
            $promptParts[] = "=== GÖREV TANIMI ===\n" . $feature->quick_prompt;
        }
        
        // 2. User input'larından gelen prompt'ları eşleştir
        $selectedPrompts = $this->mapUserInputsToPrompts($userInputs, $featureId);
        
        foreach ($selectedPrompts as $prompt) {
            $promptParts[] = "=== {$prompt->title} ===\n" . $prompt->content;
        }
        
        // 3. Response Template (Çıktı formatı)
        if ($feature->hasResponseTemplate()) {
            $promptParts[] = "=== YANIT FORMATI ===\n" . $feature->getFormattedTemplate();
        }
        
        // 4. User input'ları ekle
        if (!empty($userInputs['primary_input'])) {
            $promptParts[] = "=== KULLANICI GİRDİSİ ===\n" . $userInputs['primary_input'];
        }
        
        return implode("\n\n" . str_repeat("-", 50) . "\n\n", $promptParts);
    }
    
    /**
     * User input'larını prompt'lara eşleştir
     */
    private function mapUserInputsToPrompts(array $userInputs, int $featureId): array
    {
        $promptIds = $this->inputManager->mapInputsToPrompts($userInputs, $featureId);
        
        if (empty($promptIds)) {
            return [];
        }
        
        return Prompt::whereIn('id', $promptIds)
            ->orderBy('priority')
            ->get()
            ->toArray();
    }
    
    /**
     * Prompt önizlemesi oluştur
     */
    public function generatePromptPreview(int $featureId, array $userInputs): array
    {
        $feature = AIFeature::findOrFail($featureId);
        $preview = [];
        
        // Quick prompt
        if ($feature->hasQuickPrompt()) {
            $preview[] = [
                'type' => 'Quick Prompt',
                'content' => substr($feature->quick_prompt, 0, 100) . '...',
                'length' => strlen($feature->quick_prompt)
            ];
        }
        
        // Seçilen prompt'lar
        $selectedPrompts = $this->mapUserInputsToPrompts($userInputs, $featureId);
        
        foreach ($selectedPrompts as $prompt) {
            $preview[] = [
                'type' => $prompt['title'] ?? 'Expert Prompt',
                'content' => substr($prompt['content'], 0, 100) . '...',
                'length' => strlen($prompt['content'])
            ];
        }
        
        // Response template
        if ($feature->hasResponseTemplate()) {
            $template = $feature->getFormattedTemplate();
            $preview[] = [
                'type' => 'Response Template',
                'content' => substr($template, 0, 100) . '...',
                'length' => strlen($template)
            ];
        }
        
        return [
            'parts' => $preview,
            'total_length' => array_sum(array_column($preview, 'length')),
            'estimated_tokens' => $this->estimateTokens($preview)
        ];
    }
    
    /**
     * Token sayısını tahmin et
     */
    private function estimateTokens(array $preview): int
    {
        $totalLength = array_sum(array_column($preview, 'length'));
        
        // Ortalama 4 karakter = 1 token
        return (int) ceil($totalLength / 4);
    }
    
    /**
     * Context-aware prompt seçimi
     */
    public function selectContextAwarePrompts(int $featureId, array $userInputs, array $context = []): array
    {
        $feature = AIFeature::with(['prompts' => function($query) {
            $query->wherePivot('is_active', true)
                  ->orderBy('priority');
        }])->findOrFail($featureId);
        
        $selectedPrompts = [];
        
        foreach ($feature->prompts as $prompt) {
            $conditions = $prompt->pivot->conditions;
            
            if ($this->meetsConditions($conditions, $userInputs, $context)) {
                $selectedPrompts[] = $prompt;
            }
        }
        
        return $selectedPrompts;
    }
    
    /**
     * Şartları kontrol et
     */
    private function meetsConditions($conditions, array $userInputs, array $context): bool
    {
        if (empty($conditions)) {
            return true;
        }
        
        $conditions = is_string($conditions) ? json_decode($conditions, true) : $conditions;
        
        if (!is_array($conditions)) {
            return true;
        }
        
        // Input uzunluğu kontrolü
        if (isset($conditions['input_length'])) {
            $inputLength = strlen($userInputs['primary_input'] ?? '');
            
            if (isset($conditions['input_length']['min']) && 
                $inputLength < $conditions['input_length']['min']) {
                return false;
            }
            
            if (isset($conditions['input_length']['max']) && 
                $inputLength > $conditions['input_length']['max']) {
                return false;
            }
        }
        
        // Content type kontrolü
        if (isset($conditions['content_type']) && isset($context['content_type'])) {
            if (!in_array($context['content_type'], $conditions['content_type'])) {
                return false;
            }
        }
        
        // Language kontrolü
        if (isset($conditions['language']) && isset($context['language'])) {
            if (!in_array($context['language'], $conditions['language'])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Prompt optimizasyonu yap
     */
    public function optimizePrompt(string $prompt): string
    {
        // Gereksiz tekrarları kaldır
        $prompt = $this->removeDuplicateInstructions($prompt);
        
        // Fazla boşlukları temizle
        $prompt = preg_replace('/\n{3,}/', "\n\n", $prompt);
        $prompt = preg_replace('/\s{2,}/', ' ', $prompt);
        
        // Token sayısını azalt
        $prompt = $this->reduceTokenCount($prompt);
        
        return trim($prompt);
    }
    
    /**
     * Tekrarlayan talimatları kaldır
     */
    private function removeDuplicateInstructions(string $prompt): string
    {
        $lines = explode("\n", $prompt);
        $seen = [];
        $cleaned = [];
        
        foreach ($lines as $line) {
            $normalized = strtolower(trim($line));
            
            if (!empty($normalized) && !in_array($normalized, $seen)) {
                $seen[] = $normalized;
                $cleaned[] = $line;
            } elseif (empty($normalized)) {
                $cleaned[] = $line; // Boş satırları koru
            }
        }
        
        return implode("\n", $cleaned);
    }
    
    /**
     * Token sayısını azalt
     */
    private function reduceTokenCount(string $prompt): string
    {
        // Uzun cümleleri kısalt
        $replacements = [
            'lütfen' => '',
            'mümkün olduğunca' => '',
            'eğer mümkünse' => '',
            'dikkat etmelisin ki' => '',
            'şunu unutmamalısın ki' => ''
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $prompt);
    }
    
    /**
     * Prompt'ın gücünü hesapla
     */
    public function calculatePromptPower(int $featureId, array $userInputs): array
    {
        $finalPrompt = $this->buildFinalPrompt($featureId, $userInputs);
        
        return [
            'character_count' => strlen($finalPrompt),
            'estimated_tokens' => $this->estimateTokens([['length' => strlen($finalPrompt)]]),
            'complexity_score' => $this->calculateComplexityScore($finalPrompt),
            'sections_count' => substr_count($finalPrompt, '==='),
            'instruction_density' => $this->calculateInstructionDensity($finalPrompt)
        ];
    }
    
    /**
     * Kompleksite skorunu hesapla
     */
    private function calculateComplexityScore(string $prompt): int
    {
        $score = 0;
        
        // Uzunluk faktörü (0-40 puan)
        $length = strlen($prompt);
        $score += min(40, ($length / 1000) * 10);
        
        // Talimat sayısı (0-30 puan)
        $instructionCount = substr_count(strtolower($prompt), 'sen ') + 
                           substr_count(strtolower($prompt), 'lütfen') +
                           substr_count(strtolower($prompt), 'gerek');
        $score += min(30, $instructionCount * 2);
        
        // Bölüm sayısı (0-30 puan)
        $sectionCount = substr_count($prompt, '===');
        $score += min(30, $sectionCount * 5);
        
        return (int) $score;
    }
    
    /**
     * Talimat yoğunluğunu hesapla
     */
    private function calculateInstructionDensity(string $prompt): float
    {
        $totalWords = str_word_count($prompt);
        $instructionWords = 0;
        
        $instructionKeywords = ['sen', 'lütfen', 'gerek', 'zorunlu', 'mutlaka', 'kesinlikle'];
        
        foreach ($instructionKeywords as $keyword) {
            $instructionWords += substr_count(strtolower($prompt), $keyword);
        }
        
        return $totalWords > 0 ? round(($instructionWords / $totalWords) * 100, 2) : 0;
    }
}