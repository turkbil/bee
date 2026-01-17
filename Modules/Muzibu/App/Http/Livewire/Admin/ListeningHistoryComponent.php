<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Modules\Muzibu\App\Models\SongPlay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ListeningHistoryComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url]
    public string $selectedDate = '';

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $filterUser = null;

    #[Url]
    public int $perPage = 50;

    #[Url]
    public string $viewMode = 'hourly'; // hourly, daily, weekly, monthly

    // Kullanıcı arama
    public string $userSearch = '';
    public bool $showUserDropdown = false;

    public function mount(): void
    {
        if (empty($this->selectedDate)) {
            $this->selectedDate = now()->format('Y-m-d');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedDate(): void
    {
        $this->resetPage();
    }

    private function cacheKey(string $suffix): string
    {
        $tenantId = tenant('id') ?? 'central';
        $userPart = $this->filterUser ? "user_{$this->filterUser}_" : '';
        return "listening_history_{$tenantId}_{$userPart}{$this->selectedDate}_{$suffix}";
    }

    // 5 dakika cache (gerçek zamana yakın)
    private function cacheTTL(): int
    {
        return 300;
    }

    #[Computed]
    public function plays()
    {
        $date = Carbon::parse($this->selectedDate);
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        return SongPlay::with(['song.artist', 'song.album', 'user'])
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->when($this->filterUser, fn($q) => $q->where('user_id', $this->filterUser))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('song', function ($sq) {
                        $sq->where('title', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('email', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function searchedUsers()
    {
        if (strlen($this->userSearch) < 2) {
            return collect();
        }

        return \App\Models\User::whereHas('songPlays')
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->userSearch . '%')
                  ->orWhere('email', 'like', '%' . $this->userSearch . '%');
            })
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function selectedUserInfo(): ?object
    {
        if (!$this->filterUser) {
            return null;
        }

        $user = \App\Models\User::find($this->filterUser);
        if (!$user) return null;

        $date = Carbon::parse($this->selectedDate);
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $todayPlays = SongPlay::where('user_id', $this->filterUser)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();

        return (object) [
            'name' => $user->name,
            'email' => $user->email,
            'today_plays' => $todayPlays,
        ];
    }

    public function selectUser(int $userId): void
    {
        $this->filterUser = (string) $userId;
        $this->userSearch = '';
        $this->showUserDropdown = false;
        $this->resetPage();
    }

    public function clearUserFilter(): void
    {
        $this->filterUser = null;
        $this->userSearch = '';
        $this->showUserDropdown = false;
        $this->resetPage();
    }

    public function updatedUserSearch(): void
    {
        $this->showUserDropdown = strlen($this->userSearch) >= 2;
    }

    #[Computed]
    public function stats(): array
    {
        $filterUser = $this->filterUser;
        return Cache::remember($this->cacheKey('stats'), $this->cacheTTL(), function () use ($filterUser) {
            $date = Carbon::parse($this->selectedDate);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            $baseQuery = SongPlay::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->when($filterUser, fn($q) => $q->where('user_id', $filterUser));

            $totalPlays = (clone $baseQuery)->count();

            $uniqueListeners = $filterUser ? 1 : (clone $baseQuery)
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');

            $uniqueSongs = (clone $baseQuery)
                ->distinct('song_id')
                ->count('song_id');

            $totalDuration = (clone $baseQuery)->sum('listened_duration');

            return [
                'total_plays' => $totalPlays,
                'unique_listeners' => $uniqueListeners,
                'unique_songs' => $uniqueSongs,
                'total_hours' => round($totalDuration / 3600, 1),
            ];
        });
    }

    #[Computed]
    public function hourlyStats(): array
    {
        $filterUser = $this->filterUser;
        return Cache::remember($this->cacheKey('hourly'), $this->cacheTTL(), function () use ($filterUser) {
            $date = Carbon::parse($this->selectedDate);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            $currentHour = $date->isToday() ? now()->hour : 24;

            $hourlyData = SongPlay::select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as plays')
                )
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->when($filterUser, fn($q) => $q->where('user_id', $filterUser))
                ->when($date->isToday(), function($q) use ($currentHour) {
                    $q->whereRaw('HOUR(created_at) < ?', [$currentHour]);
                })
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('plays', 'hour')
                ->toArray();

            $fullHours = [];
            $maxHour = $date->isToday() ? $currentHour : 24;
            for ($i = 0; $i < $maxHour; $i++) {
                $fullHours[$i] = $hourlyData[$i] ?? 0;
            }

            return $fullHours;
        });
    }

    #[Computed]
    public function dailyStats(): array
    {
        $filterUser = $this->filterUser;
        return Cache::remember($this->cacheKey('daily'), $this->cacheTTL(), function () use ($filterUser) {
            $endDate = Carbon::parse($this->selectedDate)->endOfDay();
            $startDate = $endDate->copy()->subDays(6)->startOfDay();

            $dailyData = SongPlay::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as plays')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($filterUser, fn($q) => $q->where('user_id', $filterUser))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('plays', 'date')
                ->toArray();

            $fullDays = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = $endDate->copy()->subDays($i)->format('Y-m-d');
                $fullDays[$date] = $dailyData[$date] ?? 0;
            }

            return $fullDays;
        });
    }

    #[Computed]
    public function weeklyStats(): array
    {
        $filterUser = $this->filterUser;
        return Cache::remember($this->cacheKey('weekly'), $this->cacheTTL(), function () use ($filterUser) {
            $endDate = Carbon::parse($this->selectedDate)->endOfWeek();
            $startDate = $endDate->copy()->subWeeks(3)->startOfWeek();

            $weeklyData = SongPlay::select(
                    DB::raw('YEARWEEK(created_at, 1) as yearweek'),
                    DB::raw('COUNT(*) as plays')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($filterUser, fn($q) => $q->where('user_id', $filterUser))
                ->groupBy('yearweek')
                ->orderBy('yearweek')
                ->pluck('plays', 'yearweek')
                ->toArray();

            $fullWeeks = [];
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = $endDate->copy()->subWeeks($i)->startOfWeek();
                $yearweek = $weekStart->format('oW');
                $label = $weekStart->format('d M') . ' - ' . $weekStart->copy()->endOfWeek()->format('d M');
                $fullWeeks[$label] = $weeklyData[$yearweek] ?? 0;
            }

            return $fullWeeks;
        });
    }

    #[Computed]
    public function monthlyStats(): array
    {
        $filterUser = $this->filterUser;
        return Cache::remember($this->cacheKey('monthly'), $this->cacheTTL(), function () use ($filterUser) {
            $endDate = Carbon::parse($this->selectedDate)->endOfMonth();
            $startDate = $endDate->copy()->subMonths(5)->startOfMonth();

            $monthlyData = SongPlay::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as plays')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($filterUser, fn($q) => $q->where('user_id', $filterUser))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('plays', 'month')
                ->toArray();

            $fullMonths = [];
            for ($i = 5; $i >= 0; $i--) {
                $monthDate = $endDate->copy()->subMonths($i);
                $key = $monthDate->format('Y-m');
                $label = $monthDate->translatedFormat('F Y');
                $fullMonths[$label] = $monthlyData[$key] ?? 0;
            }

            return $fullMonths;
        });
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    #[Computed]
    public function lastCacheTime(): string
    {
        $key = $this->cacheKey('cache_time');
        $cacheTime = Cache::get($key);

        if (!$cacheTime) {
            $cacheTime = now();
            Cache::put($key, $cacheTime, $this->cacheTTL());
        }

        return $cacheTime->format('H:i:s');
    }

    public function refreshData(): void
    {
        $suffixes = ['stats', 'hourly', 'daily', 'weekly', 'monthly', 'cache_time'];
        foreach ($suffixes as $suffix) {
            Cache::forget($this->cacheKey($suffix));
        }

        Cache::put($this->cacheKey('cache_time'), now(), $this->cacheTTL());

        $this->dispatch('toast', [
            'title' => 'Başarılı',
            'message' => 'Veriler yenilendi',
            'type' => 'success',
        ]);
    }

    public function goToToday(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function goToPreviousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->format('Y-m-d');
        $this->resetPage();
    }

    public function goToNextDay(): void
    {
        $nextDay = Carbon::parse($this->selectedDate)->addDay();
        if ($nextDay->lte(now())) {
            $this->selectedDate = $nextDay->format('Y-m-d');
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('muzibu::admin.livewire.listening-history-component');
    }
}
