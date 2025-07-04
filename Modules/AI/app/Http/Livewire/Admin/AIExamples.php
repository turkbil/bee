<?php

namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use App\Helpers\TokenHelper;
use Modules\AI\App\Services\AIService;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class AIExamples extends Component
{
    public $selectedFeature = '';
    public $inputText = '';
    public $testResult = '';
    public $isLoading = false;
    public $showResult = false;
    public $testMode = 'demo'; // demo veya real
    public $tokensUsed = 0;
    public $processingTime = 0;
    
    public $tokenStatus = [];
    public $features = [];
    public $integrations = [];
    
    protected $listeners = ['refreshComponent' => '$refresh'];
    
    public function mount()
    {
        $this->loadData();
    }
    
    public function loadData()
    {
        // Token durumu (YENİ SİSTEM)
        $tokenStats = ai_get_token_stats();
        $this->tokenStatus = [
            'remaining_tokens' => $tokenStats['remaining'],
            'total_tokens' => $tokenStats['total_purchased'],
            'daily_usage' => ai_get_total_used(), // Geçici - daha sonra daily hesaplama eklenecek
            'monthly_usage' => $tokenStats['total_used'],
            'provider' => config('ai.default_provider', 'deepseek'),
            'provider_active' => !empty(config('ai.providers.deepseek.api_key'))
        ];
        
        // AI özellikleri - TAM LİSTE
        $this->features = [
            'active' => [
                'content_creation' => [
                    [
                        'name' => 'İçerik Oluşturma',
                        'description' => 'Başlık veya konu vererek otomatik içerik oluşturma',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Blog yazıları, makaleler, ürün açıklamaları',
                        'example' => 'ai_generate_content(\'page\', \'Laravel Nedir?\', \'blog_post\')',
                        'default_text' => 'Sivas Kangal köpeği'
                    ],
                    [
                        'name' => 'Şablondan İçerik',
                        'description' => 'Hazır şablonları kullanarak içerik üretme',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Ürün sayfaları, hizmet tanıtımları',
                        'example' => 'AI::page()->generateFromTemplate()',
                        'default_text' => 'iPhone 15 Pro Max'
                    ],
                    [
                        'name' => 'Başlık Alternatifleri',
                        'description' => 'Bir konu için farklı başlık önerileri',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'SEO optimizasyonu, A/B testleri',
                        'default_text' => 'Evde Kahve Demleme Teknikleri'
                    ],
                    [
                        'name' => 'İçerik Özeti',
                        'description' => 'Uzun metinleri özetleme',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Makale özetleri, meta açıklamalar',
                        'default_text' => 'Türkiye\'nin en büyük teknoloji fuarı TechnoFest bu yıl İstanbul\'da düzenlenecek. Etkinlikte yapay zeka, robotik, havacılık ve uzay teknolojileri alanında yüzlerce proje sergilenecek. Özellikle gençlerin teknolojiye olan ilgisini artırmayı hedefleyen fuarda, TEKNOFEST\'in ana sponsoru olan BAYKAR\'ın son teknoloji insansız hava araçları da tanıtılacak.'
                    ],
                    [
                        'name' => 'SSS Oluşturma',
                        'description' => 'İçerikten sıkça sorulan sorular üretme',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Destek sayfaları, ürün SSS',
                        'default_text' => 'Online yoga dersleri veriyoruz. Uzman eğitmenlerimizle evden yoga yapabilirsiniz.'
                    ],
                    [
                        'name' => 'Eylem Çağrısı',
                        'description' => 'Etkili CTA metinleri oluşturma',
                        'category' => 'İçerik Üretimi',
                        'usage' => 'Landing page, e-posta kampanyaları',
                        'default_text' => 'Organik zeytinyağı üretim çiftliği'
                    ]
                ],
                'content_analysis' => [
                    [
                        'name' => 'SEO Analizi',
                        'description' => 'İçeriğin SEO uyumluluğunu kontrol etme',
                        'category' => 'İçerik Analizi',
                        'usage' => 'On-page SEO optimizasyonu',
                        'example' => 'ai_analyze_seo(\'page\', $content, \'hedef kelime\')',
                        'default_text' => 'WordPress site hızlandırma rehberi: Cache, CDN ve optimizasyon teknikleri'
                    ],
                    [
                        'name' => 'Okunabilirlik Analizi',
                        'description' => 'Metnin okunabilirlik skorunu hesaplama',
                        'category' => 'İçerik Analizi',
                        'usage' => 'İçerik kalitesi kontrolü',
                        'default_text' => 'Blockchain teknolojisi merkezi olmayan bir veri tabanı sistemidir ve kriptografik hash fonksiyonları kullanarak bilgilerin güvenliğini sağlar.'
                    ],
                    [
                        'name' => 'Anahtar Kelime Çıkarma',
                        'description' => 'Metinden önemli kelimeleri bulma',
                        'category' => 'İçerik Analizi',
                        'usage' => 'Tag oluşturma, kategorizasyon',
                        'default_text' => 'Organik tarım yöntemleri ile yetiştirilen domates, biber ve patlıcan sebzeleri sağlıklı beslenmenin temel taşlarıdır.'
                    ],
                    [
                        'name' => 'Ton Analizi',
                        'description' => 'İçeriğin tonunu ve duygusunu analiz etme',
                        'category' => 'İçerik Analizi',
                        'usage' => 'Marka tutarlılığı kontrolü',
                        'default_text' => 'Merhaba arkadaşlar! Bugün sizlere süper eğlenceli bir tarif getirdim. Kesinlikle denemelisiniz!'
                    ]
                ],
                'content_optimization' => [
                    [
                        'name' => 'Meta Etiket Oluşturma',
                        'description' => 'SEO uyumlu meta title ve description',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'Arama motoru görünürlüğü',
                        'example' => 'ai_generate_meta_tags(\'page\', $content, $title)',
                        'default_text' => 'İstanbul\'da açılacak yeni müze'
                    ],
                    [
                        'name' => 'İçerik Çevirisi',
                        'description' => 'Çok dilli içerik desteği',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'Uluslararası siteler',
                        'example' => 'ai_translate(\'page\', $content, \'en\')',
                        'default_text' => 'Good morning everyone, welcome to our cooking show'
                    ],
                    [
                        'name' => 'İçerik Yeniden Yazma',
                        'description' => 'Mevcut içeriği farklı tonda yeniden yazma',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'İçerik güncelleme, ton değişimi',
                        'default_text' => 'Bu ürün çok kaliteli ve fiyatı uygun'
                    ],
                    [
                        'name' => 'Başlık Optimizasyonu',
                        'description' => 'Başlıkları SEO ve tıklanma için optimize etme',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'CTR artırma, SEO iyileştirme',
                        'default_text' => 'Web tasarım hizmetlerimiz profesyonel ekibimizle'
                    ],
                    [
                        'name' => 'İçerik Genişletme',
                        'description' => 'Kısa içerikleri detaylandırma',
                        'category' => 'İçerik Optimizasyonu',
                        'usage' => 'İçerik zenginleştirme',
                        'default_text' => 'Kahve sağlıklıdır'
                    ]
                ]
            ],
            'potential' => [
                'advanced_features' => [
                    [
                        'name' => 'İyileştirme Önerileri',
                        'description' => 'İçerik için spesifik iyileştirme tavsiyeleri',
                        'category' => 'Gelişmiş Özellikler',
                        'usage' => 'İçerik kalitesi artırma',
                        'default_text' => 'Bu yazımızda Laravel framework anlatılmıştır'
                    ],
                    [
                        'name' => 'İlgili Konu Önerileri',
                        'description' => 'Benzer konular için içerik fikirleri',
                        'category' => 'Gelişmiş Özellikler',
                        'usage' => 'İçerik planlaması',
                        'default_text' => 'React Native ile mobil uygulama geliştirme'
                    ],
                    [
                        'name' => 'İçerik Ana Hatları',
                        'description' => 'Detaylı içerik planı oluşturma',
                        'category' => 'Gelişmiş Özellikler',
                        'usage' => 'İçerik stratejisi',
                        'default_text' => 'Sıfırdan dropshipping işi nasıl kurulur'
                    ],
                    [
                        'name' => 'Sosyal Medya Postları',
                        'description' => 'İçerikten sosyal medya paylaşımları üretme',
                        'category' => 'Gelişmiş Özellikler',
                        'usage' => 'Sosyal medya yönetimi',
                        'default_text' => 'Yeni açtığımız restoranda İtalyan mutfağının en lezzetli yemeklerini deneyebilirsiniz'
                    ]
                ]
            ]
        ];
        
        // Modül entegrasyonları
        $this->integrations = [
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
            ]
        ];
    }
    
    public function selectFeature($featureName, $categoryKey, $featureIndex)
    {
        $this->selectedFeature = $featureName;
        $feature = $this->features['active'][$categoryKey][$featureIndex] ?? null;
        
        if ($feature && isset($feature['default_text'])) {
            $this->inputText = $feature['default_text'];
        }
        
        $this->showResult = false;
        $this->testResult = '';
    }
    
    public function testFeature($useRealAI = false)
    {
        $this->validate([
            'selectedFeature' => 'required',
            'inputText' => 'required|min:3'
        ], [
            'selectedFeature.required' => 'Lütfen bir özellik seçin',
            'inputText.required' => 'Test metni gereklidir',
            'inputText.min' => 'Test metni en az 3 karakter olmalıdır'
        ]);
        
        $this->isLoading = true;
        $this->showResult = false;
        $this->testMode = $useRealAI ? 'real' : 'demo';
        
        try {
            $tenant = tenant();
            
            if (!$tenant) {
                $tenant = Tenant::first();
            }
            
            $startTime = microtime(true);
            
            // Demo mode
            if (!$useRealAI) {
                sleep(1); // Simüle edilmiş bekleme
                $this->processingTime = round((microtime(true) - $startTime) * 1000);
                $this->tokensUsed = 0;
                $this->testResult = $this->getDemoResult($this->selectedFeature, $this->inputText);
                $this->showResult = true;
                $this->isLoading = false;
                return;
            }
            
            // Real AI mode
            $aiService = app(AIService::class);
            
            // Basit bir prompt oluştur
            $prompt = $this->buildPrompt($this->selectedFeature, $this->inputText);
            
            // AI'ya gönder
            $response = $aiService->sendRequest([[
                'role' => 'user',
                'content' => $prompt
            ]], $tenant->id);
            
            $this->processingTime = round((microtime(true) - $startTime) * 1000);
            
            if ($response['success']) {
                $this->testResult = $response['data']['content'] ?? 'Yanıt alınamadı';
                $this->tokensUsed = $response['tokens_used'] ?? 0;
                $this->showResult = true;
                
                // Token bilgilerini güncelle (YENİ SİSTEM)
                $this->tokenStatus['remaining_tokens'] = ai_get_token_balance();
                
                $this->emit('refreshComponent');
            } else {
                $this->addError('test', $response['error'] ?? 'AI servisi ile iletişim kurulamadı');
            }
            
        } catch (\Exception $e) {
            Log::error('AI Test Error: ' . $e->getMessage());
            $this->addError('test', 'Bir hata oluştu: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }
    
    private function buildPrompt($feature, $input)
    {
        $prompts = [
            // Content Creation
            'İçerik Oluşturma' => "'{$input}' konusu hakkında kapsamlı bir içerik oluştur. Blog yazısı formatında, SEO uyumlu olsun.",
            'Şablondan İçerik' => "'{$input}' ürünü için profesyonel bir tanıtım metni yaz. Özellikler ve faydaları vurgula.",
            'Başlık Alternatifleri' => "'{$input}' konusu için 5 farklı, yaratıcı ve SEO uyumlu başlık öner.",
            'İçerik Özeti' => "Şu metni 2-3 cümlede özetle: {$input}",
            'SSS Oluşturma' => "'{$input}' konusunda 5 sıkça sorulan soru ve cevaplarını oluştur.",
            'Eylem Çağrısı' => "'{$input}' için etkili bir eylem çağrısı (CTA) metni oluştur.",
            
            // Content Analysis
            'SEO Analizi' => "'{$input}' başlığının SEO açısından detaylı analizini yap. İyileştirme önerileri ver.",
            'Okunabilirlik Analizi' => "Şu metnin okunabilirlik analizini yap ve iyileştirme önerileri ver: {$input}",
            'Anahtar Kelime Çıkarma' => "Şu metinden en önemli 10 anahtar kelimeyi çıkar: {$input}",
            'Ton Analizi' => "Şu metnin tonunu analiz et (resmi/samimi/profesyonel/eğlenceli): {$input}",
            
            // Content Optimization
            'Meta Etiket Oluşturma' => "'{$input}' için SEO uyumlu meta title (60 karakter) ve meta description (160 karakter) oluştur.",
            'İçerik Çevirisi' => "Şu metni İngilizce'ye çevir: {$input}",
            'İçerik Yeniden Yazma' => "Şu metni daha profesyonel ve etkili bir şekilde yeniden yaz: {$input}",
            'Başlık Optimizasyonu' => "'{$input}' başlığını daha çekici ve SEO uyumlu hale getir. 3 alternatif sun.",
            'İçerik Genişletme' => "Şu kısa metni detaylandır ve genişlet: {$input}",
            
            // Advanced Features (Potential)
            'İyileştirme Önerileri' => "Şu içerik için spesifik iyileştirme önerileri ver: {$input}",
            'İlgili Konu Önerileri' => "'{$input}' konusuyla ilgili 5 içerik konusu öner.",
            'İçerik Ana Hatları' => "'{$input}' konusu için detaylı bir içerik planı ve ana hatları oluştur.",
            'Sosyal Medya Postları' => "'{$input}' konusunda Instagram, Twitter ve LinkedIn için 3 farklı post oluştur.",
        ];
        
        return $prompts[$feature] ?? "'{$input}' hakkında detaylı bilgi ver.";
    }
    
    private function getDemoResult($feature, $input)
    {
        $demoResults = [
            // Content Creation
            'İçerik Oluşturma' => "**{$input}**\n\nBu demo bir içeriktir. Gerçek AI kullanımında, burada seçtiğiniz konu hakkında detaylı ve özgün bir içerik oluşturulacaktır.\n\nDemo modda token harcanmaz, bu sadece sistemin nasıl çalıştığını gösterir.",
            'Şablondan İçerik' => "**{$input} - Ürün Tanıtımı**\n\n✨ Demo Tanıtım Metni:\nBu ürün kaliteli malzemelerden üretilmiştir. Gerçek AI kullanımında, ürününüzün özelliklerine göre profesyonel tanıtım metni oluşturulacaktır.",
            'Başlık Alternatifleri' => "Demo başlık önerileri:\n\n1. {$input} - Kapsamlı Rehber\n2. {$input} Hakkında Bilmeniz Gerekenler\n3. {$input}: En İyi Uygulamalar\n4. Profesyonel {$input} İpuçları\n5. {$input} - Başlangıç Kılavuzu",
            'İçerik Özeti' => "Demo özet: Verdiğiniz metin kısaca şunu anlatıyor... (Gerçek AI kullanımında detaylı özet yapılacaktır)",
            'SSS Oluşturma' => "Demo SSS:\n\n❓ {$input} nedir?\n✅ Demo yanıt burada olacak.\n\n❓ Nasıl kullanılır?\n✅ Demo kullanım bilgisi.\n\n(Gerçek AI kullanımında konuya özel SSS oluşturulacak)",
            'Eylem Çağrısı' => "Demo CTA: \"{$input} hakkında daha fazla bilgi alın! Hemen iletişime geçin.\" \n\n(Gerçek AI kullanımında etkili CTA metinleri oluşturulacak)",
            
            // Content Analysis
            'SEO Analizi' => "Demo SEO Analizi:\n- Başlık uzunluğu: Uygun\n- Anahtar kelime yoğunluğu: İyi\n- Okunabilirlik: Yüksek\n- Meta etiketler: Eksik\n\n(Gerçek analizde detaylı SEO raporu alırsınız)",
            'Okunabilirlik Analizi' => "Demo Okunabilirlik Raporu:\n📊 Skor: 75/100\n📝 Seviye: Orta\n💡 Öneri: Cümle uzunluklarını kısaltın\n\n(Gerçek analizde detaylı rapor)",
            'Anahtar Kelime Çıkarma' => "Demo Anahtar Kelimeler:\n🔑 {$input}\n🔑 teknoloji\n🔑 rehber\n🔑 bilgi\n🔑 ipucu\n\n(Gerçek AI ile metninize özel kelimeler)",
            'Ton Analizi' => "Demo Ton Analizi:\n🎯 Ton: Profesyonel\n😊 Duygu: Pozitif\n📈 Seviye: Orta\n\n(Gerçek analizde detaylı ton raporu)",
            
            // Content Optimization
            'Meta Etiket Oluşturma' => "Demo Meta Etiketleri:\n\n📋 Title: {$input} | Site Adı\n📝 Description: {$input} hakkında detaylı bilgi ve rehber. En güncel içerikler burada.\n\n(Gerçek AI ile SEO optimizasyonlu etiketler)",
            'İçerik Çevirisi' => "Demo Translation: This is a demo translation of your content. In real AI mode, you will get accurate professional translations.\n\n(Gerçek AI ile profesyonel çeviri)",
            'İçerik Yeniden Yazma' => "Demo Yeniden Yazım:\n\"Verdiğiniz metin daha profesyonel ve etkili bir şekilde yeniden yazılacaktır.\"\n\n(Gerçek AI ile ton ve stil optimizasyonu)",
            'Başlık Optimizasyonu' => "Demo Başlık Önerileri:\n\n✨ {$input} - Uzman Rehberi\n🚀 {$input}: Başarının Sırları\n💡 {$input} ile Fark Yaratın\n\n(Gerçek AI ile SEO optimizasyonlu başlıklar)",
            'İçerik Genişletme' => "Demo Genişletme:\n\"{$input}\" konusu hakkında daha detaylı bilgi, örnekler ve açıklamalar burada yer alacaktır.\n\n(Gerçek AI ile kapsamlı genişletme)",
            
            // Advanced Features
            'İyileştirme Önerileri' => "Demo İyileştirme Önerileri:\n📈 Daha fazla örnek ekleyin\n🎯 Call-to-action güçlendirin\n📱 Mobil uyumluluğu artırın\n\n(Gerçek AI ile spesifik öneriler)",
            'İlgili Konu Önerileri' => "Demo Konu Önerileri:\n\n1. {$input} Temelleri\n2. {$input} İpuçları\n3. {$input} Trendleri\n4. {$input} Araçları\n5. {$input} Stratejileri\n\n(Gerçek AI ile ilgili konular)",
            'İçerik Ana Hatları' => "Demo İçerik Planı:\n\n1. Giriş\n2. {$input} Nedir?\n3. Faydaları\n4. Uygulama Örnekleri\n5. Sonuç\n\n(Gerçek AI ile detaylı plan)",
            'Sosyal Medya Postları' => "Demo Sosyal Medya Postları:\n\n📱 Instagram: \"{$input} ile hayatınızı değiştirin! #demo\"\n🐦 Twitter: \"{$input} hakkında bilmeniz gerekenler\"\n💼 LinkedIn: \"Profesyonel {$input} rehberi\"\n\n(Gerçek AI ile platform özel içerik)",
        ];
        
        return $demoResults[$feature] ?? "Demo sonuç: {$input} için test sonucu burada görünecek.";
    }
    
    public function clearResult()
    {
        $this->showResult = false;
        $this->testResult = '';
        $this->selectedFeature = '';
        $this->inputText = '';
    }
    
    public function render()
    {
        return view('ai::admin.livewire.ai-examples-new');
    }
}