<?php

namespace Modules\Mail\App\Services;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;
use Modules\Mail\App\Mail\WelcomeMail;
use Modules\Mail\App\Mail\TrialEndingMail;
use Modules\Mail\App\Mail\SubscriptionRenewalMail;
use Modules\Mail\App\Mail\PaymentSuccessMail;
use Modules\Mail\App\Mail\PaymentFailedMail;
use Modules\Mail\App\Mail\NewDeviceLoginMail;
use Modules\Mail\App\Mail\TwoFactorCodeMail;
use Modules\Mail\App\Mail\CorporateInviteMail;

class MailService
{
    /**
     * Send welcome email
     */
    public function sendWelcome(User $user): void
    {
        Mail::to($user)->queue(new WelcomeMail($user));
    }

    /**
     * Send trial ending reminder
     */
    public function sendTrialEnding(User $user, int $daysLeft): void
    {
        Mail::to($user)->queue(new TrialEndingMail($user, $daysLeft));
    }

    /**
     * Send subscription renewal reminder
     */
    public function sendSubscriptionRenewal(User $user, Subscription $subscription): void
    {
        Mail::to($user)->queue(new SubscriptionRenewalMail($user, $subscription));
    }

    /**
     * Send payment success confirmation
     */
    public function sendPaymentSuccess(User $user, Subscription $subscription): void
    {
        Mail::to($user)->queue(new PaymentSuccessMail($user, $subscription));
    }

    /**
     * Send payment failed notification
     */
    public function sendPaymentFailed(User $user, Subscription $subscription, string $reason = ''): void
    {
        Mail::to($user)->queue(new PaymentFailedMail($user, $subscription, $reason));
    }

    /**
     * Send new device login alert
     */
    public function sendNewDeviceLogin(User $user, string $ip, string $userAgent, string $location = ''): void
    {
        Mail::to($user)->queue(new NewDeviceLoginMail($user, $ip, $userAgent, $location));
    }

    /**
     * Send 2FA code (email backup)
     */
    public function sendTwoFactorCode(User $user, string $code): void
    {
        Mail::to($user)->queue(new TwoFactorCodeMail($user, $code));
    }

    /**
     * Send corporate invite
     */
    public function sendCorporateInvite(User $corporateUser, string $email): void
    {
        Mail::to($email)->queue(new CorporateInviteMail($corporateUser, $email));
    }
}
