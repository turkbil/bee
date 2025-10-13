<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateLitefSeedersCommand extends Command
{
    protected $signature = 'litef:generate-seeders
                            {--categories-only : Sadece kategori seeder\'ları oluştur}
                            {--products-only : Sadece ürün seeder\'ları oluştur}
                            {--copy-photos : Fotoğrafları da kopyala}';

    protected $description = 'Litef veritabanından seeder dosyaları oluşturur';

    private $litefConnection;
    private $categoryMapping = [];
    private $stats = [
        'category_seeders' => 0,
        'product_seeders' => 0,
        'photos_copied' => 0,
    ];

    private $litefImagePath = '/Users/nurullah/Desktop/cms/litef/modules/digishop/dataimages/';
    private $targetImagePath = '/Users/nurullah/Desktop/cms/laravel/storage/app/public/litef-spare-parts/';

    private $seederPath = '/Users/nurullah/Desktop/cms/laravel/Modules/Shop/database/seeders/LitefSpareParts/';

    public function handle()
    {
        $this->info('🔧 Litef Seeder Generator Başlıyor...');
        $this->newLine();

        // Litef bağlantısı
        if (!$this->connectToLitef()) {
            $this->error('❌ Litef veritabanına bağlanılamadı!');
            return 1;
        }

        // Seeder klasörünü oluştur
        if (!File::exists($this->seederPath)) {
            File::makeDirectory($this->seederPath, 0755, true);
            $this->info("✅ Seeder klasörü oluşturuldu: {$this->seederPath}");
        }

        // Fotoğraf klasörünü oluştur
        if ($this->option('copy-photos') && !File::exists($this->targetImagePath)) {
            File::makeDirectory($this->targetImagePath, 0755, true);
            $this->info("✅ Fotoğraf klasörü oluşturuldu: {$this->targetImagePath}");
        }

        // İşlem seçimi
        if ($this->option('products-only')) {
            $this->generateProductSeeders();
        } elseif ($this->option('categories-only')) {
            $this->generateCategorySeeders();
        } else {
            $this->generateCategorySeeders();
            $this->generateProductSeeders();
        }

        // Fotoğrafları kopyala
        if ($this->option('copy-photos')) {
            $this->copyAllPhotos();
        }

        // Master seeder oluştur
        $this->generateMasterSeeder();

        // Sonuç
        $this->displaySummary();

        return 0;
    }

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
     * Kategori seeder'ları oluştur
     */
    private function generateCategorySeeders()
    {
        $this->info('📁 Kategori seeder\'ları oluşturuluyor...');

        $sparePartsRootId = 50;

        // Seviye 2 kategorileri al
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
            ->orderBy('id', 'asc')
            ->get();

        $this->info("Toplam {$categories->count()} kategori bulundu");

        // Tek bir seeder dosyası oluştur
        $this->generateCategorySeederFile($categories);

        $this->newLine();
    }

    /**
     * Kategori seeder dosyası oluştur
     */
    private function generateCategorySeederFile($categories)
    {
        $className = 'LitefSparePartsCategoriesSeeder';
        $filePath = $this->seederPath . $className . '.php';

        $code = $this->generateCategorySeederCode($className, $categories);

        File::put($filePath, $code);
        $this->stats['category_seeders']++;

        $this->info("✅ Kategori seeder oluşturuldu: {$className}");
    }

    /**
     * Kategori seeder kodu oluştur
     */
    private function generateCategorySeederCode($className, $categories): string
    {
        $code = "<?php\n\nnamespace Modules\Shop\Database\Seeders\LitefSpareParts;\n\n";
        $code .= "use Illuminate\Database\Seeder;\n";
        $code .= "use Modules\Shop\App\Models\ShopCategory;\n\n";
        $code .= "class {$className} extends Seeder\n{\n";
        $code .= "    private \$categoryMapping = [];\n\n";
        $code .= "    public function run(): void\n    {\n";

        foreach ($categories as $category) {
            $code .= $this->generateCategoryInsertCode($category);
        }

        $code .= "    }\n}\n";

        return $code;
    }

    /**
     * Tek bir kategori insert kodu
     */
    private function generateCategoryInsertCode($category): string
    {
        $parentMapping = $category->parent_id > 0 ? "\$this->categoryMapping[{$category->parent_id}] ?? null" : "null";

        $code = "\n        // Kategori: {$category->name_tr} (ID: {$category->id})\n";
        $code .= "        \$existing = ShopCategory::where('slug->tr', " . var_export($category->slug, true) . ")->first();\n";
        $code .= "        if (!\$existing) {\n";
        $code .= "            \$cat{$category->id} = ShopCategory::create([\n";
        $code .= "                'parent_id' => {$parentMapping},\n";
        $code .= "                'title' => json_encode(['tr' => " . var_export($category->name_tr, true) . "]),\n";
        $code .= "                'slug' => json_encode(['tr' => " . var_export($category->slug, true) . "]),\n";
        $code .= "                'description' => json_encode(['tr' => " . var_export($this->cleanHtml($category->body_tr ?? ''), true) . "]),\n";
        $code .= "                'image_url' => " . var_export($category->thumb, true) . ",\n";
        $code .= "                'level' => " . ($category->parent_id > 0 ? 2 : 1) . ",\n";
        $code .= "                'sort_order' => " . ($category->sorting ?? 0) . ",\n";
        $code .= "                'is_active' => true,\n";
        $code .= "                'show_in_menu' => true,\n";
        $code .= "            ]);\n";
        $code .= "            \$this->categoryMapping[{$category->id}] = \$cat{$category->id}->category_id;\n";
        $code .= "        } else {\n";
        $code .= "            \$this->categoryMapping[{$category->id}] = \$existing->category_id;\n";
        $code .= "        }\n";

        return $code;
    }

    /**
     * Ürün seeder'ları oluştur
     */
    private function generateProductSeeders()
    {
        $this->info('📦 Ürün seeder\'ları oluşturuluyor...');

        $sparePartsRootId = 50;

        // Seviye 2 kategorileri al
        $level2CategoryIds = $this->litefConnection
            ->table('mod_digishop_categories')
            ->where('parent_id', $sparePartsRootId)
            ->where('active', 1)
            ->pluck('id')
            ->toArray();

        // Seviye 3 kategorileri al
        $level3CategoryIds = $this->litefConnection
            ->table('mod_digishop_categories')
            ->whereIn('parent_id', $level2CategoryIds)
            ->where('active', 1)
            ->pluck('id')
            ->toArray();

        $categoryIds = array_merge([$sparePartsRootId], $level2CategoryIds, $level3CategoryIds);

        // Ürünleri çek
        $products = $this->litefConnection
            ->table('mod_digishop')
            ->whereIn('cid', $categoryIds)
            ->where('active', 1)
            ->get();

        $this->info("Toplam {$products->count()} ürün bulundu");

        // Ürünleri kategori bazında grupla
        $productsByCategory = $products->groupBy('cid');

        $bar = $this->output->createProgressBar($productsByCategory->count());
        $bar->start();

        foreach ($productsByCategory as $categoryId => $categoryProducts) {
            $this->generateProductSeederFile($categoryId, $categoryProducts);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    /**
     * Ürün seeder dosyası oluştur
     */
    private function generateProductSeederFile($categoryId, $products)
    {
        $categoryName = $this->litefConnection
            ->table('mod_digishop_categories')
            ->where('id', $categoryId)
            ->value('name_tr');

        $slug = Str::slug($categoryName);
        $className = 'LitefSpareParts_' . ucfirst(Str::camel($slug)) . '_Seeder';
        $filePath = $this->seederPath . $className . '.php';

        $code = $this->generateProductSeederCode($className, $categoryId, $products);

        File::put($filePath, $code);
        $this->stats['product_seeders']++;
    }

    /**
     * Ürün seeder kodu oluştur
     */
    private function generateProductSeederCode($className, $categoryId, $products): string
    {
        $code = "<?php\n\nnamespace Modules\Shop\Database\Seeders\LitefSpareParts;\n\n";
        $code .= "use Illuminate\Database\Seeder;\n";
        $code .= "use Modules\Shop\App\Models\ShopProduct;\n";
        $code .= "use Modules\Shop\App\Models\ShopCategory;\n";
        $code .= "use Modules\Shop\App\Models\ShopBrand;\n\n";
        $code .= "class {$className} extends Seeder\n{\n";
        $code .= "    public function run(): void\n    {\n";
        $code .= "        // Kategori ID mapping (Litef: {$categoryId})\n";
        $code .= "        \$category = ShopCategory::where('slug->tr', " . var_export($this->getCategorySlug($categoryId), true) . ")->first();\n";
        $code .= "        if (!\$category) {\n";
        $code .= "            \$this->command->warn('Kategori bulunamadı, ürünler atlanıyor');\n";
        $code .= "            return;\n";
        $code .= "        }\n\n";
        $code .= "        // Marka: İXTİF\n";
        $code .= "        \$brand = ShopBrand::where('slug->tr', 'ixtif')->first();\n\n";

        foreach ($products as $product) {
            $code .= $this->generateProductInsertCode($product);
        }

        $code .= "    }\n}\n";

        return $code;
    }

    /**
     * Tek bir ürün insert kodu
     */
    private function generateProductInsertCode($product): string
    {
        $sku = $this->generateSku($product);
        $photos = $this->collectProductPhotos($product);

        $code = "\n        // Ürün: {$product->title_tr}\n";
        $code .= "        \$product = ShopProduct::updateOrInsert(\n";
        $code .= "            ['sku' => " . var_export($sku, true) . "],\n";
        $code .= "            [\n";
        $code .= "                'category_id' => \$category->category_id,\n";
        $code .= "                'brand_id' => \$brand?->brand_id,\n";
        $code .= "                'model_number' => " . var_export($product->model ?: null, true) . ",\n";
        $code .= "                'title' => json_encode(['tr' => " . var_export($product->title_tr, true) . "]),\n";
        $code .= "                'slug' => json_encode(['tr' => " . var_export($product->slug, true) . "]),\n";
        $code .= "                'short_description' => json_encode(['tr' => " . var_export($this->cleanHtml($product->short_desc_tr ?? ''), true) . "]),\n";
        $code .= "                'body' => json_encode(['tr' => " . var_export($this->mergeBodyContent($product), true) . "]),\n";
        $code .= "                'product_type' => 'physical',\n";
        $code .= "                'condition' => 'new',\n";
        $code .= "                'price_on_request' => true,\n";
        $code .= "                'base_price' => 0.00,\n";
        $code .= "                'currency' => 'TRY',\n";
        $code .= "                'stock_tracking' => false,\n";
        $code .= "                'is_active' => " . ($product->active ? 'true' : 'false') . ",\n";
        $code .= "                'is_featured' => " . ($product->showcase ? 'true' : 'false') . ",\n";
        $code .= "                'published_at' => now(),\n";
        $code .= "            ]\n";
        $code .= "        );\n\n";

        // Fotoğraf ekleme kodu
        if (!empty($photos)) {
            $code .= "        // Fotoğrafları ekle\n";
            $code .= "        \$productModel = ShopProduct::where('sku', " . var_export($sku, true) . ")->first();\n";
            $code .= "        if (\$productModel && \$productModel->getMedia('featured_image')->isEmpty()) {\n";

            foreach ($photos as $index => $photo) {
                $targetPath = 'litef-spare-parts/' . $photo;
                $collection = ($index === 0) ? 'featured_image' : 'gallery';

                $code .= "            \$imagePath = storage_path('app/public/{$targetPath}');\n";
                $code .= "            if (file_exists(\$imagePath)) {\n";
                $code .= "                try {\n";
                $code .= "                    \$productModel->addMedia(\$imagePath)\n";
                $code .= "                        ->preservingOriginal()\n";
                $code .= "                        ->toMediaCollection('{$collection}', 'public');\n";
                $code .= "                } catch (\\Exception \$e) {\n";
                $code .= "                    // Fotoğraf eklenemedi\n";
                $code .= "                }\n";
                $code .= "            }\n\n";
            }

            $code .= "        }\n";
        }

        return $code;
    }

    /**
     * Tüm fotoğrafları kopyala
     */
    private function copyAllPhotos()
    {
        $this->info('📸 Fotoğraflar kopyalanıyor...');

        $sparePartsRootId = 50;

        // Seviye 2-3 kategorileri al
        $level2CategoryIds = $this->litefConnection
            ->table('mod_digishop_categories')
            ->where('parent_id', $sparePartsRootId)
            ->where('active', 1)
            ->pluck('id')
            ->toArray();

        $level3CategoryIds = $this->litefConnection
            ->table('mod_digishop_categories')
            ->whereIn('parent_id', $level2CategoryIds)
            ->where('active', 1)
            ->pluck('id')
            ->toArray();

        $categoryIds = array_merge([$sparePartsRootId], $level2CategoryIds, $level3CategoryIds);

        $products = $this->litefConnection
            ->table('mod_digishop')
            ->whereIn('cid', $categoryIds)
            ->where('active', 1)
            ->get();

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $photos = $this->collectProductPhotos($product);

            foreach ($photos as $photo) {
                $sourcePath = $this->litefImagePath . $photo;
                $targetPath = $this->targetImagePath . $photo;

                if (file_exists($sourcePath) && !file_exists($targetPath)) {
                    File::copy($sourcePath, $targetPath);
                    $this->stats['photos_copied']++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    /**
     * Master seeder oluştur
     */
    private function generateMasterSeeder()
    {
        $className = 'LitefSparePartsMasterSeeder';
        $filePath = $this->seederPath . $className . '.php';

        $code = "<?php\n\nnamespace Modules\Shop\Database\Seeders\LitefSpareParts;\n\n";
        $code .= "use Illuminate\Database\Seeder;\n\n";
        $code .= "class {$className} extends Seeder\n{\n";
        $code .= "    public function run(): void\n    {\n";
        $code .= "        \$this->call([\n";
        $code .= "            LitefSparePartsCategoriesSeeder::class,\n";

        // Ürün seeder'larını ekle
        $seederFiles = File::glob($this->seederPath . 'LitefSpareParts_*_Seeder.php');
        foreach ($seederFiles as $file) {
            $seederClass = basename($file, '.php');
            $code .= "            {$seederClass}::class,\n";
        }

        $code .= "        ]);\n";
        $code .= "    }\n}\n";

        File::put($filePath, $code);
        $this->info("✅ Master seeder oluşturuldu: {$className}");
    }

    // Helper methods
    private function getCategorySlug($categoryId): string
    {
        return $this->litefConnection
            ->table('mod_digishop_categories')
            ->where('id', $categoryId)
            ->value('slug') ?? 'unknown';
    }

    private function generateSku($product): string
    {
        if (!empty($product->product_code)) {
            return 'LITEF-' . strtoupper($product->product_code);
        }
        if (!empty($product->model)) {
            return 'LITEF-' . strtoupper(Str::slug($product->model, ''));
        }
        return 'LITEF-' . $product->id;
    }

    private function collectProductPhotos($product): array
    {
        $photos = [];
        if (!empty($product->thumb)) $photos[] = $product->thumb;
        for ($i = 1; $i <= 6; $i++) {
            $field = "thumb_{$i}";
            if (!empty($product->$field)) $photos[] = $product->$field;
        }
        return array_unique($photos);
    }

    private function mergeBodyContent($product): string
    {
        $parts = [];
        if (!empty($product->body_tr)) $parts[] = $this->cleanHtml($product->body_tr);
        if (!empty($product->body2_tr)) $parts[] = $this->cleanHtml($product->body2_tr);
        return implode("\n\n", $parts);
    }

    private function cleanHtml(?string $html): string
    {
        if (empty($html)) return '';
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = str_replace(['&lt;', '&gt;', '&amp;'], ['<', '>', '&'], $html);
        return trim($html);
    }

    private function displaySummary()
    {
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 SEEDER GENERATOR SONUÇLARI');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $this->line("✅ Kategori Seeder: <fg=green>{$this->stats['category_seeders']}</>");
        $this->line("✅ Ürün Seeder: <fg=green>{$this->stats['product_seeders']}</>");
        $this->line("📸 Kopyalanan Fotoğraf: <fg=cyan>{$this->stats['photos_copied']}</>");

        $this->newLine();
        $this->info("📂 Seeder konumu: {$this->seederPath}");
        $this->info("📂 Fotoğraf konumu: {$this->targetImagePath}");

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $this->warn('⚠️  ÖNEMLİ: Seeder\'ları çalıştırmadan önce:');
        $this->line('1. Fotoğraf klasörünü yedekleyin: storage/app/public/litef-spare-parts/');
        $this->line('2. .gitignore\'a ekleyin: /storage/app/public/litef-spare-parts/');
        $this->line('3. Seeder\'ları çalıştırın: php artisan db:seed --class=Modules\\\\Shop\\\\Database\\\\Seeders\\\\LitefSpareParts\\\\LitefSparePartsMasterSeeder');
    }
}
