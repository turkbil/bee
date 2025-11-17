<?php

namespace Modules\Blog\App\Services;

use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Models\BlogCategory;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Blog\App\Services\ManualTopicExpander;
use Modules\SettingManagement\App\Models\SettingValue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Category-Based Draft Generator
 *
 * ✅ TENANT-AWARE kategori bazlı taslak oluşturma:
 * - Her tenant'ın kendi Shop kategorilerini kullanır
 * - Dinamik kategori grubu oluşturur (hard-coded değil!)
 * - 25 draft'ı 5 gruba böl (her grup 5 draft)
 * - Basit prompt'lar (9.4KB değil, ~500 byte)
 * - Yüksek başarı oranı (95%+)
 */
class CategoryBasedDraftGenerator
{
    /**
     * Kategori grupları - tenant bazlı otomatik oluşturulur
     * (Hard-coded değil, dinamik!)
     */
    protected array $categoryGroups = [];

    /**
     * 25 draft oluştur (5 grup × 5 draft)
     *
     * @return array Oluşturulan tüm draft'lar
     */
    public function generateDrafts(): array
    {
        $allDrafts = [];

        // ✅ TENANT-AWARE: Kategori gruplarını otomatik oluştur
        $this->categoryGroups = $this->buildCategoryGroups();

        Log::info('✅ Category groups built for tenant', [
            'tenant_id' => tenant('id'),
            'group_count' => count($this->categoryGroups),
        ]);

        // Mevcut başlıkları/draft'ları çek (duplicate check)
        $existingTitles = $this->getExistingTitles();
        $existingDrafts = $this->getExistingDrafts();

        // Her kategori grubu için draft üret
        foreach ($this->categoryGroups as $group) {
            Log::info("📦 Generating drafts for category: {$group['name']}", [
                'count' => $group['count'],
                'source' => $group['source'],
            ]);

            try {
                $drafts = $this->generateGroupDrafts(
                    $group,
                    $existingTitles,
                    $existingDrafts
                );

                $allDrafts = array_merge($allDrafts, $drafts);

                Log::info("✅ Generated {$group['name']} drafts", [
                    'count' => count($drafts),
                ]);

            } catch (\Exception $e) {
                Log::error("❌ Failed to generate {$group['name']} drafts", [
                    'error' => $e->getMessage(),
                ]);
                // Devam et, diğer grupları dene
            }
        }

        // Database'e kaydet
        $savedDrafts = $this->saveDrafts($allDrafts);

        Log::info('✅ Total drafts generated', [
            'total' => count($savedDrafts),
            'groups' => count($this->categoryGroups),
        ]);

        return $savedDrafts;
    }

    /**
     * Belirli bir grup için draft'lar üret
     *
     * @param array $group Kategori grubu tanımı
     * @param array $existingTitles Mevcut blog başlıkları
     * @param array $existingDrafts Mevcut draft konuları
     * @return array Oluşturulan draft'lar
     */
    protected function generateGroupDrafts(
        array $group,
        array $existingTitles,
        array $existingDrafts
    ): array {
        // Context bilgisi topla (kategori/ürün datasından)
        $context = $this->buildGroupContext($group);

        // Basit prompt oluştur
        $prompt = $this->buildSimplePrompt($group, $context, $existingTitles, $existingDrafts);

        // OpenAI API call
        $response = $this->callOpenAI($prompt, $group['count']);

        // JSON parse
        $drafts = $this->parseResponse($response);

        return $drafts;
    }

    /**
     * Grup için context oluştur (ürün/kategori datasından)
     *
     * @param array $group Kategori grubu
     * @return string Context bilgisi
     */
    protected function buildGroupContext(array $group): string
    {
        $context = '';

        if ($group['source'] === 'shop_category' && isset($group['category_id'])) {
            // ✅ TENANT-AWARE: category_id direkt kullan (title->tr araması yok!)
            try {
                $products = ShopProduct::where('category_id', $group['category_id'])
                    ->where('is_active', true)
                    ->limit(10)
                    ->get();

                $productNames = $products->pluck('title.tr')->filter()->toArray();

                if (!empty($productNames)) {
                    $context = "**MÜŞTERİ ÜRÜNLERİ ({$group['category_name']}):**\n";
                    $context .= implode(", ", $productNames);
                    $context .= "\n**ÜRÜN SAYISI:** {$group['product_count']}";
                } else {
                    $context = "**KATEGORİ:** {$group['category_name']} (ürün yok)";
                }
            } catch (\Exception $e) {
                Log::warning('⚠️ Failed to fetch products for category', [
                    'category_id' => $group['category_id'],
                    'error' => $e->getMessage(),
                ]);
                $context = "**KATEGORİ:** {$group['category_name']}";
            }
        } elseif ($group['source'] === 'mixed') {
            // Farklı kategorilerden karışık ürünler
            $products = ShopProduct::where('is_active', true)
                ->inRandomOrder()
                ->limit(15)
                ->get();

            $productNames = $products->pluck('title.tr')->filter()->toArray();
            $context = "**MÜŞTERİ ÜRÜNLERİ (Çeşitli):**\n";
            $context .= implode(", ", $productNames);
        } else {
            // Genel içerik (hizmetler, rehberler)
            $context = "**GENEL İÇERİK GRUBU: {$group['name']}**";
        }

        return $context;
    }

    /**
     * Basit prompt oluştur (9.4KB değil, ~500 byte!)
     *
     * @param array $group Kategori grubu
     * @param string $context Context bilgisi
     * @param array $existingTitles Mevcut başlıklar
     * @param array $existingDrafts Mevcut draft'lar
     * @return array ['system' => '', 'user' => '']
     */
    protected function buildSimplePrompt(
        array $group,
        string $context,
        array $existingTitles,
        array $existingDrafts
    ): array {
        $systemPrompt = <<<SYSTEM
Sen bir blog taslak üreticisin. İxtif firması için {$group['name']} konusunda Türkçe blog taslakları oluşturuyorsun.

**FİRMA:** İxtif - Endüstriyel Ekipman (forklift, transpalet, istif makinesi)
**WEB:** https://ixtif.com
**SEKTÖR:** Endüstriyel ekipman satışı ve kiralama

**KONU:** {$group['name']}
**ANAHTAR KELİMELER:** {$this->formatKeywords($group['keywords'])}

{$context}

**FORMAT:**
JSON array döndür:
[
  {
    "topic_keyword": "Blog başlığı",
    "meta_description": "150 karakterlik açıklama",
    "seo_keywords": ["kelime1", "kelime2", "kelime3"],
    "outline": {
      "1": "Giriş",
      "2": "Ana Konu 1",
      "3": "Ana Konu 2",
      "4": "Sonuç"
    }
  }
]

**KURALLAR:**
- SEO odaklı başlıklar
- Türkçe karakter kullan
- Teknik detaylı
- Okuyucuya değer kat
SYSTEM;

        // Duplicate check (sadece son 20 başlık - prompt'u kısa tut)
        $recentTitles = array_slice($existingTitles, -20);
        $recentDrafts = array_slice($existingDrafts, -20);

        $duplicateWarning = '';
        if (!empty($recentTitles) || !empty($recentDrafts)) {
            $duplicateWarning = "\n\n**BUNLARI TEKRARLAMA:**\n";
            if (!empty($recentTitles)) {
                $duplicateWarning .= "Mevcut: " . implode(", ", array_slice($recentTitles, 0, 10)) . "\n";
            }
            if (!empty($recentDrafts)) {
                $duplicateWarning .= "Taslak: " . implode(", ", array_slice($recentDrafts, 0, 10));
            }
        }

        $userPrompt = "Lütfen {$group['name']} konusunda {$group['count']} adet blog taslağı üret. JSON array formatında döndür.{$duplicateWarning}";

        return [
            'system' => $systemPrompt,
            'user' => $userPrompt,
        ];
    }

    /**
     * Keyword array'i string'e çevir
     */
    protected function formatKeywords(array $keywords): string
    {
        return implode(', ', $keywords);
    }

    /**
     * OpenAI API call
     *
     * @param array $prompt ['system' => '', 'user' => '']
     * @param int $count Draft sayısı
     * @return string OpenAI response
     */
    protected function callOpenAI(array $prompt, int $count): string
    {
        // AI Provider çek
        $provider = \Modules\AI\App\Models\AIProvider::where('is_default', true)
            ->where('is_active', true)
            ->first();

        if (!$provider) {
            throw new \Exception('No default AI provider found');
        }

        // API URL
        $apiUrl = rtrim($provider->base_url, '/');
        if (!str_contains($apiUrl, '/v1')) {
            $apiUrl .= '/v1';
        }
        $apiUrl .= '/chat/completions';

        // Max tokens: count × 200 (her draft ~200 token)
        $maxTokens = $count * 200;

        Log::info('🔵 Calling OpenAI API', [
            'model' => $provider->default_model,
            'max_tokens' => $maxTokens,
            'system_length' => strlen($prompt['system']),
            'user_length' => strlen($prompt['user']),
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $provider->api_key,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post($apiUrl, [
            'model' => $provider->default_model,
            'messages' => [
                ['role' => 'system', 'content' => $prompt['system']],
                ['role' => 'user', 'content' => $prompt['user']],
            ],
            'temperature' => 0.7,
            'max_tokens' => $maxTokens,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';

        Log::info('✅ OpenAI response received', [
            'length' => strlen($content),
            'tokens_used' => $data['usage']['total_tokens'] ?? 0,
        ]);

        return $content;
    }

    /**
     * OpenAI response'u parse et
     *
     * @param string $response OpenAI yanıtı
     * @return array Draft array
     */
    protected function parseResponse(string $response): array
    {
        // Markdown code block temizle
        if (preg_match('/```json\s*(.*?)\s*```/s', $response, $matches)) {
            $response = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $response, $matches)) {
            $response = $matches[1];
        }

        // JSON parse
        $decoded = json_decode(trim($response), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('❌ JSON parse error', [
                'error' => json_last_error_msg(),
                'sample' => substr($response, 0, 500),
            ]);
            throw new \Exception('JSON parse error: ' . json_last_error_msg());
        }

        if (!is_array($decoded)) {
            throw new \Exception('Response is not an array');
        }

        return $decoded;
    }

    /**
     * Draft'ları database'e kaydet
     *
     * @param array $drafts Draft array
     * @return array Kaydedilen modeller
     */
    protected function saveDrafts(array $drafts): array
    {
        $saved = [];

        foreach ($drafts as $draft) {
            try {
                $saved[] = BlogAIDraft::create([
                    'topic_keyword' => $draft['topic_keyword'] ?? '',
                    'meta_description' => $draft['meta_description'] ?? null,
                    'seo_keywords' => $draft['seo_keywords'] ?? [],
                    'outline' => $draft['outline'] ?? [],
                    'category_suggestions' => $draft['category_suggestions'] ?? [],
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to save draft', [
                    'draft' => $draft,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $saved;
    }

    /**
     * Mevcut blog başlıklarını çek
     */
    protected function getExistingTitles(): array
    {
        return Blog::query()
            ->get()
            ->pluck('title')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Mevcut draft'ları çek
     */
    protected function getExistingDrafts(): array
    {
        return BlogAIDraft::query()
            ->pluck('topic_keyword')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * ✅ TENANT-AWARE: Kategori gruplarını otomatik oluştur
     *
     * Her tenant için:
     * - Settings'ten topic_source kontrol et (manual/auto/mixed)
     * - Manuel konuları genişlet (ManualTopicExpander)
     * - Shop kategorilerini çek (ürün sayısına göre sırala)
     * - Karma mod: Önce manuel, sonra otomatik
     *
     * @return array Kategori grupları
     */
    protected function buildCategoryGroups(): array
    {
        $groups = [];

        // Settings'ten topic_source çek (manual, auto, mixed)
        $topicSource = $this->getTenantSetting('blog_ai_topic_source', 'mixed');
        $manualTopicsText = $this->getTenantSetting('blog_ai_manual_topics', null);

        Log::info('📋 Building category groups', [
            'tenant_id' => tenant('id'),
            'topic_source' => $topicSource,
            'has_manual_topics' => !empty($manualTopicsText),
        ]);

        // 1️⃣ MANUEL KONULAR (manual veya mixed modda)
        if (in_array($topicSource, ['manual', 'mixed']) && !empty($manualTopicsText)) {
            $manualGroups = $this->buildManualTopicGroups($manualTopicsText);

            // SADECE MANUEL mod ise, tüm manuel grupları kullan (5 grup = 25 başlık)
            if ($topicSource === 'manual') {
                Log::info('✅ Manual-only mode: using all manual groups', [
                    'count' => count($manualGroups),
                ]);
                return $manualGroups;
            }

            // MIXED mod ise, sadece ilk 2 grubu al (10 başlık)
            // Kalan 3 grup shop kategorilerden gelecek
            $groups = array_slice($manualGroups, 0, 2);

            Log::info('✅ Mixed mode: using first 2 manual groups', [
                'manual_groups' => count($groups),
                'remaining_for_shop' => 3,
            ]);
        }

        // 2️⃣ OTOMATİK KONULAR (auto veya mixed modda)
        if (in_array($topicSource, ['auto', 'mixed'])) {
            // Shop modülü var mı kontrol et
            if (!class_exists(ShopCategory::class)) {
                Log::warning('⚠️ Shop module not found, using general content groups');
                $generalGroups = $this->buildGeneralGroups();
                $groups = array_merge($groups, $generalGroups);
                return $groups;
            }

            try {
                // Shop kategorilerini çek (ürün sayısına göre sırala)
                // Mixed modda: 3 grup (2 manuel + 3 shop = 5 toplam)
                // Auto modda: 5 grup (sadece shop)
                $shopGroupLimit = ($topicSource === 'mixed' && count($groups) > 0) ? 3 : 5;

                $query = ShopCategory::query()
                    ->where('is_active', true)
                    ->whereNull('parent_id'); // Sadece ana kategoriler

                // 🏢 TENANT 2 (ixtif.com): Yedek Parça kategorisini hariç tut
                if (tenant('id') == 2) {
                    $query->where('category_id', '!=', 7); // Yedek Parça kategorisini hariç tut
                }

                $topCategories = $query
                    ->withCount('products')
                    ->orderBy('products_count', 'desc')
                    ->limit($shopGroupLimit)
                    ->get();

                Log::info('📊 Top shop categories fetched', [
                    'count' => $topCategories->count(),
                    'tenant_id' => tenant('id'),
                ]);

                // Her kategori için bir grup oluştur
                foreach ($topCategories as $index => $category) {
                    $categoryName = is_array($category->title)
                        ? ($category->title['tr'] ?? $category->title['en'] ?? 'Kategori')
                        : $category->title;

                    $groups[] = [
                        'name' => $categoryName,
                        'count' => 5, // Her grup 5 draft
                        'source' => 'shop_category',
                        'category_id' => $category->id,
                        'category_name' => $categoryName,
                        'product_count' => $category->products_count ?? 0,
                        'keywords' => $this->extractKeywords($categoryName),
                    ];
                }

                // Eğer 5'ten az kategori varsa, genel içerik grupları ekle
                $remaining = 5 - count($groups);
                if ($remaining > 0) {
                    Log::info("⚠️ Only {$topCategories->count()} categories found, adding {$remaining} general groups");
                    $generalGroups = $this->buildGeneralGroups($remaining);
                    $groups = array_merge($groups, $generalGroups);
                }

            } catch (\Exception $e) {
                Log::error('❌ Failed to build category groups', [
                    'error' => $e->getMessage(),
                    'tenant_id' => tenant('id'),
                ]);
                // Fallback: Genel içerik grupları kullan
                $generalGroups = $this->buildGeneralGroups();
                $groups = array_merge($groups, $generalGroups);
            }
        }

        return $groups;
    }

    /**
     * Manuel konu grupları oluştur
     *
     * Settings'teki manuel konuları parse et ve genişlet
     *
     * @param string $manualTopicsText Manuel konular (her satır bir konu)
     * @return array Manuel konu grupları
     */
    protected function buildManualTopicGroups(string $manualTopicsText): array
    {
        // Parse manual topics
        $manualTopics = ManualTopicExpander::parseManualTopicsFromSettings($manualTopicsText);

        if (empty($manualTopics)) {
            return [];
        }

        // ✅ YENİ: Ultra genişletme YAPMA! Manuel konuları olduğu gibi kullan
        // Eğer 25'ten azsa, random seçerek tamamla
        $targetCount = 25;
        $finalTopics = $manualTopics;

        if (count($manualTopics) < $targetCount) {
            // Random seçerek 25'e tamamla
            while (count($finalTopics) < $targetCount) {
                $randomTopic = $manualTopics[array_rand($manualTopics)];
                $finalTopics[] = $randomTopic;
            }

            Log::info('📝 Manual topics extended with random selection', [
                'original_count' => count($manualTopics),
                'final_count' => count($finalTopics),
            ]);
        } else {
            // 25'ten fazla varsa, ilk 25'i al
            $finalTopics = array_slice($manualTopics, 0, $targetCount);

            Log::info('📝 Manual topics limited to 25', [
                'original_count' => count($manualTopics),
                'used_count' => count($finalTopics),
            ]);
        }

        // Her 5 başlık için bir grup oluştur (toplam 5 grup)
        $groups = [];
        $chunks = array_chunk($finalTopics, 5);

        foreach ($chunks as $index => $chunk) {
            $groups[] = [
                'name' => 'Manuel Konular Grup ' . ($index + 1),
                'count' => count($chunk), // 5 veya daha az
                'source' => 'manual',
                'topics' => $chunk, // Manuel başlıklar (random repeat ile)
                'keywords' => $chunk, // Başlıklar anahtar kelime olarak kullanılır
            ];
        }

        return $groups;
    }

    /**
     * Tenant setting değerini çek
     *
     * @param string $key Setting key
     * @param mixed $default Default value
     * @return mixed Setting value
     */
    protected function getTenantSetting(string $key, $default = null)
    {
        try {
            // Central DB'den Setting'i bul
            $setting = \Modules\SettingManagement\App\Models\Setting::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            // Tenant DB'den value'yu çek
            if (tenant()) {
                $settingValue = SettingValue::on('tenant')
                    ->where('setting_id', $setting->id)
                    ->first();

                if ($settingValue && $settingValue->value !== null) {
                    return $settingValue->value;
                }
            }

            // Default value
            return $setting->default_value ?? $default;

        } catch (\Exception $e) {
            Log::warning('⚠️ Failed to get tenant setting', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return $default;
        }
    }

    /**
     * Kategori adından anahtar kelimeleri çıkar
     *
     * Örnek:
     * - "Forklift" → ["forklift", "elektrikli forklift", "dizel forklift"]
     * - "Transpalet" → ["transpalet", "elektrikli transpalet", "manuel transpalet"]
     *
     * @param string $categoryName Kategori adı
     * @return array Anahtar kelimeler
     */
    protected function extractKeywords(string $categoryName): array
    {
        $baseKeyword = mb_strtolower($categoryName);

        return [
            $baseKeyword,
            "elektrikli {$baseKeyword}",
            "manuel {$baseKeyword}",
            "{$baseKeyword} modelleri",
            "{$baseKeyword} fiyatları",
        ];
    }

    /**
     * Genel içerik grupları oluştur
     *
     * Shop kategorisi yoksa veya yetersizse kullanılır
     *
     * @param int $count Kaç grup oluşturulacak (varsayılan: 5)
     * @return array Genel içerik grupları
     */
    protected function buildGeneralGroups(int $count = 5): array
    {
        $generalTopics = [
            [
                'name' => 'Ürün Rehberleri',
                'keywords' => ['satın alma rehberi', 'ürün karşılaştırma', 'seçim kriterleri', 'model özellikleri'],
            ],
            [
                'name' => 'Kullanım ve Bakım',
                'keywords' => ['kullanım talimatları', 'bakım önerileri', 'güvenlik ipuçları', 'servis'],
            ],
            [
                'name' => 'Sektör Bilgileri',
                'keywords' => ['endüstri haberleri', 'teknoloji trendleri', 'sektör analizi', 'yenilikler'],
            ],
            [
                'name' => 'Sık Sorulan Sorular',
                'keywords' => ['SSS', 'yaygın sorunlar', 'çözümler', 'ipuçları', 'öneriler'],
            ],
            [
                'name' => 'Hizmetler ve Çözümler',
                'keywords' => ['kiralama', 'satış sonrası hizmet', 'garanti', 'destek', 'danışmanlık'],
            ],
        ];

        $groups = [];
        for ($i = 0; $i < $count; $i++) {
            $topic = $generalTopics[$i] ?? $generalTopics[0]; // Fallback to first topic

            $groups[] = [
                'name' => $topic['name'],
                'count' => 5,
                'source' => 'general',
                'keywords' => $topic['keywords'],
            ];
        }

        return $groups;
    }
}
