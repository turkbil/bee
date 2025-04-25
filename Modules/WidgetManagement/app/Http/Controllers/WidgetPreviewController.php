<?php

namespace Modules\WidgetManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\WidgetManagement\app\Models\Widget;
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
            
            // Normal widget görünümü
            return view('widgetmanagement::widget.preview', [
                'widget' => $widget
            ]);
            
        } catch (\Exception $e) {
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Widget yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
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