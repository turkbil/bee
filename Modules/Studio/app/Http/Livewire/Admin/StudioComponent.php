<?php
namespace Modules\Studio\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layout')]
class StudioComponent extends Component
{
    use WithPagination;
    
    public function mount()
    {
        // Başlangıç işlemleri
    }

    public function render()
    {
        return view('studio::admin.livewire.studio-component');
    }
}