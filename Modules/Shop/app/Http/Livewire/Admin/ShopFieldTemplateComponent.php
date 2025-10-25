<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Modules\Shop\App\Models\ShopProductFieldTemplate;

#[Layout('admin.layout')]
class ShopFieldTemplateComponent extends Component
{
    public $search = '';
    public $editingTemplateId = null;

    // Template form data
    public $name = '';
    public $description = '';
    public $fields = [];
    public $is_active = true;

    // Livewire Listeners
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'updateOrder' => 'updateOrder',
        'templateDeleted' => '$refresh',
    ];

    public function mount(): void
    {
        $this->resetForm();
    }

    #[Computed]
    public function templates()
    {
        $query = ShopProductFieldTemplate::query()->ordered();

        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw("LOWER(name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("LOWER(description) LIKE ?", ["%{$search}%"]);
            });
        }

        return $query->get();
    }

    public function toggleTemplateStatus(int $templateId): void
    {
        try {
            $template = ShopProductFieldTemplate::findOrFail($templateId);
            $template->is_active = !$template->is_active;
            $template->save();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('shop::admin.template_status_updated'),
                'type' => 'success',
            ]);

            $this->dispatch('refresh-sortable');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function deleteTemplate($templateId): void
    {
        try {
            $template = ShopProductFieldTemplate::findOrFail($templateId);
            $template->delete();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('shop::admin.template_deleted'),
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function updateOrder($list = null)
    {
        try {
            $items = $list;

            if (!is_array($items) || empty($items)) {
                return;
            }

            foreach ($items as $item) {
                if (isset($item['id']) && isset($item['order'])) {
                    ShopProductFieldTemplate::where('template_id', $item['id'])
                        ->update(['sort_order' => $item['order']]);
                }
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('shop::admin.order_updated'),
                'type' => 'success',
                'duration' => 3000
            ]);

            $this->dispatch('refresh-sortable');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function openDeleteModal(int $templateId, string $name)
    {
        $this->dispatch('showTemplateDeleteModal',
            module: 'shop',
            id: $templateId,
            title: $name
        );
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->description = '';
        $this->fields = [];
        $this->is_active = true;
    }

    public function render()
    {
        return view('shop::admin.livewire.field-template-component', [
            'templates' => $this->templates,
        ]);
    }
}
