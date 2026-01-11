<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\Album;

class AlbumRepository
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;

    public function __construct(private Album $model)
    {
        $this->cachePrefix = 'muzibu_albums';
        $this->cacheTtl = (int) config('modules.cache.ttl.list', 3600);
    }

    public function findById(int $id): ?Album
    {
        return $this->model->where('album_id', $id)->first();
    }

    public function findByIdWithRelations(int $id): ?Album
    {
        return $this->model->with(['artist', 'songs', 'media'])->where('album_id', $id)->first();
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?Album
    {
        return $this->model->where(function ($query) use ($slug, $locale) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug]);
        })->active()->with(['artist', 'songs'])->first();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->with('artist')->orderBy('album_id', 'desc')->get();
    }

    public function getByArtist(int $artistId): Collection
    {
        return $this->model->where('artist_id', $artistId)->active()->orderBy('album_id', 'desc')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()->with(['artist', 'songs'])->withCount('songs');

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['artist_id'])) {
            $query->where('artist_id', $filters['artist_id']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $locales = $filters['locales'] ?? ['tr'];
            $searchLower = '%' . mb_strtolower($search) . '%';

            $query->where(function ($q) use ($searchLower, $locales) {
                // Title search
                foreach ($locales as $locale) {
                    $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) LIKE ?", [$searchLower]);
                }
                // Artist search
                $q->orWhereHas('artist', function ($artistQuery) use ($searchLower, $locales) {
                    $artistQuery->where(function ($aq) use ($searchLower, $locales) {
                        foreach ($locales as $locale) {
                            $aq->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) LIKE ?", [$searchLower]);
                        }
                    });
                });
            });
        }

        $sortField = $filters['sortField'] ?? 'album_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Album
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $album = $this->findById($id);
        return $album ? $album->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $album = $this->findById($id);
        return $album ? $album->delete() : false;
    }

    public function clearCache(): void
    {
        Cache::tags([$this->cachePrefix, 'muzibu'])->flush();
    }
}
