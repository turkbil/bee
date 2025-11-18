<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\ReviewSystem\App\Models\Review;
use Modules\ReviewSystem\App\Models\Rating;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layout')]
class ReviewStatisticsComponent extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        $stats = [
            'total_reviews' => Review::count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'approved_reviews' => Review::where('is_approved', true)->count(),
            'total_ratings' => Rating::count(),
            'average_rating' => Rating::avg('rating_value') ? round((float) Rating::avg('rating_value'), 2) : 0,
            'ratings_distribution' => Rating::select('rating_value', DB::raw('count(*) as count'))
                ->groupBy('rating_value')
                ->orderBy('rating_value', 'desc')
                ->get()
                ->pluck('count', 'rating_value'),
            'by_model' => Review::select('reviewable_type', DB::raw('count(*) as count'))
                ->groupBy('reviewable_type')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [class_basename($item->reviewable_type) => $item->count];
                }),
            'recent_reviews' => Review::with(['user', 'reviewable'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ];

        return view('reviewsystem::admin.livewire.review-statistics-component', compact('stats'));
    }
}
