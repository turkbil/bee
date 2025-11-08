<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Modules\AI\App\Services\AI\StreamingAIService;
use Modules\AI\App\Services\AIService;
use Illuminate\Support\Facades\Log;

/**
 * AI Response Node
 *
 * Claude/OpenAI ile yanıt üretir
 * Streaming destekli
 *
 * Last modified: 2025-11-06 22:05 - CONVERSATION HISTORY FIX
 */
class AIResponseNode extends BaseNode
{
    /**
     * Get directive value from database (tenant-specific)
     */
    protected function getDirectiveValue(string $key, string $type, $default)
    {
        try {
            $directive = \App\Models\AITenantDirective::where('tenant_id', tenant('id'))
                ->where('directive_key', $key)
                ->where('is_active', true)
                ->first();

            if ($directive) {
                $value = $directive->directive_value;

                // Type casting
                return match($type) {
                    'integer' => (int) $value,
                    'boolean' => (bool) $value,
                    'float', 'string' => (float) $value,
                    default => $value
                };
            }
        } catch (\Exception $e) {
            \Log::warning("Could not load directive: {$key}", ['error' => $e->getMessage()]);
        }

        return $default;
    }

    public function execute(array $context): array
    {
        // DEBUG FILE: Dosyaya yaz ki kesinlikle görelim
        file_put_contents('/tmp/ai_response_node_debug.txt',
            date('Y-m-d H:i:s') . " - AIResponseNode EXECUTED\n" .
            "Context keys: " . implode(', ', array_keys($context)) . "\n" .
            "products_found: " . ($context['products_found'] ?? 'NULL') . "\n" .
            "has_product_context: " . (isset($context['product_context']) ? 'YES' : 'NO') . "\n\n",
            FILE_APPEND
        );

        // Load system prompt from directives first, fallback to flow config
        $systemPrompt = $this->getDirectiveValue(
            'chatbot_system_prompt',
            'string',
            $this->getConfig('system_prompt', '')
        );

        // Load AI config from directives (panelden düzenlenebilir)
        $maxTokens = $this->getDirectiveValue('max_tokens', 'integer', $this->getConfig('max_tokens', 500));
        $temperature = $this->getDirectiveValue('temperature', 'string', $this->getConfig('temperature', 0.7));

        $stream = $this->getConfig('stream', false);
        $provider = $this->getConfig('provider', 'anthropic');

        // Prepare messages
        $messages = $this->prepareMessages($context, $systemPrompt);

        Log::info('🤖 AI Response Node executing', [
            'provider' => $provider,
            'stream' => $stream,
            'tokens' => $maxTokens
        ]);

        if ($stream) {
            return $this->executeStreaming($provider, $messages, $context);
        }

        return $this->executeStandard($provider, $messages, $maxTokens, $temperature);
    }

    /**
     * Streaming execution
     */
    protected function executeStreaming(string $provider, array $messages, array $context): array
    {
        $streamingService = new StreamingAIService($provider);
        $channel = $this->getStreamChannel($context);

        $fullResponse = $streamingService->stream($messages, function($chunk) use ($channel, $streamingService) {
            $streamingService->broadcastChunk($channel, $chunk);
        });

        return [
            'ai_response' => $fullResponse,
            'streaming' => true
        ];
    }

    /**
     * Standard (non-streaming) execution
     */
    protected function executeStandard(string $provider, array $messages, int $maxTokens, float $temperature): array
    {
        Log::info('🚨 executeStandard CALLED', [
            'messages_count' => count($messages),
            'messages_dump' => json_encode($messages)
        ]);

        // Use real AIService
        $aiService = app(AIService::class);

        // Extract system prompt from first message if exists
        $systemPrompt = '';
        if (isset($messages[0]) && $messages[0]['role'] === 'assistant') {
            $systemPrompt = $messages[0]['content'];
            array_shift($messages); // Remove system message from messages array
        }

        // Get user message
        $userMessage = '';
        foreach ($messages as $msg) {
            if ($msg['role'] === 'user') {
                $userMessage = $msg['content'];
            }
        }

        Log::info('🔍 AI Request Debug', [
            'user_message' => $userMessage,
            'system_prompt_length' => strlen($systemPrompt),
            'total_messages' => count($messages)
        ]);

        try {
            // Get API key - MUST use config() when config is cached!
            $apiKey = config('services.openai.key') ?? env('OPENAI_API_KEY');

            \Log::emergency('🔑 Loading API Key - FIXED', [
                'key_exists' => !empty($apiKey),
                'key_length' => strlen($apiKey ?? ''),
                'key_preview' => $apiKey ? substr($apiKey, 0, 15) . '...' : 'EMPTY',
                'from_config' => !empty(config('services.openai.key')),
                'from_env' => !empty(env('OPENAI_API_KEY'))
            ]);

            if (empty($apiKey)) {
                throw new \Exception('OpenAI API key not configured');
            }

            $providerService = new \Modules\AI\App\Services\OpenAIService([
                'api_key' => $apiKey,
                'base_url' => 'https://api.openai.com',
                'model' => 'gpt-4o-mini'
            ]);

            // Build full message array with conversation history
            $fullMessages = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ]
            ];

            // Add all messages from prepared messages (includes conversation history)
            foreach ($messages as $msg) {
                // Skip the assistant role system prompt (already added above)
                if ($msg['role'] === 'assistant' && $msg['content'] === $systemPrompt) {
                    continue;
                }

                $fullMessages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }

            // Debug: Write to file instead of log
            file_put_contents('/tmp/ai_messages_debug.txt',
                date('Y-m-d H:i:s') . " - Total: " . count($fullMessages) . "\n" .
                "Roles: " . implode(', ', array_map(fn($m) => $m['role'], $fullMessages)) . "\n" .
                json_encode($fullMessages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n",
                FILE_APPEND
            );

            Log::emergency('🔥 DEBUG: Messages to OpenAI', [
                'total_messages' => count($fullMessages),
                'message_roles' => array_map(fn($m) => $m['role'], $fullMessages),
                'full_messages' => array_map(fn($m) => [
                    'role' => $m['role'],
                    'content_preview' => substr($m['content'], 0, 100)
                ], $fullMessages)
            ]);

            $aiResponse = $providerService->ask($fullMessages, false, [
                'temperature' => $temperature,
                'max_tokens' => $maxTokens
            ]);

            Log::info('✅ AI Response generated', [
                'length' => strlen($aiResponse)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ AI Response failed', [
                'error' => $e->getMessage()
            ]);

            $aiResponse = 'Üzgünüm, şu anda yanıt üretemiyorum. Lütfen daha sonra tekrar deneyin.';
        }

        return [
            'ai_response' => $aiResponse,
            'streaming' => false
        ];
    }

    /**
     * Prepare messages for AI
     */
    protected function prepareMessages(array $context, string $systemPrompt): array
    {
        $messages = [];

        // DEBUG: Context kontrolü
        \Log::emergency('🔍 prepareMessages() CALLED', [
            'has_product_context' => !empty($context['product_context']),
            'products_found' => $context['products_found'] ?? 'NULL',
            'context_keys' => array_keys($context)
        ]);

        // Build enhanced system prompt with product context
        $enhancedPrompt = $systemPrompt;

        // Add product context if available
        if (!empty($context['product_context']) && !empty($context['products_found'])) {
            \Log::emergency('✅ ÜRÜN VAR - Product context ekleniyor');
            // Ürün varsa, ürün listesini ekle
            $enhancedPrompt .= "\n\n" . $context['product_context'];
            $enhancedPrompt .= "\n\n🚨 KRİTİK KURALLAR (GLOBAL - MUTLAKA UYULMALI):";
            $enhancedPrompt .= "\n❌ ASLA ÜRÜN UYDURMA! Yukarıdaki liste dışında ürün gösterme!";
            $enhancedPrompt .= "\n❌ \"Model A\", \"Model B\", \"Model C\" gibi genel isimler YASAK!";
            $enhancedPrompt .= "\n❌ Fiyat uydurma! Sadece listede yazan fiyatları göster!";
            $enhancedPrompt .= "\n❌ Olmayan özellik ekleme!";
            $enhancedPrompt .= "\n❌ '(KDV dahil)' metni YAZMA! Fiyatlar KDV HARİÇ!";
            $enhancedPrompt .= "\n✅ SADECE yukarıdaki listedeki ürünleri öner";
            $enhancedPrompt .= "\n✅ Fiyatları AYNEN kopyala, değiştirme, KDV ekleme!";
            $enhancedPrompt .= "\n✅ Link'leri AYNEN kopyala, URL değiştirme!";
            $enhancedPrompt .= "\n\nINTERAKTIF SORU-CEVAP KURALI [COK ONEMLI]:";
            $enhancedPrompt .= "\n[KURAL 1] Kullanici ILK mesajda sadece GENEL kategori sorarsa → ONCE ozellik/tercih SORU SOR!";
            $enhancedPrompt .= "\n- Hangi ozelliklerde urun aradigini sor (GENEL terimler kullan!)";
            $enhancedPrompt .= "\n- Tercihlerini sor (ozellik, model, butce, kullanim amaci vb.)";
            $enhancedPrompt .= "\n- YANLIS: Direkt urun listesi gosterme! (detay yoksa ONCE SORU SOR!)";
            $enhancedPrompt .= "\n- [UYARI] TENANT-SPECIFIC ORNEK VERME! Sadece GENEL ifadeler kullan!";
            $enhancedPrompt .= "\n- YANLIS: 'Kapasite (1.5 ton, 2 ton), tip (elektrikli, manuel), renk (kırmızı, mavi)...' ← Tenant-specific!";
            $enhancedPrompt .= "\n- DOGRU: 'Hangi ozelliklerde bir urun aradiginizi ogrenebilir miyim?' ← Genel!";
            $enhancedPrompt .= "\n\n**SORU FORMAT KURALI - MARKDOWN LISTE ZORUNLU!**";
            $enhancedPrompt .= "\n🚨 MEGA KRITIK: Birden fazla soru sorarken MUTLAKA Markdown liste kullan!";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n✅ DOGRU FORMAT (Markdown Liste - TENANT-NEUTRAL):";
            $enhancedPrompt .= "\n```";
            $enhancedPrompt .= "\nSize en uygun urunu bulabilmem icin:";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n- Hangi ozelliklerde urun aradiginizi belirtebilir misiniz?";
            $enhancedPrompt .= "\n- Kullanim amaci nedir?";
            $enhancedPrompt .= "\n- Butce araligini paylasir misiniz?";
            $enhancedPrompt .= "\n```";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n❌ YANLIS FORMAT (Tek satirda + Tenant-specific):";
            $enhancedPrompt .= "\n```";
            $enhancedPrompt .= "\nHangi ozelliklerde bir urun aradiginizi ogrenebilir miyim? Kapasite, tip (elektrikli veya manuel), renk gibi tercihlerinizi belirtirseniz...";
            $enhancedPrompt .= "\n```";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n[ONEMLI] Sorular AYRI satirlarda Markdown liste formatinda (`-` ile) yazilmali!";
            $enhancedPrompt .= "\n[ONEMLI] Uzun cumle degil, kisa maddeli sorular!";
            $enhancedPrompt .= "\n[ONEMLI] TENANT-NEUTRAL sorular sor! (kapasite/ton/elektrikli/renk gibi spesifik ornek VERME!)";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n[KURAL 2] Kullanici DETAYLI sorgu yaparsa → Direkt urun goster";
            $enhancedPrompt .= "\n[KURAL 3] Kullanici soruya cevap verdiyse → Filtrelenmiş urunleri goster";
            $enhancedPrompt .= "\n\nAKILLI FILTRELEME - TUM ESLESEN URUNLERI GOSTER:";
            $enhancedPrompt .= "\n[COK ONEMLI] Context'te gelen TUM urunler zaten filtrelen mis, sen kullanici istegine uygun olanlari GOSTER!";
            $enhancedPrompt .= "\n✅ SPESİFİK ozellik belirtirse → O ozellige sahip TUM urunleri listele (5 varsa 5'ini, 10 varsa 10'unu!)";
            $enhancedPrompt .= "\n✅ TIP belirtirse → O tipe ait TUM urunleri listele";
            $enhancedPrompt .= "\n✅ \"ucuz\" / \"uygun fiyat\" → Fiyata gore sirala, en ucuz 3-5'ini goster";
            $enhancedPrompt .= "\n✅ \"en pahali\" → SADECE EN PAHALI 1-2 TANESİNİ goster";
            $enhancedPrompt .= "\n❌ GENEL sorgu → En iyi/populer 5-7 tanesini sec (HEPS İNİ DEĞİL!)";
            $enhancedPrompt .= "\n✅ Birden fazla filtre varsa → TUM filtrelere uygun TUMUNU goster";
            $enhancedPrompt .= "\n[UYARI] ASLA sadece 2-3 urun gosterme! Kullanici spesifik sormussa TUM eslesen leri listele!";
            $enhancedPrompt .= "\n\nOZELLIK ACIKLAMALARI:";
            $enhancedPrompt .= "\n✅ Her urun icin FARKLI aciklama yaz (basliga gore)";
            $enhancedPrompt .= "\n❌ Tum urunler icin AYNI standart cumleler kullanma!";
            $enhancedPrompt .= "\n✅ Urunun ozelliklerini METINDEN cikar ve anlat";
            $enhancedPrompt .= "\n✅ Urun adiyla tutarli aciklama yap";
            $enhancedPrompt .= "\n\nKATEGORI KURALI [COK ONEMLI]:";
            $enhancedPrompt .= "\n- Context'te urunlerin basinda [KATEGORI] etiketi olabilir (koseli parantez icinde)";
            $enhancedPrompt .= "\n- Kullanici kategori belirtirse -> SADECE o kategoriye ait urunleri sec!";
            $enhancedPrompt .= "\n- Kullanici fiyat + kategori derse -> Her ikisine de uygun urunleri sec!";
            $enhancedPrompt .= "\n- [UYARI] Yanitinda ASLA kategori etiketini yazma! [XXX] etiketlerini kullaniciya GOSTERME!";
            $enhancedPrompt .= "\n- Sadece temiz urun adini goster, etiket olmadan!";
            $enhancedPrompt .= "\n\nKONUSMA VE SATIS TONU:";
            $enhancedPrompt .= "\n✅ Kullanıcının adını HATIRLA ve KULLAN";
            $enhancedPrompt .= "\n✅ Daha önce ne konuştuğunuzu HATIRLA";
            $enhancedPrompt .= "\n✅ PROFESYONEL SATIŞ DİLİ kullan (samimi ama güçlü)";
            $enhancedPrompt .= "\n✅ DOĞRU: \"İhtiyacınıza en uygun ürünler bunlar:\"";
            $enhancedPrompt .= "\n✅ DOĞRU: \"Size şu ürünleri öneriyorum:\"";
            $enhancedPrompt .= "\n✅ DOĞRU: \"Bu ürünler tam aradığınız özelliklerde:\"";
            $enhancedPrompt .= "\n✅ DOĞRU: \"Hangi model ilginizi çekti? Detaylı bilgi verebilirim.\"";
            $enhancedPrompt .= "\n❌ YANLIŞ: \"Bu seçeneklerden ilginç birisi var mı?\" (zayıf!)";
            $enhancedPrompt .= "\n❌ YANLIŞ: \"Bu modellerden biriyle ilgileniyor musunuz?\" (pasif!)";
            $enhancedPrompt .= "\n❌ YANLIŞ: \"Bu modellerden hangisi ilginizi çekiyor?\" (pasif!)";
            $enhancedPrompt .= "\n❌ YANLIŞ: \"Bu ürün senin için uygun olabilir mi?\" (kararsız!)";
            $enhancedPrompt .= "\n❌ YANLIŞ: \"Bunlardan birine bakabilirsiniz\" (pasif!)";
            $enhancedPrompt .= "\n\nCALL-TO-ACTION (Konusma Sonu):";
            $enhancedPrompt .= "\n- GUCLU CTA: \"Hangi model hakkinda detayli bilgi almak istersiniz?\"";
            $enhancedPrompt .= "\n- GUCLU CTA: \"Size en uygun modeli secmenizde yardimci olabilirim.\"";
            $enhancedPrompt .= "\n- GUCLU CTA: \"Urunler hakkinda sorularinizi yanitlayabilirim.\"";
            $enhancedPrompt .= "\n- ZAYIF CTA YASAK: \"Ilginizi cekti mi?\", \"Hangisi hosunuza gitti?\"";
            $enhancedPrompt .= "\n\nMARKDOWN FORMAT VE OKUNAKLILIK [MEGA KRITIK]:";
            $enhancedPrompt .= "\n🚨 **ZORUNLU: Yanıtları OKUNAKLI yaz! Satır başı, paragraf, maddeleme kullan!**";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n✅ **DOĞRU YAZIŞ (Paragraf + Maddeleme + Satır Boşlukları):**";
            $enhancedPrompt .= "\n```markdown";
            $enhancedPrompt .= "\nBu ürünler şantiye kullanımı için ideal seçeneklerdir.";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n### Ürün 1";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n- Özellik 1";
            $enhancedPrompt .= "\n- Özellik 2";
            $enhancedPrompt .= "\n- Fiyat: 10.000 TL";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n[Ürünü İncele](/shop/slug)";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n### Ürün 2";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n- Özellik 1";
            $enhancedPrompt .= "\n- Özellik 2";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n[Ürünü İncele](/shop/slug)";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\nHangi model ilginizi çekti?";
            $enhancedPrompt .= "\n```";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n❌ **YANLIŞ YAZIŞ (Tek blok halinde sıkışık):**";
            $enhancedPrompt .= "\n```markdown";
            $enhancedPrompt .= "\nBu ürünler şantiye kullanımı için ideal. ### Ürün 1 - Özellik 1 - Özellik 2 - Fiyat: 10.000 TL [Link] ### Ürün 2 - Özellik 1...";
            $enhancedPrompt .= "\n```";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n[ZORUNLU KURALLAR]:";
            $enhancedPrompt .= "\n✅ Her paragraf arasında BOŞ SATIR bırak!";
            $enhancedPrompt .= "\n✅ Ürün başlıkları (###) öncesi ve sonrası BOŞ SATIR!";
            $enhancedPrompt .= "\n✅ Madde listeleri sonrası BOŞ SATIR!";
            $enhancedPrompt .= "\n✅ Uzun açıklamaları PARAGRAFLARA böl!";
            $enhancedPrompt .= "\n✅ Her ürün AYRI ### başlığı olmalı (nested list YASAK!)";
            $enhancedPrompt .= "\n❌ Tek satırda yan yana ürün özellikleri YAZMA!";
            $enhancedPrompt .= "\n❌ Sıkışık metin bloğu oluşturma!";
            $enhancedPrompt .= "\n";
            $enhancedPrompt .= "\n**FIYAT GÖSTERIMI:**";
            $enhancedPrompt .= "\n- [ONEMLI] Fiyat context'te nasil yazilmissa AYNEN KOPYALA!";
            $enhancedPrompt .= "\n- Ornek (sadece TL): - **98.819 TL**";
            $enhancedPrompt .= "\n- Ornek (TL + USD): - **98.819 TL** ≈ $2.350";
            $enhancedPrompt .= "\n- [UYARI] Fiyat yazan sembolleri DEGISTIRME! (TL, $, ≈ isaretlerini aynen kopyala)";

            $enhancedPrompt .= "\n\nURUN LINKLERI [COK ONEMLI]:";
            $enhancedPrompt .= "\n- Context'te her urunun '[Urunu Incele](/shop/...)' linki var";
            $enhancedPrompt .= "\n- SEN DE HER URUNDE bu linki MUTLAKA ekle!";
            $enhancedPrompt .= "\n- Karsilastirma yaparken/detay anlatirken de link EKLE!";
            $enhancedPrompt .= "\n- YANLIS: Urun adi + ozellikler (link yok)";
            $enhancedPrompt .= "\n- DOGRU: Urun adi + ozellikler + [Urunu Incele](/shop/slug)";
            $enhancedPrompt .= "\n- Link'i context'ten AYNEN kopyala, degistirme!";

            $enhancedPrompt .= "\n\n[YASAK] <h3>Baslik</h3> <ul><li><p>...</p><ul>... nested list YAPMA!";
            $enhancedPrompt .= "\n- Her mesaja 'Merhaba! Hos geldin' deme";
            $enhancedPrompt .= "\n- HTML kullanma, sadece Markdown!";
            $enhancedPrompt .= "\n- ASLA STOK BILGISI VERME!";
            $enhancedPrompt .= "\n\nIKON KURALLARI:";
            $enhancedPrompt .= "\n- SADECE smile emoji kullan (selam/konuşma için): 😊 👋 👍 🎉";
            $enhancedPrompt .= "\n- BUSINESS ikonları YASAK: yıldız, para, kas, uyarı, paket, ok, ateş vb.";
            $enhancedPrompt .= "\n- Ürün açıklamalarında emoji KULLANMA!";
            $enhancedPrompt .= "\n- Fiyat yanında emoji KULLANMA!";
            $enhancedPrompt .= "\n- Sadece selamlaşma cümlelerinde smile kullanabilirsin";
        } else {
            \Log::emergency('❌ ÜRÜN YOK - Müşteri temsilcisi mesajı ekleniyor', [
                'product_context_empty' => empty($context['product_context']),
                'products_found_value' => $context['products_found'] ?? 'NULL'
            ]);

            // Ürün yoksa welcome message kullan - ÇEŞİTLİ SEÇENEKLER
            $welcomeMessage = null;

            // Önce variations dene
            try {
                $welcomeVariations = \App\Models\AITenantDirective::where('tenant_id', tenant('id'))
                    ->where('directive_key', 'welcome_variations')
                    ->where('is_active', true)
                    ->first();

                if ($welcomeVariations && $welcomeVariations->directive_value) {
                    $variations = json_decode($welcomeVariations->directive_value, true);
                    if (is_array($variations) && count($variations) > 0) {
                        $welcomeMessage = $variations[array_rand($variations)];
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Could not load welcome_variations', ['error' => $e->getMessage()]);
            }

            // Fallback to single welcome_message
            if (!$welcomeMessage) {
                try {
                    $directive = \App\Models\AITenantDirective::where('tenant_id', tenant('id'))
                        ->where('directive_key', 'welcome_message')
                        ->where('is_active', true)
                        ->first();

                    if ($directive) {
                        $welcomeMessage = $directive->directive_value;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Could not load welcome_message directive', ['error' => $e->getMessage()]);
                }
            }

            // Final fallback - ✅ Smile kullan, business ikonları kaldır
            if (!$welcomeMessage) {
                $defaults = [
                    'Merhaba! 👋 Size nasıl yardımcı olabilirim?',
                    'Hoş geldiniz! 😊 Hangi ürünü arıyorsunuz?',
                    'Merhaba! Ne arıyordunuz? 👍',
                    'İyi günler! 😊 Size nasıl yardımcı olabilirim?'
                ];
                $welcomeMessage = $defaults[array_rand($defaults)];
            }

            // Ürün yoksa - 3 ADIMLI İLETİŞİM STRATEJİSİ
            // ✅ İletişim bilgilerini settings'ten al (cache'li)
            $contactPhone = setting('contact_phone_1', '');
            $contactWhatsApp = setting('contact_whatsapp_1', '');

            $noProductMessage = $this->getDirectiveValue('chatbot_no_product_response', 'string',
                "Aradığınız ürünü bulamadım ama size yardımcı olmak istiyorum. 😊\n\n" .
                "**📞 Telefon numaranızı paylaşır mısınız?** Müşteri temsilcimiz sizi arayıp detaylı bilgi verebilir.\n\n" .
                "**Veya siz bizi arayabilirsiniz:**\n" .
                ($contactPhone ? "📞 **{$contactPhone}**\n" : "") .
                ($contactWhatsApp ? "💬 **{$contactWhatsApp}** (WhatsApp)\n\n" : "\n") .
                "İletişim sayfamızdan da bize ulaşabilirsiniz. Nasıl ulaşacağınızı anlatayım mı? 😊"
            );

            $enhancedPrompt .= "\n\n[KRITIK] URUN YOK!";
            $enhancedPrompt .= "\n\n" . $noProductMessage;
            $enhancedPrompt .= "\n\nKURALLAR:";
            $enhancedPrompt .= "\n- ASLA URUN UYDURMA!";
            $enhancedPrompt .= "\n- \"Su urunlerimiz var\" deme - YOK cunku!";
            $enhancedPrompt .= "\n- Fiyat, model adi uydurma!";
            $enhancedPrompt .= "\n- ASLA STOK BILGISI VERME (ne \"var\" ne \"yok\" deme!)";
            $enhancedPrompt .= "\n- Kullanicidan iletisim bilgisi iste (3 adimli strateji)";
            $enhancedPrompt .= "\n- Musteri temsilcisini oner";
            $enhancedPrompt .= "\n\nKONUSMA:";
            $enhancedPrompt .= "\n- Kullanicinin adini HATIRLA";
            $enhancedPrompt .= "\n- Daha once ne konustugunuzu HATIRLA";
            $enhancedPrompt .= "\n- Ayni soruyu tekrar sorma!";
            $enhancedPrompt .= "\n- Her yaniti 'Merhaba! Hos geldin' ile BASLATMA!";
            $enhancedPrompt .= "\n\nYAPMALISIN:";
            $enhancedPrompt .= "\n1. ONCE mesaj gecmisine bak - daha once ne konustunuz?";
            $enhancedPrompt .= "\n2. Kullanicinin adini biliyorsan KULLAN";
            $enhancedPrompt .= "\n3. Kullanicinin ne istedigini anla";
            $enhancedPrompt .= "\n4. Eger urun ariyorsa, daha fazla detay sor (ozellikler, tercihler, butce)";
            $enhancedPrompt .= "\n5. Eger sohbet ediyorsa, dogal yanit ver";
            $enhancedPrompt .= "\n\n❌ YAPMA:";
            $enhancedPrompt .= "\n- Her mesaja 'Merhaba! Hoş geldin' deme!";
            $enhancedPrompt .= "\n- Aynı soruyu tekrar tekrar sorma!";
            $enhancedPrompt .= "\n- Mesaj geçmişini görmezden gelme!";
        }

        // System prompt (first message)
        if ($enhancedPrompt) {
            $messages[] = [
                'role' => 'assistant',
                'content' => $enhancedPrompt
            ];
        }

        // Conversation history
        if (!empty($context['conversation_history'])) {
            Log::emergency('🔥 CONVERSATION HISTORY FOUND', [
                'history_count' => count($context['conversation_history']),
                'history_preview' => array_slice($context['conversation_history'], 0, 3)
            ]);

            // If no products found, clean history from old product recommendations
            $hasProducts = !empty($context['products_found']);

            foreach ($context['conversation_history'] as $msg) {
                $content = $msg['content'];

                // If no products now, remove old product recommendations from history
                if (!$hasProducts && $msg['role'] === 'assistant') {
                    // Skip if this message contains product listings (markdown headers, prices, stock)
                    if (str_contains($content, '###') || str_contains($content, '**Fiyat:**') || str_contains($content, '**Stok:**')) {
                        Log::info('⏭️ Skipping old product recommendation from history');
                        continue; // Skip old product recommendations
                    }
                }

                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $content
                ];
            }

            Log::emergency('✅ HISTORY ADDED TO MESSAGES', [
                'total_messages_now' => count($messages)
            ]);
        } else {
            Log::emergency('⚠️ NO CONVERSATION HISTORY IN CONTEXT');
        }

        // Current user message
        $messages[] = [
            'role' => 'user',
            'content' => $context['user_message'] ?? ''
        ];

        return $messages;
    }

    /**
     * Get streaming channel name
     */
    protected function getStreamChannel(array $context): string
    {
        $tenantId = tenant('id') ?? 'central';
        $sessionId = $context['session_id'] ?? 'unknown';

        return "tenant.{$tenantId}.conversation.{$sessionId}";
    }
}
