<?php

declare(strict_types=1);

namespace Modules\Payment\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Payment\App\Contracts\PaymentRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\DataTransferObjects\{PaymentOperationResult, BulkOperationResult};
use Modules\Payment\App\Exceptions\{PaymentNotFoundException, PaymentCreationException, PaymentProtectionException};
use App\Services\BaseService;
use Throwable;

readonly class PaymentService extends BaseService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}

    public function getPayment(int $id): Payment
    {
        return $this->paymentRepository->findById($id)
            ?? throw PaymentNotFoundException::withId($id);
    }

    public function getPaymentBySlug(string $slug, string $locale = 'tr'): Payment
    {
        return $this->paymentRepository->findBySlug($slug, $locale)
            ?? throw PaymentNotFoundException::withSlug($slug, $locale);
    }

    public function getActivePayments(): Collection
    {
        return $this->paymentRepository->getActive();
    }

    public function getPaginatedPayments(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->paymentRepository->getPaginated($filters, $perPage);
    }

    public function searchPayments(string $term, array $locales = []): Collection
    {
        return $this->paymentRepository->search($term, $locales);
    }

    public function createPayment(array $data): PaymentOperationResult
    {
        try {
            // Slug otomatik oluşturma
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title']);
            }

            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo']);
            }

            $payment = $this->paymentRepository->create($data);

            Log::info('Payment created', [
                'payment_id' => $payment->payment_id,
                'title' => $payment->title,
                'user_id' => auth()->id()
            ]);

            return PaymentOperationResult::success(
                message: __('payment::admin.payment_created_successfully'),
                data: $payment
            );
        } catch (Throwable $e) {
            Log::error('Payment creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            throw PaymentCreationException::withDatabaseError($e->getMessage());
        }
    }

    public function updatePayment(int $id, array $data): PaymentOperationResult
    {
        try {
            $payment = $this->paymentRepository->findById($id)
                ?? throw PaymentNotFoundException::withId($id);

            // Slug güncelleme
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $payment->slug ?? []);
            }

            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo'], $payment->seo ?? []);
            }

            $this->paymentRepository->update($id, $data);

            Log::info('Payment updated', [
                'payment_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);

            return PaymentOperationResult::success(
                message: __('payment::admin.payment_updated_successfully'),
                data: $payment->refresh()
            );
        } catch (PaymentNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Payment update failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return PaymentOperationResult::error(
                message: __('payment::admin.update_failed'),
                type: 'error'
            );
        }
    }

    public function deletePayment(int $id): PaymentOperationResult
    {
        try {
            $payment = $this->paymentRepository->findById($id)
                ?? throw PaymentNotFoundException::withId($id);

            $this->paymentRepository->delete($id);

            Log::info('Payment deleted', [
                'payment_id' => $id,
                'title' => $payment->title,
                'user_id' => auth()->id()
            ]);

            return PaymentOperationResult::success(
                message: __('payment::admin.payment_deleted_successfully')
            );
        } catch (PaymentNotFoundException | PaymentProtectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Payment deletion failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return PaymentOperationResult::error(
                message: __('payment::admin.deletion_failed'),
                type: 'error'
            );
        }
    }

    public function togglePaymentStatus(int $id): PaymentOperationResult
    {
        try {
            $payment = $this->paymentRepository->findById($id)
                ?? throw PaymentNotFoundException::withId($id);

            $this->paymentRepository->toggleActive($id);
            $payment->refresh();

            Log::info('Payment status toggled', [
                'payment_id' => $id,
                'new_status' => $payment->is_active,
                'user_id' => auth()->id()
            ]);

            return PaymentOperationResult::success(
                message: __($payment->is_active ? 'admin.activated' : 'admin.deactivated'),
                data: $payment,
                meta: ['new_status' => $payment->is_active]
            );
        } catch (PaymentNotFoundException $e) {
            return PaymentOperationResult::error(
                message: __('payment::admin.payment_not_found'),
                type: 'error'
            );
        } catch (PaymentProtectionException $e) {
            return PaymentOperationResult::warning(
                message: $e->getMessage()
            );
        } catch (Throwable $e) {
            Log::error('Payment status toggle failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return PaymentOperationResult::error(
                message: __('admin.operation_failed'),
                type: 'error'
            );
        }
    }

    public function bulkDeletePayments(array $ids): BulkOperationResult
    {
        try {
            if (empty($ids)) {
                return BulkOperationResult::failure(
                    message: __('admin.no_payments_selected')
                );
            }

            $deletedCount = $this->paymentRepository->bulkDelete($ids);

            Log::info('Bulk delete performed', [
                'deleted_count' => $deletedCount,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::success(
                message: __('admin.deleted_successfully'),
                affectedCount: $deletedCount
            );
        } catch (Throwable $e) {
            Log::error('Bulk delete failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::failure(
                message: __('admin.bulk_operation_failed'),
                errors: [$e->getMessage()]
            );
        }
    }

    public function bulkTogglePaymentStatus(array $ids): BulkOperationResult
    {
        try {
            $affectedCount = $this->paymentRepository->bulkToggleActive($ids);

            Log::info('Bulk status toggle performed', [
                'affected_count' => $affectedCount,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::success(
                message: __('admin.updated_successfully'),
                affectedCount: $affectedCount
            );
        } catch (Throwable $e) {
            Log::error('Bulk status toggle failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::failure(
                message: __('admin.bulk_operation_failed'),
                errors: [$e->getMessage()]
            );
        }
    }

    protected function generateSlugsFromTitles(array $titles, array $existingSlugs = []): array
    {
        $slugs = $existingSlugs;

        foreach ($titles as $locale => $title) {
            if (!empty($title) && empty($slugs[$locale])) {
                $slugs[$locale] = \Str::slug($title);
            }
        }

        return $slugs;
    }

    protected function prepareSeoData(array $seoData, array $existingSeo = []): array
    {
        $prepared = $existingSeo;

        foreach ($seoData as $locale => $data) {
            if (is_array($data)) {
                $cleanData = array_filter($data, function($value) {
                    return !is_null($value) && $value !== '' && $value !== [];
                });

                if (!empty($cleanData)) {
                    $prepared[$locale] = array_merge($prepared[$locale] ?? [], $cleanData);
                }
            }
        }

        return $prepared;
    }

    /**
     * Sayfa verilerini form için hazırla
     */
    public function preparePaymentForForm(int $id, string $language): array
    {
        // 🚨 PERFORMANCE FIX: Eager loading ile bir seferde çek
        $payment = $this->paymentRepository->findByIdWithSeo($id);

        if (!$payment) {
            return $this->getEmptyFormData($language);
        }

        // 🚨 PERFORMANCE FIX: SEO data'yı sadece bir kez çek
        $seoData = $this->seoRepository->getSeoData($payment, $language);

        // Tab completion durumunu hesapla
        $allData = array_merge($payment->toArray(), $seoData);
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'payment');

        return [
            'payment' => $payment,
            'seoData' => $seoData, // Tekrar çekme!
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('payment'),
            'seoLimits' => $this->seoRepository->getFieldLimits('payment')
        ];
    }

    /**
     * Yeni sayfa için boş form verisi
     */
    public function getEmptyFormData(string $language): array
    {
        $emptyData = [
            'title' => '',
            'body' => '',
            'slug' => '',
            'seo_title' => '',
            'seo_description' => '',
            'seo_keywords' => '',
            'canonical_url' => ''
        ];

        return [
            'payment' => null,
            'seoData' => $emptyData,
            'tabCompletion' => GlobalTabService::getTabCompletionStatus($emptyData, 'payment'),
            'tabConfig' => GlobalTabService::getJavaScriptConfig('payment'),
            'seoLimits' => $this->seoRepository->getFieldLimits('payment')
        ];
    }

    /**
     * Form validation kurallarını getir
     */
    public function getValidationRules(array $availableLanguages): array
    {
        $rules = [
            'inputs.is_active' => 'boolean',
        ];

        // Default locale'i al
        $defaultLocale = get_tenant_default_locale();

        // Çoklu dil alanları
        foreach ($availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === $defaultLocale ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
            $rules["multiLangInputs.{$lang}.slug"] = 'nullable|string|max:255';
            $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
        }

        // SEO validation kuralları
        $seoRules = $this->seoRepository->getValidationRules('payment');

        return array_merge($rules, $seoRules);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData, 'payment');
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $result = $this->paymentRepository->updateSeoField($id, $locale, $field, $value);

        if ($result) {
            Log::info('SEO field updated', [
                'payment_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    public function clearCache(): void
    {
        $this->paymentRepository->clearCache();

        Log::info('Payment cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}
