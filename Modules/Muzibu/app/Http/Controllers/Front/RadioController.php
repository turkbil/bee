<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Radio;

class RadioController extends Controller
{
    public function index()
    {
        $radios = Radio::with('logoMedia')
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(200);

        return view('themes.muzibu.radios.index', compact('radios'));
    }

    public function apiIndex()
    {
        $radios = Radio::with('logoMedia')
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
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
