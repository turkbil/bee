<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Sector;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = Sector::where('is_active', 1)
            ->withCount('playlists')
            ->paginate(20);

        return view('themes.muzibu.sectors.index', compact('sectors'));
    }

    public function show($slug)
    {
        $sector = Sector::where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        $playlists = $sector->playlists()
            ->where('muzibu_playlists.is_active', 1)
            ->withCount('songs')
            ->get();

        return view('themes.muzibu.sectors.show', compact('sector', 'playlists'));
    }
}
