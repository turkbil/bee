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
            return;
        }
        
        // Cache kontrolü
        $cacheKey = self::$runKey . '_' . Config::get('database.default');
        if (Cache::has($cacheKey)) {
            return;
        }
        
        try {
            // Modül Bileşenleri kategorisini kontrol et
            $mainModuleCategory = WidgetCategory::where('slug', 'modul-bilesenleri')
                ->orWhere('title', 'Modül Bileşenleri')
                ->first();
            
            if (!$mainModuleCategory) {
                
                try {
                    // Önce yeni bir kategori nesnesi oluştur
                    $mainModuleCategory = new WidgetCategory([
                        'title' => 'Modül Bileşenleri',
                        'slug' => 'modul-bilesenleri',
                        'description' => 'Sistem modüllerine ait bileşenler',
                        'icon' => 'fa-cubes',
                        'order' => 1,
                        'is_active' => true,
                        'parent_id' => null,
                        'has_subcategories' => true
                    ]);
                    
                    // Kaydet
                    $mainModuleCategory->save();
                    
                    // Kategori ID'sini doğrula
                    if (!$mainModuleCategory->widget_category_id) {
                        throw new \Exception("Kategori ID oluşturulamadı");
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Modül Bileşenleri kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                    return;
                }
            } else {
            }
            
            // Modül kategorilerini oluştur
            $this->createModuleWidgets($mainModuleCategory);
            
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
                    
                } catch (\Exception $e) {
                    Log::error("Modül kategorisi oluşturulamadı: $moduleSlug. Hata: " . $e->getMessage());
                    continue;
                }
                
                // Bu modüle ait widget dizinleri
                $widgetFolders = File::directories($moduleFolder);
                
                foreach ($widgetFolders as $widgetFolder) {
                    $widgetName = basename($widgetFolder);
                    $widgetViewPath = $widgetFolder . '/view.blade.php';
                    $moduleName = basename($moduleFolder);
                    
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
                                'settings_schema' => $this->generateWidgetSettings($moduleName, $widgetName, $widgetViewPath)
                            ]);
                            
                        } else {
                            // Varolan widget'ı güncelle - tip yanlışsa düzelt
                            $existingWidget->update([
                                'widget_category_id' => $moduleCategory->widget_category_id,
                                'type' => 'module',
                                'file_path' => 'modules/' . $moduleName . '/' . $widgetName . '/view',
                                'settings_schema' => $this->generateWidgetSettings($moduleName, $widgetName, $widgetViewPath)
                            ]);
                            
                            Log::info("Modül widget'ı güncellendi: $widgetTitle (slug: $widgetSlug)");
                        }
                    }
                }
            }
        } else {
            Log::warning("Modül klasörü bulunamadı: $moduleBasePath");
        }
    }
    
    private function generateWidgetSettings($moduleName, $widgetName, $viewPath)
    {
        // Temel ayarlar her widget için olmalı
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
        
        // View dosyasını okuyarak içeriğindeki PHP kodundaki değişkenleri tespit et
        if (File::exists($viewPath)) {
            $content = File::get($viewPath);
            
            // Değişkenleri bul (özellikle $settings array'inden alınanlar)
            preg_match_all('/\$settings\[\'([^\']+)\'\]/', $content, $matches);
            
            if (!empty($matches[1])) {
                $settingKeys = array_unique($matches[1]);
                
                foreach ($settingKeys as $key) {
                    // Sistem ayarlarını tekrar eklemeyi önle
                    if (in_array($key, ['title', 'unique_id'])) {
                        continue;
                    }
                    
                    // Varsayılan değerleri belirle
                    $defaultValue = null;
                    preg_match('/\$settings\[\'' . $key . '\'\]\s+\?\?\s+([^;]+)/', $content, $defaultMatch);
                    if (!empty($defaultMatch[1])) {
                        $defaultValue = trim($defaultMatch[1]);
                        // Stringler için tırnak işaretlerini kaldır
                        if (preg_match('/^[\'"](.*)[\'"]\s*$/', $defaultValue, $strMatch)) {
                            $defaultValue = $strMatch[1];
                        } elseif ($defaultValue === 'true') {
                            $defaultValue = true;
                        } elseif ($defaultValue === 'false') {
                            $defaultValue = false;
                        } elseif (is_numeric($defaultValue)) {
                            $defaultValue = (int)$defaultValue;
                        }
                    }
                    
                    // Ayar tipini belirle
                    $type = 'text'; // Varsayılan tip
                    
                    // Anahtar adına göre tip tahmini yap
                    if (Str::contains($key, ['show_', 'is_', 'has_', 'enable_'])) {
                        $type = 'checkbox';
                    } elseif (Str::contains($key, ['count', 'limit', '_id', 'size', 'width', 'height'])) {
                        $type = 'number';
                    } elseif (Str::contains($key, ['color', 'colour'])) {
                        $type = 'color';
                    } elseif (Str::contains($key, ['content', 'description', 'body', 'text'])) {
                        $type = 'textarea';
                    } elseif (Str::contains($key, ['date'])) {
                        $type = 'date';
                    } elseif (Str::contains($key, ['image', 'photo', 'picture'])) {
                        $type = 'image';
                    } elseif (Str::contains($key, ['type', 'category', 'style'])) {
                        $type = 'select';
                    }
                    
                    // Ayarı ekle
                    $setting = [
                        'name' => $key,
                        'label' => $this->generateReadableLabel($key),
                        'type' => $type,
                        'required' => false
                    ];
                    
                    // Varsayılan değer varsa ekle
                    if ($defaultValue !== null) {
                        $setting['default'] = $defaultValue;
                    }
                    
                    $settings[] = $setting;
                }
            }
        }
        
        return $settings;
    }
    
    private function generateReadableLabel($key)
    {
        // Alt çizgileri boşluklara dönüştür
        $label = str_replace('_', ' ', $key);
        
        // Her kelimenin ilk harfini büyük yap
        $label = ucwords($label);
        
        // Özel düzeltmeler
        $replacements = [
            'Id' => 'ID',
            'Url' => 'URL',
            'Bg' => 'Arkaplan',
        ];
        
        foreach ($replacements as $search => $replace) {
            $label = str_replace($search, $replace, $label);
        }
        
        return $label;
    }
}