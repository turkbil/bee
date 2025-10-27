<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DynamicLivewireUploadLimit
{
    /**
     * Handle an incoming request - Set Livewire upload limit based on user
     */
    public function handle(Request $request, Closure $next)
    {
        // Livewire upload endpoint'inde config'i override et
        if ($request->is('livewire/upload-file')) {
            $maxSize = (auth()->check() && auth()->user()->id === 1) ? (1024 * 1024) : 12288;
            config(['livewire.temporary_file_upload.rules' => ['required', 'file', 'max:' . $maxSize]]);
        }

        return $next($request);
    }
}
