<?php
namespace Modules\AI\App\Http\Controllers\Admin\Settings;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Models\Setting;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Models\AIProvider;

class SettingsController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }
    
    public function api()
    {
        $settings = Setting::first() ?: new Setting();
        
        // AI Provider'lardan veri çek ve settings'e ekle
        $providers = AIProvider::orderBy('priority', 'desc')->get();
        $providerData = [];
        
        foreach ($providers as $provider) {
            $providerData[$provider->name] = [
                'name' => $provider->display_name,
                'service_class' => $provider->service_class,
                'model' => $provider->default_model,
                'available_models' => $provider->available_models,
                'api_key' => $provider->api_key,
                'base_url' => $provider->base_url,
                'is_active' => $provider->is_active,
                'is_default' => $provider->is_default,
                'priority' => $provider->priority,
                'average_response_time' => $provider->average_response_time,
                'description' => $provider->description,
                'default_settings' => $provider->default_settings,
            ];
        }
        
        // Settings'e provider verilerini ekle
        $settings->providers = $providerData;
        
        // Aktif provider'ı belirle
        if (!$settings->active_provider) {
            $defaultProvider = $providers->where('is_default', true)->first();
            if ($defaultProvider) {
                $settings->active_provider = $defaultProvider->name;
            } else {
                $settings->active_provider = $providers->first()->name ?? 'openai';
            }
        }
        
        return view('ai::admin.settings.api', compact('settings'));
    }
    
    public function limits()
    {
        $settings = Setting::first() ?: new Setting();
        return view('ai::admin.settings.limits', compact('settings'));
    }
    
    public function prompts()
    {
        $prompts = Prompt::orderBy('is_system', 'desc')
            ->orderBy('is_default', 'desc')
            ->orderBy('is_common', 'desc')
            ->orderBy('name')
            ->get();
            
        $commonPrompt = Prompt::where('is_common', true)->first();
        
        return view('ai::admin.settings.prompts', compact('prompts', 'commonPrompt'));
    }
    
    public function general()
    {
        $settings = Setting::first() ?: new Setting();
        return view('ai::admin.settings.general', compact('settings'));
    }
    
    public function managePrompt($id = null)
    {
        $prompt = null;
        
        if ($id) {
            $prompt = Prompt::findOrFail($id);
        }
        
        return view('ai::admin.settings.prompts-manage', compact('prompt'));
    }
    
    public function updateApi(Request $request)
    {
        $settings = Setting::first() ?: new Setting();
        
        // Provider seçimi kontrolü
        if ($request->has('action') && $request->action === 'set_active_provider') {
            $newProvider = $request->active_provider;
            
            $provider = AIProvider::where('name', $newProvider)
                ->where('is_active', true)
                ->first();
                
            if ($provider) {
                // Diğer provider'ları varsayılan olmaktan çıkar
                AIProvider::where('name', '!=', $newProvider)->update(['is_default' => false]);
                
                // Yeni provider'ı varsayılan yap
                $provider->is_default = true;
                $provider->save();
                
                // Settings'te active provider'ı güncelle
                $settings->active_provider = $newProvider;
                $settings->save();
                
                return response()->json(['success' => true, 'message' => 'Provider başarıyla değiştirildi']);
            }
            
            return response()->json(['success' => false, 'message' => 'Geçersiz provider']);
        }
        
        // Normal ayar güncelleme
        $request->validate([
            'api_key' => 'nullable|string',
            'model' => 'required|string',
            'max_tokens' => 'required|integer|min:1',
            'temperature' => 'required|numeric|min:0',
            'enabled' => 'boolean',
        ]);
        
        $activeProviderName = $settings->active_provider ?? 'openai';
        $activeProvider = AIProvider::where('name', $activeProviderName)->first();
        
        if ($activeProvider) {
            // API anahtarı sadece dolu ise güncelle
            if ($request->filled('api_key')) {
                $activeProvider->api_key = $request->api_key;
            }
            
            // Model güncelle
            $activeProvider->default_model = $request->model;
            $activeProvider->save();
        }
        
        // Diğer ayarları settings'te güncelle
        $settings->model = $request->model;
        $settings->max_tokens = $request->max_tokens;
        $settings->temperature = $request->temperature;
        $settings->enabled = $request->boolean('enabled');
        $settings->save();
        
        return redirect()->back()->with('success', 'API ayarları güncellendi');
    }
    
    public function updateLimits(Request $request)
    {
        $request->validate([
            'max_question_length' => 'required|integer|min:1',
            'max_daily_questions' => 'required|integer|min:0',
            'max_monthly_questions' => 'required|integer|min:0',
            'question_token_limit' => 'required|integer|min:1',
            'free_question_tokens_daily' => 'required|integer|min:0',
            'charge_question_tokens' => 'boolean',
        ]);
        
        $settings = Setting::first() ?: new Setting();
        $settings->fill($request->all());
        $settings->charge_question_tokens = $request->boolean('charge_question_tokens');
        $settings->save();
        
        return redirect()->back()->with('success', 'Limit ayarları güncellendi');
    }
    
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'default_language' => 'required|string|in:tr,en',
            'response_format' => 'required|string|in:markdown,plain,html',
            'cache_duration' => 'required|integer|min:0',
            'concurrent_requests' => 'required|integer|min:1',
            'content_filtering' => 'boolean',
            'rate_limiting' => 'boolean',
            'detailed_logging' => 'boolean',
            'performance_monitoring' => 'boolean',
        ]);
        
        $settings = Setting::first() ?: new Setting();
        $settings->fill($request->all());
        $settings->content_filtering = $request->boolean('content_filtering');
        $settings->rate_limiting = $request->boolean('rate_limiting');
        $settings->detailed_logging = $request->boolean('detailed_logging');
        $settings->performance_monitoring = $request->boolean('performance_monitoring');
        $settings->save();
        
        return redirect()->back()->with('success', 'Genel ayarlar güncellendi');
    }
    
    public function storePrompt(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);
        
        $prompt = new Prompt();
        $prompt->name = $request->name;
        $prompt->content = $request->content;
        $prompt->is_active = $request->boolean('is_active', true);
        $prompt->is_default = $request->boolean('is_default', false);
        
        // Eğer varsayılan yapılacaksa diğerlerini kaldır
        if ($prompt->is_default) {
            Prompt::where('is_default', true)->update(['is_default' => false]);
        }
        
        $prompt->save();
        
        return redirect()->route('admin.ai.settings.prompts.manage', $prompt->id)->with('success', 'Prompt başarıyla eklendi');
    }
    
    public function updatePrompt(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'is_default' => 'boolean',
        ]);
        
        $prompt = Prompt::findOrFail($id);
        
        // Gizli sistem promptları düzenlenebilir, diğer sistem promptları düzenlenemez
        if ($prompt->is_system && !$prompt->is_common && !in_array($prompt->prompt_type, ['hidden_system', 'secret_knowledge', 'conditional'])) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem promptu düzenlenemez'
            ], 403);
        }
        
        $prompt->name = $request->name;
        $prompt->content = $request->content;
        $prompt->is_default = $request->boolean('is_default', false);
        
        // Eğer varsayılan yapılacaksa diğerlerini kaldır
        if ($prompt->is_default) {
            Prompt::where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
        }
        
        $prompt->save();
        
        return redirect()->route('admin.ai.settings.prompts.manage', $prompt->id)->with('success', 'Prompt başarıyla güncellendi');
    }
    
    public function getPrompt($id)
    {
        $prompt = Prompt::findOrFail($id);
        return response()->json($prompt);
    }
    
    public function deletePrompt($id)
    {
        $prompt = Prompt::findOrFail($id);
        
        // Sistem promptları ve ortak özellikler silinemez
        if ($prompt->is_system || $prompt->is_common || in_array($prompt->prompt_type, ['hidden_system', 'secret_knowledge', 'conditional', 'common'])) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem promptu veya ortak özellikler silinemez'
            ], 403);
        }
        
        $prompt->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Prompt başarıyla silindi'
        ]);
    }
    
    public function makeDefaultPrompt($id)
    {
        $prompt = Prompt::findOrFail($id);
        
        if ($prompt->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem promptu varsayılan yapılamaz'
            ], 403);
        }
        
        // Diğer varsayılanları kaldır
        Prompt::where('is_default', true)->update(['is_default' => false]);
        
        $prompt->is_default = true;
        $prompt->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Prompt varsayılan olarak ayarlandı'
        ]);
    }
    
    public function updateCommonPrompt(Request $request)
    {
        $request->validate([
            'common_content' => 'required|string',
        ]);
        
        $prompt = Prompt::where('is_common', true)->first();
        
        if (!$prompt) {
            $prompt = new Prompt();
            $prompt->name = 'Ortak Özellikler';
            $prompt->is_common = true;
            $prompt->is_system = true;
            $prompt->is_active = true;
        }
        
        $prompt->content = $request->common_content;
        $prompt->save();
        
        return redirect()->back()->with('success', 'Ortak özellikler güncellendi');
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string',
            'model' => 'required|string',
            'max_tokens' => 'required|integer|min:1',
            'temperature' => 'required|numeric|min:0',
            'enabled' => 'boolean',
        ]);
        
        // API anahtarı boş ise request'ten çıkar (mevcut değeri koru)
        $data = $request->all();
        if (!$request->filled('api_key')) {
            unset($data['api_key']);
        }
        
        $settings = $this->aiService->updateSettings($data);
        
        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Ayarlar güncellenirken bir hata oluştu.'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Ayarlar başarıyla güncellendi.'
        ]);
    }
    
    public function testConnection(Request $request)
    {
        try {
            $settings = Setting::first();
            $providerName = $request->provider ?? ($settings->active_provider ?? 'openai');
            
            $provider = AIProvider::where('name', $providerName)->first();
            
            if (!$provider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider bulunamadı'
                ], 404);
            }
            
            $serviceClass = "Modules\\AI\\App\\Services\\{$provider->service_class}";
            
            if (!class_exists($serviceClass)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service class bulunamadı: ' . $serviceClass
                ], 404);
            }
            
            $service = new $serviceClass();
            
            // API key ve diğer ayarları set et
            if (!empty($provider->api_key)) {
                $service->setApiKey($provider->api_key);
            }
            if (!empty($provider->base_url)) {
                $service->setBaseUrl($provider->base_url);
            }
            if (!empty($provider->default_model)) {
                $service->setModel($provider->default_model);
            }
            
            $startTime = microtime(true);
            $response = $service->ask([
                ['role' => 'user', 'content' => $request->test_message ?? 'Merhaba, test mesajı']
            ]);
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            if ($response && strlen($response) > 0) {
                // Performansı güncelle
                $provider->average_response_time = $responseTime;
                $provider->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Provider test başarılı!',
                    'response' => substr($response, 0, 200) . (strlen($response) > 200 ? '...' : ''),
                    'response_time' => $responseTime,
                    'api_endpoint' => $provider->base_url,
                    'model_used' => $provider->default_model,
                    'provider_name' => $provider->display_name
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider yanıt döndürmedi'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function features()
    {
        return view('ai::admin.features.dashboard');
    }
    
    public function examples()
    {
        // Token durumu bilgileri (YENİ SİSTEM)
        $tenantId = tenant('id') ?: '1';
        $tokenStats = ai_get_token_stats($tenantId);
        $tokenStatus = [
            'remaining_tokens' => $tokenStats['remaining'],
            'total_tokens' => $tokenStats['total_purchased'],
            'daily_usage' => ai_get_total_used($tenantId), // Geçici - daha sonra daily hesaplama eklenecek
            'monthly_usage' => $tokenStats['total_used'],
            'provider' => config('ai.default_provider', 'deepseek'),
            'provider_active' => !empty(config('ai.providers.deepseek.api_key'))
        ];
        
        // AI özellikleri ve kategorileri
        $features = [
            'active' => [
                'content_creation' => [
                    [
                        'name' => 'İçerik Oluşturma',
                        'description' => 'Başlık veya konu vererek otomatik içerik oluşturma',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Blog yazıları, makaleler, ürün açıklamaları',
                        'example' => 'ai_generate_content(\'page\', \'Laravel Nedir?\', \'blog_post\')'
                    ],
                    [
                        'name' => 'Şablondan İçerik',
                        'description' => 'Hazır şablonları kullanarak içerik üretme',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Ürün sayfaları, hizmet tanıtımları',
                        'example' => 'AI::page()->generateFromTemplate()'
                    ],
                    [
                        'name' => 'Başlık Alternatifleri',
                        'description' => 'Bir konu için farklı başlık önerileri',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'SEO optimizasyonu, A/B testleri'
                    ],
                    [
                        'name' => 'İçerik Özeti',
                        'description' => 'Uzun metinleri özetleme',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Makale özetleri, meta açıklamalar'
                    ],
                    [
                        'name' => 'SSS Oluşturma',
                        'description' => 'İçerikten sıkça sorulan sorular üretme',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Destek sayfaları, ürün SSS'
                    ],
                    [
                        'name' => 'Eylem Çağrısı',
                        'description' => 'Etkili CTA metinleri oluşturma',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Landing page, e-posta kampanyaları'
                    ]
                ],
                'content_analysis' => [
                    [
                        'name' => 'SEO Analizi',
                        'description' => 'İçeriğin SEO uyumluluğunu kontrol etme',
                        'category' => 'İçerik Analizi',
                        'usage' => 'On-page SEO optimizasyonu',
                        'example' => 'ai_analyze_seo(\'page\', $content, \'hedef kelime\')'
                    ],
                    [
                        'name' => 'Okunabilirlik Analizi',
                        'description' => 'Metnin okunabilirlik skorunu hesaplama',
                        'category' => 'İçerik Analizi',
                        'usage' => 'İçerik kalitesi kontrolü'
                    ],
                    [
                        'name' => 'Anahtar Kelime Çıkarma',
                        'description' => 'Metinden önemli kelimeleri bulma',
                        'category' => 'İçerik Analizi',
                        'usage' => 'Tag oluşturma, kategorizasyon'
                    ],
                    [
                        'name' => 'Ton Analizi',
                        'description' => 'İçeriğin tonunu ve duygusunu analiz etme',
                        'category' => 'İçerik Analizi',
                        'usage' => 'Marka tutarlılığı kontrolü'
                    ]
                ],
                'content_optimization' => [
                    [
                        'name' => 'Meta Etiket Oluşturma',
                        'description' => 'SEO uyumlu meta title ve description',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'Arama motoru görünürlüğü',
                        'example' => 'ai_generate_meta_tags(\'page\', $content, $title)'
                    ],
                    [
                        'name' => 'İçerik Çevirisi',
                        'description' => 'Çok dilli içerik desteği',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'Uluslararası siteler',
                        'example' => 'ai_translate(\'page\', $content, \'en\')'
                    ],
                    [
                        'name' => 'İçerik Yeniden Yazma',
                        'description' => 'Mevcut içeriği farklı tonda yeniden yazma',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'İçerik güncelleme, ton değişimi'
                    ],
                    [
                        'name' => 'Başlık Optimizasyonu',
                        'description' => 'Başlıkları SEO ve tıklanma için optimize etme',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'CTR artırma, SEO iyileştirme'
                    ],
                    [
                        'name' => 'İçerik Genişletme',
                        'description' => 'Kısa içerikleri detaylandırma',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'İçerik zenginleştirme'
                    ]
                ]
            ],
            'potential' => [
                'advanced_features' => [
                    [
                        'name' => 'İyileştirme Önerileri',
                        'description' => 'İçerik için spesifik iyileştirme tavsiyeleri',
                        'category' => 'Gelişmiş Özellikler',
                        'usage' => 'İçerik kalitesi artırma'
                    ],
                    [
                        'name' => 'İlgili Konu Önerileri',
                        'description' => 'Benzer konular için içerik fikirleri',
                        'category' => 'Gelişmiş Özellikler',
                        'usage' => 'İçerik planlaması'
                    ],
                    [
                        'name' => 'İçerik Ana Hatları',
                        'description' => 'Detaylı içerik planı oluşturma',
                        'category' => 'Gelişmiş Özellikler',
                        'usage' => 'İçerik stratejisi'
                    ],
                    [
                        'name' => 'Sosyal Medya Postları',
                        'description' => 'İçerikten sosyal medya paylaşımları üretme',
                        'category' => 'Gelişmiş Özellikler',
                        'usage' => 'Sosyal medya yönetimi'
                    ]
                ]
            ]
        ];
        
        // Modül entegrasyonları
        $integrations = [
            'page' => [
                'name' => 'Page Modülü',
                'status' => 'active',
                'actions' => [
                    'generateContent' => 'İçerik oluşturma',
                    'analyzeSEO' => 'SEO analizi',
                    'translateContent' => 'Çeviri işlemleri',
                    'generateMetaTags' => 'Meta etiket oluşturma'
                ]
            ],
            'portfolio' => [
                'name' => 'Portfolio Modülü',
                'status' => 'potential',
                'actions' => [
                    'generateProjectDescription' => 'Proje açıklaması oluşturma',
                    'generateTags' => 'Otomatik etiketleme'
                ]
            ],
            'studio' => [
                'name' => 'Studio Modülü',
                'status' => 'potential',
                'actions' => [
                    'generateComponentContent' => 'Widget içerik önerileri',
                    'optimizeLayout' => 'Sayfa düzeni önerileri'
                ]
            ]
        ];
        
        return view('ai::admin.examples.index', compact('features', 'tokenStatus', 'integrations'));
    }
    
    public function test()
    {
        // Token durumu bilgileri (YENİ SİSTEM)
        $tenantId = tenant('id') ?: '1';
        $tokenStats = ai_get_token_stats($tenantId);
        $tokenStatus = [
            'remaining_tokens' => $tokenStats['remaining'],
            'total_tokens' => $tokenStats['total_purchased'],
            'daily_usage' => ai_get_total_used($tenantId), // Geçici - daha sonra daily hesaplama eklenecek
            'monthly_usage' => $tokenStats['total_used'],
            'provider' => config('ai.default_provider', 'deepseek'),
            'provider_active' => !empty(config('ai.providers.deepseek.api_key'))
        ];
        
        // Basitleştirilmiş özellik listesi (adminler için)
        $features = [
            'İçerik Oluşturma' => 'Blog yazısı, makale veya ürün açıklaması oluşturma',
            'Başlık Önerileri' => 'Bir konu için alternatif başlık önerileri',
            'İçerik Özeti' => 'Uzun metinleri kısa ve anlaşılır hale getirme',
            'SSS Oluşturma' => 'İçerikten sıkça sorulan sorular üretme',
            'SEO Analizi' => 'İçeriğin arama motoru optimizasyonunu kontrol etme',
            'İçerik Çevirisi' => 'Metinleri farklı dillere çevirme',
            'İçerik İyileştirme' => 'Mevcut içeriği daha iyi hale getirme',
            'Sosyal Medya Metni' => 'Sosyal medya için kısa paylaşım metinleri'
        ];
        
        return view('ai::admin.tests.test', compact('features', 'tokenStatus'));
    }

    /**
     * AI Provider'ları yönetme sayfası
     */
    public function providers()
    {
        $providers = AIProvider::orderBy('priority', 'desc')
            ->orderBy('average_response_time', 'asc')
            ->get();
        
        return view('ai::admin.settings.providers', compact('providers'));
    }

    /**
     * AI Provider güncelle
     */
    public function updateProvider(Request $request, $id)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'api_key' => 'nullable|string',
            'default_model' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'priority' => 'integer|min:0|max:100',
            'description' => 'nullable|string'
        ]);

        $provider = AIProvider::findOrFail($id);

        $provider->display_name = $request->display_name;
        $provider->description = $request->description;
        $provider->priority = $request->priority;
        $provider->is_active = $request->boolean('is_active');
        
        // API key sadece dolu ise güncelle
        if ($request->filled('api_key')) {
            $provider->api_key = $request->api_key;
        }
        
        if ($request->filled('default_model')) {
            $provider->default_model = $request->default_model;
        }

        // Eğer varsayılan yapılacaksa diğerlerini kaldır
        if ($request->boolean('is_default')) {
            AIProvider::where('id', '!=', $id)->update(['is_default' => false]);
            $provider->is_default = true;
        }

        $provider->save();

        // Cache'i temizle
        \Cache::forget('ai_providers');

        return redirect()->back()->with('success', 'AI Provider başarıyla güncellendi');
    }

    /**
     * AI Provider test et
     */
    public function testProvider($id)
    {
        $provider = AIProvider::findOrFail($id);
        
        try {
            $service = $provider->getServiceInstance();
            
            // Basit test mesajı
            $response = $service->ask([
                ['role' => 'user', 'content' => 'Merhaba, test mesajı']
            ]);
            
            if ($response && strlen($response) > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Provider test başarılı!',
                    'response' => substr($response, 0, 100) . '...',
                    'api_endpoint' => $provider->base_url,
                    'model_used' => $provider->default_model,
                    'provider_name' => $provider->display_name
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider yanıt döndürmedi'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provider test hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Provider'ı varsayılan yap
     */
    public function makeDefaultProvider($id)
    {
        $provider = AIProvider::findOrFail($id);
        
        if (!$provider->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Pasif provider varsayılan yapılamaz'
            ], 400);
        }

        // Diğer provider'ları varsayılan olmaktan çıkar
        AIProvider::where('id', '!=', $id)->update(['is_default' => false]);
        
        $provider->is_default = true;
        $provider->save();

        // Cache'i temizle
        \Cache::forget('ai_providers');

        return response()->json([
            'success' => true,
            'message' => 'Provider varsayılan olarak ayarlandı'
        ]);
    }

    /**
     * Provider önceliklerini güncelle (Sürükle-bırak)
     */
    public function updateProviderPriorities(Request $request)
    {
        $request->validate([
            'priorities' => 'required|array',
            'priorities.*.provider' => 'required|string',
            'priorities.*.priority' => 'required|integer|min:1'
        ]);

        try {
            // Yeni öncelikleri uygula
            foreach ($request->priorities as $item) {
                $providerName = $item['provider'];
                $newPriority = $item['priority'];
                
                AIProvider::where('name', $providerName)
                    ->update(['priority' => $newPriority]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Provider öncelikleri başarıyla güncellendi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Öncelik güncelleme hatası: ' . $e->getMessage()
            ], 500);
        }
    }
}