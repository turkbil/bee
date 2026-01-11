<?php

namespace Modules\Muzibu\App\Http\Livewire\Frontend;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\{Song, Album, Playlist, Artist, Genre, Sector, Radio};
use Modules\Search\App\Models\SearchQuery;

class SearchResults extends Component
{
    public $query = '';
    public $totalCount = 0;
    public $responseTime = 0;
    public $activeTab = 'all';
    public $counts = [];
    public $fromCache = false;

    // Model ID'leri (cache için)
    public $songIds = [];
    public $albumIds = [];
    public $artistIds = [];
    public $playlistIds = [];
    public $genreIds = [];
    public $sectorIds = [];
    public $radioIds = [];
    public $myPlaylistIds = [];

    protected $queryString = ['query' => ['as' => 'q'], 'activeTab' => ['as' => 'tab']];

    const CACHE_TTL = 10;

    public function mount()
    {
        if ($this->query) {
            $this->search();
        }
    }

    public function updatedQuery()
    {
        $this->search();
    }

    public function updatedActiveTab()
    {
        // View'da filtreleme yapılıyor
    }

    private function getCacheKey(): string
    {
        $tenantId = tenant() ? tenant()->id : 'central';
        $locale = app()->getLocale();
        $normalizedQuery = mb_strtolower(trim($this->query));
        return "muzibu_search_v2:{$tenantId}:{$locale}:" . md5($normalizedQuery);
    }

    public function search()
    {
        if (strlen($this->query) < 2) {
            $this->resetResults();
            return;
        }

        $startTime = microtime(true);
        $cacheKey = $this->getCacheKey();

        // Cache'den dene
        $cached = Cache::get($cacheKey);
        if ($cached) {
            $this->loadFromCache($cached);
            $this->responseTime = (int) round((microtime(true) - $startTime) * 1000);
            $this->fromCache = true;
            return;
        }

        $this->fromCache = false;
        $this->performSearch($startTime, $cacheKey);
    }

    private function resetResults()
    {
        $this->songIds = [];
        $this->albumIds = [];
        $this->artistIds = [];
        $this->playlistIds = [];
        $this->genreIds = [];
        $this->sectorIds = [];
        $this->radioIds = [];
        $this->myPlaylistIds = [];
        $this->totalCount = 0;
        $this->counts = [];
        $this->fromCache = false;
    }

    private function loadFromCache($cached)
    {
        $this->songIds = $cached['songIds'] ?? [];
        $this->albumIds = $cached['albumIds'] ?? [];
        $this->artistIds = $cached['artistIds'] ?? [];
        $this->playlistIds = $cached['playlistIds'] ?? [];
        $this->genreIds = $cached['genreIds'] ?? [];
        $this->sectorIds = $cached['sectorIds'] ?? [];
        $this->radioIds = $cached['radioIds'] ?? [];
        $this->myPlaylistIds = $cached['myPlaylistIds'] ?? [];
        $this->counts = $cached['counts'] ?? [];
        $this->totalCount = $cached['total'] ?? 0;
    }

    private function performSearch($startTime, $cacheKey)
    {
        try {
            $this->resetResults();
            $locale = app()->getLocale();

            $this->counts = [
                'songs' => 0,
                'albums' => 0,
                'artists' => 0,
                'playlists' => 0,
                'genres' => 0,
                'sectors' => 0,
                'radios' => 0,
                'myplaylists' => 0,
            ];

            // Songs
            $songs = Song::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(30)
                ->get();
            $this->songIds = $songs->pluck('song_id')->toArray();
            $this->counts['songs'] = count($this->songIds);

            // Albums
            $albums = Album::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(15)
                ->get();
            $this->albumIds = $albums->pluck('album_id')->toArray();
            $this->counts['albums'] = count($this->albumIds);

            // Artists
            $artists = Artist::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(15)
                ->get();
            $this->artistIds = $artists->pluck('artist_id')->toArray();
            $this->counts['artists'] = count($this->artistIds);

            // Playlists
            $playlists = Playlist::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1)->where('is_public', 1))
                ->take(15)
                ->get();
            $this->playlistIds = $playlists->pluck('playlist_id')->toArray();
            $this->counts['playlists'] = count($this->playlistIds);

            // Genres
            $genres = Genre::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(15)
                ->get();
            $this->genreIds = $genres->pluck('genre_id')->toArray();
            $this->counts['genres'] = count($this->genreIds);

            // Sectors
            $sectors = Sector::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(15)
                ->get();
            $this->sectorIds = $sectors->pluck('sector_id')->toArray();
            $this->counts['sectors'] = count($this->sectorIds);

            // Radios
            $radios = Radio::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(15)
                ->get();
            $this->radioIds = $radios->pluck('radio_id')->toArray();
            $this->counts['radios'] = count($this->radioIds);

            // My Playlists
            if (auth()->check()) {
                $myPlaylists = Playlist::where('user_id', auth()->id())
                    ->where(function ($q) use ($locale) {
                        $q->where("title->{$locale}", 'like', '%' . $this->query . '%')
                          ->orWhere('title->tr', 'like', '%' . $this->query . '%')
                          ->orWhere('title->en', 'like', '%' . $this->query . '%');
                    })
                    ->take(15)
                    ->get();
                $this->myPlaylistIds = $myPlaylists->pluck('playlist_id')->toArray();
                $this->counts['myplaylists'] = count($this->myPlaylistIds);
            }

            $this->totalCount = array_sum($this->counts);
            $this->responseTime = (int) round((microtime(true) - $startTime) * 1000);

            // Cache'e kaydet
            Cache::put($cacheKey, [
                'songIds' => $this->songIds,
                'albumIds' => $this->albumIds,
                'artistIds' => $this->artistIds,
                'playlistIds' => $this->playlistIds,
                'genreIds' => $this->genreIds,
                'sectorIds' => $this->sectorIds,
                'radioIds' => $this->radioIds,
                'myPlaylistIds' => $this->myPlaylistIds,
                'counts' => $this->counts,
                'total' => $this->totalCount,
            ], now()->addMinutes(self::CACHE_TTL));

            // Log search
            SearchQuery::create([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'query' => $this->query,
                'searchable_type' => 'all',
                'results_count' => $this->totalCount,
                'response_time_ms' => $this->responseTime,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'locale' => $locale,
                'referrer_url' => request()->header('referer'),
                'is_visible_in_tags' => $this->totalCount > 0,
                'is_popular' => false,
                'is_hidden' => false,
            ]);

        } catch (\Exception $e) {
            \Log::error('🔍 Search exception: ' . $e->getMessage());
            $this->resetResults();
        }
    }

    public function render()
    {
        // Modelleri ID'lerden çek (eager loading ile)
        $songs = !empty($this->songIds)
            ? Song::with(['album.artist'])->whereIn('song_id', $this->songIds)->get()
            : collect();

        $albums = !empty($this->albumIds)
            ? Album::with(['artist', 'coverMedia'])->whereIn('album_id', $this->albumIds)->get()
            : collect();

        $artists = !empty($this->artistIds)
            ? Artist::with(['photoMedia'])->whereIn('artist_id', $this->artistIds)->get()
            : collect();

        $playlists = !empty($this->playlistIds)
            ? Playlist::with(['coverMedia'])->whereIn('playlist_id', $this->playlistIds)->get()
            : collect();

        $genres = !empty($this->genreIds)
            ? Genre::with(['iconMedia'])->whereIn('genre_id', $this->genreIds)->get()
            : collect();

        $sectors = !empty($this->sectorIds)
            ? Sector::with(['iconMedia'])->whereIn('sector_id', $this->sectorIds)->get()
            : collect();

        $radios = !empty($this->radioIds)
            ? Radio::with(['logoMedia'])->whereIn('radio_id', $this->radioIds)->get()
            : collect();

        $myPlaylists = !empty($this->myPlaylistIds)
            ? Playlist::with(['coverMedia'])->whereIn('playlist_id', $this->myPlaylistIds)->get()
            : collect();

        return view('muzibu::livewire.frontend.search-results', [
            'songs' => $songs,
            'albums' => $albums,
            'artists' => $artists,
            'playlists' => $playlists,
            'genres' => $genres,
            'sectors' => $sectors,
            'radios' => $radios,
            'myPlaylists' => $myPlaylists,
        ])->layout('themes.muzibu.layouts.app');
    }
}
