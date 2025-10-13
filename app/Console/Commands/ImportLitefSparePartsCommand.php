<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class ImportLitefSparePartsCommand extends Command
{
    protected $signature = 'litef:import-spare-parts
                            {--dry-run : Test modunda çalıştır (veritabanına kaydetmez)}
                            {--categories-only : Sadece kategorileri import et}
                            {--products-only : Sadece ürünleri import et}
                            {--match-photos : Mevcut ürünlere fotoğraf eşleştir}';

    protected $description = 'Litef projesinden yedek parça kategorileri, ürünleri ve fotoğrafları import eder';

    private $litefConnection;
    private $categoryMapping = [];
    private $importStats = [
        'categories_created' => 0,
        'products_created' => 0,
        'photos_copied' => 0,
        'photos_matched' => 0,
        'errors' => [],
    ];

    private $litefImagePath = '/Users/nurullah/Desktop/cms/litef/modules/digishop/dataimages/';
    private $targetDisk = 'public';
    private $targetPath = 'shop/products';

    public function handle()
    {
        $this->info('🚀 Litef Yedek Parça Import Başlıyor...');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('⚠️  DRY-RUN MODU: Veritabanına kayıt YAPILMAYACAK');
            $this->newLine();
        }

        // Litef veritabanı bağlantısı
        if (!$this->connectToLitef()) {
            $this->error('❌ Litef veritabanına bağlanılamadı!');
            return 1;
        }

        // İşlem seçimi
        if ($this->option('match-photos')) {
            $this->matchExistingProductPhotos();
        } elseif ($this->option('categories-only')) {
            $this->importCategories();
        } elseif ($this->option('products-only')) {
            $this->importProducts();
        } else {
            // Tüm işlem
            $this->importCategories();
            $this->importProducts();
        }

        // Sonuç raporu
        $this->displaySummary();

        return 0;
    }

    /**
     * Litef veritabanına bağlan
     */
    private function connectToLitef(): bool
    {
        try {
            config([
                'database.connections.litef' => [
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'database' => 'litef_ekim23',
                    'username' => 'litef_ekim23',
                    'password' => '*ssw3R[$)~]h',
                    'charset' => 'utf8mb3',
                    'collation' => 'utf8mb3_turkish_ci',
                    'prefix' => '',
                    'strict' => false,
                ]
            ]);

            $this->litefConnection = DB::connection('litef');
            $this->litefConnection->getPdo();

            $this->info('✅ Litef veritabanına bağlandı');
            return true;
        } catch (\Exception $e) {
            $this->error('Bağlantı hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kategorileri import et
     */
    private function importCategories()
    {
        $this->info('📁 Kategoriler import ediliyor...');

        // Yedek parça ana kategorisi (ID: 50)
        $sparePartsRootId = 50;

        // Ana kategoriyi çek
        $rootCategory = $this->litefConnection
            ->table('mod_digishop_categories')
            ->where('id', $sparePartsRootId)
            ->first();

        if (!$rootCategory) {
            $this->error('❌ Yedek Parça ana kategorisi bulunamadı!');
            return;
        }

        // Tüm alt kategorileri çek (3 seviye hiyerarşi)
        // Seviye 1: YEDEK PARÇA (50)
        // Seviye 2: Lastik Jant Teker (51), Motor Grubu (63), vs.
        // Seviye 3: Siyah Dolgu Lastik (52), Çelik Plate (89), vs. -> Ürünler burada

        // Önce seviye 2 kategorileri al
        $level2CategoryIds = $this->litefConnection
            ->table('mod_digishop_categories')
            ->where('parent_id', $sparePartsRootId)
            ->where('active', 1)
            ->pluck('id')
            ->toArray();

        // Tüm kategorileri çek (Seviye 1, 2, 3)
        $categories = $this->litefConnection
            ->table('mod_digishop_categories')
            ->where(function ($query) use ($sparePartsRootId, $level2CategoryIds) {
                $query->where('id', $sparePartsRootId)
                      ->orWhere('parent_id', $sparePartsRootId)
                      ->orWhereIn('parent_id', $level2CategoryIds);
            })
            ->where('active', 1)
            ->orderBy('parent_id', 'asc')
            ->orderBy('sorting', 'asc')
            ->get();

        $this->info("Toplam {$categories->count()} kategori bulundu");
        $this->newLine();

        $bar = $this->output->createProgressBar($categories->count());
        $bar->start();

        foreach ($categories as $category) {
            try {
                $this->importCategory($category);
                $bar->advance();
            } catch (\Exception $e) {
                $this->importStats['errors'][] = "Kategori {$category->id}: " . $e->getMessage();
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
    }

    /**
     * Tek bir kategoriyi import et
     */
    private function importCategory($litefCategory)
    {
        if ($this->option('dry-run')) {
            $this->categoryMapping[$litefCategory->id] = 999; // Fake ID
            return;
        }

        // Parent mapping kontrolü
        $parentId = null;
        if ($litefCategory->parent_id > 0 && isset($this->categoryMapping[$litefCategory->parent_id])) {
            $parentId = $this->categoryMapping[$litefCategory->parent_id];
        }

        // Kategori zaten var mı?
        $existingCategory = ShopCategory::where('slug->tr', $litefCategory->slug)->first();

        if ($existingCategory) {
            $this->categoryMapping[$litefCategory->id] = $existingCategory->category_id;
            return;
        }

        // Level hesapla (parent varsa onun level'ına +1)
        $level = 1;
        if ($parentId) {
            $parentCategory = ShopCategory::find($parentId);
            $level = $parentCategory ? $parentCategory->level + 1 : 2;
        }

        // Yeni kategori oluştur
        $category = ShopCategory::create([
            'parent_id' => $parentId,
            'title' => [
                'tr' => $litefCategory->name_tr,
            ],
            'slug' => [
                'tr' => $litefCategory->slug,
            ],
            'description' => [
                'tr' => $this->cleanHtml($litefCategory->body_tr),
            ],
            'image_url' => $litefCategory->thumb,
            'level' => $level,
            'sort_order' => $litefCategory->sorting ?? 0,
            'is_active' => (bool) $litefCategory->active,
            'show_in_menu' => true,
            'show_in_homepage' => false,
        ]);

        // Mapping kaydet
        $this->categoryMapping[$litefCategory->id] = $category->category_id;
        $this->importStats['categories_created']++;
    }

    /**
     * Ürünleri import et
     */
    private function importProducts()
    {
        $this->info('📦 Ürünler import ediliyor...');

        // Yedek parça kategorisindeki ürünleri çek (3 seviye hiyerarşi)
        $sparePartsRootId = 50;

        // Seviye 2 kategorileri al
        $level2CategoryIds = $this->litefConnection
            ->table('mod_digishop_categories')
            ->where('parent_id', $sparePartsRootId)
            ->where('active', 1)
            ->pluck('id')
            ->toArray();

        // Seviye 3 kategorileri al (ürünler burada)
        $level3CategoryIds = $this->litefConnection
            ->table('mod_digishop_categories')
            ->whereIn('parent_id', $level2CategoryIds)
            ->where('active', 1)
            ->pluck('id')
            ->toArray();

        // Tüm kategori ID'lerini birleştir
        $categoryIds = array_merge([$sparePartsRootId], $level2CategoryIds, $level3CategoryIds);

        if (empty($categoryIds)) {
            $this->warn('⚠️  Yedek parça kategorisi bulunamadı');
            return;
        }

        // Ürünleri çek
        $products = $this->litefConnection
            ->table('mod_digishop')
            ->whereIn('cid', $categoryIds)
            ->where('active', 1)
            ->get();

        $this->info("Toplam {$products->count()} ürün bulundu");
        $this->newLine();

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            try {
                $this->importProduct($product);
                $bar->advance();
            } catch (\Exception $e) {
                $this->importStats['errors'][] = "Ürün {$product->id}: " . $e->getMessage();
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
    }

    /**
     * Tek bir ürünü import et
     */
    private function importProduct($litefProduct)
    {
        if ($this->option('dry-run')) {
            return;
        }

        // Kategori mapping kontrolü
        if (!isset($this->categoryMapping[$litefProduct->cid])) {
            throw new \Exception("Kategori mapping bulunamadı: {$litefProduct->cid}");
        }

        $categoryId = $this->categoryMapping[$litefProduct->cid];

        // SKU oluştur
        $sku = $this->generateSku($litefProduct);

        // Ürün zaten var mı?
        $existingProduct = ShopProduct::where('sku', $sku)->first();

        if ($existingProduct) {
            // Fotoğrafları ekle
            $this->attachPhotosToProduct($existingProduct, $litefProduct);
            return;
        }

        // Marka: İXTİF (ID: 1 varsayıyoruz)
        $ixtifBrand = ShopBrand::where('slug->tr', 'ixtif')->first();
        $brandId = $ixtifBrand ? $ixtifBrand->brand_id : null;

        // Yeni ürün oluştur
        $product = ShopProduct::create([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'sku' => $sku,
            'model_number' => $litefProduct->model ?: null,
            'title' => [
                'tr' => $litefProduct->title_tr,
            ],
            'slug' => [
                'tr' => $litefProduct->slug,
            ],
            'short_description' => [
                'tr' => $this->cleanHtml($litefProduct->short_desc_tr),
            ],
            'body' => [
                'tr' => $this->mergeBodyContent($litefProduct),
            ],
            'product_type' => 'physical',
            'condition' => 'new',
            'price_on_request' => true, // Fiyat gösterme
            'base_price' => 0.00,
            'currency' => 'TRY',
            'stock_tracking' => false,
            'is_active' => (bool) $litefProduct->active,
            'is_featured' => (bool) $litefProduct->showcase,
            'published_at' => now(),
        ]);

        // Fotoğrafları ekle
        $this->attachPhotosToProduct($product, $litefProduct);

        $this->importStats['products_created']++;
    }

    /**
     * Ürüne fotoğraf ekle (Spatie Media)
     */
    private function attachPhotosToProduct($product, $litefProduct)
    {
        $photos = $this->collectProductPhotos($litefProduct);

        if (empty($photos)) {
            return;
        }

        foreach ($photos as $index => $photo) {
            $sourcePath = $this->litefImagePath . $photo;

            if (!file_exists($sourcePath)) {
                continue;
            }

            try {
                // İlk fotoğraf featured_image, diğerleri gallery
                $collection = ($index === 0) ? 'featured_image' : 'gallery';

                // Spatie Media ile ekle
                $product->addMedia($sourcePath)
                    ->toMediaCollection($collection, $this->targetDisk);

                $this->importStats['photos_copied']++;
            } catch (\Exception $e) {
                $this->importStats['errors'][] = "Fotoğraf eklenemedi ({$photo}): " . $e->getMessage();
            }
        }
    }

    /**
     * Ürün fotoğraflarını topla
     */
    private function collectProductPhotos($litefProduct): array
    {
        $photos = [];

        // Ana fotoğraf
        if (!empty($litefProduct->thumb)) {
            $photos[] = $litefProduct->thumb;
        }

        // Ek fotoğraflar (thumb_1 - thumb_6)
        for ($i = 1; $i <= 6; $i++) {
            $field = "thumb_{$i}";
            if (!empty($litefProduct->$field)) {
                $photos[] = $litefProduct->$field;
            }
        }

        return array_unique($photos);
    }

    /**
     * Mevcut ürünlere model_number ile fotoğraf eşleştir
     */
    private function matchExistingProductPhotos()
    {
        $this->info('🔍 Mevcut ürünlere fotoğraf eşleştiriliyor...');
        $this->newLine();

        // Laravel'deki tüm ürünleri al
        $laravelProducts = ShopProduct::whereNotNull('model_number')->get();

        $this->info("Toplam {$laravelProducts->count()} ürün kontrol edilecek");

        $bar = $this->output->createProgressBar($laravelProducts->count());
        $bar->start();

        foreach ($laravelProducts as $laravelProduct) {
            try {
                // Litef'te model_number ile eşleşen ürünü bul
                $litefProduct = $this->litefConnection
                    ->table('mod_digishop')
                    ->where('model', $laravelProduct->model_number)
                    ->where('active', 1)
                    ->first();

                if ($litefProduct) {
                    // Fotoğrafları ekle
                    $this->attachPhotosToProduct($laravelProduct, $litefProduct);
                    $this->importStats['photos_matched']++;
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->importStats['errors'][] = "Model eşleştirme hatası ({$laravelProduct->model_number}): " . $e->getMessage();
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
    }

    /**
     * SKU oluştur
     */
    private function generateSku($litefProduct): string
    {
        // Öncelik sırası: product_code > model > id
        if (!empty($litefProduct->product_code)) {
            return 'LITEF-' . strtoupper($litefProduct->product_code);
        }

        if (!empty($litefProduct->model)) {
            return 'LITEF-' . strtoupper(Str::slug($litefProduct->model, ''));
        }

        return 'LITEF-' . $litefProduct->id;
    }

    /**
     * Body içeriklerini birleştir
     */
    private function mergeBodyContent($litefProduct): string
    {
        $parts = [];

        if (!empty($litefProduct->body_tr)) {
            $parts[] = $this->cleanHtml($litefProduct->body_tr);
        }

        if (!empty($litefProduct->body2_tr)) {
            $parts[] = $this->cleanHtml($litefProduct->body2_tr);
        }

        return implode("\n\n", $parts);
    }

    /**
     * HTML temizle
     */
    private function cleanHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // HTML entity decode
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // &lt; gibi kaçış karakterlerini düzelt
        $html = str_replace(['&lt;', '&gt;', '&amp;'], ['<', '>', '&'], $html);

        return trim($html);
    }

    /**
     * Sonuç raporu
     */
    private function displaySummary()
    {
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 İMPORT SONUÇLARI');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $this->line("✅ Oluşturulan Kategori: <fg=green>{$this->importStats['categories_created']}</>");
        $this->line("✅ Oluşturulan Ürün: <fg=green>{$this->importStats['products_created']}</>");
        $this->line("📸 Kopyalanan Fotoğraf: <fg=cyan>{$this->importStats['photos_copied']}</>");
        $this->line("🔗 Eşleştirilen Fotoğraf: <fg=cyan>{$this->importStats['photos_matched']}</>");

        if (!empty($this->importStats['errors'])) {
            $this->newLine();
            $this->error("❌ Hatalar ({count($this->importStats['errors'])} adet):");
            foreach (array_slice($this->importStats['errors'], 0, 10) as $error) {
                $this->line("   • {$error}");
            }
            if (count($this->importStats['errors']) > 10) {
                $this->line("   ... ve " . (count($this->importStats['errors']) - 10) . " hata daha");
            }
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
