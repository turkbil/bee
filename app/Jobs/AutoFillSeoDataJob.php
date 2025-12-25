<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AI\AutoSeoFillService;
use Illuminate\Support\Facades\Log;

/**
 * Auto Fill SEO Data Job
 *
 * Premium tenant'lar için arka planda otomatik SEO üretimi
 * Sayfa hızını etkilemeden AI ile SEO başlık/açıklama oluşturur
 *
 * @author Claude Code
 * @version 1.0.0
 * @date 2025-12-25
 */
class AutoFillSeoDataJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Job timeout (seconds)
     */
    public $timeout = 120;

    /**
     * Max retry attempts
     */
    public $tries = 2;

    /**
     * @var string Model class name
     */
    protected $modelClass;

    /**
     * @var int Model ID
     */
    protected $modelId;

    /**
     * @var string Locale (tr, en, etc.)
     */
    protected $locale;

    /**
     * @var int Tenant ID
     */
    protected $tenantId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $modelClass, int $modelId, string $locale, int $tenantId)
    {
        $this->modelClass = $modelClass;
        $this->modelId = $modelId;
        $this->locale = $locale;
        $this->tenantId = $tenantId;
    }

    /**
     * Execute the job.
     */
    public function handle(AutoSeoFillService $autoSeoFillService): void
    {
        try {
            // Tenant'ı initialize et
            $tenant = \App\Models\Tenant::find($this->tenantId);
            if (!$tenant) {
                Log::warning('AutoFillSeoDataJob: Tenant bulunamadı', [
                    'tenant_id' => $this->tenantId
                ]);
                return;
            }

            tenancy()->initialize($tenant);

            // Model'i bul
            if (!class_exists($this->modelClass)) {
                Log::warning('AutoFillSeoDataJob: Model class bulunamadı', [
                    'class' => $this->modelClass
                ]);
                return;
            }

            $model = $this->modelClass::find($this->modelId);
            if (!$model) {
                Log::warning('AutoFillSeoDataJob: Model bulunamadı', [
                    'class' => $this->modelClass,
                    'id' => $this->modelId
                ]);
                return;
            }

            // Hala SEO üretmeye ihtiyaç var mı kontrol et
            if (!$autoSeoFillService->shouldAutoFill($model, $this->locale)) {
                Log::info('AutoFillSeoDataJob: SEO zaten dolu, üretilmeyecek', [
                    'model' => $this->modelClass,
                    'id' => $this->modelId,
                    'locale' => $this->locale
                ]);
                return;
            }

            // SEO üret
            Log::info('🎯 AutoFillSeoDataJob: SEO üretimi başlatıldı', [
                'tenant' => $this->tenantId,
                'model' => $this->modelClass,
                'id' => $this->modelId,
                'locale' => $this->locale
            ]);

            $seoData = $autoSeoFillService->autoFillSeoData($model, $this->locale);

            if ($seoData) {
                $autoSeoFillService->saveSeoData($model, $seoData, $this->locale);

                Log::info('✅ AutoFillSeoDataJob: SEO başarıyla üretildi', [
                    'model' => $this->modelClass,
                    'id' => $this->modelId,
                    'locale' => $this->locale
                ]);
            } else {
                Log::warning('⚠️ AutoFillSeoDataJob: AI SEO üretemedi', [
                    'model' => $this->modelClass,
                    'id' => $this->modelId
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ AutoFillSeoDataJob: Hata oluştu', [
                'error' => $e->getMessage(),
                'model' => $this->modelClass,
                'id' => $this->modelId,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Job'ı retry mekanizmasına bırak
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ AutoFillSeoDataJob: Job başarısız oldu', [
            'model' => $this->modelClass,
            'id' => $this->modelId,
            'locale' => $this->locale,
            'error' => $exception->getMessage()
        ]);
    }
}
