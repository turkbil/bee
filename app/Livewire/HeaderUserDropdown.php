<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class HeaderUserDropdown extends Component
{
    public $user;

    protected $listeners = [
        'avatar-updated' => 'refreshUser',
        'refreshComponent' => '$refresh'
    ];

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function refreshUser()
    {
        $this->user = Auth::user()->fresh();
    }

    public function render()
    {
        return view('livewire.header-user-dropdown');
    }
}