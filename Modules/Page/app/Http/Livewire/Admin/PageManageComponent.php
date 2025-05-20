<?php
namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class PageManageComponent extends Component
{
   use WithFileUploads;

   public $pageId;
   public $inputs = [
       'title' => '',
       'body' => '',
       'slug' => '',
       'metakey' => '',
       'metadesc' => '',
       'css' => '',
       'js' => '',
       'is_active' => true,
       'is_homepage' => false,
   ];
   
   public $studioEnabled = false;

   public function mount($id = null)
   {
       if ($id) {
           $this->pageId = $id;
           $page = Page::findOrFail($id);
           $this->inputs = $page->only(array_keys($this->inputs));
       }
       
       // Studio modülü aktif mi kontrol et
       $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\StudioEditor');
   }

   protected function rules()
   {
       return [
           'inputs.title' => 'required|min:3|max:255',
           'inputs.slug' => 'nullable|unique:pages,slug,' . $this->pageId . ',page_id',
           'inputs.metakey' => 'nullable',
           'inputs.metadesc' => 'nullable|string|max:255',
           'inputs.css' => 'nullable|string',
           'inputs.js' => 'nullable|string',
           'inputs.is_active' => 'boolean',
           'inputs.is_homepage' => 'boolean',
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

      // Eğer ana sayfa ise pasif yapılmasına izin verme
      if (($this->inputs['is_homepage'] || ($this->pageId && Page::find($this->pageId)?->is_homepage)) && isset($data['is_active']) && $data['is_active'] == false) {
          $this->dispatch('toast', [
              'title' => 'Uyarı',
              'message' => 'Ana sayfa pasif yapılamaz!',
              'type' => 'warning',
          ]);
          return;
      }
   
      if ($this->pageId) {
          $page = Page::findOrFail($this->pageId);
          $currentData = collect($page->toArray())->only(array_keys($data))->all();
          
          if ($data == $currentData) {
              $toast = [
                  'title' => 'Bilgi',
                  'message' => 'Herhangi bir değişiklik yapılmadı.',
                  'type' => 'info'
              ];
          } else {
              $page->update($data);
              log_activity($page, 'güncellendi');
              
              $toast = [
                  'title' => 'Başarılı!',
                  'message' => 'Sayfa başarıyla güncellendi.',
                  'type' => 'success'
              ];
          }
      } else {
          $page = Page::create($data);
          log_activity($page, 'oluşturuldu');
          
          $toast = [
              'title' => 'Başarılı!',
              'message' => 'Sayfa başarıyla oluşturuldu.',
              'type' => 'success'
          ];
      }
   
      if ($redirect) {
          session()->flash('toast', $toast);
          return redirect()->route('admin.page.index');
      }
   
      $this->dispatch('toast', $toast);
   
      if ($resetForm && !$this->pageId) {
          $this->reset();
      }
   }
   
   public function openStudioEditor()
   {
       if (!$this->pageId) {
           // Önce sayfayı kaydet
           $this->save();
           
           if ($this->pageId) {
               return redirect()->route('admin.studio.editor', ['module' => 'page', 'id' => $this->pageId]);
           }
       } else {
           return redirect()->route('admin.studio.editor', ['module' => 'page', 'id' => $this->pageId]);
       }
   }

   public function render()
   {
       return view('page::admin.livewire.page-manage-component');
   }
}