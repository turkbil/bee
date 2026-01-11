<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Modules\Muzibu\App\Models\MuzibuCorporateAccount;
use Modules\Muzibu\App\Models\SongPlay;
use Illuminate\Support\Facades\DB;

class CorporateUsageComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $period = '30'; // 7, 30, 90, 365 days
    public ?int $corporateId = null;

    protected $queryString = ['period', 'corporateId'];

    #[Computed]
    public function corporateAccounts()
    {
        return MuzibuCorporateAccount::orderBy('company_name')->paginate(15);
    }

    #[Computed]
    public function overallStats(): array
    {
        $startDate = now()->subDays((int)$this->period);

        return [
            'total_plays' => SongPlay::where('created_at', '>=', $startDate)->count(),
            'unique_listeners' => SongPlay::where('created_at', '>=', $startDate)
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id'),
            'active_corporates' => MuzibuCorporateAccount::where('is_active', true)->count(),
            'total_hours' => round(SongPlay::where('created_at', '>=', $startDate)
                ->sum('listened_duration') / 3600, 1),
        ];
    }

    #[Computed]
    public function dailyUsage()
    {
        return SongPlay::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as plays'),
                DB::raw('COUNT(DISTINCT user_id) as listeners')
            )
            ->where('created_at', '>=', now()->subDays((int)$this->period))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    #[Computed]
    public function allCorporates()
    {
        return MuzibuCorporateAccount::orderBy('company_name')->get();
    }

    public function render()
    {
        return view('muzibu::admin.livewire.corporate-usage-component');
    }
}
