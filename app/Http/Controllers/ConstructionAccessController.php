<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConstructionAccessController extends Controller
{
    public function verify(Request $request)
    {
        $inputPassword = $request->input('construction_password');

        // Verify password hash (SHA-256)
        $correctPasswordHash = hash('sha256', 'nn');
        $inputPasswordHash = hash('sha256', $inputPassword);

        if ($inputPasswordHash === $correctPasswordHash) {
            // Password correct - grant access
            session(['construction_access_granted' => true]);
            return redirect('/');
        }

        // Password incorrect
        return redirect('/')->with('construction_error', 'Incorrect password');
    }
}
