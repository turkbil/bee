<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\SettingManagement\App\Models\SettingGroup;
use Modules\SettingManagement\App\Models\Setting;

class FormBuilderComponent extends Component
{
    public $groupId;
    public $group;

    public function mount($groupId)
    {
        $this->groupId = $groupId;

        // Grup ID'si zorunlu ve grup bir alt grup olmalı
        $this->group = SettingGroup::where('id', $groupId)
            ->firstOrFail();
    }

    public function saveLayout($data)
    {
        try {
            $groupId = $data['groupId'] ?? $this->groupId;
            $formData = $data['formData'] ?? null;

            if (!$groupId || !$formData) {
                throw new \Exception("Eksik parametreler: groupId veya formData");
            }

            $group = SettingGroup::findOrFail($groupId);

            // JSON string olarak gelmesi durumunda
            if (is_string($formData)) {
                $formData = json_decode($formData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Geçersiz JSON formatı");
                }
            }

            // ✅ OTOMATIK SETTING SENKRONIZASYONU
            $this->syncSettingsFromLayout($groupId, $formData);

            $group->layout = $formData;
            $group->save();

            log_activity(
                $group,
                'form layout güncellendi ve settings senkronize edildi'
            );

            return ['success' => true, 'message' => 'Form yapısı ve ayarlar başarıyla kaydedildi'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Layout'tan form elementlerini extract edip settings tablosuna senkronize eder
     */
    protected function syncSettingsFromLayout($groupId, $layout)
    {
        if (!isset($layout['elements']) || !is_array($layout['elements'])) {
            return;
        }

        $extractedSettings = [];
        $this->extractSettingsRecursive($layout['elements'], $extractedSettings);

        // Mevcut settings'leri al
        $existingSettings = Setting::where('group_id', $groupId)
            ->get()
            ->keyBy('key');

        $sortOrder = 10;

        foreach ($extractedSettings as $settingData) {
            $key = $settingData['name'];

            if (empty($key)) {
                continue;
            }

            // Eğer setting varsa güncelle, yoksa oluştur
            if (isset($existingSettings[$key])) {
                // Mevcut setting'i güncelle
                $setting = $existingSettings[$key];
                $updateData = [
                    'label' => $settingData['label'] ?? $setting->label,
                    'type' => $settingData['type'] ?? $setting->type,
                    'default_value' => $settingData['default_value'] ?? $setting->default_value,
                    'is_required' => $settingData['required'] ?? false,
                    'sort_order' => $sortOrder,
                ];

                // Options varsa ekle (select, radio için)
                if (isset($settingData['options']) && !empty($settingData['options'])) {
                    $updateData['options'] = $settingData['options'];
                }

                $setting->update($updateData);
                \Log::info("Setting güncellendi: {$key}");
            } else {
                // Yeni setting oluştur
                $createData = [
                    'group_id' => $groupId,
                    'key' => $key,
                    'label' => $settingData['label'] ?? ucfirst(str_replace('_', ' ', $key)),
                    'type' => $settingData['type'] ?? 'text',
                    'default_value' => $settingData['default_value'] ?? null,
                    'is_required' => $settingData['required'] ?? false,
                    'is_active' => true,
                    'is_system' => false,
                    'sort_order' => $sortOrder,
                ];

                // Options varsa ekle (select, radio için)
                if (isset($settingData['options']) && !empty($settingData['options'])) {
                    $createData['options'] = $settingData['options'];
                }

                Setting::create($createData);
                \Log::info("Yeni setting oluşturuldu: {$key}");
            }

            $sortOrder += 10;
        }
    }

    /**
     * Layout elementlerini recursive olarak tarayıp form field'ları extract eder
     */
    protected function extractSettingsRecursive($elements, &$result)
    {
        foreach ($elements as $element) {
            $type = $element['type'] ?? null;

            // Form input tipi mi kontrol et
            $formTypes = [
                'text', 'textarea', 'number', 'email', 'password', 'tel', 'url',
                'select', 'checkbox', 'radio', 'switch', 'range',
                'date', 'time', 'color',
                'file', 'image', 'favicon', 'image_multiple', 'json'
            ];

            if (in_array($type, $formTypes)) {
                // Properties'den veya direkt element'ten al
                $props = $element['properties'] ?? $element;
                $name = $props['name'] ?? null;

                if (!empty($name)) {
                    $settingData = [
                        'name' => $name,
                        'label' => $props['label'] ?? null,
                        'type' => $type,
                        'default_value' => $props['default_value'] ?? $props['default'] ?? null,
                        'required' => $props['required'] ?? false,
                    ];

                    // Select ve radio için options'ı da al
                    if (in_array($type, ['select', 'radio']) && isset($props['options'])) {
                        // JavaScript formatı: [{value, label}] → DB formatı: {value: label}
                        $options = [];
                        foreach ($props['options'] as $opt) {
                            if (is_array($opt) && isset($opt['value']) && isset($opt['label'])) {
                                $options[$opt['value']] = $opt['label'];
                            } elseif (is_string($opt)) {
                                // Eski format: string array
                                $options[] = $opt;
                            }
                        }
                        $settingData['options'] = $options;
                    }

                    $result[] = $settingData;
                }
            }

            // Row içindeki columns'u tara
            if ($type === 'row' && isset($element['columns'])) {
                foreach ($element['columns'] as $column) {
                    if (isset($column['elements'])) {
                        $this->extractSettingsRecursive($column['elements'], $result);
                    }
                }
            }

            // Tab group içindeki tabs'ları tara
            if ($type === 'tab_group' && isset($element['tabs'])) {
                foreach ($element['tabs'] as $tab) {
                    if (isset($tab['elements'])) {
                        $this->extractSettingsRecursive($tab['elements'], $result);
                    }
                }
            }

            // Card içindeki elements'leri tara
            if ($type === 'card' && isset($element['elements'])) {
                $this->extractSettingsRecursive($element['elements'], $result);
            }
        }
    }
    
    public function render()
    {
        return view('settingmanagement::form-builder.edit')
            ->layout('settingmanagement::form-builder.layout');
    }
}