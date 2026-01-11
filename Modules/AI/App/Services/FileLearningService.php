<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * File-Based AI Learning Service
 *
 * Konuşmalardan öğrenilen bilgileri dosya sisteminde saklar.
 * Tenant-aware: Her tenant kendi öğrenme dosyasına sahip.
 *
 * GÜVENLİ ÖĞRENME:
 * ✅ Öğrenebilir: Eş anlamlılar, popüler ürünler, sık sorular, öncelikli ürünler
 * ❌ Öğrenemez: Fiyat, stok, iletişim bilgileri, politikalar
 *
 * @package Modules\AI\App\Services
 */
class FileLearningService
{
    /**
     * Öğrenme dosyası adı
     */
    private const LEARNING_FILE = 'ai-learning.json';

    /**
     * Öğrenme verisi yapısı
     */
    private array $defaultStructure = [
        'version' => '1.0.0',
        'tenant_id' => null,
        'created_at' => null,
        'updated_at' => null,

        // Eş anlamlı kelimeler (güvenli öğrenme)
        'synonyms' => [
            // 'trans palet' => 'transpalet',
            // 'fork lift' => 'forklift',
        ],

        // Öncelikli/Promosyonlu ürünler (manuel ekleme)
        'promoted_products' => [
            // [
            //     'keyword' => 'transpalet',
            //     'product_pattern' => 'F4 1.5 ton',
            //     'priority' => 1,
            //     'reason' => 'En önemli ürün',
            //     'added_at' => '2024-01-01',
            // ]
        ],

        // Popüler aramalar (otomatik öğrenme)
        'popular_searches' => [
            // 'transpalet' => ['count' => 150, 'last_searched' => '2024-01-01'],
        ],

        // Sık sorulan sorular (otomatik öğrenme)
        'common_questions' => [
            // 'Fiyat nasıl?' => ['count' => 50, 'category' => 'pricing'],
        ],

        // Kategori tercihleri (otomatik öğrenme)
        'category_preferences' => [
            // 'transpalet' => ['count' => 100, 'related_terms' => ['trans palet', 'palet']],
        ],

        // Kara liste (yanlış öğrenmeler)
        'blacklist' => [
            // 'yanlış_kelime' => ['reason' => 'Hatalı eşleşme', 'added_at' => '2024-01-01'],
        ],

        // Öğrenme geçmişi
        'learning_history' => [
            // ['type' => 'synonym', 'data' => [...], 'learned_at' => '2024-01-01'],
        ],
    ];

    /**
     * Mevcut tenant ID'sini al
     */
    private function getTenantId(): ?string
    {
        if (function_exists('tenancy') && tenancy()->initialized) {
            return (string) tenant('id');
        }
        return null;
    }

    /**
     * Öğrenme dosyası path'ini al
     */
    private function getLearningFilePath(): string
    {
        $tenantId = $this->getTenantId();

        if ($tenantId) {
            return storage_path("tenant{$tenantId}/" . self::LEARNING_FILE);
        }

        // Central tenant için
        return storage_path('app/' . self::LEARNING_FILE);
    }

    /**
     * Öğrenme verisini oku
     */
    public function getLearningData(): array
    {
        $filePath = $this->getLearningFilePath();

        if (!File::exists($filePath)) {
            return $this->initializeLearningFile();
        }

        try {
            $content = File::get($filePath);
            $data = json_decode($content, true);

            if (!$data) {
                Log::warning('[FileLearning] Invalid JSON in learning file, reinitializing', [
                    'path' => $filePath
                ]);
                return $this->initializeLearningFile();
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('[FileLearning] Error reading learning file', [
                'path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return $this->initializeLearningFile();
        }
    }

    /**
     * Öğrenme dosyasını başlat
     */
    public function initializeLearningFile(): array
    {
        $filePath = $this->getLearningFilePath();
        $directory = dirname($filePath);

        // Dizin yoksa oluştur
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $data = $this->defaultStructure;
        $data['tenant_id'] = $this->getTenantId();
        $data['created_at'] = now()->toISOString();
        $data['updated_at'] = now()->toISOString();

        // Tenant 2 için varsayılan promoted products
        if ($this->getTenantId() === '2') {
            $data['promoted_products'] = [
                [
                    'keyword' => 'transpalet',
                    'product_pattern' => 'F4',
                    'tonnage' => '1.5',
                    'priority' => 1,
                    'reason' => 'En önemli ürün - kullanıcı talebi',
                    'added_at' => now()->toISOString(),
                ],
            ];

            // Varsayılan eş anlamlılar
            $data['synonyms'] = [
                'trans palet' => 'transpalet',
                'palet jack' => 'transpalet',
                'fork lift' => 'forklift',
                'portif' => 'forklift',
                'istif makinası' => 'istif makinesi',
                'reach truck' => 'istif makinesi',
            ];
        }

        $this->saveLearningData($data);

        Log::info('[FileLearning] Learning file initialized', [
            'tenant_id' => $this->getTenantId(),
            'path' => $filePath
        ]);

        return $data;
    }

    /**
     * Öğrenme verisini kaydet
     */
    private function saveLearningData(array $data): bool
    {
        $filePath = $this->getLearningFilePath();

        try {
            $data['updated_at'] = now()->toISOString();

            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            File::put($filePath, $json);

            // İzinleri düzelt (production için)
            if (function_exists('exec')) {
                @exec("chown tuufi.com_:psaserv " . escapeshellarg($filePath));
                @exec("chmod 644 " . escapeshellarg($filePath));
            }

            return true;
        } catch (\Exception $e) {
            Log::error('[FileLearning] Error saving learning file', [
                'path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // ========================================
    // ÖNCELIKLI ÜRÜN YÖNETİMİ (Manuel)
    // ========================================

    /**
     * Öncelikli ürün ekle
     */
    public function addPromotedProduct(string $keyword, string $productPattern, int $priority = 1, string $reason = '', ?string $tonnage = null): bool
    {
        $data = $this->getLearningData();

        // Aynı keyword+pattern varsa güncelle
        $found = false;
        foreach ($data['promoted_products'] as $key => $product) {
            if ($product['keyword'] === $keyword && $product['product_pattern'] === $productPattern) {
                $data['promoted_products'][$key]['priority'] = $priority;
                $data['promoted_products'][$key]['reason'] = $reason;
                $data['promoted_products'][$key]['tonnage'] = $tonnage;
                $data['promoted_products'][$key]['updated_at'] = now()->toISOString();
                $found = true;
                break;
            }
        }

        if (!$found) {
            $data['promoted_products'][] = [
                'keyword' => $keyword,
                'product_pattern' => $productPattern,
                'tonnage' => $tonnage,
                'priority' => $priority,
                'reason' => $reason,
                'added_at' => now()->toISOString(),
            ];
        }

        // Geçmişe ekle
        $data['learning_history'][] = [
            'type' => 'promoted_product',
            'action' => $found ? 'update' : 'add',
            'data' => [
                'keyword' => $keyword,
                'product_pattern' => $productPattern,
                'tonnage' => $tonnage,
            ],
            'learned_at' => now()->toISOString(),
        ];

        Log::info('[FileLearning] Promoted product added/updated', [
            'keyword' => $keyword,
            'pattern' => $productPattern,
            'tonnage' => $tonnage,
            'priority' => $priority
        ]);

        return $this->saveLearningData($data);
    }

    /**
     * Öncelikli ürünleri al
     */
    public function getPromotedProducts(?string $keyword = null): array
    {
        $data = $this->getLearningData();
        $products = $data['promoted_products'] ?? [];

        if ($keyword) {
            $products = array_filter($products, function ($product) use ($keyword) {
                return stripos($product['keyword'], $keyword) !== false;
            });
        }

        // Priority'ye göre sırala
        usort($products, fn($a, $b) => ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999));

        return $products;
    }

    /**
     * Öncelikli ürün sil
     */
    public function removePromotedProduct(string $keyword, string $productPattern): bool
    {
        $data = $this->getLearningData();

        $data['promoted_products'] = array_filter($data['promoted_products'], function ($product) use ($keyword, $productPattern) {
            return !($product['keyword'] === $keyword && $product['product_pattern'] === $productPattern);
        });

        $data['promoted_products'] = array_values($data['promoted_products']);

        return $this->saveLearningData($data);
    }

    // ========================================
    // EŞ ANLAMLI YÖNETİMİ (Otomatik + Manuel)
    // ========================================

    /**
     * Eş anlamlı ekle
     */
    public function addSynonym(string $term, string $canonical): bool
    {
        $data = $this->getLearningData();

        // Kara listede mi kontrol et
        if (isset($data['blacklist'][$term])) {
            Log::warning('[FileLearning] Synonym in blacklist, not adding', [
                'term' => $term,
                'canonical' => $canonical
            ]);
            return false;
        }

        $data['synonyms'][$term] = $canonical;

        // Geçmişe ekle
        $data['learning_history'][] = [
            'type' => 'synonym',
            'action' => 'add',
            'data' => [
                'term' => $term,
                'canonical' => $canonical,
            ],
            'learned_at' => now()->toISOString(),
        ];

        Log::info('[FileLearning] Synonym added', [
            'term' => $term,
            'canonical' => $canonical
        ]);

        return $this->saveLearningData($data);
    }

    /**
     * Eş anlamlıları al
     */
    public function getSynonyms(): array
    {
        $data = $this->getLearningData();
        return $data['synonyms'] ?? [];
    }

    /**
     * Terimi canonical'a çevir
     */
    public function getCanonical(string $term): string
    {
        $synonyms = $this->getSynonyms();
        return $synonyms[strtolower($term)] ?? $term;
    }

    /**
     * Eş anlamlı sil
     */
    public function removeSynonym(string $term): bool
    {
        $data = $this->getLearningData();
        unset($data['synonyms'][$term]);
        return $this->saveLearningData($data);
    }

    // ========================================
    // POPÜLER ARAMA TAKİBİ (Otomatik)
    // ========================================

    /**
     * Aramayı kaydet (otomatik öğrenme)
     */
    public function recordSearch(string $query): void
    {
        $data = $this->getLearningData();
        $normalizedQuery = strtolower(trim($query));

        if (!isset($data['popular_searches'][$normalizedQuery])) {
            $data['popular_searches'][$normalizedQuery] = [
                'count' => 0,
                'first_searched' => now()->toISOString(),
            ];
        }

        $data['popular_searches'][$normalizedQuery]['count']++;
        $data['popular_searches'][$normalizedQuery]['last_searched'] = now()->toISOString();

        $this->saveLearningData($data);
    }

    /**
     * Popüler aramaları al
     */
    public function getPopularSearches(int $limit = 10): array
    {
        $data = $this->getLearningData();
        $searches = $data['popular_searches'] ?? [];

        // Count'a göre sırala
        uasort($searches, fn($a, $b) => ($b['count'] ?? 0) <=> ($a['count'] ?? 0));

        return array_slice($searches, 0, $limit, true);
    }

    // ========================================
    // SIK SORULAN SORULAR (Otomatik)
    // ========================================

    /**
     * Soruyu kaydet (otomatik öğrenme)
     */
    public function recordQuestion(string $question, string $category = 'general'): void
    {
        $data = $this->getLearningData();
        $normalizedQuestion = strtolower(trim($question));

        // Çok kısa sorular veya fiyat/stok soruları kaydetme
        if (strlen($normalizedQuestion) < 5) {
            return;
        }

        // Güvenli olmayan sorular (fiyat, stok vb.) kaydetme
        $unsafePatterns = ['fiyat', 'kaç tl', 'kaç lira', 'stok', 'var mı'];
        foreach ($unsafePatterns as $pattern) {
            if (stripos($normalizedQuestion, $pattern) !== false) {
                return;
            }
        }

        if (!isset($data['common_questions'][$normalizedQuestion])) {
            $data['common_questions'][$normalizedQuestion] = [
                'count' => 0,
                'category' => $category,
                'first_asked' => now()->toISOString(),
            ];
        }

        $data['common_questions'][$normalizedQuestion]['count']++;
        $data['common_questions'][$normalizedQuestion]['last_asked'] = now()->toISOString();

        $this->saveLearningData($data);
    }

    /**
     * Sık sorulan soruları al
     */
    public function getCommonQuestions(int $limit = 10, ?string $category = null): array
    {
        $data = $this->getLearningData();
        $questions = $data['common_questions'] ?? [];

        if ($category) {
            $questions = array_filter($questions, fn($q) => ($q['category'] ?? '') === $category);
        }

        // Count'a göre sırala
        uasort($questions, fn($a, $b) => ($b['count'] ?? 0) <=> ($a['count'] ?? 0));

        return array_slice($questions, 0, $limit, true);
    }

    // ========================================
    // KARA LİSTE YÖNETİMİ
    // ========================================

    /**
     * Kara listeye ekle (yanlış öğrenmeyi engelle)
     */
    public function addToBlacklist(string $term, string $reason = ''): bool
    {
        $data = $this->getLearningData();

        $data['blacklist'][$term] = [
            'reason' => $reason,
            'added_at' => now()->toISOString(),
        ];

        // Eğer synonyms'te varsa sil
        unset($data['synonyms'][$term]);

        Log::info('[FileLearning] Term added to blacklist', [
            'term' => $term,
            'reason' => $reason
        ]);

        return $this->saveLearningData($data);
    }

    /**
     * Kara listeden çıkar
     */
    public function removeFromBlacklist(string $term): bool
    {
        $data = $this->getLearningData();
        unset($data['blacklist'][$term]);
        return $this->saveLearningData($data);
    }

    /**
     * Kara listeyi al
     */
    public function getBlacklist(): array
    {
        $data = $this->getLearningData();
        return $data['blacklist'] ?? [];
    }

    // ========================================
    // YARDIMCI METODLAR
    // ========================================

    /**
     * Tüm öğrenme verisini sıfırla
     */
    public function resetLearningData(): bool
    {
        $filePath = $this->getLearningFilePath();

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $this->initializeLearningFile();

        Log::info('[FileLearning] Learning data reset', [
            'tenant_id' => $this->getTenantId()
        ]);

        return true;
    }

    /**
     * Öğrenme istatistiklerini al
     */
    public function getStats(): array
    {
        $data = $this->getLearningData();

        return [
            'tenant_id' => $data['tenant_id'] ?? null,
            'version' => $data['version'] ?? '1.0.0',
            'synonyms_count' => count($data['synonyms'] ?? []),
            'promoted_products_count' => count($data['promoted_products'] ?? []),
            'popular_searches_count' => count($data['popular_searches'] ?? []),
            'common_questions_count' => count($data['common_questions'] ?? []),
            'blacklist_count' => count($data['blacklist'] ?? []),
            'history_count' => count($data['learning_history'] ?? []),
            'created_at' => $data['created_at'] ?? null,
            'updated_at' => $data['updated_at'] ?? null,
        ];
    }

    /**
     * AI prompt için öğrenme context'i oluştur
     */
    public function buildLearningContext(): string
    {
        $data = $this->getLearningData();
        $context = "";

        // Öncelikli ürünler
        $promotedProducts = $data['promoted_products'] ?? [];
        if (!empty($promotedProducts)) {
            $context .= "\n\n## 🌟 ÖNCELİKLİ ÜRÜNLER (Bu ürünleri özellikle öner):\n";
            foreach ($promotedProducts as $product) {
                $tonnageInfo = !empty($product['tonnage']) ? " - {$product['tonnage']} Ton" : "";
                $context .= "- **{$product['keyword']}** aramasında → **{$product['product_pattern']}{$tonnageInfo}** ürünü öncelikli";
                if (!empty($product['reason'])) {
                    $context .= " ({$product['reason']})";
                }
                $context .= "\n";
            }
        }

        // Popüler aramalar (top 5)
        $popularSearches = $this->getPopularSearches(5);
        if (!empty($popularSearches)) {
            $context .= "\n## 📊 POPÜLER ARAMALAR:\n";
            foreach ($popularSearches as $search => $info) {
                $context .= "- {$search} ({$info['count']} kez arandı)\n";
            }
        }

        return $context;
    }

    /**
     * Öğrenme dosyası var mı kontrol et
     */
    public function hasLearningFile(): bool
    {
        return File::exists($this->getLearningFilePath());
    }

    /**
     * Öğrenme geçmişini al
     */
    public function getLearningHistory(int $limit = 50): array
    {
        $data = $this->getLearningData();
        $history = $data['learning_history'] ?? [];

        // Son eklenenleri ilk göster
        $history = array_reverse($history);

        return array_slice($history, 0, $limit);
    }
}
