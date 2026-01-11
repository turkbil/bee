<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class AICoverController extends Controller
{
    /**
     * AI Görsel Üretim Sayfası
     */
    public function index()
    {
        return view('muzibu::admin.ai-cover-index');
    }
}
