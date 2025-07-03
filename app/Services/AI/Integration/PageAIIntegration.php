<?php

namespace App\Services\AI\Integration;

use App\Services\AI\Integration\BaseModuleAIIntegration;
use Illuminate\Support\Facades\Log;
use Exception;

class PageAIIntegration extends BaseModuleAIIntegration
{
    public function getName(): string
    {
        return 'page';
    }

    protected function initializeConfiguration(): void
    {
        $this->configuration = [
            'active' => true,
            'name' => 'Page Modülü AI Entegrasyonu',
            'description' => 'Sayfa içeriği oluşturma, analiz ve optimizasyon araçları',
            'version' => '1.0.0',
            'max_content_length' => 10000,
            'supported_languages' => ['tr', 'en'],
            'templates' => [
                'blog_post' => 'Blog yazısı şablonu',
                'product_page' => 'Ürün sayfası şablonu',
                'landing_page' => 'Landing sayfası şablonu',
                'about_page' => 'Hakkımızda sayfası şablonu'
            ]
        ];
    }

    protected function registerActions(): void
    {
        // İçerik Oluşturma Actions
        $this->addAction('generateContent', [
            'method' => 'generatePageContent',
            'required_params' => ['title', 'content_type'],
            'estimate_method' => 'estimateContentGenerationTokens',
            'description' => 'Sayfa başlığına göre içerik oluştur',
            'category' => 'content_generation'
        ]);

        $this->addAction('generateFromTemplate', [
            'method' => 'generateContentFromTemplate',
            'required_params' => ['template', 'variables'],
            'estimate_method' => 'estimateTemplateTokens',
            'description' => 'Şablondan içerik oluştur',
            'category' => 'content_generation'
        ]);

        $this->addAction('expandContent', [
            'method' => 'expandExistingContent',
            'required_params' => ['existing_content', 'expansion_type'],
            'estimate_method' => 'estimateExpansionTokens',
            'description' => 'Mevcut içeriği genişlet',
            'category' => 'content_generation'
        ]);

        // İçerik Analizi Actions
        $this->addAction('analyzeSEO', [
            'method' => 'analyzeSEOScore',
            'required_params' => ['content', 'target_keyword'],
            'estimate_method' => 'estimateAnalysisTokens',
            'description' => 'SEO skoru analizi yap',
            'category' => 'content_analysis'
        ]);

        $this->addAction('analyzeReadability', [
            'method' => 'analyzeContentReadability',
            'required_params' => ['content'],
            'estimate_method' => 'estimateAnalysisTokens',
            'description' => 'İçerik okunabilirlik analizi',
            'category' => 'content_analysis'
        ]);

        $this->addAction('suggestImprovements', [
            'method' => 'suggestContentImprovements',
            'required_params' => ['content'],
            'estimate_method' => 'estimateAnalysisTokens',
            'description' => 'İçerik iyileştirme önerileri',
            'category' => 'content_analysis'
        ]);

        // İçerik Optimizasyonu Actions
        $this->addAction('optimizeSEO', [
            'method' => 'optimizeForSEO',
            'required_params' => ['content', 'target_keyword', 'meta_title'],
            'estimate_method' => 'estimateOptimizationTokens',
            'description' => 'SEO optimizasyonu yap',
            'category' => 'content_optimization'
        ]);

        $this->addAction('generateMetaTags', [
            'method' => 'generateMetaTags',
            'required_params' => ['content', 'title'],
            'estimate_method' => 'estimateMetaTokens',
            'description' => 'Meta etiketleri oluştur',
            'category' => 'content_optimization'
        ]);

        $this->addAction('translateContent', [
            'method' => 'translatePageContent',
            'required_params' => ['content', 'target_language'],
            'estimate_method' => 'estimateTranslationTokens',
            'description' => 'İçeriği çevir',
            'category' => 'content_optimization'
        ]);

        $this->addAction('rewriteContent', [
            'method' => 'rewritePageContent',
            'required_params' => ['content', 'rewrite_style'],
            'estimate_method' => 'estimateRewriteTokens',
            'description' => 'İçeriği yeniden yaz',
            'category' => 'content_optimization'
        ]);

        // KOLAY ACTIONS - Hemen kullanılabilir
        $this->addAction('generateHeadlines', [
            'method' => 'generateHeadlineAlternatives',
            'required_params' => ['title', 'content_type'],
            'estimate_method' => 'estimateHeadlineTokens',
            'description' => 'Çekici başlık alternatifleri oluştur',
            'category' => 'content_creation'
        ]);

        $this->addAction('generateSummary', [
            'method' => 'generateContentSummary',
            'required_params' => ['content'],
            'estimate_method' => 'estimateSummaryTokens',
            'description' => 'İçerik özeti oluştur',
            'category' => 'content_analysis'
        ]);

        $this->addAction('generateFAQ', [
            'method' => 'generateFAQSection',
            'required_params' => ['content'],
            'estimate_method' => 'estimateFAQTokens',
            'description' => 'SSS bölümü oluştur',
            'category' => 'content_creation'
        ]);

        $this->addAction('extractKeywords', [
            'method' => 'extractContentKeywords',
            'required_params' => ['content'],
            'estimate_method' => 'estimateKeywordTokens',
            'description' => 'Anahtar kelimeleri çıkar',
            'category' => 'content_analysis'
        ]);

        $this->addAction('generateCallToActions', [
            'method' => 'generateCTAOptions',
            'required_params' => ['content', 'goal'],
            'estimate_method' => 'estimateCTATokens',
            'description' => 'Eylem çağrısı önerileri oluştur',
            'category' => 'content_creation'
        ]);

        // ORTA ZORLUK ACTIONS
        $this->addAction('suggestRelatedTopics', [
            'method' => 'suggestRelatedTopics',
            'required_params' => ['content', 'topic_count'],
            'estimate_method' => 'estimateTopicTokens',
            'description' => 'İlgili konu önerileri',
            'category' => 'content_discovery'
        ]);

        $this->addAction('analyzeTone', [
            'method' => 'analyzeContentTone',
            'required_params' => ['content'],
            'estimate_method' => 'estimateToneTokens',
            'description' => 'Yazım tonu analizi',
            'category' => 'content_analysis'
        ]);

        $this->addAction('generateSocialPosts', [
            'method' => 'generateSocialMediaPosts',
            'required_params' => ['content', 'platforms'],
            'estimate_method' => 'estimateSocialTokens',
            'description' => 'Sosyal medya paylaşım metinleri',
            'category' => 'content_adaptation'
        ]);

        $this->addAction('optimizeHeadings', [
            'method' => 'optimizeHeadingStructure',
            'required_params' => ['content'],
            'estimate_method' => 'estimateHeadingTokens',
            'description' => 'Başlık yapısı optimizasyonu',
            'category' => 'content_optimization'
        ]);

        $this->addAction('generateOutline', [
            'method' => 'generateContentOutline',
            'required_params' => ['title', 'content_type'],
            'estimate_method' => 'estimateOutlineTokens',
            'description' => 'İçerik ana hatları oluştur',
            'category' => 'content_planning'
        ]);
    }

    /**
     * Sayfa içeriği oluştur
     */
    protected function generatePageContent(array $parameters): array
    {
        $title = $parameters['title'];
        $contentType = $parameters['content_type'];
        $language = $parameters['language'] ?? 'tr';
        $wordCount = $parameters['word_count'] ?? 500;

        $prompt = $this->buildPrompt($this->getContentGenerationPrompt(), [
            'title' => $title,
            'content_type' => $contentType,
            'language' => $language,
            'word_count' => $wordCount
        ]);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'title' => $title,
                'content_type' => $contentType,
                'language' => $language,
                'word_count' => str_word_count($response['data']['content']),
                'generated_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    /**
     * Şablondan içerik oluştur
     */
    protected function generateContentFromTemplate(array $parameters): array
    {
        $template = $parameters['template'];
        $variables = $parameters['variables'];

        if (!isset($this->configuration['templates'][$template])) {
            throw new Exception("Desteklenmeyen şablon: {$template}");
        }

        $prompt = $this->buildPrompt($this->getTemplatePrompt($template), $variables);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'template' => $template,
                'variables' => $variables,
                'generated_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    /**
     * SEO analizi yap
     */
    protected function analyzeSEOScore(array $parameters): array
    {
        $content = $parameters['content'];
        $targetKeyword = $parameters['target_keyword'];

        $prompt = $this->buildPrompt($this->getSEOAnalysisPrompt(), [
            'content' => $content,
            'target_keyword' => $targetKeyword
        ]);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'target_keyword' => $targetKeyword,
                'content_length' => strlen($content),
                'analyzed_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    /**
     * Meta etiketleri oluştur
     */
    protected function generateMetaTags(array $parameters): array
    {
        $content = $parameters['content'];
        $title = $parameters['title'];

        $prompt = $this->buildPrompt($this->getMetaTagsPrompt(), [
            'content' => substr($content, 0, 2000), // İlk 2000 karakter
            'title' => $title
        ]);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'title' => $title,
                'content_length' => strlen($content),
                'generated_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    /**
     * İçerik çevirisi
     */
    protected function translatePageContent(array $parameters): array
    {
        $content = $parameters['content'];
        $targetLanguage = $parameters['target_language'];

        if (!in_array($targetLanguage, $this->configuration['supported_languages'])) {
            throw new Exception("Desteklenmeyen dil: {$targetLanguage}");
        }

        $prompt = $this->buildPrompt($this->getTranslationPrompt(), [
            'content' => $content,
            'target_language' => $targetLanguage
        ]);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'source_language' => 'auto-detected',
                'target_language' => $targetLanguage,
                'original_length' => strlen($content),
                'translated_length' => strlen($response['data']['content']),
                'translated_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    /**
     * KOLAY ACTIONS - İmplementasyonlar
     */

    /**
     * Başlık alternatifleri oluştur
     */
    protected function generateHeadlineAlternatives(array $parameters): array
    {
        $title = $parameters['title'];
        $contentType = $parameters['content_type'];
        $count = $parameters['count'] ?? 5;

        $prompt = $this->buildPrompt($this->getHeadlinePrompt(), [
            'title' => $title,
            'content_type' => $contentType,
            'count' => $count
        ]);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'original_title' => $title,
                'content_type' => $contentType,
                'alternatives_count' => $count,
                'generated_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    /**
     * İçerik özeti oluştur
     */
    protected function generateContentSummary(array $parameters): array
    {
        $content = $parameters['content'];
        $summaryLength = $parameters['summary_length'] ?? 'short'; // short, medium, long

        $prompt = $this->buildPrompt($this->getSummaryPrompt(), [
            'content' => substr($content, 0, 3000), // İlk 3000 karakter
            'summary_length' => $summaryLength
        ]);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'original_length' => strlen($content),
                'summary_length' => $summaryLength,
                'compression_ratio' => round(strlen($response['data']['content']) / strlen($content) * 100, 2),
                'generated_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    /**
     * SSS bölümü oluştur
     */
    protected function generateFAQSection(array $parameters): array
    {
        $content = $parameters['content'];
        $questionCount = $parameters['question_count'] ?? 5;

        $prompt = $this->buildPrompt($this->getFAQPrompt(), [
            'content' => substr($content, 0, 2500),
            'question_count' => $questionCount
        ]);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'question_count' => $questionCount,
                'content_length' => strlen($content),
                'generated_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    /**
     * Anahtar kelimeleri çıkar
     */
    protected function extractContentKeywords(array $parameters): array
    {
        $content = $parameters['content'];
        $keywordCount = $parameters['keyword_count'] ?? 10;
        $includeRelated = $parameters['include_related'] ?? true;

        $prompt = $this->buildPrompt($this->getKeywordExtractionPrompt(), [
            'content' => substr($content, 0, 2000),
            'keyword_count' => $keywordCount,
            'include_related' => $includeRelated ? 'evet' : 'hayır'
        ]);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'keyword_count' => $keywordCount,
                'include_related' => $includeRelated,
                'content_length' => strlen($content),
                'generated_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    /**
     * Eylem çağrısı önerileri oluştur
     */
    protected function generateCTAOptions(array $parameters): array
    {
        $content = $parameters['content'];
        $goal = $parameters['goal']; // conversion, engagement, subscription, etc.
        $ctaCount = $parameters['cta_count'] ?? 3;

        $prompt = $this->buildPrompt($this->getCTAPrompt(), [
            'content' => substr($content, 0, 1500),
            'goal' => $goal,
            'cta_count' => $ctaCount
        ]);

        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->sendAIRequest($messages, $this->getTenantId());

        if (!$response['success']) {
            throw new Exception($response['error']);
        }

        return $this->formatResponse([
            'content' => $response['data']['content'],
            'metadata' => [
                'goal' => $goal,
                'cta_count' => $ctaCount,
                'content_length' => strlen($content),
                'generated_at' => now()->toISOString()
            ]
        ], $response['tokens_used']);
    }

    // Token Tahmin Metodları
    protected function estimateContentGenerationTokens(array $parameters): int
    {
        $wordCount = $parameters['word_count'] ?? 500;
        return (int) ceil($wordCount * 1.5); // Ortalama token/kelime oranı
    }

    protected function estimateAnalysisTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        return (int) ceil($contentLength / 4) + 200; // İçerik + analiz prompt
    }

    protected function estimateTranslationTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        return (int) ceil($contentLength / 2); // Çeviri genelde daha fazla token kullanır
    }

    // YENİ TOKEN TAHMİN METODLARı
    protected function estimateHeadlineTokens(array $parameters): int
    {
        $titleLength = strlen($parameters['title'] ?? '');
        $count = $parameters['count'] ?? 5;
        return (int) ceil($titleLength / 4) + ($count * 20); // Başlık + alternatifler
    }

    protected function estimateSummaryTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        return (int) ceil($contentLength / 6) + 100; // Özet genelde daha kısa
    }

    protected function estimateFAQTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        $questionCount = $parameters['question_count'] ?? 5;
        return (int) ceil($contentLength / 8) + ($questionCount * 50);
    }

    protected function estimateKeywordTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        return (int) ceil($contentLength / 10) + 150; // Analiz için ek token
    }

    protected function estimateCTATokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        $ctaCount = $parameters['cta_count'] ?? 3;
        return (int) ceil($contentLength / 8) + ($ctaCount * 30);
    }

    protected function estimateTopicTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        $topicCount = $parameters['topic_count'] ?? 5;
        return (int) ceil($contentLength / 6) + ($topicCount * 40);
    }

    protected function estimateToneTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        return (int) ceil($contentLength / 5) + 200; // Detaylı analiz
    }

    protected function estimateSocialTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        $platformCount = count($parameters['platforms'] ?? []);
        return (int) ceil($contentLength / 8) + ($platformCount * 100);
    }

    protected function estimateHeadingTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        return (int) ceil($contentLength / 3) + 300; // Yapısal analiz
    }

    protected function estimateOutlineTokens(array $parameters): int
    {
        $titleLength = strlen($parameters['title'] ?? '');
        $sectionCount = $parameters['section_count'] ?? 5;
        return (int) ceil($titleLength / 4) + ($sectionCount * 60);
    }

    protected function estimateRewriteTokens(array $parameters): int
    {
        $contentLength = strlen($parameters['content'] ?? '');
        return (int) ceil($contentLength / 2); // Yeniden yazma için fazla token
    }

    // Prompt Şablonları
    protected function getContentGenerationPrompt(): string
    {
        return "
Lütfen aşağıdaki bilgilere göre kaliteli bir sayfa içeriği oluşturun:

Başlık: {title}
İçerik Türü: {content_type}
Dil: {language}
Hedef Kelime Sayısı: {word_count}

İÇERİK GEREKLİLİKLERİ:
1. SEO dostu olmalı
2. Okunabilir ve akıcı olmalı
3. Hedef kelime sayısına uygun olmalı
4. Alt başlıklar kullanın
5. İçerik türüne uygun format kullanın

Lütfen sadece içeriği döndürün, ek açıklama yapmayın.
        ";
    }

    protected function getSEOAnalysisPrompt(): string
    {
        return "
Aşağıdaki içeriği SEO açısından analiz edin:

İÇERİK:
{content}

HEDEF ANAHTAR KELİME: {target_keyword}

ANALIZ KRİTERLERİ:
1. Anahtar kelime yoğunluğu
2. Başlık yapısı (H1, H2, H3)
3. Meta açıklama önerisi
4. İç bağlantı fırsatları
5. Görsel önerileri
6. Genel SEO skoru (1-100)

Lütfen detaylı bir analiz raporu ve iyileştirme önerileri sunun.
        ";
    }

    protected function getMetaTagsPrompt(): string
    {
        return "
Aşağıdaki sayfa için SEO meta etiketleri oluşturun:

BAŞLIK: {title}
İÇERİK ÖZETİ:
{content}

OLUŞTURACAĞINIZ META ETİKETLERİ:
1. Meta Title (60 karakter max)
2. Meta Description (160 karakter max)
3. Meta Keywords (10 anahtar kelime max)
4. Open Graph Title
5. Open Graph Description

Lütfen JSON formatında döndürün.
        ";
    }

    protected function getTranslationPrompt(): string
    {
        return "
Aşağıdaki içeriği {target_language} diline çevirin:

{content}

ÇEVİRİ KURALLARI:
1. Anlamı koruyun
2. Doğal akış sağlayın
3. Teknik terimleri uygun şekilde çevirin
4. Formatı koruyun (başlıklar, listeler vs.)
5. SEO değerini kaybetmeyin

Sadece çevrilmiş içeriği döndürün.
        ";
    }

    protected function getTemplatePrompt(string $template): string
    {
        $prompts = [
            'blog_post' => "
{title} başlıklı bir blog yazısı oluşturun.

İÇERİK DEĞİŞKENLERİ:
- Konu: {topic}
- Hedef Kitle: {target_audience}
- Ton: {tone}
- Kelime Sayısı: {word_count}

YAPISAL GEREKLER:
1. Giriş paragrafı
2. 3-5 ana bölüm
3. Sonuç paragrafı
4. Call-to-action
            ",
            
            'product_page' => "
{product_name} ürünü için ürün sayfası içeriği oluşturun.

ÜRÜN BİLGİLERİ:
- Ürün Adı: {product_name}
- Kategori: {category}
- Fiyat: {price}
- Özellikler: {features}

SAYFA YAPISI:
1. Ürün açıklaması
2. Özellikler listesi
3. Faydalar
4. Müşteri yorumları bölümü
5. Satın alma çağrısı
            "
        ];

        return $prompts[$template] ?? $prompts['blog_post'];
    }

    /**
     * YENİ PROMPT ŞABLONLARI
     */

    protected function getHeadlinePrompt(): string
    {
        return "
Aşağıdaki başlık için {count} adet çekici ve farklı başlık alternatifi oluşturun:

MEVCUT BAŞLIK: {title}
İÇERİK TÜRÜ: {content_type}

BAŞLIK KRİTERLERİ:
1. SEO dostu olmalı
2. Tıklanabilir ve çekici olmalı
3. İçerik türüne uygun olmalı
4. Farklı tonlarda olmalı (merak uyandıran, problem çözen, fayda odaklı)
5. 60 karakter altında olmalı

Lütfen her alternatifi numaralayarak listeleyin.
        ";
    }

    protected function getSummaryPrompt(): string
    {
        return "
Aşağıdaki içeriğin {summary_length} bir özetini oluşturun:

İÇERİK:
{content}

ÖZET KRİTERLERİ:
- Kısa: 2-3 cümle
- Orta: 4-6 cümle
- Uzun: 1-2 paragraf

Ana noktaları koruyun ve akıcı bir dille yazın.
        ";
    }

    protected function getFAQPrompt(): string
    {
        return "
Aşağıdaki içerik için {question_count} adet sık sorulan soru ve cevap oluşturun:

İÇERİK:
{content}

SSS KRİTERLERİ:
1. Gerçekçi ve kullanıcıların sorabileceği sorular olmalı
2. Cevaplar açık ve anlaşılır olmalı
3. İçerikle doğrudan ilgili olmalı
4. Farklı açılardan yaklaşmalı

Format:
S: Soru metni?
C: Cevap metni...
        ";
    }

    protected function getKeywordExtractionPrompt(): string
    {
        return "
Aşağıdaki içerikten {keyword_count} adet anahtar kelime çıkarın:

İÇERİK:
{content}

İLGİLİ KELİMELER DAHİL: {include_related}

ANAHTAR KELİME KRİTERLERİ:
1. SEO değeri yüksek olmalı
2. İçerikle doğrudan ilgili olmalı
3. Arama hacmi potansiyeli yüksek olmalı
4. Rekabet seviyesi uygun olmalı

Lütfen önem sırasına göre listeleyin ve her birinin SEO potansiyelini belirtin.
        ";
    }

    protected function getCTAPrompt(): string
    {
        return "
Aşağıdaki içerik için {goal} hedefli {cta_count} adet eylem çağrısı oluşturun:

İÇERİK ÖZETİ:
{content}

HEDEF: {goal}

CTA KRİTERLERİ:
1. Hedefe uygun olmalı
2. İkna edici olmalı
3. Aciliyet hissi yaratmalı
4. Net ve anlaşılır olmalı
5. Farklı tonlarda olmalı

Her CTA için kullanım yeri önerisi de ekleyin (sayfa sonu, ortası, sidebar vs.)
        ";
    }

    protected function getRelatedTopicsPrompt(): string
    {
        return "
Aşağıdaki içerikle ilgili {topic_count} adet konu önerisi yapın:

İÇERİK:
{content}

KONU ÖNERİ KRİTERLERİ:
1. İçerikle doğrudan ilgili olmalı
2. Yeni içerik fırsatları sunmalı
3. SEO potansiyeli yüksek olmalı
4. Hedef kitleyi ilgilendirebilir olmalı
5. Mevcut içeriği tamamlayıcı olmalı

Her konu için kısa açıklama ve neden önemli olduğunu belirtin.
        ";
    }

    protected function getToneAnalysisPrompt(): string
    {
        return "
Aşağıdaki içeriğin yazım tonunu analiz edin:

İÇERİK:
{content}

ANALİZ KRİTERLERİ:
1. Yazım tonu (resmi, samimi, profesyonel, arkadaşça vs.)
2. Hedef kitle uyumu
3. Duygusal etki
4. Güven vericilik
5. İkna gücü
6. Okunabilirlik seviyesi

Analiz sonuçlarını puanla (1-10) ve iyileştirme önerileri sun.
        ";
    }

    protected function getSocialMediaPrompt(): string
    {
        return "
Aşağıdaki içerik için sosyal medya paylaşım metinleri oluşturun:

İÇERİK:
{content}

PLATFORMLAR: {platforms}

PLATFORM ÖZELLİKLERİ:
- Twitter: 280 karakter, hashtag kullan
- Facebook: Uzun metin, emoji kullan
- LinkedIn: Profesyonel ton, industry hashtag
- Instagram: Görsel odaklı, story formatı

Her platform için uygun metin ve hashtag önerileri sunun.
        ";
    }

    protected function getHeadingOptimizationPrompt(): string
    {
        return "
Aşağıdaki içeriğin başlık yapısını analiz edin ve optimize edin:

İÇERİK:
{content}

OPTİMİZASYON KRİTERLERİ:
1. H1-H6 hiyerarşisi düzgün olmalı
2. SEO dostu başlıklar
3. Okuyucu dostu yapı
4. Anahtar kelime optimizasyonu
5. Başlık uzunlukları uygun olmalı

Mevcut yapıyı analiz edin ve iyileştirilmiş versiyonu sunun.
        ";
    }

    protected function getOutlinePrompt(): string
    {
        return "
\"{title}\" konulu {content_type} için {section_count} bölümlü detaylı içerik ana hatları oluşturun:

BAŞLIK: {title}
İÇERİK TÜRÜ: {content_type}

ANA HAT KRİTERLERİ:
1. Mantıklı sıralama
2. Her bölüm net ve belirgin
3. Alt başlıklar dahil
4. Ana noktalar belirtilmeli
5. Tahmini kelime sayıları

Format:
1. Ana Bölüm Başlığı
   - Alt başlık 1
   - Alt başlık 2
   - Ana noktalar
   - Tahmini: 200-300 kelime
        ";
    }
}