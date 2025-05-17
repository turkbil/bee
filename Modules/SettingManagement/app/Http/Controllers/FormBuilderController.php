<?php

namespace Modules\SettingManagement\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SettingManagement\App\Models\SettingGroup;
use Modules\SettingManagement\App\Models\Setting;

class FormBuilderController extends Controller
{
    /**
     * Form layout yükle
     * @param int $groupId
     * @return \Illuminate\Http\JsonResponse
     */
    public function load($groupId)
    {
        $group = SettingGroup::findOrFail($groupId);
        
        return response()->json([
            'success' => true,
            'layout' => $group->layout
        ]);
    }
    
    /**
     * Grup için ayarların listesini getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings(Request $request)
    {
        $groupId = $request->input('group');
        
        if (!$groupId) {
            return response()->json([]);
        }
        
        $settings = Setting::where('group_id', $groupId)
            ->where('is_active', true)
            ->select('id', 'label', 'key', 'type')
            ->orderBy('sort_order', 'asc')
            ->get();
            
        return response()->json($settings);
    }
}