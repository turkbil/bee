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
        // Controller'dan share edilen metaTags varsa onu kullan, yoksa generate et
        $sharedData = view()->getShared();

        if (isset($sharedData['metaTags']) && is_array($sharedData['metaTags'])) {
            // Controller'dan share edilen metaTags'i kullan (Shop gibi özel modüller için)
            $this->metaTags = $sharedData['metaTags'];
        } else {
            // Normal flow: SEO servisinden generate et
            $this->metaTags = $this->seoService->generateMetaTags();
        }
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