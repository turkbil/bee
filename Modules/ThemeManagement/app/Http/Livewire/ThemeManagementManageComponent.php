<?php

namespace Modules\ThemeManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\ThemeManagement\App\Models\Theme;
use Modules\ThemeManagement\App\Http\Livewire\Traits\WithImageUpload;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Layout('admin.layout')]
class ThemeManagementManageComponent extends Component
{
   use WithFileUploads, WithImageUpload;

   public $themeId;
   public $inputs = [
       'name' => '',
       'title' => '',
       'folder_name' => '',
       'description' => '',
       'is_active' => true,
       'is_default' => false,
   ];

   public function mount($id = null)
   {
       if ($id) {
           $this->themeId = $id;
           $theme = Theme::findOrFail($id);
           $this->inputs = $theme->only(array_keys($this->inputs));
       }
   }

   protected function rules()
   {
       return [
           'inputs.name' => 'required|min:3|max:255|unique:themes,name,' . $this->themeId . ',theme_id',
           'inputs.title' => 'required|min:3|max:255',
           'inputs.folder_name' => 'required|min:3|max:255|unique:themes,folder_name,' . $this->themeId . ',theme_id',
           'inputs.description' => 'nullable|string',
           'temporaryImages.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
           'inputs.is_active' => 'boolean',
           'inputs.is_default' => 'boolean',
       ];
   }

   protected $messages = [
       'inputs.name.required' => 'Tema kodu alanı zorunludur.',
       'inputs.name.min' => 'Tema kodu en az 3 karakter olmalıdır.',
       'inputs.name.max' => 'Tema kodu 255 karakteri geçemez.',
       'inputs.name.unique' => 'Bu tema kodu zaten kullanılıyor.',
       'inputs.title.required' => 'Başlık alanı zorunludur.',
       'inputs.title.min' => 'Başlık en az 3 karakter olmalıdır.',
       'inputs.title.max' => 'Başlık 255 karakteri geçemez.',
       'inputs.folder_name.required' => 'Klasör adı alanı zorunludur.',
       'inputs.folder_name.min' => 'Klasör adı en az 3 karakter olmalıdır.',
       'inputs.folder_name.max' => 'Klasör adı 255 karakteri geçemez.',
       'inputs.folder_name.unique' => 'Bu klasör adı zaten kullanılıyor.',
       'temporaryImages.*.image' => 'Yüklenen dosya bir resim olmalıdır.',
       'temporaryImages.*.mimes' => 'Resim dosyası jpg, jpeg, png veya webp formatında olmalıdır.',
       'temporaryImages.*.max' => 'Resim boyutu en fazla 2MB olabilir.',
   ];

   public function save($redirect = false, $resetForm = false)
   {
      // folder_name'i name değeri ile aynı yap
      $this->inputs['folder_name'] = $this->inputs['name'];
      
      $this->validate();
      
      $data = $this->inputs;
      
      // Eğer bu tema varsayılan olarak ayarlanırsa, diğer temalar varsayılan olmaktan çıkarılmalı
      if ($data['is_default']) {
          Theme::where('is_default', true)->update(['is_default' => false]);
      }
   
      if ($this->themeId) {
          $theme = Theme::findOrFail($this->themeId);
          $currentData = collect($theme->toArray())->only(array_keys($data))->all();
          
          if ($data == $currentData && empty($this->temporaryImages)) {
              $toast = [
                  'title' => 'Bilgi',
                  'message' => 'Herhangi bir değişiklik yapılmadı.',
                  'type' => 'info'
              ];
          } else {
              $theme->update($data);
              $this->handleImageUpload($theme);
              
              log_activity($theme, 'güncellendi');
              
              $toast = [
                  'title' => 'Başarılı!',
                  'message' => 'Tema başarıyla güncellendi.',
                  'type' => 'success'
              ];
          }
      } else {
          $theme = Theme::create($data);
          $this->handleImageUpload($theme);
          
          log_activity($theme, 'oluşturuldu');
          
          $toast = [
              'title' => 'Başarılı!',
              'message' => 'Tema başarıyla oluşturuldu.',
              'type' => 'success'
          ];
      }
   
      if ($redirect) {
          session()->flash('toast', $toast);
          return redirect()->route('admin.thememanagement.index');
      }
   
      $this->dispatch('toast', $toast);
   
      if ($resetForm && !$this->themeId) {
          $this->reset();
      }
   }

   public function render()
   {
       return view('thememanagement::livewire.theme-management-manage-component', [
           'model' => $this->themeId ? Theme::find($this->themeId) : null
       ]);
   }
}