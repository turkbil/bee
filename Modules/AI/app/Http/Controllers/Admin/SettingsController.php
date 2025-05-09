<?php
namespace Modules\AI\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\DeepSeekService;

class SettingsController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string',
            'model' => 'required|string',
            'max_tokens' => 'required|integer|min:1|max:8000',
            'temperature' => 'required|numeric|min:0|max:1',
            'enabled' => 'boolean',
        ]);
        
        $settings = $this->aiService->updateSettings($request->all());
        
        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Ayarlar güncellenirken bir hata oluştu.'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Ayarlar başarıyla güncellendi.'
        ]);
    }
    
    public function testConnection(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
        ]);
        
        try {
            $deepSeekService = new DeepSeekService();
            $deepSeekService->setApiKey($request->api_key);
            
            $connectionSuccess = $deepSeekService->testConnection();
            
            return response()->json([
                'success' => $connectionSuccess,
                'message' => $connectionSuccess 
                    ? 'API bağlantısı başarılı!' 
                    : 'API bağlantısı başarısız. Lütfen API anahtarını kontrol edin.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bağlantı testi sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}