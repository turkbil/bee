<?php

namespace Modules\WidgetManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\App\Models\Widget;
use Modules\WidgetManagement\App\Models\WidgetCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class FileWidgetSeeder extends Seeder
{
    public function run()
    {
        // Eğer tenant contextindeysek, bu seeder'ı çalıştırma
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, FileWidgetSeeder atlanıyor.');
            }
            return;
        }
        
        // Kategori haritalaması
        $categoryMappings = [
            'cards' => 'Kart',
            'content' => 'İçerik',
            'features' => 'Özellik',
            'form' => 'Form',
            'hero' => 'Hero',
            'layout' => 'Yerleşim',
            'media' => 'Medya',
            'testimonials' => 'Referans'
        ];
        
        // Blok tiplerinin açıklamaları
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
        
        // Önce tüm klasörleri oluştur (kategoriler)
        foreach ($categoryMappings as $folder => $categoryName) {
            $category = WidgetCategory::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'title' => $categoryName,
                    'description' => $categoryName . ' için şablonlar',
                    'order' => array_search($folder, array_keys($categoryMappings)) + 1,
                    'is_active' => true
                ]
            );
            
            // Şimdi bu klasöre ait dosyaları kontrol et
            $blocks = $this->getBlocksInFolder($folder);
            
            foreach ($blocks as $blockPath) {
                $blockName = $this->getBlockName($blockPath);
                $blockSlug = Str::slug($blockName);
                $filePath = $blockPath;
                
                // Eğer bu dosya için önceden oluşturulmuş bir widget yoksa
                if (!Widget::where('slug', $blockSlug)->exists()) {
                    Widget::create([
                        'widget_category_id' => $category->widget_category_id,
                        'name' => $blockName,
                        'slug' => $blockSlug,
                        'description' => $blockDescriptions[$blockPath] ?? $blockName,
                        'type' => 'file',
                        'file_path' => $filePath,
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
                    
                    if ($this->command) {
                        $this->command->info('Widget oluşturuldu: ' . $blockName);
                    }
                }
            }
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
                    $blocks[] = $folder . '/' . $baseName;
                }
            }
        }
        
        return $blocks;
    }
    
    private function getBlockName($path)
    {
        $pathParts = explode('/', $path);
        $lastPart = end($pathParts);
        
        // İlk harfi büyük yap ve tire/alt çizgi karakterlerini boşluğa dönüştür
        return ucfirst(str_replace(['-', '_'], ' ', $lastPart));
    }
}