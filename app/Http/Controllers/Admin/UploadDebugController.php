<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class UploadDebugController extends Controller
{
    /**
     * Debug sayfasını göster
     */
    public function index()
    {
        $this->log('DEBUG_PAGE_LOADED', 'Debug sayfası açıldı');

        return view('admin.debug.upload-debug');
    }

    /**
     * Method 1: Standard FormData Upload (No Livewire)
     */
    public function uploadFormData(Request $request)
    {
        $this->log('UPLOAD_START', 'FormData upload başladı', [
            'method' => 'POST',
            'has_file' => $request->hasFile('file'),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
        ]);

        try {
            $this->log('VALIDATION_START', 'Dosya validasyonu başlıyor');

            $request->validate([
                'file' => 'required|file|max:20480', // 20MB
            ]);

            $this->log('VALIDATION_SUCCESS', 'Validasyon başarılı');

            $file = $request->file('file');

            $this->log('FILE_INFO', 'Dosya bilgileri', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension(),
            ]);

            // Tenant storage'a kaydet
            $this->log('STORAGE_START', 'Storage kayıt işlemi başlıyor');

            $path = $file->store('debug-uploads', 'tenant');

            $this->log('STORAGE_SUCCESS', 'Dosya kaydedildi', [
                'path' => $path,
                'full_path' => Storage::disk('tenant')->path($path),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FormData upload başarılı!',
                'path' => $path,
                'url' => Storage::disk('tenant')->url($path),
            ]);

        } catch (\Exception $e) {
            $this->log('UPLOAD_ERROR', 'Upload hatası', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ], 'error');

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Method 2: Base64 Upload
     */
    public function uploadBase64(Request $request)
    {
        $this->log('UPLOAD_BASE64_START', 'Base64 upload başladı', [
            'has_data' => !empty($request->file_data),
            'data_length' => strlen($request->file_data ?? ''),
        ]);

        try {
            $this->log('BASE64_VALIDATION_START', 'Base64 validasyonu');

            $request->validate([
                'file_data' => 'required|string',
                'file_name' => 'required|string',
            ]);

            $this->log('BASE64_DECODE_START', 'Base64 decode ediliyor');

            // Base64 decode
            $fileData = $request->file_data;

            // Data URI scheme varsa temizle (data:image/png;base64,...)
            if (preg_match('/^data:image\/(\w+);base64,/', $fileData, $type)) {
                $fileData = substr($fileData, strpos($fileData, ',') + 1);
                $extension = strtolower($type[1]);

                $this->log('BASE64_DATA_URI_DETECTED', 'Data URI temizlendi', [
                    'extension' => $extension,
                ]);
            } else {
                $extension = pathinfo($request->file_name, PATHINFO_EXTENSION);
            }

            $fileData = base64_decode($fileData);

            $this->log('BASE64_DECODED', 'Base64 decode edildi', [
                'decoded_size' => strlen($fileData),
            ]);

            // Geçici dosya oluştur
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
            file_put_contents($tempPath, $fileData);

            $this->log('TEMP_FILE_CREATED', 'Geçici dosya oluşturuldu', [
                'temp_path' => $tempPath,
                'size' => filesize($tempPath),
            ]);

            // Storage'a kaydet
            $storagePath = 'debug-uploads/' . uniqid() . '_' . $request->file_name;
            Storage::disk('tenant')->put($storagePath, $fileData);

            $this->log('BASE64_STORAGE_SUCCESS', 'Base64 dosya kaydedildi', [
                'path' => $storagePath,
            ]);

            // Geçici dosyayı sil
            @unlink($tempPath);

            return response()->json([
                'success' => true,
                'message' => 'Base64 upload başarılı!',
                'path' => $storagePath,
            ]);

        } catch (\Exception $e) {
            $this->log('BASE64_ERROR', 'Base64 upload hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 'error');

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Method 3: Chunked Upload
     */
    public function uploadChunked(Request $request)
    {
        $chunkIndex = $request->input('chunk_index', 0);
        $totalChunks = $request->input('total_chunks', 1);
        $fileId = $request->input('file_id');

        $this->log('CHUNK_UPLOAD_START', "Chunk upload başladı ({$chunkIndex}/{$totalChunks})", [
            'chunk_index' => $chunkIndex,
            'total_chunks' => $totalChunks,
            'file_id' => $fileId,
            'has_file' => $request->hasFile('chunk'),
        ]);

        try {
            $request->validate([
                'chunk' => 'required|file',
                'chunk_index' => 'required|integer',
                'total_chunks' => 'required|integer',
                'file_id' => 'required|string',
                'file_name' => 'required|string',
            ]);

            $chunk = $request->file('chunk');

            // Chunk'ı geçici klasöre kaydet
            $chunkPath = storage_path("app/chunks/{$fileId}_{$chunkIndex}");
            $chunk->move(dirname($chunkPath), basename($chunkPath));

            $this->log('CHUNK_SAVED', "Chunk kaydedildi ({$chunkIndex}/{$totalChunks})", [
                'chunk_path' => $chunkPath,
                'chunk_size' => filesize($chunkPath),
            ]);

            // Son chunk ise birleştir
            if ($chunkIndex == $totalChunks - 1) {
                $this->log('CHUNK_MERGE_START', 'Tüm chunk\'lar birleştiriliyor');

                $finalPath = storage_path("app/chunks/{$fileId}_final");
                $finalFile = fopen($finalPath, 'wb');

                for ($i = 0; $i < $totalChunks; $i++) {
                    $chunkFile = storage_path("app/chunks/{$fileId}_{$i}");
                    $chunk = fopen($chunkFile, 'rb');
                    stream_copy_to_stream($chunk, $finalFile);
                    fclose($chunk);
                    @unlink($chunkFile);
                }

                fclose($finalFile);

                $this->log('CHUNK_MERGE_SUCCESS', 'Chunk\'lar birleştirildi', [
                    'final_size' => filesize($finalPath),
                ]);

                // Storage'a taşı
                $storagePath = 'debug-uploads/' . uniqid() . '_' . $request->file_name;
                Storage::disk('tenant')->put($storagePath, file_get_contents($finalPath));

                @unlink($finalPath);

                $this->log('CHUNK_UPLOAD_COMPLETE', 'Chunked upload tamamlandı', [
                    'storage_path' => $storagePath,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Chunked upload tamamlandı!',
                    'path' => $storagePath,
                    'complete' => true,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Chunk {$chunkIndex} yüklendi",
                'complete' => false,
            ]);

        } catch (\Exception $e) {
            $this->log('CHUNK_ERROR', 'Chunk upload hatası', [
                'chunk_index' => $chunkIndex,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 'error');

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test: Direct file save to tenant storage
     */
    public function testStorage(Request $request)
    {
        $this->log('STORAGE_TEST_START', 'Storage erişim testi başladı');

        try {
            // Test dosyası oluştur
            $testContent = 'Test content - ' . now()->toDateTimeString();
            $testPath = 'debug-uploads/test-' . uniqid() . '.txt';

            $this->log('STORAGE_WRITE_TEST', 'Test dosyası yazılıyor', [
                'path' => $testPath,
                'content_length' => strlen($testContent),
            ]);

            Storage::disk('tenant')->put($testPath, $testContent);

            $this->log('STORAGE_WRITE_SUCCESS', 'Test dosyası yazıldı');

            // Oku
            $readContent = Storage::disk('tenant')->get($testPath);

            $this->log('STORAGE_READ_SUCCESS', 'Test dosyası okundu', [
                'matches' => $testContent === $readContent,
            ]);

            // Sil
            Storage::disk('tenant')->delete($testPath);

            $this->log('STORAGE_DELETE_SUCCESS', 'Test dosyası silindi');

            return response()->json([
                'success' => true,
                'message' => 'Storage testi başarılı!',
                'disk' => 'tenant',
                'root' => Storage::disk('tenant')->path(''),
            ]);

        } catch (\Exception $e) {
            $this->log('STORAGE_TEST_ERROR', 'Storage test hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 'error');

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get logs
     */
    public function getLogs()
    {
        $logFile = storage_path('logs/upload-debug.log');

        if (!file_exists($logFile)) {
            return response()->json([
                'logs' => [],
                'message' => 'Henüz log yok',
            ]);
        }

        $logs = file_get_contents($logFile);
        $lines = array_filter(explode("\n", $logs));

        // Son 100 satırı al
        $lines = array_slice($lines, -100);

        return response()->json([
            'logs' => $lines,
            'total' => count($lines),
        ]);
    }

    /**
     * Clear logs
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/upload-debug.log');

        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }

        $this->log('LOGS_CLEARED', 'Loglar temizlendi');

        return response()->json([
            'success' => true,
            'message' => 'Loglar temizlendi',
        ]);
    }

    /**
     * Client-side log'ları server'a kaydet
     */
    public function logClientEvent(Request $request)
    {
        $event = $request->input('event');
        $message = $request->input('message');
        $context = $request->input('context', []);

        $this->log(
            '[CLIENT] ' . $event,
            $message,
            array_merge($context, [
                'user_agent' => $request->header('User-Agent'),
                'ip' => $request->ip(),
            ])
        );

        return response()->json(['success' => true]);
    }

    /**
     * Helper: Log yaz
     */
    private function log($event, $message, $context = [], $level = 'info')
    {
        $logData = [
            'timestamp' => now()->format('Y-m-d H:i:s.u'),
            'event' => $event,
            'message' => $message,
            'context' => $context,
            'memory' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'tenant' => tenancy()->initialized ? tenant('id') : 'central',
        ];

        // Custom log dosyasına yaz
        $logFile = storage_path('logs/upload-debug.log');
        $logLine = json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n---\n";
        file_put_contents($logFile, $logLine, FILE_APPEND);

        // Laravel log'a da yaz
        Log::channel('single')->{$level}("[UPLOAD_DEBUG] {$event}: {$message}", $context);
    }
}
