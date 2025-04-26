<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class BlockWidgetSeeder extends Seeder
{
    // Çalıştırma izleme anahtarı
    private static $runKey = 'block_widget_seeder_executed';

    public function run()
    {
        // Tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, BlockWidgetSeeder atlanıyor.');
            }
            return;
        }

        // Cache kontrolü
        $cacheKey = self::$runKey . '_' . Config::get('database.default');
        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            // "Modül Bileşenleri" kategorisinin varlığını kontrol et
            $modulesCategory = WidgetCategory::where('slug', 'modul-bilesenleri')
                ->orWhere('title', 'Modül Bileşenleri')
                ->first();
            
            if (!$modulesCategory) {
                
                try {
                    $modulesCategory = new WidgetCategory([
                        'title' => 'Modül Bileşenleri',
                        'slug' => 'modul-bilesenleri',
                        'description' => 'Sistem modüllerine ait bileşenler',
                        'icon' => 'fa-cubes',
                        'order' => 1,
                        'is_active' => true,
                        'parent_id' => null,
                        'has_subcategories' => true
                    ]);
                    
                    $modulesCategory->save();
                    
                    if (!$modulesCategory->widget_category_id) {
                        throw new \Exception("Kategori ID oluşturulamadı");
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Modül Bileşenleri kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                }
            } else {
            }
            
            // Blok kategorileri oluştur
            $this->createBlockWidgets();

            // Seeder'ın çalıştırıldığını işaretle (10 dakika süreyle cache'de tut)
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('BlockWidgetSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if ($this->command) {
                $this->command->error('BlockWidgetSeeder hatası: ' . $e->getMessage());
            }
        }
    }

    private function createBlockWidgets()
    {
        // Blok dizini
        $blockBasePath = base_path('Modules/WidgetManagement/resources/views/blocks');
        
        // Kategori eşleştirmeleri
        $categoryMappings = [
            'cards' => 'kartlar',
            'content' => 'icerikler',
            'features' => 'ozellikler',
            'form' => 'formlar',
            'hero' => 'herolar',
            'layout' => 'yerlesimler',
            'media' => 'medya',
            'testimonials' => 'referanslar',
            'slider' => 'sliderlar'
        ];
        
        // Blok açıklamaları
        $blockDescriptions = [
            'cards/basic' => 'Temel kart bileşeni',
            'cards/grid' => 'Grid şeklinde düzenlenmiş kartlar',
            'content/hero' => 'Hero içerik alanı',
            'content/text' => 'Temel metin bileşeni',
            'features/basic' => 'Özellik listeleme bileşeni',
            'form/contact-form' => 'İletişim formu',
            'hero/simple' => 'Basit hero bileşeni',
            'layout/footer' => 'Sayfa alt kısmı',
            'layout/header' => 'Sayfa üst kısmı',
            'layout/one-column' => 'Tek sütunlu yerleşim',
            'layout/two-columns' => 'İki sütunlu yerleşim',
            'layout/three-columns' => 'Üç sütunlu yerleşim',
            'media/image' => 'Görsel bileşeni',
            'testimonials/basic' => 'Müşteri yorumu bileşeni'
        ];
        
        if (File::isDirectory($blockBasePath)) {
            $categoryFolders = File::directories($blockBasePath);
            
            foreach ($categoryFolders as $categoryFolder) {
                $categoryName = basename($categoryFolder);
                
                // modules klasörünü atla
                if ($categoryName === 'modules') {
                    continue;
                }
                
                // Kategori eşleştirmesini bul
                $categorySlug = $categoryMappings[$categoryName] ?? Str::slug($categoryName);
                
                // Kategoriyi bul, yoksa oluştur
                $category = WidgetCategory::where('slug', $categorySlug)->first();
                
                if (!$category) {
                    try {
                        // Kategori adını düzgün formatta oluştur
                        $categoryTitle = ucfirst(str_replace(['-', '_'], ' ', $categoryName));
                        
                        // Kategori oluştur
                        $category = new WidgetCategory([
                            'title' => $categoryTitle,
                            'slug' => $categorySlug,
                            'description' => $categoryTitle . ' bileşenleri',
                            'icon' => 'fa-puzzle-piece',
                            'order' => 999,
                            'is_active' => true,
                            'parent_id' => null
                        ]);
                        
                        $category->save();
                        
                        // Kategori ID'sini doğrula
                        if (!$category->widget_category_id) {
                            throw new \Exception("Kategori ID oluşturulamadı");
                        }
                        
                    } catch (\Exception $e) {
                        Log::error("Kategori oluşturulamadı: $categorySlug. Hata: " . $e->getMessage());
                        continue;
                    }
                    
                    if (!$category || !$category->widget_category_id) {
                        Log::error("Kategori oluşturulamadı: $categorySlug");
                        continue;
                    }
                }
                
                // Bu kategorideki tüm bileşenleri bul ve alt klasörleri de tara
                $blockPaths = $this->getBlocksInFolder($categoryName);
                
                foreach ($blockPaths as $blockPath) {
                    $fullPath = "$categoryName/$blockPath";
                    $blockName = $this->getBlockName($blockPath);
                    $blockSlug = Str::slug($categoryName . '-' . $blockPath);
                    
                    // Widget'ın zaten var olup olmadığını kontrol et
                    $existingWidget = Widget::where('slug', $blockSlug)->first();
                    
                    if (!$existingWidget) {
                        try {
                            // Kategori ID'sini kontrol et
                            if (!$category) {
                                continue;
                            }
                            
                            if (!$category->widget_category_id) {
                                continue;
                            }
                            
                            $widget = new Widget([
                                'widget_category_id' => $category->widget_category_id,
                                'name' => $blockName,
                                'slug' => $blockSlug,
                                'description' => $blockDescriptions[$fullPath] ?? "$blockName bileşeni",
                                'type' => 'file',
                                'file_path' => $fullPath,
                                'has_items' => false,
                                'is_active' => true,
                                'is_core' => true,
                                'settings_schema' => [
                                    [
                                        'name' => 'title',
                                        'label' => 'Başlık',
                                        'type' => 'text',
                                        'required' => true,
                                        'system' => true
                                    ],
                                    [
                                        'name' => 'unique_id',
                                        'label' => 'Benzersiz ID',
                                        'type' => 'text',
                                        'required' => false,
                                        'system' => true,
                                        'hidden' => true
                                    ]
                                ]
                            ]);
                            
                            $widget->save();
                            
                        } catch (\Exception $e) {
                            Log::error("Widget oluşturulamadı: $blockName. Hata: " . $e->getMessage());
                        }
                    } else {
                        // Eğer widget varsa ama kategori ID'si yoksa güncelle
                        if (!$existingWidget->widget_category_id && $category && $category->widget_category_id) {
                            $existingWidget->widget_category_id = $category->widget_category_id;
                            $existingWidget->save();
                            Log::info("Widget güncellendi: $blockName (Kategori ID eklendi: {$category->widget_category_id})");
                        } else {
                            Log::info("Widget zaten mevcut: $blockName (slug: $blockSlug)");
                        }
                    }
                }
            }
        } else {
            Log::warning("Blok klasörü bulunamadı: $blockBasePath");
        }
    }
    
    private function getBlocksInFolder($folder)
    {
        $blocks = [];
        $path = base_path('Modules/WidgetManagement/resources/views/blocks/' . $folder);
        
        if (File::isDirectory($path)) {
            $files = File::files($path);
            
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                if (Str::endsWith($fileName, '.blade.php')) {
                    $baseName = Str::replaceLast('.blade.php', '', $fileName);
                    $blocks[] = $baseName;
                }
            }
            
            // Alt klasörleri kontrol et
            $directories = File::directories($path);
            foreach ($directories as $directory) {
                $subFolder = basename($directory);
                
                // Alt klasöre ait view.blade.php dosyası var mı kontrol et
                $viewFile = $directory . '/view.blade.php';
                if (File::exists($viewFile)) {
                    $blocks[] = $subFolder . '/view';
                } else {
                    // Alt klasörlerdeki tüm blade dosyalarını kontrol et
                    $subFiles = File::files($directory);
                    foreach ($subFiles as $file) {
                        $fileName = $file->getFilename();
                        if (Str::endsWith($fileName, '.blade.php')) {
                            $baseName = Str::replaceLast('.blade.php', '', $fileName);
                            $blocks[] = $subFolder . '/' . $baseName;
                        }
                    }
                }
            }
        } else {
            Log::warning("Klasör bulunamadı: $path");
        }
        
        return $blocks;
    }
    
    private function getBlockName($path)
    {
        $parts = explode('/', $path);
        
        if (count($parts) > 1 && end($parts) === 'view') {
            // Eğer alt klasöründe view.blade.php varsa, alt klasör adını kullan
            $blockName = $parts[count($parts) - 2];
        } else {
            // Diğer durumda son parçayı kullan
            $blockName = end($parts);
        }
        
        // İlk harfi büyük yap ve tire/alt çizgi karakterlerini boşluğa dönüştür
        return ucfirst(str_replace(['-', '_'], ' ', $blockName));
    }
}