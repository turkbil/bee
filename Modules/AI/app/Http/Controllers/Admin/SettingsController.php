<?php
namespace Modules\AI\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\DeepSeekService;
use Modules\AI\App\Models\Setting;
use Modules\AI\App\Models\Prompt;

class SettingsController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }
    
    public function api()
    {
        $settings = Setting::first() ?: new Setting();
        return view('ai::admin.settings.api', compact('settings'));
    }
    
    public function limits()
    {
        $settings = Setting::first() ?: new Setting();
        return view('ai::admin.settings.limits', compact('settings'));
    }
    
    public function prompts()
    {
        $prompts = Prompt::orderBy('is_system', 'desc')
            ->orderBy('is_default', 'desc')
            ->orderBy('is_common', 'desc')
            ->orderBy('name')
            ->get();
            
        $commonPrompt = Prompt::where('is_common', true)->first();
        
        return view('ai::admin.settings.prompts', compact('prompts', 'commonPrompt'));
    }
    
    public function general()
    {
        $settings = Setting::first() ?: new Setting();
        return view('ai::admin.settings.general', compact('settings'));
    }
    
    public function managePrompt($id = null)
    {
        $prompt = null;
        
        if ($id) {
            $prompt = Prompt::findOrFail($id);
        }
        
        return view('ai::admin.settings.prompts-manage', compact('prompt'));
    }
    
    public function updateApi(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string',
            'model' => 'required|string',
            'max_tokens' => 'required|integer|min:1',
            'temperature' => 'required|numeric|min:0',
            'enabled' => 'boolean',
        ]);
        
        $settings = Setting::first() ?: new Setting();
        $settings->fill($request->all());
        $settings->enabled = $request->boolean('enabled');
        $settings->save();
        
        return redirect()->back()->with('success', 'API ayarları güncellendi');
    }
    
    public function updateLimits(Request $request)
    {
        $request->validate([
            'max_question_length' => 'required|integer|min:1',
            'max_daily_questions' => 'required|integer|min:0',
            'max_monthly_questions' => 'required|integer|min:0',
            'question_token_limit' => 'required|integer|min:1',
            'free_question_tokens_daily' => 'required|integer|min:0',
            'charge_question_tokens' => 'boolean',
        ]);
        
        $settings = Setting::first() ?: new Setting();
        $settings->fill($request->all());
        $settings->charge_question_tokens = $request->boolean('charge_question_tokens');
        $settings->save();
        
        return redirect()->back()->with('success', 'Limit ayarları güncellendi');
    }
    
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'default_language' => 'required|string|in:tr,en',
            'response_format' => 'required|string|in:markdown,plain,html',
            'cache_duration' => 'required|integer|min:0',
            'concurrent_requests' => 'required|integer|min:1',
            'content_filtering' => 'boolean',
            'rate_limiting' => 'boolean',
            'detailed_logging' => 'boolean',
            'performance_monitoring' => 'boolean',
        ]);
        
        $settings = Setting::first() ?: new Setting();
        $settings->fill($request->all());
        $settings->content_filtering = $request->boolean('content_filtering');
        $settings->rate_limiting = $request->boolean('rate_limiting');
        $settings->detailed_logging = $request->boolean('detailed_logging');
        $settings->performance_monitoring = $request->boolean('performance_monitoring');
        $settings->save();
        
        return redirect()->back()->with('success', 'Genel ayarlar güncellendi');
    }
    
    public function storePrompt(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);
        
        $prompt = new Prompt();
        $prompt->name = $request->name;
        $prompt->content = $request->content;
        $prompt->is_active = $request->boolean('is_active', true);
        $prompt->is_default = $request->boolean('is_default', false);
        
        // Eğer varsayılan yapılacaksa diğerlerini kaldır
        if ($prompt->is_default) {
            Prompt::where('is_default', true)->update(['is_default' => false]);
        }
        
        $prompt->save();
        
        return redirect()->route('admin.ai.settings.prompts.manage', $prompt->id)->with('success', 'Prompt başarıyla eklendi');
    }
    
    public function updatePrompt(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'is_default' => 'boolean',
        ]);
        
        $prompt = Prompt::findOrFail($id);
        
        // Gizli sistem promptları düzenlenebilir, diğer sistem promptları düzenlenemez
        if ($prompt->is_system && !$prompt->is_common && !in_array($prompt->prompt_type, ['hidden_system', 'secret_knowledge', 'conditional'])) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem promptu düzenlenemez'
            ], 403);
        }
        
        $prompt->name = $request->name;
        $prompt->content = $request->content;
        $prompt->is_default = $request->boolean('is_default', false);
        
        // Eğer varsayılan yapılacaksa diğerlerini kaldır
        if ($prompt->is_default) {
            Prompt::where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
        }
        
        $prompt->save();
        
        return redirect()->route('admin.ai.settings.prompts.manage', $prompt->id)->with('success', 'Prompt başarıyla güncellendi');
    }
    
    public function getPrompt($id)
    {
        $prompt = Prompt::findOrFail($id);
        return response()->json($prompt);
    }
    
    public function deletePrompt($id)
    {
        $prompt = Prompt::findOrFail($id);
        
        // Sistem promptları ve ortak özellikler silinemez
        if ($prompt->is_system || $prompt->is_common || in_array($prompt->prompt_type, ['hidden_system', 'secret_knowledge', 'conditional', 'common'])) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem promptu veya ortak özellikler silinemez'
            ], 403);
        }
        
        $prompt->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Prompt başarıyla silindi'
        ]);
    }
    
    public function makeDefaultPrompt($id)
    {
        $prompt = Prompt::findOrFail($id);
        
        if ($prompt->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem promptu varsayılan yapılamaz'
            ], 403);
        }
        
        // Diğer varsayılanları kaldır
        Prompt::where('is_default', true)->update(['is_default' => false]);
        
        $prompt->is_default = true;
        $prompt->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Prompt varsayılan olarak ayarlandı'
        ]);
    }
    
    public function updateCommonPrompt(Request $request)
    {
        $request->validate([
            'common_content' => 'required|string',
        ]);
        
        $prompt = Prompt::where('is_common', true)->first();
        
        if (!$prompt) {
            $prompt = new Prompt();
            $prompt->name = 'Ortak Özellikler';
            $prompt->is_common = true;
            $prompt->is_system = true;
            $prompt->is_active = true;
        }
        
        $prompt->content = $request->common_content;
        $prompt->save();
        
        return redirect()->back()->with('success', 'Ortak özellikler güncellendi');
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string',
            'model' => 'required|string',
            'max_tokens' => 'required|integer|min:1',
            'temperature' => 'required|numeric|min:0',
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
    
    public function features()
    {
        return view('ai::admin.features.dashboard');
    }
}