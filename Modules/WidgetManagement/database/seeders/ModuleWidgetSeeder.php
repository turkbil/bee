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

class ModuleWidgetSeeder extends Seeder
{
    // Çalıştırma izleme anahtarı
    private static $runKey = 'module_widget_seeder_executed';
    public function run()
    {
        // Tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, ModuleWidgetSeeder atlanıyor.');
            }
            Log::info('Tenant contextinde çalışıyor, ModuleWidgetSeeder atlanıyor. Tenant ID: ' . tenant('id'));
            return;
        }
        
        // Seeder'ın daha önce çalıştırılıp çalıştırılmadığını kontrol et
        $cacheKey = self::$runKey . '_' . Config::get('database.default');
        if (Cache::has($cacheKey)) {
            Log::info('ModuleWidgetSeeder zaten çalıştırılmış, atlanıyor...');
            return;
        }
        
        Log::info('ModuleWidgetSeeder merkezi veritabanında çalışıyor...');
        
        try {
            // Ana modul kategorisi - önce kontrol et, yoksa oluştur
            $mainModuleCategory = WidgetCategory::where('slug', 'modul-bilesenleri')->first();
            
            if (!$mainModuleCategory) {
                // Kategori bulunamazsa, veritabanı işlemlerinin tamamlanması için kısa bir bekleme ekleyelim
                Log::warning("Ana modül kategorisi (modul-bilesenleri) bulunamadı, oluşturuluyor...");
                sleep(1); // 1 saniye bekle
                
                // Tekrar deneyelim
                $mainModuleCategory = WidgetCategory::where('slug', 'modul-bilesenleri')->first();
                
                if (!$mainModuleCategory) {
                    // Kategori oluştur
                    try {
                        $mainModuleCategory = WidgetCategory::create([
                            'title' => 'Modül Bileşenleri',
                            'slug' => 'modul-bilesenleri',
                            'description' => 'Sistem modüllerine ait bileşenler',
                            'icon' => 'fa-cubes',
                            'order' => 1,
                            'is_active' => true,
                            'parent_id' => null,
                            'has_subcategories' => true
                        ]);
                        
                        Log::info("Ana modül kategorisi oluşturuldu: Modül Bileşenleri (slug: modul-bilesenleri)");
                    } catch (\Exception $e) {
                        Log::error("Ana modül kategorisi (modul-bilesenleri) oluşturulamadı. Hata: " . $e->getMessage());
                        return;
                    }
                }
                
                if (!$mainModuleCategory) {
                    Log::error("Ana modül kategorisi (modul-bilesenleri) bulunamadı ve oluşturulamadı, widget oluşturulamıyor.");
                    return;
                }
            }
            
            // Modül kategorilerini oluştur
            $this->createModuleWidgets($mainModuleCategory);
            
            Log::info('Modül bileşenleri başarıyla oluşturuldu.');
            
            // Seeder'ın çalıştırıldığını işaretle (10 dakika süreyle cache'de tut)
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('ModuleWidgetSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if ($this->command) {
                $this->command->error('ModuleWidgetSeeder hatası: ' . $e->getMessage());
            }
        }
    }
    
    private function createModuleWidgets($mainModuleCategory)
    {
        // Modül dizini
        $moduleBasePath = base_path('Modules/WidgetManagement/resources/views/blocks/modules');
        
        // Modül dizini varsa
        if (File::isDirectory($moduleBasePath)) {
            $moduleFolders = File::directories($moduleBasePath);
            
            foreach ($moduleFolders as $moduleFolder) {
                $moduleName = basename($moduleFolder);
                $moduleSlug = Str::slug($moduleName) . '-modulu';
                
                // Modül kategorisini bul - yoksa oluştur
                $moduleCategory = WidgetCategory::where('slug', $moduleSlug)->first();
                
                if (!$moduleCategory) {
                    Log::warning("Modül kategorisi bulunamadı: $moduleSlug, yeni kategori oluşturuluyor...");
                    
                    // Yeni modül kategorisi oluştur
                    try {
                        $moduleCategory = WidgetCategory::create([
                            'title' => ucfirst($moduleName) . ' Modülü',
                            'slug' => $moduleSlug,
                            'description' => ucfirst($moduleName) . ' modülüne ait bileşenler',
                            'icon' => 'fa-puzzle-piece',
                            'order' => 999,
                            'is_active' => true,
                            'parent_id' => $mainModuleCategory->widget_category_id,
                            'has_subcategories' => false
                        ]);
                        
                        Log::info("Yeni modül kategorisi oluşturuldu: {$moduleCategory->title}");
                    } catch (\Exception $e) {
                        Log::error("Modül kategorisi oluşturulamadı: $moduleSlug. Hata: " . $e->getMessage());
                        continue;
                    }
                    
                    if (!$moduleCategory) {
                        Log::error("Modül kategorisi oluşturulamadı: $moduleSlug");
                        continue;
                    }
                }
                
                // Bu modüle ait widget dizinleri
                $widgetFolders = File::directories($moduleFolder);
                
                foreach ($widgetFolders as $widgetFolder) {
                    $widgetName = basename($widgetFolder);
                    $widgetViewPath = $widgetFolder . '/view.blade.php';
                    
                    // Eğer view.blade.php dosyası varsa
                    if (File::exists($widgetViewPath)) {
                        $widgetTitle = ucfirst(str_replace(['-', '_'], ' ', $widgetName));
                        $widgetSlug = Str::slug($moduleName . '-' . $widgetName);
                        
                        // Widget'ın zaten var olup olmadığını kontrol et
                        $existingWidget = Widget::where('slug', $widgetSlug)->first();
                        
                        if (!$existingWidget) {
                            Widget::create([
                                'widget_category_id' => $moduleCategory->widget_category_id,
                                'name' => $widgetTitle,
                                'slug' => $widgetSlug,
                                'description' => $widgetTitle . ' modül bileşeni',
                                'type' => 'module',
                                'file_path' => 'modules/' . $moduleName . '/' . $widgetName . '/view',
                                'has_items' => false,
                                'is_active' => true,
                                'is_core' => true,
                                'settings_schema' => $this->getModuleWidgetSettings($moduleName, $widgetName)
                            ]);
                            
                            Log::info("Widget oluşturuldu: $widgetTitle (path: modules/{$moduleName}/{$widgetName}/view)");
                        } else {
                            // Varolan widget'ı güncelle - tip yanlışsa düzelt
                            $existingWidget->update([
                                'type' => 'module',
                                'file_path' => 'modules/' . $moduleName . '/' . $widgetName . '/view',
                                'widget_category_id' => $moduleCategory->widget_category_id
                            ]);
                            
                            Log::info("Widget güncellendi: $widgetTitle (slug: $widgetSlug)");
                        }
                    }
                }
            }
        } else {
            Log::warning("Modül klasörü bulunamadı: $moduleBasePath");
        }
    }
    
    private function getModuleWidgetSettings($moduleName, $widgetName)
    {
        // Temel ayarlar
        $settings = [
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
        ];
        
        // Modül ve widget tipine göre özel ayarlar ekle
        if ($moduleName == 'page') {
            if ($widgetName == 'recent') {
                $settings[] = [
                    'name' => 'show_dates',
                    'label' => 'Tarihleri Göster',
                    'type' => 'checkbox',
                    'required' => false
                ];
                $settings[] = [
                    'name' => 'limit',
                    'label' => 'Gösterilecek Sayfa Sayısı',
                    'type' => 'number',
                    'required' => false,
                    'default' => 5
                ];
            } elseif ($widgetName == 'home') {
                $settings[] = [
                    'name' => 'show_title',
                    'label' => 'Sayfa Başlığını Göster',
                    'type' => 'checkbox',
                    'required' => false
                ];
            }
        } elseif ($moduleName == 'portfolio') {
            if ($widgetName == 'list') {
                $settings[] = [
                    'name' => 'show_description',
                    'label' => 'Açıklamayı Göster',
                    'type' => 'checkbox',
                    'required' => false
                ];
                $settings[] = [
                    'name' => 'description',
                    'label' => 'Açıklama Metni',
                    'type' => 'textarea',
                    'required' => false
                ];
                $settings[] = [
                    'name' => 'show_all_link',
                    'label' => 'Tümünü Göster Bağlantısı',
                    'type' => 'checkbox',
                    'required' => false
                ];
                $settings[] = [
                    'name' => 'limit',
                    'label' => 'Gösterilecek Proje Sayısı',
                    'type' => 'number',
                    'required' => false,
                    'default' => 6
                ];
            } elseif ($widgetName == 'detail') {
                $settings[] = [
                    'name' => 'project_id',
                    'label' => 'Proje ID',
                    'type' => 'number',
                    'required' => false
                ];
                $settings[] = [
                    'name' => 'show_gallery',
                    'label' => 'Galeriyi Göster',
                    'type' => 'checkbox',
                    'required' => false,
                    'default' => true
                ];
            }
        }
        
        return $settings;
    }
}