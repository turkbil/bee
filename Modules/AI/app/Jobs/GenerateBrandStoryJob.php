<?php

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\AI\App\Models\AITenantProfile;
use Illuminate\Support\Facades\Log;

class GenerateBrandStoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $profile;
    public $timeout = 900; // 15 dakika

    public function __construct(AITenantProfile $profile)
    {
        $this->profile = $profile;
    }

    public function handle()
    {
        Log::info('Brand story job başlatılıyor', ['profile_id' => $this->profile->id]);
        
        try {
            // Profil hala tamamlanmış ve brand story yoksa oluştur
            if ($this->profile->is_completed && !$this->profile->hasBrandStory()) {
                $this->profile->generateBrandStory();
                Log::info('Brand story başarıyla oluşturuldu', ['profile_id' => $this->profile->id]);
            }
        } catch (\Exception $e) {
            Log::error('Brand story job hatası: ' . $e->getMessage(), [
                'profile_id' => $this->profile->id,
                'error' => $e->getMessage()
            ]);
            
            // Job'u tekrar çalıştır (maksimum 3 kez)
            if ($this->attempts() < 3) {
                throw $e;
            }
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Brand story job tamamen başarısız oldu', [
            'profile_id' => $this->profile->id,
            'error' => $exception->getMessage()
        ]);
    }
}