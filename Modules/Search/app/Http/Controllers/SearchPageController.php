<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class SearchPageController extends Controller
{
    /**
     * Display search results page
     */
    public function show(Request $request, ?string $query = null)
    {
        // Get query from URL parameter or route parameter
        $searchQuery = $query ?? $request->get('q', '');

        // Decode URL-encoded query
        $searchQuery = urldecode($searchQuery);

        // Convert slug format to normal text (optional)
        // forklift-yedek-parca -> forklift yedek parça
        if ($query) {
            $searchQuery = str_replace('-', ' ', $searchQuery);
        }

        return view('search::show', [
            'query' => $searchQuery,
            'pageTitle' => $searchQuery
                ? "'{$searchQuery}' - Arama Sonuçları"
                : 'Arama',
        ]);
    }

    /**
     * Display popular searches page (for SEO)
     */
    public function tags()
    {
        return view('search::tags', [
            'pageTitle' => 'Popüler Aramalar',
        ]);
    }
}
