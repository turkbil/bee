<?php

namespace App\Services\AI;

use App\Models\Tenant;
use App\Models\AITenantDirective;
use Modules\AI\App\Models\Flow;
use Illuminate\Support\Facades\Log;

/**
 * Simple Flow & Directive Copy Service
 *
 * Mevcut tenant'tan başka tenant'a flow ve directive kopyalar
 * Yeni tablo açmaz, mevcut yapıyı kullanır
 */
class SimpleFlowCopyService
{
    /**
     * Flow'u bir tenant'tan diğerine kopyala
     */
    public function copyFlow(int $fromTenantId, int $toTenantId): bool
    {
        try {
            Log::info("Copying flow from Tenant {$fromTenantId} to Tenant {$toTenantId}");

            // Source flow'u al (Central DB'den)
            $sourceFlow = Flow::where('tenant_id', $fromTenantId)
                ->where('status', 'active')
                ->orderBy('priority', 'asc')
                ->first();

            if (!$sourceFlow) {
                Log::warning("No active flow found for Tenant {$fromTenantId}");
                return false;
            }

            // Target tenant için mevcut flow'ları inactive yap
            Flow::where('tenant_id', $toTenantId)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);

            // Yeni flow oluştur (Central DB'de)
            $newFlow = Flow::create([
                'tenant_id' => $toTenantId,
                'name' => $sourceFlow->name . " (copied from Tenant {$fromTenantId})",
                'description' => $sourceFlow->description,
                'flow_data' => $sourceFlow->flow_data,
                'metadata' => array_merge(
                    $sourceFlow->metadata ?? [],
                    ['copied_from' => $fromTenantId, 'copied_at' => now()]
                ),
                'priority' => 10,
                'status' => 'active'
            ]);

            Log::info("Flow copied successfully. New flow ID: {$newFlow->id}");
            return true;

        } catch (\Exception $e) {
            Log::error("Error copying flow: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Directives'leri kopyala
     */
    public function copyDirectives(int $fromTenantId, int $toTenantId, bool $overwrite = false): int
    {
        // AITenantDirective model'inde zaten copyFromTenant methodu var!
        return AITenantDirective::copyFromTenant($fromTenantId, $toTenantId, $overwrite);
    }

    /**
     * Full AI setup: Flow + Directives
     */
    public function setupTenantAI(int $targetTenantId, int $templateTenantId = 2): array
    {
        $results = [
            'flow_copied' => false,
            'directives_copied' => 0,
            'errors' => []
        ];

        // 1. Flow kopyala
        $results['flow_copied'] = $this->copyFlow($templateTenantId, $targetTenantId);

        if (!$results['flow_copied']) {
            $results['errors'][] = "Flow could not be copied from Tenant {$templateTenantId}";
        }

        // 2. Directives kopyala
        $results['directives_copied'] = $this->copyDirectives($templateTenantId, $targetTenantId);

        if ($results['directives_copied'] == 0) {
            $results['errors'][] = "No directives copied from Tenant {$templateTenantId}";
        }

        // 3. Log results
        Log::info("Tenant {$targetTenantId} AI setup completed", $results);

        return $results;
    }

    /**
     * Global directive ekle/güncelle (tenant_id = 0)
     */
    public function setGlobalDirective(string $key, $value, string $type = 'string'): AITenantDirective
    {
        return AITenantDirective::setValue(
            0, // tenant_id = 0 means global
            $key,
            $value,
            $type
        );
    }

    /**
     * Global directive'i tüm tenant'lara kopyala
     */
    public function copyGlobalToAllTenants(string $key): int
    {
        $global = AITenantDirective::where('tenant_id', 0)
            ->where('directive_key', $key)
            ->first();

        if (!$global) {
            Log::warning("Global directive not found: {$key}");
            return 0;
        }

        $copied = 0;
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Eğer tenant'ta yoksa ekle
            $exists = AITenantDirective::where('tenant_id', $tenant->id)
                ->where('directive_key', $key)
                ->exists();

            if (!$exists) {
                AITenantDirective::create([
                    'tenant_id' => $tenant->id,
                    'directive_key' => $key,
                    'directive_value' => $global->directive_value,
                    'directive_type' => $global->directive_type,
                    'category' => $global->category,
                    'description' => $global->description . ' (from global)',
                    'is_active' => true
                ]);
                $copied++;
            }
        }

        Log::info("Global directive '{$key}' copied to {$copied} tenants");
        return $copied;
    }
}