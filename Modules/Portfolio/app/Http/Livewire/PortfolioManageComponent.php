<?php

namespace Modules\Portfolio\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Portfolio\App\Models\Portfolio;
use Illuminate\Support\Str;

class PortfolioManageComponent extends Component
{
    use WithFileUploads;

    // Input alanlarını tek bir yerde tanımla
    public $portfolioId;
    public $image; // Görsel dosyası
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
            $this->portfolioId = $id;
            $portfolio = Portfolio::findOrFail($id);
            $this->inputs = $portfolio->only(array_keys($this->inputs)); // Sadece tanımlı inputları doldur
        }
    }

    // Doğrulama kuralları
    protected function rules()
    {
        return [
            'inputs.title' => 'required|min:3|max:255',
            'inputs.slug' => 'nullable|unique:portfolios,slug,' . $this->portfolioId . ',portfolio_id',
            'inputs.metakey' => 'nullable|string|max:255',
            'inputs.metadesc' => 'nullable|string|max:255',
            'inputs.css' => 'nullable|string',
            'inputs.js' => 'nullable|string',
            'inputs.is_active' => 'boolean',
            'image' => 'nullable|image|max:1024', // Maksimum 1MB görsel boyutu
        ];
    }

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
        if ($this->portfolioId) {
            $portfolio = Portfolio::findOrFail($this->portfolioId);
            $portfolio->update($data);
            $message = 'Sayfa başarıyla güncellendi.';
        } else {
            $portfolio = Portfolio::create($data);
            $message = 'Sayfa başarıyla oluşturuldu.';
        }

        // Görsel yükleme
        if ($this->image) {
            $portfolio->addMedia($this->image->getRealPath())->toMediaCollection('images');
        }
    
        // Yönlendirme yapılacaksa flash mesajı ayarla
        if ($redirect) {
            session()->flash('toast', [
                'title' => 'Başarılı!',
                'message' => $message,
                'type' => 'success',
            ]);
            return redirect()->route('admin.portfolio.index');
        }
    
        // Aynı sayfada kalınıyorsa toast mesajı göster
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $message,
            'type' => 'success',
        ]);
    
        // Eğer "Kaydet ve Yeni Ekle" butonu kullanıldıysa formu sıfırla
        if ($resetForm && !$this->portfolioId) {
            $this->reset();
        }
    }

    // Render metodu
    public function render()
    {
        return view('portfolio::livewire.portfolio-manage-component')
            ->extends('admin.layout')
            ->section('content');
    }
}
