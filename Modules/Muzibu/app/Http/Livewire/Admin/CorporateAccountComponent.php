<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Url, Computed};
use App\Models\User;
use Modules\Muzibu\App\Models\MuzibuCorporateAccount;
use Illuminate\Support\Facades\Log;

class CorporateAccountComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Ana Firma Oluşturma
    public ?int $selectedUserId = null;
    public string $companyName = '';
    public string $corporateCode = '';

    // Şube Yönetimi
    public ?int $selectedParentId = null;
    public string $branchSearch = '';

    // Filtreler
    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    // UI State
    public array $expandedRows = [];

    protected $rules = [
        'selectedUserId' => 'required|exists:users,id',
        'companyName' => 'nullable|string|max:255',
        'corporateCode' => 'required|string|max:20|unique:muzibu_corporate_accounts,corporate_code',
    ];

    protected $messages = [
        'selectedUserId.required' => 'Kullanıcı seçimi zorunludur',
        'selectedUserId.exists' => 'Seçilen kullanıcı bulunamadı',
        'corporateCode.required' => 'Kurumsal kod zorunludur',
        'corporateCode.unique' => 'Bu kurumsal kod zaten kullanılıyor',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Tüm ana firmalar (şube yönetimi dropdown için)
     */
    #[Computed]
    public function parentAccounts()
    {
        return MuzibuCorporateAccount::whereNull('parent_id')
            ->with('owner')
            ->where('is_active', true)
            ->orderBy('company_name')
            ->get();
    }

    /**
     * Ana firma listesi (tablo için - paginated)
     */
    #[Computed]
    public function corporateAccounts()
    {
        $query = MuzibuCorporateAccount::whereNull('parent_id')
            ->with(['owner', 'children.owner']);

        // Arama
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('company_name', 'like', $searchTerm)
                  ->orWhere('corporate_code', 'like', $searchTerm)
                  ->orWhereHas('owner', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', $searchTerm)
                                ->orWhere('email', 'like', $searchTerm);
                  });
            });
        }

        // Durum filtresi
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        return $query->orderBy('company_name', 'asc')->paginate(15);
    }

    /**
     * Bağlı olmayan kullanıcılar (sol liste)
     */
    #[Computed]
    public function availableUsers()
    {
        // Zaten herhangi bir kurumsal hesaba bağlı kullanıcıları hariç tut
        $usedUserIds = MuzibuCorporateAccount::pluck('user_id')->toArray();

        $query = User::query()
            ->whereNotIn('id', $usedUserIds)
            ->orderBy('name');

        // Arama
        if ($this->branchSearch) {
            $searchTerm = '%' . $this->branchSearch . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        return $query->limit(50)->get();
    }

    /**
     * Seçili ana firmanın şubeleri (sağ liste)
     */
    #[Computed]
    public function branchAccounts()
    {
        if (!$this->selectedParentId) {
            return collect([]);
        }

        return MuzibuCorporateAccount::where('parent_id', $this->selectedParentId)
            ->with('owner')
            ->orderBy('company_name')
            ->get();
    }

    /**
     * Seçili ana firma bilgisi
     */
    #[Computed]
    public function selectedParent()
    {
        if (!$this->selectedParentId) {
            return null;
        }

        return MuzibuCorporateAccount::with('owner')->find($this->selectedParentId);
    }

    /**
     * Tüm kullanıcılar (ana firma dropdown için)
     * Zaten ana firma sahibi olanları hariç tut
     */
    #[Computed]
    public function allUsers()
    {
        // Ana firma sahiplerinin ID'lerini al
        $parentOwnerIds = MuzibuCorporateAccount::whereNull('parent_id')
            ->pluck('user_id')
            ->toArray();

        return User::orderBy('name')
            ->whereNotIn('id', $parentOwnerIds)
            ->get();
    }

    /**
     * Otomatik kod oluştur
     */
    public function generateCode(): void
    {
        $this->corporateCode = MuzibuCorporateAccount::generateCode();

        $this->dispatch('toast', [
            'type' => 'info',
            'title' => 'Kod Oluşturuldu',
            'message' => 'Kurumsal kod: ' . $this->corporateCode
        ]);
    }

    /**
     * Ana firma oluştur
     */
    public function createParent(): void
    {
        $this->validate();

        try {
            // Kullanıcı zaten ana firma mı?
            $existingParent = MuzibuCorporateAccount::where('user_id', $this->selectedUserId)
                ->whereNull('parent_id')
                ->first();

            if ($existingParent) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'title' => 'Hata',
                    'message' => 'Bu kullanıcı zaten bir ana firmaya sahip'
                ]);
                return;
            }

            $user = User::find($this->selectedUserId);

            $account = MuzibuCorporateAccount::create([
                'user_id' => $this->selectedUserId,
                'parent_id' => null,
                'company_name' => $this->companyName ?: $user->name,
                'corporate_code' => $this->corporateCode,
                'is_active' => true,
            ]);

            Log::info('Muzibu: Ana firma oluşturuldu', [
                'account_id' => $account->id,
                'user_id' => $this->selectedUserId,
                'code' => $this->corporateCode,
                'admin_id' => auth()->id()
            ]);

            // Reset form
            $this->reset(['selectedUserId', 'companyName', 'corporateCode']);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => 'Ana firma oluşturuldu: ' . $account->company_name
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Ana firma oluşturma hatası', [
                'error' => $e->getMessage(),
                'user_id' => $this->selectedUserId
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Firma oluşturulurken bir hata oluştu'
            ]);
        }
    }

    /**
     * Şube ekle (kullanıcıyı ana firmaya bağla)
     */
    public function addBranch(int $userId): void
    {
        if (!$this->selectedParentId) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Önce ana firma seçin'
            ]);
            return;
        }

        try {
            $user = User::find($userId);
            if (!$user) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'title' => 'Hata',
                    'message' => 'Kullanıcı bulunamadı'
                ]);
                return;
            }

            // Zaten bir kurumsal hesaba bağlı mı?
            $existing = MuzibuCorporateAccount::where('user_id', $userId)->first();
            if ($existing) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'title' => 'Uyarı',
                    'message' => 'Bu kullanıcı zaten bir kurumsal hesaba bağlı'
                ]);
                return;
            }

            // Üye kaydı oluştur (kod yok - sadece kurum sahiplerinin kodu olur)
            $branch = MuzibuCorporateAccount::create([
                'user_id' => $userId,
                'parent_id' => $this->selectedParentId,
                'company_name' => null, // Üyelerin şirket adı yok
                'corporate_code' => null, // Üyelerin kodu yok
                'is_active' => true,
            ]);

            Log::info('Muzibu: Şube eklendi', [
                'branch_id' => $branch->id,
                'parent_id' => $this->selectedParentId,
                'user_id' => $userId,
                'admin_id' => auth()->id()
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => $user->name . ' şube olarak eklendi'
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Şube ekleme hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'parent_id' => $this->selectedParentId,
                'user_id' => $userId
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Şube çıkar
     */
    public function removeBranch(int $accountId): void
    {
        try {
            $branch = MuzibuCorporateAccount::find($accountId);

            if (!$branch || !$branch->parent_id) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'title' => 'Hata',
                    'message' => 'Şube bulunamadı'
                ]);
                return;
            }

            $branchName = $branch->branch_name ?: $branch->company_name;
            $branch->delete();

            Log::info('Muzibu: Şube çıkarıldı', [
                'account_id' => $accountId,
                'admin_id' => auth()->id()
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => $branchName . ' şubeden çıkarıldı'
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Şube çıkarma hatası', [
                'error' => $e->getMessage(),
                'account_id' => $accountId
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Şube çıkarılırken bir hata oluştu'
            ]);
        }
    }

    /**
     * Şubeyi bağımsızlaştır (ana firma yap)
     */
    public function detachBranch(int $accountId): void
    {
        try {
            $branch = MuzibuCorporateAccount::find($accountId);

            if (!$branch || !$branch->parent_id) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'title' => 'Hata',
                    'message' => 'Bu zaten bir ana firmadır'
                ]);
                return;
            }

            $oldParent = $branch->parent;
            $branchName = $branch->branch_name ?: 'İsimsiz Şube';

            // Bağımsızlaştır
            $branch->update([
                'parent_id' => null,
                'company_name' => $branchName, // Şube adını firma adı yap
                'branch_name' => null,
                'corporate_code' => MuzibuCorporateAccount::generateCode(),
            ]);

            Log::info('Muzibu: Şube bağımsızlaştırıldı', [
                'account_id' => $accountId,
                'old_parent_id' => $oldParent->id,
                'new_corporate_code' => $branch->corporate_code,
                'admin_id' => auth()->id()
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => $branchName . ' artık bağımsız bir ana firma!'
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Şube bağımsızlaştırma hatası', [
                'error' => $e->getMessage(),
                'account_id' => $accountId
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Bağımsızlaştırma sırasında hata oluştu'
            ]);
        }
    }

    /**
     * Şube adı güncelle
     */
    public function updateBranchName(int $accountId, string $name): void
    {
        try {
            $branch = MuzibuCorporateAccount::find($accountId);

            if (!$branch) {
                return;
            }

            $branch->update(['company_name' => $name]);

            Log::info('Muzibu: Şube adı güncellendi', [
                'account_id' => $accountId,
                'new_name' => $name,
                'admin_id' => auth()->id()
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => 'Şube adı güncellendi'
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Şube adı güncelleme hatası', [
                'error' => $e->getMessage(),
                'account_id' => $accountId
            ]);
        }
    }

    /**
     * Ana firma sil
     */
    public function deleteParent(int $accountId): void
    {
        try {
            $account = MuzibuCorporateAccount::find($accountId);

            if (!$account || $account->parent_id) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'title' => 'Hata',
                    'message' => 'Ana firma bulunamadı'
                ]);
                return;
            }

            $branchCount = $account->children()->count();
            $companyName = $account->company_name;

            $account->delete();

            Log::info('Muzibu: Ana firma silindi', [
                'account_id' => $accountId,
                'branch_count' => $branchCount,
                'admin_id' => auth()->id()
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => $companyName . ' ve ' . $branchCount . ' şubesi silindi'
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Ana firma silme hatası', [
                'error' => $e->getMessage(),
                'account_id' => $accountId
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Firma silinirken bir hata oluştu'
            ]);
        }
    }

    /**
     * Durum değiştir
     */
    public function toggleActive(int $id): void
    {
        try {
            $account = MuzibuCorporateAccount::findOrFail($id);
            $account->update(['is_active' => !$account->is_active]);

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $account->is_active
                    ? 'Kurumsal hesap aktifleştirildi'
                    : 'Kurumsal hesap devre dışı bırakıldı',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'İşlem başarısız',
                'type' => 'error',
            ]);
        }
    }

    /**
     * Row expand toggle
     */
    public function toggleRow(int $accountId): void
    {
        if (in_array($accountId, $this->expandedRows)) {
            $this->expandedRows = array_diff($this->expandedRows, [$accountId]);
        } else {
            $this->expandedRows[] = $accountId;
        }
    }

    public function render()
    {
        return view('muzibu::admin.livewire.corporate-account-component');
    }
}
