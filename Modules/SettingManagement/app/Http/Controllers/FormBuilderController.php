<?php

namespace Modules\SettingManagement\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SettingManagement\App\Models\SettingGroup;
use Modules\SettingManagement\App\Models\Setting;
use Illuminate\Support\Str;

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
     * @return \Illuminate\Http\JsonResponse
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
            
            // Eski ayarları temizle - düzen elemanları hariç
            $this->cleanOldSettings($groupId, $formData);
            
            // Form elemanlarını settings tablosuna ekle
            $this->saveFormElementsToSettings($groupId, $formData, $group);
            
            // Layout'u kaydet
            $group->layout = $formData;
            $group->save();
            
            log_activity(
                $group,
                'form layout güncellendi'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Form yapısı başarıyla kaydedildi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Form yapısı kaydedilirken bir hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Eski ayarları temizle - form elemanlarında olmayan ayarları sil
     */
    private function cleanOldSettings($groupId, $formData)
    {
        // Formda bulunan elementlerin name değerlerini topla
        $elementKeys = [];
        
        // Düzen elemanları listesi
        $layoutElements = ['row', 'heading', 'paragraph', 'divider', 'spacer', 'card', 'tab_group'];
        
        // Form elemanlarını topla
        if (!empty($formData['elements'])) {
            foreach ($formData['elements'] as $element) {
                // Düzen elemanlarını atla
                if (in_array($element['type'], $layoutElements)) {
                    // Row elementleri içindeki elemanları da kontrol et
                    if ($element['type'] === 'row' && !empty($element['columns'])) {
                        foreach ($element['columns'] as $column) {
                            if (!empty($column['elements'])) {
                                foreach ($column['elements'] as $columnElement) {
                                    // Düzen elemanlarını atla
                                    if (!in_array($columnElement['type'], $layoutElements)) {
                                        if (isset($columnElement['properties']['name'])) {
                                            $elementKeys[] = $columnElement['properties']['name'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    continue;
                }
                
                // Normal elemanların name değerlerini al
                if (isset($element['properties']['name'])) {
                    $elementKeys[] = $element['properties']['name'];
                }
            }
        }
        
        // Formda olmayan ayarları sil (is_system=false olanları)
        Setting::where('group_id', $groupId)
            ->where('is_system', false)
            ->whereNotIn('key', $elementKeys)
            ->delete();
    }
    
    /**
     * Form elemanlarını settings tablosuna kaydet
     */
    private function saveFormElementsToSettings($groupId, $formData, $group)
    {
        // Düzen elemanları listesi
        $layoutElements = ['row', 'heading', 'paragraph', 'divider', 'spacer', 'card', 'tab_group'];
        
        // Sort order için başlangıç değeri
        $sortOrder = 1;
        
        // Form elemanlarını kaydet
        if (!empty($formData['elements'])) {
            foreach ($formData['elements'] as $element) {
                // Düzen elemanlarını atla
                if (in_array($element['type'], $layoutElements)) {
                    // Row elementleri içindeki elemanları da kaydet
                    if ($element['type'] === 'row' && !empty($element['columns'])) {
                        foreach ($element['columns'] as $column) {
                            if (!empty($column['elements'])) {
                                foreach ($column['elements'] as $columnElement) {
                                    // Düzen elemanlarını atla
                                    if (!in_array($columnElement['type'], $layoutElements)) {
                                        $this->saveSettingFromElement($groupId, $columnElement, $sortOrder, $group);
                                        $sortOrder++;
                                    }
                                }
                            }
                        }
                    }
                    continue;
                }
                
                // Normal elemanları kaydet
                $this->saveSettingFromElement($groupId, $element, $sortOrder, $group);
                $sortOrder++;
            }
        }
    }
    
    /**
     * Bir form elemanını setting olarak kaydet
     */
    private function saveSettingFromElement($groupId, $element, $sortOrder, $group)
    {
        if (!isset($element['properties'])) {
            return;
        }
        
        $properties = $element['properties'];
        
        // Element type ve name kontrolü
        if (empty($element['type']) || empty($properties['name'])) {
            return;
        }
        
        // Prefix kontrolü ve alan adı oluşturma
        $name = $properties['name'];
        $groupPrefix = !empty($group->prefix) ? Str::slug($group->prefix, '_') : Str::slug($group->name, '_');
        
        // Eğer name zaten prefix ile başlıyorsa tekrar ekleme
        if (!Str::startsWith($name, $groupPrefix . '_')) {
            $name = $groupPrefix . '_' . $name;
        }
        
        // Label kontrolü
        $label = $properties['label'] ?? ucfirst($element['type']);
        
        // Key oluşturma
        $key = Str::slug($name, '_');
        
        // Varsayılan değer
        $defaultValue = $properties['default_value'] ?? null;
        
        // Mevcut setting var mı diye kontrol et
        $setting = Setting::where('group_id', $groupId)
            ->where('key', $key)
            ->first();
        
        if (!$setting) {
            // Yeni setting oluştur
            $setting = new Setting();
            $setting->group_id = $groupId;
        }
        
        // Setting değerlerini güncelle
        $setting->label = $label;
        $setting->key = $key;
        $setting->type = $element['type'];
        $setting->default_value = $defaultValue;
        $setting->sort_order = $sortOrder;
        $setting->is_active = $properties['is_active'] ?? true;
        $setting->is_system = $properties['is_system'] ?? false;
        
        // Select için options
        if ($element['type'] === 'select' && isset($properties['options'])) {
            $setting->options = $properties['options'];
        }
        
        $setting->save();
    }
}