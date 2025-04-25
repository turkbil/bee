<?php

namespace Modules\WidgetManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\WidgetManagement\app\Models\Widget;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class WidgetPreviewController extends Controller
{
    public function show($id)
    {
        Log::info("WidgetPreviewController@show başlatıldı, ID: {$id}");
        
        try {
            $widget = Widget::findOrFail($id);
            Log::info("Widget bulundu: {$widget->id} - {$widget->name} - Tip: {$widget->type}");
            
            // Modül tipinde bir widget ise
            if ($widget->type === 'module') {
                Log::info("Modül tipi widget tespit edildi");
                
                // Data source tanımlı değilse, widget ID'sinden tahmin edelim
                $dataSource = $widget->data_source;
                
                // Widget 19 için portfolio/list olarak tanımlayalım (ID'ye göre hardcoded çözüm)
                if ($widget->id == 19 && empty($dataSource)) {
                    $dataSource = 'portfolio/list';
                    Log::info("Widget 19 için data_source otomatik belirlendi: {$dataSource}");
                }
                
                // Yine de boşsa, bir hata mesajı gösterelim
                if (empty($dataSource)) {
                    Log::error("Modül widget'ı için data_source tanımlanmamış");
                    return response()->view('widgetmanagement::widget.error', [
                        'message' => 'Bu modül bileşeni için veri kaynağı (data_source) tanımlanmamış. Lütfen widget\'ı düzenleyerek bir veri kaynağı belirtin.'
                    ], 404);
                }
                
                // Modül bileşeni için olası view yollarını kontrol et
                // 1. İlk olarak direkt olarak dataSource yolunu dene
                $viewPath = 'widgetmanagement::blocks.modules.' . str_replace('/', '.', $dataSource);
                
                // 2. Eğer bulunamazsa, dataSource/view yolunu dene
                $viewPathWithView = 'widgetmanagement::blocks.modules.' . str_replace('/', '.', $dataSource) . '.view';
                
                Log::info("Kontrol edilen modül view yolları: {$viewPath}, {$viewPathWithView}");
                
                // View'ları kontrol et ve varsa kullan
                if (View::exists($viewPathWithView)) {
                    $viewPath = $viewPathWithView;
                    Log::info("Modül view dosyası bulundu: {$viewPath}");
                } elseif (View::exists($viewPath)) {
                    Log::info("Modül view dosyası bulundu: {$viewPath}");
                } else {
                    Log::error("Modül view dosyası bulunamadı: {$viewPath} veya {$viewPathWithView}");
                    
                    // Alternatif klasör yapısını kontrol et
                    $fullViewPathWithBladeExtension = base_path() . '/Modules/WidgetManagement/resources/views/blocks/modules/' . 
                        str_replace('.', '/', str_replace('/', '/', $dataSource)) . '/view.blade.php';
                    
                    if (File::exists($fullViewPathWithBladeExtension)) {
                        // Relatif blade yolunu oluştur
                        $relativePath = 'widgetmanagement::blocks.modules.' . str_replace('/', '.', $dataSource) . '.view';
                        $viewPath = $relativePath;
                        Log::info("Alternatif modül view dosyası bulundu: {$viewPath}");
                    } else {
                        return response()->view('widgetmanagement::widget.error', [
                            'message' => 'Belirtilen modül dosyası bulunamadı: ' . $viewPath . '<br>Veri kaynağı: ' . $dataSource . 
                                '<br><br>Lütfen aşağıdaki dosya yollarını kontrol edin: <br><code>Modules/WidgetManagement/resources/views/blocks/modules/' . 
                                str_replace('.', '/', $dataSource) . '.blade.php</code><br>veya<br><code>Modules/WidgetManagement/resources/views/blocks/modules/' . 
                                str_replace('.', '/', $dataSource) . '/view.blade.php</code>'
                        ], 404);
                    }
                }
                
                // Varsayılan ayarları oluştur
                $settings = [
                    'title' => $widget->name,
                    'unique_id' => Str::random(),
                    'show_description' => true,
                    'limit' => 6,
                    'order_direction' => 'desc'
                ];
                
                // Widget ayarlarından modül tipi varsa ekle
                if (isset($widget->settings_schema['module_type'])) {
                    $settings['module_type'] = $widget->settings_schema['module_type'];
                }
                
                Log::info("Modül widget'ı için file-preview şablonuna yönlendiriliyor. Kullanılan view: {$viewPath}");
                // Modül widget'ını file-preview şablonu ile görüntüle
                return view('widgetmanagement::widget.file-preview', [
                    'widget' => $widget,
                    'viewPath' => $viewPath,
                    'settings' => $settings
                ]);
            }
            
            Log::info("Normal widget görünümü kullanılıyor");
            // Normal widget görünümü
            return view('widgetmanagement::widget.preview', [
                'widget' => $widget
            ]);
            
        } catch (\Exception $e) {
            Log::error("WidgetPreviewController@show exception: " . $e->getMessage());
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Widget yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function showFile($id)
    {
        Log::info("WidgetPreviewController@showFile başlatıldı, ID: {$id}");
        
        try {
            $widget = Widget::findOrFail($id);
            Log::info("File widget bulundu: {$widget->id} - {$widget->name}");
            
            if ($widget->type !== 'file' || empty($widget->file_path)) {
                Log::error("Widget dosya tipinde değil veya dosya yolu yok");
                return response()->view('widgetmanagement::widget.error', [
                    'message' => 'Bu widget bir dosya tipinde değil veya dosya yolu belirtilmemiş.'
                ], 404);
            }
            
            // Hazır dosyayı render etmeye çalış
            $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
            Log::info("File view yolu: {$viewPath}");
            
            if (!View::exists($viewPath)) {
                // Alternatif klasör yapısını kontrol et (file_path/view.blade.php)
                $alternativeViewPath = 'widgetmanagement::blocks.' . $widget->file_path . '.view';
                
                if (View::exists($alternativeViewPath)) {
                    $viewPath = $alternativeViewPath;
                    Log::info("Alternatif view dosyası bulundu: {$viewPath}");
                } else {
                    Log::error("View dosyası bulunamadı: {$viewPath} ve {$alternativeViewPath}");
                    return response()->view('widgetmanagement::widget.error', [
                        'message' => 'Belirtilen view dosyası bulunamadı: ' . $viewPath . '<br>Alternatif: ' . $alternativeViewPath
                    ], 404);
                }
            }
            
            Log::info("File widget'ı için file-preview şablonuna yönlendiriliyor");
            return view('widgetmanagement::widget.file-preview', [
                'widget' => $widget,
                'viewPath' => $viewPath,
                'settings' => [
                    'title' => $widget->name,
                    'unique_id' => Str::random()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("WidgetPreviewController@showFile exception: " . $e->getMessage());
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Widget yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}