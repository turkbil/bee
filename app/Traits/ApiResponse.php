<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Success response
     */
    protected function success($data = null, $message = 'Success', $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response
     */
    protected function error($message = 'Error', $code = 400, $data = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation error response
     */
    protected function validationError($errors, $message = 'Validation errors'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorized($message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }

    /**
     * Forbidden response
     */
    protected function forbidden($message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }

    /**
     * Not found response
     */
    protected function notFound($message = 'Not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }

    /**
     * Server error response
     */
    protected function serverError($message = 'Server error'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }

    /**
     * Paginated response
     */
    protected function paginated($data, $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
                'has_more_pages' => $data->hasMorePages(),
                'first_page_url' => $data->url(1),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ],
        ]);
    }

    /**
     * Created response
     */
    protected function created($data = null, $message = 'Created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Updated response
     */
    protected function updated($data = null, $message = 'Updated successfully'): JsonResponse
    {
        return $this->success($data, $message, 200);
    }

    /**
     * Deleted response
     */
    protected function deleted($message = 'Deleted successfully'): JsonResponse
    {
        return $this->success(null, $message, 200);
    }

    /**
     * No content response
     */
    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}