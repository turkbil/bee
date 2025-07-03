<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\AI\AIServiceManager;

/**
 * AI Facade
 * 
 * @method static array sendRequest(array $messages, string $tenantId, string $moduleContext = null, array $options = [])
 * @method static \Generator sendStreamRequest(array $messages, string $tenantId, string $moduleContext = null, array $options = [])
 * @method static void registerIntegration(string $module, \App\Contracts\AI\AIIntegrationInterface $integration)
 * @method static \App\Contracts\AI\AIIntegrationInterface|null getIntegration(string $module)
 * @method static array executeModuleAction(string $module, string $action, array $parameters, string $tenantId)
 * @method static array getTokenStatus(string $tenantId)
 * @method static array getRegisteredIntegrations()
 * 
 * @see \App\Services\AI\AIServiceManager
 */
class AI extends Facade
{
    /**
     * Facade'in bağlı olduğu service container key'ini döndür
     */
    protected static function getFacadeAccessor(): string
    {
        return AIServiceManager::class;
    }

    /**
     * Modül için AI action çalıştır - Kısa yol metodu
     */
    public static function forModule(string $module): ModuleAIBuilder
    {
        return new ModuleAIBuilder($module, static::getFacadeRoot());
    }

    /**
     * Page modülü için kısa yol
     */
    public static function page(): ModuleAIBuilder
    {
        return static::forModule('page');
    }

    /**
     * Portfolio modülü için kısa yol
     */
    public static function portfolio(): ModuleAIBuilder
    {
        return static::forModule('portfolio');
    }

    /**
     * Studio modülü için kısa yol
     */
    public static function studio(): ModuleAIBuilder
    {
        return static::forModule('studio');
    }

    /**
     * Announcement modülü için kısa yol
     */
    public static function announcement(): ModuleAIBuilder
    {
        return static::forModule('announcement');
    }
}

/**
 * Modül AI Builder - Fluent API için
 */
class ModuleAIBuilder
{
    protected string $module;
    protected AIServiceManager $aiManager;
    protected string $action;
    protected array $parameters = [];
    protected ?string $tenantId = null;

    public function __construct(string $module, AIServiceManager $aiManager)
    {
        $this->module = $module;
        $this->aiManager = $aiManager;
        $this->tenantId = tenant('id') ?: 'default';
    }

    /**
     * Action belirle
     */
    public function action(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Parametreleri belirle
     */
    public function with(array $parameters): self
    {
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    /**
     * Tek parametre ekle
     */
    public function withParameter(string $key, $value): self
    {
        $this->parameters[$key] = $value;
        return $this;
    }

    /**
     * İçerik parametresi ekle
     */
    public function withContent(string $content): self
    {
        return $this->withParameter('content', $content);
    }

    /**
     * Başlık parametresi ekle
     */
    public function withTitle(string $title): self
    {
        return $this->withParameter('title', $title);
    }

    /**
     * Dil parametresi ekle
     */
    public function withLanguage(string $language): self
    {
        return $this->withParameter('language', $language);
    }

    /**
     * Prompt parametresi ekle
     */
    public function withPrompt(string $prompt): self
    {
        return $this->withParameter('prompt', $prompt);
    }

    /**
     * Token limiti belirle
     */
    public function withTokenLimit(int $tokenLimit): self
    {
        return $this->withParameter('token_limit', $tokenLimit);
    }

    /**
     * Tenant ID belirle
     */
    public function forTenant(string $tenantId): self
    {
        $this->tenantId = $tenantId;
        return $this;
    }

    /**
     * Action'ı çalıştır
     */
    public function execute(): array
    {
        if (!isset($this->action)) {
            throw new \InvalidArgumentException('Action belirtilmedi. action() metodunu kullanın.');
        }

        return $this->aiManager->executeModuleAction(
            $this->module,
            $this->action,
            $this->parameters,
            $this->tenantId
        );
    }

    /**
     * Token miktarını tahmin et
     */
    public function estimateTokens(): int
    {
        if (!isset($this->action)) {
            throw new \InvalidArgumentException('Action belirtilmedi. action() metodunu kullanın.');
        }

        $integration = $this->aiManager->getIntegration($this->module);
        if (!$integration) {
            return 0;
        }

        return $integration->estimateTokens($this->action, $this->parameters);
    }

    /**
     * Desteklenen action'ları getir
     */
    public function getSupportedActions(): array
    {
        $integration = $this->aiManager->getIntegration($this->module);
        if (!$integration) {
            return [];
        }

        return $integration->getSupportedActions();
    }

    /**
     * İçerik oluşturma kısa yolu
     */
    public function generateContent(string $title, string $contentType = 'blog_post'): array
    {
        return $this->action('generateContent')
            ->withTitle($title)
            ->withParameter('content_type', $contentType)
            ->execute();
    }

    /**
     * SEO analizi kısa yolu
     */
    public function analyzeSEO(string $content, string $targetKeyword): array
    {
        return $this->action('analyzeSEO')
            ->withContent($content)
            ->withParameter('target_keyword', $targetKeyword)
            ->execute();
    }

    /**
     * Meta etiketleri oluşturma kısa yolu
     */
    public function generateMetaTags(string $content, string $title): array
    {
        return $this->action('generateMetaTags')
            ->withContent($content)
            ->withTitle($title)
            ->execute();
    }

    /**
     * İçerik çevirisi kısa yolu
     */
    public function translate(string $content, string $targetLanguage): array
    {
        return $this->action('translateContent')
            ->withContent($content)
            ->withParameter('target_language', $targetLanguage)
            ->execute();
    }

    /**
     * YENİ KOLAY ACTION KISA YOLLARI
     */

    /**
     * Başlık alternatifleri kısa yolu
     */
    public function generateHeadlines(string $title, string $contentType = 'blog_post', int $count = 5): array
    {
        return $this->action('generateHeadlines')
            ->withTitle($title)
            ->withParameter('content_type', $contentType)
            ->withParameter('count', $count)
            ->execute();
    }

    /**
     * İçerik özeti kısa yolu
     */
    public function generateSummary(string $content, string $length = 'short'): array
    {
        return $this->action('generateSummary')
            ->withContent($content)
            ->withParameter('summary_length', $length)
            ->execute();
    }

    /**
     * SSS oluşturma kısa yolu
     */
    public function generateFAQ(string $content, int $questionCount = 5): array
    {
        return $this->action('generateFAQ')
            ->withContent($content)
            ->withParameter('question_count', $questionCount)
            ->execute();
    }

    /**
     * Anahtar kelime çıkarma kısa yolu
     */
    public function extractKeywords(string $content, int $keywordCount = 10, bool $includeRelated = true): array
    {
        return $this->action('extractKeywords')
            ->withContent($content)
            ->withParameter('keyword_count', $keywordCount)
            ->withParameter('include_related', $includeRelated)
            ->execute();
    }

    /**
     * CTA oluşturma kısa yolu
     */
    public function generateCTA(string $content, string $goal = 'conversion', int $ctaCount = 3): array
    {
        return $this->action('generateCallToActions')
            ->withContent($content)
            ->withParameter('goal', $goal)
            ->withParameter('cta_count', $ctaCount)
            ->execute();
    }

    /**
     * İlgili konular kısa yolu
     */
    public function suggestTopics(string $content, int $topicCount = 5): array
    {
        return $this->action('suggestRelatedTopics')
            ->withContent($content)
            ->withParameter('topic_count', $topicCount)
            ->execute();
    }

    /**
     * Ton analizi kısa yolu
     */
    public function analyzeTone(string $content): array
    {
        return $this->action('analyzeTone')
            ->withContent($content)
            ->execute();
    }

    /**
     * Sosyal medya postları kısa yolu
     */
    public function generateSocialPosts(string $content, array $platforms = ['twitter', 'facebook', 'linkedin']): array
    {
        return $this->action('generateSocialPosts')
            ->withContent($content)
            ->withParameter('platforms', $platforms)
            ->execute();
    }

    /**
     * Başlık optimizasyonu kısa yolu
     */
    public function optimizeHeadings(string $content): array
    {
        return $this->action('optimizeHeadings')
            ->withContent($content)
            ->execute();
    }

    /**
     * İçerik ana hatları kısa yolu
     */
    public function generateOutline(string $title, string $contentType = 'blog_post', int $sectionCount = 5): array
    {
        return $this->action('generateOutline')
            ->withTitle($title)
            ->withParameter('content_type', $contentType)
            ->withParameter('section_count', $sectionCount)
            ->execute();
    }
}