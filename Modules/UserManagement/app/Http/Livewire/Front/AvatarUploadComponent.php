<?php

namespace Modules\UserManagement\App\Http\Livewire\Front;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class AvatarUploadComponent extends Component
{
    use WithFileUploads;

    public $user;
    public $avatar;

    protected $rules = [
        'avatar' => 'required|image|max:2048',
    ];

    protected $messages = [
        'avatar.required' => 'Lütfen bir dosya seçin.',
        'avatar.image' => 'Sadece resim dosyaları yükleyebilirsiniz.',
        'avatar.max' => 'Dosya boyutu maksimum 2MB olabilir.',
    ];

    public function mount($user)
    {
        $this->user = $user;
    }

    public function updatedAvatar()
    {
        $this->validate();

        try {
            // Test için 2 saniye bekle (loading görmek için)
            sleep(2);
            
            // Eski avatarı sil
            $this->user->clearMediaCollection('avatar');
            
            // Yeni avatarı kaydet
            $fileName = Str::slug($this->user->name) . '-' . uniqid() . '.' . $this->avatar->extension();
            
            $this->user->addMedia($this->avatar->getRealPath())
                ->usingFileName($fileName)
                ->toMediaCollection('avatar', 'public');
            
            session()->flash('message', 'Avatar başarıyla yüklendi!');
            
            return redirect()->route('profile.avatar');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function removeAvatar()
    {
        try {
            // Test için 1 saniye bekle (loading görmek için)
            sleep(1);
            
            $this->user->clearMediaCollection('avatar');
            
            session()->flash('message', 'Avatar başarıyla silindi!');
            
            return redirect()->route('profile.avatar');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('usermanagement::front.livewire.avatar-upload-component');
    }
}