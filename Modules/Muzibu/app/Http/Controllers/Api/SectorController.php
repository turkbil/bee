<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SectorController extends Controller
{
    /**
     * Get all sectors
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $sectors = DB::table('muzibu_sectors')
            ->where('is_active', 1)
            ->select(['sector_id', 'title', 'slug'])
            ->get();

        $sectors = $sectors->map(function ($sector) {
            $sector->title = json_decode($sector->title, true);
            $sector->slug = json_decode($sector->slug, true);
            return $sector;
        });

        return response()->json($sectors);
    }

    /**
     * Get playlists by sector
     *
     * @param int $id
     * @return JsonResponse
     */
    public function playlists(int $id): JsonResponse
    {
        $playlists = DB::table('muzibu_playlist_sector')
            ->join('muzibu_playlists', 'muzibu_playlist_sector.playlist_id', '=', 'muzibu_playlists.playlist_id')
            ->where('muzibu_playlist_sector.sector_id', $id)
            ->where('muzibu_playlists.is_active', 1)
            ->select([
                'muzibu_playlists.playlist_id',
                'muzibu_playlists.title',
                'muzibu_playlists.slug',
                'muzibu_playlists.description',
                'muzibu_playlists.media_id'
            ])
            ->get();

        $playlists = $playlists->map(function ($playlist) {
            $playlist->title = json_decode($playlist->title, true);
            $playlist->slug = json_decode($playlist->slug, true);
            $playlist->description = json_decode($playlist->description, true);

            $songCount = DB::table('muzibu_playlist_song')
                ->where('playlist_id', $playlist->playlist_id)
                ->count();

            $playlist->song_count = $songCount;

            return $playlist;
        });

        return response()->json($playlists);
    }
}
