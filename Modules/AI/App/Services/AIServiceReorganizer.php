<?php

namespace Modules\AI\App\Services;

/**
 * 🔧 AI SERVICE REORGANIZER - Modüler Yapı Planlayıcısı
 * 
 * AIService'i konulara göre ayırmak için plan:
 * 
 * 1. TRANSLATION METHODS (Çeviri)
 *    - translateText()
 *    - translateLongHtmlContent()
 *    - buildTranslationPrompt() [DEPRECATED - UltraAssertive ile değiştirildi]
 *    
 * 2. FEATURE METHODS (Feature Test/Execute)
 *    - askFeature()
 *    - executeFeature()
 *    - buildFeaturePrompt()
 *    
 * 3. CREDIT METHODS (Kredi Yönetimi)
 *    - deductCredits()
 *    - calculateCredits()
 *    - checkCreditBalance()
 *    
 * 4. PROVIDER METHODS (Provider Yönetimi)
 *    - switchProvider()
 *    - getAvailableProviders()
 *    - processRequest()
 *    
 * 5. CONVERSATION METHODS (Konuşma Tracking)
 *    - createConversationRecord()
 *    - trackConversation()
 *    
 * 6. CORE METHODS (Temel İşlemler)
 *    - enforceStructure()
 *    - parseApiResponse()
 *    - logDebugInfo()
 */
class AIServiceReorganizer
{
    /**
     * Bu sınıf AIService'in yeniden organize edilmesi için rehber olarak kullanılacak.
     * Her bölüm için ayrı trait'ler oluşturabiliriz.
     */
    
    public static function getReorganizationPlan(): array
    {
        return [
            'translation_methods' => [
                'translateText',
                'translateLongHtmlContent', 
                'buildTranslationPrompt_DEPRECATED'
            ],
            'feature_methods' => [
                'askFeature',
                'executeFeature',
                'buildFeaturePrompt'
            ],
            'credit_methods' => [
                'deductCredits',
                'calculateCredits', 
                'checkCreditBalance'
            ],
            'provider_methods' => [
                'switchProvider',
                'getAvailableProviders',
                'processRequest'
            ],
            'conversation_methods' => [
                'createConversationRecord',
                'trackConversation'
            ],
            'core_methods' => [
                'enforceStructure',
                'parseApiResponse',
                'logDebugInfo'
            ]
        ];
    }
}