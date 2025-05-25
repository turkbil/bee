<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Services\WidgetService;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class WidgetSettingsComponent extends Component
{
    use WithFileUploads;
    
    public $tenantWidgetId;
    public $formData = [];
    public $schema = [];
    public $tenantWidget;
    public $temporaryUpload = [];
    
    protected $widgetService;
    
    public function boot(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }
    
    public function mount($tenantWidgetId)
    {
        $this->tenantWidgetId = $tenantWidgetId;
        $this->tenantWidget = TenantWidget::with('widget')->findOrFail($tenantWidgetId);
        
        $this->schema = $this->tenantWidget->widget->getSettingsSchema();
        $this->formData = $this->tenantWidget->settings ?? [];
        
        $this->processSchema();
        $this->initializeFormDataFromSchema();
        
        if (!isset($this->formData['unique_id'])) {
            $this->formData['unique_id'] = (string) Str::uuid();
        }
        
        if (!isset($this->formData['title'])) {
            $this->formData['title'] = $this->tenantWidget->widget->name;
        }
    }
    
    protected function processSchema()
    {
        if (!is_array($this->schema)) {
            $this->schema = [];
        }
        
        $this->schema = array_filter($this->schema ?? [], function($field) {
            if (!isset($field['name'])) return true;
            return $field['name'] !== 'unique_id' && $field['name'] !== 'id';
        });
        
        $hasTitle = false;
        $hasUniqueId = false;
        
        foreach ($this->schema as $field) {
            if (!isset($field['name'])) continue;
            
            if ($field['name'] === 'title') $hasTitle = true;
            if ($field['name'] === 'unique_id') $hasUniqueId = true;
        }
        
        if (!$hasTitle) {
            array_unshift($this->schema, [
                'name' => 'title',
                'label' => 'Widget Başlığı',
                'type' => 'text',
                'required' => true,
                'system' => true,
                'properties' => [
                    'width' => 12,
                    'placeholder' => 'Widget başlığını giriniz',
                    'default_value' => $this->tenantWidget->widget->name
                ]
            ]);
        }
        
        if (!$hasUniqueId) {
            $this->schema[] = [
                'name' => 'unique_id',
                'label' => 'Benzersiz ID',
                'type' => 'text',
                'required' => false,
                'system' => true,
                'hidden' => true,
                'properties' => [
                    'width' => 12
                ]
            ];
        }
    }
    
    protected function initializeFormDataFromSchema()
    {
        foreach ($this->schema as $field) {
            if (!isset($field['name']) || !isset($field['type'])) continue;
            
            $fieldName = $field['name'];
            
            if (!isset($this->formData[$fieldName])) {
                if (isset($field['properties']['default_value'])) {
                    $this->formData[$fieldName] = $field['properties']['default_value'];
                } elseif (isset($field['default'])) {
                    $this->formData[$fieldName] = $field['default'];
                } else {
                    switch ($field['type']) {
                        case 'checkbox':
                        case 'switch':
                            $this->formData[$fieldName] = false;
                            break;
                        case 'number':
                            $this->formData[$fieldName] = 0;
                            break;
                        case 'image':
                        case 'file':
                            $this->formData[$fieldName] = null;
                            break;
                        case 'select':
                            if (isset($field['options']) && is_array($field['options'])) {
                                $firstOption = array_key_first($field['options']);
                                $this->formData[$fieldName] = $firstOption;
                            } else {
                                $this->formData[$fieldName] = '';
                            }
                            break;
                        default:
                            $this->formData[$fieldName] = '';
                            break;
                    }
                }
            }
        }
        
        $this->processNestedFields($this->schema);
    }
    
    protected function processNestedFields($schema)
    {
        foreach ($schema as $field) {
            if (!isset($field['type'])) continue;
            
            if ($field['type'] === 'row' && isset($field['columns'])) {
                foreach ($field['columns'] as $column) {
                    if (isset($column['elements']) && is_array($column['elements'])) {
                        $this->processNestedFields($column['elements']);
                    }
                }
            } elseif ($field['type'] === 'card' && isset($field['elements'])) {
                $this->processNestedFields($field['elements']);
            } elseif ($field['type'] === 'tab_group' && isset($field['properties']['tabs'])) {
                foreach ($field['properties']['tabs'] as $tab) {
                    if (isset($tab['elements']) && is_array($tab['elements'])) {
                        $this->processNestedFields($tab['elements']);
                    }
                }
            } else {
                if (isset($field['name']) && !isset($this->formData[$field['name']])) {
                    $fieldName = $field['name'];
                    
                    if (isset($field['properties']['default_value'])) {
                        $this->formData[$fieldName] = $field['properties']['default_value'];
                    } elseif (isset($field['default'])) {
                        $this->formData[$fieldName] = $field['default'];
                    } else {
                        switch ($field['type']) {
                            case 'checkbox':
                            case 'switch':
                                $this->formData[$fieldName] = false;
                                break;
                            case 'number':
                                $this->formData[$fieldName] = 0;
                                break;
                            case 'image':
                            case 'file':
                                $this->formData[$fieldName] = null;
                                break;
                            case 'select':
                                if (isset($field['options']) && is_array($field['options'])) {
                                    $firstOption = array_key_first($field['options']);
                                    $this->formData[$fieldName] = $firstOption;
                                } else {
                                    $this->formData[$fieldName] = '';
                                }
                                break;
                            default:
                                $this->formData[$fieldName] = '';
                                break;
                        }
                    }
                }
            }
        }
    }
    
    public function save($redirect = false, $resetForm = false)
    {
        $rules = [];
        
        foreach ($this->schema as $field) {
            if (!isset($field['name']) || !isset($field['type'])) continue;
            if ($field['type'] === 'row') continue;
            
            if (isset($field['required']) && $field['required'] && $field['name'] !== 'unique_id' && $field['name'] !== 'id') {
                $rules['formData.' . $field['name']] = 'required';
            }
        }
        
        $this->validate($rules);

        foreach ($this->temporaryUpload as $fieldName => $upload) {
            if ($upload) {
                $tenantId = tenant()->id ?? 'central';
                $filename = time() . '_' . Str::slug($fieldName) . '.' . $upload->getClientOriginalExtension();
                $path = $upload->storeAs("widgets/{$tenantId}/settings", $filename, 'public');
                $this->formData[$fieldName] = asset('storage/' . $path);
            }
        }

        if (!isset($this->formData['unique_id'])) {
            $this->formData['unique_id'] = (string) Str::uuid();
        }
        
        $this->tenantWidget->update([
            'settings' => $this->formData
        ]);
        
        $this->widgetService->clearWidgetCache(tenant()->id ?? null, $this->tenantWidgetId);
        
        if ($redirect) {
            session()->flash('toast', [
                'title' => 'Başarılı!',
                'message' => 'Widget ayarları kaydedildi.',
                'type' => 'success'
            ]);
            return redirect()->route('admin.widgetmanagement.index');
        }
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Widget ayarları kaydedildi.',
            'type' => 'success'
        ]);
        
        $this->dispatch('widgetSettingsUpdated');
    }
    
    public function render()
    {
        return view('widgetmanagement::livewire.widget-settings-component');
    }
}