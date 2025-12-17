<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\{Song, Album, Playlist, Artist, Genre, Sector, Radio};
use Modules\Search\App\Models\SearchQuery;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $query = $request->input('q');
            $type = $request->input('type', 'all');

            if (!$query || strlen($query) < 2) {
                return response()->json(['error' => 'Query too short'], 400);
            }

            // 🔥 CRITICAL: Ensure tenant context for Meilisearch
            if (!tenant() || tenant()->central) {
                return response()->json(['error' => 'Tenant context required'], 400);
            }

            // 🌍 Get active languages dynamically (tenant-aware)
            try {
                $activeLangs = \DB::connection('tenant')
                    ->table('tenant_languages')
                    ->where('is_active', 1)
                    ->pluck('code')
                    ->toArray();
            } catch (\Exception $e) {
                $activeLangs = [];
            }

            // Fallback: Use default tenant locale if no active languages found
            if (empty($activeLangs)) {
                $activeLangs = [get_tenant_default_locale() ?? 'tr'];
            }

            // Current user's locale (frontend will use this)
            $currentLocale = app()->getLocale();

            $results = [];
            $totalResults = 0;

            // Songs - Meilisearch
            if ($type === 'all' || $type === 'songs') {
                $scoutResults = Song::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(20)
                    ->get();

                $results['songs'] = $scoutResults->map(function ($song) use ($activeLangs, $currentLocale) {
                    $songData = [
                        'song_id' => $song->song_id,
                        'duration' => $song->duration,
                        'file_path' => $song->file_path,
                        'hls_path' => $song->hls_path,
                    ];

                    // Add title for each active language
                    foreach ($activeLangs as $lang) {
                        $songData["title_{$lang}"] = $song->getTranslated('title', $lang);
                        $songData["slug_{$lang}"] = $song->getTranslated('slug', $lang);
                    }

                    // Add current locale's title as default (for backward compatibility)
                    $songData['title'] = $song->getTranslated('title', $currentLocale);
                    $songData['slug'] = $song->getTranslated('slug', $currentLocale);

                    // Album & Artist translations
                    if ($song->album) {
                        $albumData = [];
                        foreach ($activeLangs as $lang) {
                            $albumData["title_{$lang}"] = $song->album->getTranslated('title', $lang);
                        }
                        $albumData['title'] = $song->album->getTranslated('title', $currentLocale);
                        $songData['album'] = $albumData;

                        if ($song->album->artist) {
                            $artistData = [];
                            foreach ($activeLangs as $lang) {
                                $artistData["title_{$lang}"] = $song->album->artist->getTranslated('title', $lang);
                            }
                            $artistData['title'] = $song->album->artist->getTranslated('title', $currentLocale);
                            $songData['artist'] = $artistData;
                        }
                    }

                    return $songData;
                });

                $totalResults += $scoutResults->count();
            }

            // Albums - Meilisearch
            if ($type === 'all' || $type === 'albums') {
                $scoutResults = Album::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['albums'] = $scoutResults->map(function ($album) use ($activeLangs, $currentLocale) {
                    $albumData = ['album_id' => $album->album_id];

                    foreach ($activeLangs as $lang) {
                        $albumData["title_{$lang}"] = $album->getTranslated('title', $lang);
                        $albumData["slug_{$lang}"] = $album->getTranslated('slug', $lang);
                    }
                    $albumData['title'] = $album->getTranslated('title', $currentLocale);
                    $albumData['slug'] = $album->getTranslated('slug', $currentLocale);

                    if ($album->artist) {
                        $artistData = [];
                        foreach ($activeLangs as $lang) {
                            $artistData["title_{$lang}"] = $album->artist->getTranslated('title', $lang);
                        }
                        $artistData['title'] = $album->artist->getTranslated('title', $currentLocale);
                        $albumData['artist'] = $artistData;
                    }

                    return $albumData;
                });

                $totalResults += $scoutResults->count();
            }

            // Playlists - Meilisearch
            if ($type === 'all' || $type === 'playlists') {
                $scoutResults = Playlist::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1)->where('is_public', '=', 1))
                    ->take(10)
                    ->get();

                $results['playlists'] = $scoutResults->map(function ($playlist) use ($activeLangs, $currentLocale) {
                    $playlistData = [
                        'playlist_id' => $playlist->playlist_id,
                        'is_system' => $playlist->is_system,
                    ];

                    foreach ($activeLangs as $lang) {
                        $playlistData["title_{$lang}"] = $playlist->getTranslated('title', $lang);
                        $playlistData["slug_{$lang}"] = $playlist->getTranslated('slug', $lang);
                    }
                    $playlistData['title'] = $playlist->getTranslated('title', $currentLocale);
                    $playlistData['slug'] = $playlist->getTranslated('slug', $currentLocale);

                    return $playlistData;
                });

                $totalResults += $scoutResults->count();
            }

            // Artists - Meilisearch
            if ($type === 'all' || $type === 'artists') {
                $scoutResults = Artist::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['artists'] = $scoutResults->map(function ($artist) use ($activeLangs, $currentLocale) {
                    $artistData = ['artist_id' => $artist->artist_id];

                    foreach ($activeLangs as $lang) {
                        $artistData["title_{$lang}"] = $artist->getTranslated('title', $lang);
                        $artistData["slug_{$lang}"] = $artist->getTranslated('slug', $lang);
                    }
                    $artistData['title'] = $artist->getTranslated('title', $currentLocale);
                    $artistData['slug'] = $artist->getTranslated('slug', $currentLocale);

                    return $artistData;
                });

                $totalResults += $scoutResults->count();
            }

            // Genres - Meilisearch
            if ($type === 'all' || $type === 'genres') {
                $scoutResults = Genre::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['genres'] = $scoutResults->map(function ($genre) use ($activeLangs, $currentLocale) {
                    $genreData = ['genre_id' => $genre->genre_id];

                    foreach ($activeLangs as $lang) {
                        $genreData["title_{$lang}"] = $genre->getTranslated('title', $lang);
                        $genreData["slug_{$lang}"] = $genre->getTranslated('slug', $lang);
                    }
                    $genreData['title'] = $genre->getTranslated('title', $currentLocale);
                    $genreData['slug'] = $genre->getTranslated('slug', $currentLocale);

                    return $genreData;
                });

                $totalResults += $scoutResults->count();
            }

            // Sectors - Meilisearch
            if ($type === 'all' || $type === 'sectors') {
                $scoutResults = Sector::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['sectors'] = $scoutResults->map(function ($sector) use ($activeLangs, $currentLocale) {
                    $sectorData = ['sector_id' => $sector->sector_id];

                    foreach ($activeLangs as $lang) {
                        $sectorData["title_{$lang}"] = $sector->getTranslated('title', $lang);
                        $sectorData["slug_{$lang}"] = $sector->getTranslated('slug', $lang);
                    }
                    $sectorData['title'] = $sector->getTranslated('title', $currentLocale);
                    $sectorData['slug'] = $sector->getTranslated('slug', $currentLocale);

                    return $sectorData;
                });

                $totalResults += $scoutResults->count();
            }

            // Radios - Meilisearch
            if ($type === 'all' || $type === 'radios') {
                $scoutResults = Radio::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['radios'] = $scoutResults->map(function ($radio) use ($activeLangs, $currentLocale) {
                    $radioData = ['radio_id' => $radio->radio_id];

                    foreach ($activeLangs as $lang) {
                        $radioData["title_{$lang}"] = $radio->getTranslated('title', $lang);
                        $radioData["slug_{$lang}"] = $radio->getTranslated('slug', $lang);
                    }
                    $radioData['title'] = $radio->getTranslated('title', $currentLocale);
                    $radioData['slug'] = $radio->getTranslated('slug', $currentLocale);

                    return $radioData;
                });

                $totalResults += $scoutResults->count();
            }

            // Calculate response time
            $responseTime = (int) round((microtime(true) - $startTime) * 1000);

            // Log search to Search module
            SearchQuery::create([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'query' => $query,
                'searchable_type' => $type,
                'results_count' => $totalResults,
                'response_time_ms' => $responseTime,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'locale' => app()->getLocale(),
                'referrer_url' => $request->header('referer'),
                'is_visible_in_tags' => $totalResults > 0,
                'is_popular' => false,
                'is_hidden' => false,
            ]);

            return response()->json($results);

        } catch (\Exception $e) {
            \Log::error('Search error:', ['query' => $request->input('q'), 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
