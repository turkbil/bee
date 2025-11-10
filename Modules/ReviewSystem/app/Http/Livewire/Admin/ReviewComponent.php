<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ReviewSystem\App\Models\Review;

#[Layout('admin.layout')]
class ReviewComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage;

    #[Url]
    public $sortField = 'id';

    #[Url]
    public $sortDirection = 'desc';

    #[Url]
    public $filterStatus = 'all'; // all, approved, pending

    public function boot(): void
    {
        $this->perPage = $this->perPage ?? config('modules.pagination.admin_per_page', 10);
    }

    public function updatedPerPage()
    {
        $this->perPage = (int) $this->perPage;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function approveReview(int $id): void
    {
        try {
            $review = Review::findOrFail($id);
            $review->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'Yorum onaylandı',
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

    public function deleteReview(int $id): void
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'Yorum silindi',
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

    public function render(): \Illuminate\Contracts\View\View
    {
        $query = Review::with(['user', 'reviewable'])
            ->when($this->search, function ($q) {
                $q->where('review_body', 'like', '%' . $this->search . '%')
                    ->orWhere('author_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($this->filterStatus === 'pending', fn($q) => $q->where('is_approved', false))
            ->whereNull('parent_id') // Sadece ana yorumlar
            ->orderBy($this->sortField, $this->sortDirection);

        $reviews = $query->paginate((int) $this->perPage);

        return view('reviewsystem::admin.livewire.review-component', [
            'reviews' => $reviews,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
