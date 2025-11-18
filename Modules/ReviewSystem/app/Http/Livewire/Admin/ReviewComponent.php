<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ReviewSystem\App\Models\Review;
use Modules\ReviewSystem\App\Models\Rating;
use Illuminate\Support\Facades\DB;

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

    #[Url]
    public $activeTab = 'reviews'; // reviews, ratings

    // Inline edit (reviews)
    public $editingId = null;
    public $editBody = '';
    public $editAuthorName = '';

    // Inline edit (ratings)
    public $editingRatingId = null;
    public $editRatingValue = 5;

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

    public function startEdit(int $id): void
    {
        $review = Review::findOrFail($id);
        $this->editingId = $id;
        $this->editBody = $review->review_body;
        $this->editAuthorName = $review->author_name ?? '';
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editBody = '';
        $this->editAuthorName = '';
    }

    public function saveEdit(): void
    {
        try {
            $review = Review::findOrFail($this->editingId);
            $review->update([
                'review_body' => $this->editBody,
                'author_name' => $this->editAuthorName ?: $review->author_name,
            ]);

            $this->cancelEdit();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'Yorum güncellendi',
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

    // Rating methods
    public function startEditRating(int $id): void
    {
        $rating = Rating::findOrFail($id);
        $this->editingRatingId = $id;
        $this->editRatingValue = $rating->rating_value;
    }

    public function cancelEditRating(): void
    {
        $this->editingRatingId = null;
        $this->editRatingValue = 5;
    }

    public function saveEditRating(): void
    {
        try {
            if ($this->editRatingValue < 1 || $this->editRatingValue > 5) {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => 'Puan 1-5 arası olmalı',
                    'type' => 'error',
                ]);
                return;
            }

            $rating = Rating::findOrFail($this->editingRatingId);
            $rating->update([
                'rating_value' => (int) $this->editRatingValue,
            ]);

            $this->cancelEditRating();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'Puan güncellendi',
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

    public function deleteRating(int $id): void
    {
        try {
            $rating = Rating::findOrFail($id);
            $rating->delete();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'Puan silindi',
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
        // Reviews query
        $reviewQuery = Review::with(['user', 'reviewable'])
            ->when($this->search, function ($q) {
                $q->where('review_body', 'like', '%' . $this->search . '%')
                    ->orWhere('author_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($this->filterStatus === 'pending', fn($q) => $q->where('is_approved', false))
            ->whereNull('parent_id') // Sadece ana yorumlar
            ->orderBy($this->sortField, $this->sortDirection);

        $reviews = $reviewQuery->paginate((int) $this->perPage);

        // Ratings query
        $ratingQuery = Rating::with(['user', 'ratable'])
            ->orderBy($this->sortField === 'id' ? 'id' : ($this->sortField === 'created_at' ? 'created_at' : 'rating_value'), $this->sortDirection);

        $ratings = $ratingQuery->paginate((int) $this->perPage);

        // Model bazlı istatistikler
        $modelStats = Review::select(
                'reviewable_type',
                DB::raw('COUNT(*) as total_reviews'),
                DB::raw('SUM(CASE WHEN is_approved = 1 THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending_count')
            )
            ->whereNull('parent_id')
            ->groupBy('reviewable_type')
            ->get()
            ->map(function ($item) {
                // Her model için rating istatistikleri
                $ratingStats = Rating::where('ratable_type', $item->reviewable_type)
                    ->selectRaw('COUNT(*) as total_ratings, AVG(rating_value) as avg_rating')
                    ->first();

                // Son 3 içeriği getir (title/name ile)
                $recentItems = Review::where('reviewable_type', $item->reviewable_type)
                    ->with('reviewable')
                    ->whereNull('parent_id')
                    ->orderByDesc('created_at')
                    ->limit(3)
                    ->get()
                    ->map(function ($review) {
                        $title = $review->reviewable->title ?? $review->reviewable->name ?? 'İçerik #' . $review->reviewable_id;

                        // Eğer title array ise (JSON field), string'e çevir
                        if (is_array($title)) {
                            $currentLocale = session('tenant_locale', config('app.locale'));
                            $title = $title[$currentLocale] ?? reset($title) ?? 'İçerik #' . $review->reviewable_id;
                        }

                        return [
                            'id' => $review->reviewable_id,
                            'title' => (string) $title,
                        ];
                    });

                return [
                    'model_name' => class_basename($item->reviewable_type),
                    'model_type' => $item->reviewable_type,
                    'total_reviews' => $item->total_reviews,
                    'approved_count' => $item->approved_count,
                    'pending_count' => $item->pending_count,
                    'total_ratings' => $ratingStats->total_ratings ?? 0,
                    'avg_rating' => $ratingStats->avg_rating ? round((float) $ratingStats->avg_rating, 2) : 0,
                    'recent_items' => $recentItems,
                ];
            });

        return view('reviewsystem::admin.livewire.review-component', [
            'reviews' => $reviews,
            'ratings' => $ratings,
            'modelStats' => $modelStats,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'activeTab' => $this->activeTab,
        ]);
    }
}
