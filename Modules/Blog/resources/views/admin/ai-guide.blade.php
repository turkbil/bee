@php
    View::share('pretitle', 'Blog AI Sistemi');
@endphp
@include('blog::admin.helper')
<div>
    {{-- CLAUDE AI İÇİN GERÇEK SİSTEM REHBERİ --}}

    <div class="alert alert-danger mb-4">
        <div class="d-flex">
            <div>
                <i class="fas fa-robot fa-2x me-3"></i>
            </div>
            <div>
                <h4 class="alert-title">Claude AI - Blog Yazım Sistemi (Gerçek Sistem)</h4>
                <p class="mb-0">Bu rehber, <code>BlogAIContentWriter</code> + <code>Tenant2Prompts</code> sistemini kullanarak blog yazmayı anlatır.</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">

            {{-- 1. SİSTEM MİMARİSİ --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h3 class="card-title text-white"><i class="fas fa-sitemap me-2"></i>1. Sistem Mimarisi</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Gerçek Sistem Bileşenleri:</strong>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Dosya</th>
                                <th>Görev</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>BlogAIContentWriter.php</code></td>
                                <td>Ana blog oluşturma servisi - Draft'ı blog'a çevirir</td>
                            </tr>
                            <tr>
                                <td><code>TenantPromptLoader.php</code></td>
                                <td>Tenant'a göre doğru prompt sınıfını yükler</td>
                            </tr>
                            <tr>
                                <td><code>Tenant2Prompts.php</code></td>
                                <td>İxtif'e özel prompt'lar, kurallar, context</td>
                            </tr>
                            <tr>
                                <td><code>Tenant2BlogProductInjector.php</code></td>
                                <td>Blog içeriğine ürün kartları ve CTA ekler</td>
                            </tr>
                            <tr>
                                <td><code>LeonardoAIService.php</code></td>
                                <td>AI görsel üretimi (hero image)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 class="mt-4">Prompt Dosyaları:</h5>
                    <ul>
                        <li><code>readme/blog-prompt/2-blog-yazdirma-SHORT.md</code> - Ana içerik prompt'u</li>
                        <li><code>Tenant2Prompts::getTimeContext()</code> - Yıl/fiyat yasakları</li>
                        <li><code>Tenant2Prompts::getCompanyUsageRules()</code> - Firma adı kuralları</li>
                        <li><code>Tenant2Prompts::getProductMentionRules()</code> - Ürün bahsetme zorunluluğu</li>
                    </ul>
                </div>
            </div>

            {{-- 2. YENİ BLOG YAZMA (GERÇEK SİSTEM) --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title text-white"><i class="fas fa-plus-circle me-2"></i>2. Yeni Blog Yazma</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Kullanıcı:</strong> "istanbul forklift çatal kılıfı hakkında blog yaz"
                    </div>

                    <h5>Claude'un Yapacakları:</h5>

                    <h6 class="mt-3">A) Tenant Başlat + Servisleri Yükle</h6>
                    <pre class="bg-dark text-light p-3 rounded small">// Tenant 2 (ixtif.com) başlat
$tenant = App\Models\Tenant::find(2);
tenancy()->initialize($tenant);

// Servisleri yükle
$promptLoader = new \Modules\Blog\App\Services\TenantPrompts\TenantPromptLoader();
$openai = new \Modules\AI\App\Services\OpenAIService();

// Tenant context al (firma bilgileri, ürünler)
$context = $promptLoader->getTenantContext();
$companyName = $context['company_info']['name']; // "iXtif"</pre>

                    <h6 class="mt-3">B) Outline Oluştur (4-5 H2 Başlık)</h6>
                    <pre class="bg-dark text-light p-3 rounded small">$topic = "istanbul forklift çatal kılıfı";

$outlinePrompt = "'{$topic}' konusu için blog outline'ı oluştur.
4-5 H2 başlık belirle. JSON array döndür: [\"Başlık 1\", \"Başlık 2\", ...]

⚠️ YASAKLI BAŞLIKLAR:
- ❌ 'Giriş', 'Sonuç', 'Özet', 'Hakkında', 'İletişim'
- ❌ 'Sık Sorulan Sorular' (FAQ ayrı section)

✅ DOĞRU ÖRNEK:
- 'Forklift Çatal Kılıfı Nedir?'
- 'Çatal Kılıfı Çeşitleri ve Özellikleri'
- 'Doğru Çatal Kılıfı Nasıl Seçilir?'";

$outline = json_decode($openai->ask($outlinePrompt, false, [
    'model' => 'gpt-4o-mini',
    'max_tokens' => 1000,
    'temperature' => 0.7,
]), true);</pre>

                    <h6 class="mt-3">C) Her H2 Bölümü İçin İçerik Üret (Iterative)</h6>
                    <pre class="bg-dark text-light p-3 rounded small">$fullContent = '';

foreach ($outline as $h2Title) {
    $sectionPrompt = "'{$h2Title}' konusunda UZUN ve DETAYLI bölüm yaz.

📏 UZUNLUK: Minimum 500-600 kelime per section
📝 YAPI: 4-6 paragraf + 3-4 H3 alt başlık
🏢 FİRMA: '{$companyName}' adını kullan (ilk/son bölümde)

⚠️ YASAKLAR:
- ❌ Spesifik fiyat rakamları (TL, USD, EUR)
- ❌ Yıl bahsetme (2023, 2024, 2025)
- ❌ 'Giriş', 'Sonuç' başlıkları

HTML çıktı döndür (<h2>, <h3>, <p>, <ul>)";

    $sectionContent = $openai->ask($sectionPrompt, false, [
        'model' => 'gpt-4o-mini',
        'max_tokens' => 3500,
        'temperature' => 0.8,
    ]);

    $fullContent .= "\n\n" . $sectionContent;
    sleep(1); // Rate limit
}</pre>

                    <h6 class="mt-3">D) FAQ Üret (10 Soru)</h6>
                    <pre class="bg-dark text-light p-3 rounded small">$faqPrompt = "'{$topic}' konusunda 10 sık sorulan soru ve cevap oluştur.
Her cevap 50-80 kelime olsun.

JSON formatı:
[{\"question\": {\"tr\": \"Soru?\"}, \"answer\": {\"tr\": \"Cevap...\"}, \"icon\": \"fas fa-question-circle\"}]

⚠️ Her soruya FARKLI ikon seç:
fas fa-question-circle, fas fa-info-circle, fas fa-lightbulb, fas fa-wrench,
fas fa-shield-alt, fas fa-chart-bar, fas fa-cog, fas fa-tools, fas fa-check-circle";

$faqData = json_decode($openai->ask($faqPrompt, false, [
    'model' => 'gpt-4o-mini',
    'max_tokens' => 3000,
]), true);</pre>

                    <h6 class="mt-3">E) HowTo Üret (7 Adım)</h6>
                    <pre class="bg-dark text-light p-3 rounded small">$howtoPrompt = "'{$topic}' için 7 adımlık 'Nasıl Yapılır' rehberi oluştur.
Her adım 80-100 kelime olsun.

JSON formatı:
{
    \"name\": {\"tr\": \"Rehber Başlığı\"},
    \"description\": {\"tr\": \"Kısa açıklama...\"},
    \"steps\": [
        {\"name\": {\"tr\": \"Adım 1: X\"}, \"text\": {\"tr\": \"Detay...\"}, \"icon\": \"fas fa-search\"}
    ]
}

İkonlar: fas fa-search, fas fa-ruler, fas fa-clipboard-check, fas fa-tools,
fas fa-cog, fas fa-shield-alt, fas fa-check-circle";

$howtoData = json_decode($openai->ask($howtoPrompt, false, [
    'model' => 'gpt-4o-mini',
    'max_tokens' => 3000,
]), true);</pre>

                    <h6 class="mt-3">F) Blog Kaydet + SEO + Görsel</h6>
                    <pre class="bg-dark text-light p-3 rounded small">use Modules\Blog\App\Models\Blog;
use Illuminate\Support\Str;

DB::beginTransaction();
try {
    $title = "İstanbul Forklift Çatal Kılıfı: Kapsamlı Rehber";
    $slug = Str::slug($title);

    // Blog oluştur
    $blog = Blog::create([
        'title' => ['tr' => $title],
        'slug' => ['tr' => $slug],
        'body' => ['tr' => $fullContent],
        'excerpt' => ['tr' => mb_substr(strip_tags($fullContent), 0, 80)],
        'faq_data' => $faqData,
        'howto_data' => $howtoData,
        'blog_category_id' => 13, // Yedek Parça Rehberi
        'is_active' => true,
        'published_at' => now(),
    ]);

    // SEO ayarları
    $blog->seoSetting()->create([
        'titles' => ['tr' => $title],
        'descriptions' => ['tr' => mb_substr(strip_tags($fullContent), 0, 155)],
        'status' => 'active',
    ]);

    // Leonardo AI görsel
    $leonardo = app(\App\Services\Media\LeonardoAIService::class);
    $imageResult = $leonardo->generateForBlog($title, 'blog');

    if ($imageResult) {
        $tempPath = sys_get_temp_dir() . '/' . uniqid('leonardo_') . '.jpg';
        file_put_contents($tempPath, $imageResult['content']);

        $blog->addMedia($tempPath)
            ->usingFileName(uniqid('leonardo_') . '.jpg')
            ->toMediaCollection('hero', 'tenant');

        @unlink($tempPath);
    }

    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}</pre>

                    <div class="alert alert-success mt-3">
                        <strong>Sonuç:</strong> Blog oluşturuldu!<br>
                        <code>https://ixtif.com/blog/{slug}</code>
                    </div>
                </div>
            </div>

            {{-- 3. İÇERİK KURALLARI (TENANT 2 ÖZEL) --}}
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title text-white"><i class="fas fa-exclamation-triangle me-2"></i>3. İçerik Kuralları (Tenant 2 / İxtif)</h3>
                </div>
                <div class="card-body">

                    {{-- Firma Adı Kuralları --}}
                    <h5 class="text-primary"><i class="fas fa-building me-2"></i>Firma Adı Kullanımı (ZORUNLU)</h5>
                    <div class="alert alert-warning">
                        <strong>Firma adı "iXtif" EN AZ 3 KEZ kullanılmalı:</strong>
                        <ol class="mb-0 mt-2">
                            <li>İlk 200 kelime içinde (giriş paragrafı)</li>
                            <li>Orta bölümde (teknik detay kısmı)</li>
                            <li>Sonuç/CTA bölümünde</li>
                        </ol>
                    </div>
                    <pre class="bg-light p-2 rounded small">✅ DOĞRU: "iXtif olarak, forklift çatal kılıfı seçiminde..."
✅ DOĞRU: "iXtif uzman ekibi size yardımcı olacaktır."
❌ YANLIŞ: "Firmamız olarak..." (firma adı yok!)
❌ YANLIŞ: "Profesyonel destek için bize ulaşın" (firma adı yok!)</pre>

                    <hr>

                    {{-- Yıl Yasağı --}}
                    <h5 class="text-danger"><i class="fas fa-calendar-times me-2"></i>Yıl Bahsetme YASAK</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger">❌ YASAK</h6>
                            <ul class="small">
                                <li>"2023 yılında..."</li>
                                <li>"2024 için en iyi..."</li>
                                <li>"2025 modelleri..."</li>
                                <li>"Geçen yıl..."</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">✅ DOĞRU</h6>
                            <ul class="small">
                                <li>"Güncel forklift modelleri..."</li>
                                <li>"Modern teknolojiler..."</li>
                                <li>"Son dönemde popüler olan..."</li>
                                <li>"Yeni nesil sistemler..."</li>
                            </ul>
                        </div>
                    </div>

                    <hr>

                    {{-- Fiyat Yasağı --}}
                    <h5 class="text-danger"><i class="fas fa-money-bill-wave me-2"></i>Fiyat Rakamı YASAK</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger">❌ YASAK</h6>
                            <ul class="small">
                                <li>"25.000 TL"</li>
                                <li>"$15,000 USD"</li>
                                <li>"Yaklaşık 50 bin lira"</li>
                                <li>"Fiyatı: 35.000₺"</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">✅ DOĞRU</h6>
                            <ul class="small">
                                <li>"Fiyatları etkileyen faktörler..."</li>
                                <li>"Detaylı fiyat için iletişime geçin"</li>
                                <li>"Bütçenize uygun modeller..."</li>
                                <li>"Ekonomik ve premium seçenekler..."</li>
                            </ul>
                        </div>
                    </div>

                    <hr>

                    {{-- Başlık Yasakları --}}
                    <h5 class="text-danger"><i class="fas fa-heading me-2"></i>Yasaklı Başlıklar</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-danger">❌ Giriş</span>
                        <span class="badge bg-danger">❌ Sonuç</span>
                        <span class="badge bg-danger">❌ Özet</span>
                        <span class="badge bg-danger">❌ Hakkında</span>
                        <span class="badge bg-danger">❌ Hakkımızda</span>
                        <span class="badge bg-danger">❌ İletişim</span>
                        <span class="badge bg-danger">❌ Sık Sorulan Sorular</span>
                    </div>

                    <hr>

                    {{-- Ürün Bahsetme --}}
                    <h5 class="text-primary"><i class="fas fa-shopping-cart me-2"></i>Ürün Bahsetme (ZORUNLU)</h5>
                    <div class="alert alert-info">
                        <strong>iXtif hem ÜRÜN SAHİBİ hem PAZAR YERİ:</strong>
                        <ol class="mb-0 mt-2">
                            <li><strong>İLK:</strong> iXtif marka ürünleri öv</li>
                            <li><strong>İKİNCİ:</strong> Diğer markalar (Toyota, Linde, Heli...)</li>
                            <li><strong>KAPANIŞ:</strong> Danışmanlık + İletişim</li>
                        </ol>
                    </div>
                    <pre class="bg-light p-2 rounded small">✅ "iXtif marka elektrikli transpalet, kalite ve uygun fiyatı bir arada sunar."
✅ "Diğer markaları tercih ediyorsanız, iXtif'te Toyota, Linde gibi global markaları bulabilirsiniz."
✅ "Detaylı bilgi için iXtif uzman danışmanları ile iletişime geçin."</pre>

                </div>
            </div>

            {{-- 4. MEVCUT BLOGU GÜNCELLEME --}}
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h3 class="card-title"><i class="fas fa-edit me-2"></i>4. Mevcut Blogu Güncelleme</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Kullanıcı:</strong> "438 nolu blogu gerçek sisteme göre yeniden yaz"
                    </div>

                    <h6 class="mt-3">A) Mevcut Blogu Al</h6>
                    <pre class="bg-dark text-light p-3 rounded small">$blog = Blog::find(438);
$topic = $blog->title['tr']; // Mevcut başlık</pre>

                    <h6 class="mt-3">B) Yukarıdaki Adımları Uygula</h6>
                    <pre class="bg-dark text-light p-3 rounded small">// 1. Outline oluştur (mevcut başlığa göre)
// 2. Her H2 için içerik üret (iterative)
// 3. FAQ üret (10 soru)
// 4. HowTo üret (7 adım)
// 5. Güncelle:

$blog->update([
    'body' => ['tr' => $fullContent],
    'faq_data' => $faqData,
    'howto_data' => $howtoData,
]);

// Eski görseli koru veya yenisini ekle</pre>

                    <div class="alert alert-warning mt-3">
                        <strong>Dikkat:</strong> Güncelleme yaparken mevcut görseli silme! Yeni görsel gerekirse <code>clearMediaCollection('hero')</code> sonra ekle.
                    </div>
                </div>
            </div>

            {{-- 5. VERİ YAPILARI --}}
            <div class="card mb-4">
                <div class="card-header bg-purple text-white">
                    <h3 class="card-title text-white"><i class="fas fa-database me-2"></i>5. Veri Yapıları</h3>
                </div>
                <div class="card-body">

                    <h5>Blog Tablosu</h5>
                    <table class="table table-sm table-bordered">
                        <tr><th>Alan</th><th>Tip</th><th>Format</th></tr>
                        <tr><td>title</td><td>JSON</td><td><code>{"tr": "Başlık"}</code></td></tr>
                        <tr><td>slug</td><td>JSON</td><td><code>{"tr": "baslik-slug"}</code></td></tr>
                        <tr><td>body</td><td>JSON</td><td><code>{"tr": "&lt;h2&gt;...&lt;/h2&gt;"}</code></td></tr>
                        <tr><td>excerpt</td><td>JSON</td><td><code>{"tr": "80 karakter..."}</code></td></tr>
                        <tr><td>faq_data</td><td>JSON</td><td>Array of objects (10 adet)</td></tr>
                        <tr><td>howto_data</td><td>JSON</td><td>Object with steps array (7 adım)</td></tr>
                        <tr><td>blog_category_id</td><td>INT</td><td>Kategori ID</td></tr>
                    </table>

                    <h5 class="mt-4">FAQ Formatı</h5>
                    <pre class="bg-light p-3 rounded small">[
    {
        "question": {"tr": "Forklift çatal kılıfı nedir?"},
        "answer": {"tr": "Forklift çatal kılıfı, forkliftin çatal kısmını koruyan... (50-80 kelime)"},
        "icon": "fas fa-question-circle"
    },
    // ... 10 adet
]</pre>

                    <h5 class="mt-4">HowTo Formatı</h5>
                    <pre class="bg-light p-3 rounded small">{
    "name": {"tr": "Forklift Çatal Kılıfı Seçimi Rehberi"},
    "description": {"tr": "Bu rehber, doğru çatal kılıfı seçimi adımlarını açıklar."},
    "steps": [
        {
            "name": {"tr": "Adım 1: İhtiyaç Analizi"},
            "text": {"tr": "Forklift modelinizi ve kullanım amacını belirleyin... (80-100 kelime)"},
            "icon": "fas fa-search"
        },
        // ... 7 adet
    ]
}</pre>

                </div>
            </div>

        </div>

        {{-- Sağ Kolon --}}
        <div class="col-lg-4">

            {{-- HIZLI CHECKLIST --}}
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title text-white"><i class="fas fa-check-double me-2"></i>Blog Yazım Checklist</h3>
                </div>
                <div class="card-body">
                    <h6>İçerik Kontrol:</h6>
                    <ul class="small mb-3">
                        <li>☐ 1500+ kelime</li>
                        <li>☐ 4-5 H2 başlık</li>
                        <li>☐ Her H2'de 3-4 H3</li>
                        <li>☐ "iXtif" min 3 kez</li>
                        <li>☐ 10 FAQ soru-cevap</li>
                        <li>☐ 7 adım HowTo</li>
                    </ul>

                    <h6>Yasak Kontrol:</h6>
                    <ul class="small mb-3">
                        <li>☐ Yıl yok (2023, 2024, 2025)</li>
                        <li>☐ Fiyat rakamı yok</li>
                        <li>☐ "Giriş/Sonuç" başlık yok</li>
                    </ul>

                    <h6>Teknik Kontrol:</h6>
                    <ul class="small mb-0">
                        <li>☐ SEO ayarları eklendi</li>
                        <li>☐ Leonardo görsel eklendi</li>
                        <li>☐ Kategori atandı</li>
                    </ul>
                </div>
            </div>

            {{-- KATEGORİLER --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-folder me-2"></i>Blog Kategorileri</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        @php
                            $categories = \Modules\Blog\App\Models\BlogCategory::all();
                        @endphp
                        @foreach($categories as $cat)
                        <tr>
                            <td><strong>{{ $cat->getKey() }}</strong></td>
                            <td>{{ $cat->getTranslated('title', 'tr') }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            {{-- FONT AWESOME İKONLAR --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-icons me-2"></i>FAQ/HowTo İkonları</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-1">
                        <code class="small">fa-question-circle</code>
                        <code class="small">fa-info-circle</code>
                        <code class="small">fa-lightbulb</code>
                        <code class="small">fa-wrench</code>
                        <code class="small">fa-shield-alt</code>
                        <code class="small">fa-chart-bar</code>
                        <code class="small">fa-cog</code>
                        <code class="small">fa-tools</code>
                        <code class="small">fa-check-circle</code>
                        <code class="small">fa-search</code>
                        <code class="small">fa-ruler</code>
                        <code class="small">fa-clipboard-check</code>
                    </div>
                </div>
            </div>

            {{-- FİRMA BİLGİLERİ --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building me-2"></i>Firma Bilgileri</h3>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li><strong>Marka:</strong> iXtif</li>
                        <li><strong>Sektör:</strong> Endüstriyel Ekipman</li>
                        <li><strong>Ürünler:</strong> Forklift, Transpalet, İstif, Reach Truck</li>
                        <li><strong>Rol:</strong> Hem üretici hem pazar yeri</li>
                        <li><strong>Markalar:</strong> iXtif + Toyota, Linde, Heli, EP</li>
                    </ul>
                </div>
            </div>

            {{-- ÖNEMLİ NOTLAR --}}
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i>Önemli Notlar</h3>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li>Blog primary key: <code>blog_id</code></li>
                        <li>Tüm text alanları JSON: <code>['tr' => '...']</code></li>
                        <li>Tenant başlat: <code>tenancy()->initialize(2)</code></li>
                        <li>Görsel collection: <code>hero</code></li>
                        <li>Görsel disk: <code>tenant</code></li>
                        <li>Model: <code>gpt-4o-mini</code> (ucuz)</li>
                    </ul>
                </div>
            </div>

            {{-- ÖRNEK PROMPT --}}
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title text-white"><i class="fas fa-comment me-2"></i>Kullanıcı Prompt Örnekleri</h3>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li>"istanbul forklift çatal kılıfı hakkında blog yaz"</li>
                        <li>"elektrikli transpalet avantajları blog yaz"</li>
                        <li>"438 nolu blogu güncelle"</li>
                        <li>"78 nolu blogun FAQ'larını yenile"</li>
                        <li>"reach truck seçim rehberi yaz"</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
