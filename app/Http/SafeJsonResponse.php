<?php

namespace App\Http;

use Illuminate\Http\JsonResponse as BaseJsonResponse;

/**
 * Safe JSON Response - UTF-8 Sanitizing JSON Response
 *
 * Extends Laravel's JsonResponse to automatically sanitize
 * invalid UTF-8 characters before JSON encoding
 */
class SafeJsonResponse extends BaseJsonResponse
{
    /**
     * {@inheritdoc}
     */
    public function setData($data = []): static
    {
        // Sanitize data before encoding
        $sanitized = $this->sanitizeForJson($data);

        try {
            // Try normal encoding first
            return parent::setData($sanitized);
        } catch (\InvalidArgumentException $e) {
            // If still fails, do aggressive cleanup
            if (str_contains($e->getMessage(), 'UTF-8')) {
                \Log::warning('SafeJsonResponse: Aggressive UTF-8 cleanup required', [
                    'original_error' => $e->getMessage(),
                    'data_keys' => is_array($data) ? array_keys($data) : 'not-array',
                ]);

                // More aggressive sanitization
                $aggressiveSanitized = $this->aggressiveSanitize($sanitized);
                return parent::setData($aggressiveSanitized);
            }

            throw $e;
        }
    }

    /**
     * Recursively sanitize data for JSON encoding
     */
    private function sanitizeForJson($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                // Sanitize both key and value
                $cleanKey = is_string($key) ? $this->cleanUtf8($key) : $key;
                $result[$cleanKey] = $this->sanitizeForJson($value);
            }
            return $result;
        }

        if (is_object($data)) {
            // For objects, try to sanitize public properties
            if (method_exists($data, 'toArray')) {
                return $this->sanitizeForJson($data->toArray());
            }
            if ($data instanceof \JsonSerializable) {
                return $this->sanitizeForJson($data->jsonSerialize());
            }
            // Return as-is for other objects
            return $data;
        }

        if (is_string($data)) {
            return $this->cleanUtf8($data);
        }

        return $data;
    }

    /**
     * Clean UTF-8 string
     */
    private function cleanUtf8(string $string): string
    {
        // Check if valid UTF-8
        if (mb_check_encoding($string, 'UTF-8')) {
            return $string;
        }

        // Try to convert
        $converted = mb_convert_encoding($string, 'UTF-8', 'UTF-8');

        // Double check
        if (!mb_check_encoding($converted, 'UTF-8')) {
            // Last resort: remove all non-UTF-8 characters
            $converted = iconv('UTF-8', 'UTF-8//IGNORE', $string);
        }

        return $converted ?: '';
    }

    /**
     * Aggressive sanitization as last resort
     */
    private function aggressiveSanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'aggressiveSanitize'], $data);
        }

        if (is_string($data)) {
            // Remove all invalid UTF-8 sequences
            $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $data);
            if ($clean === false) {
                // Even iconv failed, use regex
                return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $data);
            }
            return $clean;
        }

        return $data;
    }
}
