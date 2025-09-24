<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AI\App\Models\AIProvider;
use Modules\AI\App\Models\AIModelCreditRate;
use Modules\AI\App\Services\CreditCalculatorService;

/**
 * Model Credit Rate Management Controller
 * 
 * PHASE 3 API endpoints for provider/model selection with credit information
 */
class ModelCreditRateController extends Controller
{
    public function __construct(
        private readonly CreditCalculatorService $creditCalculator
    ) {}

    /**
     * Get available providers with their models and credit rates
     */
    public function getProvidersWithModels(): JsonResponse
    {
        try {
            $providers = AIProvider::where('is_active', true)
                ->with(['modelCreditRates'])
                ->get()
                ->map(function ($provider) {
                    return [
                        'id' => $provider->id,
                        'name' => $provider->name,
                        'slug' => $provider->slug,
                        'models' => $this->getProviderModelsWithRates($provider)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $providers,
                'message' => 'Providers and models retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving providers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get models for specific provider with credit rates
     */
    public function getProviderModels(int $providerId): JsonResponse
    {
        try {
            $provider = AIProvider::with(['modelCreditRates'])->find($providerId);
            
            if (!$provider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider not found'
                ], 404);
            }

            $models = $this->getProviderModelsWithRates($provider);

            return response()->json([
                'success' => true,
                'data' => [
                    'provider' => [
                        'id' => $provider->id,
                        'name' => $provider->name,
                        'slug' => $provider->slug
                    ],
                    'models' => $models
                ],
                'message' => 'Models retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving models: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate credit cost for specific model and usage
     */
    public function calculateCreditCost(Request $request): JsonResponse
    {
        $request->validate([
            'provider_id' => 'required|integer|exists:ai_providers,id',
            'model_id' => 'sometimes|integer|exists:ai_provider_models,id',
            'model_name' => 'sometimes|string',
            'input_tokens' => 'required|integer|min:0',
            'output_tokens' => 'required|integer|min:0'
        ]);

        try {
            // Provider ID'den provider name'i al
            $provider = \Modules\AI\App\Models\AIProvider::find($request->integer('provider_id'));
            if (!$provider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider not found'
                ], 404);
            }

            // Model bilgilerini al - önce model_id, sonra model_name
            $modelName = '';
            if ($request->has('model_id')) {
                $modelRecord = \Modules\AI\App\Models\AIProviderModel::find($request->integer('model_id'));
                if (!$modelRecord) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Model not found'
                    ], 404);
                }
                $modelName = $modelRecord->model_name;
            } else {
                $modelName = (string) $request->string('model_name');
            }

            $cost = $this->creditCalculator->calculateCreditsForModel(
                $provider->name,
                $modelName,
                $request->integer('input_tokens'),
                $request->integer('output_tokens')
            );

            // Check if existing rate exists for form filling
            $existingRate = $provider->models()
                ->where('model_name', $modelName)
                ->first();

            $responseData = [
                'total_credits' => $cost,
                'input_tokens' => $request->integer('input_tokens'),
                'output_tokens' => $request->integer('output_tokens'),
                'provider_id' => $request->integer('provider_id'),
                'model_name' => $modelName
            ];

            // Add existing rate data for form filling
            if ($existingRate) {
                $responseData['existing_rate'] = [
                    'id' => $existingRate->id,
                    'input_cost' => (float) $existingRate->credit_per_1k_input_tokens,
                    'output_cost' => (float) $existingRate->credit_per_1k_output_tokens,
                    'input_rate' => (float) $existingRate->credit_per_1k_input_tokens,
                    'output_rate' => (float) $existingRate->credit_per_1k_output_tokens,
                    'markup_percentage' => (float) $existingRate->markup_percentage,
                    'base_cost' => (float) $existingRate->base_cost_usd ?? 0,
                    'base_cost_usd' => (float) $existingRate->base_cost_usd ?? 0,
                    'base_cost_per_request' => (float) $existingRate->base_cost_usd ?? 0,
                    'is_active' => (bool) $existingRate->is_active,
                    'notes' => $existingRate->notes ?? ''
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => 'Credit cost calculated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating cost: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compare models across providers for cost estimation
     */
    public function compareModels(Request $request): JsonResponse
    {
        $request->validate([
            'input_tokens' => 'required|integer|min:0',
            'output_tokens' => 'required|integer|min:0',
            'provider_ids' => 'nullable|array',
            'provider_ids.*' => 'integer|exists:ai_providers,id'
        ]);

        try {
            $inputTokens = $request->integer('input_tokens');
            $outputTokens = $request->integer('output_tokens');
            $providerIds = $request->array('provider_ids');

            $comparison = $this->creditCalculator->compareModelsAcrossProviders(
                $inputTokens,
                $outputTokens,
                $providerIds
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'comparison' => $comparison,
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'cheapest_option' => $this->findCheapestOption($comparison)
                ],
                'message' => 'Model comparison completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error comparing models: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tenant's current AI configuration
     */
    public function getTenantConfiguration(?int $tenantId = null): JsonResponse
    {
        try {
            // If no tenant ID provided, try to get from current tenant context
            if (!$tenantId && function_exists('tenant')) {
                $tenant = tenant();
                $tenantId = $tenant?->id;
            }

            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant ID required'
                ], 400);
            }

            $tenant = \App\Models\Tenant::find($tenantId);
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $data = json_decode($tenant->data, true) ?? [];
            $defaultAiModel = $data['default_ai_model'] ?? null;

            $configuration = [
                'tenant_id' => $tenant->id,
                'default_provider_id' => $tenant->default_ai_provider_id,
                'default_model' => $defaultAiModel,
                'provider_info' => null,
                'model_info' => null
            ];

            // Get provider information if set
            if ($tenant->default_ai_provider_id) {
                $provider = AIProvider::with(['modelCreditRates'])->find($tenant->default_ai_provider_id);
                if ($provider) {
                    $configuration['provider_info'] = [
                        'id' => $provider->id,
                        'name' => $provider->name,
                        'slug' => $provider->slug
                    ];

                    // Get model information if set
                    if ($defaultAiModel) {
                        $modelRate = $provider->getModelRate($defaultAiModel);
                        $configuration['model_info'] = [
                            'name' => $defaultAiModel,
                            'has_custom_rate' => (bool)$modelRate,
                            'input_cost_per_1k' => $modelRate?->input_cost_per_1k ?? 0,
                            'output_cost_per_1k' => $modelRate?->output_cost_per_1k ?? 0
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $configuration,
                'message' => 'Tenant configuration retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving tenant configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Get provider models with credit rates
     */
    private function getProviderModelsWithRates(AIProvider $provider): array
    {
        if (!$provider->available_models || !is_array($provider->available_models)) {
            return [];
        }

        return collect($provider->available_models)->map(function ($model, $key) use ($provider) {
            $modelName = is_string($model) ? $model : $key;
            $modelLabel = is_array($model) ? ($model['name'] ?? $key) : $model;
            
            // Get credit rate for this model
            $creditRate = $provider->getModelRate($modelName);
            
            $modelInfo = [
                'name' => $modelName,
                'label' => $modelLabel,
                'has_custom_rate' => (bool)$creditRate,
                'input_cost_per_1k' => $creditRate?->input_cost_per_1k ?? 0,
                'output_cost_per_1k' => $creditRate?->output_cost_per_1k ?? 0,
                'base_cost' => $creditRate?->base_cost ?? 0,
                'markup_percentage' => $creditRate?->markup_percentage ?? 0
            ];

            // Add original model info if available
            if (is_array($model)) {
                $modelInfo['original_data'] = $model;
            }

            return $modelInfo;
        })->values()->toArray();
    }

    /**
     * Helper: Find cheapest option from comparison
     */
    private function findCheapestOption(array $comparison): ?array
    {
        $cheapest = null;
        $lowestCost = PHP_FLOAT_MAX;

        foreach ($comparison as $providerId => $models) {
            foreach ($models as $model) {
                if ($model['total_credits'] < $lowestCost) {
                    $lowestCost = $model['total_credits'];
                    $cheapest = [
                        'provider_id' => $providerId,
                        'model_name' => $model['model_name'],
                        'total_credits' => $model['total_credits']
                    ];
                }
            }
        }

        return $cheapest;
    }

    // PHASE 4: Admin Panel Pages

    /**
     * Model Credit Rate ana yönetim sayfası
     */
    public function index()
    {
        $providers = AIProvider::with(['modelCreditRates'])->where('is_active', true)->get();
        
        $statistics = [
            'total_providers' => $providers->count(),
            'total_models' => $providers->sum(function($provider) {
                return is_array($provider->available_models) ? count($provider->available_models) : 0;
            }),
            'configured_rates' => AIModelCreditRate::count(),
            'avg_input_cost' => AIModelCreditRate::avg('credit_per_1k_input_tokens'),
            'avg_output_cost' => AIModelCreditRate::avg('credit_per_1k_output_tokens'),
        ];

        return view('ai::admin.credit-rates.index', compact('providers', 'statistics'));
    }

    /**
     * API endpoint for DataTable - Credit rates listesi için JSON response
     */
    public function apiIndex(): JsonResponse
    {
        try {
            $creditRates = AIModelCreditRate::with(['provider'])
                ->orderBy('provider_id')
                ->orderBy('model_name')
                ->get()
                ->map(function ($rate) {
                    return [
                        'id' => $rate->id,
                        'provider_name' => $rate->provider->name ?? 'N/A',
                        'provider_id' => $rate->provider_id,
                        'model_name' => $rate->model_name,
                        'input_cost' => round((float) $rate->credit_per_1k_input_tokens, 4),
                        'output_cost' => round((float) $rate->credit_per_1k_output_tokens, 4),
                        'total_cost_1k' => round((float) $rate->credit_per_1k_input_tokens + (float) $rate->credit_per_1k_output_tokens, 4),
                        'markup_percentage' => $rate->markup_percentage,
                        'is_active' => $rate->is_active,
                        'created_at' => $rate->created_at->format('d.m.Y H:i'),
                        'updated_at' => $rate->updated_at->format('d.m.Y H:i')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $creditRates,
                'total' => $creditRates->count(),
                'message' => 'Credit rates loaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading credit rates: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Credit rate düzenleme sayfası
     */
    public function manage(int $providerId, ?string $modelName = null)
    {
        $provider = AIProvider::with(['modelCreditRates'])->findOrFail($providerId);
        
        $modelRate = null;
        if ($modelName) {
            $modelRate = $provider->getModelRate($modelName);
        }

        $availableModels = [];
        if ($provider->available_models && is_array($provider->available_models)) {
            $availableModels = collect($provider->available_models)->map(function($model, $key) use ($provider) {
                $name = is_string($model) ? $model : $key;
                return [
                    'name' => $name,
                    'label' => is_array($model) ? ($model['name'] ?? $key) : $model,
                    'has_rate' => (bool)$provider->getModelRate($name)
                ];
            })->values()->toArray();
        }

        return view('ai::admin.credit-rates.manage', compact('provider', 'modelRate', 'modelName', 'availableModels'));
    }

    /**
     * Bulk import sayfası
     */
    public function importPage()
    {
        $providers = AIProvider::where('is_active', true)->get();
        return view('ai::admin.credit-rates.import', compact('providers'));
    }

    /**
     * Bulk import işlemi
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:ai_providers,id',
            'import_file' => 'required|file|mimes:csv,json'
        ]);

        // Bulk import özelliği henüz implementa edilmedi
        return back()->with('warning', 'Bulk import özelliği yakında eklenecek');
    }

    /**
     * Export işlemi
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $providerId = $request->get('provider_id');

        $query = AIModelCreditRate::with(['provider']);
        
        if ($providerId) {
            $query->where('provider_id', $providerId);
        }

        $rates = $query->get();

        if ($format === 'json') {
            return response()->json($rates);
        }

        // CSV export
        $filename = 'model-credit-rates-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\""
        ];

        return response()->stream(function() use ($rates) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Provider', 'Model', 'Input Cost/1K', 'Output Cost/1K', 'Base Cost', 'Markup %']);
            
            foreach ($rates as $rate) {
                fputcsv($handle, [
                    $rate->provider->name,
                    $rate->model_name,
                    $rate->input_cost_per_1k,
                    $rate->output_cost_per_1k,
                    $rate->base_cost,
                    $rate->markup_percentage
                ]);
            }
            
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Credit calculator dashboard
     */
    public function calculator()
    {
        $providers = AIProvider::with(['modelCreditRates'])->where('is_active', true)->get();
        return view('ai::admin.credit-rates.calculator', compact('providers'));
    }

    /**
     * Model performance analytics
     */
    public function analytics()
    {
        $usageStats = AIModelCreditRate::selectRaw('
            provider_id,
            COUNT(*) as model_count,
            AVG(credit_per_1k_input_tokens) as avg_input_cost,
            AVG(credit_per_1k_output_tokens) as avg_output_cost,
            SUM(base_cost_usd) as total_base_cost
        ')
        ->with(['provider'])
        ->groupBy('provider_id')
        ->get();

        $mostExpensiveModels = AIModelCreditRate::with(['provider'])
            ->orderByDesc('credit_per_1k_output_tokens')
            ->limit(10)
            ->get();

        $mostEconomicalModels = AIModelCreditRate::with(['provider'])
            ->orderBy('credit_per_1k_input_tokens')
            ->limit(10)
            ->get();

        return view('ai::admin.credit-rates.analytics', compact(
            'usageStats',
            'mostExpensiveModels', 
            'mostEconomicalModels'
        ));
    }
}