<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
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

        return back()->with('status', 'password-updated');
    }
}
