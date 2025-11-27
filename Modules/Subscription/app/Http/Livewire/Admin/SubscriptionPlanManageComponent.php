<?php

declare(strict_types=1);

namespace Modules\Subscription\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed, Locked};
use Livewire\Component;
use Modules\Subscription\App\Models\SubscriptionPlan;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class SubscriptionPlanManageComponent extends Component
{
    public ?int $planId = null;
    public string $currentLanguage = 'tr';
    public array $availableLanguages = [];
    public array $multiLangInputs = [];  // RE-ENABLED - needed by Blade
    public array $features = [];  // RE-ENABLED - needed by Blade
    public string $newFeature = '';

    // Form fields
    public string $slug = '';
    public float $price_monthly = 0;
    public float $price_yearly = 0;
    public ?float $compare_price_monthly = null;
    public ?float $compare_price_yearly = null;
    public int $trial_days = 0;
    public int $device_limit = 1;
    public bool $is_featured = false;
    public bool $is_active = true;

    public function exception($e, $stopPropagation)
    {
        \Log::error('🔥🔥🔥 LIVEWIRE COMPONENT EXCEPTION!', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        $stopPropagation();

        $this->dispatch('toast', [
            'title' => 'Hata',
            'message' => 'İşlem başarısız: ' . $e->getMessage(),
            'type' => 'error'
        ]);
    }

    public function hydrate()
    {
        try {
            \Log::info('🔄 HYDRATE - Properties after request', [
                'planId' => gettype($this->planId) . ': ' . var_export($this->planId, true),
                'multiLangInputs_type' => gettype($this->multiLangInputs),
                'multiLangInputs_structure' => json_encode($this->multiLangInputs),
                'features_type' => gettype($this->features),
                'features_content' => json_encode($this->features),
                'price_monthly_type' => gettype($this->price_monthly),
                'price_monthly_value' => $this->price_monthly,
                'compare_price_monthly_type' => gettype($this->compare_price_monthly),
                'compare_price_monthly_value' => $this->compare_price_monthly,
            ]);
        } catch (\Exception $e) {
            \Log::error('🔥 HYDRATE ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function dehydrate()
    {
        try {
            \Log::info('💧 DEHYDRATE - Properties before response', [
                'planId' => gettype($this->planId) . ': ' . var_export($this->planId, true),
                'multiLangInputs_type' => gettype($this->multiLangInputs),
                'features_type' => gettype($this->features),
            ]);
        } catch (\Exception $e) {
            \Log::error('🔥 DEHYDRATE ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    protected function rules()
    {
        return [
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $this->planId . ',subscription_plan_id',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'compare_price_monthly' => 'nullable|numeric|min:0',
            'compare_price_yearly' => 'nullable|numeric|min:0',
            'trial_days' => 'nullable|integer|min:0',
            'device_limit' => 'required|integer|min:1',
            'features' => 'nullable',
        ];
    }

    public function mount($id = null)
    {
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();

        $this->currentLanguage = $this->availableLanguages[0] ?? 'tr';

        $this->initializeFormData();

        if ($id) {
            $this->planId = (int) $id;
            $this->loadPlan();
        }
    }

    protected function initializeFormData()
    {
        // Ensure features is always an array
        if (!is_array($this->features)) {
            $this->features = [];
        }

        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'description' => '',
            ];
        }
    }

    protected function loadPlan()
    {
        $plan = SubscriptionPlan::findOrFail($this->planId);

        $this->slug = (string) $plan->slug;
        $this->price_monthly = (float) $plan->price_monthly;
        $this->price_yearly = (float) $plan->price_yearly;
        $this->compare_price_monthly = $plan->compare_price_monthly ? (float) $plan->compare_price_monthly : null;
        $this->compare_price_yearly = $plan->compare_price_yearly ? (float) $plan->compare_price_yearly : null;
        $this->trial_days = (int) ($plan->trial_days ?? 0);
        $this->device_limit = (int) ($plan->device_limit ?? 1);
        $this->features = is_array($plan->features) ? $plan->features : [];
        $this->is_featured = (bool) $plan->is_featured;
        $this->is_active = (bool) $plan->is_active;

        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang]['title'] = (string) ($plan->getTranslated('title', $lang) ?? '');
            $this->multiLangInputs[$lang]['description'] = (string) ($plan->getTranslated('description', $lang) ?? '');
        }
    }

    public function generateSlug()
    {
        $title = $this->multiLangInputs[$this->currentLanguage]['title'] ?? '';
        if (!empty($title)) {
            $this->slug = Str::slug($title);
        }
    }

    public function addFeature()
    {
        if (!empty($this->newFeature)) {
            $this->features[] = $this->newFeature;
            $this->newFeature = '';
        }
    }

    public function removeFeature($index)
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
    }

    public function save()
    {
        \Log::info('🔍 SAVE METHOD STARTED', [
            'planId' => gettype($this->planId) . ': ' . var_export($this->planId, true),
            'slug' => gettype($this->slug) . ': ' . $this->slug,
            'price_monthly' => gettype($this->price_monthly) . ': ' . $this->price_monthly,
            'price_yearly' => gettype($this->price_yearly) . ': ' . $this->price_yearly,
            'compare_price_monthly' => gettype($this->compare_price_monthly) . ': ' . var_export($this->compare_price_monthly, true),
            'compare_price_yearly' => gettype($this->compare_price_yearly) . ': ' . var_export($this->compare_price_yearly, true),
            'multiLangInputs_type' => gettype($this->multiLangInputs),
            'multiLangInputs_json' => json_encode($this->multiLangInputs),
            'features_type' => gettype($this->features),
            'features_json' => json_encode($this->features),
        ]);

        try {
            $this->validate();
            \Log::info('✅ VALIDATION PASSED');
        } catch (\Exception $e) {
            \Log::error('❌ VALIDATION FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        // Validate at least one language has title
        $hasTitle = false;
        foreach ($this->multiLangInputs as $data) {
            if (is_array($data) && !empty($data['title'])) {
                $hasTitle = true;
                break;
            }
        }

        if (!$hasTitle) {
            \Log::warning('❌ No title provided', ['multiLangInputs' => $this->multiLangInputs]);
            $this->addError('multiLangInputs', __('subscription::admin.plans.title_required'));
            return;
        }

        \Log::info('✅ Title validation passed');

        try {
            $titleArray = [];
            $descriptionArray = [];

            foreach ($this->multiLangInputs as $lang => $data) {
                if (is_array($data)) {
                    if (!empty($data['title'])) {
                        $titleArray[$lang] = $data['title'];
                    }
                    if (!empty($data['description'])) {
                        $descriptionArray[$lang] = $data['description'];
                    }
                }
            }

            \Log::info('✅ Language arrays prepared', [
                'titleArray' => $titleArray,
                'descriptionArray' => $descriptionArray,
            ]);

            // Ensure features is an array of strings
            $featuresArray = [];
            if (is_array($this->features)) {
                foreach ($this->features as $feature) {
                    if (is_string($feature) && !empty(trim($feature))) {
                        $featuresArray[] = trim($feature);
                    }
                }
            }

            $data = [
                'title' => $titleArray,
                'description' => $descriptionArray,
                'slug' => $this->slug,
                'price_monthly' => $this->price_monthly,
                'price_yearly' => $this->price_yearly,
                'compare_price_monthly' => $this->compare_price_monthly ?: null,
                'compare_price_yearly' => $this->compare_price_yearly ?: null,
                'trial_days' => $this->trial_days ?: 0,
                'device_limit' => $this->device_limit,
                'features' => $featuresArray,
                'is_featured' => $this->is_featured,
                'is_active' => $this->is_active,
            ];

            \Log::info('✅ Data array prepared', ['data' => $data]);

            if ($this->planId) {
                \Log::info('🔄 Updating existing plan', ['planId' => $this->planId]);
                $plan = SubscriptionPlan::findOrFail($this->planId);
                $plan->update($data);
                \Log::info('✅ Plan updated successfully');
                $message = __('admin.updated_successfully');
            } else {
                \Log::info('➕ Creating new plan');
                $data['sort_order'] = (SubscriptionPlan::max('sort_order') ?? 0) + 1;
                SubscriptionPlan::create($data);
                \Log::info('✅ Plan created successfully');
                $message = __('admin.created_successfully');
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $message,
                'type' => 'success'
            ]);

            return redirect()->route('admin.subscription.plans.index');

        } catch (\Exception $e) {
            \Log::error('Subscription plan save error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data ?? null,
            ]);

            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed') . ': ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    #[Computed]
    public function availableLanguagesList()
    {
        return TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function render()
    {
        // CRITICAL DEBUG - Log render state
        file_put_contents('/tmp/livewire-render-debug.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'planId' => $this->planId,
            'features_type' => gettype($this->features),
            'features_value' => $this->features,
            'features_is_null' => is_null($this->features),
            'multiLangInputs_type' => gettype($this->multiLangInputs),
        ], JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

        return view('subscription::admin.livewire.subscription-plan-manage-component');
    }
}
