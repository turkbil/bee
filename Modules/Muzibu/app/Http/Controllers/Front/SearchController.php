<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\{Song, Album, Playlist, Artist};

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->input('q');
            $type = $request->input('type', 'all');

            if (!$query || strlen($query) < 2) {
                return response()->json(['error' => 'Query too short'], 400);
            }

            //🔒 FIXED: Use Eloquent (tenant-aware)
            $results = [];

            if ($type === 'all' || $type === 'songs') {
                $results['songs'] = Song::where('is_active', 1)
                    ->where(function ($q) use ($query) {
                        $q->whereRaw("JSON_EXTRACT(title, '$.tr') LIKE ?", ["%{$query}%"])
                          ->orWhereRaw("JSON_EXTRACT(title, '$.en') LIKE ?", ["%{$query}%"]);
                    })
                    ->with('album.artist')
                    ->limit(20)
                    ->get()
                    ->map(function ($song) {
                        return [
                            'song_id' => $song->song_id,
                            'title' => $song->title,
                            'slug' => $song->slug,
                            'duration' => $song->duration,
                            'file_path' => $song->file_path,
                            'hls_path' => $song->hls_path,
                            'hls_converted' => $song->hls_converted,
                            'album' => ['title' => $song->album?->title],
                            'artist' => ['title' => $song->album?->artist?->title],
                        ];
                    });
            }

            if ($type === 'all' || $type === 'albums') {
                $results['albums'] = Album::where('is_active', 1)
                    ->where(function ($q) use ($query) {
                        $q->whereRaw("JSON_EXTRACT(title, '$.tr') LIKE ?", ["%{$query}%"])
                          ->orWhereRaw("JSON_EXTRACT(title, '$.en') LIKE ?", ["%{$query}%"]);
                    })
                    ->with('artist')
                    ->limit(10)
                    ->get();
            }

            if ($type === 'all' || $type === 'playlists') {
                $results['playlists'] = Playlist::where('is_active', 1)
                    ->where(function ($q) use ($query) {
                        $q->whereRaw("JSON_EXTRACT(title, '$.tr') LIKE ?", ["%{$query}%"])
                          ->orWhereRaw("JSON_EXTRACT(title, '$.en') LIKE ?", ["%{$query}%"]);
                    })
                    ->limit(10)
                    ->get();
            }

            if ($type === 'all' || $type === 'artists') {
                $results['artists'] = Artist::where('is_active', 1)
                    ->where(function ($q) use ($query) {
                        $q->whereRaw("JSON_EXTRACT(title, '$.tr') LIKE ?", ["%{$query}%"])
                          ->orWhereRaw("JSON_EXTRACT(title, '$.en') LIKE ?", ["%{$query}%"]);
                    })
                    ->limit(10)
                    ->get();
            }

            return response()->json($results);

        } catch (\Exception $e) {
            \Log::error('Search error:', ['query' => $request->input('q'), 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
