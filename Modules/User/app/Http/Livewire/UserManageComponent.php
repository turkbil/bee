<?php

namespace Modules\User\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User; // Global User modelini kullan
use Illuminate\Support\Facades\Hash;

class UserManageComponent extends Component
{
    use WithFileUploads;

    // Input alanlarını tek bir yerde tanımla
    public $userId;
    public $inputs = [
        'name' => '',
        'email' => '',
        'password' => '',
        'is_active' => true,
    ];

    // Mount metodu
    public function mount($id = null)
    {
        if ($id) {
            $this->userId = $id;
            $user = User::findOrFail($id);
            $this->inputs = $user->only(array_keys($this->inputs)); // Sadece tanımlı inputları doldur
            $this->inputs['password'] = ''; // Şifre alanını boş bırak
        }
    }

    // Doğrulama kuralları
    protected function rules()
    {
        return [
            'inputs.name' => 'required|min:3|max:255',
            'inputs.email' => 'required|email|unique:users,email,' . $this->userId . ',id',
            'inputs.password' => $this->userId ? 'nullable|min:6' : 'required|min:6', // Şifre alanı
            'inputs.is_active' => 'boolean',
        ];
    }

    // Hata mesajları
    protected $messages = [
        'inputs.name.required' => 'İsim alanı zorunludur.',
        'inputs.name.min' => 'İsim en az 3 karakter olmalıdır.',
        'inputs.name.max' => 'İsim 255 karakteri geçemez.',
        'inputs.email.required' => 'E-posta alanı zorunludur.',
        'inputs.email.email' => 'Geçerli bir e-posta adresi girin.',
        'inputs.email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
        'inputs.password.required' => 'Şifre alanı zorunludur.',
        'inputs.password.min' => 'Şifre en az 6 karakter olmalıdır.',
    ];

    // Kaydetme işlemi
    public function save($redirect = false, $resetForm = false)
    {
        // Doğrulama kurallarını uygula
        $this->validate();
    
        // Şifreyi hashle
        if (!empty($this->inputs['password'])) {
            $this->inputs['password'] = Hash::make($this->inputs['password']);
        } else {
            unset($this->inputs['password']); // Şifre alanını kaldır
        }
    
        // Veriyi hazırla
        $data = $this->inputs;
        $data['tenant_id'] = tenant('id');
    
        // Kaydet veya güncelle
        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            $message = 'Kullanıcı başarıyla güncellendi.';
        } else {
            $user = User::create($data);
            $message = 'Kullanıcı başarıyla oluşturuldu.';
        }
    
        // Yönlendirme yapılacaksa flash mesajı ayarla
        if ($redirect) {
            session()->flash('toast', [
                'title' => 'Başarılı!',
                'message' => $message,
                'type' => 'success',
            ]);
            return redirect()->route('admin.user.index');
        }
    
        // Aynı sayfada kalınıyorsa toast mesajı göster
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $message,
            'type' => 'success',
        ]);
    
        // Eğer "Kaydet ve Yeni Ekle" butonu kullanıldıysa formu sıfırla
        if ($resetForm && !$this->userId) {
            $this->reset();
        }
    }

    // Render metodu
    public function render()
    {
        return view('user::livewire.user-manage-component')
            ->extends('admin.layout')
            ->section('content');
    }
}