<?php

namespace Modules\WidgetManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\WidgetManagement\app\Models\Widget;

class WidgetPreviewController extends Controller
{
    public function show($id)
    {
        $widget = Widget::findOrFail($id);
        return view('widgetmanagement::widget.preview', [
            'widget' => $widget
        ]);
    }
}