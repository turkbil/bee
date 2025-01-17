<?php

use Spatie\Activitylog\Models\Activity;

if (! function_exists('log_activity')) {
    function log_activity(string $moduleName, string $description, $model, ?array $additionalProperties = null, ?string $event = null): void
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            \Log::error('Tenant bilgisi alınamadı!');
            return;
        }

        $tenant_id  = $tenant->id;
        $batch_uuid = (string) \Str::uuid();

        $event = $event ?? 'action'; // Eğer event gönderilmezse varsayılan "action"

        $activity = activity()
            ->performedOn($model)
            ->causedBy(auth()->user() ?? null)
            ->withProperties(array_merge([
                'module'     => $moduleName,
                'action'     => $event,
                'type'       => class_basename($model),
                'title'      => $model->title ?? $model->name ?? 'Bilinmeyen',
                'id'         => $model->getKey(),
                'tenant_id'  => $tenant_id,
                'batch_uuid' => $batch_uuid,
            ], $additionalProperties ?? []))
            ->log($description);

        Activity::where('id', $activity->id)->update([
            'tenant_id'  => $tenant_id,
            'batch_uuid' => $batch_uuid,
            'event'      => $event,
        ]);

        \Log::info('Activity log oluşturuldu', [
            'tenant_id'   => $tenant_id,
            'batch_uuid'  => $batch_uuid,
            'event'       => $event,
            'description' => $description,
        ]);
    }
}
