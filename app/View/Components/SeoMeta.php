<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\SeoMetaTagService;

class SeoMeta extends Component
{
    private array $metaTags;
    
    /**
     * Create a new component instance.
     */
    public function __construct(
        private readonly SeoMetaTagService $seoService
    ) {
        \Log::info('🔧 SeoMeta Component constructor called', [
            'url' => request()->fullUrl(),
            'path' => request()->path()
        ]);
        
        $this->metaTags = $this->seoService->generateMetaTags();
        
        \Log::info('🔧 SeoMeta Component meta tags generated', [
            'has_schema' => isset($this->metaTags['schema']) && !empty($this->metaTags['schema']),
            'title' => $this->metaTags['title'] ?? 'null'
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.seo-meta', [
            'metaTags' => $this->metaTags
        ]);
    }
}