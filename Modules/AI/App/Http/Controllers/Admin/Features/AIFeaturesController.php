<?php
namespace Modules\AI\App\Http\Controllers\Admin\Features;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\AIResponseRepository;
use Modules\AI\App\Models\Conversation;
use App\Services\ThemeService;
use App\Services\AI\AIServiceManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AIFeaturesController extends Controller
{
    protected $aiService;
    protected $aiResponseRepository;
    protected $themeService;
    protected $aiServiceManager;

    public function __construct(AIService $aiService, AIResponseRepository $aiResponseRepository, ThemeService $themeService, AIServiceManager $aiServiceManager)
    {
        $this->aiService = $aiService;
        $this->aiResponseRepository = $aiResponseRepository;
        $this->themeService = $themeService;
        $this->aiServiceManager = $aiServiceManager;
    }


    /**
     * YENİ METOD: Features Management Ana Sayfası
     * Admin panelinde features yönetimi için
     */
    public function manage(Request $request)
    {
        $query = \Modules\AI\App\Models\AIFeature::query();

        // Filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }
        if ($request->filled('category')) {
            $query->where('ai_feature_category_id', $request->category);
        }

        $features = $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $categories = [
            'content-creation' => 'İçerik Oluşturma',
            'web-editor' => 'Web Editör',
            'productivity' => 'Prodüktivite',
            'communication' => 'İletişim',
            'education' => 'Eğitim',
            'analysis' => 'Analiz',
            'translation' => 'Çeviri',
            'other' => 'Diğer'
        ];

        $statuses = [
            'active' => 'Aktif',
            'inactive' => 'Pasif',
            'beta' => 'Beta',
            'planned' => 'Planlanmış'
        ];

        $complexityLevels = [
            'beginner' => 'Başlangıç',
            'intermediate' => 'Orta',
            'advanced' => 'İleri'
        ];

        return view('ai::admin.features.manage', compact(
            'features',
            'categories',
            'statuses',
            'complexityLevels'
        ));
    }

    /**
     * Yeni feature oluşturma sayfası
     */
    public function create()
    {
        $categories = [
            'content-creation' => 'İçerik Oluşturma',
            'web-editor' => 'Web Editör',
            'productivity' => 'Prodüktivite',
            'communication' => 'İletişim',
            'education' => 'Eğitim',
            'analysis' => 'Analiz',
            'translation' => 'Çeviri',
            'other' => 'Diğer'
        ];

        $complexityLevels = [
            'beginner' => 'Başlangıç',
            'intermediate' => 'Orta',
            'advanced' => 'İleri'
        ];

        return view('ai::admin.features.create', compact('categories', 'complexityLevels'));
    }

    /**
     * Feature kaydetme
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:ai_features,slug',
            'description' => 'required|string',
            'ai_feature_category_id' => 'required|integer|exists:ai_feature_categories,ai_feature_category_id',
            'complexity_level' => 'required|string',
            'status' => 'required|string|in:active,inactive,beta,planned',
            'sort_order' => 'nullable|integer|min:1',
            'emoji' => 'nullable|string|max:10',
            'icon' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:20',
            'input_placeholder' => 'nullable|string|max:500',
            'helper_function' => 'nullable|string|max:100',
            'custom_prompt' => 'nullable|string',
            'response_length' => 'nullable|string',
            'response_format' => 'nullable|string',
            'additional_config' => 'nullable|json',
            'usage_examples' => 'nullable|json',
            'input_validation' => 'nullable|json',
        ]);

        try {
            $feature = \Modules\AI\App\Models\AIFeature::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'emoji' => $request->emoji ?: '🤖',
                'icon' => $request->icon ?: 'fas fa-robot',
                'ai_feature_category_id' => $request->ai_feature_category_id,
                'complexity_level' => $request->complexity_level,
                'status' => $request->status,
                'sort_order' => $request->sort_order ?: 999,
                'badge_color' => $request->badge_color ?: 'primary',
                'input_placeholder' => $request->input_placeholder,
                'helper_function' => $request->helper_function,
                'custom_prompt' => $request->custom_prompt,
                'response_length' => $request->response_length ?: 'medium',
                'response_format' => $request->response_format ?: 'text',
                'additional_config' => $request->additional_config,
                'usage_examples' => $request->usage_examples,
                'input_validation' => $request->input_validation,
                'is_featured' => $request->has('is_featured'),
                'show_in_examples' => $request->has('show_in_examples'),
                'requires_input' => $request->has('requires_input'),
                'is_system' => false,
                'usage_count' => 0,
                'avg_rating' => 0,
                'rating_count' => 0,
            ]);

            if ($request->input('action') === 'save_and_continue') {
                return redirect()->route('admin.ai.features.edit', $feature->id)
                    ->with('success', 'AI Feature başarıyla oluşturuldu!');
            }

            return redirect()->route('admin.ai.features.index')
                ->with('success', 'AI Feature başarıyla oluşturuldu!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Feature oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Feature düzenleme sayfası
     */
    public function edit($id)
    {
        $feature = \Modules\AI\App\Models\AIFeature::findOrFail($id);
        
        $categories = [
            'content-creation' => 'İçerik Oluşturma',
            'web-editor' => 'Web Editör',
            'productivity' => 'Prodüktivite',
            'communication' => 'İletişim',
            'education' => 'Eğitim',
            'analysis' => 'Analiz',
            'translation' => 'Çeviri',
            'other' => 'Diğer'
        ];

        $complexityLevels = [
            'beginner' => 'Başlangıç',
            'intermediate' => 'Orta',
            'advanced' => 'İleri'
        ];

        $statuses = [
            'active' => 'Aktif',
            'inactive' => 'Pasif',
            'beta' => 'Beta',
            'planned' => 'Planlanmış'
        ];

        return view('ai::admin.features.edit', compact('feature', 'categories', 'complexityLevels', 'statuses'));
    }

    /**
     * Feature güncelleme
     */
    public function update(Request $request, $id)
    {
        $feature = \Modules\AI\App\Models\AIFeature::findOrFail($id);
        
        // Quick update (sadece status değişikliği)
        if ($request->has('quick_update')) {
            $feature->update(['status' => $request->status]);
            return response()->json(['success' => true, 'message' => 'Durum başarıyla güncellendi!']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:ai_features,slug,' . $id,
            'description' => 'required|string',
            'ai_feature_category_id' => 'required|integer|exists:ai_feature_categories,ai_feature_category_id',
            'complexity_level' => 'required|string',
            'status' => 'required|string|in:active,inactive,beta,planned',
            'sort_order' => 'nullable|integer|min:1',
            'emoji' => 'nullable|string|max:10',
            'icon' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:20',
            'input_placeholder' => 'nullable|string|max:500',
            'helper_function' => 'nullable|string|max:100',
            'custom_prompt' => 'nullable|string',
            'response_length' => 'nullable|string',
            'response_format' => 'nullable|string',
            'additional_config' => 'nullable|json',
            'usage_examples' => 'nullable|json',
            'input_validation' => 'nullable|json',
        ]);

        try {
            $feature->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'emoji' => $request->emoji ?: '🤖',
                'icon' => $request->icon ?: 'fas fa-robot',
                'ai_feature_category_id' => $request->ai_feature_category_id,
                'complexity_level' => $request->complexity_level,
                'status' => $request->status,
                'sort_order' => $request->sort_order ?: 999,
                'badge_color' => $request->badge_color ?: 'primary',
                'input_placeholder' => $request->input_placeholder,
                'helper_function' => $request->helper_function,
                'custom_prompt' => $request->custom_prompt,
                'response_length' => $request->response_length ?: 'medium',
                'response_format' => $request->response_format ?: 'text',
                'additional_config' => $request->additional_config,
                'usage_examples' => $request->usage_examples,
                'input_validation' => $request->input_validation,
                'is_featured' => $request->has('is_featured'),
                'show_in_examples' => $request->has('show_in_examples'),
                'requires_input' => $request->has('requires_input'),
            ]);

            if ($request->input('action') === 'save_and_continue') {
                return redirect()->route('admin.ai.features.edit', $feature->id)
                    ->with('success', 'AI Feature başarıyla güncellendi!');
            }

            return redirect()->route('admin.ai.features.index')
                ->with('success', 'AI Feature başarıyla güncellendi!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Feature güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Feature silme
     */
    public function destroy($id)
    {
        try {
            $feature = \Modules\AI\App\Models\AIFeature::findOrFail($id);
            
            if ($feature->is_system) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sistem feature\'ları silinemez!'
                ], 403);
            }

            $feature->delete();

            return response()->json([
                'success' => true,
                'message' => 'Feature başarıyla silindi!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feature silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sıralama güncelleme (AJAX)
     */
    public function updateSort(Request $request)
    {
        try {
            $items = $request->input('items', []);
            
            foreach ($items as $item) {
                \Modules\AI\App\Models\AIFeature::where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sıralama başarıyla güncellendi!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Feature durumu değiştirme (AJAX)
     */
    public function toggleStatus($id)
    {
        try {
            $feature = \Modules\AI\App\Models\AIFeature::findOrFail($id);
            
            // Sistem feature'ları için uyarı
            if ($feature->is_system) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sistem feature\'larının durumu değiştirilemez!'
                ], 403);
            }

            // Durum değiştir
            $newStatus = $feature->status === 'active' ? 'inactive' : 'active';
            $feature->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Feature durumu başarıyla güncellendi!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Durum güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
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
        
        // YENİ MERKEZI REPOSITORY SİSTEMİ
        $result = $this->aiResponseRepository->executeRequest('admin_chat', [
            'message' => $message,
            'conversation_id' => $conversationId,
            'custom_prompt' => $request->custom_prompt ?? ''
        ]);
        
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 400);
        }
        
        // Conversation handling için legacy code korundu
        if ($conversationId) {
            $conversation = Conversation::where('id', $conversationId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        } else {
            // Yeni konuşma oluştur
            $title = substr($message, 0, 30) . '...';
            $conversation = $this->aiService->conversations()->createConversation($title);
        }
        
        // Word buffer için format oluştur
        $wordBufferResponse = $this->aiResponseRepository->formatWithWordBuffer(
            $result['response'], 
            'admin_chat', 
            [
                'conversation_id' => $conversation->id,
                'conversation_title' => $conversation->title
            ]
        );

        return response()->json([
            'conversation_id' => $conversation->id,
            'response' => $result['response'],
            'formatted_response' => $result['formatted_response'],
            'title' => $conversation->title,
            'success' => true,
            'word_buffer_enabled' => $wordBufferResponse['word_buffer_enabled'],
            'word_buffer_config' => $wordBufferResponse['word_buffer_config']
        ]);
    }

    /**
     * Feature tek başına görüntüleme sayfası (slug veya ID ile)
     */
    public function show($feature)
    {
        // ID veya slug ile feature bul
        $featureModel = is_numeric($feature) 
            ? \Modules\AI\App\Models\AIFeature::findOrFail($feature)
            : \Modules\AI\App\Models\AIFeature::where('slug', $feature)->firstOrFail();

        // Content length prompts'ları veritabanından çek
        $contentLengthOptions = \Modules\AI\App\Models\Prompt::where('prompt_type', 'content_length')
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->get()
            ->map(function($prompt) {
                // Extract numeric value from content for ordering
                preg_match('/(\d+)/', $prompt->content, $matches);
                $numericValue = isset($matches[1]) ? (int)$matches[1] : 0;
                
                return [
                    'value' => $numericValue ?: $prompt->prompt_id,
                    'label' => $prompt->name,
                    'description' => $prompt->content
                ];
            });

        return view('ai::admin.features.show', compact('featureModel', 'contentLengthOptions'));
    }

    /**
     * AI Prowess sayfası - Müşteri görünümü
     */
    public function prowess()
    {
        $features = \Modules\AI\App\Models\AIFeature::where('status', 'active')
            ->where('show_in_examples', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($feature) {
                // Manuel kategori ilişkisi - relationship çalışmıyor
                if ($feature->ai_feature_category_id) {
                    $category = \Modules\AI\App\Models\AIFeatureCategory::where('ai_feature_category_id', $feature->ai_feature_category_id)->first();
                    return $category ? $category->slug : 'uncategorized';
                }
                return 'uncategorized';
            });

        // Kategori isimlerini ai_feature_categories tablosundan al
        $categoryNames = \Modules\AI\App\Models\AIFeatureCategory::where('is_active', true)
            ->orderBy('order')
            ->pluck('title', 'slug')
            ->toArray();
            
        // Debug: Kategori-feature ilişkisini kontrol et
        \Log::info('Prowess Debug:', [
            'total_features' => $features->count(),
            'grouped_features' => $features->mapWithKeys(function($group, $key) {
                return [$key => $group->count()];
            })->toArray(),
            'category_names' => $categoryNames
        ]);

        // Content length prompts'ları veritabanından çek
        // Content length prompts: Range 1-5 = Çok Kısa'dan Çok Detaylı'ya doğru
        $contentLengthPrompts = \Modules\AI\App\Models\Prompt::where('prompt_type', 'content_length')
            ->where('is_active', true)
            ->orderBy('priority', 'desc') // DESC: priority 5,4,3,2,1 = Çok Kısa → Çok Detaylı
            ->orderBy('name')
            ->get();
        
        $contentLengthOptions = $contentLengthPrompts->map(function($prompt, $index) {
            return [
                'value' => $index + 1, // Range değeri: 1,2,3,4,5
                'label' => $prompt->name,
                'description' => $prompt->content,
                'prompt_id' => $prompt->prompt_id, // Backend mapping için
                'priority' => $prompt->priority // Debug için
            ];
        });
            
        // Content length slider ayarları
        $contentLengthConfig = [
            'min' => 1,
            'max' => max(5, $contentLengthOptions->count()), // Minimum 5, maksimum seçenek sayısı
            'default' => ceil($contentLengthOptions->count() / 2) ?: 3 // Ortadaki değer veya 3
        ];

        // AI Widget Helper kullanarak token bilgilerini al
        $tenantId = tenant('id') ?: '1';
        $tokenStatus = ai_widget_token_data($tenantId);

        // Şirket Profili kontrolleri
        $hasCompanyProfile = $this->checkCompanyProfileAvailability();
        
        // Yazım tonu seçeneklerini çek
        $writingToneOptions = \Modules\AI\App\Models\Prompt::where('prompt_type', 'writing_tone')
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->get();

        return view('ai::admin.features.prowess.showcase', compact(
            'features', 
            'categoryNames', 
            'tokenStatus', 
            'contentLengthOptions', 
            'contentLengthConfig',
            'hasCompanyProfile',
            'writingToneOptions'
        ));
    }


    /**
     * AI Feature test API endpoint
     */
    public function testFeature(Request $request)
    {
        // Slug veya ID kabul et
        $request->validate([
            'feature_id' => 'required_without:feature_slug|integer',
            'feature_slug' => 'required_without:feature_id|string',
            'input_text' => 'nullable|string|min:5',
            'custom_prompt' => 'nullable|string|min:5'
        ]);

        $featureId = $request->feature_id;
        $featureSlug = $request->feature_slug;
        $inputText = $request->input_text ?? $request->custom_prompt ?? 'Analiz yap';
        $tenantId = tenant('id') ?: '1';

        // Feature'ı veritabanından al (ID veya slug ile)
        $feature = $featureId 
            ? \Modules\AI\App\Models\AIFeature::find($featureId)
            : \Modules\AI\App\Models\AIFeature::where('slug', $featureSlug)->first();

        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => "Feature bulunamadı."
            ], 404);
        }

        // Token kontrolü - YENİ SİSTEM
        $estimatedTokens = max(10, (int)(strlen($inputText) / 4));
        
        if (!ai_can_use_tokens($estimatedTokens, $tenantId)) {
            $currentBalance = ai_get_token_balance($tenantId);
            $tokenPackagesUrl = route('admin.ai.token-packages.index');
            
            return response()->json([
                'success' => false,
                'message' => 'Yetersiz token bakiyesi. Token satın almak için paket sayfasını ziyaret edin.',
                'tokens_required' => $estimatedTokens,
                'current_balance' => $currentBalance,
                'redirect_url' => $tokenPackagesUrl,
                'purchase_link' => $tokenPackagesUrl
            ], 402);
        }

        try {
            // AKILLI UYARI SİSTEMİ - Doğru feature kontrol et
            $smartAnalysis = $this->checkFeatureCompatibility($feature, $inputText);
            
            // YENİ MERKEZI REPOSITORY SİSTEMİ - Prowess için
            $result = $this->aiResponseRepository->executeRequest('prowess_test', [
                'feature_id' => $feature->id,
                'input_text' => $inputText,
                'tenant_id' => $tenantId
            ]);
            
            // Akıllı uyarı sonuçlarını ekle
            if ($result['success'] && $smartAnalysis) {
                $result['smart_analysis'] = $smartAnalysis;
            }

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            // ARTIK KULLANILMIYOR - ai_use_calculated_credits() AIService'de otomatik çalışıyor
            // Credit kullanımı AIService.ask() içinde gerçek token bilgileri ile yapılacak

            // Word buffer için format oluştur
            $wordBufferResponse = $this->aiResponseRepository->formatWithWordBuffer(
                $result['response'], 
                'prowess_test', 
                [
                    'feature_name' => $feature->name,
                    'feature_id' => $feature->id,
                    'showcase_mode' => true
                ]
            );

            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'formatted_response' => $result['formatted_response'],
                'feature' => $result['feature'],
                'tokens_used' => $estimatedTokens,
                'word_buffer_enabled' => $wordBufferResponse['word_buffer_enabled'],
                'word_buffer_config' => $wordBufferResponse['word_buffer_config']
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Feature Test Error: ' . $e->getMessage(), [
                'feature' => $feature->name,
                'tenant_id' => $tenantId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Test sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gerçek AI servisi ile test işlemi
     */
    private function processRealAIFeature($feature, string $inputText, int $tenantId): array
    {
        $featureName = $feature->name;
        
        try {
            // YENİ TEMPLATE SİSTEMİ: İki katmanlı prompt hierarchy kullan
            if ($feature->hasQuickPrompt() || $feature->hasResponseTemplate()) {
                // Modern: Yeni template sistemi (Quick Prompt + Expert Prompts + Response Template)
                $aiResponse = $this->aiService->askFeature($feature, $inputText, [
                    'content' => $inputText,
                    'language' => 'Turkish',
                    'level' => 'intermediate'
                ]);
                
                \Log::info('🎯 YENİ Template Sistemi Kullanıldı', [
                    'feature' => $feature->name,
                    'has_quick_prompt' => $feature->hasQuickPrompt(),
                    'has_response_template' => $feature->hasResponseTemplate(),
                    'prompt_hierarchy' => 'Gizli Sistem → Quick → Expert → Template → Gizli Bilgi → Şartlı'
                ]);
            } else {
                // Legacy: Eski custom prompt sistemi (geriye uyumluluk)
                $customPrompt = $feature->custom_prompt ?: $this->generateSystemPrompt($feature);
                
                $aiResponse = $this->aiService->ask($inputText, [
                    'context' => $customPrompt
                ]);
                
                \Log::info('🔙 Legacy Prompt Sistemi Kullanıldı', [
                    'feature' => $feature->name,
                    'reason' => 'Quick prompt veya response template bulunamadı'
                ]);
            }

            // AI yanıtı kontrolü
            if (!$aiResponse) {
                throw new \Exception('AI servisinden yanıt alınamadı');
            }

            // Token kullanımını tahmin et
            $tokensUsed = max(10, (int)((strlen($inputText) + strlen($aiResponse)) / 4));
            
            // Controller'dan token kullanımını kaydet
            ai_use_tokens($tokensUsed, 'ai', 'prowess_test', $tenantId, [
                'feature_id' => $feature->id,
                'feature_name' => $feature->name,
                'input_text_length' => strlen($inputText),
                'response_length' => strlen($aiResponse),
                'source' => 'prowess_page'
            ]);
            
            // CONVERSATION KAYDINI OLUŞTUR - Prowess testleri görünür olsun
            $this->createProwessConversationRecord($feature, $inputText, $aiResponse, $tokensUsed, $tenantId);
            
            // Usage count artır
            $feature->increment('usage_count');

            $newBalance = ai_get_token_balance($tenantId);
            
            return [
                'success' => true,
                'ai_result' => $aiResponse,
                'tokens_used' => $tokensUsed,
                'tokens_used_formatted' => ai_format_token_count($tokensUsed) . ' kullanıldı',
                'demo_mode' => false,
                'new_balance' => $newBalance,
                'new_balance_formatted' => ai_format_token_count($newBalance)
            ];

        } catch (\Exception $e) {
            \Log::error('Real AI Feature Error: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'feature' => $feature->name,
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
    private function processDemoFeature($feature, string $inputText, int $tenantId): array
    {
        $featureName = $feature->name;
        $demoResult = $this->generateDemoResult($feature, $inputText);
        $tokensUsed = max(10, (int)(strlen($inputText) / 8));
        
        // Tenant'ı al ve token düş
        $tenant = \App\Models\Tenant::find($tenantId);
        if ($tenant && $tenant->ai_tokens_balance >= $tokensUsed) {
            $tenant->decrement('ai_tokens_balance', $tokensUsed);
            $tenant->increment('ai_tokens_used_this_month', $tokensUsed);
        }
        
        // Usage count artır
        $feature->increment('usage_count');

        return [
            'success' => true,
            'ai_result' => $demoResult,
            'tokens_used' => $tokensUsed,
            'demo_mode' => true,
            'new_balance' => $tenant ? $tenant->fresh()->ai_tokens_balance : 0
        ];
    }

    /**
     * Demo AI sonucu üret (test modu)
     */
    private function generateDemoResult($feature, string $inputText): string
    {
        $featureName = $feature->name;
        $featureDescription = $feature->description;
        
        // Feature'a özel demo results
        $specificDemoResults = [
            'Blog Yazısı Oluşturma' => "
                <strong>📝 Blog Yazısı - AI Üretimi</strong><br><br>
                
                <strong>Başlık:</strong> {$inputText}: Kapsamlı Rehber ve İpuçları<br><br>
                
                <strong>Giriş Paragrafı:</strong><br>
                {$inputText} konusu günümüzde artan önemine paralel olarak daha çok tartışılmaya başlandı. Bu yazımızda konuyla ilgili temel bilgilerden pratik uygulamalara kadar birçok konuyu ele alacağız.<br><br>
                
                <strong>Ana Başlıklar:</strong><br>
                • {$inputText} Nedir?<br>
                • {$inputText} Faydaları<br>
                • {$inputText} Kullanım Alanları<br>
                • Pratik Öneriler ve İpuçları<br>
                • Sonuç ve Değerlendirme<br><br>
                
                <strong>SEO Uyumluluğu:</strong> %92<br>
                <strong>Okunabilirlik:</strong> Orta seviye<br>
                <strong>Tahmini Kelime Sayısı:</strong> 800-1200 kelime
            ",
            
            'İçerik Özeti' => "
                <strong>📋 İçerik Özeti - AI Analizi</strong><br><br>
                
                <strong>Orijinal Metin Uzunluğu:</strong> " . str_word_count($inputText) . " kelime<br>
                <strong>Özet Uzunluğu:</strong> " . max(20, (int)(str_word_count($inputText) * 0.15)) . " kelime (%85 azaltma)<br><br>
                
                <strong>Özet:</strong><br>
                <em>" . substr($inputText, 0, 200) . "...</em><br><br>
                
                <strong>Ana Konular:</strong><br>
                • Temel kavramlar ve tanımlar<br>
                • Uygulama alanları<br>
                • Önemli noktalar<br><br>
                
                <strong>Özet Kalitesi:</strong> Yüksek (%89)
            ",
            
            'Template Bazlı İçerik Oluşturma' => "
                <strong>🎨 Template Bazlı İçerik - AI Üretimi</strong><br><br>
                
                <strong>Seçilen Template:</strong> İş/Kurumsal<br>
                <strong>Konu:</strong> {$inputText}<br><br>
                
                <strong>Hero Section:</strong><br>
                <em>{$inputText} konusunda uzman çözümler sunuyoruz. Kaliteli hizmet anlayışımızla sektörde öncü konumdayız.</em><br><br>
                
                <strong>Hakkımızda Section:</strong><br>
                <em>Yılların deneyimi ve uzman kadromuzla {$inputText} alanında güvenilir partneriniziz.</em><br><br>
                
                <strong>Hizmetler Section:</strong><br>
                • Danışmanlık Hizmetleri<br>
                • Uygulama ve Implementasyon<br>
                • Eğitim ve Destek<br><br>
                
                <strong>İletişim Section:</strong><br>
                <em>Projeleriniz için bizimle iletişime geçin. Ücretsiz ön değerlendirme imkanı.</em>
            "
        ];

        // Özel sonuçları kontrol et, yoksa genel dinamik sonuç üret
        if (isset($specificDemoResults[$featureName])) {
            return $specificDemoResults[$featureName];
        }
        
        // Genel dinamik demo sonucu
        return "
            <strong>{$feature->emoji} {$featureName} - AI Demo Analizi</strong><br><br>
            
            <strong>📋 Özellik Açıklaması:</strong><br>
            <em>{$featureDescription}</em><br><br>
            
            <strong>📝 Analiz Edilen Metin:</strong><br>
            <div class='bg-light p-2 rounded'>" . Str::limit($inputText, 200) . "</div><br>
            
            <strong>🎯 Demo Sonuçlar:</strong><br>
            • Metin uzunluğu: " . str_word_count($inputText) . " kelime<br>
            • Kategori: {$feature->getCategoryName()}<br>
            • Zorluk seviyesi: {$feature->getComplexityName()}<br><br>
            
            <strong>✅ Demo Test Başarılı!</strong><br>
            <div class='text-success'>Bu özellik hazır! Gerçek AI testi için 'Gerçek AI' seçeneğini kullanın.</div><br>
            
            <strong>💡 Sonraki Adımlar:</strong><br>
            • <span class='text-primary'>Gerçek AI</span> modunu deneyin<br>
            • Farklı test metinleri ile test edin<br>
            • Sonuçları değerlendirin
        ";
    }

    /**
     * Feature için sistem prompt üret
     */
    private function generateSystemPrompt($feature): string
    {
        $basePrompt = "You are a professional AI assistant specialized in {$feature->name}.";
        
        // Critical: Format kurallarını her zaman dahil et
        $formatRules = "

CRITICAL OUTPUT FORMAT REQUIREMENTS:
ABSOLUTELY FORBIDDEN: Never use these symbols in your response:
❌ # (hashtags)
❌ ## ### #### (markdown headers) 
❌ * ** *** (asterisks)
❌ ``` (code blocks)
❌ • - (bullet symbols)
❌ <picture> tags

✅ REQUIRED FORMAT:
- Write clean, flowing text in professional Turkish
- Use natural sentences and paragraphs
- Instead of '## Başlık', write 'Başlık:' or just the title normally
- Instead of '* item', write '1. item' or 'item content in sentences'
- Instead of code blocks, write explanations in plain text
- Instead of bullet points, use numbered lists or flowing paragraphs
- Make content elegant, readable, and presentation-ready
- Think like writing for a business presentation, not technical documentation
- Output should be clean HTML-ready text, no markdown artifacts";
        
        $categorySlug = $feature->aiFeatureCategory ? $feature->aiFeatureCategory->slug : 'other';
        switch ($categorySlug) {
            case 'content-creation':
                return $basePrompt . " Create high-quality, engaging content that provides real value to readers. Write in Turkish language." . $formatRules;
            case 'web-editor':
                return $basePrompt . " Help users optimize and improve their web content. Provide practical suggestions. Respond in Turkish." . $formatRules;
            case 'analysis':
                return $basePrompt . " Analyze the given content thoroughly and provide detailed insights. Be objective and precise. Write in Turkish." . $formatRules;
            default:
                return $basePrompt . " Provide helpful and accurate assistance. Always respond in Turkish language." . $formatRules;
        }
    }

    /**
     * Feature için maksimum token sayısını belirle
     */
    private function getMaxTokensForFeature($feature): int
    {
        switch ($feature->response_length) {
            case 'short':
                return 300;
            case 'medium':
                return 800;
            case 'long':
                return 1500;
            case 'variable':
                return 1000;
            default:
                return 800;
        }
    }

    
    /**
     * Prowess test için conversation kaydı oluştur
     */
    private function createProwessConversationRecord($feature, string $inputText, string $aiResponse, int $tokensUsed, $tenantId)
    {
        try {
            $userId = auth()->id() ?: 1;
            
            // Conversation oluştur
            $conversation = \Modules\AI\App\Models\Conversation::create([
                'title' => 'Prowess Test: ' . $feature->name,
                'type' => 'prowess_test',
                'feature_name' => $feature->name,
                'is_demo' => false,
                'user_id' => $userId,
                'tenant_id' => $tenantId,
                'total_tokens_used' => $tokensUsed,
                'status' => 'active',
                'metadata' => [
                    'source' => 'prowess_page',
                    'feature_id' => $feature->id,
                    'feature_slug' => $feature->slug,
                    'complexity_level' => $feature->complexity_level,
                    'category' => $feature->aiFeatureCategory ? $feature->aiFeatureCategory->slug : 'other'
                ]
            ]);
            
            // User input message
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'content' => $inputText,
                'tokens' => (int)(strlen($inputText) / 4),
                'message_type' => 'prowess_test',
                'metadata' => [
                    'feature_name' => $feature->name,
                    'input_length' => strlen($inputText)
                ]
            ]);
            
            // AI response message
            \Modules\AI\App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $aiResponse,
                'tokens' => (int)(strlen($aiResponse) / 4),
                'model_used' => $this->getCurrentProviderModel(),
                'message_type' => 'prowess_test',
                'metadata' => [
                    'feature_name' => $feature->name,
                    'response_length' => strlen($aiResponse),
                    'processing_source' => $feature->hasQuickPrompt() ? 'new_template_system' : 'legacy_system'
                ]
            ]);
            
            \Log::info('🎯 Prowess Conversation Oluşturuldu', [
                'conversation_id' => $conversation->id,
                'feature' => $feature->name,
                'tenant_id' => $tenantId,
                'tokens_used' => $tokensUsed
            ]);
            
            return $conversation;
            
        } catch (\Exception $e) {
            \Log::error('Prowess Conversation Error: ' . $e->getMessage(), [
                'feature' => $feature->name,
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Feature uyumluluk kontrolü - Akıllı uyarı sistemi
     */
    private function checkFeatureCompatibility($currentFeature, $inputText): ?array
    {
        try {
            // Akıllı analiz yap
            $analysisResult = ai_analyze_question($inputText);
            
            if (!$analysisResult['success']) {
                return null;
            }
            
            $recommendedFeature = $analysisResult['recommended_feature'];
            $confidence = $analysisResult['confidence'];
            
            // Mevcut feature ile önerilen feature aynı mı?
            if ($currentFeature->slug === $recommendedFeature) {
                return [
                    'is_compatible' => true,
                    'confidence' => $confidence,
                    'message' => 'Bu soru seçilen feature\'a uygun.'
                ];
            }
            
            // Farklı feature öneriliyor ve confidence yüksekse uyarı ver
            if ($confidence >= 0.75) {
                // Önerilen feature'ı bul
                $suggestedFeature = \Modules\AI\App\Models\AIFeature::where('slug', $recommendedFeature)
                    ->where('status', 'active')
                    ->first();
                
                if ($suggestedFeature) {
                    return [
                        'is_compatible' => false,
                        'confidence' => $confidence,
                        'recommended_feature' => $recommendedFeature,
                        'recommended_feature_name' => $suggestedFeature->name,
                        'current_feature' => $currentFeature->slug,
                        'current_feature_name' => $currentFeature->name,
                        'message' => "Bu soru '{$suggestedFeature->name}' feature'ına daha uygun. (Güven: " . round($confidence * 100) . "%)",
                        'suggestion' => "Daha iyi sonuç için '{$suggestedFeature->name}' feature'ını kullanmayı deneyin."
                    ];
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Şirket Profili kullanılabilirlik kontrolü
     * Tenant'ın profil bilgileri var mı kontrol eder
     */
    private function checkCompanyProfileAvailability(): bool
    {
        try {
            // AI Tenant Profile tablosundan gerçek profil verilerini kontrol et
            $aiProfile = \Modules\AI\app\Models\AITenantProfile::currentOrCreate();
            
            // Minimum profil tamamlanma kontrolü - temel bilgiler ve sektör yeterli
            $hasBasicCompanyInfo = $aiProfile->company_info && 
                                  !empty($aiProfile->company_info['brand_name']);
                                  
            $hasSectorSelection = $aiProfile->sector_details && 
                                !empty($aiProfile->sector_details['sector_selection']);
            
            // Bu minimum bilgiler varsa profil kullanılabilir
            $isBasicallyComplete = $hasBasicCompanyInfo && $hasSectorSelection;
            
            \Log::info('Company Profile Availability Check', [
                'tenant_id' => tenant('id'),
                'has_brand_name' => !empty($aiProfile->company_info['brand_name'] ?? ''),
                'has_sector' => !empty($aiProfile->sector_details['sector_selection'] ?? ''),
                'is_available' => $isBasicallyComplete,
                'company_info_count' => count($aiProfile->company_info ?? []),
                'sector_info_count' => count($aiProfile->sector_details ?? [])
            ]);
            
            return $isBasicallyComplete;
        } catch (\Exception $e) {
            \Log::warning('AI Profile kontrolü başarısız: ' . $e->getMessage());
            return false; // Güvenli fallback - hata varsa profil yok sayılsın
        }
    }

    /**
     * Şu anda aktif olan provider'ın model bilgisini al
     */
    private function getCurrentProviderModel(): string
    {
        try {
            $defaultProvider = \Modules\AI\App\Models\AIProvider::getDefault();
            if ($defaultProvider) {
                return $defaultProvider->name . '/' . $defaultProvider->default_model;
            }
            
            return 'unknown/unknown';
        } catch (\Exception $e) {
            return 'unknown/error';
        }
    }
}