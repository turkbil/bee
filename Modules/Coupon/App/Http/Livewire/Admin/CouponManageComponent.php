<?php

declare(strict_types=1);

namespace Modules\Coupon\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Modules\Coupon\App\Models\Coupon;
use Modules\Coupon\App\Services\CouponService;
use Modules\LanguageManagement\App\Models\TenantLanguage;

#[Layout('admin.layout')]
class CouponManageComponent extends Component
{
    public $couponId = null;
    public $currentLanguage = 'tr';
    public $availableLanguages = [];
    public $multiLangInputs = [];

    // Form fields
    public $code = '';
    public $applies_to = 'all';
    public $coupon_type = 'percentage';
    public $discount_percentage = 0;
    public $discount_amount = 0;
    public $min_order_amount = null;
    public $max_discount_amount = null;
    public $usage_limit_total = null;
    public $usage_limit_per_user = 1;
    public $valid_from = null;
    public $valid_until = null;
    public $is_active = true;

    private CouponService $couponService;

    protected $listeners = [
        'switchLanguage' => 'switchLanguage',
    ];

    protected function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code,' . $this->couponId . ',coupon_id',
            'applies_to' => 'required|in:all,shop,subscription',
            'coupon_type' => 'required|in:percentage,fixed_amount,free_shipping,buy_x_get_y',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit_total' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ];
    }

    public function boot(CouponService $couponService)
    {
        $this->couponService = $couponService;
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
            $this->couponId = $id;
            $this->loadCoupon();
        }
    }

    protected function initializeFormData()
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'description' => '',
            ];
        }
    }

    protected function loadCoupon()
    {
        $coupon = Coupon::findOrFail($this->couponId);

        $this->code = $coupon->code;
        $this->applies_to = $coupon->applies_to ?? 'all';
        $this->coupon_type = $coupon->coupon_type;
        $this->discount_percentage = $coupon->discount_percentage;
        $this->discount_amount = $coupon->discount_amount;
        $this->min_order_amount = $coupon->minimum_order_amount;
        $this->max_discount_amount = $coupon->max_discount_amount;
        $this->usage_limit_total = $coupon->usage_limit_total;
        $this->usage_limit_per_user = $coupon->usage_limit_per_customer ?? 1;
        $this->valid_from = $coupon->valid_from?->format('Y-m-d\TH:i');
        $this->valid_until = $coupon->valid_until?->format('Y-m-d\TH:i');
        $this->is_active = $coupon->is_active;

        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang]['description'] = $coupon->getTranslated('description', $lang) ?? '';
        }
    }

    public function switchLanguage($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;
        }
    }

    public function generateCode()
    {
        $this->code = $this->couponService->generateCode();
    }

    public function save()
    {
        $this->validate();

        try {
            $descriptionArray = [];

            foreach ($this->multiLangInputs as $lang => $data) {
                if (!empty($data['description'])) {
                    $descriptionArray[$lang] = $data['description'];
                }
            }

            $data = [
                'code' => strtoupper($this->code),
                'description' => $descriptionArray,
                'applies_to' => $this->applies_to,
                'coupon_type' => $this->coupon_type,
                'discount_percentage' => $this->coupon_type === 'percentage' ? $this->discount_percentage : null,
                'discount_amount' => in_array($this->coupon_type, ['fixed_amount', 'buy_x_get_y']) ? $this->discount_amount : null,
                'minimum_order_amount' => $this->min_order_amount,
                'max_discount_amount' => $this->max_discount_amount,
                'usage_limit_total' => $this->usage_limit_total,
                'usage_limit_per_customer' => $this->usage_limit_per_user,
                'valid_from' => $this->valid_from ? \Carbon\Carbon::parse($this->valid_from) : null,
                'valid_until' => $this->valid_until ? \Carbon\Carbon::parse($this->valid_until) : null,
                'is_active' => $this->is_active,
            ];

            if ($this->couponId) {
                $coupon = Coupon::findOrFail($this->couponId);
                $coupon->update($data);
                $message = __('admin.updated_successfully');
            } else {
                Coupon::create($data);
                $message = __('admin.created_successfully');
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $message,
                'type' => 'success'
            ]);

            return redirect()->route('admin.coupon.index');

        } catch (\Exception $e) {
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
        return view('coupon::admin.livewire.coupon-manage-component');
    }
}
