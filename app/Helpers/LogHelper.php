<?php

use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

if (! function_exists('log_activity')) {
    function log_activity(
        Model $model, 
        string $event,
        ?array $degisenler = null
    ): void {
        try {
            $baslik = $model->title ?? $model->name ?? 'Bilinmeyen';
            $modelName = class_basename($model); // Model adını al (örneğin "Page")
            $batchUuid = Str::uuid();
            
            // Aktif transaction var mı kontrolü
            if (DB::transactionLevel() > 0) {
                // Transaction içindeyiz, doğrudan DB kullanarak kaydet
                DB::table('activity_log')->insert([
                    'log_name' => $modelName,
                    'description' => "\"{$baslik}\" {$event}",
                    'subject_type' => get_class($model),
                    'subject_id' => $model->id,
                    'causer_type' => auth()->check() ? get_class(auth()->user()) : null,
                    'causer_id' => auth()->id(),
                    'properties' => json_encode([
                        'baslik' => $baslik,
                        'modul' => $modelName,
                        'degisenler' => $degisenler ?: []
                    ]),
                    'batch_uuid' => $batchUuid,
                    'event' => $event,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                // Transaction dışındayız, Spatie kullanabiliriz
                activity()
                    ->performedOn($model)
                    ->causedBy(auth()->user())
                    ->inLog($modelName) // Log name'i model adı yap (örneğin "Page")
                    ->withProperties([
                        'baslik' => $baslik,
                        'modul' => $modelName, // Modül adını properties'e ekle
                        'degisenler' => $degisenler ?: []
                    ])
                    ->tap(function (Activity $activity) use ($batchUuid, $event) {
                        $activity->batch_uuid = $batchUuid; // Batch UUID'yi sütuna ekle
                        $activity->event = $event; // Event'i sütuna ekle
                    })
                    ->log("\"{$baslik}\" {$event}");
            }

            Log::info("Log: {$baslik} - {$event}");
        } catch (\Exception $e) {
            Log::error("Aktivite log hatası: " . $e->getMessage());
            
            // Hata durumunda en azından normal log'a kaydet
            try {
                Log::warning("Log kayıt hatası. İşlem: " . $event . ", Model: " . get_class($model) . ", ID: " . $model->id);
            } catch (\Exception $inner) {
                Log::error("Kritik log hatası: " . $inner->getMessage());
            }
        }
    }
}