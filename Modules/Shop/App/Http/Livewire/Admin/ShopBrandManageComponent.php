<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use App\Helpers\SlugHelper;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Shop\App\Models\ShopBrand;
use Modules\Shop\App\Services\ShopBrandService;

#[Layout('admin.layout')]
class ShopBrandManageComponent extends Component
{
    public ?int $brandId = null;

    public array $availableLanguages = [];
    public string $currentLanguage;

    public array $multiLangInputs = [];

    public array $inputs = [
        'logo_url' => null,
        'website_url' => null,
        'country_code' => null,
        'founded_year' => null,
        'headquarters' => null,
        'is_active' => true,
        'is_featured' => false,
        'sort_order' => 0,
    ];

    protected ShopBrandService $brandService;

    public function boot(ShopBrandService $brandService): void
    {
        $this->brandService = $brandService;

        $this->availableLanguages = TenantLanguage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();

        $this->currentLanguage = get_tenant_default_locale();
        $this->initializeLanguageInputs();
    }

    public function mount(?int $id = null): void
    {
        $this->brandId = $id;

        if ($this->brandId !== null) {
            $this->loadBrand($this->brandId);
        }
    }

    public function updated(string $property): void
    {
        if (str_contains($property, 'multiLangInputs') && str_ends_with($property, '.title')) {
            [$locale] = array_slice(explode('.', $property), 1, 1);
            $this->generateSlugFor($locale);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('shop::admin.livewire.brand-manage-component');
    }

    public function save(): void
    {
        $this->validate($this->rules());

        $payload = $this->preparePayload();

        if ($this->brandId === null) {
            $result = $this->brandService->createBrand($payload);
        } else {
            $result = [
                'success' => $this->brandService->updateBrand($this->brandId, $payload),
                'message' => __('shop::admin.brand_updated'),
                'type' => 'success',
                'data' => ShopBrand::find($this->brandId),
            ];
        }

        $this->dispatch('toast', [
            'title' => $result['success'] ? __('admin.success') : __('admin.error'),
            'message' => $result['message'],
            'type' => $result['type'] ?? ($result['success'] ? 'success' : 'error'),
        ]);

        if (($result['success'] ?? false) && !empty($result['data'])) {
            $this->brandId = $result['data']->brand_id;
            $this->loadBrand($this->brandId);
        }
    }

    public function switchLanguage(string $locale): void
    {
        if (in_array($locale, $this->availableLanguages, true)) {
            $this->currentLanguage = $locale;
        }
    }

    private function initializeLanguageInputs(): void
    {
        foreach ($this->availableLanguages as $locale) {
            $this->multiLangInputs[$locale] = [
                'title' => '',
                'description' => '',
                'slug' => '',
            ];
        }
    }

    private function loadBrand(int $brandId): void
    {
        $brand = $this->brandService->findBrand($brandId);

        if (!$brand) {
            return;
        }

        $this->inputs = array_merge($this->inputs, [
            'logo_url' => $brand->logo_url,
            'website_url' => $brand->website_url,
            'country_code' => $brand->country_code,
            'founded_year' => $brand->founded_year,
            'headquarters' => $brand->headquarters,
            'is_active' => (bool) $brand->is_active,
            'is_featured' => (bool) $brand->is_featured,
            'sort_order' => $brand->sort_order,
        ]);

        foreach ($this->availableLanguages as $locale) {
            $this->multiLangInputs[$locale]['title'] = $brand->getTranslated('title', $locale, false) ?? '';
            $this->multiLangInputs[$locale]['description'] = $brand->getTranslated('description', $locale, false) ?? '';
            $this->multiLangInputs[$locale]['slug'] = $brand->getTranslated('slug', $locale, false) ?? '';
        }
    }

    private function generateSlugFor(string $locale): void
    {
        $title = $this->multiLangInputs[$locale]['title'] ?? '';

        if (empty($title)) {
            return;
        }

        $this->multiLangInputs[$locale]['slug'] = SlugHelper::generateFromTitle(
            ShopBrand::class,
            $title,
            $locale,
            'slug',
            'brand_id',
            $this->brandId
        );
    }

    private function rules(): array
    {
        $defaultLocale = get_tenant_default_locale();

        $rules = [
            'inputs.logo_url' => 'nullable|url|max:255',
            'inputs.website_url' => 'nullable|url|max:255',
            'inputs.country_code' => 'nullable|string|size:2',
            'inputs.founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'inputs.headquarters' => 'nullable|string|max:191',
            'inputs.is_active' => 'boolean',
            'inputs.is_featured' => 'boolean',
            'inputs.sort_order' => 'integer|min:0|max:1000',
        ];

        foreach ($this->availableLanguages as $locale) {
            $titleRule = $locale === $defaultLocale ? 'required' : 'nullable';
            $rules["multiLangInputs.{$locale}.title"] = "{$titleRule}|string|min:2|max:191";
            $rules["multiLangInputs.{$locale}.slug"] = 'nullable|string|max:191';
            $rules["multiLangInputs.{$locale}.description"] = 'nullable|string';
        }

        return $rules;
    }

    private function preparePayload(): array
    {
        $translations = [
            'title' => [],
            'slug' => [],
            'description' => [],
        ];

        foreach ($this->availableLanguages as $locale) {
            $translations['title'][$locale] = $this->multiLangInputs[$locale]['title'] ?? null;
            $translations['slug'][$locale] = $this->multiLangInputs[$locale]['slug'] ?? null;
            $translations['description'][$locale] = $this->multiLangInputs[$locale]['description'] ?? null;
        }

        return array_merge($this->inputs, $translations);
    }
}
