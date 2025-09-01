<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;

class EnhancedJavaScriptProtector
{
    private $attributePlaceholders = [];
    private $expressionPlaceholders = [];
    private $attributeCounter = 0;
    private $expressionCounter = 0;

    /**
     * 🚨 ENHANCED JAVASCRIPT PROTECTION SYSTEM V2.0
     * Phase 1: Protect JavaScript attributes
     * Phase 2: Protect JavaScript expressions within content
     */
    public function protectJavaScript(string $html): string
    {
        $htmlWithPlaceholders = $html;

        // PHASE 1: Protect JavaScript attributes
        $htmlWithPlaceholders = $this->protectJavaScriptAttributes($htmlWithPlaceholders);

        // PHASE 2: Protect JavaScript expressions in content
        $htmlWithPlaceholders = $this->protectJavaScriptExpressions($htmlWithPlaceholders);

        Log::info('🛡️ Enhanced JavaScript protection applied', [
            'js_attributes_found' => count($this->attributePlaceholders),
            'js_expressions_found' => count($this->expressionPlaceholders),
            'total_protections' => count($this->attributePlaceholders) + count($this->expressionPlaceholders)
        ]);

        return $htmlWithPlaceholders;
    }

    /**
     * Phase 1: Protect complete JavaScript attributes
     */
    private function protectJavaScriptAttributes(string $html): string
    {
        // Protect x-data, x-init, x-show, @click, :class etc.
        $jsAttributePatterns = [
            '/\sx-[a-z\-]+="[^"]*"/i',     // x-data, x-init, x-show etc.
            '/\s:[a-z\-]+="[^"]*"/i',      // :class, :style etc.
            '/\s@[a-z\-]+="[^"]*"/i',      // @click, @submit etc.
            '/\sv-[a-z\-]+="[^"]*"/i',     // Vue.js directives
            '/\s\(click\)="[^"]*"/i',      // Angular style
            '/\s\[ngIf\]="[^"]*"/i',       // Angular directives
            '/\sonclick="[^"]*"/i',        // onclick, onchange etc.
            '/\sonchange="[^"]*"/i',
            '/\sonsubmit="[^"]*"/i',
        ];

        foreach ($jsAttributePatterns as $pattern) {
            $html = preg_replace_callback($pattern, function($match) {
                $placeholder = " JS_ATTR_PLACEHOLDER_{$this->attributeCounter} ";
                $this->attributePlaceholders[$placeholder] = $match[0];
                $this->attributeCounter++;
                return $placeholder;
            }, $html);
        }

        return $html;
    }

    /**
     * Phase 2: Protect JavaScript expressions and keywords in content
     */
    private function protectJavaScriptExpressions(string $html): string
    {
        // Target the exact expressions that are being translated
        $jsExpressionPatterns = [
            '/loaded\s*=\s*true/i',        // Alpine.js loaded = true
            '/loaded\s*=\s*false/i',       // Alpine.js loaded = false  
            '/count\s*\+=\s*\d+/i',        // count += 3
            '/count\s*<\s*\d+/i',          // count < 150
            '/if\s*\(\s*count\s*<\s*\d+\)/i', // if(count < 150)
            '/setTimeout\s*\(\s*\(\)\s*=>/i',  // setTimeout(() =>
            '/setInterval\s*\(\s*\(\)\s*=>/i', // setInterval(() =>
            '/true\s*,\s*\d+/i',           // true, 300
            '/false\s*,\s*\d+/i',          // false, 300
        ];

        foreach ($jsExpressionPatterns as $pattern) {
            $html = preg_replace_callback($pattern, function($match) {
                $placeholder = "JS_EXPR_PLACEHOLDER_{$this->expressionCounter}";
                $this->expressionPlaceholders[$placeholder] = $match[0];
                $this->expressionCounter++;
                return $placeholder;
            }, $html);
        }

        return $html;
    }

    /**
     * Restore all protected JavaScript code
     */
    public function restoreJavaScript(string $html): string
    {
        // Restore JavaScript expressions first
        foreach ($this->expressionPlaceholders as $placeholder => $original) {
            $html = str_replace($placeholder, $original, $html);
        }

        // Then restore JavaScript attributes
        foreach ($this->attributePlaceholders as $placeholder => $original) {
            $html = str_replace($placeholder, $original, $html);
        }

        Log::info('✅ JavaScript protection restored', [
            'attributes_restored' => count($this->attributePlaceholders),
            'expressions_restored' => count($this->expressionPlaceholders),
            'total_restored' => count($this->attributePlaceholders) + count($this->expressionPlaceholders)
        ]);

        return $html;
    }

    /**
     * Get protection stats for debugging
     */
    public function getProtectionStats(): array
    {
        return [
            'attributes_protected' => count($this->attributePlaceholders),
            'expressions_protected' => count($this->expressionPlaceholders),
            'attribute_samples' => array_slice($this->attributePlaceholders, 0, 3),
            'expression_samples' => array_slice($this->expressionPlaceholders, 0, 3),
        ];
    }

    /**
     * Reset protection state for new translation
     */
    public function reset(): void
    {
        $this->attributePlaceholders = [];
        $this->expressionPlaceholders = [];
        $this->attributeCounter = 0;
        $this->expressionCounter = 0;
    }
}