<?php

declare(strict_types=1);

namespace Modules\Favorite\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Favorite\App\Models\Favorite;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layout')]
class FavoriteStatisticsComponent extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        $stats = [
            'total' => Favorite::count(),
            'total_users' => Favorite::distinct('user_id')->count('user_id'),
            'by_model' => Favorite::select('favoritable_type', DB::raw('count(*) as count'))
                ->groupBy('favoritable_type')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [class_basename($item->favoritable_type) => $item->count];
                }),
            'most_favorited' => Favorite::with('favoritable')
                ->select('favoritable_type', 'favoritable_id', DB::raw('count(*) as count'))
                ->groupBy('favoritable_type', 'favoritable_id')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
            'recent' => Favorite::with(['user', 'favoritable'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ];

        return view('favorite::admin.livewire.favorite-statistics-component', compact('stats'));
    }
}
