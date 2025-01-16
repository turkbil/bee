<?php

namespace Modules\Page\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Str;

class PageManageComponent extends Component
{
    use WithFileUploads;

    // Input alanlarını tek bir yerde tanımla
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
    ];

    // Mount metodu
    public function mount($id = null)
    {
        if ($id) {
            $this->pageId = $id;
            $page = Page::findOrFail($id);
            $this->inputs = $page->only(array_keys($this->inputs)); // Sadece tanımlı inputları doldur
        }
    }

    // Doğrulama kuralları
    protected function rules()
    {
        return [
            'inputs.title' => 'required|min:3|max:255',
            'inputs.slug' => 'nullable|unique:pages,slug,' . $this->pageId . ',page_id',
            'inputs.metakey' => 'nullable|string|max:255',
            'inputs.metadesc' => 'nullable|string|max:255',
            'inputs.css' => 'nullable|string',
            'inputs.js' => 'nullable|string',
            'inputs.is_active' => 'boolean',
        ];
    }

    // Hata mesajları
    protected $messages = [
        'inputs.title.required' => 'Başlık alanı zorunludur.',
        'inputs.title.min' => 'Başlık en az 3 karakter olmalıdır.',
        'inputs.title.max' => 'Başlık 255 karakteri geçemez.',
    ];

    // Kaydetme işlemi
    public function save($redirect = false, $resetForm = false)
    {
        // Doğrulama kurallarını uygula
        $this->validate();
    
        // Slug otomatik oluşturma
        if (empty($this->inputs['slug'])) {
            $this->inputs['slug'] = Str::slug($this->inputs['title']);
        }
    
        // Meta alanlarını otomatik doldur
        if (empty($this->inputs['metakey'])) {
            $this->inputs['metakey'] = Str::limit($this->inputs['title'], 255);
        }
    
        if (empty($this->inputs['metadesc'])) {
            $this->inputs['metadesc'] = Str::limit($this->inputs['body'], 255);
        }
    
        // Veriyi hazırla
        $data = $this->inputs;
        $data['tenant_id'] = tenant('id');
    
        // Kaydet veya güncelle
        if ($this->pageId) {
            $page = Page::findOrFail($this->pageId);
            $page->update($data);
            $message = 'Sayfa başarıyla güncellendi.';
        } else {
            $page = Page::create($data);
            $message = 'Sayfa başarıyla oluşturuldu.';
        }
    
        // Yönlendirme yapılacaksa flash mesajı ayarla
        if ($redirect) {
            session()->flash('toast', [
                'title' => 'Başarılı!',
                'message' => $message,
                'type' => 'success',
            ]);
            return redirect()->route('admin.page.index');
        }
    
        // Aynı sayfada kalınıyorsa toast mesajı göster
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $message,
            'type' => 'success',
        ]);
    
        // Eğer "Kaydet ve Yeni Ekle" butonu kullanıldıysa formu sıfırla
        if ($resetForm && !$this->pageId) {
            $this->reset();
        }
    }

    // Render metodu
    public function render()
    {
        return view('page::livewire.page-manage-component')
            ->extends('admin.layout')
            ->section('content');
    }
}