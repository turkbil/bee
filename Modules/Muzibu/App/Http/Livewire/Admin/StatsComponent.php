<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Artist;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\SongPlay;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StatsComponent extends Component
{
    public string $period = '7'; // 7, 30, 90, 365 days

    private function cacheKey(string $suffix): string
    {
        $tenantId = tenant('id') ?? 'central';
        $today = now()->format('Y-m-d');
        return "muzibu_stats_{$tenantId}_{$today}_{$this->period}_{$suffix}";
    }

    // Gece yarısına kadar cache'le
    private function cacheTTL(): int
    {
        return (int) now()->endOfDay()->diffInSeconds(now());
    }

    #[Computed]
    public function totalPlays(): int
    {
        return Cache::remember($this->cacheKey('total_plays'), $this->cacheTTL(), function () {
            return SongPlay::where('created_at', '>=', now()->subDays((int)$this->period))->count();
        });
    }

    #[Computed]
    public function uniqueListeners(): int
    {
        return Cache::remember($this->cacheKey('unique_listeners'), $this->cacheTTL(), function () {
            return SongPlay::where('created_at', '>=', now()->subDays((int)$this->period))
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');
        });
    }

    #[Computed]
    public function totalListeningHours(): float
    {
        return Cache::remember($this->cacheKey('listening_hours'), $this->cacheTTL(), function () {
            $seconds = SongPlay::where('created_at', '>=', now()->subDays((int)$this->period))
                ->sum('listened_duration');
            return round($seconds / 3600, 1);
        });
    }

    #[Computed]
    public function avgPlaysPerUser(): float
    {
        $listeners = $this->uniqueListeners;
        if ($listeners === 0) return 0;
        return round($this->totalPlays / $listeners, 1);
    }

    #[Computed]
    public function topSongs()
    {
        return Cache::remember($this->cacheKey('top_songs'), $this->cacheTTL(), function () {
            $startDate = now()->subDays((int)$this->period);

            return SongPlay::select('song_id', DB::raw('COUNT(*) as play_count_period'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('song_id')
                ->orderByDesc('play_count_period')
                ->limit(10)
                ->get()
                ->map(function($play) {
                    $song = Song::with('artist')->find($play->song_id);
                    return (object)[
                        'title' => $song?->title ?? 'Silinmiş Şarkı',
                        'artist_title' => $song?->artist?->title ?? '-',
                        'play_count_period' => $play->play_count_period,
                    ];
                });
        });
    }

    #[Computed]
    public function topAlbums()
    {
        return Cache::remember($this->cacheKey('top_albums'), $this->cacheTTL(), function () {
            $startDate = now()->subDays((int)$this->period);

            return SongPlay::select('muzibu_songs.album_id', DB::raw('COUNT(muzibu_song_plays.id) as total_plays'))
                ->join('muzibu_songs', 'muzibu_song_plays.song_id', '=', 'muzibu_songs.song_id')
                ->where('muzibu_song_plays.created_at', '>=', $startDate)
                ->whereNotNull('muzibu_songs.album_id')
                ->groupBy('muzibu_songs.album_id')
                ->orderByDesc('total_plays')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    $album = Album::with('artist')->find($item->album_id);
                    return (object)[
                        'title' => $album?->title ?? 'Bilinmeyen Albüm',
                        'artist_title' => $album?->artist?->title ?? '-',
                        'cover' => $album?->getFirstMediaUrl('hero', 'thumb'),
                        'total_plays' => $item->total_plays,
                    ];
                });
        });
    }

    #[Computed]
    public function topArtists()
    {
        return Cache::remember($this->cacheKey('top_artists'), $this->cacheTTL(), function () {
            $startDate = now()->subDays((int)$this->period);

            return SongPlay::select('muzibu_albums.artist_id', DB::raw('COUNT(muzibu_song_plays.id) as total_plays'))
                ->join('muzibu_songs', 'muzibu_song_plays.song_id', '=', 'muzibu_songs.song_id')
                ->join('muzibu_albums', 'muzibu_songs.album_id', '=', 'muzibu_albums.album_id')
                ->where('muzibu_song_plays.created_at', '>=', $startDate)
                ->whereNotNull('muzibu_albums.artist_id')
                ->groupBy('muzibu_albums.artist_id')
                ->orderByDesc('total_plays')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    $artist = Artist::find($item->artist_id);
                    return (object)[
                        'title' => $artist?->title ?? 'Bilinmeyen',
                        'cover' => $artist?->getFirstMediaUrl('hero', 'thumb'),
                        'total_plays' => $item->total_plays,
                    ];
                });
        });
    }

    #[Computed]
    public function topListeners()
    {
        return Cache::remember($this->cacheKey('top_listeners'), $this->cacheTTL(), function () {
            $startDate = now()->subDays((int)$this->period);

            return SongPlay::select('user_id',
                    DB::raw('COUNT(*) as total_plays'),
                    DB::raw('SUM(listened_duration) as total_duration')
                )
                ->where('created_at', '>=', $startDate)
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderByDesc('total_plays')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    $user = User::find($item->user_id);
                    return (object)[
                        'name' => $user?->name ?? 'Silinmiş Kullanıcı',
                        'email' => $user?->email ?? '',
                        'avatar' => $user ? substr($user->name, 0, 1) : '?',
                        'total_plays' => $item->total_plays,
                        'total_hours' => round(($item->total_duration ?? 0) / 3600, 1),
                    ];
                });
        });
    }

    #[Computed]
    public function dailyStats()
    {
        return Cache::remember($this->cacheKey('daily_stats'), $this->cacheTTL(), function () {
            return SongPlay::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as plays'),
                    DB::raw('COUNT(DISTINCT user_id) as listeners')
                )
                ->where('created_at', '>=', now()->subDays((int)$this->period))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        });
    }

    #[Computed]
    public function userStats(): array
    {
        return Cache::remember($this->cacheKey('user_stats'), $this->cacheTTL(), function () {
            $startDate = now()->subDays((int)$this->period);

            $newUsers = User::where('created_at', '>=', $startDate)->count();

            $activeListeners = SongPlay::where('created_at', '>=', $startDate)
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');

            $totalUsers = User::count();

            $peakDay = SongPlay::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as plays'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderByDesc('plays')
                ->first();

            return [
                'new_users' => $newUsers,
                'active_listeners' => $activeListeners,
                'total_users' => $totalUsers,
                'activity_rate' => $totalUsers > 0 ? round(($activeListeners / $totalUsers) * 100, 1) : 0,
                'peak_day' => $peakDay?->date ? Carbon::parse($peakDay->date)->format('d.m.Y') : '-',
                'peak_plays' => $peakDay?->plays ?? 0,
            ];
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

        return $cacheTime->format('d.m.Y H:i');
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    public function refreshStats(): void
    {
        $suffixes = ['total_plays', 'unique_listeners', 'listening_hours', 'top_songs',
                     'top_albums', 'top_artists', 'top_listeners', 'daily_stats', 'user_stats', 'cache_time'];

        $tenantId = tenant('id') ?? 'central';
        $today = now()->format('Y-m-d');

        foreach (['7', '30', '90', '365'] as $period) {
            foreach ($suffixes as $suffix) {
                Cache::forget("muzibu_stats_{$tenantId}_{$today}_{$period}_{$suffix}");
            }
        }

        // Cache time'ı şimdi olarak güncelle
        Cache::put($this->cacheKey('cache_time'), now(), $this->cacheTTL());

        $this->dispatch('toast', [
            'title' => 'Başarılı',
            'message' => 'İstatistikler yenilendi',
            'type' => 'success',
        ]);
    }

    public function render()
    {
        return view('muzibu::admin.livewire.stats-component');
    }
}
