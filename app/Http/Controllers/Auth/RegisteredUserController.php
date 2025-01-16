<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Geçerli tenant bilgisi alınır
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            // Tenant bulunamadığında hata mesajı göster
            return redirect()->back()->withErrors(['tenant' => 'Tenant bilgisi bulunamadı. Lütfen sistem yöneticinize başvurun.']);
        }

        $tenantId = $tenant->id; // Tenant ID alınır

        // Verileri doğrula
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                // Email ve tenant_id kombinasyonunu kontrol et
                function ($attribute, $value, $fail) use ($tenantId) {
                    if (User::where('email', $value)->where('tenant_id', $tenantId)->exists()) {
                        $fail('Bu e-posta adresi zaten kayıtlı.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Kullanıcı oluştur
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'tenant_id' => $tenantId, // tenant_id alanını ekle
            ]);

            // Kullanıcı kaydı event'ini tetikle
            event(new Registered($user));

            // Kullanıcıyı oturum açtır
            Auth::login($user);

            // Dashboard'a yönlendir
            return redirect(route('dashboard'));
        } catch (\Exception $e) {
            // Hata durumunda kullanıcıya bilgi ver
            return redirect()->back()->withErrors(['error' => 'Kayıt sırasında bir hata oluştu: ' . $e->getMessage()]);
        }
    }
}
