<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Artist;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Radio;
use Modules\Muzibu\App\Models\Sector;

class DashboardComponent extends Component
{
    #[Computed]
    public function totalSongs(): int
    {
        return Song::count();
    }

    #[Computed]
    public function totalAlbums(): int
    {
        return Album::count();
    }

    #[Computed]
    public function totalArtists(): int
    {
        return Artist::count();
    }

    #[Computed]
    public function totalPlaylists(): int
    {
        return Playlist::count();
    }

    #[Computed]
    public function totalGenres(): int
    {
        return Genre::count();
    }

    #[Computed]
    public function totalRadios(): int
    {
        return Radio::count();
    }

    #[Computed]
    public function totalSectors(): int
    {
        return Sector::count();
    }

    #[Computed]
    public function recentSongs()
    {
        return Song::with(['album.artist', 'genre'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function popularSongs()
    {
        return Song::with(['album.artist'])
            ->where('play_count', '>', 0)
            ->orderBy('play_count', 'desc')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function hlsStats(): array
    {
        return [
            'completed' => Song::whereNotNull('hls_path')->count(),
            'pending' => Song::whereNotNull('file_path')
                ->whereNull('hls_path')
                ->count(),
            'failed' => 0, // TODO: Implement failed tracking
        ];
    }

    public function render()
    {
        return view('muzibu::admin.livewire.dashboard-component');
    }
}
