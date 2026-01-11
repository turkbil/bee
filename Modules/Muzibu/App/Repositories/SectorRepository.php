<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\Sector;

class SectorRepository
{
    private readonly string $cachePrefix;

    public function __construct(private Sector $model)
    {
        $this->cachePrefix = 'muzibu_sectors';
    }

    public function findById(int $id): ?Sector
    {
        return $this->model->where('sector_id', $id)->first();
    }

    public function findByIdWithRelations(int $id): ?Sector
    {
        return $this->model
            ->where('sector_id', $id)
            ->with(['radios', 'playlists'])
            ->first();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('sector_id', 'desc')->get();
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('sector_id', 'desc')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $locales = $filters['locales'] ?? ['tr'];
            $searchLower = '%' . mb_strtolower($search) . '%';

            $query->where(function ($q) use ($searchLower, $locales) {
                foreach ($locales as $locale) {
                    $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) LIKE ?", [$searchLower]);
                }
            });
        }

        $sortField = $filters['sortField'] ?? 'sector_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Sector
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $sector = $this->findById($id);
        return $sector ? $sector->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $sector = $this->findById($id);
        return $sector ? $sector->delete() : false;
    }

    public function clearCache(): void
    {
        Cache::tags([$this->cachePrefix, 'muzibu'])->flush();
    }
}
