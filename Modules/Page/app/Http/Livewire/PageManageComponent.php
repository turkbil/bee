<?php
namespace Modules\Page\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Modules\Page\App\Models\Page;

#[Layout('admin.layout')]
class PageManageComponent extends Component
{
    public ?Page $page = null;

    #[Rule('required|min:3|max:255')]
    public string $title = '';

    #[Rule('required|max:255')]
    public string $slug = '';

    public ?string $body = '';
    public ?string $css = '';
    public ?string $js = '';
    public ?string $metakey = '';
    public ?string $metadesc = '';
    public bool $is_active = true;

    public function mount(?Page $page = null): void
    {
        if ($page) {
            $this->page = $page;
            $this->fill($page->toArray());
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'tenant_id' => tenancy()->tenant->id,
            'title'     => $this->title,
            'slug'      => $this->slug,
            'body'      => $this->body,
            'css'       => $this->css,
            'js'        => $this->js,
            'metakey'   => $this->metakey,
            'metadesc'  => $this->metadesc,
            'is_active' => $this->is_active,
        ];

        if ($this->page) {
            $this->page->update($data);
            $action = 'güncellendi';
        } else {
            $this->page = Page::create($data);
            $action     = 'oluşturuldu';
        }

        log_activity('Sayfa', $action, $this->page);

        $this->dispatch('showToast', [
            'type'    => 'success',
            'message' => 'Sayfa başarıyla ' . $action,
        ]);

        $this->redirectRoute('admin.page.index');
    }

    public function render()
    {
        return view('page::livewire.page-manage-component');
    }
}
