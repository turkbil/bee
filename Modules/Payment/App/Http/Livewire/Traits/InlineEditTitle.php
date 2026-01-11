<?php

namespace Modules\Payment\App\Http\Livewire\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

trait InlineEditTitle
{
    public $editingTitleId = null;
    public $newTitle = '';

    public function startEditingTitle($id, $currentTitle)
    {
        $this->editingTitleId = $id;
        $this->newTitle = $currentTitle;
    }

    public function updateTitleInline()
    {
        if (!$this->editingTitleId) {
            return;
        }

        $modelClass = $this->getModelClass();
        $model = $modelClass::where('payment_id', $this->editingTitleId)
            ->first();

        if ($model) {
            $validator = Validator::make(
                ['title' => $this->newTitle],
                ['title' => 'required|string|max:191']
            );

            if ($validator->fails()) {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => __('admin.title_validation_error'),
                    'type' => 'error',
                ]);
                return;
            }

            // Site dilini al (hibrit sistem)
            $currentSiteLocale = method_exists($this, 'getSiteLocale')
                ? $this->getSiteLocale()
                : session('tenant_locale', 'tr');

            // Mevcut başlık değerini kontrol et
            $currentTitle = $model->getTranslated('title', $currentSiteLocale);
            if ($currentTitle === $this->newTitle) {
                $this->editingTitleId = null;
                $this->newTitle = '';
                return;
            }

            // JSON title güncelle
            $titles = is_array($model->title) ? $model->title : [];
            $oldTitle = $titles[$currentSiteLocale] ?? '';
            $titles[$currentSiteLocale] = Str::limit($this->newTitle, 191, '');
            $model->title = $titles;
            $model->save();

            log_activity(
                $model,
                __('admin.title_updated'),
                ['old' => $oldTitle, 'new' => $titles[$currentSiteLocale], 'locale' => $currentSiteLocale]
            );

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.title_updated_successfully'),
                'type' => 'success',
            ]);
        }

        $this->editingTitleId = null;
        $this->newTitle = '';
        $this->dispatch('refresh');
    }

    abstract protected function getModelClass();
}
