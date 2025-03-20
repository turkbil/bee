<?php

use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


if (! function_exists('log_activity')) {
    function log_activity(
        Model $model, 
        string $event,
        ?array $degisenler = null
    ): void {
        $baslik = $model->title ?? $model->name ?? 'Bilinmeyen';
        $modelName = class_basename($model); // Model adını al (örneğin "Page")
        $batchUuid = Str::uuid();

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

        Log::info("Log: {$baslik} - {$event}");
    }
}