<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugUploadController extends Controller
{
    /**
     * Debug upload form
     */
    public function index()
    {
        return view('debug.upload-test');
    }

    /**
     * Debug upload handler - Full logging
     */
    public function upload(Request $request)
    {
        Log::info('🔍 DEBUG UPLOAD START', [
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'is_root' => auth()->check() && auth()->user()->id === 1,
            'method' => $request->method(),
            'url' => $request->url(),
            'has_file' => $request->hasFile('file'),
        ]);

        try {
            $file = $request->file('file');

            if (!$file) {
                Log::warning('❌ No file uploaded');
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }

            Log::info('📁 File received', [
                'original_name' => $file->getClientOriginalName(),
                'size_bytes' => $file->getSize(),
                'size_mb' => round($file->getSize() / 1024 / 1024, 2),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'is_valid' => $file->isValid(),
                'error' => $file->getError(),
            ]);

            // Check if root user
            $isRoot = auth()->check() && auth()->user()->id === 1;
            Log::info('👤 User check', [
                'is_authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'is_root' => $isRoot,
            ]);

            // Validation based on user
            if (!$isRoot) {
                Log::info('⚠️ Non-root user - applying validation');

                try {
                    $request->validate([
                        'file' => 'required|file|max:20480', // 20MB
                    ]);
                    Log::info('✅ Validation passed for non-root user');
                } catch (\Illuminate\Validation\ValidationException $e) {
                    Log::error('❌ Validation failed', [
                        'errors' => $e->errors(),
                    ]);
                    throw $e;
                }
            } else {
                Log::info('✅ Root user - SKIPPING validation');
            }

            // Try to store file
            Log::info('💾 Attempting to store file...');
            $tempPath = $file->store('debug-uploads', 'public');

            Log::info('✅ File stored successfully', [
                'temp_path' => $tempPath,
                'full_path' => storage_path('app/public/' . $tempPath),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully!',
                'data' => [
                    'original_name' => $file->getClientOriginalName(),
                    'size_mb' => round($file->getSize() / 1024 / 1024, 2),
                    'temp_path' => $tempPath,
                    'user_id' => auth()->id(),
                    'is_root' => $isRoot,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Upload failed with exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
                'error' => get_class($e),
            ], 500);
        }
    }

    /**
     * Clear logs
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }

        return response()->json(['success' => true, 'message' => 'Logs cleared']);
    }

    /**
     * Get recent logs
     */
    public function getLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return response()->json(['logs' => 'No log file found']);
        }

        $logs = shell_exec("tail -100 {$logFile} | grep -E 'DEBUG UPLOAD|File received|User check|Validation|Upload failed' | tail -50");

        return response()->json(['logs' => $logs ?: 'No recent upload logs']);
    }
}
