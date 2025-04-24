<?php

namespace Modules\WidgetManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\App\Models\Widget;
use Modules\WidgetManagement\App\Models\WidgetCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FileWidgetSeeder extends Seeder
{
    public function run()
    {
        // Eğer tenant contextindeysek, bu seeder'ı çalıştırma
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, FileWidgetSeeder atlanıyor.');
            }
            Log::info('Tenant contextinde çalışıyor, FileWidgetSeeder atlanıyor. Tenant ID: ' . tenant('id'));
            return;
        }
        
        Log::info('FileWidgetSeeder merkezi veritabanında çalışıyor...');
        
        try {
            // Önce modül widgetlarını oluştur
            $this->createModuleWidgets();
            
            // Sonra diğer normal widgetları oluştur
            $this->createBlockWidgets();
        } catch (\Exception $e) {
            Log::error('FileWidgetSeeder genel hata: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if ($this->command) {
                $this->command->error('FileWidgetSeeder genel hata: ' . $e->getMessage());
            }
        }
    }
    
    private function createModuleWidgets()
    {
        // Modül kategorilerini ID'leri ile al
        $pageCategory = DB::table('widget_categories')
            ->where('slug', 'sayfa-modulu')
            ->first();
            
        $portfolioCategory = DB::table('widget_categories')
            ->where('slug', 'portfolio-modulu')
            ->first();
        
        if (!$pageCategory) {
            Log::warning('Sayfa modülü kategorisi bulunamadı, atlanıyor...');
        } else {
            // Son Eklenen Sayfalar Widget'ı
            if (!Widget::where('slug', 'son-eklenen-sayfalar')->exists()) {
                Widget::create([
                    'widget_category_id' => $pageCategory->widget_category_id,
                    'name' => 'Son Eklenen Sayfalar',
                    'slug' => 'son-eklenen-sayfalar',
                    'description' => 'Son eklenen sayfaları listeler',
                    'type' => 'file',
                    'file_path' => 'modules/page/recent/view',
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
                        ],
                        [
                            'name' => 'show_dates',
                            'label' => 'Tarihleri Göster',
                            'type' => 'checkbox',
                            'required' => false
                        ],
                        [
                            'name' => 'limit',
                            'label' => 'Gösterilecek Sayfa Sayısı',
                            'type' => 'number',
                            'required' => false
                        ]
                    ]
                ]);
                
                Log::info('Son Eklenen Sayfalar widget\'ı başarıyla oluşturuldu.');
            } else {
                Log::info('Son Eklenen Sayfalar widget\'ı zaten mevcut, atlanıyor...');
            }
            
            // Ana Sayfa İçeriği Widget'ı
            if (!Widget::where('slug', 'anasayfa-icerik')->exists()) {
                Widget::create([
                    'widget_category_id' => $pageCategory->widget_category_id,
                    'name' => 'Ana Sayfa İçeriği',
                    'slug' => 'anasayfa-icerik',
                    'description' => 'Ana sayfa olarak işaretlenmiş sayfanın içeriğini gösterir',
                    'type' => 'file',
                    'file_path' => 'modules/page/home/view',
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
                        ],
                        [
                            'name' => 'show_title',
                            'label' => 'Sayfa Başlığını Göster',
                            'type' => 'checkbox',
                            'required' => false
                        ]
                    ]
                ]);
                
                Log::info('Ana Sayfa İçeriği widget\'ı başarıyla oluşturuldu.');
            } else {
                Log::info('Ana Sayfa İçeriği widget\'ı zaten mevcut, atlanıyor...');
            }
        }
        
        if (!$portfolioCategory) {
            Log::warning('Portfolio modülü kategorisi bulunamadı, atlanıyor...');
        } else {
            // Portfolio Liste Widget'ı
            if (!Widget::where('slug', 'portfolio-liste')->exists()) {
                Widget::create([
                    'widget_category_id' => $portfolioCategory->widget_category_id,
                    'name' => 'Portfolio Liste',
                    'slug' => 'portfolio-liste',
                    'description' => 'Projeleri listeler',
                    'type' => 'file',
                    'file_path' => 'modules/portfolio/list/view',
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
                        ],
                        [
                            'name' => 'show_description',
                            'label' => 'Açıklamayı Göster',
                            'type' => 'checkbox',
                            'required' => false
                        ],
                        [
                            'name' => 'description',
                            'label' => 'Açıklama Metni',
                            'type' => 'textarea',
                            'required' => false
                        ],
                        [
                            'name' => 'show_all_link',
                            'label' => 'Tümünü Göster Bağlantısı',
                            'type' => 'checkbox',
                            'required' => false
                        ],
                        [
                            'name' => 'all_link_text',
                            'label' => 'Bağlantı Metni',
                            'type' => 'text',
                            'required' => false
                        ],
                        [
                            'name' => 'limit',
                            'label' => 'Gösterilecek Proje Sayısı',
                            'type' => 'number',
                            'required' => false
                        ],
                        [
                            'name' => 'order_direction',
                            'label' => 'Sıralama Yönü',
                            'type' => 'select',
                            'options' => [
                                'desc' => 'Yeniden Eskiye',
                                'asc' => 'Eskiden Yeniye'
                            ],
                            'required' => false
                        ],
                        [
                            'name' => 'category_id',
                            'label' => 'Kategori Filtresi',
                            'type' => 'select',
                            'options' => [],
                            'required' => false
                        ]
                    ]
                ]);
                
                Log::info('Portfolio Liste widget\'ı başarıyla oluşturuldu.');
            } else {
                Log::info('Portfolio Liste widget\'ı zaten mevcut, atlanıyor...');
            }
            
            // Portfolio Detay Widget'ı
            if (!Widget::where('slug', 'portfolio-detay')->exists()) {
                Widget::create([
                    'widget_category_id' => $portfolioCategory->widget_category_id,
                    'name' => 'Portfolio Detay',
                    'slug' => 'portfolio-detay',
                    'description' => 'Seçilen bir projenin detaylarını gösterir',
                    'type' => 'file',
                    'file_path' => 'modules/portfolio/detail/view',
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
                        ],
                        [
                            'name' => 'project_id',
                            'label' => 'Proje ID',
                            'type' => 'number',
                            'required' => false
                        ],
                        [
                            'name' => 'project_slug',
                            'label' => 'Proje Slug',
                            'type' => 'text',
                            'required' => false
                        ],
                        [
                            'name' => 'show_date',
                            'label' => 'Tarihi Göster',
                            'type' => 'checkbox',
                            'required' => false
                        ],
                        [
                            'name' => 'show_category',
                            'label' => 'Kategoriyi Göster',
                            'type' => 'checkbox',
                            'required' => false
                        ],
                        [
                            'name' => 'show_cover',
                            'label' => 'Kapak Resmini Göster',
                            'type' => 'checkbox',
                            'required' => false
                        ],
                        [
                            'name' => 'show_gallery',
                            'label' => 'Galeriyi Göster',
                            'type' => 'checkbox',
                            'required' => false
                        ],
                        [
                            'name' => 'show_related',
                            'label' => 'Benzer Projeleri Göster',
                            'type' => 'checkbox',
                            'required' => false
                        ]
                    ]
                ]);
                
                Log::info('Portfolio Detay widget\'ı başarıyla oluşturuldu.');
            } else {
                Log::info('Portfolio Detay widget\'ı zaten mevcut, atlanıyor...');
            }
        }
    }
    
    private function createBlockWidgets()
    {
        // Kategori haritalaması
        $categoryMappings = [
            'cards' => 'kart-bilesenleri',
            'content' => 'icerik-bilesenleri',
            'features' => 'ozellik-bilesenleri',
            'form' => 'form-bilesenleri',
            'hero' => 'hero-bilesenleri',
            'layout' => 'yerlesim-bilesenleri',
            'media' => 'medya-bilesenleri',
            'testimonials' => 'referans-bilesenleri'
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
        
        foreach ($categoryMappings as $folder => $categorySlug) {
            // Kategoriyi bul
            $category = WidgetCategory::where('slug', $categorySlug)->first();
            
            if (!$category) {
                Log::warning("Kategori bulunamadı: $categorySlug, atlanıyor...");
                continue;
            }
            
            // Bu klasöre ait blokları al
            $blocks = $this->getBlocksInFolder($folder);
            Log::info("$folder klasöründe " . count($blocks) . " blok bulundu.");
            
            foreach ($blocks as $blockPath) {
                $blockName = $this->getBlockName($blockPath);
                $blockSlug = Str::slug($blockName);
                
                // Eğer bu dosya için önceden oluşturulmuş bir widget yoksa
                if (!Widget::where('slug', $blockSlug)->exists()) {
                    try {
                        Widget::create([
                            'widget_category_id' => $category->widget_category_id,
                            'name' => $blockName,
                            'slug' => $blockSlug,
                            'description' => $blockDescriptions[$blockPath] ?? $blockName,
                            'type' => 'file',
                            'file_path' => $blockPath,
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
                        
                        Log::info("Widget başarıyla oluşturuldu: $blockName ($blockPath)");
                    } catch (\Exception $e) {
                        Log::error("Widget oluşturma hatası ($blockPath): " . $e->getMessage());
                    }
                } else {
                    Log::info("Widget zaten mevcut, atlanıyor: $blockName ($blockPath)");
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
            
            // Alt klasörleri de kontrol et
            $directories = File::directories($path);
            foreach ($directories as $directory) {
                $subFolder = basename($directory);
                $subPath = $folder . '/' . $subFolder;
                
                // Özyinelemeli olarak alt klasörleri tara
                $this->scanSubFolders($subPath, $blocks);
            }
        } else {
            Log::warning("Klasör bulunamadı: $path");
        }
        
        return $blocks;
    }
    
    private function scanSubFolders($folder, &$blocks)
    {
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
            
            // Alt klasörleri de kontrol et
            $directories = File::directories($path);
            foreach ($directories as $directory) {
                $subFolder = basename($directory);
                $subPath = $folder . '/' . $subFolder;
                
                // Özyinelemeli olarak devam et
                $this->scanSubFolders($subPath, $blocks);
            }
        }
    }
    
    private function getBlockName($path)
    {
        $pathParts = explode('/', $path);
        $lastPart = end($pathParts);
        
        // İlk harfi büyük yap ve tire/alt çizgi karakterlerini boşluğa dönüştür
        return ucfirst(str_replace(['-', '_'], ' ', $lastPart));
    }
}