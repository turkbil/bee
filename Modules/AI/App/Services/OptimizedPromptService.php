<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Services\TenantServiceFactory;

/**
 * Optimized Prompt Service
 *
 * AI için optimize edilmiş prompt oluşturur.
 * Tenant-specific kurallar + context bilgileri + conversation history birleştirir.
 *
 * Özellikler:
 * - Tenant-aware prompt building (TenantServiceFactory kullanır)
 * - User subscription context (premium/free/guest)
 * - Conversation history integration
 * - Smart search results embedding
 */
class OptimizedPromptService
{
    /**
     * Full AI prompt oluştur
     *
     * @param array $aiContext ModuleContextOrchestrator'dan gelen context
     * @param array $conversationHistory Son konuşma mesajları
     * @return string Tam sistem promptu
     */
    public function getFullPrompt(array $aiContext, array $conversationHistory = []): string
    {
        $prompts = [];

        // 1. Tenant-specific kurallar (EN ÖNEMLİ!)
        $tenantPrompt = $this->buildTenantPrompt();
        if (!empty($tenantPrompt)) {
            $prompts[] = $tenantPrompt;
        }

        // 2. Kullanıcı abonelik durumu (TENANT-AWARE!)
        $subscriptionContext = $this->buildSubscriptionContext($aiContext);
        if (!empty($subscriptionContext)) {
            $prompts[] = $subscriptionContext;
        }

        // 3. Genel context bilgileri
        $generalContext = $this->buildGeneralContext($aiContext);
        if (!empty($generalContext)) {
            $prompts[] = $generalContext;
        }

        // 4. Conversation history özeti
        $historyContext = $this->buildHistoryContext($conversationHistory);
        if (!empty($historyContext)) {
            $prompts[] = $historyContext;
        }

        return implode("\n\n", $prompts);
    }

    /**
     * Tenant-specific prompt kurallarını al
     */
    protected function buildTenantPrompt(): string
    {
        try {
            $promptService = TenantServiceFactory::getPromptService();
            if ($promptService && method_exists($promptService, 'getPromptAsString')) {
                return $promptService->getPromptAsString();
            }
        } catch (\Exception $e) {
            \Log::warning('OptimizedPromptService: Tenant prompt alınamadı', [
                'error' => $e->getMessage()
            ]);
        }

        return '';
    }

    /**
     * Kullanıcı abonelik durumu context'i oluştur
     *
     * @param array $aiContext
     * @return string
     */
    protected function buildSubscriptionContext(array $aiContext): string
    {
        $subscription = $aiContext['user_subscription'] ?? null;

        // Subscription bilgisi yoksa
        if (empty($subscription)) {
            return $this->buildGuestContext();
        }

        $status = $subscription['status'] ?? 'guest';
        $isPremium = $subscription['is_premium'] ?? false;
        $daysRemaining = $subscription['days_remaining'] ?? 0;
        $message = $subscription['message'] ?? '';

        $context = [];
        $context[] = "## 🔐 KULLANICI ABONELİK DURUMU";
        $context[] = "";
        $context[] = "**user_subscription bilgisi:**";
        $context[] = "```json";
        $context[] = json_encode($subscription, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $context[] = "```";
        $context[] = "";

        if ($status === 'premium' && $isPremium) {
            $context[] = "✅ **KULLANICI: PREMİUM ÜYE**";
            $context[] = "- Tüm özellikler aktif";
            $context[] = "- Playlist oluşturabilir";
            $context[] = "- [Dinle] butonları gösterilebilir";
            if ($daysRemaining > 0) {
                $context[] = "- Kalan gün: **{$daysRemaining} gün**";
            }
        } elseif ($status === 'free') {
            $context[] = "🟡 **KULLANICI: ÜCRETSİZ ÜYE**";
            $context[] = "- Kısıtlı özellikler";
            $context[] = "- ❌ Playlist oluşturamaz";
            $context[] = "- [Dinle] butonu gösterilebilir (reklam ile)";
            $context[] = "- Premium'a geçiş öner";
        } else {
            // guest/none
            $context[] = "⛔ **KULLANICI: ÜYE DEĞİL (GUEST)**";
            $context[] = "- ❌ ASLA [Dinle] butonu GÖSTERME!";
            $context[] = "- ❌ ASLA playlist OLUŞTURMA!";
            $context[] = "- ❌ ASLA [Playlist'e Ekle] butonu GÖSTERME!";
            $context[] = "- ❌ ASLA [Favorilere Ekle] butonu GÖSTERME!";
            $context[] = "- ✅ SADECE şarkı isimlerini metin olarak göster";
            $context[] = "- ✅ SADECE [Üye Ol](/login) butonu göster";
        }

        $context[] = "";

        return implode("\n", $context);
    }

    /**
     * Guest (üye olmayan) kullanıcı için context
     */
    protected function buildGuestContext(): string
    {
        $context = [];
        $context[] = "## 🔐 KULLANICI ABONELİK DURUMU";
        $context[] = "";
        $context[] = "⛔ **KULLANICI: ÜYE DEĞİL (GUEST) - user_subscription: null**";
        $context[] = "";
        $context[] = "**❌❌❌ YASAKLAR (ASLA YAPMA!):**";
        $context[] = "- ❌ ASLA [Dinle] butonu GÖSTERME!";
        $context[] = "- ❌ ASLA playlist OLUŞTURMA!";
        $context[] = "- ❌ ASLA [Playlist'e Ekle] butonu GÖSTERME!";
        $context[] = "- ❌ ASLA [Favorilere Ekle] butonu GÖSTERME!";
        $context[] = "- ❌ ASLA ACTION:CREATE_PLAYLIST tag'i KULLANMA!";
        $context[] = "";
        $context[] = "**✅ YAPMAN GEREKENLER:**";
        $context[] = "- ✅ Şarkı isimlerini SADECE metin olarak göster (link YOK!)";
        $context[] = "- ✅ 'Bu şarkıları dinlemek için üye olman gerekiyor!' de";
        $context[] = "- ✅ SADECE [Üye Ol](/login) butonu göster";
        $context[] = "";

        return implode("\n", $context);
    }

    /**
     * Genel context bilgilerini formatla
     */
    protected function buildGeneralContext(array $aiContext): string
    {
        $context = [];
        $context[] = "## BAĞLAM BİLGİLERİ";
        $context[] = "";

        // System prompt (settings'den gelen)
        if (!empty($aiContext['system_prompt'])) {
            $context[] = $aiContext['system_prompt'];
            $context[] = "";
        }

        // Module context
        if (!empty($aiContext['context']['modules'])) {
            foreach ($aiContext['context']['modules'] as $moduleName => $moduleData) {
                if (!empty($moduleData)) {
                    $context[] = "### {$moduleName} Module Context:";
                    $context[] = "```json";
                    $context[] = json_encode($moduleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    $context[] = "```";
                    $context[] = "";
                }
            }
        }

        // Smart search results
        if (!empty($aiContext['smart_search_results'])) {
            $context[] = "### Smart Search Results:";
            $context[] = "```json";
            $context[] = json_encode($aiContext['smart_search_results'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $context[] = "```";
            $context[] = "";
        }

        // User sentiment
        if (!empty($aiContext['user_sentiment'])) {
            $context[] = "### User Sentiment:";
            $context[] = json_encode($aiContext['user_sentiment'], JSON_UNESCAPED_UNICODE);
            $context[] = "";
        }

        return implode("\n", $context);
    }

    /**
     * Konuşma geçmişini formatla
     */
    protected function buildHistoryContext(array $conversationHistory): string
    {
        if (empty($conversationHistory)) {
            return '';
        }

        $context = [];
        $context[] = "## KONUŞMA GEÇMİŞİ (Son " . count($conversationHistory) . " mesaj)";
        $context[] = "";

        foreach ($conversationHistory as $message) {
            $role = $message['role'] ?? 'user';
            $content = $message['content'] ?? '';
            $roleLabel = $role === 'assistant' ? '🤖 AI' : '👤 Kullanıcı';
            $context[] = "**{$roleLabel}:** " . mb_substr($content, 0, 500) . (strlen($content) > 500 ? '...' : '');
        }

        $context[] = "";

        return implode("\n", $context);
    }
}
