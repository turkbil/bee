<?php

namespace Modules\Blog\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class BlogAiGuideComponent extends Component
{
    public function mount()
    {
        // Root kontrolü (user_id = 1)
        if (!auth()->check() || auth()->user()->id !== 1) {
            abort(403, 'Bu sayfaya sadece root kullanıcılar erişebilir.');
        }
    }

    public function render()
    {
        return view('blog::admin.ai-guide');
    }
}
