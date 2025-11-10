<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\Genre;

class GenreRepository
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;

    public function __construct(private Genre $model)
    {
        $this->cachePrefix = 'muzibu_genres';
        $this->cacheTtl = (int) config('modules.cache.ttl.list', 3600);
    }

    public function findById(int $id): ?Genre
    {
        return $this->model->where('genre_id', $id)->first();
    }

    public function findByIdWithRelations(int $id): ?Genre
    {
        return $this->model
            ->where('genre_id', $id)
            ->first();
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?Genre
    {
        return $this->model->where(function ($query) use ($slug, $locale) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug]);
        })->active()->first();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('genre_id', 'desc')->get();
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('genre_id', 'desc')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_SEARCH(title, 'one', ?) IS NOT NULL", ["%{$search}%"]);
            });
        }

        $sortField = $filters['sortField'] ?? 'genre_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Genre
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $genre = $this->findById($id);
        return $genre ? $genre->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $genre = $this->findById($id);
        return $genre ? $genre->delete() : false;
    }

    public function clearCache(): void
    {
        Cache::tags([$this->cachePrefix, 'muzibu'])->flush();
    }
}
