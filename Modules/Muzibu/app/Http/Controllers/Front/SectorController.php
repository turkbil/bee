<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Sector;

class SectorController extends Controller
{
    public function index()
    {
        // Only show sectors with at least 1 active playlist (that has active songs) - alfabetik sıralı
        $sectors = Sector::with('iconMedia')
            ->where('is_active', 1)
            ->whereHas('playlists', function($q) {
                $q->where('is_active', 1)
                  ->whereHas('songs', function($sq) {
                      $sq->where('is_active', 1);
                  });
            })
            ->withCount(['playlists' => function($q) {
                $q->where('is_active', 1)
                  ->whereHas('songs', function($sq) {
                      $sq->where('is_active', 1);
                  });
            }])
            ->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr")))')
            ->paginate(200);

        return view('themes.muzibu.sectors.index', compact('sectors'));
    }

    public function show($slug)
    {
        $sector = Sector::with('iconMedia')
            ->where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        // Sektöre ait aktif radyolar (üstte gösterilecek) - alfabetik sıralı
        $radios = $sector->radios()
            ->with('logoMedia')
            ->where('muzibu_radios.is_active', 1)
            ->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(muzibu_radios.title, "$.tr")))')
            ->get();

        // Only show playlists with active songs (altta gösterilecek) - son eklenenler önce
        $playlists = $sector->playlists()
            ->with('coverMedia')
            ->where('muzibu_playlists.is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->withSum(['songs' => function($q) {
                $q->where('is_active', 1);
            }], 'duration')
            ->orderBy('muzibu_playlists.created_at', 'desc')
            ->get();

        return view('themes.muzibu.sectors.show', compact('sector', 'radios', 'playlists'));
    }

    public function apiIndex()
    {
        // Only show sectors with at least 1 active playlist (that has active songs)
        $sectors = Sector::with('iconMedia')
            ->where('is_active', 1)
            ->whereHas('playlists', function($q) {
                $q->where('is_active', 1)
                  ->whereHas('songs', function($sq) {
                      $sq->where('is_active', 1);
                  });
            })
            ->withCount(['playlists' => function($q) {
                $q->where('is_active', 1)
                  ->whereHas('songs', function($sq) {
                      $sq->where('is_active', 1);
                  });
            }])
            ->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr")))')
            ->paginate(200);
        $html = view('themes.muzibu.partials.sectors-grid', compact('sectors'))->render();
        return response()->json(['html' => $html, 'meta' => ['title' => 'Sektörler - Muzibu', 'description' => 'Tüm sektörleri keşfedin']]);
    }

    public function apiShow($slug)
    {
        $sector = Sector::with('iconMedia')->where(function($q) use ($slug) { $q->where('slug->tr', $slug)->orWhere('slug->en', $slug); })->where('is_active', 1)->firstOrFail();

        // Sektöre ait aktif radyolar (üstte gösterilecek) - alfabetik sıralı
        $radios = $sector->radios()
            ->with('logoMedia')
            ->where('muzibu_radios.is_active', 1)
            ->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(muzibu_radios.title, "$.tr")))')
            ->get();

        // Only show playlists with active songs (altta gösterilecek) - son eklenenler önce
        $playlists = $sector->playlists()
            ->with('coverMedia')
            ->where('muzibu_playlists.is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->withSum(['songs' => function($q) {
                $q->where('is_active', 1);
            }], 'duration')
            ->orderBy('muzibu_playlists.created_at', 'desc')
            ->get();
        $html = view('themes.muzibu.partials.sector-detail', compact('sector', 'radios', 'playlists'))->render();
        $titleJson = @json_decode($sector->title);
        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $sector->title;
        return response()->json(['html' => $html, 'meta' => ['title' => $title . ' - Muzibu', 'description' => 'Sektör detaylarını inceleyin']]);
    }
}
