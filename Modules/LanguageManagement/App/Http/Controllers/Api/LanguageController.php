<?php

declare(strict_types=1);

namespace Modules\LanguageManagement\App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\LanguageManagement\App\Models\TenantLanguage;

class LanguageController extends Controller
{
    /**
     * Get visible languages
     */
    public function visibleLanguages(): JsonResponse
    {
        $languages = TenantLanguage::where('is_active', true)
            ->where('is_visible', true)
            ->select('language_code', 'name', 'native_name', 'is_default')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($lang) {
                return [
                    'language_code' => $lang->language_code,
                    'name' => $lang->native_name ?: $lang->name,
                    'is_default' => $lang->is_default
                ];
            });

        return response()->json([
            'success' => true,
            'languages' => $languages
        ]);
    }
}