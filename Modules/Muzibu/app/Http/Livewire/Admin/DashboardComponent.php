<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Artist;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Radio;
use Modules\Muzibu\App\Models\Sector;
use Modules\Muzibu\App\Models\SongPlay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardComponent extends Component
{
    #[Url]
    public string $selectedDate = '';

    #[Url]
    public string $viewMode = 'hourly'; // hourly, daily, weekly, monthly

    public function mount(): void
    {
        if (empty($this->selectedDate)) {
            $this->selectedDate = now()->format('Y-m-d');
        }
    }

    private function cacheKey(string $suffix): string
    {
        $tenantId = tenant('id') ?? 'central';
        return "dashboard_{$tenantId}_{$this->selectedDate}_{$suffix}";
    }

    // 5 dakika cache
    private function cacheTTL(): int
    {
        return 300;
    }

    public function goToPreviousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->format('Y-m-d');
    }

    public function goToNextDay(): void
    {
        $nextDay = Carbon::parse($this->selectedDate)->addDay();
        if ($nextDay->lte(now())) {
            $this->selectedDate = $nextDay->format('Y-m-d');
        }
    }

    public function goToToday(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function refreshStats(): void
    {
        $suffixes = ['hourly', 'daily', 'weekly', 'monthly', 'listening_stats'];
        foreach ($suffixes as $suffix) {
            Cache::forget($this->cacheKey($suffix));
        }

        $this->dispatch('toast', [
            'title' => 'Başarılı',
            'message' => 'Veriler yenilendi',
            'type' => 'success',
        ]);
    }

    #[Computed]
    public function listeningStats(): array
    {
        return Cache::remember($this->cacheKey('listening_stats'), $this->cacheTTL(), function () {
            $date = Carbon::parse($this->selectedDate);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            // Performans için current hour hariç
            $isToday = $date->isToday();
            if ($isToday) {
                $endOfDay = now()->subHour()->endOfHour();
            }

            $totalPlays = SongPlay::whereBetween('created_at', [$startOfDay, $endOfDay])->count();

            $uniqueListeners = SongPlay::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');

            $totalDuration = SongPlay::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->sum('listened_duration');

            return [
                'total_plays' => $totalPlays,
                'unique_listeners' => $uniqueListeners,
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

            // Performans için bugünse current hour hariç
            $currentHour = $date->isToday() ? now()->hour : 24;

            $hourlyData = SongPlay::select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as plays')
                )
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->when($date->isToday(), function($q) use ($currentHour) {
                    $q->whereRaw('HOUR(created_at) < ?', [$currentHour]);
                })
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('plays', 'hour')
                ->toArray();

            // 0-23 saat doldur (bugünse current hour'a kadar)
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
        return Cache::remember($this->cacheKey('daily'), $this->cacheTTL(), function () {
            // Son 7 gün
            $endDate = Carbon::parse($this->selectedDate)->endOfDay();
            $startDate = $endDate->copy()->subDays(6)->startOfDay();

            $dailyData = SongPlay::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as plays')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('plays', 'date')
                ->toArray();

            // 7 günlük veri
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
        return Cache::remember($this->cacheKey('weekly'), $this->cacheTTL(), function () {
            // Son 4 hafta
            $endDate = Carbon::parse($this->selectedDate)->endOfWeek();
            $startDate = $endDate->copy()->subWeeks(3)->startOfWeek();

            $weeklyData = SongPlay::select(
                    DB::raw('YEARWEEK(created_at, 1) as yearweek'),
                    DB::raw('COUNT(*) as plays')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('yearweek')
                ->orderBy('yearweek')
                ->pluck('plays', 'yearweek')
                ->toArray();

            // 4 haftalık veri
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
        return Cache::remember($this->cacheKey('monthly'), $this->cacheTTL(), function () {
            // Son 6 ay
            $endDate = Carbon::parse($this->selectedDate)->endOfMonth();
            $startDate = $endDate->copy()->subMonths(5)->startOfMonth();

            $monthlyData = SongPlay::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as plays')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('plays', 'month')
                ->toArray();

            // 6 aylık veri
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

    #[Computed]
    public function totalSongs(): int
    {
        return Song::count();
    }

    #[Computed]
    public function totalAlbums(): int
    {
        return Album::count();
    }

    #[Computed]
    public function totalArtists(): int
    {
        return Artist::count();
    }

    #[Computed]
    public function totalPlaylists(): int
    {
        return Playlist::count();
    }

    #[Computed]
    public function totalGenres(): int
    {
        return Genre::count();
    }

    #[Computed]
    public function totalRadios(): int
    {
        return Radio::count();
    }

    #[Computed]
    public function totalSectors(): int
    {
        return Sector::count();
    }

    #[Computed]
    public function recentSongs()
    {
        return Song::with(['album.artist', 'genre'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function popularSongs()
    {
        return Song::with(['album.artist'])
            ->where('play_count', '>', 0)
            ->orderBy('play_count', 'desc')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function hlsStats(): array
    {
        return [
            'completed' => Song::whereNotNull('hls_path')->count(),
            'pending' => Song::whereNotNull('file_path')
                ->whereNull('hls_path')
                ->count(),
            'failed' => 0, // TODO: Implement failed tracking
        ];
    }

    public function render()
    {
        return view('muzibu::admin.livewire.dashboard-component');
    }
}
