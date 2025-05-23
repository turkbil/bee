<?php

namespace Modules\WidgetManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Modules\WidgetManagement\App\Models\Widget;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class WidgetCodeEditorComponent extends Component
{
    #[Url]
    public $widgetId;
    public $widget = [];
    public $isSubmitting = false;
    
    protected $rules = [
        'widget.content_html' => 'nullable',
        'widget.content_css' => 'nullable',
        'widget.content_js' => 'nullable',
    ];
    
    public function mount($id)
    {
        $this->widgetId = $id;
        $widget = Widget::findOrFail($id);
        
        $this->widget = [
            'name' => $widget->name,
            'type' => $widget->type,
            'content_html' => $widget->content_html ?? '',
            'content_css' => $widget->content_css ?? '',
            'content_js' => $widget->content_js ?? '',
            'css_files' => $widget->css_files ?? [],
            'js_files' => $widget->js_files ?? [],
            'settings_schema' => $widget->settings_schema ?? [],
            'item_schema' => $widget->item_schema ?? [],
            'has_items' => $widget->has_items ?? false
        ];
    }
    
    public function addCssFile()
    {
        $cssFiles = $this->widget['css_files'] ?? [];
        $cssFiles[] = '';
        $this->widget['css_files'] = $cssFiles;
    }

    public function removeCssFile($index)
    {
        $cssFiles = $this->widget['css_files'];
        unset($cssFiles[$index]);
        $this->widget['css_files'] = array_values($cssFiles);
    }

    public function addJsFile()
    {
        $jsFiles = $this->widget['js_files'] ?? [];
        $jsFiles[] = '';
        $this->widget['js_files'] = $jsFiles;
    }

    public function removeJsFile($index)
    {
        $jsFiles = $this->widget['js_files'];
        unset($jsFiles[$index]);
        $this->widget['js_files'] = array_values($jsFiles);
    }
    
    public function getAvailableVariables()
    {
        $variables = [];
        
        if (!empty($this->widget['settings_schema'])) {
            $variables['settings'] = $this->extractFieldsFromSchema($this->widget['settings_schema']);
        }
        
        if ($this->widget['has_items'] && !empty($this->widget['item_schema'])) {
            $variables['items'] = $this->extractFieldsFromSchema($this->widget['item_schema']);
        }
        
        return $variables;
    }
    
    private function extractFieldsFromSchema($schema)
    {
        $fields = [];
        
        foreach ($schema as $field) {
            if (!isset($field['type'])) {
                continue;
            }
            
            // Tasarım elementlerini hariç tut
            $designElements = ['row', 'card', 'tab_group'];
            if (in_array($field['type'], $designElements)) {
                // İç elementleri kontrol et
                if (isset($field['elements']) && is_array($field['elements'])) {
                    $nestedFields = $this->extractFieldsFromSchema($field['elements']);
                    $fields = array_merge($fields, $nestedFields);
                }
                
                // Tab group için sekmelerdeki elementleri kontrol et
                if ($field['type'] === 'tab_group' && isset($field['properties']['tabs'])) {
                    foreach ($field['properties']['tabs'] as $tab) {
                        if (isset($tab['elements']) && is_array($tab['elements'])) {
                            $tabFields = $this->extractFieldsFromSchema($tab['elements']);
                            $fields = array_merge($fields, $tabFields);
                        }
                    }
                }
                
                // Row için kolonlardaki elementleri kontrol et
                if ($field['type'] === 'row' && isset($field['columns'])) {
                    foreach ($field['columns'] as $column) {
                        if (isset($column['elements']) && is_array($column['elements'])) {
                            $columnFields = $this->extractFieldsFromSchema($column['elements']);
                            $fields = array_merge($fields, $columnFields);
                        }
                    }
                }
                
                continue;
            }
            
            // Name alanı olan ve gizli olmayan elementleri ekle
            if (isset($field['name']) && !empty($field['name']) && 
                (!isset($field['hidden']) || !$field['hidden'])) {
                $fields[] = [
                    'name' => $field['name'],
                    'label' => $field['label'] ?? 'Tanımsız',
                    'type' => $field['type'] ?? 'text'
                ];
            }
        }
        
        return $fields;
    }
    
    public function save()
    {
        if ($this->isSubmitting) {
            return;
        }
        
        $this->isSubmitting = true;
        
        $this->validate();
        
        try {
            $widget = Widget::findOrFail($this->widgetId);
            $widget->update([
                'content_html' => $this->widget['content_html'],
                'content_css' => $this->widget['content_css'],
                'content_js' => $this->widget['content_js'],
                'css_files' => $this->widget['css_files'],
                'js_files' => $this->widget['js_files'],
            ]);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Widget kodu kaydedildi.',
                'type' => 'success'
            ]);

            $this->js('window.updateWidgetEditors(' . json_encode($this->widget) . ')');

            if (function_exists('log_activity')) {
                log_activity($widget, 'widget kod editörü güncellendi');
            }
            
        } catch (\Exception $e) {
            Log::error('Widget kod kaydetme hatası: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Widget kodu kaydedilirken bir hata oluştu.',
                'type' => 'error'
            ]);
        }
        
        $this->isSubmitting = false;
    }
    
    public function render()
    {
        return view('widgetmanagement::livewire.widget-manage.widget-code-editor', [
            'title' => 'Widget Kod Editörü'
        ]);
    }
}