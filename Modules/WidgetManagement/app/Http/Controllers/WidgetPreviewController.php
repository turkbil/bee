<?php

namespace Modules\WidgetManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class WidgetPreviewController extends Controller
{
    public function show($id)
    {
        try {
            $widget = Widget::findOrFail($id);
            
            // Modül tipinde bir widget ise
            if ($widget->type === 'module') {
                // file_path bilgisini al
                $filePath = $widget->file_path;
                
                // file_path boş ise hata göster
                if (empty($filePath)) {
                    return response()->view('widgetmanagement::widget.error', [
                        'message' => 'Bu modül bileşeni için dosya yolu (file_path) tanımlanmamış. Lütfen widget\'ı düzenleyerek bir dosya yolu belirtin.'
                    ], 404);
                }
                
                // Modül bileşeni için view yolunu doğrudan oluştur
                $viewPath = 'widgetmanagement::blocks.' . $filePath;
                
                // View'un varlığını kontrol et
                if (!View::exists($viewPath)) {
                    // Gerçek dosya yolunu oluştur ve kontrol et
                    $fullViewPathWithBladeExtension = base_path() . '/Modules/WidgetManagement/resources/views/blocks/' . $filePath . '.blade.php';
                    
                    if (!File::exists($fullViewPathWithBladeExtension)) {
                        return response()->view('widgetmanagement::widget.error', [
                            'message' => 'Belirtilen modül dosyası bulunamadı: ' . $viewPath . 
                                '<br><br>Dosya yolu: <code>Modules/WidgetManagement/resources/views/blocks/' . 
                                $filePath . '.blade.php</code>'
                        ], 404);
                    }
                }
                
                // Varsayılan ayarları oluştur
                $settings = [
                    'title' => $widget->name,
                    'unique_id' => Str::random(),
                    'show_description' => true
                ];
                
                // Widget ayarlarını ekle
                if (!empty($widget->settings_schema) && is_array($widget->settings_schema)) {
                    $settings = array_merge($settings, $widget->settings_schema);
                }
                
                // Modül widget'ını file-preview şablonu ile görüntüle
                return view('widgetmanagement::widget.file-preview', [
                    'widget' => $widget,
                    'viewPath' => $viewPath,
                    'settings' => $settings
                ]);
            }
            
            // Dinamik veya statik widget: context oluştur
            $tenantWidgets = $widget->tenantWidgets()
                ->where('is_active', true)
                ->with(['items' => fn($query) => $query->orderBy('order')])
                ->get();
            $itemsData = $widget->has_items
                ? $tenantWidgets->flatMap(fn($tw) => $tw->items->pluck('content'))->toArray()
                : [];
            // Eğer dinamik widget için henüz öğe yoksa, schema default ve label ile placeholder oluştur
            if ($widget->has_items && empty($itemsData)) {
                $defaultItem = [];
                foreach ($widget->getItemSchema() as $schema) {
                    $key = $schema['name'];
                    if (isset($schema['default']) && $schema['default'] !== '') {
                        $value = $schema['default'];
                    } else {
                        if ($key === 'image') {
                            $value = 'https://via.placeholder.com/800x400?text=Placeholder';
                        } else {
                            $value = $schema['label'] ?? ucfirst(str_replace('_', ' ', $key));
                        }
                    }
                    $defaultItem[$key] = $value;
                }
                $itemsData = [$defaultItem];
            }
            // Görsel URL'lerini cdn() ile dönüştür (sadece relatif URL'ler)
            foreach ($itemsData as &$item) {
                if (isset($item['image']) && !preg_match('/^https?:\/\//', $item['image'])) {
                    $item['image'] = cdn($item['image']);
                }
            }
            unset($item);
            // Tenant ayarları
            $tenantSettings = $tenantWidgets->first()->settings ?? [];
            // Ayar değerleri, schema üzerinden default/tenant
            $context = [];
            if (!empty($widget->settings_schema) && is_array($widget->settings_schema)) {
                foreach ($widget->settings_schema as $schema) {
                    $key = $schema['name'];
                    if (array_key_exists($key, $tenantSettings)) {
                        $context[$key] = $tenantSettings[$key];
                    } elseif (array_key_exists('default', $schema)) {
                        $context[$key] = $schema['default'];
                    }
                }
            }
            // Statik için title, unique_id özelliği ekle
            if (!isset($context['title'])) {
                $context['title'] = $widget->name;
            }
            if (!isset($context['unique_id'])) {
                $context['unique_id'] = Str::random();
            }
            // Öğeler context'e ekle
            $context['items'] = $itemsData;
            
            // HTML içeriğindeki çift süslü parantezleri güvenli hale getir
            $contentHtml = $widget->content_html;
            
            return view('widgetmanagement::widget.preview', [
                'widget' => $widget,
                'context' => $context,
                'useHandlebars' => true
            ]);
            
        } catch (\Exception $e) {
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Widget yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Embed widget preview for Studio
     */
    public function embed($tenantWidgetId)
    {
        try {
            $tw = TenantWidget::with('items')->findOrFail($tenantWidgetId);
            $widget = $tw->widget;
            $itemsData = $widget->has_items ? $tw->items->pluck('content')->toArray() : [];
            if ($widget->has_items && empty($itemsData)) {
                $defaultItem = [];
                foreach ($widget->getItemSchema() as $schema) {
                    $key = $schema['name'];
                    if (isset($schema['default']) && $schema['default'] !== '') {
                        $value = $schema['default'];
                    } else {
                        if ($key === 'image') {
                            $value = 'https://via.placeholder.com/800x400?text=Placeholder';
                        } else {
                            $value = $schema['label'] ?? ucfirst(str_replace('_', ' ', $key));
                        }
                    }
                    $defaultItem[$key] = $value;
                }
                $itemsData = [$defaultItem];
            }
            foreach ($itemsData as &$item) {
                if (isset($item['image']) && !preg_match('/^https?:\/\//', $item['image'])) {
                    $item['image'] = cdn($item['image']);
                }
            }
            unset($item);
            $tenantSettings = $tw->settings ?? [];
            $context = [];
            if (!empty($widget->settings_schema) && is_array($widget->settings_schema)) {
                foreach ($widget->settings_schema as $schema) {
                    $key = $schema['name'];
                    if (array_key_exists($key, $tenantSettings)) {
                        $context[$key] = $tenantSettings[$key];
                    } elseif (array_key_exists('default', $schema)) {
                        $context[$key] = $schema['default'];
                    }
                }
            }
            if (!isset($context['title'])) {
                $context['title'] = $widget->name;
            }
            if (!isset($context['unique_id'])) {
                $context['unique_id'] = Str::random();
            }
            $context['items'] = $itemsData;
            
            return view('widgetmanagement::widget.embed', [
                'widget' => $widget,
                'context' => $context,
                'tenantWidgetId' => $tenantWidgetId,
                'useHandlebars' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * JSON embed endpoint for Studio loader
     */
    public function embedJson($tenantWidgetId)
    {
        try {
            $tw = TenantWidget::with('items')->findOrFail($tenantWidgetId);
            $widget = $tw->widget;
            $itemsData = $widget->has_items ? $tw->items->pluck('content')->toArray() : [];
            if ($widget->has_items && empty($itemsData)) {
                $defaultItem = [];
                foreach ($widget->getItemSchema() as $schema) {
                    $key = $schema['name'];
                    if (isset($schema['default']) && $schema['default'] !== '') {
                        $value = $schema['default'];
                    } else {
                        if ($key === 'image') {
                            $value = 'https://via.placeholder.com/800x400?text=Placeholder';
                        } else {
                            $value = $schema['label'] ?? ucfirst(str_replace('_', ' ', $key));
                        }
                    }
                    $defaultItem[$key] = $value;
                }
                $itemsData = [$defaultItem];
            }
            foreach ($itemsData as &$item) {
                if (isset($item['image']) && !preg_match('/^https?:\/\//', $item['image'])) {
                    $item['image'] = cdn($item['image']);
                }
            }
            unset($item);
            $tenantSettings = $tw->settings ?? [];
            $context = [];
            if (!empty($widget->settings_schema) && is_array($widget->settings_schema)) {
                foreach ($widget->settings_schema as $schema) {
                    $key = $schema['name'];
                    if (array_key_exists($key, $tenantSettings)) {
                        $context[$key] = $tenantSettings[$key];
                    } elseif (array_key_exists('default', $schema)) {
                        $context[$key] = $schema['default'];
                    }
                }
            }
            if (!isset($context['title'])) {
                $context['title'] = $widget->name;
            }
            if (!isset($context['unique_id'])) {
                $context['unique_id'] = Str::random();
            }
            $context['items'] = $itemsData;
            return response()->json([
                'content_html' => $widget->content_html,
                'context'      => $context,
                'content_css'  => $widget->content_css ?? '',
                'content_js'   => $widget->content_js ?? '',
                'useHandlebars'=> true
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showFile($id)
    {
        try {
            $widget = Widget::findOrFail($id);
            
            if ($widget->type !== 'file' || empty($widget->file_path)) {
                return response()->view('widgetmanagement::widget.error', [
                    'message' => 'Bu widget bir dosya tipinde değil veya dosya yolu belirtilmemiş.'
                ], 404);
            }
            
            // Hazır dosyayı render etmeye çalış - file_path değeri doğrudan kullanılır
            $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
            
            if (!View::exists($viewPath)) {
                // Gerçek dosya yolunu oluştur ve kontrol et
                $fullViewPathWithBladeExtension = base_path() . '/Modules/WidgetManagement/resources/views/blocks/' . $widget->file_path . '.blade.php';
                
                if (!File::exists($fullViewPathWithBladeExtension)) {
                    return response()->view('widgetmanagement::widget.error', [
                        'message' => 'Belirtilen view dosyası bulunamadı: ' . $viewPath . 
                            '<br><br>Dosya yolu: <code>Modules/WidgetManagement/resources/views/blocks/' . 
                            $widget->file_path . '.blade.php</code>'
                    ], 404);
                }
            }
            
            return view('widgetmanagement::widget.file-preview', [
                'widget' => $widget,
                'viewPath' => $viewPath,
                'settings' => [
                    'title' => $widget->name,
                    'unique_id' => Str::random()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Widget yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}