<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\User;
use Modules\Muzibu\App\Models\MuzibuCorporateAccount;
use Illuminate\Support\Facades\Log;

class CorporateAccountManageComponent extends Component
{
    public ?int $corporateId = null;

    public array $inputs = [
        'user_id' => null,
        'company_name' => '',
        'branch_name' => '',
        'corporate_code' => '',
        'is_active' => true,
        'spot_enabled' => false,
        'spot_songs_between' => 5,
    ];

    // User search
    public string $userSearch = '';
    public bool $showUserDropdown = false;

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function rules(): array
    {
        $uniqueRule = $this->corporateId
            ? 'unique:muzibu_corporate_accounts,corporate_code,' . $this->corporateId
            : 'unique:muzibu_corporate_accounts,corporate_code';

        return [
            'inputs.user_id' => 'required|exists:users,id',
            'inputs.company_name' => 'required|string|min:2|max:255',
            'inputs.branch_name' => 'nullable|string|max:255',
            'inputs.corporate_code' => 'required|string|max:20|' . $uniqueRule,
            'inputs.is_active' => 'boolean',
            'inputs.spot_enabled' => 'boolean',
            'inputs.spot_songs_between' => 'nullable|integer|min:1|max:50',
        ];
    }

    protected $messages = [
        'inputs.user_id.required' => 'Hesap sahibi seçimi zorunludur.',
        'inputs.company_name.required' => 'Firma adı zorunludur.',
        'inputs.corporate_code.required' => 'Kurum kodu zorunludur.',
        'inputs.corporate_code.unique' => 'Bu kurum kodu zaten kullanılıyor.',
    ];

    public function mount($corporateId = null): void
    {
        $this->corporateId = $corporateId ? (int) $corporateId : null;

        if ($this->corporateId) {
            $this->loadCorporateData();
            view()->share('pretitle', 'Kurumsal Hesap Düzenle');
            view()->share('title', $this->inputs['company_name'] ?: 'Düzenle');
        } else {
            // Yeni kayıt için otomatik kod oluştur
            $this->inputs['corporate_code'] = MuzibuCorporateAccount::generateCode();
            view()->share('pretitle', 'Yeni Kurumsal Hesap');
            view()->share('title', 'Oluştur');
        }
    }

    protected function loadCorporateData(): void
    {
        $account = MuzibuCorporateAccount::with('user')->findOrFail($this->corporateId);

        $this->inputs = [
            'user_id' => $account->user_id,
            'company_name' => $account->company_name ?? '',
            'branch_name' => $account->branch_name ?? '',
            'corporate_code' => $account->corporate_code ?? '',
            'is_active' => $account->is_active,
            'spot_enabled' => $account->spot_enabled ?? false,
            'spot_songs_between' => $account->spot_songs_between ?? 5,
        ];

        // User search için
        if ($account->user) {
            $this->userSearch = $account->user->name . ' (' . $account->user->email . ')';
        }
    }

    #[Computed]
    public function searchUsers(): \Illuminate\Database\Eloquent\Collection
    {
        // Dropdown kapalıysa veya arama kısa ise sorgu yapma
        if (!$this->showUserDropdown || strlen($this->userSearch) < 2) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        return User::on('tenant')
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->userSearch}%")
                    ->orWhere('email', 'like', "%{$this->userSearch}%");
            })
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function currentAccount(): ?MuzibuCorporateAccount
    {
        if (!$this->corporateId) {
            return null;
        }
        return MuzibuCorporateAccount::with(['user', 'children', 'spots'])->find($this->corporateId);
    }

    public function selectUser(int $userId): void
    {
        $user = User::on('tenant')->find($userId);
        if ($user) {
            $this->inputs['user_id'] = $user->id;
            $this->userSearch = $user->name . ' (' . $user->email . ')';

            // Firma adı boşsa kullanıcı adını kullan
            if (empty($this->inputs['company_name'])) {
                $this->inputs['company_name'] = $user->name;
            }
        }
        $this->showUserDropdown = false;
    }

    public function updatedUserSearch(): void
    {
        $this->showUserDropdown = strlen($this->userSearch) >= 2;
    }

    public function generateCode(): void
    {
        $this->inputs['corporate_code'] = MuzibuCorporateAccount::generateCode();

        $this->dispatch('toast', [
            'type' => 'info',
            'title' => 'Kod Oluşturuldu',
            'message' => 'Yeni kurum kodu: ' . $this->inputs['corporate_code']
        ]);
    }

    public function save(bool $redirect = false): void
    {
        $this->validate();

        try {
            $data = [
                'user_id' => $this->inputs['user_id'],
                'company_name' => $this->inputs['company_name'],
                'branch_name' => $this->inputs['branch_name'] ?: null,
                'corporate_code' => $this->inputs['corporate_code'],
                'is_active' => $this->inputs['is_active'],
                'spot_enabled' => $this->inputs['spot_enabled'],
                'spot_songs_between' => $this->inputs['spot_songs_between'] ?? 5,
            ];

            $isNew = !$this->corporateId;

            if ($this->corporateId) {
                $account = MuzibuCorporateAccount::findOrFail($this->corporateId);
                $account->update($data);
                log_activity($account, 'güncellendi');
                $message = 'Kurumsal hesap güncellendi.';
            } else {
                $data['parent_id'] = null; // Ana firma
                $account = MuzibuCorporateAccount::create($data);
                $this->corporateId = $account->id;
                log_activity($account, 'oluşturuldu');
                $message = 'Kurumsal hesap oluşturuldu.';
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $message,
                'type' => 'success',
            ]);

            if ($redirect) {
                session()->flash('toast', [
                    'title' => __('admin.success'),
                    'message' => $message,
                    'type' => 'success',
                ]);
                $this->redirectRoute('admin.muzibu.corporate.index');
                return;
            }

            if ($isNew) {
                session()->flash('toast', [
                    'title' => __('admin.success'),
                    'message' => $message,
                    'type' => 'success',
                ]);
                $this->redirectRoute('admin.muzibu.corporate.manage', ['id' => $account->id]);
            }

        } catch (\Exception $e) {
            Log::error('Corporate account save error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('muzibu::admin.livewire.corporate-account-manage-component', [
            'users' => $this->searchUsers,
        ]);
    }
}
