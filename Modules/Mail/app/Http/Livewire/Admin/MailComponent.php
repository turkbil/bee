<?php

declare(strict_types=1);

namespace Modules\Mail\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Mail;
use Modules\Mail\Services\MailTemplateService;
use Modules\Mail\Models\MailTemplate;

class MailComponent extends Component
{
    #[Url]
    public string $search = '';

    public ?string $selectedKey = null;
    public ?string $previewHtml = null;
    public string $testEmail = '';

    // Edit modal
    public bool $showEditModal = false;
    public ?string $editKey = null;
    public string $editName = '';
    public array $editSubject = ['tr' => '', 'en' => ''];
    public array $editContent = ['tr' => '', 'en' => ''];
    public string $editCategory = 'system';
    public bool $editIsActive = true;

    protected MailTemplateService $templateService;

    public function boot(MailTemplateService $templateService): void
    {
        $this->templateService = $templateService;
    }

    public function mount(): void
    {
        $this->testEmail = auth()->user()->email ?? '';
    }

    public function getTemplatesProperty(): array
    {
        $templates = $this->templateService->getAllTemplates();

        if (empty($this->search)) {
            return $templates;
        }

        return array_filter($templates, function ($template) {
            $searchLower = strtolower($this->search);
            return str_contains(strtolower($template['name'] ?? ''), $searchLower) ||
                   str_contains(strtolower($template['key'] ?? ''), $searchLower) ||
                   str_contains(strtolower($template['category'] ?? ''), $searchLower);
        });
    }

    public function preview(string $key): void
    {
        $this->selectedKey = $key;
        $template = $this->templateService->getTemplate($key);

        if (!$template) {
            $this->previewHtml = '<div class="text-danger">Şablon bulunamadı</div>';
            return;
        }

        $sampleVariables = $this->getSampleVariables($template);
        $content = $template->getContentForLocale('tr');
        $this->previewHtml = $this->templateService->renderContent($content, $sampleVariables);
    }

    protected function getSampleVariables(MailTemplate $template): array
    {
        $defaults = [
            'user_name' => 'Test Kullanıcı',
            'user_email' => 'test@example.com',
            'days_left' => '3',
            'renewal_date' => now()->addDays(7)->format('d.m.Y'),
            'plan_name' => 'Pro Plan',
            'amount' => '99.00 TL',
            'reason' => 'Yetersiz bakiye',
            'device_name' => 'Chrome - Windows',
            'ip_address' => '192.168.1.1',
            'login_time' => now()->format('d.m.Y H:i'),
            'location' => 'İstanbul, Türkiye',
            'code' => '123456',
            'expires_in' => '5',
            'company_name' => 'ABC Şirketi',
            'invite_code' => 'ABC123',
            'transaction_id' => 'TXN' . rand(100000, 999999),
        ];

        $variables = [];
        foreach ($template->variables ?? [] as $var) {
            $variables[$var] = $defaults[$var] ?? "{{$var}}";
        }

        return $variables;
    }

    public function closePreview(): void
    {
        $this->selectedKey = null;
        $this->previewHtml = null;
    }

    public function edit(string $key): void
    {
        $template = $this->templateService->getTemplate($key);

        if (!$template) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Şablon bulunamadı'
            ]);
            return;
        }

        $this->editKey = $key;
        $this->editName = $template->name;
        $this->editSubject = $template->subject ?? ['tr' => '', 'en' => ''];
        $this->editContent = $template->content ?? ['tr' => '', 'en' => ''];
        $this->editCategory = $template->category;
        $this->editIsActive = $template->is_active;
        $this->showEditModal = true;
    }

    public function saveTemplate(): void
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editSubject.tr' => 'required|string|max:255',
            'editContent.tr' => 'required|string',
        ]);

        $this->templateService->saveTemplate($this->editKey, [
            'name' => $this->editName,
            'subject' => $this->editSubject,
            'content' => $this->editContent,
            'category' => $this->editCategory,
            'is_active' => $this->editIsActive,
        ]);

        $this->showEditModal = false;
        $this->resetEditForm();

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Başarılı',
            'message' => 'Şablon kaydedildi'
        ]);
    }

    public function resetToDefault(string $key): void
    {
        if ($this->templateService->resetToDefault($key)) {
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => 'Şablon varsayılana döndürüldü'
            ]);
        } else {
            $this->dispatch('toast', [
                'type' => 'info',
                'title' => 'Bilgi',
                'message' => 'Şablon zaten varsayılan'
            ]);
        }
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetEditForm();
    }

    protected function resetEditForm(): void
    {
        $this->editKey = null;
        $this->editName = '';
        $this->editSubject = ['tr' => '', 'en' => ''];
        $this->editContent = ['tr' => '', 'en' => ''];
        $this->editCategory = 'system';
        $this->editIsActive = true;
    }

    public function sendTest(string $key, ?string $email = null): void
    {
        $targetEmail = $email ?? $this->testEmail;

        if (empty($targetEmail)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'E-posta adresi gerekli'
            ]);
            return;
        }

        $template = $this->templateService->getTemplate($key);

        if (!$template) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Şablon bulunamadı'
            ]);
            return;
        }

        try {
            $variables = $this->getSampleVariables($template);
            $subject = $this->templateService->renderContent($template->getSubjectForLocale('tr'), $variables);
            $content = $this->templateService->renderContent($template->getContentForLocale('tr'), $variables);

            Mail::html($content, function ($message) use ($subject, $targetEmail) {
                $message->to($targetEmail)
                    ->subject($subject);
            });

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => "Test maili {$targetEmail} adresine gönderildi"
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Mail gönderilemedi: ' . $e->getMessage()
            ]);
        }
    }

    public function saveTemplateFromAlpine(array $data): void
    {
        $this->templateService->saveTemplate($data['key'], [
            'name' => $data['name'],
            'subject' => ['tr' => $data['subject_tr'], 'en' => $data['subject_en']],
            'content' => ['tr' => $data['content_tr'], 'en' => $data['content_en']],
            'category' => $data['category'],
            'is_active' => $data['is_active'],
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Başarılı',
            'message' => 'Şablon kaydedildi'
        ]);
    }

    protected function getCategoryIcon(string $category): string
    {
        return match ($category) {
            'auth' => 'fa-shield-halved',
            'payment' => 'fa-credit-card',
            'subscription' => 'fa-rotate',
            'corporate' => 'fa-building',
            default => 'fa-envelope',
        };
    }

    protected function getCategoryColor(string $category): string
    {
        return match ($category) {
            'auth' => 'bg-blue-lt',
            'payment' => 'bg-green-lt',
            'subscription' => 'bg-purple-lt',
            'corporate' => 'bg-orange-lt',
            default => 'bg-secondary-lt',
        };
    }

    public function render()
    {
        return view('mail::admin.livewire.mail-component');
    }
}
