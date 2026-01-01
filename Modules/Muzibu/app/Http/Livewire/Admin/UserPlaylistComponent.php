<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Modules\Muzibu\App\Models\Playlist;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserPlaylistComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url]
    public string $search = '';

    #[Url]
    public string $sortField = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    #[Url]
    public ?string $filterUser = null;

    #[Url]
    public int $perPage = 15;

    // Kullanıcı arama için
    public string $userSearch = '';
    public bool $showUserDropdown = false;

    public array $selectedItems = [];
    public bool $selectAll = false;

    protected $listeners = [
        'refreshPageData' => '$refresh',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterUser(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterUser = null;
        $this->userSearch = '';
        $this->showUserDropdown = false;
        $this->resetPage();
    }

    #[Computed]
    public function playlists()
    {
        return Playlist::with(['user', 'songs'])
            ->withCount('songs')
            ->where('is_system', false)
            ->whereNotNull('user_id')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->filterUser, fn($q) => $q->where('user_id', $this->filterUser))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function stats(): array
    {
        $base = Playlist::where('is_system', false)->whereNotNull('user_id');

        return [
            'total_playlists' => (clone $base)->count(),
            'total_users' => (clone $base)->distinct('user_id')->count('user_id'),
            'public_playlists' => (clone $base)->where('is_public', true)->count(),
            'private_playlists' => (clone $base)->where('is_public', false)->count(),
        ];
    }

    #[Computed]
    public function searchedUsers()
    {
        // Sadece arama yapıldığında sonuç döndür (minimum 2 karakter)
        if (strlen($this->userSearch) < 2) {
            return collect();
        }

        return User::whereHas('playlists', function($q) {
                $q->where('is_system', false);
            })
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->userSearch . '%')
                  ->orWhere('email', 'like', '%' . $this->userSearch . '%');
            })
            ->withCount(['playlists' => function($q) {
                $q->where('is_system', false);
            }])
            ->orderByDesc('playlists_count')
            ->limit(10) // Maksimum 10 sonuç
            ->get();
    }

    #[Computed]
    public function selectedUserName(): ?string
    {
        if (!$this->filterUser) {
            return null;
        }

        return User::find($this->filterUser)?->name;
    }

    public function selectUser(int $userId): void
    {
        $this->filterUser = (string) $userId;
        $this->userSearch = '';
        $this->showUserDropdown = false;
        $this->resetPage();
    }

    public function updatedUserSearch(): void
    {
        $this->showUserDropdown = strlen($this->userSearch) >= 2;
    }

    public function toggleActive(int $id): void
    {
        $playlist = Playlist::find($id);
        if ($playlist && !$playlist->is_system) {
            $playlist->is_active = !$playlist->is_active;
            $playlist->save();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $playlist->is_active ? 'Playlist aktifleştirildi' : 'Playlist pasifleştirildi',
                'type' => 'success',
            ]);
        }
    }

    public function togglePublic(int $id): void
    {
        $playlist = Playlist::find($id);
        if ($playlist && !$playlist->is_system) {
            $playlist->is_public = !$playlist->is_public;
            $playlist->save();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $playlist->is_public ? 'Playlist herkese açık yapıldı' : 'Playlist gizli yapıldı',
                'type' => 'success',
            ]);
        }
    }

    public function deletePlaylist(int $id): void
    {
        $playlist = Playlist::find($id);
        if ($playlist && !$playlist->is_system) {
            $playlist->delete();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'Playlist silindi',
                'type' => 'success',
            ]);
        }
    }

    public function render()
    {
        return view('muzibu::admin.livewire.user-playlist-component');
    }
}
