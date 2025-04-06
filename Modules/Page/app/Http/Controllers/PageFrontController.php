<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Page\App\Models\Page;
use App\Services\ThemeService;

class PageFrontController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function index()
    {
        $pages = Page::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $viewPath = $this->themeService->getThemeViewPath('index', 'page');
        return view($viewPath, compact('pages'));
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $viewPath = $this->themeService->getThemeViewPath('show', 'page');
        return view($viewPath, compact('page'));
    }
}