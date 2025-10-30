<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationHub;

class CallMeBackForm extends Component
{
    // Form Fields
    public $name = '';
    public $phone = '';
    public $email = '';
    public $company = '';
    public $message = '';
    public $preferred_time = 'anytime'; // anytime, morning, afternoon, evening
    public $terms_accepted = false;

    // Modal State
    public $showModal = false;
    public $modalType = ''; // 'success' or 'error'

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'company' => 'nullable|string|max:255',
        'message' => 'nullable|string|max:1000',
        'preferred_time' => 'required|in:anytime,morning,afternoon,evening',
        'terms_accepted' => 'accepted',
    ];

    protected $messages = [
        'name.required' => 'Ad Soyad zorunludur',
        'phone.required' => 'Telefon zorunludur',
        'email.required' => 'E-posta zorunludur',
        'email.email' => 'Geçerli bir e-posta adresi giriniz',
        'preferred_time.required' => 'Tercih edilen zaman dilimini seçiniz',
        'terms_accepted.accepted' => 'Kişisel verilerin işlenmesini kabul etmelisiniz',
    ];

    public function submit()
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'company' => $this->company,
                'message' => $this->message,
                'preferred_time' => $this->preferred_time,
            ];

            // NotificationHub ile bildirim gönder (Telegram + WhatsApp + Email)
            $this->sendNotifications($data);

            // Log kaydet
            Log::info('Call Me Back Request Received', [
                'customer_name' => $data['name'],
                'customer_phone' => $data['phone'],
                'customer_email' => $data['email'],
                'preferred_time' => $data['preferred_time'],
            ]);

            // Success modal aç
            $this->showModal = true;
            $this->modalType = 'success';

            // Form resetle
            $this->reset([
                'name', 'phone', 'email', 'company', 'message', 'preferred_time', 'terms_accepted'
            ]);

        } catch (\Exception $e) {
            Log::error('Call Me Back Request Error', [
                'error' => $e->getMessage(),
                'data' => $data ?? [],
            ]);

            // Error modal aç
            $this->showModal = true;
            $this->modalType = 'error';
        }
    }

    private function sendNotifications(array $data)
    {
        try {
            $notificationHub = new NotificationHub();

            // Preferred time Türkçe karşılığı
            $preferredTimeMap = [
                'anytime' => 'Farketmez',
                'morning' => 'Sabah (09:00-12:00)',
                'afternoon' => 'Öğleden Sonra (12:00-17:00)',
                'evening' => 'Akşam (17:00-20:00)',
            ];

            $preferredTimeText = $preferredTimeMap[$data['preferred_time']] ?? 'Farketmez';

            // Build inquiry message
            $inquiry = "📞 Sizi Arayalım Talebi\n\n";
            $inquiry .= "Tercih Edilen Zaman: {$preferredTimeText}\n";
            if (!empty($data['message'])) {
                $inquiry .= "\nMesaj: {$data['message']}";
            }

            // Send via NotificationHub (Telegram + WhatsApp + Email)
            $results = $notificationHub->sendCustomerLead(
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'company' => $data['company'],
                ],
                $inquiry,
                [], // Suggested products (boş - bu form product-specific değil)
                [
                    'site' => tenant('domain') ?? parse_url(url('/'), PHP_URL_HOST),
                    'page_url' => url()->current(),
                    'device' => request()->userAgent(),
                    'form_type' => 'Sizi Arayalım',
                ]
            );

            Log::info('Call Me Back Notifications Sent', [
                'telegram' => $results['telegram'],
                'whatsapp' => $results['whatsapp'],
                'email' => $results['email'],
                'total_sent' => $results['sent_count'],
            ]);

        } catch (\Exception $e) {
            Log::error('Call Me Back Notification Failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalType = '';
    }

    public function render()
    {
        return view('livewire.page.call-me-back-form');
    }
}
