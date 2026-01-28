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
        // Tema-aware register view
        $theme = app(\App\Services\ThemeService::class)->getActiveTheme();
        $themeName = $theme ? $theme->name : 'simple';
        $viewPath = "themes.{$themeName}.auth.register";

        // Fallback: Tema yoksa veya view yoksa default auth.register kullan
        if (!view()->exists($viewPath)) {
            $viewPath = 'auth.register';
        }

        return view($viewPath);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate with 'register' error bag
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'regex:/^(5)([0-9]{2})\s?([0-9]{3})\s?([0-9]{2})\s?([0-9]{2})$/', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
            'marketing_consent' => ['required', 'in:0,1'],
        ], [
            'terms.required' => 'Kullanım Koşulları ve Üyelik Sözleşmesi ile Aydınlatma Metni\'ni kabul etmelisiniz.',
            'terms.accepted' => 'Kullanım Koşulları ve Üyelik Sözleşmesi ile Aydınlatma Metni\'ni kabul etmelisiniz.',
            'marketing_consent.required' => 'Ticari elektronik ileti tercihinizi belirtmelisiniz.',
            'phone.required' => 'Telefon numarası zorunludur.',
            'phone.regex' => 'Geçerli bir telefon numarası giriniz (5XX XXX XX XX).',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'register')
                ->withInput();
        }

        // Kullanıcının IP adresini al
        $ipAddress = $request->ip();
        $acceptedAt = now();

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            // Kullanım Koşulları ve Üyelik Sözleşmesi (terms checkbox'ından)
            'terms_accepted' => true,
            'terms_accepted_at' => $acceptedAt,
            'terms_accepted_ip' => $ipAddress,
            // Üyelik ve Satın Alım Faaliyetleri Kapsamında Aydınlatma Metni (terms checkbox'ından)
            'privacy_accepted' => true,
            'privacy_accepted_at' => $acceptedAt,
            'privacy_accepted_ip' => $ipAddress,
            // Ticari Elektronik İleti Gönderimi (marketing_consent radio'sundan)
            'marketing_accepted' => (bool) $request->marketing_consent,
            'marketing_accepted_at' => $acceptedAt,
            'marketing_accepted_ip' => $ipAddress,
        ]);

        event(new Registered($user));

        // ✅ Email doğrulaması için login yap (ama verified middleware ile bloklanacak)
        Auth::login($user);

        // 🔐 DEVICE LIMIT - Session register (Tenant-aware, email verified olunca aktif olacak)
        if (tenant()) {
            try {
                $deviceService = app(\Modules\Muzibu\App\Services\DeviceService::class);
                $deviceService->registerSession($user);

                \Log::info('🔐 POST-REGISTER: Session registered', [
                    'user_id' => $user->id,
                    'session_id' => substr(session()->getId(), 0, 20) . '...',
                ]);
            } catch (\Exception $e) {
                \Log::error('🔐 POST-REGISTER: Device service failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Kayıt log'u
        activity()
            ->causedBy($user)
            ->inLog('User')
            ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
            ->tap(function ($activity) {
                $activity->event = 'kayıt oldu';
            })
            ->log("\"{$user->name}\" kayıt oldu");

        // Trial subscription başlat (auth_subscription açıksa ve trial plan varsa)
        // NOT: Trial subscription email doğrulandıktan sonra aktif olacak
        if (setting('auth_subscription')) {
            $subscriptionService = app(\Modules\Subscription\App\Services\SubscriptionService::class);
            $subscriptionService->createTrialForUser($user);
        }

        // Email doğrulama sayfasına yönlendir
        return redirect()->route('verification.notice');
    }
}
