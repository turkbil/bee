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
        
        // Cache'i temizle ve her zaman çalıştır
        $cacheKey = self::$runKey . '_' . Config::get('database.default');
        Cache::forget($cacheKey);
        Log::info('ModuleWidgetSeeder cache temizlendi: ' . $cacheKey);
        
        Log::info('ModuleWidgetSeeder merkezi veritabanında çalışıyor...');
        
        try {
            // Önce tüm olası slug değerlerini kontrol et
            $mainModuleCategory = WidgetCategory::where('slug', 'modul-bilesenleri')->orWhere('slug', 'moduel-bilesenleri')->first();
            
            if (!$mainModuleCategory) {
                Log::warning("Ana modül kategorisi bulunamadı, oluşturuluyor...");
                
                try {
                    // Önce yeni bir kategori nesnesi oluştur
                    $mainModuleCategory = new WidgetCategory([
                        'title' => 'Modül Bileşenleri',
                        'slug' => 'modul-bilesenleri',
                        'description' => 'Sistem modüllerine ait bileşenler',
                        'icon' => 'fa-cubes',
                        'order' => 1,
                        'is_active' => true,
                        'parent_id' => null
                    ]);
                    
                    // Kaydet
                    $mainModuleCategory->save();
                    
                    // Kategori ID'sini doğrula
                    if (!$mainModuleCategory->widget_category_id) {
                        throw new \Exception("Kategori ID oluşturulamadı");
                    }
                    
                    Log::info("Ana modül kategorisi oluşturuldu: Modül Bileşenleri (slug: {$mainModuleCategory->slug}) (ID: {$mainModuleCategory->widget_category_id})");
                } catch (\Exception $e) {
                    Log::error("Ana modül kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                    return;
                }
            } else {
                Log::info("Ana modül kategorisi bulundu: {$mainModuleCategory->title} (slug: {$mainModuleCategory->slug}) (ID: {$mainModuleCategory->widget_category_id})");
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
        // Önce tüm mevcut modül kategorilerini temizle
        try {
            $existingCategories = WidgetCategory::where('parent_id', $mainModuleCategory->widget_category_id)->get();
            
            if ($existingCategories->count() > 0) {
                Log::info("Mevcut {$existingCategories->count()} modül kategorisi temizleniyor...");
                
                foreach ($existingCategories as $category) {
                    // Bu kategoriye ait widget'ları sil
                    $widgets = Widget::where('widget_category_id', $category->widget_category_id)->get();
                    
                    foreach ($widgets as $widget) {
                        $widget->delete();
                    }
                    
                    // Kategoriyi sil
                    $category->delete();
                    Log::info("Kategori silindi: {$category->title} (ID: {$category->widget_category_id})");
                }
            }
        } catch (\Exception $e) {
            Log::error("Mevcut kategoriler temizlenirken hata oluştu: " . $e->getMessage());
        }
        
        // Modül dizini
        $moduleBasePath = base_path('Modules/WidgetManagement/resources/views/blocks/modules');
        
        // Modül dizini varsa
        if (File::isDirectory($moduleBasePath)) {
            $moduleFolders = File::directories($moduleBasePath);
            $processedModules = [];
            
            foreach ($moduleFolders as $moduleFolder) {
                $moduleName = basename($moduleFolder);
                
                // Aynı modülü birden fazla işlemeyi önle
                if (in_array($moduleName, $processedModules)) {
                    Log::warning("Modül '{$moduleName}' zaten işlendi, atlanıyor.");
                    continue;
                }
                
                $processedModules[] = $moduleName;
                $moduleSlug = Str::slug($moduleName) . '-modulu';
                
                // Modül kategorisini oluştur
                try {
                    $moduleCategory = new WidgetCategory([
                        'title' => ucfirst($moduleName) . ' Modülü',
                        'slug' => $moduleSlug,
                        'description' => ucfirst($moduleName) . ' modülüne ait bileşenler',
                        'icon' => 'fa-puzzle-piece',
                        'order' => 999,
                        'is_active' => true,
                        'parent_id' => $mainModuleCategory->widget_category_id
                    ]);
                    
                    $moduleCategory->save();
                    
                    if (!$moduleCategory->widget_category_id) {
                        throw new \Exception("Kategori ID oluşturulamadı");
                    }
                    
                    Log::info("Yeni modül kategorisi oluşturuldu: {$moduleCategory->title} (ID: {$moduleCategory->widget_category_id})");
                } catch (\Exception $e) {
                    Log::error("Modül kategorisi oluşturulamadı: $moduleSlug. Hata: " . $e->getMessage());
                    continue;
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