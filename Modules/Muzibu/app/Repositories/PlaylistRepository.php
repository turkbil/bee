<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\Playlist;

class PlaylistRepository
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;

    public function __construct(private Playlist $model)
    {
        $this->cachePrefix = 'muzibu_playlists';
        $this->cacheTtl = (int) config('modules.cache.ttl.list', 3600);
    }

    public function findById(int $id): ?Playlist
    {
        return $this->model->where('playlist_id', $id)->first();
    }

    public function findByIdWithSongs(int $id): ?Playlist
    {
        return $this->model->with(['songs.album.artist', 'songs.genre', 'songs.media', 'sectors', 'radios'])
            ->where('playlist_id', $id)->first();
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?Playlist
    {
        return $this->model->where(function ($query) use ($slug, $locale) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug]);
        })->active()->public()->with(['songs.album.artist', 'songs.media'])->first();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('playlist_id', 'desc')->get();
    }

    public function getPublic(): Collection
    {
        return $this->model->active()->public()->orderBy('playlist_id', 'desc')->get();
    }

    public function getSystem(): Collection
    {
        return $this->model->active()->system()->orderBy('playlist_id', 'desc')->get();
    }

    public function getUserPlaylists(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->active()->orderBy('playlist_id', 'desc')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', $filters['is_public']);
        }

        if (isset($filters['is_system'])) {
            $query->where('is_system', $filters['is_system']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_SEARCH(title, 'one', ?) IS NOT NULL", ["%{$search}%"]);
            });
        }

        $sortField = $filters['sortField'] ?? 'playlist_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Playlist
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $playlist = $this->findById($id);
        return $playlist ? $playlist->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $playlist = $this->findById($id);
        return $playlist ? $playlist->delete() : false;
    }

    public function addSong(int $playlistId, int $songId, int $position = 0): bool
    {
        $playlist = $this->findById($playlistId);
        if (!$playlist) {
            return false;
        }

        $playlist->songs()->attach($songId, ['position' => $position]);
        return true;
    }

    public function removeSong(int $playlistId, int $songId): bool
    {
        $playlist = $this->findById($playlistId);
        if (!$playlist) {
            return false;
        }

        $playlist->songs()->detach($songId);
        return true;
    }

    public function reorderSongs(int $playlistId, array $songIds): bool
    {
        $playlist = $this->findById($playlistId);
        if (!$playlist) {
            return false;
        }

        foreach ($songIds as $position => $songId) {
            $playlist->songs()->updateExistingPivot($songId, ['position' => $position]);
        }

        return true;
    }

    public function clearCache(): void
    {
        Cache::tags([$this->cachePrefix, 'muzibu'])->flush();
    }
}
