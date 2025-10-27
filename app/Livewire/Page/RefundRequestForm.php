<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RefundRequestNotification;

class RefundRequestForm extends Component
{
    // Form Fields
    public $order_number = '';
    public $order_date = '';
    public $delivery_date = '';
    public $products = '';
    public $invoice_number = '';
    public $tc_number = '';
    public $full_name = '';
    public $address = '';
    public $email = '';
    public $phone = '';
    public $refund_reason = '';
    public $terms_accepted = false;

    // Modal State
    public $showModal = false;
    public $modalType = ''; // 'success' or 'error'

    protected $rules = [
        'order_number' => 'required|string|max:255',
        'order_date' => 'required|date',
        'delivery_date' => 'required|date',
        'products' => 'required|string|max:1000',
        'tc_number' => 'required|string|size:11',
        'full_name' => 'required|string|max:255',
        'address' => 'required|string|max:500',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'terms_accepted' => 'accepted',
        'invoice_number' => 'nullable|string|max:255',
        'refund_reason' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'order_number.required' => 'Sipariş numarası zorunludur',
        'order_date.required' => 'Sipariş tarihi zorunludur',
        'delivery_date.required' => 'Teslim tarihi zorunludur',
        'products.required' => 'İade edilecek ürünleri belirtiniz',
        'tc_number.required' => 'T.C. Kimlik No zorunludur',
        'tc_number.size' => 'T.C. Kimlik No 11 haneli olmalıdır',
        'full_name.required' => 'Ad Soyad zorunludur',
        'address.required' => 'Adres zorunludur',
        'email.required' => 'E-posta zorunludur',
        'email.email' => 'Geçerli bir e-posta adresi giriniz',
        'phone.required' => 'Telefon zorunludur',
        'terms_accepted.accepted' => 'Cayma hakkı şartlarını kabul etmelisiniz',
    ];

    public function submit()
    {
        $this->validate();

        try {
            $data = [
                'order_number' => $this->order_number,
                'order_date' => $this->order_date,
                'delivery_date' => $this->delivery_date,
                'products' => $this->products,
                'invoice_number' => $this->invoice_number,
                'tc_number' => $this->tc_number,
                'full_name' => $this->full_name,
                'address' => $this->address,
                'email' => $this->email,
                'phone' => $this->phone,
                'refund_reason' => $this->refund_reason,
            ];

            // Admin'e bildirim gönder (Mail + Telegram + WhatsApp)
            $this->sendAdminNotification($data);

            // Log kaydet
            Log::info('Refund Request Received', [
                'order_number' => $data['order_number'],
                'customer_email' => $data['email'],
                'customer_name' => $data['full_name'],
            ]);

            // Success modal aç
            $this->showModal = true;
            $this->modalType = 'success';

            // Form resetle
            $this->reset([
                'order_number', 'order_date', 'delivery_date', 'products',
                'invoice_number', 'tc_number', 'full_name', 'address',
                'email', 'phone', 'refund_reason', 'terms_accepted'
            ]);

        } catch (\Exception $e) {
            Log::error('Refund Request Error', [
                'error' => $e->getMessage(),
                'data' => $data ?? [],
            ]);

            // Error modal aç
            $this->showModal = true;
            $this->modalType = 'error';
        }
    }

    private function sendAdminNotification(array $data)
    {
        // Admin email fallback chain
        $adminEmail = config('page.refund.admin_email')
            ?? get_setting('contact_email')
            ?? setting('contact_email_1')
            ?? 'info@' . parse_url(url('/'), PHP_URL_HOST);

        // Notification gönder (Mail + Telegram)
        Notification::route('mail', $adminEmail)
            ->route('telegram', config('services.telegram-bot-api.chat_id'))
            ->notify(new RefundRequestNotification($data));

        // WhatsApp bildirimi gönder
        try {
            $whatsappService = app(\App\Services\WhatsAppNotificationService::class);
            $whatsappService->sendCustomerLead(
                [
                    'name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ],
                'Cayma Hakkı Talebi - Sipariş: ' . $data['order_number'],
                [
                    [
                        'title' => 'İade Edilecek Ürünler: ' . $data['products'],
                        'url' => url('/page/cayma-hakki'),
                    ]
                ],
                [
                    'site' => tenant('domain'),
                    'page_url' => url('/page/cayma-hakki'),
                    'order_number' => $data['order_number'],
                ]
            );
        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalType = '';
    }

    public function render()
    {
        return view('livewire.page.refund-request-form');
    }
}
