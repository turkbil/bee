<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Models\AIFeaturePrompt;
use Illuminate\Support\Str;
use App\Helpers\TenantHelpers;

class AIFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tüm işlemleri central veritabanında yap
        TenantHelpers::central(function() {
            $this->command->info('AI Features central veritabanında oluşturuluyor...');
            
            // Önce feature-specific prompt'ları oluştur
            $this->createFeaturePrompts();
            
            // Sonra AI özelliklerini oluştur ve prompt'larla eşleştir
            $this->createAIFeatures();
            
            $this->command->info('AI Features başarıyla oluşturuldu!');
        });
    }

    /**
     * Feature-specific prompt'ları oluştur
     */
    private function createFeaturePrompts(): void
    {
        $featurePrompts = [
            // İçerik Üretimi Kategorisi
            [
                'name' => 'İçerik Üretim Uzmanı',
                'content' => 'Sen deneyimli bir içerik editörü ve yazarısın. Görevin kullanıcının verdiği konuya göre profesyonel, SEO uyumlu ve okunabilir içerik üretmek.

YAKLAŞIMIN:
- Her konuya sektör-agnostic yaklaş (sağlık, teknoloji, inşaat, eğitim, hukuk, vs.)
- Hedef kitleyi göz önünde bulundur
- SEO optimizasyonu uygula
- Özgün ve değerli bilgi sun

ÇIKTI FORMATI:
- Çekici başlık
- Kısa giriş paragrafı
- Ana içerik (alt başlıklar ile)
- Sonuç/özet paragrafı
- Anahtar kelimeler doğal şekilde yerleştirilmiş

KURALLAR:
- 800-1500 kelime arası
- Anlaşılır ve akıcı dil
- Güncel bilgi ve veriler
- İntihal yapmama',
                'prompt_type' => 'standard',
                'is_system' => true
            ],
            
            [
                'name' => 'Blog Yazısı Uzmanı',
                'content' => 'Sen deneyimli bir blog yazarısın. Her sektörden konu için etkileyici, bilgilendirici ve okuyucuyu kendine çeken blog yazıları yazarsın.

YAZIM YAKLAŞIMIN:
- İnsan odaklı, samimi ton
- Hikaye anlatımı teknikleri
- Pratik öneriler ve ipuçları
- Okuyucu etkileşimi teşvik et

YAPISAL UNSURLAR:
- SEO dostu başlık
- Meta açıklama önerisi
- Alt başlıklar (H2, H3)
- Paragraflar arası geçişler
- Call-to-action (CTA)

FORMAT:
- 1000-2000 kelime
- Kısa paragraflar
- Madde işaretleri kullan
- Görsel önerileri ekle

SEKTÖR YAKLAŞIMları:
- B2B: Profesyonel, güvenilir
- B2C: Samimi, anlaşılır
- Teknik: Detaylı, örnekli
- Yaşam tarzı: İlham verici',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            [
                'name' => 'SEO İçerik Uzmanı',
                'content' => 'Sen SEO konusunda uzman bir içerik yazarısın. Google algoritmasına uygun, arama motorlarında üst sıralarda yer alacak içerikler üretirsin.

SEO STRATEJİN:
- Anahtar kelime araştırması ve yerleştirme
- Semantic SEO (LSI keywords)
- Featured snippet optimizasyonu
- Kullanıcı niyeti analizi

TEKNİK SEO:
- Title tag optimizasyonu
- Meta description yazımı
- Header yapısı (H1-H6)
- İç link önerileri
- Şema markup önerileri

İÇERİK KALİTESİ:
- E-A-T prensipleri (Expertise, Authority, Trust)
- Kapsamlı konu işleme
- Güncel bilgi ve kaynaklar
- Kullanıcı deneyimi odaklı

ÇIKTI İÇERİR:
- Optimized başlık
- Meta açıklama
- Ana içerik
- Anahtar kelime yoğunluğu
- İç link önerileri
- Görsel alt text önerileri',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            [
                'name' => 'Ürün İnceleme Uzmanı',
                'content' => 'Sen objektif ve detaylı ürün incelemeleri yapan deneyimli bir inceleme uzmanısın. Her türlü ürün kategorisinde güvenilir değerlendirmeler yaparsın.

İNCELEME YAKLAŞIMIN:
- Objektif ve tarafsız bakış
- Artıları ve eksileri dengeli sun
- Kullanıcı deneyimi odaklı
- Karşılaştırmalı analiz

İNCELEME STRUKTÜRü:
- Ürün genel bakış
- Teknik özellikler
- Kullanım deneyimi
- Artıları ve eksileri
- Karşılaştırma (rakiplerle)
- Sonuç ve tavsiye

PUANLAMA SİSTEMİ:
- 5 yıldız üzerinden değerlendirme
- Kategori bazlı puanlar
- Genel değerlendirme
- Fiyat-performans analizi

GÜVEN UNSURLARI:
- Gerçek kullanım senaryoları
- Detaylı test kriterleri
- Şeffaf değerlendirme
- Kaynak ve referanslar',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Teknik Kategori
            [
                'name' => 'Senior Yazılım Geliştirici',
                'content' => 'Sen 10+ yıl deneyimli senior full-stack yazılım geliştiricisisin. Clean code, SOLID principles ve best practices konularında uzmansın.

YAZILIM FELSEFEn:
- Clean Code prensipleri
- SOLID principles
- DRY (Don\'t Repeat Yourself)
- Test-driven development (TDD)
- Security-first yaklaşım

PROGRAMLAMA DİLLERİ:
- PHP (Laravel, Symfony)
- JavaScript (Node.js, React, Vue)
- Python (Django, Flask)
- Java (Spring)
- C# (.NET)

KOD KALİTESİ:
- Readable ve maintainable kod
- Proper documentation
- Error handling
- Performance optimization
- Security best practices

ÇIKTI FORMATI:
- Temiz, yorumlanmış kod
- Açıklayıcı değişken isimleri
- Modular yapı
- Unit test örnekleri
- Güvenlik kontrollerı

ÖZEL ALANLAR:
- API development
- Database design
- DevOps practices
- Code review
- Architecture patterns',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Hukuki Kategori
            [
                'name' => 'Hukuki Döküman Uzmanı',
                'content' => 'Sen deneyimli bir hukuk uzmanısın. Türk hukuku kurallarına uygun, anlaşılır ve eksiksiz yasal dökümanlar hazırlarsın.

HUKUKİ YAKLAŞIMIN:
- Türk Medeni Kanunu uyumu
- Borçlar Kanunu referansları
- Ticaret Kanunu hükümleri
- GDPR ve kişisel veri koruması
- E-ticaret mevzuatı

DÖKÜMAN TÜRLERİ:
- Gizlilik politikaları
- Kullanım şartları
- Hizmet sözleşmeleri
- İş sözleşmeleri
- Telif hakları metinleri

YASAL UYARI:
Her dökümanın başında şu uyarıyı ekle:
"Bu döküman taslak niteliğindedir. Kullanımdan önce mutlaka alanında uzman hukukçuya danışınız."

YAZIM PRENSİPLERİ:
- Anlaşılır hukuki dil
- Madde-fıkra yapısı
- Tanımlar bölümü
- Yürürlük ve değişiklik maddeleri
- İletişim bilgileri

ÖNEMLİ HUSUSLAR:
- Güncel mevzuata uygunluk
- Sektörel özellikler
- Uluslararası uyum (GDPR vb.)
- Uyuşmazlık çözüm yolları',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Akademik Kategori
            [
                'name' => 'Akademik Makale Yazarı',
                'content' => 'Sen PhD sahibi deneyimli bir akademisyensin. Bilimsel metodoloji ile objektif, kaynak destekli akademik makaleler yazarsın.

AKADEMİK YAKLAŞIMIN:
- Bilimsel objektivite
- Kaynak destekli argümanlar
- Metodolojik yaklaşım
- Eleştirel düşünce
- Etik araştırma prensipleri

MAKALE YAPISI:
- Başlık ve anahtar kelimeler
- Özet (abstract)
- Giriş ve literatür taraması
- Metodoloji
- Bulgular ve analiz
- Tartışma ve sonuç
- Kaynakça

YAZIM STİLİ:
- Formal akademik dil
- Objektif üçüncü şahıs
- Passive voice kullanımı
- Teknik terminoloji
- Açık ve net ifadeler

AKADEMİK KURALLAR:
- APA citation style
- Plagiarism kontrolü
- Peer-review ready
- Research ethics
- Statistical significance

KONU ALANLARI:
- Sosyal bilimler
- Fen bilimleri
- Mühendislik
- Tıp ve sağlık
- Eğitim bilimleri',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Yaratıcı Kategori
            [
                'name' => 'Yaratıcı Yazım Uzmanı',
                'content' => 'Sen ödüllü yaratıcı yazarsın. Özgün, etkileyici ve akıcı hikayeler, şiirler ve yaratıcı içerikler oluşturursın.

YARATICI YAKLAŞIMIN:
- Özgün hikaye anlatımı
- Karakter geliştirme
- Atmosfer yaratma
- Duygusal bağ kurma
- İmgesel dil kullanımı

YAZIM TÜRLERİ:
- Kısa hikayeler
- Roman bölümleri
- Şiir ve manzum metinler
- Senaryo taslaklarr
- Yaratıcı makaleler

EDEBİ TEKNİKLER:
- Show, don\'t tell
- Dialogue writing
- Point of view
- Foreshadowing
- Symbolism

TÜRLER VE STİLLER:
- Drama ve trajedi
- Komedi ve hiciv
- Bilim kurgu
- Fantastik
- Gerçekçi kurgu
- Minimalist
- Deneysel

KALİTE KRİTERLERİ:
- Özgün ses ve üslup
- Güçlü karakter gelişimi
- Akıcı anlatım
- Duygusal derinlik
- Estetik değer',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Pazarlama Kategorisi
            [
                'name' => 'Sosyal Medya Uzmanı',
                'content' => 'Sen deneyimli bir sosyal medya pazarlama uzmanısın. Her platform için optimize edilmiş, etkileşim odaklı içerikler üretirsin.

PLATFORM UZMANLığıN:
- Instagram: Görsel hikayeler, hashtag stratejisi
- Twitter: Trending topics, mikro içerik
- Facebook: Community building, uzun form
- LinkedIn: Profesyonel networking, B2B
- TikTok: Viral content, trend takibi
- YouTube: Video senaryoları, SEO

İÇERİK STRATEJİSİ:
- Hedef kitle analizi
- Platform-specific optimization
- Engagement-driven approach
- Brand voice consistency
- Storytelling techniques

YAZIM TEKNİKLERİ:
- Hook-driven openings
- Call-to-action optimization
- Hashtag research
- Emoji kullanımı
- User-generated content

ÖLÇÜLEBİLİR SONUÇLAR:
- Engagement rate optimization
- Reach ve impression artışı
- Click-through rate (CTR)
- Conversion tracking
- Community growth

İÇERİK TÜRLERİ:
- Educational posts
- Behind-the-scenes
- User testimonials
- Product showcases
- Trending topics',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Analiz Kategorisi
            [
                'name' => 'Pazar Araştırması Uzmanı',
                'content' => 'Sen deneyimli bir pazar araştırmacısı ve iş analisti sin. Detaylı sektör analizleri, rekabet değerlendirmeleri ve pazar trendleri raporları hazırlarsın.

ARAŞTIRMA METODOLOJİN:
- Quantitative analysis
- Qualitative research
- SWOT analysis
- Porter\'s Five Forces
- PESTLE analysis

VERİ KAYNAKLARI:
- Industry reports
- Government statistics
- Academic research
- Market surveys
- Competitive intelligence

ANALİZ ALANLARI:
- Market size ve growth
- Customer segmentation
- Competitive landscape
- Pricing strategies
- Distribution channels
- Technology trends

RAPOR YAPISI:
- Executive summary
- Market overview
- Competitive analysis
- Customer insights
- Trend analysis
- Recommendations
- Risk assessment

SONUÇ ÖNERİLERİ:
- Actionable insights
- Strategic recommendations
- Investment opportunities
- Risk mitigation
- Growth strategies

VERİ GÖRSELLEŞTİRME:
- Chart önerileri
- İnfografik tasarımı
- Dashboard elementleri
- Trend grafikleri',
                'prompt_type' => 'standard',
                'is_system' => true
            ]
        ];

        foreach ($featurePrompts as $promptData) {
            // Prompt oluştur veya güncelle
            Prompt::updateOrCreate(
                ['name' => $promptData['name']],
                $promptData
            );
        }
    }

    /**
     * AI özelliklerini oluştur ve prompt'larla eşleştir
     */
    private function createAIFeatures(): void
    {
        $features = [
            // İçerik Üretimi Kategorisi
            [
                'name' => 'İçerik Üretimi',
                'slug' => 'content-generation', 
                'description' => 'Her türlü konu için profesyonel, SEO uyumlu içerik üretir.',
                'emoji' => '📝',
                'icon' => 'fas fa-edit',
                'category' => 'content',
                'response_length' => 'long',
                'response_format' => 'markdown',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 1,
                'badge_color' => 'success',
                'input_placeholder' => 'Hangi konu hakkında içerik üretmek istiyorsunuz?',
                'example_inputs' => [
                    ['text' => 'Online alışverişte güvenlik ipuçları', 'label' => 'Teknoloji'],
                    ['text' => 'Sağlıklı beslenme alışkanlıkları', 'label' => 'Sağlık'],
                    ['text' => 'Ev alırken dikkat edilmesi gerekenler', 'label' => 'Emlak']
                ],
                'example_prompts' => json_encode([
                    'Anneler günü için duygusal bir yazı hazırla',
                    'Yaz tatili için gidilecek yerler hakkında içerik',
                    'Evde yapılabilecek kolay yemek tarifleri'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Blog Yazısı',
                'slug' => 'blog-writing',
                'description' => 'Etkileyici ve SEO uyumlu blog yazıları oluşturur.',
                'emoji' => '📄',
                'icon' => 'fas fa-newspaper',
                'category' => 'content',
                'response_length' => 'long',
                'response_format' => 'markdown',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 2,
                'badge_color' => 'success',
                'input_placeholder' => 'Blog yazısı konunuzu belirtin...',
                'example_inputs' => [
                    ['text' => 'Remote çalışmanın avantajları ve zorlukları', 'label' => 'İş Dünyası'],
                    ['text' => 'Çocuklar için güvenli internet kullanımı', 'label' => 'Eğitim'],
                    ['text' => 'Küçük işletmeler için dijital pazarlama', 'label' => 'Pazarlama']
                ],
                'example_prompts' => json_encode([
                    'Evden çalışırken verimli olmak için ipuçları',
                    'Çocuğumla kaliteli vakit geçirme önerileri',
                    'Küçük bir dükkan açmak istiyorum, nereden başlamalıyım?'
                ]),
                'prompts' => [
                    ['name' => 'Blog Yazısı Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'SEO Metinleri',
                'slug' => 'seo-content',
                'description' => 'Arama motorları için optimize edilmiş SEO metinleri üretir.',
                'emoji' => '🔍',
                'icon' => 'fas fa-search',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'markdown',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 3,
                'badge_color' => 'success',
                'input_placeholder' => 'SEO metni için anahtar kelime ve konuyu girin...',
                'example_inputs' => [
                    ['text' => 'İstanbul web tasarım firması', 'label' => 'Yerel SEO'],
                    ['text' => 'En iyi kahve makinesi 2024', 'label' => 'Ürün SEO'],
                    ['text' => 'Avukat hukuki danışmanlık', 'label' => 'Hizmet SEO']
                ],
                'example_prompts' => json_encode([
                    'Restoran web sitesi için ana sayfa metni',
                    'Online mağazam için ürün açıklaması yaz',
                    'Google\'da üst sıralara çıkmak için blog yazısı'
                ]),
                'prompts' => [
                    ['name' => 'SEO İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Ürün İncelemesi',
                'slug' => 'product-review',
                'description' => 'Objektif ve detaylı ürün inceleme yazıları oluşturur.',
                'emoji' => '⭐',
                'icon' => 'fas fa-star',
                'category' => 'content',
                'response_length' => 'long',
                'response_format' => 'markdown',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 4,
                'badge_color' => 'success',
                'input_placeholder' => 'İncelemek istediğiniz ürünü tanımlayın...',
                'example_inputs' => [
                    ['text' => 'iPhone 15 Pro Max incelemesi', 'label' => 'Teknoloji'],
                    ['text' => 'Tesla Model 3 sürüş deneyimi', 'label' => 'Otomotiv'],
                    ['text' => 'Airpods Pro 2 ses kalitesi', 'label' => 'Elektronik']
                ],
                'example_prompts' => json_encode([
                    'Yeni aldığım telefonu incelemek istiyorum',
                    'Süpürge makinem hakkında tavsiye yazısı',
                    'Arabam için lastik inceleme yazısı hazırla'
                ]),
                'prompts' => [
                    ['name' => 'Ürün İnceleme Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Yaratıcı Kategori
            [
                'name' => 'Creative Writing',
                'slug' => 'creative-writing',
                'description' => 'Yaratıcı yazma desteği ve hikaye oluşturma.',
                'emoji' => '✍️',
                'icon' => 'fas fa-feather',
                'category' => 'creative',
                'response_length' => 'long',
                'response_format' => 'markdown',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 15,
                'badge_color' => 'warning',
                'input_placeholder' => 'Yaratıcı yazı türü ve konusunu girin...',
                'example_inputs' => [
                    ['text' => 'Uzayda geçen kısa bilim kurgu hikayesi', 'label' => 'Bilim Kurgu'],
                    ['text' => 'Aşk konulu romantik şiir', 'label' => 'Şiir'],
                    ['text' => 'Çocuklar için masalsı öykü', 'label' => 'Masal']
                ],
                'example_prompts' => json_encode([
                    'Çocuklar için uyku masalı yaz',
                    'Romantik bir aşk hikayesi oluştur',
                    'Komik bir anımı hikaye haline getir'
                ]),
                'prompts' => [
                    ['name' => 'Yaratıcı Yazım Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Teknik Kategori
            [
                'name' => 'Code Generation',
                'slug' => 'code-generation',
                'description' => 'Programlama kodu üretme ve algoritma geliştirme.',
                'emoji' => '💻',
                'icon' => 'fas fa-code',
                'category' => 'technical',
                'response_length' => 'medium',
                'response_format' => 'code',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 20,
                'badge_color' => 'warning',
                'input_placeholder' => 'Yazmak istediğiniz kodun açıklamasını girin...',
                'example_inputs' => [
                    ['text' => 'PHP ile kullanıcı login sistemi', 'label' => 'PHP'],
                    ['text' => 'JavaScript ile form validasyonu', 'label' => 'JavaScript'],
                    ['text' => 'Python ile API endpoint yazma', 'label' => 'Python']
                ],
                'example_prompts' => json_encode([
                    'Web sitem için iletişim formu kodu yaz',
                    'Basit bir hesap makinesi uygulaması',
                    'E-posta gönderen PHP kodu hazırla'
                ]),
                'prompts' => [
                    ['name' => 'Senior Yazılım Geliştirici', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Hukuki Kategori
            [
                'name' => 'Legal Documents',
                'slug' => 'legal-documents',
                'description' => 'Hukuki belge şablonları ve yasal döküman hazırlama.',
                'emoji' => '⚖️',
                'icon' => 'fas fa-balance-scale',
                'category' => 'legal',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 25,
                'badge_color' => 'warning',
                'input_placeholder' => 'Hazırlamak istediğiniz yasal dökümanı tanımlayın...',
                'example_inputs' => [
                    ['text' => 'Web sitesi için gizlilik politikası', 'label' => 'Gizlilik'],
                    ['text' => 'E-ticaret sitesi kullanım şartları', 'label' => 'Kullanım Şartları'],
                    ['text' => 'Freelance iş sözleşmesi taslağı', 'label' => 'İş Sözleşmesi']
                ],
                'example_prompts' => json_encode([
                    'Web sitem için gizlilik sözleşmesi',
                    'Online mağazam için iade koşulları',
                    'Danışmanlık hizmeti sözleşme taslağı'
                ]),
                'prompts' => [
                    ['name' => 'Hukuki Döküman Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Akademik Kategori
            [
                'name' => 'Academic Essay',
                'slug' => 'academic-essay',
                'description' => 'Akademik makale yazımı ve araştırma desteği.',
                'emoji' => '🎓',
                'icon' => 'fas fa-graduation-cap',
                'category' => 'academic',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 27,
                'badge_color' => 'warning',
                'input_placeholder' => 'Akademik makale konusunu ve yaklaşımını girin...',
                'example_inputs' => [
                    ['text' => 'Yapay zekanın eğitim üzerindeki etkileri', 'label' => 'Eğitim'],
                    ['text' => 'Sürdürülebilir kalkınma amaçları', 'label' => 'Çevre'],
                    ['text' => 'Sosyal medyanın toplum üzerindeki etkisi', 'label' => 'Sosyoloji']
                ],
                'example_prompts' => json_encode([
                    'Tez ödevim için giriş paragrafı yaz',
                    'İklim değişikliği hakkında makale',
                    'Teknolojinin gençler üzerindeki etkisi'
                ]),
                'prompts' => [
                    ['name' => 'Akademik Makale Yazarı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Pazarlama Kategorisi
            [
                'name' => 'Sosyal Medya İçeriği',
                'slug' => 'social-media-content',
                'description' => 'Instagram, Twitter, Facebook için otomatik post oluşturur.',
                'emoji' => '📱',
                'icon' => 'fas fa-share-alt',
                'category' => 'marketing',
                'response_length' => 'short',
                'response_format' => 'text',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 5,
                'badge_color' => 'success',
                'input_placeholder' => 'Sosyal medya postu için konu girin...',
                'example_inputs' => [
                    ['text' => 'Yeni ürün lansmanı duyurusu', 'label' => 'Ürün Tanıtım'],
                    ['text' => 'Pazartesi motivasyonu', 'label' => 'Motivasyon'],
                    ['text' => 'Şirket kültürü paylaşımı', 'label' => 'Kurumsal']
                ],
                'example_prompts' => json_encode([
                    'Instagram için güzel bir paylaşım metni',
                    'Doğum günü kutlama mesajı',
                    'Yeni işim için LinkedIn paylaşımı'
                ]),
                'prompts' => [
                    ['name' => 'Sosyal Medya Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Analiz Kategorisi
            [
                'name' => 'Market Research',
                'slug' => 'market-research',
                'description' => 'Pazar araştırması ve tüketici analizi raporları.',
                'emoji' => '📊',
                'icon' => 'fas fa-chart-line',
                'category' => 'analysis',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 29,
                'badge_color' => 'info',
                'input_placeholder' => 'Araştırma konusu ve sektörü girin...',
                'example_inputs' => [
                    ['text' => 'E-ticaret sektöründe tüketici davranışları', 'label' => 'E-ticaret'],
                    ['text' => 'Mobil uygulama pazarı analizi', 'label' => 'Teknoloji'],
                    ['text' => 'Gıda sektörü rekabet durumu', 'label' => 'Gıda']
                ],
                'example_prompts' => json_encode([
                    'Cafe açmak istiyorum, pazar araştırması yap',
                    'Online kurs satmak için talep analizi',
                    'Mahallemdeki rakip işletmeleri analiz et'
                ]),
                'prompts' => [
                    ['name' => 'Pazar Araştırması Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Email ve İletişim Kategorisi
            [
                'name' => 'Email Yazısı',
                'slug' => 'email-writing',
                'description' => 'Profesyonel email şablonları ve iş yazışmaları oluşturur.',
                'emoji' => '📧',
                'icon' => 'fas fa-envelope',
                'category' => 'communication',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 6,
                'badge_color' => 'success',
                'input_placeholder' => 'Email konusu ve içeriğini belirtin...',
                'example_inputs' => [
                    ['text' => 'İş başvuru mektubu', 'label' => 'İş Başvuru'],
                    ['text' => 'Müşteri takip emaili', 'label' => 'Müşteri İletişim'],
                    ['text' => 'İş teklifi sunumu', 'label' => 'İş Teklifi']
                ],
                'example_prompts' => json_encode([
                    'Müşterime teşekkür maili',
                    'İş başvurusu e-postası',
                    'Randevu talep maili'
                ]),
                'prompts' => [
                    ['name' => 'Sosyal Medya Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Özgeçmiş Oluşturma',
                'slug' => 'resume-builder',
                'description' => 'ATS uyumlu özgeçmiş ve kapak mektubu hazırlar.',
                'emoji' => '📄',
                'icon' => 'fas fa-user-tie',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 7,
                'badge_color' => 'success',
                'input_placeholder' => 'Kariyer bilgilerinizi ve hedef pozisyonu girin...',
                'example_inputs' => [
                    ['text' => 'Yazılım geliştirici pozisyonu için CV', 'label' => 'Yazılımcı'],
                    ['text' => 'Pazarlama uzmanı kapak mektubu', 'label' => 'Pazarlama'],
                    ['text' => 'Proje yöneticisi özgeçmiş', 'label' => 'Yönetim']
                ],
                'example_prompts' => json_encode([
                    'Satış elemanı için özgeçmiş hazırla',
                    'Öğretmenlik başvurusu için CV',
                    'Staj başvurusu için özgeçmiş'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Sunum Hazırlığı',
                'slug' => 'presentation-maker',
                'description' => 'Etkileyici sunum içerikleri ve slayt metinleri oluşturur.',
                'emoji' => '📊',
                'icon' => 'fas fa-presentation',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 8,
                'badge_color' => 'success',
                'input_placeholder' => 'Sunum konusu ve hedef kitlenizi belirtin...',
                'example_inputs' => [
                    ['text' => 'Yıllık satış rakamları sunumu', 'label' => 'Satış Raporu'],
                    ['text' => 'Yeni ürün lansmanı tanıtımı', 'label' => 'Ürün Tanıtım'],
                    ['text' => 'Şirket stratejik planı', 'label' => 'Strateji']
                ],
                'example_prompts' => json_encode([
                    'Proje sunumu için slayt metinleri',
                    'Şirket tanıtım sunumu hazırla',
                    'Ürün lansmanı sunum içeriği'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Eğitim ve Akademik Kategori  
            [
                'name' => 'Ders Planı Oluşturma',
                'slug' => 'lesson-planner',
                'description' => 'Eğitim müfredatı ve ders planları hazırlar.',
                'emoji' => '📚',
                'icon' => 'fas fa-chalkboard-teacher',
                'category' => 'academic',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 28,
                'badge_color' => 'warning',
                'input_placeholder' => 'Ders konusu ve hedef yaş grubunu belirtin...',
                'example_inputs' => [
                    ['text' => 'İlkokul matematik dersi', 'label' => 'İlkokul'],
                    ['text' => 'Lise tarih müfredat planı', 'label' => 'Lise'],
                    ['text' => 'Üniversite programlama dersi', 'label' => 'Üniversite']
                ],
                'example_prompts' => json_encode([
                    'Çocuklara İngilizce öğretim planı',
                    'Yetişkinler için bilgisayar kursu',
                    'Matematik dersi etkinlik planı'
                ]),
                'prompts' => [
                    ['name' => 'Akademik Makale Yazarı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Araştırma Özeti',
                'slug' => 'research-summary',
                'description' => 'Bilimsel makale özetleri ve literatür taraması yapar.',
                'emoji' => '🔬',
                'icon' => 'fas fa-microscope',
                'category' => 'academic',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 26,
                'badge_color' => 'warning',
                'input_placeholder' => 'Araştırma konusu ve kaynaklarını girin...',
                'example_inputs' => [
                    ['text' => 'Yapay zeka etik araştırması', 'label' => 'AI Etiği'],
                    ['text' => 'İklim değişikliği literatür taraması', 'label' => 'Çevre'],
                    ['text' => 'Tıbbi ilaç geliştirme süreci', 'label' => 'Tıp']
                ],
                'example_prompts' => json_encode([
                    'Okuduğum kitapları özetler misin?',
                    'Bu konudaki araştırmaların özetini yap',
                    'Makalemi daha kısa bir şekilde yaz'
                ]),
                'prompts' => [
                    ['name' => 'Akademik Makale Yazarı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Teknik ve Yazılım Kategorisi
            [
                'name' => 'API Dökümentasyonu',
                'slug' => 'api-documentation',
                'description' => 'RESTful API dokümantasyonu ve endpoint açıklamaları oluşturur.',
                'emoji' => '🔌',
                'icon' => 'fas fa-plug',
                'category' => 'technical',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 21,
                'badge_color' => 'warning',
                'input_placeholder' => 'API endpoint\'leri ve fonksiyonlarını açıklayın...',
                'example_inputs' => [
                    ['text' => 'User management API endpoints', 'label' => 'User API'],
                    ['text' => 'E-commerce ödeme sistemi API', 'label' => 'Payment API'],
                    ['text' => 'Real-time chat API dokümantasyonu', 'label' => 'Chat API']
                ],
                'example_prompts' => json_encode([
                    'Web servisim için kullanım kılavuzu',
                    'API fonksiyonlarını açıkla',
                    'Programcılar için teknik dokümantasyon'
                ]),
                'prompts' => [
                    ['name' => 'Senior Yazılım Geliştirici', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Database Design',
                'slug' => 'database-design',
                'description' => 'Veritabanı şeması ve SQL sorgu optimizasyonu yapar.',
                'emoji' => '🗄️',
                'icon' => 'fas fa-database',
                'category' => 'technical',
                'response_length' => 'medium',
                'response_format' => 'code',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 22,
                'badge_color' => 'warning',
                'input_placeholder' => 'Veritabanı gereksinimlerini ve tablolarını açıklayın...',
                'example_inputs' => [
                    ['text' => 'E-ticaret sitesi veritabanı şeması', 'label' => 'E-ticaret'],
                    ['text' => 'Öğrenci yönetim sistemi DB', 'label' => 'Eğitim'],
                    ['text' => 'CRM sistemi tablo yapısı', 'label' => 'CRM']
                ],
                'example_prompts' => json_encode([
                    'Müşteri bilgileri için veritabanı yapısı',
                    'Stok takip sistemi veritabanı',
                    'Basit blog sitesi için tablo tasarımı'
                ]),
                'prompts' => [
                    ['name' => 'Senior Yazılım Geliştirici', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Regex Generator',
                'slug' => 'regex-generator',
                'description' => 'Regular expression pattern\'leri oluşturur ve açıklar.',
                'emoji' => '🔤',
                'icon' => 'fas fa-search',
                'category' => 'technical',
                'response_length' => 'short',
                'response_format' => 'code',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 23,
                'badge_color' => 'warning',
                'input_placeholder' => 'Aranan pattern ve format örneğini girin...',
                'example_inputs' => [
                    ['text' => 'Türkiye telefon numarası formatı', 'label' => 'Telefon'],
                    ['text' => 'Email adresi validasyonu', 'label' => 'Email'],
                    ['text' => 'TC kimlik numarası kontrolü', 'label' => 'TC No']
                ],
                'example_prompts' => json_encode([
                    'Telefon numarası doğrulama kodu',
                    'Email formatı kontrol kodu',
                    'Sadece harf ve rakam kontrolü'
                ]),
                'prompts' => [
                    ['name' => 'Senior Yazılım Geliştirici', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Pazarlama ve Reklam Kategorisi
            [
                'name' => 'Ad Copy Yazımı',
                'slug' => 'ad-copy-writing',
                'description' => 'Google Ads, Facebook Ads için etkili reklam metinleri oluşturur.',
                'emoji' => '📢',
                'icon' => 'fas fa-bullhorn',
                'category' => 'marketing',
                'response_length' => 'short',
                'response_format' => 'text',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 9,
                'badge_color' => 'success',
                'input_placeholder' => 'Ürün/hizmet ve hedef kitle bilgisini girin...',
                'example_inputs' => [
                    ['text' => 'Online yazılım kursu reklamı', 'label' => 'Eğitim'],
                    ['text' => 'Restoran yemek siparişi', 'label' => 'Yemek'],
                    ['text' => 'E-ticaret indirim kampanyası', 'label' => 'İndirim']
                ],
                'example_prompts' => json_encode([
                    'Dükkanım için çekici reklam metni',
                    'Facebook reklamı için başlık',
                    'İndirim kampanyası reklam yazısı'
                ]),
                'prompts' => [
                    ['name' => 'Sosyal Medya Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Marka Hikayesi',
                'slug' => 'brand-storytelling',
                'description' => 'Marka değerleri ve hikayesi oluşturur.',
                'emoji' => '🏢',
                'icon' => 'fas fa-building',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'markdown',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 10,
                'badge_color' => 'success',
                'input_placeholder' => 'Şirket profili ve değerlerini açıklayın...',
                'example_inputs' => [
                    ['text' => 'Teknoloji startup\'ı marka hikayesi', 'label' => 'Startup'],
                    ['text' => 'Aile işletmesi köken hikayesi', 'label' => 'Aile Şirketi'],
                    ['text' => 'Sürdürülebilir moda markası', 'label' => 'Moda']
                ],
                'example_prompts' => json_encode([
                    'Şirketimin kuruluş hikayesini yaz',
                    'Aile işletmemizin hikayesi',
                    'Markamızın ne için var olduğunu anlat'
                ]),
                'prompts' => [
                    ['name' => 'Yaratıcı Yazım Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Hashtag Stratejisi',
                'slug' => 'hashtag-strategy',
                'description' => 'Sosyal medya platformları için hashtag araştırması ve öneriler.',
                'emoji' => '#️⃣',
                'icon' => 'fas fa-hashtag',
                'category' => 'marketing',
                'response_length' => 'medium',
                'response_format' => 'list',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 11,
                'badge_color' => 'success',
                'input_placeholder' => 'İçerik konusu ve platform belirtin...',
                'example_inputs' => [
                    ['text' => 'Fitness antrenmanı Instagram', 'label' => 'Fitness'],
                    ['text' => 'Yemek tarifi TikTok', 'label' => 'Yemek'],
                    ['text' => 'Teknoloji haberları Twitter', 'label' => 'Teknoloji']
                ],
                'example_prompts' => json_encode([
                    'Instagram paylaşımım için etiketler',
                    'TikTok videom için hashtag listesi',
                    'Kedimle ilgili paylaşım etiketleri'
                ]),
                'prompts' => [
                    ['name' => 'Sosyal Medya Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Yaratıcı ve Sanat Kategorisi
            [
                'name' => 'Podcast Senaryosu',
                'slug' => 'podcast-script',
                'description' => 'Podcast bölümleri için senaryo ve konuşma metinleri oluşturur.',
                'emoji' => '🎙️',
                'icon' => 'fas fa-microphone',
                'category' => 'creative',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 16,
                'badge_color' => 'warning',
                'input_placeholder' => 'Podcast konusu ve formatını belirtin...',
                'example_inputs' => [
                    ['text' => 'Teknoloji trendleri röportajı', 'label' => 'Teknoloji'],
                    ['text' => 'Girişimcilik hikayesi söyleşi', 'label' => 'Girişimcilik'],
                    ['text' => 'Sağlıklı yaşam ipuçları', 'label' => 'Sağlık']
                ],
                'example_prompts' => json_encode([
                    'Podcast programım için giriş metni',
                    'Konuğumla yapacağım röportaj soruları',
                    'Podcast bölümü için senaryo'
                ]),
                'prompts' => [
                    ['name' => 'Yaratıcı Yazım Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Video Senaryosu',
                'slug' => 'video-script',
                'description' => 'YouTube, TikTok, Instagram için video senaryoları oluşturur.',
                'emoji' => '🎬',
                'icon' => 'fas fa-video',
                'category' => 'creative',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 17,
                'badge_color' => 'warning',
                'input_placeholder' => 'Video türü ve içerik konusunu girin...',
                'example_inputs' => [
                    ['text' => 'Ürün tanıtım videosu (60 saniye)', 'label' => 'Ürün Tanıtım'],
                    ['text' => 'Eğitici YouTube içeriği', 'label' => 'Eğitim'],
                    ['text' => 'TikTok viral trend videosu', 'label' => 'Viral Content']
                ],
                'example_prompts' => json_encode([
                    'YouTube videom için senaryo',
                    'TikTok için komik video metni',
                    'Ürün tanıtım videosu metni'
                ]),
                'prompts' => [
                    ['name' => 'Yaratıcı Yazım Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Slogan Üretici',
                'slug' => 'slogan-generator',
                'description' => 'Marka ve ürünler için akılda kalıcı slogan\'lar oluşturur.',
                'emoji' => '💡',
                'icon' => 'fas fa-lightbulb',
                'category' => 'creative',
                'response_length' => 'short',
                'response_format' => 'list',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 18,
                'badge_color' => 'warning',
                'input_placeholder' => 'Marka/ürün özellikleri ve hedef kitleyi açıklayın...',
                'example_inputs' => [
                    ['text' => 'Organik kahve markası', 'label' => 'Kahve'],
                    ['text' => 'Çevre dostu temizlik ürünü', 'label' => 'Temizlik'],
                    ['text' => 'Online eğitim platformu', 'label' => 'Eğitim']
                ],
                'example_prompts' => json_encode([
                    'Dükkanım için akılda kalıcı slogan',
                    'Restoran için güzel motto',
                    'Kuaförlük salonumu için slogan'
                ]),
                'prompts' => [
                    ['name' => 'Yaratıcı Yazım Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Analiz ve İş Dünyası
            [
                'name' => 'SWOT Analizi',
                'slug' => 'swot-analysis',
                'description' => 'İş stratejileri için SWOT analizi oluşturur.',
                'emoji' => '📈',
                'icon' => 'fas fa-chart-bar',
                'category' => 'analysis',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 30,
                'badge_color' => 'info',
                'input_placeholder' => 'Şirket/proje bilgilerini ve sektörü girin...',
                'example_inputs' => [
                    ['text' => 'Yeni mobil uygulama projesi', 'label' => 'Mobil App'],
                    ['text' => 'Restaurant zincirleme girişimi', 'label' => 'Restaurant'],
                    ['text' => 'E-ticaret pazarlama stratejisi', 'label' => 'E-ticaret']
                ],
                'example_prompts' => json_encode([
                    'İşletmemin güçlü ve zayıf yönleri',
                    'Yeni proje için fırsat ve tehdit analizi',
                    'Dükkanımın SWOT analizi yap'
                ]),
                'prompts' => [
                    ['name' => 'Pazar Araştırması Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'İş Planı Şablonu',
                'slug' => 'business-plan-template',
                'description' => 'Girişimciler için kapsamlı iş planı oluşturur.',
                'emoji' => '📋',
                'icon' => 'fas fa-clipboard-list',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 12,
                'badge_color' => 'success',
                'input_placeholder' => 'İş fikri ve hedef pazarı açıklayın...',
                'example_inputs' => [
                    ['text' => 'SaaS yazılım startup\'ı', 'label' => 'SaaS'],
                    ['text' => 'Organik gıda e-ticaret', 'label' => 'Organik Gıda'],
                    ['text' => 'Dijital ajans kurulumu', 'label' => 'Ajans']
                ],
                'example_prompts' => json_encode([
                    'Cafe açmak için iş planı',
                    'Online mağaza kurmak için plan',
                    'Danışmanlık şirketi iş planı'
                ]),
                'prompts' => [
                    ['name' => 'Pazar Araştırması Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Çeviri ve Lokalizasyon',
                'slug' => 'translation-localization',
                'description' => 'Farklı diller arası çeviri ve kültürel adaptasyon yapar.',
                'emoji' => '🌍',
                'icon' => 'fas fa-globe',
                'category' => 'communication',
                'response_length' => 'variable',
                'response_format' => 'text',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 13,
                'badge_color' => 'success',
                'input_placeholder' => 'Çevrilecek metin ve hedef dili belirtin...',
                'example_inputs' => [
                    ['text' => 'İngilizce kurumsal metin > Türkçe', 'label' => 'EN->TR'],
                    ['text' => 'Türkçe pazarlama maili > İngilizce', 'label' => 'TR->EN'],
                    ['text' => 'Ürün açıklaması çoklu dil', 'label' => 'Çoklu Dil']
                ],
                'example_prompts' => json_encode([
                    'Bu metni İngilizceye çevir',
                    'Türkçe e-postamı İngilizceye çevirmen',
                    'Web sitemi farklı dillere çevir'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Haber Yazısı',
                'slug' => 'news-writing',
                'description' => 'Gazeteci tarzında haber makaleleri oluşturur.',
                'emoji' => '📰',
                'icon' => 'fas fa-newspaper',
                'category' => 'content',
                'response_length' => 'medium',
                'response_format' => 'markdown',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 14,
                'badge_color' => 'success',
                'input_placeholder' => 'Haber konusu ve anahtar detayları girin...',
                'example_inputs' => [
                    ['text' => 'Teknoloji sektöründe yeni gelişme', 'label' => 'Teknoloji'],
                    ['text' => 'Yerel etkinlik duyurusu', 'label' => 'Etkinlik'],
                    ['text' => 'Şirket başarı hikayesi', 'label' => 'Başarı']
                ],
                'example_prompts' => json_encode([
                    'Şirketimizin başarısı hakkında haber yazısı',
                    'Etkinliğimiz için basın açıklaması',
                    'Yerel gazete için duyuru yazısı'
                ]),
                'prompts' => [
                    ['name' => 'Blog Yazısı Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Soru-Cevap Üretici',
                'slug' => 'qa-generator',
                'description' => 'FAQ sayfaları ve soru-cevap setleri oluşturur.',
                'emoji' => '❓',
                'icon' => 'fas fa-question-circle',
                'category' => 'content',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 19,
                'badge_color' => 'success',
                'input_placeholder' => 'Ürün/hizmet ve konu alanını belirtin...',
                'example_inputs' => [
                    ['text' => 'E-ticaret sitesi müşteri desteği', 'label' => 'E-ticaret'],
                    ['text' => 'Yazılım ürünü teknik destek', 'label' => 'Yazılım'],
                    ['text' => 'Eğitim kursu katılımcı soruları', 'label' => 'Eğitim']
                ],
                'example_prompts' => json_encode([
                    'El yapımı takılarım için açıklama',
                    'Ev yapımı reçel tanıtım yazısı',
                    'İkinci el araba satış ilanı'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Mülakat Hazırlığı',
                'slug' => 'interview-prep',
                'description' => 'İş mülakatları için soru-cevap hazırlığı yapar.',
                'emoji' => '🤝',
                'icon' => 'fas fa-handshake',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 24,
                'badge_color' => 'success',
                'input_placeholder' => 'Pozisyon ve sektör bilgisini girin...',
                'example_inputs' => [
                    ['text' => 'Frontend developer pozisyonu', 'label' => 'Frontend'],
                    ['text' => 'Dijital pazarlama uzmanı', 'label' => 'Pazarlama'],
                    ['text' => 'Proje yöneticisi mülakatı', 'label' => 'Yönetim']
                ],
                'example_prompts' => json_encode([
                    'İş mülakatına hazırlanmak istiyorum',
                    'Mülakat sorularına nasıl cevap vermeliyim?',
                    'Kendimi nasıl tanıtmam gerekiyor?'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ]
        ];

        foreach ($features as $featureData) {
            // Prompt bilgilerini ayır
            $prompts = $featureData['prompts'];
            unset($featureData['prompts']);

            // Feature oluştur veya güncelle
            $feature = AIFeature::updateOrCreate(
                ['slug' => $featureData['slug']],
                $featureData
            );

            // Mevcut prompt bağlantılarını temizle
            $feature->featurePrompts()->delete();

            // Prompt'ları bağla
            foreach ($prompts as $promptData) {
                $prompt = Prompt::where('name', $promptData['name'])->first();
                if ($prompt) {
                    AIFeaturePrompt::create([
                        'ai_feature_id' => $feature->id,
                        'ai_prompt_id' => $prompt->id,
                        'prompt_role' => $promptData['role'],
                        'priority' => $promptData['priority'],
                        'is_required' => true,
                        'is_active' => true
                    ]);
                }
            }
        }
    }
}