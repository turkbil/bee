<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DynamicLivewireUploadLimit
{
    /**
     * Handle an incoming request - Set Livewire upload limit based on user
     * MUST run BEFORE the request reaches FileUploadController
     */
    public function handle(Request $request, Closure $next)
    {
        // Livewire upload endpoint için config override (GENIŞ KONTROL)
        if ($request->is('livewire/upload-file') || $request->is('livewire/*upload*')) {
            $isRoot = auth()->check() && auth()->user()->id === 1;
            $maxSize = $isRoot ? (1024 * 1024) : 12288; // 1GB vs 12MB

            config(['livewire.temporary_file_upload.rules' => ['required', 'file', 'max:' . $maxSize]]);

            Log::info('🔧 DynamicLivewireUploadLimit ACTIVE', [
                'user_id' => auth()->id(),
                'is_root' => $isRoot,
                'max_size_kb' => $maxSize,
                'max_size_mb' => round($maxSize / 1024, 2),
                'path' => $request->path(),
            ]);
        }

        return $next($request);
    }
}
