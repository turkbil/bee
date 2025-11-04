<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HeaderUserDropdown extends Component
{
    public $user;
    public $userAvatar = null;

    public function mount()
    {
        $this->user = Auth::user();

        // Avatar'ı güvenli şekilde yükle - sadece tenant varsa
        if ($this->user && tenant()) {
            try {
                // Tenant varsa media sorgula
                $avatar = $this->user->getFirstMedia('avatar');
                if ($avatar) {
                    $this->userAvatar = $avatar->getUrl() . '?v=' . time();
                }
            } catch (\Exception $e) {
                // Hata durumunda avatar'ı null bırak
                Log::warning('HeaderUserDropdown: Avatar yüklenemedi - ' . $e->getMessage());
                $this->userAvatar = null;
            }
        }
    }

    public function render()
    {
        return view('livewire.header-user-dropdown');
    }
}