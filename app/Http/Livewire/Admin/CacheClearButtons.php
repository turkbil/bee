<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Spatie\ResponseCache\Facades\ResponseCache;

class CacheClearButtons extends Component
{
    public $type = 'clear'; // 'clear' or 'all'
    public $mobile = false;
    public $isClearing = false;

    public function mount($type = 'clear', $mobile = false)
    {
        $this->type = $type;
        $this->mobile = $mobile;
    }

    public function clearCache()
    {        
        try {
            if ($this->type === 'all') {
                // All cache clear commands
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                
                // Response Cache temizle - KRİTİK!
                ResponseCache::clear();
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı',
                    'message' => 'Sistem cache temizlendi',
                    'type' => 'success'
                ]);
            } else {
                // Basic cache clear
                Cache::flush();
                
                // Response Cache de temizle - Her durumda!
                ResponseCache::clear();
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı',
                    'message' => 'Cache temizlendi (ResponseCache dahil)',
                    'type' => 'success'
                ]);
            }
            
            // Kısa delay ekle loading göstermek için
            usleep(500000); // 0.5 saniye
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Cache temizleme başarısız',
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.cache-clear-buttons');
    }
}