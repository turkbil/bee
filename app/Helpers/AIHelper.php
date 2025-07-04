<?php

use App\Facades\AI;

/**
 * Global AI Helper Functions
 * 
 * Bu dosya composer.json autoload files bölümüne eklenerek
 * tüm projede global fonksiyonlar olarak kullanılabilir.
 */

if (!function_exists('ai')) {
    /**
     * AI facade'ine kısa yoldan erişim
     * 
     * @return \App\Services\AI\AIServiceManager
     */
    function ai()
    {
        return app(\App\Services\AI\AIServiceManager::class);
    }
}

if (!function_exists('ai_for_module')) {
    /**
     * Belirtilen modül için AI builder döndür
     * 
     * @param string $module
     * @return \App\Facades\ModuleAIBuilder
     */
    function ai_for_module(string $module)
    {
        return AI::forModule($module);
    }
}

if (!function_exists('ai_page')) {
    /**
     * Page modülü için AI builder
     * 
     * @return \App\Facades\ModuleAIBuilder
     */
    function ai_page()
    {
        return AI::page();
    }
}

if (!function_exists('ai_portfolio')) {
    /**
     * Portfolio modülü için AI builder
     * 
     * @return \App\Facades\ModuleAIBuilder
     */
    function ai_portfolio()
    {
        return AI::portfolio();
    }
}

if (!function_exists('ai_studio')) {
    /**
     * Studio modülü için AI builder
     * 
     * @return \App\Facades\ModuleAIBuilder
     */
    function ai_studio()
    {
        return AI::studio();
    }
}

if (!function_exists('ai_announcement')) {
    /**
     * Announcement modülü için AI builder
     * 
     * @return \App\Facades\ModuleAIBuilder
     */
    function ai_announcement()
    {
        return AI::announcement();
    }
}

if (!function_exists('ai_generate_content')) {
    /**
     * Hızlı içerik oluşturma
     * 
     * @param string $module
     * @param string $title
     * @param string $contentType
     * @param array $options
     * @return array
     */
    function ai_generate_content(string $module, string $title, string $contentType = 'blog_post', array $options = []): array
    {
        return ai_for_module($module)
            ->action('generateContent')
            ->withTitle($title)
            ->withParameter('content_type', $contentType)
            ->with($options)
            ->execute();
    }
}

if (!function_exists('ai_analyze_seo')) {
    /**
     * Hızlı SEO analizi
     * 
     * @param string $module
     * @param string $content
     * @param string $targetKeyword
     * @return array
     */
    function ai_analyze_seo(string $module, string $content, string $targetKeyword): array
    {
        return ai_for_module($module)
            ->action('analyzeSEO')
            ->withContent($content)
            ->withParameter('target_keyword', $targetKeyword)
            ->execute();
    }
}

if (!function_exists('ai_translate')) {
    /**
     * Hızlı çeviri
     * 
     * @param string $module
     * @param string $content
     * @param string $targetLanguage
     * @return array
     */
    function ai_translate(string $module, string $content, string $targetLanguage): array
    {
        return ai_for_module($module)
            ->action('translateContent')
            ->withContent($content)
            ->withParameter('target_language', $targetLanguage)
            ->execute();
    }
}

if (!function_exists('ai_generate_meta_tags')) {
    /**
     * Hızlı meta etiket oluşturma
     * 
     * @param string $module
     * @param string $content
     * @param string $title
     * @return array
     */
    function ai_generate_meta_tags(string $module, string $content, string $title): array
    {
        return ai_for_module($module)
            ->action('generateMetaTags')
            ->withContent($content)
            ->withTitle($title)
            ->execute();
    }
}

if (!function_exists('ai_estimate_tokens')) {
    /**
     * Token miktarı tahmini
     * 
     * @param string $module
     * @param string $action
     * @param array $parameters
     * @return int
     */
    function ai_estimate_tokens(string $module, string $action, array $parameters = []): int
    {
        return ai_for_module($module)
            ->action($action)
            ->with($parameters)
            ->estimateTokens();
    }
}

if (!function_exists('ai_check_tokens')) {
    /**
     * Token durumu kontrolü (YENİ SİSTEME YÖNLENDİRİLDİ)
     * 
     * @param string|null $tenantId
     * @return array
     */
    function ai_check_tokens(?string $tenantId = null): array
    {
        return ai_get_token_stats($tenantId);
    }
}

if (!function_exists('ai_can_use_tokens_old')) {
    /**
     * Token kullanım kontrolü (ESKİ SİSTEM - YENİ SİSTEM AITokenHelper'da)
     * 
     * @param int $tokensNeeded
     * @param string|null $tenantId
     * @return bool
     */
    function ai_can_use_tokens_old(int $tokensNeeded, ?string $tenantId = null): bool
    {
        return ai_can_use_tokens($tokensNeeded, $tenantId);
    }
}

if (!function_exists('ai_get_remaining_tokens')) {
    /**
     * Kalan token miktarını getir (YENİ SİSTEME YÖNLENDİRİLDİ)
     * 
     * @param string|null $tenantId
     * @return int
     */
    function ai_get_remaining_tokens(?string $tenantId = null): int
    {
        return ai_get_token_balance($tenantId);
    }
}

if (!function_exists('ai_get_supported_actions')) {
    /**
     * Desteklenen AI action'larını getir
     * 
     * @param string $module
     * @return array
     */
    function ai_get_supported_actions(string $module): array
    {
        return ai_for_module($module)->getSupportedActions();
    }
}

if (!function_exists('ai_is_module_available')) {
    /**
     * Modül AI entegrasyonunun aktif olup olmadığını kontrol et
     * 
     * @param string $module
     * @return bool
     */
    function ai_is_module_available(string $module): bool
    {
        $integration = ai()->getIntegration($module);
        return $integration && $integration->isActive();
    }
}

if (!function_exists('ai_quick_request')) {
    /**
     * Hızlı AI isteği (ham mesaj gönderimi)
     * 
     * @param string $message
     * @param string|null $tenantId
     * @param string|null $moduleContext
     * @return array
     */
    function ai_quick_request(string $message, ?string $tenantId = null, ?string $moduleContext = null): array
    {
        $tenantId = $tenantId ?: tenant('id') ?: 'default';
        
        $messages = [
            ['role' => 'user', 'content' => $message]
        ];
        
        return ai()->sendRequest($messages, $tenantId, $moduleContext);
    }
}

if (!function_exists('ai_batch_process')) {
    /**
     * Toplu AI işlemi
     * 
     * @param string $module
     * @param array $items
     * @param string $action
     * @param array $commonParameters
     * @return array
     */
    function ai_batch_process(string $module, array $items, string $action, array $commonParameters = []): array
    {
        $results = [];
        
        foreach ($items as $key => $item) {
            try {
                $parameters = array_merge($commonParameters, is_array($item) ? $item : ['content' => $item]);
                
                $result = ai_for_module($module)
                    ->action($action)
                    ->with($parameters)
                    ->execute();
                
                $results[$key] = $result;
                
            } catch (Exception $e) {
                $results[$key] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'tokens_used' => 0
                ];
            }
        }
        
        return $results;
    }
}

// YENİ KOLAY ACTION HELPER'LARI

if (!function_exists('ai_generate_headlines')) {
    /**
     * Başlık alternatifleri oluştur
     * 
     * @param string $module
     * @param string $title
     * @param string $contentType
     * @param int $count
     * @return array
     */
    function ai_generate_headlines(string $module, string $title, string $contentType = 'blog_post', int $count = 5): array
    {
        return ai_for_module($module)
            ->action('generateHeadlines')
            ->withTitle($title)
            ->withParameter('content_type', $contentType)
            ->withParameter('count', $count)
            ->execute();
    }
}

if (!function_exists('ai_generate_summary')) {
    /**
     * İçerik özeti oluştur
     * 
     * @param string $module
     * @param string $content
     * @param string $length
     * @return array
     */
    function ai_generate_summary(string $module, string $content, string $length = 'short'): array
    {
        return ai_for_module($module)
            ->action('generateSummary')
            ->withContent($content)
            ->withParameter('summary_length', $length)
            ->execute();
    }
}

if (!function_exists('ai_generate_faq')) {
    /**
     * SSS bölümü oluştur
     * 
     * @param string $module
     * @param string $content
     * @param int $questionCount
     * @return array
     */
    function ai_generate_faq(string $module, string $content, int $questionCount = 5): array
    {
        return ai_for_module($module)
            ->action('generateFAQ')
            ->withContent($content)
            ->withParameter('question_count', $questionCount)
            ->execute();
    }
}

if (!function_exists('ai_extract_keywords')) {
    /**
     * Anahtar kelimeleri çıkar
     * 
     * @param string $module
     * @param string $content
     * @param int $keywordCount
     * @param bool $includeRelated
     * @return array
     */
    function ai_extract_keywords(string $module, string $content, int $keywordCount = 10, bool $includeRelated = true): array
    {
        return ai_for_module($module)
            ->action('extractKeywords')
            ->withContent($content)
            ->withParameter('keyword_count', $keywordCount)
            ->withParameter('include_related', $includeRelated)
            ->execute();
    }
}

if (!function_exists('ai_generate_cta')) {
    /**
     * Eylem çağrısı önerileri oluştur
     * 
     * @param string $module
     * @param string $content
     * @param string $goal
     * @param int $ctaCount
     * @return array
     */
    function ai_generate_cta(string $module, string $content, string $goal, int $ctaCount = 3): array
    {
        return ai_for_module($module)
            ->action('generateCallToActions')
            ->withContent($content)
            ->withParameter('goal', $goal)
            ->withParameter('cta_count', $ctaCount)
            ->execute();
    }
}

// ORTA ZORLUK ACTION HELPER'LARI

if (!function_exists('ai_suggest_topics')) {
    /**
     * İlgili konu önerileri
     * 
     * @param string $module
     * @param string $content
     * @param int $topicCount
     * @return array
     */
    function ai_suggest_topics(string $module, string $content, int $topicCount = 5): array
    {
        return ai_for_module($module)
            ->action('suggestRelatedTopics')
            ->withContent($content)
            ->withParameter('topic_count', $topicCount)
            ->execute();
    }
}

if (!function_exists('ai_analyze_tone')) {
    /**
     * Yazım tonu analizi
     * 
     * @param string $module
     * @param string $content
     * @return array
     */
    function ai_analyze_tone(string $module, string $content): array
    {
        return ai_for_module($module)
            ->action('analyzeTone')
            ->withContent($content)
            ->execute();
    }
}

if (!function_exists('ai_generate_social_posts')) {
    /**
     * Sosyal medya paylaşım metinleri
     * 
     * @param string $module
     * @param string $content
     * @param array $platforms
     * @return array
     */
    function ai_generate_social_posts(string $module, string $content, array $platforms = ['twitter', 'facebook', 'linkedin']): array
    {
        return ai_for_module($module)
            ->action('generateSocialPosts')
            ->withContent($content)
            ->withParameter('platforms', $platforms)
            ->execute();
    }
}

if (!function_exists('ai_optimize_headings')) {
    /**
     * Başlık yapısı optimizasyonu
     * 
     * @param string $module
     * @param string $content
     * @return array
     */
    function ai_optimize_headings(string $module, string $content): array
    {
        return ai_for_module($module)
            ->action('optimizeHeadings')
            ->withContent($content)
            ->execute();
    }
}

if (!function_exists('ai_generate_outline')) {
    /**
     * İçerik ana hatları oluştur
     * 
     * @param string $module
     * @param string $title
     * @param string $contentType
     * @param int $sectionCount
     * @return array
     */
    function ai_generate_outline(string $module, string $title, string $contentType = 'blog_post', int $sectionCount = 5): array
    {
        return ai_for_module($module)
            ->action('generateOutline')
            ->withTitle($title)
            ->withParameter('content_type', $contentType)
            ->withParameter('section_count', $sectionCount)
            ->execute();
    }
}

// HIZLI KULLANIM ÖRNEKLERİ

if (!function_exists('ai_page_headlines')) {
    /**
     * Page modülü için başlık alternatifleri
     */
    function ai_page_headlines(string $title, string $contentType = 'blog_post', int $count = 5): array
    {
        return ai_generate_headlines('page', $title, $contentType, $count);
    }
}

if (!function_exists('ai_page_summary')) {
    /**
     * Page modülü için içerik özeti
     */
    function ai_page_summary(string $content, string $length = 'short'): array
    {
        return ai_generate_summary('page', $content, $length);
    }
}

if (!function_exists('ai_page_faq')) {
    /**
     * Page modülü için SSS
     */
    function ai_page_faq(string $content, int $questionCount = 5): array
    {
        return ai_generate_faq('page', $content, $questionCount);
    }
}

if (!function_exists('ai_page_keywords')) {
    /**
     * Page modülü için anahtar kelime çıkarma
     */
    function ai_page_keywords(string $content, int $keywordCount = 10): array
    {
        return ai_extract_keywords('page', $content, $keywordCount);
    }
}

if (!function_exists('ai_page_cta')) {
    /**
     * Page modülü için CTA önerileri
     */
    function ai_page_cta(string $content, string $goal = 'conversion', int $ctaCount = 3): array
    {
        return ai_generate_cta('page', $content, $goal, $ctaCount);
    }
}