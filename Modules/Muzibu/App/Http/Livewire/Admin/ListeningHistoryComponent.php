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
        return "listening_history_{$tenantId}_{$this->selectedDate}_{$suffix}";
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
        return Cache::remember($this->cacheKey('stats'), $this->cacheTTL(), function () {
            $date = Carbon::parse($this->selectedDate);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            $totalPlays = SongPlay::whereBetween('created_at', [$startOfDay, $endOfDay])->count();

            $uniqueListeners = SongPlay::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');

            $uniqueSongs = SongPlay::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->distinct('song_id')
                ->count('song_id');

            $totalDuration = SongPlay::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->sum('listened_duration');

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
        return Cache::remember($this->cacheKey('hourly'), $this->cacheTTL(), function () {
            $date = Carbon::parse($this->selectedDate);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            $hourlyData = SongPlay::select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as plays')
                )
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('plays', 'hour')
                ->toArray();

            // Tüm 24 saati doldur
            $fullHours = [];
            for ($i = 0; $i < 24; $i++) {
                $fullHours[$i] = $hourlyData[$i] ?? 0;
            }

            return $fullHours;
        });
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
        $suffixes = ['stats', 'hourly', 'cache_time'];
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
