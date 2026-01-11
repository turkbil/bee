<?php

namespace Modules\AI\App\Http\Controllers\Admin\Features;

use App\Http\Controllers\Controller;
use Modules\AI\App\Models\AIFeatureCategory;
use Illuminate\Http\Request;

class AIFeatureCategoriesController extends Controller
{
    public function index()
    {
        $categories = AIFeatureCategory::orderBy('order')->get();
        $search = '';
        
        return view('ai::admin.features.categories', compact('categories', 'search'));
    }
    
    public function updateOrder(Request $request)
    {
        $updates = $request->input('updates', []);
        
        foreach ($updates as $update) {
            if (isset($update['value'], $update['order'])) {
                AIFeatureCategory::where('ai_feature_category_id', $update['value'])
                    ->update(['order' => $update['order']]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => __('ai::admin.categories_order_updated')
        ]);
    }
    
    public function toggleStatus(Request $request, $id)
    {
        $category = AIFeatureCategory::where('ai_feature_category_id', $id)->first();
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => __('ai::admin.category_not_found')
            ]);
        }
        
        $category->update([
            'is_active' => $request->boolean('is_active')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => __('ai::admin.category_status_updated')
        ]);
    }
}