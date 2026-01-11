<?php

namespace Modules\UserManagement\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the avatar update form.
     */
    public function avatar(Request $request): View
    {
        $user = $request->user();
        
        // Debug bilgisi
        logger('Avatar sayfası yüklendi', [
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);
        
        return view('usermanagement::front.profile.avatar', [
            'user' => $user,
        ]);
    }
}