<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = DB::table('muzibu_sectors')
            ->where('is_active', 1)
            ->get();

        $sectors = $sectors->map(function ($sector) {
            $sector->title = json_decode($sector->title, true);
            $sector->slug = json_decode($sector->slug, true);

            $sector->playlist_count = DB::table('muzibu_playlist_sector')
                ->where('sector_id', $sector->sector_id)
                ->count();

            return $sector;
        });

        return view('muzibu::themes.muzibu.sectors.index', compact('sectors'));
    }

    public function show($id)
    {
        $sector = DB::table('muzibu_sectors')
            ->where('sector_id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$sector) {
            abort(404);
        }

        $sector->title = json_decode($sector->title, true);
        $sector->slug = json_decode($sector->slug, true);

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

            $playlist->song_count = DB::table('muzibu_playlist_song')
                ->where('playlist_id', $playlist->playlist_id)
                ->count();

            return $playlist;
        });

        return view('muzibu::themes.muzibu.sectors.show', compact('sector', 'playlists'));
    }
}
