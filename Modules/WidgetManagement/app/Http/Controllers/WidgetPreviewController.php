<?php

namespace Modules\WidgetManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\WidgetManagement\app\Models\Widget;
use Illuminate\Support\Facades\View;

class WidgetPreviewController extends Controller
{
    public function show($id)
    {
        $widget = Widget::findOrFail($id);
        return view('widgetmanagement::widget.preview', [
            'widget' => $widget
        ]);
    }
    
    public function showFile($id)
    {
        $widget = Widget::findOrFail($id);
        
        if ($widget->type !== 'file' || empty($widget->file_path)) {
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Bu widget bir dosya tipinde değil veya dosya yolu belirtilmemiş.'
            ], 404);
        }
        
        // Hazır dosyayı render etmeye çalış
        $viewPath = 'widgetmanagement::blocks.' . $widget->file_path;
        
        if (!View::exists($viewPath)) {
            return response()->view('widgetmanagement::widget.error', [
                'message' => 'Belirtilen view dosyası bulunamadı: ' . $viewPath
            ], 404);
        }
        
        return view('widgetmanagement::widget.file-preview', [
            'widget' => $widget,
            'viewPath' => $viewPath
        ]);
    }
}