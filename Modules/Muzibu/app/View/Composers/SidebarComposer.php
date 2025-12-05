<?php

namespace Modules\Muzibu\app\View\Composers;

use Illuminate\View\View;
use Modules\Muzibu\app\Models\Playlist;

class SidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Sidebar için featured playlists'i her zaman sağla
        if (!$view->offsetExists('featuredPlaylists')) {
            $featuredPlaylists = Playlist::where('is_active', 1)
                ->where('is_system', 1)
                ->with(['songs', 'coverMedia'])
                ->limit(10)
                ->get();
            
            $view->with('featuredPlaylists', $featuredPlaylists);
        }
    }
}
