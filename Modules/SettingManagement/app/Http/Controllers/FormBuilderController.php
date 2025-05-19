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
        try {
            $group = SettingGroup::findOrFail($groupId);
            
            // Boş değilse ve geçerli bir JSON ise doğrudan, değilse boş bir layout nesnesi döndür
            $layout = null;
            
            if (!empty($group->layout)) {
                $layout = is_array($group->layout) ? $group->layout : json_decode($group->layout, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $layout = null;
                }
            }
            
            // Eğer layout boşsa varsayılan boş bir yapı oluştur
            if (empty($layout)) {
                $layout = [
                    'title' => $group->name . ' Formu',
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
                    'title' => 'Form Yapısı',
                    'elements' => []
                ]
            ], 200); // 500 yerine 200 döndürelim ama hata bilgisiyle
        }
    }
    
    /**
     * Grup için ayarların listesini getir
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 200); // 500 yerine 200 döndürelim
        }
    }
    
    /**
     * Form layout kaydet
     * @param Request $request
     * @param int $groupId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, $groupId)
    {
        try {
            $group = SettingGroup::findOrFail($groupId);
            
            $formData = $request->input('layout');
            
            if (empty($formData)) {
                throw new \Exception("Form verisi boş olamaz.");
            }
            
            // JSON string olarak gelmesi durumunda
            if (is_string($formData)) {
                $formData = json_decode($formData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Geçersiz JSON formatı");
                }
            }
            
            $group->layout = $formData;
            $group->save();
            
            log_activity(
                $group,
                'form layout güncellendi'
            );
            
            return redirect()->back()->with('success', 'Form yapısı başarıyla kaydedildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Form yapısı kaydedilirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}