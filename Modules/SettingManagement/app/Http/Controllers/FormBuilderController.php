<?php

namespace Modules\SettingManagement\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SettingManagement\App\Models\SettingGroup;

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
}