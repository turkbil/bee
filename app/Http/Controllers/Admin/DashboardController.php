<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard');
    }
    
    /**
     * Dashboard widget sıralamasını kaydet
     */
    public function saveDashboardLayout(Request $request)
    {
        try {
            $layout = $request->input('layout');
            
            if (is_array($layout)) {
                // Session'a kaydet
                session(['dashboard_layout' => $layout]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Dashboard layout kaydedildi',
                    'layout' => $layout
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz layout verisi'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layout kaydetme hatası: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Dashboard widget sıralamasını getir
     */
    public function getDashboardLayout()
    {
        $user = Auth::user();
        $preferences = $user->dashboard_preferences ?? [];
        
        return response()->json([
            'success' => true,
            'layout' => $preferences['widget_layout'] ?? [],
            'updated_at' => $preferences['layout_updated_at'] ?? null
        ]);
    }
}