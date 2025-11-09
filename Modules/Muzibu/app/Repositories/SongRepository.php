<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\Song;

class SongRepository
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;

    public function __construct(private Song $model)
    {
        $this->cachePrefix = 'muzibu_songs';
        $this->cacheTtl = (int) config('modules.cache.ttl.list', 3600);
    }

    public function findById(int $id): ?Song
    {
        return $this->model->where('song_id', $id)->first();
    }

    public function findByIdWithRelations(int $id): ?Song
    {
        return $this->model->with(['album.artist', 'genre', 'media', 'playlists'])->where('song_id', $id)->first();
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?Song
    {
        return $this->model->where(function ($query) use ($slug, $locale) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug]);
        })->active()->with(['album.artist', 'genre', 'media'])->first();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->with(['album.artist', 'genre'])->orderBy('song_id', 'desc')->get();
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return $this->model->featured()->active()->with(['album.artist', 'genre', 'media'])->limit($limit)->get();
    }

    public function getPopular(int $limit = 10): Collection
    {
        return $this->model->active()->popular($limit)->with(['album.artist', 'genre', 'media'])->get();
    }

    public function getByGenre(int $genreId, int $limit = null): Collection
    {
        $query = $this->model->where('genre_id', $genreId)->active()->with(['album.artist', 'media']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->orderBy('song_id', 'desc')->get();
    }

    public function getByAlbum(int $albumId): Collection
    {
        return $this->model->where('album_id', $albumId)->active()->orderBy('song_id')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()->with(['album.artist', 'genre']);

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['genre_id'])) {
            $query->where('genre_id', $filters['genre_id']);
        }

        if (isset($filters['album_id'])) {
            $query->where('album_id', $filters['album_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_SEARCH(title, 'one', ?) IS NOT NULL", ["%{$search}%"])
                  ->orWhereRaw("JSON_SEARCH(lyrics, 'one', ?) IS NOT NULL", ["%{$search}%"]);
            });
        }

        $sortField = $filters['sortField'] ?? 'song_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Song
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $song = $this->findById($id);
        return $song ? $song->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $song = $this->findById($id);
        return $song ? $song->delete() : false;
    }

    public function incrementPlayCount(int $id, ?int $userId = null, ?string $ipAddress = null): void
    {
        $song = $this->findById($id);
        if ($song) {
            $song->incrementPlayCount($userId, $ipAddress);
        }
    }

    public function clearCache(): void
    {
        Cache::tags([$this->cachePrefix, 'muzibu'])->flush();
    }
}
