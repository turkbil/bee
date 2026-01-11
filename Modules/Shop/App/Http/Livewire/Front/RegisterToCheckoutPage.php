<?php

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use App\Models\User;
use Modules\Shop\App\Models\ShopCustomer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterToCheckoutPage extends Component
{
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        // Zaten login ise direkt checkout'a yönlendir
        if (Auth::check()) {
            return redirect()->route('shop.checkout');
        }
    }

    public function register()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
        ], [
            'first_name.required' => 'Ad zorunludur',
            'last_name.required' => 'Soyad zorunludur',
            'email.required' => 'Email zorunludur',
            'email.email' => 'Geçerli bir email adresi giriniz',
            'email.unique' => 'Bu email adresi zaten kayıtlı. Lütfen giriş yapın.',
            'phone.required' => 'Telefon zorunludur',
            'password.required' => 'Şifre zorunludur',
            'password.min' => 'Şifre en az 8 karakter olmalıdır',
            'password.confirmed' => 'Şifreler eşleşmiyor',
        ]);

        try {
            // User oluştur
            $user = User::create([
                'name' => $this->first_name . ' ' . $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            // Customer oluştur
            $customer = ShopCustomer::create([
                'user_id' => $user->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'customer_type' => 'individual',
                'billing_type' => 'individual',
            ]);

            // Otomatik login
            Auth::login($user);

            session()->flash('success', 'Hesabınız oluşturuldu! Şimdi sipariş verebilirsiniz.');

            // Checkout sayfasına yönlendir
            return redirect()->route('shop.checkout');

        } catch (\Exception $e) {
            session()->flash('error', 'Hesap oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Layout: Tenant temasından (header/footer için)
        // View: Module default (içerik fallback'ten)
        $theme = tenant()->theme ?? 'simple';
        $layoutPath = "themes.{$theme}.layouts.app";

        // Tenant layout yoksa simple fallback
        if (!view()->exists($layoutPath)) {
            $layoutPath = 'themes.simple.layouts.app';
        }

        return view('shop::livewire.front.register-to-checkout')
            ->layout($layoutPath);
    }
}
