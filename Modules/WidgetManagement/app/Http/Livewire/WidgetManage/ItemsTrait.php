<?php

namespace Modules\WidgetManagement\app\Http\Livewire\WidgetManage;

use Illuminate\Support\Str;

trait ItemsTrait
{
    // Yeni şema alanı için form
    public $newField = [
        'name' => '',
        'label' => '',
        'type' => 'text',
        'required' => false,
        'options' => [],
        'options_array' => []
    ];
    
    public $newOption = [
        'key' => '',
        'value' => ''
    ];
    
    public $optionFormat = 'key-value'; // 'key-value' veya 'text'
    
    // Kullanılabilir alan tipleri
    public $availableTypes = [
        'text' => 'Metin',
        'textarea' => 'Uzun Metin',
        'number' => 'Sayı',
        'select' => 'Seçim Kutusu',
        'checkbox' => 'Onay Kutusu',
        'file' => 'Dosya',
        'image' => 'Resim',
        'image_multiple' => 'Çoklu Resim',
        'color' => 'Renk',
        'date' => 'Tarih',
        'email' => 'E-posta',
        'tel' => 'Telefon',
        'url' => 'URL',
        'time' => 'Saat',
    ];
    
    // Format değişikliğinde verilerin sağlıklı aktarımı
    public function updatedOptionFormat($value)
    {
        if ($value === 'text') {
            // options_array'den options'a dönüştür - string olarak ayarla
            if (empty($this->newField['options_array'])) {
                $this->newField['options'] = '';
                return;
            }
            
            $options = [];
            foreach ($this->newField['options_array'] as $option) {
                if (isset($option['key']) && isset($option['value'])) {
                    $options[] = $option['key'] . '=' . $option['value'];
                }
            }
            
            // String formatında ayarla
            $this->newField['options'] = implode("\n", $options);
        } 
        elseif ($value === 'key-value') {        
            // Eğer options bir string değilse veya boşsa, boş bir string olarak ayarla
            if (empty($this->newField['options']) || !is_string($this->newField['options'])) {
                $this->newField['options'] = '';
                $this->newField['options_array'] = []; // options boşsa, array'i de temizle
                return; 
            }
            
            // options'dan options_array'e dönüştür
            $parsedOptionsArray = []; // Geçici dizi
            $lines = explode("\n", $this->newField['options']);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $id = Str::random(6); // Yeni ID oluştur
                    
                    // Anahtar=Değer formatında mı kontrol et
                    if (strpos($line, '=') !== false) {
                        list($key, $value) = explode('=', $line, 2);
                        $parsedOptionsArray[$id] = [
                            'key' => trim($key),
                            'value' => trim($value)
                        ];
                    } else {
                        // Sadece değer varsa, anahtar olarak slugını al
                        $parsedOptionsArray[$id] = [
                            'key' => Str::slug($line, '_'),
                            'value' => $line
                        ];
                    }
                }
            }
            
            // Parse edilen array ile güncelle
            $this->newField['options_array'] = $parsedOptionsArray;
        }
    }
    
    public function addItemSchemaField()
    {
        $this->validate([
            'newField.name' => 'required|regex:/^[a-zA-Z0-9_]+$/i',
            'newField.label' => 'required',
            'newField.type' => 'required'
        ]);
        
        // Sistem alanı ise düzenlenemez
        $systemSpecialFields = ['title', 'is_active', 'unique_id'];
        if (in_array($this->newField['name'], $systemSpecialFields)) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Bu alan ismi sistem tarafından ayrılmıştır ve kullanılamaz.',
                'type' => 'error'
            ]);
            return;
        }
        
        $field = [
            'name' => $this->newField['name'],
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
        
        $itemSchema = $this->widget['item_schema'] ?? [];
        $itemSchema[] = $field;
        
        $this->widget['item_schema'] = $itemSchema;
        
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
    
    public function removeItemSchemaField($index)
    {
        $itemSchema = $this->widget['item_schema'] ?? [];
        
        // Sistem alanları silinemez
        if (isset($itemSchema[$index]) && isset($itemSchema[$index]['system']) && $itemSchema[$index]['system']) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Sistem alanları silinemez.',
                'type' => 'error'
            ]);
            return;
        }
        
        if (isset($itemSchema[$index])) {
            unset($itemSchema[$index]);
            $this->widget['item_schema'] = array_values($itemSchema);
        }
    }
    
    // Select için option ekle
    public function addFieldOption()
    {
        $id = Str::random(6);
        $this->newField['options_array'][$id] = [
            'key' => '',
            'value' => ''
        ];
    }
    
    // Select option'ı sil
    public function removeFieldOption($key)
    {
        if (isset($this->newField['options_array'][$key])) {
            unset($this->newField['options_array'][$key]);
        }
    }
    
    // Seçenek değerini otomatik slug yapma
    public function slugifyOptionKey($id, $value)
    {
        if (isset($this->newField['options_array'][$id])) {
            $this->newField['options_array'][$id]['key'] = Str::slug($value, '_');
        }
    }
}