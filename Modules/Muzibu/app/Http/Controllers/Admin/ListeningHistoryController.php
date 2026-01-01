<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ListeningHistoryController extends Controller
{
    public function index(): View
    {
        return view('muzibu::admin.listening-history-index', [
            'title' => 'Dinleme Geçmişi',
        ]);
    }
}
