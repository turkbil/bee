<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConstructionAccessController extends Controller
{
    public function verify(Request $request)
    {
        $inputPassword = $request->input('construction_password');
        $correctPassword = 'nn'; // layouts/app.blade.php ile aynı şifre

        if ($inputPassword === $correctPassword) {
            // Password correct - Set cookie (same logic as layouts/app.blade.php)
            $cookieName = 'mzb_auth_' . tenant('id');
            $cookieValue = md5($correctPassword . 'salt2024');

            // Set cookie for 30 days
            setcookie($cookieName, $cookieValue, time() + (86400 * 30), '/');

            return redirect('/');
        }

        // Password incorrect
        return back()->with('error', 'Şifre hatalı!');
    }
}
