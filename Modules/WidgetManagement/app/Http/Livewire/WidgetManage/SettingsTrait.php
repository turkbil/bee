<?php

namespace Modules\WidgetManagement\app\Http\Livewire\WidgetManage;

use Illuminate\Support\Str;

trait SettingsTrait
{
    public function addSettingsSchemaField()
    {
        $this->validate([
            'newField.name' => 'required|regex:/^[a-zA-Z0-9_]+$/i',
            'newField.label' => 'required',
            'newField.type' => 'required'
        ]);
        
        // Sistem alanı ise düzenlenemez
        $systemSpecialFields = ['title', 'unique_id'];
        if (in_array($this->newField['name'], $systemSpecialFields)) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Bu alan ismi sistem tarafından ayrılmıştır ve kullanılamaz.',
                'type' => 'error'
            ]);
            return;
        }
        
        // Önek ekleme (widget.) - "title" ve "unique_id" dışındaki tüm alanlar için
        $fieldName = $this->newField['name'];
        if (!in_array($fieldName, ['title', 'unique_id'])) {
            $fieldName = 'widget.' . $fieldName;
        }
        
        $field = [
            'name' => $fieldName,
            'label' => $this->newField['label'],
            'type' => $this->newField['type'],
            'required' => $this->newField['required'] ?? false
        ];
        
        if ($this->newField['type'] === 'select') {
            // Seçim kutusu için options
            if ($this->optionFormat === 'key-value') {
                $options = [];
                if (!empty($this->newField['options_array'])) {
                    foreach ($this->newField['options_array'] as $option) {
                        if (isset($option['key']) && !empty($option['key']) && isset($option['value'])) {
                            $options[$option['key']] = $option['value'];
                        }
                    }
                }
                $field['options'] = $options;
            } else {
                // Text formatından dönüştür
                $options = [];
                $lines = explode("\n", $this->newField['options']);
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    
                    if (strpos($line, '=') !== false) {
                        list($key, $value) = explode('=', $line, 2);
                        $options[trim($key)] = trim($value);
                    } else {
                        $options[Str::slug($line)] = $line;
                    }
                }
                
                $field['options'] = $options;
            }
        }
        
        $settingsSchema = $this->widget['settings_schema'] ?? [];
        $settingsSchema[] = $field;
        
        $this->widget['settings_schema'] = $settingsSchema;
        
        // Temizle
        $this->newField = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'options' => '',
            'options_array' => []
        ];
    }
    
    public function removeSettingsSchemaField($index)
    {
        $settingsSchema = $this->widget['settings_schema'] ?? [];
        
        // Sistem alanları silinemez
        if (isset($settingsSchema[$index]) && isset($settingsSchema[$index]['system']) && $settingsSchema[$index]['system']) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Sistem alanları silinemez.',
                'type' => 'error'
            ]);
            return;
        }
        
        if (isset($settingsSchema[$index])) {
            unset($settingsSchema[$index]);
            $this->widget['settings_schema'] = array_values($settingsSchema);
        }
    }
}