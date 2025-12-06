<?php

namespace Modules\Muzibu\App\Http\Livewire\Frontend;

use Livewire\Component;
use Modules\Muzibu\App\Models\{Song, Album, Playlist, Artist, Genre, Sector, Radio};
use Modules\Search\App\Models\SearchQuery;

class SearchResults extends Component
{
    public $query = '';
    public $results = [];
    public $totalCount = 0;
    public $responseTime = 0;
    public $activeTab = 'all';

    protected $queryString = ['query' => ['as' => 'q'], 'activeTab' => ['as' => 'tab']];

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
        $this->search();
    }

    public function search()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->totalCount = 0;
            return;
        }

        $startTime = microtime(true);

        try {
            $this->results = [];
            $totalResults = 0;

            // Songs
            if ($this->activeTab === 'all' || $this->activeTab === 'songs') {
                $songs = Song::search($this->query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(20)
                    ->get();

                foreach ($songs as $song) {
                    $this->results[] = [
                        'type' => 'song',
                        'type_label' => '🎵 Şarkı',
                        'id' => $song->song_id,
                        'title' => $song->getTranslated('title', app()->getLocale()),
                        'url' => $song->getUrl(),
                        'image' => $song->getCoverUrl(300, 300),
                        'duration' => $song->getFormattedDuration(),
                        'artist' => $song->album?->artist?->getTranslated('title', app()->getLocale()),
                        'album' => $song->album?->getTranslated('title', app()->getLocale()),
                    ];
                }

                $totalResults += $songs->count();
            }

            // Albums
            if ($this->activeTab === 'all' || $this->activeTab === 'albums') {
                $albums = Album::search($this->query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                foreach ($albums as $album) {
                    $this->results[] = [
                        'type' => 'album',
                        'type_label' => '💿 Albüm',
                        'id' => $album->album_id,
                        'title' => $album->getTranslated('title', app()->getLocale()),
                        'url' => $album->getUrl(),
                        'image' => $album->getCoverUrl(300, 300),
                        'artist' => $album->artist?->getTranslated('title', app()->getLocale()),
                    ];
                }

                $totalResults += $albums->count();
            }

            // Artists
            if ($this->activeTab === 'all' || $this->activeTab === 'artists') {
                $artists = Artist::search($this->query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                foreach ($artists as $artist) {
                    $this->results[] = [
                        'type' => 'artist',
                        'type_label' => '🎤 Sanatçı',
                        'id' => $artist->artist_id,
                        'title' => $artist->getTranslated('title', app()->getLocale()),
                        'url' => $artist->getUrl(),
                        'image' => $artist->getCoverUrl(300, 300),
                    ];
                }

                $totalResults += $artists->count();
            }

            // Playlists
            if ($this->activeTab === 'all' || $this->activeTab === 'playlists') {
                $playlists = Playlist::search($this->query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1)->where('is_public', '=', 1))
                    ->take(10)
                    ->get();

                foreach ($playlists as $playlist) {
                    $this->results[] = [
                        'type' => 'playlist',
                        'type_label' => '📃 Playlist',
                        'id' => $playlist->playlist_id,
                        'title' => $playlist->getTranslated('title', app()->getLocale()),
                        'url' => $playlist->getUrl(),
                        'image' => $playlist->getCoverUrl(300, 300),
                    ];
                }

                $totalResults += $playlists->count();
            }

            // Genres
            if ($this->activeTab === 'all' || $this->activeTab === 'genres') {
                $genres = Genre::search($this->query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                foreach ($genres as $genre) {
                    $this->results[] = [
                        'type' => 'genre',
                        'type_label' => '🎼 Tür',
                        'id' => $genre->genre_id,
                        'title' => $genre->getTranslated('title', app()->getLocale()),
                        'url' => $genre->getUrl(),
                    ];
                }

                $totalResults += $genres->count();
            }

            // Sectors
            if ($this->activeTab === 'all' || $this->activeTab === 'sectors') {
                $sectors = Sector::search($this->query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                foreach ($sectors as $sector) {
                    $this->results[] = [
                        'type' => 'sector',
                        'type_label' => '🏢 Sektör',
                        'id' => $sector->sector_id,
                        'title' => $sector->getTranslated('title', app()->getLocale()),
                        'url' => '/muzibu/sector/' . $sector->getTranslated('slug', app()->getLocale()),
                    ];
                }

                $totalResults += $sectors->count();
            }

            // Radios
            if ($this->activeTab === 'all' || $this->activeTab === 'radios') {
                $radios = Radio::search($this->query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                foreach ($radios as $radio) {
                    $this->results[] = [
                        'type' => 'radio',
                        'type_label' => '📻 Radyo',
                        'id' => $radio->radio_id,
                        'title' => $radio->getTranslated('title', app()->getLocale()),
                        'url' => '/muzibu/radio/' . $radio->getTranslated('slug', app()->getLocale()),
                    ];
                }

                $totalResults += $radios->count();
            }

            $this->totalCount = $totalResults;
            $this->responseTime = (int) round((microtime(true) - $startTime) * 1000);

            // Log search to Search module
            SearchQuery::create([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'query' => $this->query,
                'searchable_type' => $this->activeTab,
                'results_count' => $this->totalCount,
                'response_time_ms' => $this->responseTime,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'locale' => app()->getLocale(),
                'referrer_url' => request()->header('referer'),
                'is_visible_in_tags' => $this->totalCount > 0,
                'is_popular' => false,
                'is_hidden' => false,
            ]);

        } catch (\Exception $e) {
            \Log::error('Search error:', ['query' => $this->query, 'message' => $e->getMessage()]);
            $this->results = [];
            $this->totalCount = 0;
        }
    }

    public function render()
    {
        return view('muzibu::livewire.frontend.search-results')
            ->layout('themes.muzibu.layouts.app');
    }
}
