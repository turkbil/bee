<?php
namespace Modules\AI\App\Http\Controllers\Admin\Settings;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\DeepSeekService;
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
        // Artık sadece ai_providers tablosunu kullanıyoruz
        $providers = AIProvider::orderBy('priority', 'desc')->get();
        
        // Global config'den varsayılan ayarları al
        $globalSettings = [
            'enabled' => config('ai.enabled', true),
            'debug' => config('ai.debug', false),
            'cache_duration' => config('ai.cache_duration', 60),
            'default_provider' => config('ai.default_provider', 'openai'),
            'default_model' => config('ai.default_model', 'gpt-4o-mini'),
            'global_token_limit' => config('ai.global_token_limit', 100000),
        ];
        
        // Aktif provider'ı belirle (config'den veya ilk default provider)
        $activeProvider = $providers->where('is_default', true)->first() 
            ?? $providers->where('name', $globalSettings['default_provider'])->first()
            ?? $providers->first();
        
        return view('ai::admin.settings.api', compact('providers', 'globalSettings', 'activeProvider'));
    }
    
    public function limits()
    {
        // Config tabanlı limit ayarları
        $limitSettings = [
            'global_token_limit' => config('ai.global_token_limit', 100000),
            'daily_token_limit' => config('ai.daily_token_limit', 10000),
            'request_token_limit' => config('ai.request_token_limit', 4000),
            'rate_limit_per_minute' => config('ai.rate_limit_per_minute', 60),
            'rate_limit_per_hour' => config('ai.rate_limit_per_hour', 1000),
            'concurrent_requests' => config('ai.concurrent_requests', 5),
            'max_retries' => config('ai.max_retries', 3),
            'timeout' => config('ai.timeout', 30),
        ];
        
        return view('ai::admin.settings.limits', compact('limitSettings'));
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
        // Config tabanlı genel ayarlar
        $generalSettings = [
            'default_language' => config('ai.integrations.page.supported_languages.0', 'tr'),
            'response_format' => 'markdown',
            'cache_duration' => config('ai.cache_duration', 60),
            'concurrent_requests' => config('ai.token_management.rate_limit_per_minute', 10),
            'content_filtering' => config('ai.security.enable_content_filter', true),
            'rate_limiting' => config('ai.security.enable_rate_limiting', true),
            'detailed_logging' => config('ai.logging.enabled', true),
            'performance_monitoring' => config('ai.performance.cache_responses', false),
        ];
        
        return view('ai::admin.settings.general', compact('generalSettings'));
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
        // Provider seçimi kontrolü - artık sadece ai_providers tablosu
        if ($request->has('action') && $request->action === 'set_active_provider') {
            $providerId = $request->active_provider;
            
            $provider = AIProvider::where('id', $providerId)
                ->where('is_active', true)
                ->first();
                
            if ($provider) {
                // Diğer provider'ları varsayılan olmaktan çıkar
                AIProvider::where('id', '!=', $providerId)->update(['is_default' => false]);
                
                // Yeni provider'ı varsayılan yap
                $provider->is_default = true;
                $provider->save();
                
                return response()->json(['success' => true, 'message' => 'Provider başarıyla değiştirildi']);
            }
            
            return response()->json(['success' => false, 'message' => 'Geçersiz provider']);
        }
        
        // Provider settings güncelleme - artık sadece ai_providers tablosu
        $request->validate([
            'provider_id' => 'required|exists:ai_providers,id',
            'api_key' => 'nullable|string',
            'default_model' => 'required|string',
            'temperature' => 'required|numeric|min:0|max:2',
            'max_tokens' => 'required|integer|min:1|max:32000',
        ]);
        
        $provider = AIProvider::findOrFail($request->provider_id);
        
        // API anahtarı sadece dolu ise güncelle
        if ($request->filled('api_key')) {
            $provider->api_key = $request->api_key;
        }
        
        // Model ve settings güncelle
        $provider->default_model = $request->default_model;
        
        // Provider settings JSON'ını güncelle
        $settings = $provider->default_settings ?? [];
        $settings['temperature'] = (float) $request->temperature;
        $settings['max_tokens'] = (int) $request->max_tokens;
        $provider->default_settings = $settings;
        
        $provider->save();
        
        return redirect()->back()->with('success', 'Provider ayarları güncellendi');
    }
    
    public function updateLimits(Request $request)
    {
        $request->validate([
            'global_token_limit' => 'required|integer|min:1000',
            'daily_token_limit' => 'required|integer|min:100',
            'request_token_limit' => 'required|integer|min:100',
            'rate_limit_per_minute' => 'required|integer|min:1',
            'rate_limit_per_hour' => 'required|integer|min:10',
            'concurrent_requests' => 'required|integer|min:1',
            'max_retries' => 'required|integer|min:1',
            'timeout' => 'required|integer|min:10',
        ]);
        
        // Config dosyasında bu ayarlar bulunduğu için
        // Bu fonksiyon artık sadece bilgilendirme amaçlı
        
        return redirect()->back()->with('info', 'Limit ayarları config/ai.php dosyasından yönetiliyor');
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
        
        // Config dosyasında bu ayarlar bulunduğu için
        // Bu fonksiyon artık sadece bilgilendirme amaçlı
        
        return redirect()->back()->with('info', 'Genel ayarlar config/ai.php dosyasından yönetiliyor');
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
            // Provider ID ya da name ile arama
            if ($request->has('provider_id')) {
                $provider = AIProvider::find($request->provider_id);
            } else {
                $providerName = $request->provider ?? config('ai.default_provider', 'openai');
                $provider = AIProvider::where('name', $providerName)->first();
            }
            
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