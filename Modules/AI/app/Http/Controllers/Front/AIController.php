<?php
namespace Modules\AI\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\Conversation;
use App\Services\ThemeService;
use App\Services\AI\AIServiceManager;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    protected $aiService;
    protected $themeService;
    protected $aiServiceManager;

    public function __construct(AIService $aiService, ThemeService $themeService, AIServiceManager $aiServiceManager)
    {
        $this->aiService = $aiService;
        $this->themeService = $themeService;
        $this->aiServiceManager = $aiServiceManager;
    }

    public function index()
    {
        $conversations = $this->aiService->conversations()->getConversations(10);
        
        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'ai');
            return view($viewPath, compact('conversations'));
        } catch (\Exception $e) {
            // Hatayı logla
            \Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('ai::front.index', compact('conversations'));
        }
    }

    public function chat($id = null)
    {
        $conversation = null;
        
        if ($id) {
            $conversation = Conversation::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }
        
        $conversations = $this->aiService->conversations()->getConversations(10);
        
        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('chat', 'ai');
            return view($viewPath, compact('conversation', 'conversations'));
        } catch (\Exception $e) {
            // Hatayı logla
            \Log::error("Theme Error: " . $e->getMessage());
            
            // Fallback view'a yönlendir
            return view('ai::front.chat', compact('conversation', 'conversations'));
        }
    }

    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|exists:ai_conversations,id'
        ]);
        
        $message = $request->message;
        $conversationId = $request->conversation_id;
        
        if ($conversationId) {
            $conversation = Conversation::where('id', $conversationId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $response = $this->aiService->conversations()->getAIResponse($conversation, $message);
        } else {
            // Yeni konuşma oluştur
            $title = substr($message, 0, 30) . '...';
            $conversation = $this->aiService->conversations()->createConversation($title);
            
            $response = $this->aiService->conversations()->getAIResponse($conversation, $message);
        }
        
        return response()->json([
            'success' => true,
            'response' => $response,
            'conversation_id' => $conversation->id
        ]);
    }

    public function createConversation(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'prompt_id' => 'nullable|exists:ai_prompts,id'
        ]);
        
        $conversation = $this->aiService->conversations()->createConversation(
            $request->title,
            $request->prompt_id
        );
        
        return response()->json([
            'success' => true,
            'conversation' => $conversation
        ]);
    }

    public function deleteConversation($id)
    {
        $conversation = Conversation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $this->aiService->conversations()->deleteConversation($conversation);
        
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * AI Özellikler Sayfası
     */
    public function features()
    {
        // Tüm entegrasyonları al
        $integrations = $this->aiServiceManager->getRegisteredIntegrations();
        
        // Mevcut tenant'ın token bilgilerini al
        $tenant = tenant();
        if (!$tenant) {
            // Eğer tenant yoksa (admin panelde), ID 1 tenant'ı kullan
            $tenant = \App\Models\Tenant::find(1);
            if (!$tenant) {
                // Hiç tenant yoksa fallback değerler
                $tenant = (object)[
                    'ai_tokens_balance' => 0,
                    'ai_tokens_used_this_month' => 0,
                    'ai_monthly_token_limit' => 0,
                    'ai_last_used_at' => null
                ];
            }
        }
        
        $tokenStatus = [
            'remaining_tokens' => $tenant->ai_tokens_balance ?? 0,
            'total_tokens' => $tenant->ai_tokens_balance ?? 0,
            'daily_usage' => 0, // Günlük kullanım hesaplanabilir
            'monthly_usage' => $tenant->ai_tokens_used_this_month ?? 0,
            'provider' => 'deepseek',
            'provider_active' => true
        ];
        
        // Mevcut ve potansiyel özellikler
        $features = $this->getAIFeatures();
        
        // Admin panel için direkt admin view kullan
        return view('ai::admin.features', compact('integrations', 'tokenStatus', 'features'));
    }

    /**
     * AI özellik test metodu - basitleştirilmiş test modu
     */
    public function testFeature(\Illuminate\Http\Request $request)
    {
        try {
            $request->validate([
                'feature_name' => 'required|string',
                'input_text' => 'required|string|max:2000',
                'tenant_id' => 'required|integer',
                'real_ai' => 'boolean'
            ]);

            $featureName = $request->feature_name;
            $inputText = $request->input_text;
            $tenantId = (int) $request->tenant_id;
            $useRealAI = $request->boolean('real_ai', false);

            \Log::info('AI Feature Test başlıyor', [
                'feature' => $featureName,
                'input_length' => strlen($inputText),
                'tenant_id' => $tenantId,
                'real_ai' => $useRealAI
            ]);

            $startTime = microtime(true);

            if ($useRealAI) {
                // Gerçek AI çağrısı yap
                $result = $this->processRealAIFeature($featureName, $inputText, $tenantId);
            } else {
                // Demo test modu - konuşma kaydı oluştur
                $result = $this->processDemoFeature($featureName, $inputText, $tenantId);
            }
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000);

            // Token bilgilerini al
            $tenant = \App\Models\Tenant::find($tenantId);
            
            return response()->json([
                'success' => $result['success'],
                'ai_result' => $result['ai_result'],
                'tokens_used' => $result['tokens_used'],
                'remaining_tokens' => $tenant ? $tenant->ai_tokens_balance : 0,
                'processing_time' => $processingTime,
                'demo_mode' => $result['demo_mode'] ?? false,
                'message' => $result['message'] ?? null
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Feature Test hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Test hatası: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Gerçek AI API çağrısı yap - AIService kullanarak
     */
    private function processRealAIFeature(string $featureName, string $inputText, int $tenantId): array
    {
        try {
            // Tenant'ı al
            $tenant = \App\Models\Tenant::find($tenantId);
            if (!$tenant) {
                throw new \Exception('Tenant bulunamadı: ' . $tenantId);
            }

            // Token tahmini
            $estimatedTokens = max(50, (int)(strlen($inputText) / 4));
            
            // Token kontrolü
            if ($tenant->ai_tokens_balance < $estimatedTokens) {
                return [
                    'success' => false,
                    'message' => 'Yetersiz token bakiyesi. Gerekli: ' . $estimatedTokens . ', Mevcut: ' . $tenant->ai_tokens_balance,
                    'ai_result' => '',
                    'tokens_used' => 0
                ];
            }

            // AI prompt oluştur
            $prompt = $this->generatePromptForFeature($featureName, $inputText);
            
            // AIService'i kullan (aynı şekilde admin panelde çalışan)
            $aiResponse = $this->aiService->ask($prompt, [
                'context' => "AI özellik testi: {$featureName}",
                'module' => 'ai_features',
                'entity_id' => $tenantId
            ]);

            if (!$aiResponse || empty($aiResponse)) {
                throw new \Exception('AI servisi yanıt vermedi');
            }

            // Token hesaplama
            $actualTokens = max(20, (int)(strlen($prompt . $aiResponse) / 4));
            
            // Konuşma kaydı oluştur (conversation history için)
            $conversation = \Modules\AI\App\Models\Conversation::create([
                'title' => "Test: {$featureName}",
                'type' => 'feature_test',
                'feature_name' => $featureName,
                'is_demo' => false,
                'user_id' => auth()->id(),
                'tenant_id' => $tenantId,
                'total_tokens_used' => $actualTokens,
                'metadata' => [
                    'test_type' => 'real_ai',
                    'input_length' => strlen($inputText),
                    'output_length' => strlen($aiResponse),
                    'processing_time' => round((microtime(true) - (microtime(true) - 1)) * 1000)
                ],
                'status' => 'active'
            ]);

            // Kullanıcı mesajı kaydet
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'content' => $inputText,
                'tokens' => (int)(strlen($inputText) / 4),
                'prompt_tokens' => (int)(strlen($inputText) / 4),
                'completion_tokens' => 0,
                'model_used' => 'user_input',
                'processing_time_ms' => 0,
                'message_type' => 'test',
                'metadata' => [
                    'feature_test' => $featureName,
                    'is_demo' => false
                ]
            ]);

            // AI yanıtını kaydet
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $aiResponse,
                'tokens' => $actualTokens,
                'prompt_tokens' => (int)(strlen($prompt) / 4),
                'completion_tokens' => (int)(strlen($aiResponse) / 4),
                'model_used' => 'deepseek-chat',
                'processing_time_ms' => round((microtime(true) - (microtime(true) - 1)) * 1000),
                'message_type' => 'test',
                'metadata' => [
                    'feature_test' => $featureName,
                    'is_demo' => false,
                    'tenant_id' => $tenantId
                ]
            ]);

            // Manuel token tüketimi ve kayıt (AIService tenant() bulamadığı için)
            \Log::info('Token tüketimi başlıyor', [
                'tenant_id' => $tenantId,
                'old_balance' => $tenant->ai_tokens_balance,
                'tokens_to_use' => $actualTokens,
                'conversation_id' => $conversation->id
            ]);
            
            // Tenant bakiyesini güncelle
            $tenant->decrement('ai_tokens_balance', $actualTokens);
            
            // Güncellenmiş bakiyeyi yenile
            $tenant->refresh();
            
            \Log::info('Token tüketimi yapıldı', [
                'tenant_id' => $tenantId,
                'new_balance' => $tenant->ai_tokens_balance,
                'tokens_used' => $actualTokens,
                'conversation_id' => $conversation->id
            ]);
            
            // AI token kullanım kaydı oluştur
            $usageRecord = \Modules\AI\App\Models\AITokenUsage::create([
                'tenant_id' => $tenantId,
                'user_id' => auth()->id(),
                'tokens_used' => $actualTokens,
                'prompt_tokens' => (int)(strlen($prompt) / 4),
                'completion_tokens' => (int)(strlen($aiResponse) / 4),
                'usage_type' => 'feature_test',
                'model' => 'deepseek-chat',
                'purpose' => 'ai_feature_test',
                'description' => $featureName . ' test - ' . substr($inputText, 0, 50),
                'metadata' => json_encode([
                    'feature_name' => $featureName,
                    'input_length' => strlen($inputText),
                    'response_length' => strlen($aiResponse),
                    'estimated_tokens' => $estimatedTokens,
                    'actual_tokens' => $actualTokens
                ]),
                'used_at' => now()
            ]);
            
            \Log::info('Usage kaydı oluşturuldu', [
                'usage_id' => $usageRecord->id,
                'tenant_id' => $tenantId,
                'tokens_used' => $actualTokens
            ]);

            // Cache temizle (header dropdown ve features sayfası için)
            \Cache::forget("ai_settings_global");
            \Cache::forget("tenant_{$tenantId}_token_stats");

            // AI yanıtını formatla
            $formattedResult = $this->formatAIResponseAsHTML($aiResponse, $featureName);

            return [
                'success' => true,
                'ai_result' => $formattedResult,
                'tokens_used' => $actualTokens,
                'new_balance' => $tenant->ai_tokens_balance,
                'message' => 'Gerçek AI analizi tamamlandı!'
            ];

        } catch (\Exception $e) {
            \Log::error('Real AI Feature Test hatası', [
                'error' => $e->getMessage(),
                'feature' => $featureName,
                'tenant_id' => $tenantId
            ]);

            return [
                'success' => false,
                'message' => 'AI servisi hatası: ' . $e->getMessage(),
                'ai_result' => '',
                'tokens_used' => 0
            ];
        }
    }

    /**
     * Demo test işlemi (conversation kayıtlı)
     */
    private function processDemoFeature(string $featureName, string $inputText, int $tenantId): array
    {
        $demoResult = $this->generateDemoResult($featureName, $inputText);
        $tokensUsed = max(10, (int)(strlen($inputText) / 8));
        
        try {
            // Demo konuşma kaydı oluştur
            $conversation = \Modules\AI\App\Models\Conversation::create([
                'title' => "Demo Test: {$featureName}",
                'type' => 'feature_test',
                'feature_name' => $featureName,
                'is_demo' => true,
                'user_id' => auth()->id(),
                'tenant_id' => $tenantId,
                'total_tokens_used' => 0, // Demo test token tüketmez
                'metadata' => [
                    'test_type' => 'demo',
                    'input_length' => strlen($inputText),
                    'output_length' => strlen($demoResult),
                    'processing_time' => rand(150, 500) // Simüle edilmiş süre
                ],
                'status' => 'active'
            ]);

            // Kullanıcı mesajı kaydet
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'content' => $inputText,
                'tokens' => 0, // Demo test token tüketmez
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'model_used' => 'demo_input',
                'processing_time_ms' => 0,
                'message_type' => 'test',
                'metadata' => [
                    'feature_test' => $featureName,
                    'is_demo' => true
                ]
            ]);

            // Demo AI yanıtını kaydet
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $demoResult,
                'tokens' => 0, // Demo test token tüketmez
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'model_used' => 'demo_ai',
                'processing_time_ms' => rand(150, 500),
                'message_type' => 'test',
                'metadata' => [
                    'feature_test' => $featureName,
                    'is_demo' => true,
                    'tenant_id' => $tenantId
                ]
            ]);

            \Log::info('Demo test kaydedildi', [
                'conversation_id' => $conversation->id,
                'feature' => $featureName,
                'tenant_id' => $tenantId
            ]);

        } catch (\Exception $e) {
            \Log::error('Demo test kayıt hatası: ' . $e->getMessage());
        }

        return [
            'success' => true,
            'ai_result' => $demoResult,
            'tokens_used' => $tokensUsed,
            'demo_mode' => true
        ];
    }

    /**
     * Demo AI sonucu üret (test modu)
     */
    private function generateDemoResult(string $featureName, string $inputText): string
    {
        $demoResults = [
            'İçerik Oluşturma' => "
                <strong>🤖 İçerik Oluşturma - AI Analizi:</strong><br><br>
                <strong>📝 '{$inputText}' Konulu Blog Yazısı</strong><br><br>
                
                <strong>Önerilen Başlık:</strong><br>
                • {$inputText}: Kapsamlı Rehber ve İpuçları<br><br>
                
                <strong>Ana Başlıklar:</strong><br>
                • {$inputText} Nedir?<br>
                • {$inputText} Faydaları<br>
                • {$inputText} Kullanım Alanları<br>
                • Pratik Öneriler ve İpuçları<br><br>
                
                <strong>Tahmini Kelime Sayısı:</strong> 800-1200 kelime<br>
                <strong>SEO Skoru:</strong> %85 (Yüksek)<br>
                <strong>Okunabilirlik:</strong> Orta seviye
            ",
            
            'Başlık Alternatifleri' => "
                <strong>🤖 Başlık Alternatifleri - AI Analizi:</strong><br><br>
                
                <strong>'{$inputText}' için 5 SEO Dostu Başlık:</strong><br><br>
                
                <strong>1.</strong> {$inputText}: 2024'te Bilmeniz Gereken Her Şey<br>
                <em>CTR Potansiyeli: %8.5 - Yıl belirtimi tıklanma artırır</em><br><br>
                
                <strong>2.</strong> {$inputText} Hakkında Merak Ettikleriniz<br>
                <em>CTR Potansiyeli: %7.2 - Merak uyandırıcı</em><br><br>
                
                <strong>3.</strong> 10 Adımda {$inputText} Rehberi<br>
                <em>CTR Potansiyeli: %9.1 - Sayısal liste popüler</em><br><br>
                
                <strong>4.</strong> {$inputText}: Yeni Başlayanlar İçin Tam Kılavuz<br>
                <em>CTR Potansiyeli: %8.8 - Hedef kitle net</em><br><br>
                
                <strong>5.</strong> {$inputText} ile İlgili Sık Yapılan 7 Hata<br>
                <em>CTR Potansiyeli: %9.3 - Problem odaklı başlık</em>
            ",
            
            'SEO Analizi' => "
                <strong>🤖 SEO Analizi - AI Sonucu:</strong><br><br>
                
                <strong>📊 İçerik Analizi:</strong><br>
                • Kelime Sayısı: " . str_word_count($inputText) . " kelime<br>
                • Anahtar Kelime Yoğunluğu: %2.3 (Uygun)<br>
                • Başlık Yapısı: Düzenlenebilir<br><br>
                
                <strong>🎯 SEO Önerileri:</strong><br>
                • Meta description eklenmeli (155 karakter)<br>
                • H2-H3 başlıkları düzenlenmeli<br>
                • İç linkler artırılabilir<br>
                • Alt etiketleri optimize edilmeli<br><br>
                
                <strong>📈 Ranking Potansiyeli:</strong> Orta-Yüksek<br>
                <strong>🔍 Tahmini Görünürlük:</strong> %73
            ",
            
            'Ton Analizi' => "
                <strong>🤖 Ton Analizi - AI Değerlendirmesi:</strong><br><br>
                
                <strong>📝 Genel Ton:</strong> Profesyonel<br>
                <strong>😊 Duygu Durumu:</strong> Pozitif (%78)<br>
                <strong>🎯 Hedef Kitle Uyumu:</strong> Yüksek<br>
                <strong>📊 Güven Seviyesi:</strong> %82<br><br>
                
                <strong>📖 Okunabilirlik Analizi:</strong><br>
                • Cümle Uzunluğu: Orta (18 kelime ortalama)<br>
                • Karmaşıklık: Düşük-Orta<br>
                • Anlaşılabilirlik: %85<br><br>
                
                <strong>💡 İyileştirme Önerileri:</strong><br>
                • Daha kısa cümleler kullanın<br>
                • Aktif cümle yapısını tercih edin<br>
                • Örneklerle destekleyin
            ",
            
            'İçerik Özeti' => "
                <strong>🤖 İçerik Özeti - AI Analizi:</strong><br><br>
                
                <strong>📋 Ana Konular:</strong><br>
                • " . substr($inputText, 0, 50) . "...<br>
                • Temel kavramlar ve tanımlar<br>
                • Pratik uygulamalar<br><br>
                
                <strong>🔑 Anahtar Noktalar:</strong><br>
                • Konu kapsamlı şekilde ele alınmış<br>
                • Örneklerle desteklenmiş<br>
                • Uygulanabilir öneriler içeriyor<br><br>
                
                <strong>📊 Özet (50 kelime):</strong><br>
                <em>{$inputText} konusunda temel bilgiler ve uygulamalı örnekler sunulan bu içerik, okuyuculara konuya dair kapsamlı bakış açısı kazandırmayı hedeflemektedir.</em>
            ",
            
            'Sosyal Medya Postları' => "
                <strong>🤖 Sosyal Medya Postları - AI Üretimi:</strong><br><br>
                
                <strong>📱 Twitter (280 karakter):</strong><br>
                <em>🔥 {$inputText} hakkında bilmeniz gerekenler! ✨ Pratik ipuçları ve uzman önerileri blog yazımızda. 📖 #" . str_replace(' ', '', strtolower($inputText)) . " #ipucu</em><br><br>
                
                <strong>💼 LinkedIn (Profesyonel):</strong><br>
                <em>Günümüzde {$inputText} konusu giderek önem kazanıyor. Sektördeki deneyimlerimizi sizlerle paylaştığımız yazımızda, pratik çözümler ve uzman görüşleri bulabilirsiniz.</em><br><br>
                
                <strong>📸 Instagram (Hashtag'li):</strong><br>
                <em>✨ {$inputText} rehberimiz yayında! 📚 Story'de detayları kaçırmayın 💫<br>
                #{$inputText} #rehber #ipucu #başarı #motivasyon #öğrenme</em><br><br>
                
                <strong>👥 Facebook (Uzun format):</strong><br>
                <em>Arkadaşlar, {$inputText} konusunda kapsamlı bir yazı hazırladık. Bu yazıda konuyla ilgili temel bilgilerden pratik uygulamalara kadar birçok faydalı bilgi bulacaksınız. Yorumlarınızı bekliyoruz! 💬</em>
            "
        ];

        return $demoResults[$featureName] ?? "
            <strong>🤖 {$featureName} - AI Test Sonucu:</strong><br><br>
            <strong>📝 Analiz Edilen Metin:</strong><br>
            <em>{$inputText}</em><br><br>
            
            <strong>✅ Test Başarılı!</strong><br>
            Bu özellik şu anda geliştirme aşamasında. Demo sonuç gösteriliyor.<br><br>
            
            <strong>💡 Gerçek AI analizi için:</strong><br>
            • API ayarları tamamlanmalı<br>
            • Token sistemi aktif olmalı<br>
            • Provider bağlantısı kurulmalı
        ";
    }

    /**
     * AI özelliğini işle ve sonuç döndür
     */
    private function processAIFeature(string $featureName, string $inputText): string
    {
        // AI prompt oluştur
        $prompt = $this->generatePromptForFeature($featureName, $inputText);
        
        // AI mesaj formatı hazırla
        $messages = [
            [
                'role' => 'system',
                'content' => 'Sen bir AI asistanısın. Türkçe yanıt ver. HTML formatında detaylı ve profesyonel analiz yap.'
            ],
            [
                'role' => 'user', 
                'content' => $prompt
            ]
        ];

        // Gerçek AI API çağrısı yap - timeout ayarlarını kontrol et
        $response = $this->aiServiceManager->sendRequest(
            $messages,
            '1', // tenant ID
            'ai', // module context
            [
                'model' => 'deepseek-chat',
                'max_tokens' => 1000,
                'temperature' => 0.7,
                'timeout' => 300 // 5 dakika timeout
            ]
        );

        if (!$response['success']) {
            throw new \Exception('AI API hatası: ' . ($response['error'] ?? 'Bilinmeyen hata'));
        }

        // AI yanıtını al ve HTML formatına çevir
        $aiContent = $response['data']['content'] ?? '';
        
        if (empty($aiContent)) {
            throw new \Exception('AI yanıtı boş geldi');
        }
        
        // Yanıtı HTML formatına çevir
        return $this->formatAIResponseAsHTML($aiContent, $featureName);
    }

    /**
     * Özelliğe göre AI prompt oluştur
     */
    private function generatePromptForFeature(string $featureName, string $inputText): string
    {
        $prompts = [
            'İçerik Oluşturma' => "'{$inputText}' konusu hakkında kapsamlı bir blog yazısı oluştur. Şunları içersin:\n- Çekici bir başlık\n- Giriş paragrafı\n- Ana başlıklar (H2, H3)\n- Detaylı açıklamalar\n- Sonuç bölümü\n- SEO dostu olmalı ve 800+ kelime olmalı",
            
            'Başlık Alternatifleri' => "'{$inputText}' konusu için 5 farklı başlık önerisi oluştur:\n- SEO dostu olmalı\n- Tıklanma oranını artıracak şekilde çekici\n- Farklı tarzlarda (soru, sayı, nasıl yapılır, vb.)\n- Her birinin CTR potansiyelini belirt",
            
            'SEO Analizi' => "Bu metni SEO açısından detaylı analiz et: '{$inputText}'\n- Anahtar kelime yoğunluğu\n- Meta description önerisi\n- İç/dış link önerileri\n- Başlık yapısı\n- Google ranking potansiyeli\n- İyileştirme önerileri",
            
            'Ton Analizi' => "Bu metnin ayrıntılı ton analizini yap: '{$inputText}'\n- Genel ton (resmi, samimi, profesyonel)\n- Duygu durumu\n- Hedef kitle uyumu\n- Güven seviyesi\n- Okunabilirlik düzeyi\n- Iyileştirme önerileri",
            
            'İçerik Özeti' => "Bu metni özetle: '{$inputText}'\n- Ana konuları belirt\n- Önemli noktaları çıkar\n- Kısa ve net özet\n- Anahtar kavramlar\n- Sonuç ve çıkarımlar",
            
            'Anahtar Kelime Çıkarma' => "Bu metinden SEO anahtar kelimelerini çıkar: '{$inputText}'\n- Birincil anahtar kelime\n- İkincil anahtar kelimeler\n- Uzun kuyruk anahtar kelimeler\n- LSI (anlamsal) kelimeler\n- Rekabet analizi\n- Arama hacmi tahmini",
            
            'SSS Oluşturma' => "'{$inputText}' konusu için 6-8 sık sorulan soru ve cevap oluştur:\n- Müşterilerin merak edeceği sorular\n- Net ve anlaşılır cevaplar\n- SEO dostu format\n- Farklı zorluklarda sorular",
            
            'Eylem Çağrısı' => "'{$inputText}' için 4 farklı CTA (Call to Action) oluştur:\n- Farklı amaçlar için (satış, kayıt, iletişim, bilgi)\n- Ikna edici dil kullan\n- Aciliyet hissi yarat\n- Dönüşüm odaklı",
            
            'Sosyal Medya Postları' => "'{$inputText}' konusu için sosyal medya postları oluştur:\n- Twitter için (280 karakter)\n- LinkedIn için (profesyonel)\n- Instagram için (hashtag'lerle)\n- Facebook için (uzun format)\n- Her platform için uygun hashtag önerileri"
        ];

        return $prompts[$featureName] ?? "'{$inputText}' konusunu analiz et ve detaylı öneriler sun.";
    }

    /**
     * AI yanıtını HTML formatına çevir
     */
    private function formatAIResponseAsHTML(string $aiContent, string $featureName): string
    {
        // AI yanıtını temizle ve HTML formatına çevir
        $htmlContent = nl2br(htmlspecialchars($aiContent, ENT_QUOTES, 'UTF-8'));
        
        // Markdown benzeri formatları HTML'e çevir
        $htmlContent = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $htmlContent);
        $htmlContent = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $htmlContent);
        $htmlContent = preg_replace('/^- (.+)$/m', '• $1', $htmlContent);
        $htmlContent = preg_replace('/^(\d+\. .+)$/m', '<strong>$1</strong>', $htmlContent);
        
        // Özellik başlığı ile birlikte formatla
        $formattedResponse = '<strong>🤖 ' . $featureName . ' - AI Analizi:</strong><br><br>' . $htmlContent;
        
        return $formattedResponse;
    }


    /**
     * AI özellik listesini hazırla
     */
    private function getAIFeatures(): array
    {
        return [
            'active' => [
                'content_generation' => [
                    'generateContent' => [
                        'name' => 'İçerik Oluşturma',
                        'description' => 'Blog yazıları, ürün açıklamaları, sayfa içerikleri oluşturur',
                        'usage' => 'E-ticaret, blog, kurumsal sayfalar',
                        'example' => 'ai_page()->generateContent("Laravel Nedir?", "blog_post")',
                        'category' => 'İçerik Üretimi'
                    ],
                    'generateFromTemplate' => [
                        'name' => 'Şablondan İçerik',
                        'description' => 'Hazır şablonlardan özelleştirilmiş içerik üretir',
                        'usage' => 'Ürün sayfaları, landing sayfalar',
                        'example' => 'ai_page()->action("generateFromTemplate")->with(["template" => "product_page"])',
                        'category' => 'İçerik Üretimi'
                    ],
                    'generateHeadlines' => [
                        'name' => 'Başlık Alternatifleri',
                        'description' => 'Çekici ve SEO dostu başlık seçenekleri oluşturur',
                        'usage' => 'Blog yazıları, ürün isimleri, kampanya başlıkları',
                        'example' => 'ai_page_headlines("E-ticaret Rehberi", "guide", 5)',
                        'category' => 'Yaratıcı İçerik'
                    ],
                    'generateSummary' => [
                        'name' => 'İçerik Özeti',
                        'description' => 'Uzun metinlerin kısa, öz ve akıcı özetlerini çıkarır',
                        'usage' => 'Blog özetleri, ürün kısa açıklamaları, sosyal medya',
                        'example' => 'ai_page_summary($content, "short")',
                        'category' => 'İçerik İşleme'
                    ],
                    'generateFAQ' => [
                        'name' => 'SSS Oluşturma',
                        'description' => 'İçerik bazlı sık sorulan sorular ve cevaplar üretir',
                        'usage' => 'Ürün sayfaları, hizmet açıklamaları, destek sayfaları',
                        'example' => 'ai_page_faq($content, 5)',
                        'category' => 'Müşteri Desteği'
                    ],
                    'generateCallToActions' => [
                        'name' => 'Eylem Çağrısı',
                        'description' => 'Dönüşüm odaklı ikna edici CTA metinleri oluşturur',
                        'usage' => 'Satış sayfaları, e-posta kampanyaları, buttonlar',
                        'example' => 'ai_page_cta($content, "conversion", 3)',
                        'category' => 'Pazarlama'
                    ]
                ],
                'content_analysis' => [
                    'analyzeSEO' => [
                        'name' => 'SEO Analizi',
                        'description' => 'İçeriğin SEO performansını analiz eder ve öneriler sunar',
                        'usage' => 'Blog yazıları, sayfa optimizasyonu, anahtar kelime analizi',
                        'example' => 'ai_analyze_seo("page", $content, "laravel framework")',
                        'category' => 'SEO & Optimizasyon'
                    ],
                    'analyzeReadability' => [
                        'name' => 'Okunabilirlik Analizi',
                        'description' => 'Metnin ne kadar anlaşılır olduğunu değerlendirir',
                        'usage' => 'İçerik kalitesi kontrol, hedef kitle uyumu',
                        'example' => 'ai_page()->action("analyzeReadability")->withContent($content)',
                        'category' => 'İçerik Kalitesi'
                    ],
                    'extractKeywords' => [
                        'name' => 'Anahtar Kelime Çıkarma',
                        'description' => 'İçerikten SEO değeri yüksek anahtar kelimeleri bulur',
                        'usage' => 'SEO stratejisi, içerik etiketleme, kategorizasyon',
                        'example' => 'ai_page_keywords($content, 10)',
                        'category' => 'SEO & Optimizasyon'
                    ],
                    'analyzeTone' => [
                        'name' => 'Ton Analizi',
                        'description' => 'Yazının tonunu (resmi, samimi, profesyonel) analiz eder',
                        'usage' => 'Marka uyumu, hedef kitle analizi, içerik stratejisi',
                        'example' => 'ai_analyze_tone("page", $content)',
                        'category' => 'İçerik Kalitesi'
                    ]
                ],
                'content_optimization' => [
                    'optimizeSEO' => [
                        'name' => 'SEO Optimizasyonu',
                        'description' => 'İçeriği SEO dostu hale getirir ve anahtar kelime yoğunluğunu ayarlar',
                        'usage' => 'Sayfa ranking, Google görünürlük, organik trafik',
                        'example' => 'ai_page()->action("optimizeSEO")->with(["target_keyword" => "laravel"])',
                        'category' => 'SEO & Optimizasyon'
                    ],
                    'generateMetaTags' => [
                        'name' => 'Meta Etiket Oluşturma',
                        'description' => 'SEO meta title, description ve diğer etiketleri oluşturur',
                        'usage' => 'Sayfa SEO, sosyal medya paylaşım, arama motoru',
                        'example' => 'ai_generate_meta_tags("page", $content, $title)',
                        'category' => 'SEO & Optimizasyon'
                    ],
                    'translateContent' => [
                        'name' => 'İçerik Çevirisi',
                        'description' => 'İçeriği farklı dillere doğal ve anlamlı şekilde çevirir',
                        'usage' => 'Çok dilli siteler, uluslararası pazarlama',
                        'example' => 'ai_translate("page", $content, "en")',
                        'category' => 'Lokalizasyon'
                    ],
                    'rewriteContent' => [
                        'name' => 'İçerik Yeniden Yazma',
                        'description' => 'Mevcut içeriği farklı tarzda yeniden düzenler',
                        'usage' => 'A/B test, farklı tonlarda yazım, özgünlük',
                        'example' => 'ai_page()->action("rewriteContent")->with(["rewrite_style" => "professional"])',
                        'category' => 'İçerik İşleme'
                    ],
                    'optimizeHeadings' => [
                        'name' => 'Başlık Optimizasyonu',
                        'description' => 'H1-H6 başlık yapısını SEO ve okunabilirlik için optimize eder',
                        'usage' => 'Sayfa yapısı, SEO hiyerarşi, kullanıcı deneyimi',
                        'example' => 'ai_optimize_headings("page", $content)',
                        'category' => 'SEO & Optimizasyon'
                    ]
                ],
                'content_expansion' => [
                    'expandContent' => [
                        'name' => 'İçerik Genişletme',
                        'description' => 'Mevcut içeriği daha detaylı ve kapsamlı hale getirir',
                        'usage' => 'Kısa içerikleri genişletme, detay ekleme',
                        'example' => 'ai_page()->action("expandContent")->with(["expansion_type" => "detailed"])',
                        'category' => 'İçerik Geliştirme'
                    ],
                    'suggestImprovements' => [
                        'name' => 'İyileştirme Önerileri',
                        'description' => 'İçeriğin nasıl geliştirilebileceği konusunda öneriler sunar',
                        'usage' => 'İçerik kalitesi artırma, performans iyileştirme',
                        'example' => 'ai_page()->action("suggestImprovements")->withContent($content)',
                        'category' => 'İçerik Geliştirme'
                    ],
                    'suggestRelatedTopics' => [
                        'name' => 'İlgili Konu Önerileri',
                        'description' => 'Mevcut içerikle ilgili yazılabilecek konuları önerir',
                        'usage' => 'İçerik planlaması, blog takvimi, seri yazılar',
                        'example' => 'ai_suggest_topics("page", $content, 5)',
                        'category' => 'İçerik Planlaması'
                    ],
                    'generateOutline' => [
                        'name' => 'İçerik Ana Hatları',
                        'description' => 'Belirli bir konu için detaylı içerik planı oluşturur',
                        'usage' => 'Yazı planlaması, rehber oluşturma, içerik yapısı',
                        'example' => 'ai_generate_outline("page", "Laravel Kurulum", "tutorial", 6)',
                        'category' => 'İçerik Planlaması'
                    ]
                ],
                'social_media' => [
                    'generateSocialPosts' => [
                        'name' => 'Sosyal Medya Postları',
                        'description' => 'Farklı platformlar için optimize edilmiş paylaşım metinleri',
                        'usage' => 'Twitter, Facebook, LinkedIn, Instagram pazarlama',
                        'example' => 'ai_generate_social_posts("page", $content, ["twitter", "linkedin"])',
                        'category' => 'Sosyal Medya'
                    ]
                ]
            ],
            'potential' => [
                'advanced_analysis' => [
                    'checkPlagiarism' => [
                        'name' => 'Özgünlük Kontrolü',
                        'description' => 'İçeriğin benzersizliğini kontrol eder ve benzer içerikleri tespit eder',
                        'usage' => 'İçerik kalitesi, SEO güvenliği, akademik yazım',
                        'category' => 'İçerik Kalitesi'
                    ],
                    'analyzeEmotionalTone' => [
                        'name' => 'Duygusal Ton Analizi',
                        'description' => 'Metnin duygusal etkisini ve hissettirdiği duyguları analiz eder',
                        'usage' => 'Marka iletişimi, müşteri memnuniyeti, pazarlama etkisi',
                        'category' => 'İçerik Kalitesi'
                    ],
                    'analyzeCompetitors' => [
                        'name' => 'Rakip Analizi',
                        'description' => 'Rakip içeriklerini analiz ederek güçlü-zayıf yönleri tespit eder',
                        'usage' => 'Rekabet stratejisi, pazar analizi, içerik stratejisi',
                        'category' => 'Pazarlama Analizi'
                    ]
                ],
                'content_enhancement' => [
                    'generateInfographicText' => [
                        'name' => 'İnfografik Metinleri',
                        'description' => 'Görsel infografikler için kısa ve etkili metin blokları',
                        'usage' => 'Sosyal medya, sunumlar, görsel pazarlama',
                        'category' => 'Görsel İçerik'
                    ],
                    'generateVideoScript' => [
                        'name' => 'Video Senaryosu',
                        'description' => 'YouTube, Instagram, TikTok için video senaryoları',
                        'usage' => 'Video pazarlama, eğitim içerikleri, tanıtım videoları',
                        'category' => 'Video İçerik'
                    ],
                    'generatePodcastScript' => [
                        'name' => 'Podcast Metinleri',
                        'description' => 'Podcast bölümleri için konuşma metinleri ve ana hatlar',
                        'usage' => 'Podcast üretimi, ses içerikleri, röportajlar',
                        'category' => 'Ses İçerik'
                    ]
                ],
                'seo_advanced' => [
                    'analyzeLSIKeywords' => [
                        'name' => 'LSI Anahtar Kelime Analizi',
                        'description' => 'Ana anahtar kelimeyle anlamsal olarak ilişkili kelimeleri bulur',
                        'usage' => 'Gelişmiş SEO, Google NLP optimizasyonu',
                        'category' => 'İleri SEO'
                    ],
                    'generateSchemaMarkup' => [
                        'name' => 'Schema Markup Oluşturma',
                        'description' => 'Arama motorları için yapılandırılmış veri etiketleri',
                        'usage' => 'Zengin snippet\'ler, arama görünürlüğü',
                        'category' => 'İleri SEO'
                    ],
                    'optimizeForVoice' => [
                        'name' => 'Sesli Arama Optimizasyonu',
                        'description' => 'Alexa, Google Assistant gibi sesli aramalar için optimizasyon',
                        'usage' => 'Gelecek SEO, sesli asistan uyumluluğu',
                        'category' => 'İleri SEO'
                    ]
                ],
                'personalization' => [
                    'personalizeContent' => [
                        'name' => 'İçerik Kişiselleştirme',
                        'description' => 'Kullanıcı segmentlerine göre içeriği uyarlar',
                        'usage' => 'E-ticaret, kişisel deneyim, hedefli pazarlama',
                        'category' => 'Kişiselleştirme'
                    ],
                    'adaptToAudience' => [
                        'name' => 'Hedef Kitle Uyarlama',
                        'description' => 'İçeriği belirli demografik gruplara göre optimize eder',
                        'usage' => 'Yaş grupları, eğitim seviyesi, ilgi alanlarına göre',
                        'category' => 'Kişiselleştirme'
                    ]
                ],
                'analytics_prediction' => [
                    'predictEngagement' => [
                        'name' => 'Etkileşim Tahmini',
                        'description' => 'İçeriğin ne kadar etkileşim alacağını tahmin eder',
                        'usage' => 'İçerik stratejisi, yayın zamanlaması',
                        'category' => 'Performans Analizi'
                    ],
                    'suggestABTests' => [
                        'name' => 'A/B Test Önerileri',
                        'description' => 'İçerik performansını test etmek için A/B test senaryoları',
                        'usage' => 'Dönüşüm optimizasyonu, performans artırma',
                        'category' => 'Performans Analizi'
                    ],
                    'predictTrends' => [
                        'name' => 'Trend Tahmini',
                        'description' => 'Gelecekteki içerik trendlerini önceden tahmin eder',
                        'usage' => 'İçerik planlaması, pazarlama stratejisi',
                        'category' => 'Gelecek Analizi'
                    ],
                    'analyzeUserBehavior' => [
                        'name' => 'Kullanıcı Davranış Analizi',
                        'description' => 'Kullanıcıların içerikle nasıl etkileşim kurduğunu analiz eder',
                        'usage' => 'UX iyileştirme, kişiselleştirme',
                        'category' => 'Kullanıcı Analizi'
                    ]
                ],
                'automation_features' => [
                    'autoContentScheduling' => [
                        'name' => 'Otomatik İçerik Programlama',
                        'description' => 'İçerikleri en uygun zamanlarda otomatik yayınlar',
                        'usage' => 'Sosyal medya yönetimi, blog yayınlama',
                        'category' => 'Otomasyon'
                    ],
                    'autoSEOOptimization' => [
                        'name' => 'Otomatik SEO Optimizasyonu',
                        'description' => 'İçerikleri SEO kurallarına göre otomatik optimize eder',
                        'usage' => 'Arama motoru sıralaması, organik trafik',
                        'category' => 'SEO Otomasyonu'
                    ],
                    'autoTagGeneration' => [
                        'name' => 'Otomatik Etiket Oluşturma',
                        'description' => 'İçeriğe uygun etiketleri otomatik olarak oluşturur',
                        'usage' => 'İçerik kategorilendirme, arama kolaylığı',
                        'category' => 'Otomasyon'
                    ],
                    'smartWorkflow' => [
                        'name' => 'Akıllı İş Akışı',
                        'description' => 'İçerik üretim sürecini otomatik olarak yönetir',
                        'usage' => 'Proje yönetimi, takım koordinasyonu',
                        'category' => 'İş Akışı'
                    ]
                ],
                'creative_tools' => [
                    'generateMemes' => [
                        'name' => 'Meme Oluşturucu',
                        'description' => 'Viral potansiyeli yüksek meme içerikleri oluşturur',
                        'usage' => 'Sosyal medya pazarlama, viral içerik',
                        'category' => 'Yaratıcı İçerik'
                    ],
                    'generateSlogans' => [
                        'name' => 'Slogan Üretici',
                        'description' => 'Marka için çekici ve akılda kalıcı sloganlar üretir',
                        'usage' => 'Marka iletişimi, reklam kampanyaları',
                        'category' => 'Pazarlama Kreatifi'
                    ],
                    'generateJingles' => [
                        'name' => 'Jingle Yaratıcı',
                        'description' => 'Radyo ve TV reklamları için müzikal jingle önerileri',
                        'usage' => 'Reklam müziği, marka tanıtımı',
                        'category' => 'Ses Branding'
                    ],
                    'generateHashtags' => [
                        'name' => 'Hashtag Üretici',
                        'description' => 'Viral potansiyeli yüksek hashtagler önerir',
                        'usage' => 'Sosyal medya görünürlüğü, trend yakalama',
                        'category' => 'Sosyal Medya'
                    ]
                ],
                'advanced_analytics' => [
                    'sentimentAnalysis' => [
                        'name' => 'Duygu Analizi',
                        'description' => 'Kullanıcı yorumlarındaki duygusal tonları analiz eder',
                        'usage' => 'Müşteri memnuniyeti, krız yönetimi',
                        'category' => 'Duygu Analizi'
                    ],
                    'competitorMonitoring' => [
                        'name' => 'Rakip İzleme',
                        'description' => 'Rakiplerin içerik stratejilerini sürekli izler',
                        'usage' => 'Rekabet analizi, strateji geliştirme',
                        'category' => 'Rekabet İzleme'
                    ],
                    'brandMentionTracking' => [
                        'name' => 'Marka Bahis Takibi',
                        'description' => 'İnternette marka bahislerini izler ve raporlar',
                        'usage' => 'Marka bilinirliği, PR yönetimi',
                        'category' => 'Marka İzleme'
                    ]
                ]
            ]
        ];
    }
}