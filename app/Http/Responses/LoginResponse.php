<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // Pending favorite eklendikten sonra intended_url varsa oraya yönlendir
        if ($intendedUrl = session('intended_url')) {
            session()->forget('intended_url'); // Session'dan temizle
            return redirect($intendedUrl)->with('success', 'Giriş başarılı! Blog favorilerinize eklendi.');
        }

        // Default redirect (Laravel Fortify)
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended(config('fortify.home'));
    }
}
