<?php

namespace Modules\UserManagement\App\Http\Livewire\Modals;

use Livewire\Component;

class ConfirmActionModal extends Component
{
    public $showModal = false;
    public $title = '';
    public $message = '';
    public $method = '';
    public $params = [];

    protected $listeners = ['showConfirmModal'];

    public function showConfirmModal($data)
    {
        $this->title = $data['title'] ?? 'Onay';
        $this->message = $data['message'] ?? 'Bu işlemi yapmak istediğinize emin misiniz?';
        $this->method = $data['method'] ?? '';
        $this->params = $data['params'] ?? [];
        $this->showModal = true;
    }

    public function confirm()
    {
        if ($this->method) {
            if (empty($this->params)) {
                $this->dispatch($this->method);
            } else {
                $this->dispatch($this->method, $this->params);
            }
        }
        
        $this->showModal = false;
    }

    public function render()
    {
        return view('usermanagement::livewire.modals.confirm-action-modal');
    }
}