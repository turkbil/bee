<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CallMeBackNotification;

class CallMeBackForm extends Component
{
    // Form Fields
    public $name = '';
    public $phone = '';
    public $email = '';

    // Page Context (captured on mount or query params)
    public $pageUrl = '';
    public $referrerUrl = '';
    public $productId = null;
    public $productName = null;

    // Modal State
    public $showModal = false;
    public $modalType = ''; // 'success' or 'error'

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
    ];

    protected $messages = [
        'name.required' => 'Ad Soyad zorunludur',
        'phone.required' => 'Telefon zorunludur',
        'email.email' => 'Geçerli bir e-posta adresi giriniz',
    ];

    public function mount()
    {
        // localStorage'dan gelecek (JavaScript tarafında set edilecek)
        // Bu değerler Livewire'a JavaScript'ten gönderilecek

        // Fallback: Normal referer header
        $this->pageUrl = url()->current();
        $this->referrerUrl = request()->headers->get('referer', '');
    }

    // JavaScript'ten localStorage verilerini al
    public function setContextFromLocalStorage($productId, $productName, $fromUrl)
    {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->pageUrl = $fromUrl;
        $this->referrerUrl = $fromUrl;
    }

    public function submit()
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'referrer' => $this->referrerUrl,
                'landing_page' => $this->pageUrl,
                'product_id' => $this->productId,
                'product_name' => $this->productName,
            ];

            // NotificationHub ile bildirim gönder (Telegram + WhatsApp + Email)
            $this->sendNotifications($data);

            // Log kaydet
            Log::info('Call Me Back Request Received', [
                'customer_name' => $data['name'],
                'customer_phone' => $data['phone'],
                'customer_email' => $data['email'],
            ]);

            // Success modal aç
            $this->showModal = true;
            $this->modalType = 'success';

            // Form resetle
            $this->reset(['name', 'phone', 'email']);

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
            // Admin email fallback chain: config → domain-based
            $adminEmail = config('mail.from.address') ?? 'info@' . parse_url(url('/'), PHP_URL_HOST);

            // Send notification via Laravel Notification system (Mail + Telegram)
            Notification::route('mail', $adminEmail)
                ->route('telegram', config('services.telegram-bot-api.chat_id'))
                ->notify(new CallMeBackNotification(
                    [
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'email' => $data['email'],
                    ],
                    $data['referrer'] ?? '',
                    $data['landing_page'] ?? '',
                    $data['product_id'] ?? null,
                    $data['product_name'] ?? null
                ));

            Log::info('Call Me Back Notification Sent', [
                'customer_name' => $data['name'],
                'customer_phone' => $data['phone'],
                'admin_email' => $adminEmail,
                'telegram_chat_id' => config('services.telegram-bot-api.chat_id'),
            ]);

        } catch (\Exception $e) {
            Log::error('Call Me Back Notification Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
