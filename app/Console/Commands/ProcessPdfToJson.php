<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Process PDF to JSON Command
 *
 * EP PDF klasöründeki tüm PDF'leri AI ile işler ve JSON extract üretir.
 *
 * Kullanım:
 * php artisan shop:process-pdf-to-json
 * php artisan shop:process-pdf-to-json --folder="2-Transpalet"
 * php artisan shop:process-pdf-to-json --single="/path/to/file.pdf"
 */
class ProcessPdfToJson extends Command
{
    protected $signature = 'shop:process-pdf-to-json
                            {--folder= : Sadece belirli klasörü işle (örn: 2-Transpalet)}
                            {--single= : Tek bir PDF dosyasını işle}
                            {--overwrite : Mevcut JSON dosyalarını üzerine yaz}';

    protected $description = 'EP PDF klasöründeki tüm PDF\'leri AI ile işler ve JSON extract üretir';

    private string $epPdfPath = '/Users/nurullah/Desktop/cms/EP PDF';
    private string $jsonExtractPath;
    private string $promptPath;
    private array $stats = [
        'total_pdfs' => 0,
        'processed' => 0,
        'skipped' => 0,
        'errors' => [],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->jsonExtractPath = base_path('readme/ecommerce/json-extracts');
        $this->promptPath = base_path('readme/ecommerce/ai-prompts/01-pdf-to-product-json.md');
    }

    public function handle(): int
    {
        $this->info('🤖 PDF → JSON İşleme Başlıyor...');
        $this->info('📂 EP PDF Klasörü: ' . $this->epPdfPath);
        $this->info('📂 JSON Extract Klasörü: ' . $this->jsonExtractPath);

        // Klasör kontrolü
        if (!File::isDirectory($this->epPdfPath)) {
            $this->error('❌ EP PDF klasörü bulunamadı: ' . $this->epPdfPath);
            return 1;
        }

        // JSON extract klasörünü oluştur
        if (!File::isDirectory($this->jsonExtractPath)) {
            File::makeDirectory($this->jsonExtractPath, 0755, true);
            $this->info('✅ JSON extract klasörü oluşturuldu');
        }

        // Tek PDF işleme modu
        if ($singlePdf = $this->option('single')) {
            return $this->processSinglePdf($singlePdf);
        }

        // Klasör filtresi
        $folderFilter = $this->option('folder');

        // PDF dosyalarını tara
        $pdfFiles = $this->scanPdfFiles($folderFilter);
        $this->stats['total_pdfs'] = count($pdfFiles);

        if (empty($pdfFiles)) {
            $this->warn('⚠️  İşlenecek PDF dosyası bulunamadı');
            return 0;
        }

        $this->info("📄 {$this->stats['total_pdfs']} adet PDF bulundu");

        // Kullanıcı onayı
        if (!$this->confirm('İşleme devam edilsin mi?', true)) {
            $this->info('❌ İşlem iptal edildi');
            return 0;
        }

        // İşleme başla
        $bar = $this->output->createProgressBar(count($pdfFiles));
        $bar->start();

        foreach ($pdfFiles as $pdfFile) {
            try {
                $this->processPdf($pdfFile);
                $bar->advance();
            } catch (\Exception $e) {
                $this->stats['errors'][] = [
                    'file' => $pdfFile,
                    'error' => $e->getMessage()
                ];
            }
        }

        $bar->finish();
        $this->newLine(2);

        // İstatistikler
        $this->printStats();

        // Sonraki adım önerisi
        $this->suggestNextStep();

        return 0;
    }

    /**
     * PDF dosyalarını tarar
     */
    private function scanPdfFiles(?string $folderFilter = null): array
    {
        $pattern = $folderFilter
            ? $this->epPdfPath . '/' . $folderFilter . '/**/*.pdf'
            : $this->epPdfPath . '/**/*.pdf';

        return File::glob($pattern);
    }

    /**
     * Tek bir PDF dosyasını işler
     */
    private function processSinglePdf(string $pdfPath): int
    {
        if (!File::exists($pdfPath)) {
            $this->error('❌ PDF dosyası bulunamadı: ' . $pdfPath);
            return 1;
        }

        $this->info('📄 İşleniyor: ' . basename($pdfPath));

        try {
            $this->processPdf($pdfPath);
            $this->info('✅ İşlem tamamlandı');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Hata: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Bir PDF dosyasını AI ile işler
     */
    private function processPdf(string $pdfPath): void
    {
        $fileName = basename($pdfPath, '.pdf');
        $jsonFileName = $this->sanitizeFileName($fileName) . '.json';
        $jsonPath = $this->jsonExtractPath . '/' . $jsonFileName;

        // Mevcut JSON kontrolü
        if (File::exists($jsonPath) && !$this->option('overwrite')) {
            $this->stats['skipped']++;
            return;
        }

        // ====================================================================
        // ÖNEMLİ: Gerçek AI entegrasyonu buraya gelecek
        // ====================================================================
        // Şu an için placeholder JSON oluşturuyoruz
        // Gerçek implementasyonda:
        // 1. AI API'ye PDF gönderilecek
        // 2. Prompt dosyası (01-pdf-to-product-json.md) okunacak
        // 3. AI'dan dönen JSON kaydedilecek
        // ====================================================================

        $this->createPlaceholderJson($pdfPath, $jsonPath, $fileName);

        $this->stats['processed']++;
    }

    /**
     * Placeholder JSON oluşturur (Geliştirme için)
     */
    private function createPlaceholderJson(string $pdfPath, string $jsonPath, string $fileName): void
    {
        // Kategori bilgisini PDF path'inden çıkar
        $categoryFolder = $this->extractCategoryFromPath($pdfPath);

        $placeholderData = [
            'product_info' => [
                'sku' => strtoupper($this->sanitizeFileName($fileName)),
                'model_number' => $fileName,
                'series_name' => 'Series',
                'product_type' => 'physical',
                'condition' => 'new',
            ],
            'basic_data' => [
                'title' => [
                    'tr' => $fileName,
                    'en' => $fileName,
                    'vs.' => '...'
                ],
                'slug' => [
                    'tr' => \Str::slug($fileName),
                    'en' => \Str::slug($fileName),
                    'vs.' => '...'
                ],
                'short_description' => [
                    'tr' => 'Kısa açıklama buraya gelecek...',
                    'en' => 'Short description here...',
                    'vs.' => '...'
                ],
                'long_description' => [
                    'tr' => '<section class="marketing-intro"><p>Pazarlama içeriği buraya gelecek...</p></section>',
                    'en' => '<section class="marketing-intro"><p>Marketing content here...</p></section>',
                    'vs.' => '...'
                ],
            ],
            'category_brand' => [
                'category_name' => $categoryFolder,
                'brand_id' => 1,
                'brand_name' => 'İXTİF',
                'manufacturer' => 'İXTİF İç ve Dış Ticaret A.Ş.'
            ],
            'pricing' => [
                'price_on_request' => true,
                'currency' => 'TRY',
                'deposit_required' => true,
                'deposit_percentage' => 30,
                'installment_available' => true,
            ],
            'inventory' => [
                'stock_tracking' => true,
                'current_stock' => 0,
                'lead_time_days' => 45,
            ],
            'technical_specs' => [],
            'features' => [],
            'use_cases' => [],
            'target_industries' => [],
            'faq_data' => [],
            'tags' => [],
            'metadata' => [
                'pdf_source' => $pdfPath,
                'extraction_date' => now()->toDateString(),
                'note' => 'Bu placeholder JSON. Gerçek veriyi AI dolduracak.'
            ]
        ];

        File::put($jsonPath, json_encode($placeholderData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * PDF path'inden kategori klasörünü çıkarır
     */
    private function extractCategoryFromPath(string $pdfPath): string
    {
        // Örn: "/path/2-Transpalet/F4 201/file.pdf" → "2-Transpalet"
        $relativePath = str_replace($this->epPdfPath . '/', '', $pdfPath);
        $parts = explode('/', $relativePath);
        return $parts[0] ?? 'unknown';
    }

    /**
     * Dosya adını temizler (URL-safe)
     */
    private function sanitizeFileName(string $fileName): string
    {
        return preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($fileName));
    }

    /**
     * İstatistikleri yazdır
     */
    private function printStats(): void
    {
        $this->info("📊 İSTATİSTİKLER:");
        $this->info("   📄 Toplam PDF: {$this->stats['total_pdfs']}");
        $this->info("   ✅ İşlenen: {$this->stats['processed']}");
        $this->info("   ⏭️  Atlanan: {$this->stats['skipped']}");

        if (!empty($this->stats['errors'])) {
            $this->error("\n❌ HATALAR ({count($this->stats['errors'])}):");
            foreach ($this->stats['errors'] as $error) {
                $this->error("   {$error['file']}: {$error['error']}");
            }
        }
    }

    /**
     * Sonraki adım önerir
     */
    private function suggestNextStep(): void
    {
        $this->newLine();
        $this->info("🚀 SONRAK İ ADIMLAR:");
        $this->info("   1️⃣  JSON dosyalarını AI ile doldur (Manuel veya API)");
        $this->info("   2️⃣  php artisan db:seed --class=ShopAttributeSeeder");
        $this->info("   3️⃣  php artisan db:seed --class=ShopProductMasterSeeder");
        $this->info("   4️⃣  Admin panelinden ürünleri kontrol et");
    }
}
