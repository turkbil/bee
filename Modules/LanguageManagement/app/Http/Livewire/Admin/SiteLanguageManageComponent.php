<?php

namespace Modules\LanguageManagement\app\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\LanguageManagement\app\Services\SiteLanguageService;
use Modules\LanguageManagement\app\Models\SiteLanguage;

#[Layout('admin.layout')]
class SiteLanguageManageComponent extends Component
{
    public $languageId;
    public $code = '';
    public $name = '';
    public $native_name = '';
    public $direction = 'ltr';
    public $flag_icon = '';
    public $is_active = true;
    public $is_default = false;

    public $isEditing = false;

    protected $rules = [
        'code' => 'required|string|max:10|alpha',
        'name' => 'required|string|max:255',
        'native_name' => 'required|string|max:255',
        'direction' => 'required|in:ltr,rtl',
        'flag_icon' => 'nullable|string|max:10',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected $messages = [
        'code.required' => 'Dil kodu gereklidir',
        'code.alpha' => 'Dil kodu sadece harf içerebilir',
        'code.max' => 'Dil kodu maksimum 10 karakter olabilir',
        'name.required' => 'İngilizce adı gereklidir',
        'native_name.required' => 'Yerel adı gereklidir',
        'direction.required' => 'Metin yönü seçilmelidir',
        'direction.in' => 'Geçerli bir metin yönü seçin',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->languageId = $id;
            $this->loadLanguage();
        }
    }

    public function loadLanguage()
    {
        $language = SiteLanguage::find($this->languageId);
        
        if (!$language) {
            session()->flash('error', 'Site dili bulunamadı.');
            return redirect()->route('admin.languagemanagement.site.index');
        }

        $this->isEditing = true;
        $this->code = $language->code;
        $this->name = $language->name;
        $this->native_name = $language->native_name;
        $this->direction = $language->direction;
        $this->flag_icon = $language->flag_icon;
        $this->is_active = $language->is_active;
        $this->is_default = $language->is_default;
    }

    public function save($redirect = true, $addNew = false)
    {
        $this->validate();

        try {
            $siteLanguageService = app(SiteLanguageService::class);
            
            // Kod benzersizlik kontrolü (düzenleme hariç)
            if (!$this->isEditing) {
                $existing = SiteLanguage::where('code', $this->code)->first();
                if ($existing) {
                    $this->addError('code', 'Bu dil kodu zaten kullanılıyor.');
                    return;
                }
            } elseif ($this->isEditing) {
                $existing = SiteLanguage::where('code', $this->code)
                    ->where('id', '!=', $this->languageId)
                    ->first();
                if ($existing) {
                    $this->addError('code', 'Bu dil kodu zaten kullanılıyor.');
                    return;
                }
            }

            $data = [
                'code' => strtolower($this->code),
                'name' => $this->name,
                'native_name' => $this->native_name,
                'direction' => $this->direction,
                'flag_icon' => $this->flag_icon ?: null,
                'is_active' => $this->is_active,
                'is_default' => $this->is_default,
                'sort_order' => $this->isEditing ? 
                    SiteLanguage::find($this->languageId)->sort_order : 
                    (SiteLanguage::max('sort_order') + 1),
            ];

            if ($this->isEditing) {
                $language = SiteLanguage::find($this->languageId);
                $language->update($data);
                $message = 'Site dili başarıyla güncellendi.';
                $logMessage = 'güncellendi';
            } else {
                $language = $siteLanguageService->createOrUpdateSiteLanguage($data);
                $message = 'Site dili başarıyla oluşturuldu.';
                $logMessage = 'oluşturuldu';
            }

            if (function_exists('log_activity')) {
                log_activity($language, $logMessage);
            }

            session()->flash('message', $message);
            
            if ($redirect) {
                if ($addNew) {
                    return redirect()->route('admin.languagemanagement.site.manage');
                } else {
                    return redirect()->route('admin.languagemanagement.site.index');
                }
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('languagemanagement::admin.livewire.site-language-manage-component');
    }
}