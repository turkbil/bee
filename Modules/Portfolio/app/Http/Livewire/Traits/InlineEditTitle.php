<?php
namespace Modules\Portfolio\App\Http\Livewire\Traits;

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
        $model = $modelClass::where('portfolio_id', $this->editingTitleId)
            ->first();

        if ($model) {
            $validator = Validator::make(
                ['title' => $this->newTitle],
                ['title' => 'required|string|max:191']
            );

            if ($validator->fails()) {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Başlık geçersiz. Lütfen kontrol edin.',
                    'type' => 'error',
                ]);
                return;
            }

            if ($model->title === $this->newTitle) {
                $this->editingTitleId = null;
                $this->newTitle = '';
                return;
            }

            $oldTitle = $model->title;
            $model->title = Str::limit($this->newTitle, 191, '');
            $model->save();

            log_activity(
                $model, 
                'başlık güncellendi',
                ['old' => $oldTitle, 'new' => $model->title]
            );

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "Başlık başarıyla güncellendi.",
                'type' => 'success',
            ]);
        }

        $this->editingTitleId = null;
        $this->newTitle = '';
        $this->dispatch('refresh');
    }

    abstract protected function getModelClass();
}