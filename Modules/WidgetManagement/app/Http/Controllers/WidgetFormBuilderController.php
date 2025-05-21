<?php

namespace Modules\WidgetManagement\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\WidgetManagement\App\Models\Widget;
use Illuminate\Support\Str;

class WidgetFormBuilderController extends Controller
{
    /**
     * Widget form layout yükle
     * @param int $widgetId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function load($widgetId, Request $request)
    {
        try {
            $widget = Widget::findOrFail($widgetId);
            $schemaType = $request->get('schema', 'item'); // item veya settings
            
            $layout = null;
            
            if ($schemaType === 'settings') {
                $schemaData = $widget->settings_schema;
            } else {
                $schemaData = $widget->item_schema;
            }
            
            if (!empty($schemaData)) {
                $layout = is_array($schemaData) ? $schemaData : json_decode($schemaData, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $layout = null;
                }
            }
            
            if (empty($layout)) {
                $layout = [
                    'title' => $widget->name . ' ' . ($schemaType === 'settings' ? 'Ayarları' : 'İçerik Yapısı'),
                    'elements' => []
                ];
            }
                
            return response()->json([
                'success' => true,
                'layout' => $layout
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'layout' => [
                    'title' => 'Widget Form Yapısı',
                    'elements' => []
                ]
            ], 200);
        }
    }
    
    /**
     * Widget form layout kaydet
     * @param Request $request
     * @param int $widgetId
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request, $widgetId)
    {
        try {
            $widget = Widget::findOrFail($widgetId);
            $schemaType = $request->get('schema', 'item');
            
            $formData = $request->input('layout');
            
            if (empty($formData)) {
                throw new \Exception("Form verisi boş olamaz.");
            }
            
            if (is_string($formData)) {
                $formData = json_decode($formData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Geçersiz JSON formatı");
                }
            }
            
            // Schema tipine göre kaydet
            if ($schemaType === 'settings') {
                $widget->settings_schema = $formData;
            } else {
                $widget->item_schema = $formData;
            }
            
            $widget->save();
            
            log_activity(
                $widget,
                'widget ' . ($schemaType === 'settings' ? 'ayar' : 'içerik') . ' form yapısı güncellendi'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Widget form yapısı başarıyla kaydedildi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Widget form yapısı kaydedilirken bir hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
}