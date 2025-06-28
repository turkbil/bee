<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class HeaderMenu extends Component
{
    public function render()
    {
        return view('livewire.auth.header-menu');
    }
    
    /**
     * Auth durumunu kontrol et - cache-safe
     */
    public function getIsGuestProperty()
    {
        return !Auth::check();
    }
    
    /**
     * User bilgilerini getir - cache-safe  
     */
    public function getUserProperty()
    {
        return Auth::user();
    }
    
    /**
     * Register route var mı kontrol et
     */
    public function getHasRegisterRouteProperty()
    {
        return Route::has('register');
    }
}
