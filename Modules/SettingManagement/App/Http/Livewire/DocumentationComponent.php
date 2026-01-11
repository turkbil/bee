<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class DocumentationComponent extends Component
{
    public function mount()
    {
        // Root kontrolü
        if (!auth()->user()->hasRole('root')) {
            abort(403, 'Bu sayfaya sadece root kullanıcılar erişebilir.');
        }
    }

    public function render()
    {
        return view('settingmanagement::documentation');
    }
}
