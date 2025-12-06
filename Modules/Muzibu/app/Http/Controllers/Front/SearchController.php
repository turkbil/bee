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

            $results = [];
            $totalResults = 0;

            // Songs - Meilisearch
            if ($type === 'all' || $type === 'songs') {
                $scoutResults = Song::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(20)
                    ->get();

                $results['songs'] = $scoutResults->map(function ($song) {
                    return [
                        'song_id' => $song->song_id,
                        'title' => $song->title,
                        'slug' => $song->slug,
                        'duration' => $song->duration,
                        'file_path' => $song->file_path,
                        'hls_path' => $song->hls_path,
                        'album' => ['title' => $song->album?->title],
                        'artist' => ['title' => $song->album?->artist?->title],
                    ];
                });

                $totalResults += $scoutResults->count();
            }

            // Albums - Meilisearch
            if ($type === 'all' || $type === 'albums') {
                $scoutResults = Album::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['albums'] = $scoutResults->map(function ($album) {
                    return [
                        'album_id' => $album->album_id,
                        'title' => $album->title,
                        'slug' => $album->slug,
                        'artist' => ['title' => $album->artist?->title],
                    ];
                });

                $totalResults += $scoutResults->count();
            }

            // Playlists - Meilisearch
            if ($type === 'all' || $type === 'playlists') {
                $scoutResults = Playlist::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1)->where('is_public', '=', 1))
                    ->take(10)
                    ->get();

                $results['playlists'] = $scoutResults->map(function ($playlist) {
                    return [
                        'playlist_id' => $playlist->playlist_id,
                        'title' => $playlist->title,
                        'slug' => $playlist->slug,
                        'is_system' => $playlist->is_system,
                    ];
                });

                $totalResults += $scoutResults->count();
            }

            // Artists - Meilisearch
            if ($type === 'all' || $type === 'artists') {
                $scoutResults = Artist::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['artists'] = $scoutResults->map(function ($artist) {
                    return [
                        'artist_id' => $artist->artist_id,
                        'title' => $artist->title,
                        'slug' => $artist->slug,
                    ];
                });

                $totalResults += $scoutResults->count();
            }

            // Genres - Meilisearch
            if ($type === 'all' || $type === 'genres') {
                $scoutResults = Genre::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['genres'] = $scoutResults->map(function ($genre) {
                    return [
                        'genre_id' => $genre->genre_id,
                        'title' => $genre->title,
                        'slug' => $genre->slug,
                    ];
                });

                $totalResults += $scoutResults->count();
            }

            // Sectors - Meilisearch
            if ($type === 'all' || $type === 'sectors') {
                $scoutResults = Sector::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['sectors'] = $scoutResults->map(function ($sector) {
                    return [
                        'sector_id' => $sector->sector_id,
                        'title' => $sector->title,
                        'slug' => $sector->slug,
                    ];
                });

                $totalResults += $scoutResults->count();
            }

            // Radios - Meilisearch
            if ($type === 'all' || $type === 'radios') {
                $scoutResults = Radio::search($query)
                    ->query(fn ($meilisearch) => $meilisearch->where('is_active', '=', 1))
                    ->take(10)
                    ->get();

                $results['radios'] = $scoutResults->map(function ($radio) {
                    return [
                        'radio_id' => $radio->radio_id,
                        'title' => $radio->title,
                        'slug' => $radio->slug,
                    ];
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
