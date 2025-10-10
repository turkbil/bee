<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use App\Helpers\SlugHelper;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Services\ShopProductService;
use Modules\Shop\App\Services\ShopCategoryService;
use Modules\Shop\App\Services\ShopBrandService;
use Modules\LanguageManagement\App\Models\TenantLanguage;

#[Layout('admin.layout')]
class ShopProductManageComponent extends Component
{
    public ?int $productId = null;

    public array $availableLanguages = [];
    public string $currentLanguage;

    public array $multiLangInputs = [];

    public array $inputs = [
        'category_id' => null,
        'brand_id' => null,
        'sku' => null,
        'product_type' => 'physical',
        'condition' => 'new',
        'price_on_request' => false,
        'base_price' => null,
        'compare_at_price' => null,
        'currency' => 'TRY',
        'is_active' => true,
        'sort_order' => 0,
    ];

    public array $seoData = [];
    public array $tabCompletion = [];

    protected ShopProductService $productService;
    protected ShopCategoryService $categoryService;
    protected ShopBrandService $brandService;

    public function boot(
        ShopProductService $productService,
        ShopCategoryService $categoryService,
        ShopBrandService $brandService
    ): void {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;

        $this->availableLanguages = TenantLanguage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();

        $this->currentLanguage = get_tenant_default_locale();
    }

    public function mount(?int $id = null): void
    {
        $this->productId = $id;
        $this->initializeLanguageInputs();

        if ($this->productId !== null) {
            $this->loadProduct($this->productId);
        }
    }

    public function updated(string $property): void
    {
        if (str_contains($property, 'multiLangInputs') && str_ends_with($property, '.title')) {
            [$locale] = array_slice(explode('.', $property), 1, 1);
            $this->generateSlugFor($locale);
        }
    }

    public function switchLanguage(string $locale): void
    {
        if (in_array($locale, $this->availableLanguages, true)) {
            $this->currentLanguage = $locale;
        }
    }

    public function generateSlugFor(string $locale): void
    {
        $title = $this->multiLangInputs[$locale]['title'] ?? '';

        if (empty($title)) {
            return;
        }

        $this->multiLangInputs[$locale]['slug'] = SlugHelper::generateFromTitle(
            ShopProduct::class,
            $title,
            $locale,
            'slug',
            'product_id',
            $this->productId
        );
    }

    public function save(): void
    {
        $this->validateData();

        $payload = $this->preparePayload();

        if ($this->productId === null) {
            $result = $this->productService->createProduct($payload);
        } else {
            $result = $this->productService->updateProduct($this->productId, $payload);
        }

        $this->dispatch('toast', [
            'title' => $result->success ? __('admin.success') : __('admin.' . $result->type),
            'message' => $result->message,
            'type' => $result->type,
        ]);

        if ($result->success && $result->data) {
            $this->productId = $result->data->product_id;
            $this->loadProduct($this->productId);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('shop::admin.livewire.product-manage-component', [
            'categories' => $this->categoryService->getActiveCategories(),
            'brands' => $this->brandService->getActiveBrands(),
            'currentProduct' => $this->productId ? ShopProduct::find($this->productId) : null,
        ]);
    }

    private function initializeLanguageInputs(): void
    {
        foreach ($this->availableLanguages as $locale) {
            $this->multiLangInputs[$locale] = [
                'title' => '',
                'short_description' => '',
                'long_description' => '',
                'slug' => '',
            ];
        }
    }

    private function loadProduct(int $productId): void
    {
        $formData = $this->productService->prepareProductForForm($productId, $this->currentLanguage);

        /** @var \Modules\Shop\App\Models\ShopProduct|null $product */
        $product = $formData['product'] ?? null;

        if (!$product) {
            return;
        }

        $this->inputs = array_merge($this->inputs, [
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'sku' => $product->sku,
            'product_type' => $product->product_type,
            'condition' => $product->condition,
            'price_on_request' => (bool) $product->price_on_request,
            'base_price' => $product->base_price,
            'compare_at_price' => $product->compare_at_price,
            'currency' => $product->currency,
            'is_active' => (bool) $product->is_active,
        ]);

        foreach ($this->availableLanguages as $locale) {
            $this->multiLangInputs[$locale]['title'] = $product->getTranslated('title', $locale, false) ?? '';
            $this->multiLangInputs[$locale]['short_description'] = $product->getTranslated('short_description', $locale, false) ?? '';
            $this->multiLangInputs[$locale]['long_description'] = $product->getTranslated('long_description', $locale, false) ?? '';
            $this->multiLangInputs[$locale]['slug'] = $product->getTranslated('slug', $locale, false) ?? '';
        }

        $this->seoData = $formData['seoData'] ?? [];
        $this->tabCompletion = $formData['tabCompletion'] ?? [];
    }

    private function validateData(): void
    {
        $rules = $this->productService->getValidationRules($this->availableLanguages);

        $additionalRules = [
            'inputs.brand_id' => 'nullable|integer|exists:shop_brands,brand_id',
            'inputs.sku' => 'required|string|max:191',
            'inputs.currency' => 'required|string|size:3',
        ];

        $this->validate(array_merge($rules, $additionalRules));
    }

    private function preparePayload(): array
    {
        $translations = [
            'title' => [],
            'short_description' => [],
            'long_description' => [],
            'slug' => [],
        ];

        foreach ($this->availableLanguages as $locale) {
            $translations['title'][$locale] = $this->multiLangInputs[$locale]['title'] ?? null;
            $translations['short_description'][$locale] = $this->multiLangInputs[$locale]['short_description'] ?? null;
            $translations['long_description'][$locale] = $this->multiLangInputs[$locale]['long_description'] ?? null;
            $translations['slug'][$locale] = $this->multiLangInputs[$locale]['slug'] ?? null;
        }

        return array_merge($this->inputs, $translations, [
            'seo' => $this->seoData,
        ]);
    }
}
