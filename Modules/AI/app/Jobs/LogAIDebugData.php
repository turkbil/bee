<?php

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AI DEBUG DATA LOGGING JOB
 * 
 * Bu job AI priority engine'den gelen debug verilerini
 * ai_tenant_debug_logs tablosuna asenkron olarak kaydeder.
 * 
 * Performance için queue ile çalışır.
 */
class LogAIDebugData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;
    public $backoff = [5, 10, 30]; // Retry delays

    protected array $debugData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $debugData)
    {
        $this->debugData = $debugData;
        
        // Queue configuration
        $this->onQueue('ai-debug'); // Ayrı queue kullan
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Tenant context'i restore et (eğer var ise)
            $tenantId = $this->debugData['tenant_id'] ?? null;
            
            if ($tenantId && $tenantId !== 'default' && class_exists('\App\Models\Tenant')) {
                $tenant = \App\Models\Tenant::find($tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            // Input preview oluştur (privacy-safe)
            $inputPreview = $this->debugData['input_preview'] ?? null;
            if (!$inputPreview && isset($this->debugData['user_input'])) {
                $inputPreview = substr(strip_tags($this->debugData['user_input']), 0, 100);
            }

            // Response preview oluştur
            $responsePreview = $this->debugData['response_preview'] ?? null;
            if (!$responsePreview && isset($this->debugData['response'])) {
                $responsePreview = substr(strip_tags($this->debugData['response']), 0, 200);
            }

            // Database'e kaydet
            DB::table('ai_tenant_debug_logs')->insert([
                'tenant_id' => $this->debugData['tenant_id'],
                'user_id' => $this->debugData['user_id'],
                'session_id' => $this->debugData['session_id'] ?? session()->getId(),
                'feature_slug' => $this->debugData['feature_slug'],
                'request_type' => $this->debugData['request_type'],
                'context_type' => $this->debugData['context_type'],
                
                // Prompt Analysis (JSON data)
                'prompts_analysis' => json_encode($this->debugData['prompts_analysis']),
                'scoring_summary' => json_encode($this->debugData['scoring_summary']),
                
                // Quick stats
                'threshold_used' => $this->debugData['threshold_used'],
                'total_available_prompts' => $this->debugData['total_available_prompts'],
                'actually_used_prompts' => $this->debugData['actually_used_prompts'],
                'filtered_prompts' => $this->debugData['filtered_prompts'],
                'highest_score' => $this->debugData['highest_score'],
                'lowest_used_score' => $this->debugData['lowest_used_score'],
                
                // Performance
                'execution_time_ms' => $this->debugData['execution_time_ms'],
                'response_length' => $this->debugData['response_length'] ?? null,
                'token_usage' => $this->debugData['token_usage'] ?? null,
                'cost_estimate' => $this->debugData['cost_estimate'] ?? null,
                
                // Input/Output previews
                'input_hash' => isset($this->debugData['user_input']) ? md5($this->debugData['user_input']) : null,
                'input_preview' => $inputPreview,
                'response_preview' => $responsePreview,
                'response_quality' => $this->assessResponseQuality(),
                
                // Technical info
                'ai_model' => $this->debugData['ai_model'],
                'ip_address' => $this->debugData['ip_address'],
                'user_agent' => $this->debugData['user_agent'],
                'request_headers' => json_encode($this->debugData['request_headers'] ?? []),
                
                // Error tracking
                'has_error' => $this->debugData['has_error'],
                'error_message' => $this->debugData['error_message'] ?? null,
                'error_details' => isset($this->debugData['error_details']) ? json_encode($this->debugData['error_details']) : null,
                
                'created_at' => $this->debugData['created_at'] ?? now(),
                'updated_at' => now()
            ]);

            Log::info('🎯 AI Debug data logged successfully', [
                'tenant_id' => $this->debugData['tenant_id'],
                'feature_slug' => $this->debugData['feature_slug'],
                'used_prompts' => $this->debugData['actually_used_prompts'],
                'execution_time' => $this->debugData['execution_time_ms']
            ]);

        } catch (\Exception $e) {
            Log::error('❌ AI Debug logging failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $this->debugData['tenant_id'] ?? 'unknown',
                'feature_slug' => $this->debugData['feature_slug'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle failed job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('🚨 AI Debug logging job failed permanently', [
            'tenant_id' => $this->debugData['tenant_id'] ?? 'unknown',
            'feature_slug' => $this->debugData['feature_slug'] ?? 'unknown',
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Response kalitesini otomatik değerlendir
     */
    private function assessResponseQuality(): ?string
    {
        $usedPrompts = $this->debugData['actually_used_prompts'] ?? 0;
        $executionTime = $this->debugData['execution_time_ms'] ?? 0;
        $hasError = $this->debugData['has_error'] ?? false;

        if ($hasError) {
            return 'poor';
        }

        if ($usedPrompts >= 8 && $executionTime < 3000) {
            return 'excellent';
        } elseif ($usedPrompts >= 5 && $executionTime < 5000) {
            return 'good';
        } elseif ($usedPrompts >= 3) {
            return 'average';
        } else {
            return 'poor';
        }
    }

    /**
     * Get unique job identifier
     */
    public function uniqueId(): string
    {
        return 'ai-debug-' . ($this->debugData['session_id'] ?? 'unknown') . '-' . time();
    }
}