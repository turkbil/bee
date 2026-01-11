<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\Artist;

class ArtistRepository
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;

    public function __construct(
        private Artist $model
    ) {
        $this->cachePrefix = 'muzibu_artists';
        $this->cacheTtl = (int) config('modules.cache.ttl.list', 3600);
    }

    public function findById(int $id): ?Artist
    {
        return $this->model->where('artist_id', $id)->first();
    }

    public function findByIdWithRelations(int $id): ?Artist
    {
        return $this->model->with(['albums', 'songs', 'media'])->where('artist_id', $id)->first();
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?Artist
    {
        return $this->model->where(function ($query) use ($slug, $locale) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug]);
        })->active()->first();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('artist_id', 'desc')->get();
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('artist_id', 'desc')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->withCount('albums')
            ->withCount(['albums as songs_count' => function ($query) {
                $query->selectRaw('COALESCE(SUM((SELECT COUNT(*) FROM muzibu_songs WHERE muzibu_songs.album_id = muzibu_albums.album_id)), 0)');
            }]);

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
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
                // Bio search
                foreach ($locales as $locale) {
                    $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(bio, '$.{$locale}'))) LIKE ?", [$searchLower]);
                }
            });
        }

        $sortField = $filters['sortField'] ?? 'artist_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Artist
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $artist = $this->findById($id);
        return $artist ? $artist->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $artist = $this->findById($id);
        return $artist ? $artist->delete() : false;
    }

    public function restore(int $id): bool
    {
        return $this->model->withTrashed()->where('artist_id', $id)->restore();
    }

    public function forceDelete(int $id): bool
    {
        $artist = $this->model->withTrashed()->where('artist_id', $id)->first();
        return $artist ? $artist->forceDelete() : false;
    }

    private function getCacheKey(string $key): string
    {
        return "{$this->cachePrefix}.{$key}";
    }

    private function getCacheTags(): array
    {
        return [$this->cachePrefix, 'muzibu'];
    }

    public function clearCache(): void
    {
        Cache::tags($this->getCacheTags())->flush();
    }
}
