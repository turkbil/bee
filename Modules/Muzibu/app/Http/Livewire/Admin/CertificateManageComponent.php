<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\{Locked, Computed};
use Livewire\Component;
use Modules\Muzibu\App\Models\Certificate;
use Modules\Subscription\App\Models\Subscription;

class CertificateManageComponent extends Component
{
    #[Locked]
    public ?int $certificateId = null;

    public ?int $user_id = null;
    public string $member_name = '';
    public string $tax_office = '';
    public string $tax_number = '';
    public string $address = '';
    public bool $is_valid = true;

    // Auto-filled from subscription (readonly display)
    public ?string $membership_start = null;
    public ?string $subscription_expires_at = null;

    // User search
    public string $userSearch = '';
    public bool $showUserDropdown = false;

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'member_name' => 'required|string|min:3|max:255',
            'tax_office' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'required|string|min:10|max:500',
            'is_valid' => 'boolean',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'user_id' => 'Kullanici',
            'member_name' => 'Uye Adi',
            'tax_office' => 'Vergi Dairesi',
            'tax_number' => 'Vergi Numarasi',
            'address' => 'Adres',
            'is_valid' => 'Gecerlilik',
        ];
    }

    public function mount($certificateId = null): void
    {
        $this->certificateId = $certificateId ? (int) $certificateId : null;

        if ($this->certificateId) {
            $this->loadCertificate();
            view()->share('pretitle', 'Sertifika Duzenle');
            view()->share('title', 'Sertifika #' . $this->certificateId);
        } else {
            view()->share('pretitle', 'Yeni Sertifika');
            view()->share('title', 'Sertifika Olustur');
        }
    }

    public function loadCertificate(): void
    {
        $certificate = Certificate::findOrFail($this->certificateId);

        $this->user_id = $certificate->user_id;
        $this->member_name = $certificate->member_name;
        $this->tax_office = $certificate->tax_office ?? '';
        $this->tax_number = $certificate->tax_number ?? '';
        $this->address = $certificate->address ?? '';
        $this->is_valid = $certificate->is_valid;
        $this->membership_start = $certificate->membership_start?->format('d.m.Y');

        // Set userSearch for display (tenant DB)
        $user = User::on('tenant')->find($certificate->user_id);
        if ($user) {
            $this->userSearch = $user->name . ' (' . $user->email . ')';
            $this->subscription_expires_at = $user->subscription_expires_at?->format('d.m.Y');
        }
    }

    #[Computed]
    public function searchUsers(): \Illuminate\Database\Eloquent\Collection
    {
        if (strlen($this->userSearch) < 2) {
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

    public function selectUser(int $userId): void
    {
        $user = User::on('tenant')->find($userId);
        if ($user) {
            $this->user_id = $user->id;
            $this->userSearch = $user->name . ' (' . $user->email . ')';

            // Auto-fill member_name from user name if empty
            if (empty($this->member_name)) {
                $this->member_name = Certificate::correctSpelling($user->name);
            }

            // Get first PAID subscription date (not trial)
            $firstPaidSubscription = Subscription::on('tenant')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->where(function($q) {
                    $q->where('has_trial', false)
                      ->orWhereNull('has_trial');
                })
                ->orderBy('started_at', 'asc')
                ->first();

            if ($firstPaidSubscription && $firstPaidSubscription->started_at) {
                $this->membership_start = $firstPaidSubscription->started_at->format('d.m.Y');
            } else {
                // Fallback: any active subscription
                $anySubscription = Subscription::on('tenant')
                    ->where('user_id', $userId)
                    ->where('status', 'active')
                    ->orderBy('started_at', 'asc')
                    ->first();

                if ($anySubscription && $anySubscription->started_at) {
                    $this->membership_start = $anySubscription->started_at->format('d.m.Y');
                }
            }

            // Get subscription expiry
            $this->subscription_expires_at = $user->subscription_expires_at?->format('d.m.Y');
        }
        $this->showUserDropdown = false;
    }

    public function updatedUserSearch(): void
    {
        $this->showUserDropdown = strlen($this->userSearch) >= 2;
    }

    public function save(): void
    {
        $this->validate();

        // Parse date from display format
        $membershipStartDate = null;
        if ($this->membership_start) {
            try {
                $membershipStartDate = \Carbon\Carbon::createFromFormat('d.m.Y', $this->membership_start);
            } catch (\Exception $e) {
                $membershipStartDate = now();
            }
        } else {
            $membershipStartDate = now();
        }

        try {
            $data = [
                'user_id' => $this->user_id,
                'member_name' => Certificate::correctSpelling($this->member_name),
                'tax_office' => $this->tax_office ? Certificate::correctSpelling($this->tax_office) : null,
                'tax_number' => $this->tax_number ?: null,
                'address' => Certificate::correctSpelling($this->address),
                'is_valid' => $this->is_valid,
                'membership_start' => $membershipStartDate,
            ];

            if ($this->certificateId) {
                $certificate = Certificate::findOrFail($this->certificateId);
                $certificate->update($data);
                $message = 'Sertifika guncellendi.';
                log_activity($certificate, 'guncellendi');
            } else {
                $data['certificate_code'] = Certificate::generateCode();
                $data['qr_hash'] = Certificate::generateHash();
                $data['issued_at'] = now();

                $certificate = Certificate::create($data);
                $message = 'Sertifika olusturuldu.';
                log_activity($certificate, 'olusturuldu');
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $message,
                'type' => 'success',
            ]);

            // Redirect to list
            $this->redirect(route('admin.muzibu.certificate.index'), navigate: true);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    #[Computed]
    public function certificate(): ?Certificate
    {
        if (!$this->certificateId) {
            return null;
        }
        return Certificate::find($this->certificateId);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('muzibu::admin.livewire.certificate-manage-component', [
            'users' => $this->searchUsers,
        ]);
    }
}
