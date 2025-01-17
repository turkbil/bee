<?php
namespace Modules\Portfolio\App\Http\Livewire\Traits;

use Illuminate\Support\Facades\Validator;

trait InlineEditTitle
{
    public $editingTitleId = null;
    public $newTitle       = '';

    public function startEditingTitle($id, $currentTitle)
    {
        $this->editingTitleId = $id;
        $this->newTitle       = $currentTitle;
    }

    public function updateTitleInline()
    {
        // Tenant ve model kontrolü
        $tenant = tenancy()->tenant;
        if (! $tenant || ! $this->editingTitleId) {
            return;
        }

        // Modeli al
        $modelClass = $this->getModelClass();
        $model      = $modelClass::where('portfolio_id', $this->editingTitleId)
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($model) {
            // Doğrulama
            $validator = Validator::make(
                ['title' => $this->newTitle],
                ['title' => 'required|string|max:255']
            );

            if ($validator->fails()) {
                $this->dispatch('toast', [
                    'title'   => 'Hata!',
                    'message' => 'Başlık geçersiz. Lütfen kontrol edin.',
                    'type'    => 'error',
                ]);
                return;
            }

            // Başlık değişmediyse işlem yapma
            if ($model->title === $this->newTitle) {
                $this->editingTitleId = null;
                $this->newTitle       = '';
                return;
            }

            // Başlığı güncelle
            $oldTitle     = $model->title;
            $model->title = $this->newTitle;
            $model->save();

            // Loglama
            log_activity(
                'Sayfa',
                "\"{$oldTitle}\" başlığı \"{$this->newTitle}\" olarak güncellendi.",
                $model,
                [],
                'baslik_guncellendi'
            );

            // Toast mesajı göster
            $this->dispatch('toast', [
                'title'   => 'Başarılı!',
                'message' => "\"{$oldTitle}\" başlığı \"{$this->newTitle}\" olarak güncellendi.",
                'type'    => 'success',
            ]);
        }

        // Formu resetle
        $this->editingTitleId = null;
        $this->newTitle       = '';
        $this->dispatch('refresh');
    }

    abstract protected function getModelClass();
}