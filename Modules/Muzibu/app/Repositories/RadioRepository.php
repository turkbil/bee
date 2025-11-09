<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\Radio;

class RadioRepository
{
    private readonly string $cachePrefix;

    public function __construct(private Radio $model)
    {
        $this->cachePrefix = 'muzibu_radios';
    }

    public function findById(int $id): ?Radio
    {
        return $this->model->where('radio_id', $id)->first();
    }

    public function findByIdWithRelations(int $id): ?Radio
    {
        return $this->model->with(['sectors', 'playlists'])->where('radio_id', $id)->first();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->with('sectors')->orderBy('radio_id', 'desc')->get();
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('radio_id', 'desc')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()->with('sectors');

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_SEARCH(title, 'one', ?) IS NOT NULL", ["%{$search}%"]);
            });
        }

        $sortField = $filters['sortField'] ?? 'radio_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Radio
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $radio = $this->findById($id);
        return $radio ? $radio->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $radio = $this->findById($id);
        return $radio ? $radio->delete() : false;
    }

    public function clearCache(): void
    {
        Cache::tags([$this->cachePrefix, 'muzibu'])->flush();
    }
}
