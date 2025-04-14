<?php
namespace App\Livewire\Modals;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\User;

class UserDeleteModal extends Component
{
   public $showModal = false;
   public $userId;
   public $userName;

   protected $listeners = ['showDeleteModal'];

   public function showDeleteModal($userId, $userName)
   {
       $this->userId = $userId;
       $this->userName = $userName;
       $this->showModal = true;
   }

   public function delete()
   {
       try {
           DB::beginTransaction();

           $user = User::find($this->userId);

           if (!$user) {
               $this->dispatch('toast', [
                   'title' => 'Hata!',
                   'message' => "Kullanıcı bulunamadı.",
                   'type' => 'error',
               ]);
               return;
           }

           log_activity(
               $user,
               'silindi'
           );

           $user->delete();

           DB::commit();

           $this->showModal = false;

           $this->dispatch('toast', [
               'title' => 'Silindi!',
               'message' => "\"{$this->userName}\" kullanıcısı silindi.",
               'type' => 'danger',
           ]);

           $this->dispatch('itemDeleted', $this->userId)->to('user-component');

       } catch (\Exception $e) {
           DB::rollBack();

           $this->dispatch('toast', [
               'title' => 'Hata!',
               'message' => 'Silme işlemi sırasında bir hata oluştu.',
               'type' => 'error',
           ]);
       }
   }
   
   public function render()
   {
       return view('livewire.modals.user-delete-modal');
   }
}