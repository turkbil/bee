<?php

namespace Modules\Muzibu\App\Http\Livewire\Frontend;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\{Song, Album, Playlist, Artist, Genre, Sector, Radio};
use Modules\Search\App\Models\SearchQuery;

class SearchResults extends Component
{
    public $query = '';
    public $allResults = []; // Tüm sonuçlar burada cache'lenir
    public $totalCount = 0;
    public $responseTime = 0;
    public $activeTab = 'all';
    public $counts = []; // Her tip için sayılar
    public $fromCache = false; // Cache'den mi geldi?

    protected $queryString = ['query' => ['as' => 'q'], 'activeTab' => ['as' => 'tab']];

    // Cache süresi (dakika)
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

    // Tab değişikliğinde yeniden sorgu YAPILMAZ - sadece frontend'de filtrele
    public function updatedActiveTab()
    {
        // Hiçbir şey yapma - filtreleme view'da yapılıyor
    }

    // Filtrelenmiş sonuçları döndür (computed property gibi)
    public function getFilteredResultsProperty()
    {
        if ($this->activeTab === 'all') {
            return $this->allResults;
        }

        return collect($this->allResults)->filter(function ($item) {
            return $item['type'] === rtrim($this->activeTab, 's'); // songs -> song
        })->values()->toArray();
    }

    // Cache key oluştur
    private function getCacheKey(): string
    {
        $tenantId = tenant() ? tenant()->id : 'central';
        $locale = app()->getLocale();
        $normalizedQuery = mb_strtolower(trim($this->query));
        return "muzibu_search:{$tenantId}:{$locale}:" . md5($normalizedQuery);
    }

    public function search()
    {
        if (strlen($this->query) < 2) {
            $this->allResults = [];
            $this->totalCount = 0;
            $this->counts = [];
            $this->fromCache = false;
            return;
        }

        $startTime = microtime(true);
        $locale = app()->getLocale();
        $cacheKey = $this->getCacheKey();

        // Cache'den dene
        $cached = Cache::get($cacheKey);
        if ($cached) {
            $this->allResults = $cached['results'];
            $this->counts = $cached['counts'];
            $this->totalCount = $cached['total'];
            $this->responseTime = (int) round((microtime(true) - $startTime) * 1000);
            $this->fromCache = true;
            return;
        }

        $this->fromCache = false;

        try {
            $this->allResults = [];
            $this->counts = [
                'songs' => 0,
                'albums' => 0,
                'artists' => 0,
                'playlists' => 0,
                'genres' => 0,
                'sectors' => 0,
                'radios' => 0,
            ];

            // Songs - Eager load relations
            $songs = Song::search($this->query)
                ->query(fn ($q) => $q->with(['album.artist'])->where('is_active', 1))
                ->take(30)
                ->get();

            foreach ($songs as $song) {
                $this->allResults[] = [
                    'type' => 'song',
                    'type_label' => '🎵 Şarkı',
                    'id' => $song->song_id,
                    'title' => $song->getTranslated('title', $locale),
                    'url' => $song->getUrl(),
                    'image' => $song->getCoverUrl(300, 300),
                    'duration' => $song->getFormattedDuration(),
                    'artist' => $song->album?->artist?->getTranslated('title', $locale),
                    'album' => $song->album?->getTranslated('title', $locale),
                ];
            }
            $this->counts['songs'] = $songs->count();

            // Albums - Eager load
            $albums = Album::search($this->query)
                ->query(fn ($q) => $q->with(['artist'])->where('is_active', 1))
                ->take(15)
                ->get();

            foreach ($albums as $album) {
                $this->allResults[] = [
                    'type' => 'album',
                    'type_label' => '💿 Albüm',
                    'id' => $album->album_id,
                    'title' => $album->getTranslated('title', $locale),
                    'url' => $album->getUrl(),
                    'image' => $album->getCoverUrl(300, 300),
                    'artist' => $album->artist?->getTranslated('title', $locale),
                ];
            }
            $this->counts['albums'] = $albums->count();

            // Artists
            $artists = Artist::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(15)
                ->get();

            foreach ($artists as $artist) {
                $this->allResults[] = [
                    'type' => 'artist',
                    'type_label' => '🎤 Sanatçı',
                    'id' => $artist->artist_id,
                    'title' => $artist->getTranslated('title', $locale),
                    'url' => $artist->getUrl(),
                    'image' => $artist->getCoverUrl(300, 300),
                ];
            }
            $this->counts['artists'] = $artists->count();

            // Playlists
            $playlists = Playlist::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1)->where('is_public', 1))
                ->take(15)
                ->get();

            foreach ($playlists as $playlist) {
                $this->allResults[] = [
                    'type' => 'playlist',
                    'type_label' => '📃 Playlist',
                    'id' => $playlist->playlist_id,
                    'title' => $playlist->getTranslated('title', $locale),
                    'url' => $playlist->getUrl(),
                    'image' => $playlist->getCoverUrl(300, 300),
                ];
            }
            $this->counts['playlists'] = $playlists->count();

            // Genres
            $genres = Genre::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(15)
                ->get();

            foreach ($genres as $genre) {
                $this->allResults[] = [
                    'type' => 'genre',
                    'type_label' => '🎼 Tür',
                    'id' => $genre->genre_id,
                    'title' => $genre->getTranslated('title', $locale),
                    'url' => $genre->getUrl(),
                ];
            }
            $this->counts['genres'] = $genres->count();

            // Sectors
            $sectors = Sector::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(15)
                ->get();

            foreach ($sectors as $sector) {
                $this->allResults[] = [
                    'type' => 'sector',
                    'type_label' => '🏢 Sektör',
                    'id' => $sector->sector_id,
                    'title' => $sector->getTranslated('title', $locale),
                    'url' => $sector->getUrl(),
                ];
            }
            $this->counts['sectors'] = $sectors->count();

            // Radios
            $radios = Radio::search($this->query)
                ->query(fn ($q) => $q->where('is_active', 1))
                ->take(15)
                ->get();

            foreach ($radios as $radio) {
                $this->allResults[] = [
                    'type' => 'radio',
                    'type_label' => '📻 Radyo',
                    'id' => $radio->radio_id,
                    'title' => $radio->getTranslated('title', $locale),
                    'url' => $radio->getUrl(),
                ];
            }
            $this->counts['radios'] = $radios->count();

            $this->totalCount = count($this->allResults);
            $this->responseTime = (int) round((microtime(true) - $startTime) * 1000);

            // Cache'e kaydet (10 dakika)
            Cache::put($cacheKey, [
                'results' => $this->allResults,
                'counts' => $this->counts,
                'total' => $this->totalCount,
            ], now()->addMinutes(self::CACHE_TTL));

            // Log search - sadece ilk aramada (cache miss)
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
            \Log::error('Search error:', ['query' => $this->query, 'message' => $e->getMessage()]);
            $this->allResults = [];
            $this->totalCount = 0;
            $this->counts = [];
        }
    }

    public function render()
    {
        return view('muzibu::livewire.frontend.search-results', [
            'results' => $this->filteredResults,
        ])->layout('themes.muzibu.layouts.app');
    }
}
