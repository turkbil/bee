<?php

namespace Modules\SettingManagement\App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SettingManagement\App\Models\SettingGroup;
use Illuminate\Support\Facades\Log;

class FormBuilderController extends Controller
{
    /**
     * Form Builder ana sayfasını göster
     * @return Renderable
     */
    public function index()
    {
        // Sadece alt grupları getir
        $groups = SettingGroup::whereNotNull('parent_id')->get();
        return view('settingmanagement::form-builder.index', compact('groups'));
    }

    /**
     * Belirli bir grup için Form Builder'ı göster
     * @param int $groupId
     * @return Renderable
     */
    public function edit($groupId)
    {
        $group = SettingGroup::findOrFail($groupId);
        return view('settingmanagement::form-builder.edit', compact('group'));
    }

    /**
     * Form Builder konfigürasyonunu kaydet
     * @param Request $request
     * @param int $groupId
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request, $groupId)
    {
        $group = SettingGroup::findOrFail($groupId);
        
        $formData = $request->json('formData');
        
        // JSON formatında layout alanına kaydet
        $group->layout = $formData;
        $group->save();
        
        log_activity(
            $group,
            'form layout güncellendi'
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Form yapısı başarıyla kaydedildi'
        ]);
    }

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