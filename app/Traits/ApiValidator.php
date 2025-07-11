<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ApiValidator
{
    /**
     * Validate request data
     */
    protected function validateRequest(Request $request, array $rules, array $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->toArray(),
            ];
        }

        return [
            'success' => true,
            'data' => $validator->validated(),
        ];
    }

    /**
     * Common validation rules
     */
    protected function getPaginationRules()
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'sort_by' => 'sometimes|string|max:50',
            'sort_direction' => 'sometimes|in:asc,desc',
        ];
    }

    /**
     * Get search validation rules
     */
    protected function getSearchRules()
    {
        return [
            'query' => 'required|string|min:2|max:255',
            'type' => 'sometimes|string|in:all,users,pages,content',
        ];
    }

    /**
     * Get date range validation rules
     */
    protected function getDateRangeRules()
    {
        return [
            'from' => 'sometimes|date',
            'to' => 'sometimes|date|after_or_equal:from',
        ];
    }

    /**
     * Validate pagination parameters
     */
    protected function validatePagination(Request $request)
    {
        return $this->validateRequest($request, $this->getPaginationRules());
    }

    /**
     * Validate search parameters
     */
    protected function validateSearch(Request $request)
    {
        return $this->validateRequest($request, $this->getSearchRules());
    }

    /**
     * Validate file upload
     */
    protected function validateFileUpload(Request $request, $field = 'file', $maxSize = 5120, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'])
    {
        $rules = [
            $field => 'required|file|max:' . $maxSize . '|mimes:' . implode(',', $allowedTypes),
        ];

        return $this->validateRequest($request, $rules);
    }

    /**
     * Validate ID parameter
     */
    protected function validateId($id, $model = null)
    {
        if (!is_numeric($id) || $id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid ID provided',
            ];
        }

        if ($model && !$model::find($id)) {
            return [
                'success' => false,
                'message' => 'Record not found',
            ];
        }

        return [
            'success' => true,
            'id' => (int) $id,
        ];
    }

    /**
     * Validate email format
     */
    protected function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format',
            ];
        }

        return [
            'success' => true,
            'email' => $email,
        ];
    }

    /**
     * Validate phone number
     */
    protected function validatePhone($phone)
    {
        if (!preg_match('/^[\d\s\-\+\(\)]+$/', $phone)) {
            return [
                'success' => false,
                'message' => 'Invalid phone format',
            ];
        }

        return [
            'success' => true,
            'phone' => $phone,
        ];
    }

    /**
     * Validate password strength
     */
    protected function validatePassword($password)
    {
        if (strlen($password) < 8) {
            return [
                'success' => false,
                'message' => 'Password must be at least 8 characters long',
            ];
        }

        // Add more password strength checks here if needed
        // if (!preg_match('/[A-Z]/', $password)) {
        //     return [
        //         'success' => false,
        //         'message' => 'Password must contain at least one uppercase letter',
        //     ];
        // }

        return [
            'success' => true,
            'password' => $password,
        ];
    }
}