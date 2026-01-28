<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use App\Services\ThemeService;

class ProfileController extends Controller
{
    /**
     * ThemeService instance
     */
    protected ThemeService $themeService;

    /**
     * Constructor
     */
    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Get theme-aware view path using ThemeService
     */
    protected function getThemeView(string $view): string
    {
        // ThemeService ile tema-aware view çözümle
        $theme = $this->themeService->getActiveTheme();
        $themeName = $theme ? $theme->name : 'simple';

        // 1. Tema view kontrolü
        $themeView = "themes.{$themeName}.profile.{$view}";
        if (view()->exists($themeView)) {
            return $themeView;
        }

        // 2. Simple tema fallback
        if ($themeName !== 'simple') {
            $simpleView = "themes.simple.profile.{$view}";
            if (view()->exists($simpleView)) {
                return $simpleView;
            }
        }

        // 3. Global fallback
        return "profile.{$view}";
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view($this->getThemeView('edit'), [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $user = $request->user();
        $user->save();

        // Profil güncelleme log'u
        activity()
            ->causedBy($user)
            ->inLog('User')
            ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
            ->tap(function ($activity) {
                $activity->event = 'profil güncellendi';
            })
            ->log("\"{$user->name}\" profil güncellendi");

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Display the password change form.
     */
    public function password(Request $request): View
    {
        return view($this->getThemeView('password'), [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the avatar management form.
     */
    public function avatar(Request $request)
    {
        $response = response()->view($this->getThemeView('avatar'), [
            'user' => $request->user(),
        ]);

        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                       ->header('Pragma', 'no-cache')
                       ->header('Expires', '0');
    }

    /**
     * Display the delete account form.
     */
    public function delete(Request $request): View
    {
        return view($this->getThemeView('delete'), [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the KVKK consents form.
     */
    public function consents(Request $request): View
    {
        return view($this->getThemeView('consents'), [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update KVKK consents.
     */
    public function updateConsents(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'marketing_accepted' => ['required', 'boolean'],
        ]);

        $user = $request->user();

        $user->update([
            'marketing_accepted' => $validated['marketing_accepted'],
            'marketing_accepted_at' => now(),
            'marketing_accepted_ip' => $request->ip(),
        ]);

        // KVKK onayı güncelleme log'u
        activity()
            ->causedBy($user)
            ->inLog('User')
            ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
            ->tap(function ($activity) {
                $activity->event = 'KVKK onayları güncellendi';
            })
            ->log("\"{$user->name}\" KVKK onayları güncellendi");

        return Redirect::route('profile.consents')->with('status', 'consents-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Şifre değiştirme log'u
        activity()
            ->causedBy($user)
            ->inLog('User')
            ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
            ->tap(function ($activity) {
                $activity->event = 'şifre değiştirildi';
            })
            ->log("\"{$user->name}\" şifre değiştirildi");

        return Redirect::route('profile.password')->with('status', 'password-updated');
    }

    /**
     * Upload avatar - AJAX için
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $user = $request->user();
            
            // Eski avatarı sil
            $user->clearMediaCollection('avatar');
            
            // Yeni avatarı kaydet
            $fileName = Str::slug($user->name) . '-' . uniqid() . '.' . $request->file('avatar')->extension();
            
            $media = $user->addMedia($request->file('avatar')->getRealPath())
                ->usingFileName($fileName)
                ->toMediaCollection('avatar', 'public');
            
            // Avatar yükleme log'u
            activity()
                ->causedBy($user)
                ->inLog('User')
                ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
                ->tap(function ($activity) {
                    $activity->event = 'avatar yüklendi';
                })
                ->log("\"{$user->name}\" avatar yüklendi");
            
            // Cache temizle - daha agresif cache temizleme
            cache()->forget('user_avatar_' . $user->id);
            cache()->flush(); // Tüm cache'i temizle
            
            // Opcache'i de temizle
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            // AJAX response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Avatar başarıyla yüklendi!',
                    'avatar_url' => $media->getUrl() . '?v=' . time()
                ]);
            }
            
            return Redirect::route('profile.avatar')->with('message', 'Avatar başarıyla yüklendi!');
            
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bir hata oluştu: ' . $e->getMessage()
                ], 400);
            }
            
            return Redirect::route('profile.avatar')->with('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove avatar - AJAX için
     */
    public function removeAvatar(Request $request)
    {
        try {
            $user = $request->user();
            $user->clearMediaCollection('avatar');
            
            // Avatar silme log'u
            activity()
                ->causedBy($user)
                ->inLog('User')
                ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
                ->tap(function ($activity) {
                    $activity->event = 'avatar silindi';
                })
                ->log("\"{$user->name}\" avatar silindi");
            
            // Cache temizle - daha agresif cache temizleme
            cache()->forget('user_avatar_' . $user->id);
            cache()->flush(); // Tüm cache'i temizle
            
            // Opcache'i de temizle
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            // AJAX response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Avatar başarıyla silindi!'
                ]);
            }
            
            return Redirect::route('profile.avatar')->with('message', 'Avatar başarıyla silindi!');
            
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bir hata oluştu: ' . $e->getMessage()
                ], 400);
            }
            
            return Redirect::route('profile.avatar')->with('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Hesap silme log'u
        activity()
            ->causedBy($user)
            ->inLog('User')
            ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
            ->tap(function ($activity) {
                $activity->event = 'hesap silindi';
            })
            ->log("\"{$user->name}\" hesap silindi");

        Auth::logout();

        // Soft delete - kullanıcı silinir ama veritabanında kalır
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

}
