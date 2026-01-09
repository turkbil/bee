<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Radio;

class RadioController extends Controller
{
    public function index()
    {
        // Show all active radios with playlists (alfabetik sıralı)
        $radios = Radio::with('logoMedia')
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

        return view('themes.muzibu.radios.index', compact('radios'));
    }

    public function apiIndex()
    {
        // Only show radios with at least 1 active playlist (that has active songs)
        $radios = Radio::with('logoMedia')
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

        $html = view('themes.muzibu.partials.radios-grid', compact('radios'))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => 'Canlı Radyolar - Muzibu',
                'description' => 'Canlı radyo yayınlarını dinleyin',
            ]
        ]);
    }
}
