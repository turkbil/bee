<?php

use Spatie\Activitylog\Models\Activity;

if (! function_exists('log_activity')) {
    /**
     * Genel Loglama Fonksiyonu
     *
     * @param string $moduleName (Modül adı, örn. "Page")
     * @param string $action (created, updated, deleted)
     * @param mixed $model (İşlem yapılan model örneği)
     * @param array|null $additionalProperties (Ek veriler)
     * @param string|null $event (Loglama olay türü, örneğin 'created', 'updated' vs.)
     */
    function log_activity(string $moduleName, string $action, $model, array $additionalProperties = null, string $event = null): void
    {
        // Tenant bilgisi
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            \Log::error('Tenant bilgisi alınamadı!');
            return;
        }

        $tenant_id = $tenant->id;

        // Batch UUID oluşturuluyor
        $batch_uuid = (string) \Str::uuid();

        $event = $event ?? $action; // ucfirst() kaldırıldı, direk action kullanılıyor

        // Activity log kaydını oluşturuyoruz
        $activity = activity()
            ->performedOn($model)
            ->causedBy(auth()->user() ?? null)
            ->withProperties(array_merge([
                'action'    => $action,
                'type'      => class_basename($model),
                'title'     => $model->title ?? $model->name ?? 'Unnamed',
                'id'        => $model->getKey(),
                'tenant_id' => $tenant_id,   // Tenant ID burada
                'batch_uuid' => $batch_uuid, // Batch UUID burada
                'event' => $event,           // Event burada
            ], $additionalProperties ?? []))
            ->log($moduleName . ' ' . $action); // Modül adı ve işlem türü

        // Activity kaydını veritabanına kaydetmek için activity() fonksiyonu dönüyor.
        // Log kaydını kaydettiğimizde ID'yi almak için activity()->id kullanabiliriz.

        // Şimdi kaydın ID'si alındıktan sonra update işlemi yapılacak
        Activity::where('id', $activity->id)->update([
            'tenant_id' => $tenant_id,   // Tenant ID'yi burada ekliyoruz
            'batch_uuid' => $batch_uuid, // Batch UUID'yi burada ekliyoruz
            'event' => $event,           // Event'i burada ekliyoruz
        ]);

        // Debug logları ekleyelim
        \Log::info('Activity log oluşturuldu', [
            'tenant_id'  => $tenant_id,
            'batch_uuid' => $batch_uuid,
            'event'      => $event,
        ]);
    }
}
