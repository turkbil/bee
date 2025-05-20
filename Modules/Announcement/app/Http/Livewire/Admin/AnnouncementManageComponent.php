<?php
namespace Modules\Announcement\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Announcement\App\Models\Announcement;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class AnnouncementManageComponent extends Component
{
   use WithFileUploads;

   public $announcementId;
   public $inputs = [
       'title' => '',
       'body' => '',
       'slug' => '',
       'metakey' => '',
       'metadesc' => '',
       'is_active' => true,
   ];
   
   public $studioEnabled = false;

   public function mount($id = null)
   {
       if ($id) {
           $this->announcementId = $id;
           $announcement = Announcement::findOrFail($id);
           $this->inputs = $announcement->only(array_keys($this->inputs));
       }
       
       // Studio modülü aktif mi kontrol et
       $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\StudioEditor');
   }

   protected function rules()
   {
       return [
           'inputs.title' => 'required|min:3|max:255',
           'inputs.slug' => 'nullable|unique:announcements,slug,' . $this->announcementId . ',announcement_id',
           'inputs.metakey' => 'nullable',
           'inputs.metadesc' => 'nullable|string|max:255',
           'inputs.is_active' => 'boolean',
       ];
   }

   protected $messages = [
       'inputs.title.required' => 'Başlık alanı zorunludur.',
       'inputs.title.min' => 'Başlık en az 3 karakter olmalıdır.',
       'inputs.title.max' => 'Başlık 255 karakteri geçemez.',
   ];

   public function save($redirect = false, $resetForm = false)
   {
      $this->validate();
      
      $data = array_merge($this->inputs, [
          'title' => Str::limit($this->inputs['title'], 191, ''),
          'slug' => $this->inputs['slug'] ?: Str::slug($this->inputs['title']),
          'metakey' => is_array($this->inputs['metakey']) ? implode(',', $this->inputs['metakey']) : $this->inputs['metakey'],
          'metadesc' => Str::limit($this->inputs['metadesc'] ?? $this->inputs['body'], 191, '')
      ]);
   
      if ($this->announcementId) {
          $announcement = Announcement::findOrFail($this->announcementId);
          $currentData = collect($announcement->toArray())->only(array_keys($data))->all();
          
          if ($data == $currentData) {
              $toast = [
                  'title' => 'Bilgi',
                  'message' => 'Herhangi bir değişiklik yapılmadı.',
                  'type' => 'info'
              ];
          } else {
              $announcement->update($data);
              log_activity($announcement, 'güncellendi');
              
              $toast = [
                  'title' => 'Başarılı!',
                  'message' => 'Duyuru başarıyla güncellendi.',
                  'type' => 'success'
              ];
          }
      } else {
          $announcement = Announcement::create($data);
          log_activity($announcement, 'oluşturuldu');
          
          $toast = [
              'title' => 'Başarılı!',
              'message' => 'Duyuru başarıyla oluşturuldu.',
              'type' => 'success'
          ];
      }
   
      if ($redirect) {
          session()->flash('toast', $toast);
          return redirect()->route('admin.announcement.index');
      }
   
      $this->dispatch('toast', $toast);
   
      if ($resetForm && !$this->announcementId) {
          $this->reset();
      }
   }
   
   public function openStudioEditor()
   {
       if (!$this->announcementId) {
           // Önce duyuruyı kaydet
           $this->save();
           
           if ($this->announcementId) {
               return redirect()->route('admin.studio.editor', ['module' => 'announcement', 'id' => $this->announcementId]);
           }
       } else {
           return redirect()->route('admin.studio.editor', ['module' => 'announcement', 'id' => $this->announcementId]);
       }
   }

   public function render()
   {
       return view('announcement::admin.livewire.announcement-manage-component');
   }
}