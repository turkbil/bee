<?php

namespace Modules\Muzibu\App\Services;

use App\Models\User;
use Modules\Muzibu\App\Models\Certificate;
use Modules\Subscription\App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Exception;

class CertificateService
{
    /**
     * Check if user can create a certificate
     * Only active premium members (not trial) can create
     */
    public function canCreateCertificate(User $user): array
    {
        // Check if user already has a certificate
        $existingCertificate = Certificate::forUser($user->id)->valid()->first();
        if ($existingCertificate) {
            return [
                'can_create' => false,
                'reason' => 'already_exists',
                'certificate' => $existingCertificate,
            ];
        }

        // Check if user has active paid subscription (not trial)
        $activeSubscription = $this->getActivePaidSubscription($user);
        if (!$activeSubscription) {
            return [
                'can_create' => false,
                'reason' => 'no_active_subscription',
                'certificate' => null,
            ];
        }

        return [
            'can_create' => true,
            'reason' => null,
            'certificate' => null,
            'first_paid_date' => $this->getFirstPaidSubscriptionDate($user),
        ];
    }

    /**
     * Get active paid subscription (not trial)
     */
    public function getActivePaidSubscription(User $user): ?Subscription
    {
        return Subscription::on('tenant')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function($q) {
                $q->where('has_trial', false)
                  ->orWhereNull('has_trial');
            })
            ->first();
    }

    /**
     * Get first paid subscription date
     */
    public function getFirstPaidSubscriptionDate(User $user): ?\Carbon\Carbon
    {
        $firstSubscription = Subscription::on('tenant')
            ->where('user_id', $user->id)
            ->where(function($q) {
                $q->where('has_trial', false)
                  ->orWhereNull('has_trial');
            })
            ->orderBy('started_at', 'asc')
            ->first();

        return $firstSubscription?->started_at;
    }

    /**
     * Get all subscription periods for a user (for verification page)
     */
    public function getSubscriptionPeriods(User $user): array
    {
        $subscriptions = Subscription::on('tenant')
            ->where('user_id', $user->id)
            ->where(function($q) {
                $q->where('has_trial', false)
                  ->orWhereNull('has_trial');
            })
            ->orderBy('started_at', 'asc')
            ->get();

        return $subscriptions->map(function ($sub) {
            return [
                'start' => $sub->started_at,
                'end' => $sub->ends_at,
                'is_active' => $sub->status === 'active',
                'plan_name' => $sub->plan?->title ?? 'Premium',
            ];
        })->toArray();
    }

    /**
     * Create a certificate for user
     */
    public function createCertificate(User $user, array $data): Certificate
    {
        // Check eligibility
        $eligibility = $this->canCreateCertificate($user);
        if (!$eligibility['can_create']) {
            throw new Exception('User is not eligible for certificate: ' . $eligibility['reason']);
        }

        // Get first paid subscription date
        $membershipStart = $this->getFirstPaidSubscriptionDate($user);
        if (!$membershipStart) {
            throw new Exception('Could not determine first paid subscription date');
        }

        // Check if skip correction is enabled (for member_name only)
        $skipCorrection = $data['skip_correction'] ?? false;

        // Apply spelling correction (skip for member_name if requested)
        $memberName = $skipCorrection ? $data['member_name'] : Certificate::correctSpelling($data['member_name']);
        $taxOffice = !empty($data['tax_office']) ? Certificate::correctSpelling($data['tax_office']) : null;
        $address = !empty($data['address']) ? Certificate::correctSpelling($data['address']) : null;

        // Create certificate
        return Certificate::create([
            'user_id' => $user->id,
            'certificate_code' => Certificate::generateCode(),
            'qr_hash' => Certificate::generateHash(),
            'member_name' => $memberName,
            'tax_office' => $taxOffice,
            'tax_number' => $data['tax_number'] ?? null,
            'address' => $address,
            'membership_start' => $membershipStart,
            'issued_at' => now(),
            'is_valid' => true,
            'view_count' => 0,
        ]);
    }

    /**
     * Get certificate by hash (for verification)
     */
    public function getCertificateByHash(string $hash): ?Certificate
    {
        return Certificate::where('qr_hash', $hash)->first();
    }

    /**
     * Verify certificate and increment view count
     */
    public function verifyCertificate(string $hash): array
    {
        $certificate = $this->getCertificateByHash($hash);

        if (!$certificate) {
            return [
                'found' => false,
                'certificate' => null,
                'user' => null,
                'subscription_periods' => [],
                'is_currently_active' => false,
            ];
        }

        // Increment view count
        $certificate->incrementViewCount();

        // Get user
        $user = $certificate->user;

        // Check if currently active
        $activeSubscription = $user ? $this->getActivePaidSubscription($user) : null;

        // Get subscription periods
        $periods = $user ? $this->getSubscriptionPeriods($user) : [];

        return [
            'found' => true,
            'certificate' => $certificate,
            'user' => $user,
            'subscription_periods' => $periods,
            'is_currently_active' => $activeSubscription !== null,
        ];
    }

    /**
     * Get user's certificate
     */
    public function getUserCertificate(User $user): ?Certificate
    {
        return Certificate::forUser($user->id)->valid()->first();
    }
}
