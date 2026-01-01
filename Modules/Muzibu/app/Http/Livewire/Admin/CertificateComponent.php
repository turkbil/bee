<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Muzibu\App\Models\Certificate;

class CertificateComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $sortField = 'id';

    #[Url]
    public $sortDirection = 'desc';

    #[Url]
    public $validFilter = '';

    public array $selectedIds = [];
    public bool $selectAll = false;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'certificateDeleted' => '$refresh',
    ];

    public function mount(): void
    {
        view()->share('pretitle', 'Sertifika Yonetimi');
        view()->share('title', 'Sertifikalar');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->certificates->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedValidFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleValid(int $id): void
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $certificate->update(['is_valid' => !$certificate->is_valid]);

            $status = $certificate->is_valid ? 'aktiflestirildi' : 'iptal edildi';
            log_activity($certificate, $status);

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => "Sertifika $status.",
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function deleteCertificate(int $id): void
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $code = $certificate->certificate_code;

            $certificate->delete();

            log_activity($certificate, 'silindi');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => "Sertifika ($code) silindi.",
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => 'Lutfen en az bir sertifika secin.',
                'type' => 'warning',
            ]);
            return;
        }

        try {
            $certificates = Certificate::whereIn('id', $this->selectedIds)->get();
            $count = $certificates->count();

            foreach ($certificates as $certificate) {
                $certificate->delete();
            }

            $this->selectedIds = [];
            $this->selectAll = false;

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => "$count sertifika silindi.",
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    #[Computed]
    public function certificates()
    {
        $query = Certificate::query()
            ->with(['user']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('certificate_code', 'like', "%{$this->search}%")
                    ->orWhere('member_name', 'like', "%{$this->search}%")
                    ->orWhere('tax_number', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%");
                    });
            });
        }

        // Valid filter
        if ($this->validFilter !== '') {
            $query->where('is_valid', $this->validFilter === '1');
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('muzibu::admin.livewire.certificate-component', [
            'certificates' => $this->certificates,
        ]);
    }
}
