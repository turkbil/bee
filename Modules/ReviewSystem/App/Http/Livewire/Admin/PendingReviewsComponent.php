<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ReviewSystem\App\Models\Review;

#[Layout('admin.layout')]
class PendingReviewsComponent extends Component
{
    use WithPagination;

    public $perPage;

    public function boot(): void
    {
        $this->perPage = $this->perPage ?? config('modules.pagination.admin_per_page', 10);
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
        $reviews = Review::with(['user', 'reviewable'])
            ->where('is_approved', false)
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->paginate((int) $this->perPage);

        return view('reviewsystem::admin.livewire.pending-reviews-component', compact('reviews'));
    }
}
