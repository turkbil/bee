<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugLivewireUpload
{
    public function handle(Request $request, Closure $next)
    {
        // Sadece Livewire upload endpoint'i için çalış
        if ($request->is('livewire/upload-file')) {
            Log::info('🔍 LIVEWIRE UPLOAD DEBUG', [
                'user_id' => auth()->id(),
                'is_root' => auth()->check() && auth()->user()->id === 1,
                'file_size' => $request->hasFile('file') ? $request->file('file')->getSize() : 'no file',
                'file_size_mb' => $request->hasFile('file') ? round($request->file('file')->getSize() / 1024 / 1024, 2) . ' MB' : 'no file',
                'request_method' => $request->method(),
                'content_length' => $request->header('Content-Length'),
                'all_headers' => $request->headers->all(),
            ]);
        }

        try {
            $response = $next($request);

            if ($request->is('livewire/upload-file')) {
                Log::info('✅ LIVEWIRE UPLOAD RESPONSE', [
                    'status' => $response->getStatusCode(),
                    'content' => $response->getContent(),
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            if ($request->is('livewire/upload-file')) {
                Log::error('❌ LIVEWIRE UPLOAD EXCEPTION', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            throw $e;
        }
    }
}
