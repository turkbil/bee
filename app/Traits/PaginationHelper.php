<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait PaginationHelper
{
    /**
     * Apply pagination to query
     */
    protected function applyPagination(Builder $query, Request $request, $defaultPerPage = 15)
    {
        $perPage = $request->get('per_page', $defaultPerPage);
        $page = $request->get('page', 1);

        // Limit per_page to prevent abuse
        $perPage = min(max($perPage, 1), 100);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Apply search to query
     */
    protected function applySearch(Builder $query, Request $request, array $searchFields = [])
    {
        $search = $request->get('search');
        
        if (!empty($search) && !empty($searchFields)) {
            $query->where(function ($q) use ($search, $searchFields) {
                foreach ($searchFields as $field) {
                    if (strpos($field, '.') !== false) {
                        // Relation field
                        $parts = explode('.', $field, 2);
                        $relation = $parts[0];
                        $relationField = $parts[1];
                        
                        $q->orWhereHas($relation, function ($relationQuery) use ($relationField, $search) {
                            $relationQuery->where($relationField, 'like', '%' . $search . '%');
                        });
                    } else {
                        // Direct field
                        $q->orWhere($field, 'like', '%' . $search . '%');
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query, Request $request, array $filterableFields = [])
    {
        foreach ($filterableFields as $field => $type) {
            $value = $request->get($field);
            
            if ($value === null || $value === '') {
                continue;
            }

            switch ($type) {
                case 'exact':
                    $query->where($field, $value);
                    break;
                    
                case 'like':
                    $query->where($field, 'like', '%' . $value . '%');
                    break;
                    
                case 'date':
                    $query->whereDate($field, $value);
                    break;
                    
                case 'date_range':
                    if (isset($value['from'])) {
                        $query->whereDate($field, '>=', $value['from']);
                    }
                    if (isset($value['to'])) {
                        $query->whereDate($field, '<=', $value['to']);
                    }
                    break;
                    
                case 'numeric':
                    $query->where($field, $value);
                    break;
                    
                case 'numeric_range':
                    if (isset($value['min'])) {
                        $query->where($field, '>=', $value['min']);
                    }
                    if (isset($value['max'])) {
                        $query->where($field, '<=', $value['max']);
                    }
                    break;
                    
                case 'boolean':
                    $query->where($field, (bool) $value);
                    break;
                    
                case 'in':
                    if (is_array($value)) {
                        $query->whereIn($field, $value);
                    } else {
                        $query->whereIn($field, explode(',', $value));
                    }
                    break;
                    
                case 'relation':
                    if (strpos($field, '.') !== false) {
                        $parts = explode('.', $field, 2);
                        $relation = $parts[0];
                        $relationField = $parts[1];
                        
                        $query->whereHas($relation, function ($relationQuery) use ($relationField, $value) {
                            $relationQuery->where($relationField, $value);
                        });
                    }
                    break;
            }
        }

        return $query;
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, Request $request, array $sortableFields = [], $defaultSort = 'id', $defaultDirection = 'desc')
    {
        $sortBy = $request->get('sort_by', $defaultSort);
        $sortDirection = $request->get('sort_direction', $defaultDirection);

        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = $defaultDirection;
        }

        // Check if field is sortable
        if (!empty($sortableFields) && !in_array($sortBy, $sortableFields)) {
            $sortBy = $defaultSort;
        }

        // Handle relation sorting
        if (strpos($sortBy, '.') !== false) {
            $parts = explode('.', $sortBy, 2);
            $relation = $parts[0];
            $relationField = $parts[1];
            
            $query->join($relation, $relation . '.id', '=', $query->getModel()->getTable() . '.' . $relation . '_id')
                  ->orderBy($relation . '.' . $relationField, $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query;
    }

    /**
     * Apply all pagination helpers
     */
    protected function applyPaginationHelpers(Builder $query, Request $request, array $config = [])
    {
        $config = array_merge([
            'searchFields' => [],
            'filterableFields' => [],
            'sortableFields' => [],
            'defaultSort' => 'id',
            'defaultDirection' => 'desc',
            'defaultPerPage' => 15,
        ], $config);

        // Apply search
        $this->applySearch($query, $request, $config['searchFields']);

        // Apply filters
        $this->applyFilters($query, $request, $config['filterableFields']);

        // Apply sorting
        $this->applySorting($query, $request, $config['sortableFields'], $config['defaultSort'], $config['defaultDirection']);

        // Apply pagination
        return $this->applyPagination($query, $request, $config['defaultPerPage']);
    }

    /**
     * Format paginated response
     */
    protected function formatPaginatedResponse($paginatedData, $message = 'Data retrieved successfully')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $paginatedData->items(),
            'pagination' => [
                'current_page' => $paginatedData->currentPage(),
                'per_page' => $paginatedData->perPage(),
                'total' => $paginatedData->total(),
                'last_page' => $paginatedData->lastPage(),
                'has_more_pages' => $paginatedData->hasMorePages(),
                'from' => $paginatedData->firstItem(),
                'to' => $paginatedData->lastItem(),
                'links' => [
                    'first' => $paginatedData->url(1),
                    'last' => $paginatedData->url($paginatedData->lastPage()),
                    'prev' => $paginatedData->previousPageUrl(),
                    'next' => $paginatedData->nextPageUrl(),
                ],
            ],
            'filters' => [
                'search' => request('search'),
                'sort_by' => request('sort_by'),
                'sort_direction' => request('sort_direction'),
                'per_page' => request('per_page'),
            ],
        ];
    }
}