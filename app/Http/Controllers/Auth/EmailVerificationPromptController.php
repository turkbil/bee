<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/');
        }

        // Tema-aware verify-email view
        $theme = app(\App\Services\ThemeService::class)->getActiveTheme();
        $themeName = $theme ? $theme->name : 'simple';
        $viewPath = "themes.{$themeName}.auth.verify-email";

        // Fallback: Tema yoksa veya view yoksa default auth.verify-email kullan
        if (!view()->exists($viewPath)) {
            $viewPath = 'auth.verify-email';
        }

        return view($viewPath);
    }
}
