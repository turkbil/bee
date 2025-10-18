<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopProduct;
use Modules\AI\App\Services\AIService;
use Illuminate\Support\Facades\Cache;

/**
 * AI-Powered Spare Parts Content Generation
 *
 * Generates description, tags, and short description for spare parts
 * based on product title and industrial equipment sector knowledge
 */
class GenerateSparePartsContentCommand extends Command
{
    protected $signature = 'shop:generate-spare-parts-content
                            {--tenant= : Specific tenant ID}
                            {--limit= : Limit number of products}
                            {--force : Overwrite existing content}
                            {--dry-run : Preview only}
                            {--category= : Specific category ID}';

    protected $description = 'Generate AI-powered content for spare parts (description + tags)';

    protected AIService $aiService;
    protected int $generated = 0;
    protected int $skipped = 0;
    protected int $errors = 0;

    public function __construct(AIService $aiService)
    {
        parent::__construct();
        $this->aiService = $aiService;
    }

    public function handle()
    {
        $this->info('🤖 AI-Powered Spare Parts Content Generation');
        $this->newLine();

        if ($tenantId = $this->option('tenant')) {
            $this->generateForTenant($tenantId);
        } else {
            $this->generateForAllTenants();
        }

        $this->displaySummary();

        return 0;
    }

    protected function generateForAllTenants()
    {
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            $this->info("Processing Tenant: {$tenant->id}");
            tenancy()->initialize($tenant);
            $this->generateTenantContent($tenant->id);
            tenancy()->end();
        }
    }

    protected function generateForTenant($tenantId)
    {
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found!");
            return;
        }

        tenancy()->initialize($tenant);
        $this->generateTenantContent($tenantId);
        tenancy()->end();
    }

    protected function generateTenantContent($tenantId)
    {
        // Get spare parts products (all products are spare parts)
        $query = ShopProduct::query();

        if ($categoryId = $this->option('category')) {
            $query->where('category_id', $categoryId);
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int)$limit);
        }

        // Order by: products without description first
        $query->orderByRaw('CASE WHEN body IS NULL OR body = "" THEN 0 ELSE 1 END');

        $products = $query->get();

        $this->info("Found {$products->count()} products to process");

        $bar = $this->output->createProgressBar($products->count());
        $bar->setFormat('%current%/%max% [%bar%] %percent:3s%% %message%');

        foreach ($products as $product) {
            // Skip if content exists and not force
            if (!$this->option('force') && !empty($product->body)) {
                $this->skipped++;
                $bar->setMessage("Skipped: {$product->sku} (has content)");
                $bar->advance();
                continue;
            }

            try {
                $content = $this->generateContent($product);

                if (!$this->option('dry-run')) {
                    $this->updateProduct($product, $content);
                }

                $this->generated++;
                $bar->setMessage("Generated: {$product->sku}");
            } catch (\Exception $e) {
                $this->errors++;
                $this->error("\nError for {$product->sku}: " . $e->getMessage());
                $bar->setMessage("Error: {$product->sku}");
            }

            $bar->advance();

            // Rate limiting - 1 request per second
            sleep(1);
        }

        $bar->finish();
        $this->newLine(2);
    }

    protected function generateContent(ShopProduct $product): array
    {
        $title = is_array($product->title)
            ? ($product->title['tr'] ?? $product->title['en'] ?? '')
            : $product->title;

        $sku = $product->sku;

        // Get existing content for reference (if any)
        $existingBody = is_array($product->body)
            ? ($product->body['tr'] ?? $product->body['en'] ?? '')
            : ($product->body ?? '');

        $existingShortDesc = is_array($product->short_description)
            ? ($product->short_description['tr'] ?? $product->short_description['en'] ?? '')
            : ($product->short_description ?? '');

        // Build AI prompt
        $prompt = $this->buildPrompt($title, $sku, $existingBody, $existingShortDesc);

        // Call AI
        $response = $this->aiService->ask($prompt, [
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ]);

        // Parse response
        return $this->parseAIResponse($response['content'] ?? '');
    }

    protected function buildPrompt(string $title, string $sku, string $existingBody, string $existingShortDesc): string
    {
        $prompt = <<<PROMPT
# GÖREV: Endüstriyel Yedek Parça İçeriği Oluştur

## SEKTÖR BİLGİSİ
- **Sektör:** Endüstriyel ekipman yedek parçaları
- **Ana Ürünler:** Forklift, transpalet, istif makinesi, reach truck
- **Hedef Müşteri:** Lojistik firmaları, depo işletmeleri, üretim tesisleri
- **Marka:** İXTİF

## ÜRÜN BİLGİLERİ
- **Başlık:** {$title}
- **SKU:** {$sku}
PROMPT;

        if (!empty($existingBody)) {
            $prompt .= "\n- **Mevcut İçerik (Referans):** " . mb_substr(strip_tags($existingBody), 0, 200);
        }

        if (!empty($existingShortDesc)) {
            $prompt .= "\n- **Mevcut Kısa Açıklama:** " . mb_substr(strip_tags($existingShortDesc), 0, 100);
        }

        $prompt .= <<<PROMPT


## OLUŞTURULMASI GEREKENLER

### 1. DESCRIPTION (Tailwind HTML Format)
**Gereklilikler:**
- Tailwind CSS class'ları kullan
- Responsive tasarım
- SEO-friendly başlıklar
- Ürünün ne olduğunu açıkla
- Hangi ekipmanlarda kullan ıldığını belirt
- Teknik özellikleri listele (varsa başlıktan çıkar)
- Kullanım alanları
- Avantajları
- Minimum 300, maksimum 500 kelime

**Tailwind Stil Kuralları:**
- Başlıklar: `<h2 class="text-2xl font-bold text-gray-800 mb-4">`
- Paragraflar: `<p class="text-gray-700 leading-relaxed mb-4">`
- Liste: `<ul class="list-disc list-inside space-y-2 text-gray-700">`
- Vurgu: `<strong class="text-blue-600 font-semibold">`
- Dikkat: `<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-4">`

### 2. SHORT DESCRIPTION
- Maksimum 160 karakter
- Ürünü özetleyen, SEO-friendly
- Hangi ekipman için olduğunu belirt

### 3. TAGS
- Virgülle ayrılmış
- Endüstriyel, ekipman tipi, kategori, özellikler
- Minimum 5, maksimum 10 tag
- Örnekler: forklift yedek parça, transpalet parçası, tekerlek, hidrolik, elektrikli

## ÖZEL TALIMATLAR

1. **Başlıktan Çıkarım:**
   - Ürün tipini belirle (tekerlek, hidrolik, batarya, motor, vb.)
   - Hangi ekipman için olduğunu anla (forklift/transpalet/istif)
   - Boyut bilgisi varsa ekle
   - Malzeme bilgisi varsa vurgula

2. **Sektöre Uygun Dil:**
   - Profesyonel ama anlaşılır
   - Teknik terimler kullan ama açıkla
   - B2B tone (firmalarla konuşuyoruz)
   - Güvenilirlik ve kalite vurgusu

3. **SEO Optimizasyonu:**
   - Ana keyword: başlıktaki ürün adı
   - LSI keywords: ekipman tipleri, kullanım alanları
   - Natural dil kullan (keyword stuffing yok)

4. **Tailwind Best Practices:**
   - Mobile-first responsive
   - Consistent spacing (mb-4, space-y-2)
   - Readable colors (gray-700 for text, gray-800 for headings)
   - Semantic HTML (h2, p, ul, strong)

## ÇIKTI FORMATI

Sadece şu JSON formatında yanıt ver (başka metin ekleme):

```json
{
  "description": "Tailwind HTML formatted description here",
  "short_description": "160 karakter kısa açıklama",
  "tags": "tag1, tag2, tag3, tag4, tag5"
}
```

## ÖRNEK ÇIKTI

Başlık: "Poliüretan Transpalet Tekeri - 82x70 mm"

```json
{
  "description": "<h2 class=\\"text-2xl font-bold text-gray-800 mb-4\\">Poliüretan Transpalet Tekeri - Yüksek Dayanıklı Tekerlek Çözümü</h2><p class=\\"text-gray-700 leading-relaxed mb-4\\">82x70 mm boyutlarındaki poliüretan transpalet tekeri, manuel ve elektrikli transpaletlerde kullanılmak üzere tasarlanmış yüksek kaliteli bir yedek parçadır. Poliüretan malzeme yapısı sayesinde uzun ömürlü ve dayanıklıdır.</p><h3 class=\\"text-xl font-semibold text-gray-800 mb-3\\">Teknik Özellikler</h3><ul class=\\"list-disc list-inside space-y-2 text-gray-700 mb-4\\"><li>Boyut: 82 mm çap x 70 mm genişlik</li><li>Malzeme: Yüksek yoğunluklu poliüretan</li><li>Kullanım Alanı: Manuel ve elektrikli transpaletler</li><li>Yük Kapasitesi: 2000 kg'a kadar</li></ul><div class=\\"bg-blue-50 border-l-4 border-blue-400 p-4 my-4\\"><p class=\\"text-blue-800\\"><strong>Avantajlar:</strong> Sessiz çalışma, zemin koruma, düşük yuvarlanma direnci, uzun ömür</p></div>",
  "short_description": "82x70 mm poliüretan transpalet tekeri. Manuel ve elektrikli transpaletler için yüksek dayanıklı, sessiz çalışan yedek parça.",
  "tags": "transpalet yedek parça, poliüretan tekerlek, transpalet tekeri, endüstriyel tekerlek, 82mm tekerlek, yedek parça, depo ekipmanı"
}
```

ŞİMDİ BAŞLA - Yukarıdaki ürün bilgilerini kullanarak içerik oluştur:
PROMPT;

        return $prompt;
    }

    protected function parseAIResponse(string $response): array
    {
        // DEBUG: Log raw response
        $this->line("\n📄 AI Response Preview:");
        $this->line(mb_substr($response, 0, 500) . "...\n");

        // Extract JSON from response
        if (preg_match('/```json\s*(.*?)\s*```/s', $response, $matches)) {
            $json = $matches[1];
        } elseif (preg_match('/\{[\s\S]*\}/s', $response, $matches)) {
            $json = $matches[0];
        } else {
            throw new \Exception('Could not parse AI response - no JSON found');
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("JSON Error: " . json_last_error_msg());
            $this->line("JSON attempt: " . mb_substr($json, 0, 300));
            throw new \Exception('Invalid JSON: ' . json_last_error_msg());
        }

        if (empty($data['description']) || empty($data['short_description']) || empty($data['tags'])) {
            $this->warn("Parsed data: " . json_encode(array_keys($data)));
            throw new \Exception('Missing required fields in AI response');
        }

        return $data;
    }

    protected function updateProduct(ShopProduct $product, array $content): void
    {
        // Update body (description) - support JSON fields
        if (is_array($product->body)) {
            $product->body = array_merge($product->body, [
                'tr' => $content['description'],
                'en' => $content['description'], // Same for now
            ]);
        } else {
            $product->body = [
                'tr' => $content['description'],
                'en' => $content['description'],
            ];
        }

        // Update short_description
        if (is_array($product->short_description)) {
            $product->short_description = array_merge($product->short_description, [
                'tr' => $content['short_description'],
                'en' => $content['short_description'],
            ]);
        } else {
            $product->short_description = [
                'tr' => $content['short_description'],
                'en' => $content['short_description'],
            ];
        }

        // Update tags (convert comma-separated to array)
        $tags = array_map('trim', explode(',', $content['tags']));
        $product->tags = $tags;

        $product->save();
    }

    protected function displaySummary()
    {
        $this->newLine();
        $this->info('========================================');
        $this->info('📊 CONTENT GENERATION SUMMARY');
        $this->info('========================================');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE');
            $this->newLine();
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['✅ Generated', $this->generated],
                ['⏭️  Skipped', $this->skipped],
                ['❌ Errors', $this->errors],
            ]
        );

        $total = $this->generated + $this->skipped + $this->errors;
        $successRate = $total > 0 ? round(($this->generated / $total) * 100, 2) : 0;

        $this->newLine();
        $this->info("Success Rate: {$successRate}%");

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->info('💡 Run without --dry-run to save changes');
        }
    }
}
