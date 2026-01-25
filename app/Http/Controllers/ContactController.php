<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    /**
     * Handle contact form submission
     */
    public function submit(Request $request)
    {
        // Rate limiting - 5 requests per minute per IP
        $key = 'contact-form:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Çok fazla deneme yaptınız. Lütfen bir dakika bekleyin.'
            ], 429);
        }

        RateLimiter::hit($key, 60);

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|max:20',
            'subject' => 'nullable|max:100',
            'message' => 'required|min:10|max:2000',
        ], [
            'name.required' => 'Ad Soyad alanı zorunludur.',
            'name.min' => 'Ad Soyad en az 2 karakter olmalıdır.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'message.required' => 'Mesaj alanı zorunludur.',
            'message.min' => 'Mesaj en az 10 karakter olmalıdır.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get site settings
            $toEmail = setting('contact_email_1');
            $siteName = setting('site_title') ?? 'Web Site';

            if (!$toEmail) {
                Log::warning('Contact form: No contact email configured', [
                    'tenant_id' => tenant()?->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'İletişim e-posta adresi yapılandırılmamış.'
                ], 500);
            }

            // Build email content
            $content = $this->buildEmailContent($request, $siteName);

            // Send email
            Mail::raw($content, function ($mail) use ($toEmail, $siteName, $request) {
                $mail->to($toEmail)
                    ->replyTo($request->input('email'), $request->input('name'))
                    ->subject(($request->input('subject') ?: 'İletişim Formu') . ' - ' . $siteName);
            });

            // Log success
            Log::info('Contact form submitted successfully', [
                'tenant_id' => tenant()?->id,
                'from_email' => $request->input('email'),
                'to_email' => $toEmail,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.'
            ]);

        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage(), [
                'tenant_id' => tenant()?->id,
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Mesaj gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.'
            ], 500);
        }
    }

    /**
     * Build email content
     */
    protected function buildEmailContent(Request $request, string $siteName): string
    {
        $content = "Yeni İletişim Formu Mesajı - {$siteName}\n";
        $content .= "================================================\n\n";
        $content .= "Ad Soyad: " . $request->input('name') . "\n";
        $content .= "E-posta: " . $request->input('email') . "\n";

        if ($request->input('phone')) {
            $content .= "Telefon: " . $request->input('phone') . "\n";
        }

        if ($request->input('subject')) {
            $content .= "Konu: " . $request->input('subject') . "\n";
        }

        $content .= "\nMesaj:\n";
        $content .= "------------------------------------------------\n";
        $content .= $request->input('message') . "\n";
        $content .= "------------------------------------------------\n\n";
        $content .= "Gönderim Tarihi: " . now()->format('d.m.Y H:i') . "\n";
        $content .= "IP Adresi: " . $request->ip() . "\n";
        $content .= "User Agent: " . $request->userAgent() . "\n";

        return $content;
    }
}
