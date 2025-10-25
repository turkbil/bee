<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Modules\Shop\App\Models\ShopProductFieldTemplate;

#[Layout('admin.layout')]
class ShopFieldTemplateManageComponent extends Component
{
    public $templateId;

    // Template form data
    public $name = '';
    public $description = '';
    public $fields = [];
    public $is_active = true;

    // Livewire Listeners
    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function boot()
    {
        view()->share('pretitle', __('shop::admin.field_templates'));
        view()->share('title', $this->templateId
            ? __('shop::admin.edit_template')
            : __('shop::admin.new_template')
        );
    }

    public function mount($id = null)
    {
        $this->boot();

        if ($id) {
            $this->templateId = $id;
            $this->loadTemplateData($id);
        } else {
            $this->initializeEmptyFields();
        }
    }

    #[Computed]
    public function currentTemplate()
    {
        if (!$this->templateId) {
            return null;
        }

        return ShopProductFieldTemplate::query()->find($this->templateId);
    }

    protected function loadTemplateData($id)
    {
        $template = ShopProductFieldTemplate::find($id);

        if ($template) {
            $this->name = $template->name;
            $this->description = $template->description;
            $this->fields = $template->fields ?? [];
            $this->is_active = $template->is_active;
        }
    }

    protected function initializeEmptyFields()
    {
        $this->fields = [
            ['name' => '', 'type' => 'input', 'order' => 1]
        ];
    }

    public function addField()
    {
        $maxOrder = count($this->fields) > 0
            ? max(array_column($this->fields, 'order'))
            : 0;

        $this->fields[] = [
            'name' => '',
            'type' => 'input',
            'order' => $maxOrder + 1
        ];
    }

    public function removeField($index)
    {
        if (count($this->fields) > 1) {
            unset($this->fields[$index]);
            $this->fields = array_values($this->fields);

            // Reorder
            foreach ($this->fields as $key => $field) {
                $this->fields[$key]['order'] = $key + 1;
            }
        } else {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => __('shop::admin.minimum_one_field'),
                'type' => 'warning'
            ]);
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:191|unique:shop_product_field_templates,name,' . ($this->templateId ?? 'NULL') . ',template_id',
            'description' => 'nullable|string|max:1000',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:100',
            'fields.*.type' => 'required|in:input,textarea,checkbox',
            'fields.*.order' => 'required|integer',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Template adı zorunludur',
        'name.unique' => 'Bu template adı zaten kullanılıyor',
        'fields.required' => 'En az bir alan tanımlanmalıdır',
        'fields.*.name.required' => 'Alan adı zorunludur',
        'fields.*.type.required' => 'Alan tipi seçilmelidir',
    ];

    public function save($redirect = false)
    {
        try {
            $this->validate($this->rules(), $this->messages);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Doğrulama Hatası',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return;
        }

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'fields' => $this->fields,
            'is_active' => $this->is_active,
        ];

        if ($this->templateId) {
            $template = ShopProductFieldTemplate::query()->findOrFail($this->templateId);
            $template->update($data);

            $toast = [
                'title' => __('admin.success'),
                'message' => __('shop::admin.template_updated'),
                'type' => 'success'
            ];
        } else {
            // Sort order: son template'in sort_order'ından +1
            $lastOrder = ShopProductFieldTemplate::max('sort_order') ?? 0;
            $data['sort_order'] = $lastOrder + 1;

            $template = ShopProductFieldTemplate::query()->create($data);
            $this->templateId = $template->template_id;

            $toast = [
                'title' => __('admin.success'),
                'message' => __('shop::admin.template_created'),
                'type' => 'success'
            ];
        }

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.shop.field-templates.index');
        }

        $this->dispatch('toast', $toast);
    }

    public function deleteTemplate()
    {
        if (!$this->templateId) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('shop::admin.template_not_found'),
                'type' => 'error'
            ]);
            return;
        }

        try {
            $template = ShopProductFieldTemplate::findOrFail($this->templateId);
            $template->delete();

            session()->flash('toast', [
                'title' => __('admin.success'),
                'message' => __('shop::admin.template_deleted'),
                'type' => 'success'
            ]);

            return redirect()->route('admin.shop.field-templates.index');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('shop::admin.livewire.field-template-manage-component');
    }
}
