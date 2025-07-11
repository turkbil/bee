<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\PaginationHelper;
use Illuminate\Http\Request;

class ListsController extends Controller
{
    use ApiResponse, PaginationHelper;

    /**
     * Get users list with pagination and filtering
     */
    public function getUsers(Request $request)
    {
        try {
            $query = User::query();

            $config = [
                'searchFields' => ['name', 'email'],
                'filterableFields' => [
                    'is_active' => 'boolean',
                    'created_at' => 'date_range',
                    'email_verified_at' => 'date',
                ],
                'sortableFields' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                'defaultSort' => 'created_at',
                'defaultDirection' => 'desc',
                'defaultPerPage' => 15,
            ];

            $paginatedData = $this->applyPaginationHelpers($query, $request, $config);

            // Transform data for API response
            $transformedData = $paginatedData->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'phone' => $user->phone,
                    'is_active' => $user->is_active,
                    'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : null,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json($this->formatPaginatedResponse($transformedData, 'Users retrieved successfully'));

        } catch (\Exception $e) {
            return $this->serverError('Failed to retrieve users: ' . $e->getMessage());
        }
    }

    /**
     * Get general search results
     */
    public function search(Request $request)
    {
        try {
            // Basic validation
            if (!$request->has('query') || empty(trim($request->query))) {
                return $this->validationError(['query' => 'Search query is required']);
            }

            $query = trim($request->query);
            $type = $request->get('type', 'all'); // users, content, etc.
            $results = [];

            // Search users
            if ($type === 'all' || $type === 'users') {
                $users = User::where('name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%')
                    ->where('is_active', true)
                    ->limit(5)
                    ->get(['id', 'name', 'email', 'avatar']);

                $results['users'] = $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'type' => 'user',
                    ];
                });
            }

            // Here you can add more search types when modules are ready
            // if ($type === 'all' || $type === 'pages') {
            //     // Search pages
            // }

            return $this->success($results, 'Search completed successfully');

        } catch (\Exception $e) {
            return $this->serverError('Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Get filter options for forms
     */
    public function getFilterOptions(Request $request)
    {
        try {
            $type = $request->get('type', 'users');
            $options = [];

            switch ($type) {
                case 'users':
                    $options = [
                        'sort_options' => [
                            'id' => 'ID',
                            'name' => 'Name',
                            'email' => 'Email',
                            'created_at' => 'Created Date',
                            'updated_at' => 'Updated Date',
                        ],
                        'filter_options' => [
                            'is_active' => [
                                'type' => 'boolean',
                                'label' => 'Active Status',
                                'options' => [
                                    '1' => 'Active',
                                    '0' => 'Inactive',
                                ]
                            ],
                            'created_at' => [
                                'type' => 'date_range',
                                'label' => 'Created Date Range',
                            ],
                            'email_verified_at' => [
                                'type' => 'date',
                                'label' => 'Email Verified Date',
                            ],
                        ],
                        'per_page_options' => [10, 15, 25, 50, 100],
                    ];
                    break;

                default:
                    return $this->error('Invalid filter type');
            }

            return $this->success($options, 'Filter options retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to retrieve filter options: ' . $e->getMessage());
        }
    }

    /**
     * Get stats for dashboard
     */
    public function getStats(Request $request)
    {
        try {
            $stats = [
                'users' => [
                    'total' => User::count(),
                    'active' => User::where('is_active', true)->count(),
                    'inactive' => User::where('is_active', false)->count(),
                    'verified' => User::whereNotNull('email_verified_at')->count(),
                    'recent' => User::where('created_at', '>=', now()->subDays(7))->count(),
                ],
                'tenant' => [
                    'name' => tenant()?->name ?? 'No tenant',
                    'is_active' => tenant()?->is_active ?? false,
                    'plan' => tenant()?->plan ?? 'unknown',
                ],
            ];

            return $this->success($stats, 'Stats retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to retrieve stats: ' . $e->getMessage());
        }
    }
}