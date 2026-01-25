<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $subject = '';
    public string $message = '';

    public bool $submitted = false;
    public string $errorMessage = '';

    // Tema stilleri için
    public string $theme = 'default';
    public string $buttonText = 'Mesaj Gönder';
    public bool $showSubject = true;

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'email' => 'required|email|max:100',
        'phone' => 'nullable|max:20',
        'subject' => 'nullable|max:100',
        'message' => 'required|min:10|max:2000',
    ];

    protected $messages = [
        'name.required' => 'Ad Soyad alanı zorunludur.',
        'name.min' => 'Ad Soyad en az 2 karakter olmalıdır.',
        'email.required' => 'E-posta alanı zorunludur.',
        'email.email' => 'Geçerli bir e-posta adresi giriniz.',
        'message.required' => 'Mesaj alanı zorunludur.',
        'message.min' => 'Mesaj en az 10 karakter olmalıdır.',
    ];

    public function mount(string $theme = 'default', string $buttonText = 'Mesaj Gönder', bool $showSubject = true)
    {
        $this->theme = $theme;
        $this->buttonText = $buttonText;
        $this->showSubject = $showSubject;
    }

    public function submit()
    {
        $this->validate();

        try {
            // Site ayarlarından mail adresini al
            $toEmail = setting('contact_email_1');
            $siteName = setting('site_title');

            if (!$toEmail) {
                $this->errorMessage = 'İletişim e-posta adresi yapılandırılmamış.';
                return;
            }

            // Mail gönder
            Mail::raw($this->buildEmailContent(), function ($mail) use ($toEmail, $siteName) {
                $mail->to($toEmail)
                    ->replyTo($this->email, $this->name)
                    ->subject(($this->subject ?: 'İletişim Formu') . ' - ' . $siteName);
            });

            // Başarılı
            $this->submitted = true;
            $this->reset(['name', 'email', 'phone', 'subject', 'message']);

            // Log
            Log::info('Contact form submitted', [
                'tenant_id' => tenant()?->id,
                'email' => $this->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            $this->errorMessage = 'Mesaj gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
        }
    }

    protected function buildEmailContent(): string
    {
        $content = "Yeni İletişim Formu Mesajı\n";
        $content .= "================================\n\n";
        $content .= "Ad Soyad: {$this->name}\n";
        $content .= "E-posta: {$this->email}\n";

        if ($this->phone) {
            $content .= "Telefon: {$this->phone}\n";
        }

        if ($this->subject) {
            $content .= "Konu: {$this->subject}\n";
        }

        $content .= "\nMesaj:\n";
        $content .= "--------------------------------\n";
        $content .= $this->message . "\n";
        $content .= "--------------------------------\n\n";
        $content .= "Gönderim Tarihi: " . now()->format('d.m.Y H:i') . "\n";
        $content .= "IP: " . request()->ip() . "\n";

        return $content;
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
