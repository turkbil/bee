<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\Certificate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Certificate Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Activity logging ve cache temizleme işlemlerini otomatikleştirir.
 */
class CertificateObserver
{
    /**
     * Handle the Certificate "creating" event.
     */
    public function creating(Certificate $certificate): void
    {
        // Otomatik sertifika kodu oluştur
        if (empty($certificate->certificate_code)) {
            $certificate->certificate_code = Certificate::generateCode();
        }

        // Otomatik QR hash oluştur
        if (empty($certificate->qr_hash)) {
            $certificate->qr_hash = Certificate::generateHash();
        }

        // Varsayılan değerler
        if (!isset($certificate->is_valid)) {
            $certificate->is_valid = true;
        }

        if (!isset($certificate->view_count)) {
            $certificate->view_count = 0;
        }

        if (empty($certificate->issued_at)) {
            $certificate->issued_at = now();
        }

        Log::info('Muzibu Certificate creating', [
            'certificate_code' => $certificate->certificate_code,
            'user_id' => $certificate->user_id,
            'member_name' => $certificate->member_name,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Handle the Certificate "created" event.
     */
    public function created(Certificate $certificate): void
    {
        $this->clearCertificateCaches($certificate->user_id);

        if (function_exists('log_activity')) {
            log_activity($certificate, 'oluşturuldu');
        }

        Log::info('Muzibu Certificate created successfully', [
            'id' => $certificate->id,
            'certificate_code' => $certificate->certificate_code,
            'user_id' => $certificate->user_id,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Handle the Certificate "updating" event.
     */
    public function updating(Certificate $certificate): void
    {
        $dirty = $certificate->getDirty();

        Log::info('Muzibu Certificate updating', [
            'id' => $certificate->id,
            'certificate_code' => $certificate->certificate_code,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Certificate "updated" event.
     */
    public function updated(Certificate $certificate): void
    {
        $this->clearCertificateCaches($certificate->user_id, $certificate->id, $certificate->qr_hash);

        if (function_exists('log_activity')) {
            $changes = $certificate->getChanges();
            unset($changes['updated_at']);
            unset($changes['view_count']); // View count değişikliklerini loglamayalım

            if (!empty($changes)) {
                $description = null;

                // is_valid değişikliği önemli
                if (isset($changes['is_valid'])) {
                    $description = $certificate->is_valid ? 'aktifleştirildi' : 'iptal edildi';
                } else {
                    $description = 'güncellendi';
                }

                log_activity($certificate, $description, [
                    'changed_fields' => array_keys($changes)
                ]);
            }
        }

        Log::info('Muzibu Certificate updated successfully', [
            'id' => $certificate->id,
            'certificate_code' => $certificate->certificate_code,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the Certificate "saving" event.
     */
    public function saving(Certificate $certificate): void
    {
        // Member name validation ve düzeltme
        if (!empty($certificate->member_name)) {
            // Turkish title case uygula
            $certificate->member_name = Certificate::correctSpelling($certificate->member_name);
        }

        // Tax number formatı (sadece rakam)
        if (!empty($certificate->tax_number)) {
            $certificate->tax_number = preg_replace('/[^0-9]/', '', $certificate->tax_number);
        }
    }

    /**
     * Handle the Certificate "saved" event.
     */
    public function saved(Certificate $certificate): void
    {
        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the Certificate "deleting" event.
     */
    public function deleting(Certificate $certificate): bool
    {
        Log::warning('Muzibu Certificate deleting', [
            'id' => $certificate->id,
            'certificate_code' => $certificate->certificate_code,
            'user_id' => $certificate->user_id,
            'member_name' => $certificate->member_name,
            'deleted_by' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the Certificate "deleted" event.
     */
    public function deleted(Certificate $certificate): void
    {
        $this->clearCertificateCaches($certificate->user_id, $certificate->id, $certificate->qr_hash);

        if (function_exists('log_activity')) {
            log_activity($certificate, 'silindi', null, $certificate->certificate_code);
        }

        Log::warning('Muzibu Certificate deleted', [
            'id' => $certificate->id,
            'certificate_code' => $certificate->certificate_code,
            'user_id' => $certificate->user_id,
            'deleted_by' => auth()->id()
        ]);
    }

    /**
     * Clear certificate related caches
     */
    private function clearCertificateCaches(?int $userId = null, ?int $certificateId = null, ?string $qrHash = null): void
    {
        Cache::forget('muzibu_certificates_list');

        if ($userId) {
            Cache::forget("muzibu_certificates_user_{$userId}");
        }

        if ($certificateId) {
            Cache::forget("muzibu_certificate_detail_{$certificateId}");
        }

        if ($qrHash) {
            Cache::forget("muzibu_certificate_hash_{$qrHash}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['muzibu_certificates', 'muzibu', 'corporate'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }
}
