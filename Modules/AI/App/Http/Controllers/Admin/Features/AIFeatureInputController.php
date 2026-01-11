<?php

namespace Modules\AI\App\Http\Controllers\Admin\Features;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureInput;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Services\FormBuilder\UniversalInputManager;
use Modules\AI\App\Services\FormBuilder\PromptMapper;
use Modules\AI\App\Services\UniversalInputAIService;

class AIFeatureInputController extends Controller
{
    public function __construct(
        private UniversalInputManager $inputManager,
        private PromptMapper $promptMapper,
        private UniversalInputAIService $aiService
    ) {}
    
    /**
     * Feature input yönetim sayfası
     */
    public function manage($featureId)
    {
        $feature = AIFeature::with(['inputs.options', 'inputs.group'])->findOrFail($featureId);
        $availablePrompts = Prompt::orderBy('priority')->get();
        
        return view('ai::admin.features.inputs.manage', compact('feature', 'availablePrompts'));
    }
    
    /**
     * Yeni input ekle
     */
    public function store(Request $request, $featureId)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:50',
            'type' => 'required|in:textarea,text,select,radio,checkbox,range,number',
            'name' => 'required|string|max:255',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'is_primary' => 'boolean',
            'group_id' => 'nullable|integer|exists:ai_input_groups,id',
            'sort_order' => 'integer|min:0',
            'is_required' => 'boolean',
            'validation_rules' => 'nullable|json',
            'default_value' => 'nullable|string',
            'conditional_logic' => 'nullable|json'
        ]);
        
        // Unique kontrol
        $exists = AIFeatureInput::where('feature_id', $featureId)
            ->where('slug', $validated['slug'])
            ->exists();
            
        if ($exists) {
            return back()->withErrors(['slug' => 'Bu slug zaten kullanılıyor.']);
        }
        
        $validated['feature_id'] = $featureId;
        
        $input = AIFeatureInput::create($validated);
        
        // Cache'i temizle
        $this->inputManager->clearFormCache($featureId);
        
        return redirect()->route('ai.features.inputs.manage', $featureId)
            ->with('success', 'Input başarıyla eklendi.');
    }
    
    /**
     * Input güncelle
     */
    public function update(Request $request, $featureId, $inputId)
    {
        $input = AIFeatureInput::where('feature_id', $featureId)
            ->findOrFail($inputId);
            
        $validated = $request->validate([
            'slug' => 'required|string|max:50',
            'type' => 'required|in:textarea,text,select,radio,checkbox,range,number',
            'name' => 'required|string|max:255',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'is_primary' => 'boolean',
            'group_id' => 'nullable|integer|exists:ai_input_groups,id',
            'sort_order' => 'integer|min:0',
            'is_required' => 'boolean',
            'validation_rules' => 'nullable|json',
            'default_value' => 'nullable|string',
            'conditional_logic' => 'nullable|json'
        ]);
        
        // Slug değişiyorsa unique kontrol
        if ($input->slug !== $validated['slug']) {
            $exists = AIFeatureInput::where('feature_id', $featureId)
                ->where('slug', $validated['slug'])
                ->where('id', '!=', $inputId)
                ->exists();
                
            if ($exists) {
                return back()->withErrors(['slug' => 'Bu slug zaten kullanılıyor.']);
            }
        }
        
        $input->update($validated);
        
        // Cache'i temizle
        $this->inputManager->clearFormCache($featureId);
        
        return redirect()->route('ai.features.inputs.manage', $featureId)
            ->with('success', 'Input başarıyla güncellendi.');
    }
    
    /**
     * Input sil
     */
    public function destroy($featureId, $inputId)
    {
        $input = AIFeatureInput::where('feature_id', $featureId)
            ->findOrFail($inputId);
            
        $input->delete();
        
        // Cache'i temizle
        $this->inputManager->clearFormCache($featureId);
        
        return redirect()->route('ai.features.inputs.manage', $featureId)
            ->with('success', 'Input başarıyla silindi.');
    }
    
    /**
     * API: Form yapısını getir
     */
    public function getFormStructure($featureId): JsonResponse
    {
        try {
            $structure = $this->inputManager->getFormStructure($featureId);
            
            return response()->json([
                'success' => true,
                'data' => $structure
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form yapısı alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Input'ları validate et
     */
    public function validateInputs(Request $request, $featureId): JsonResponse
    {
        try {
            $userInputs = $request->all();
            $errors = $this->inputManager->validateInputs($userInputs, $featureId);
            
            if (empty($errors)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Validation başarılı.',
                    'data' => $userInputs
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validation hataları var.',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation işlemi başarısız.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Form verilerini işle ve prompt oluştur
     */
    public function processForm(Request $request, $featureId): JsonResponse
    {
        try {
            $userInputs = $request->all();
            
            // Önce validate et
            $errors = $this->inputManager->validateInputs($userInputs, $featureId);
            
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation hataları var.',
                    'errors' => $errors
                ], 422);
            }
            
            // Prompt oluştur
            $finalPrompt = $this->promptMapper->buildFinalPrompt($featureId, $userInputs);
            
            // Prompt önizlemesi
            $preview = $this->promptMapper->generatePromptPreview($featureId, $userInputs);
            
            // Prompt gücü hesapla
            $power = $this->promptMapper->calculatePromptPower($featureId, $userInputs);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'final_prompt' => $finalPrompt,
                    'preview' => $preview,
                    'power' => $power,
                    'ready_for_ai' => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form işleme başarısız.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Smart default değerleri getir
     */
    public function getSmartDefaults(Request $request, $featureId): JsonResponse
    {
        try {
            $context = $request->get('context', []);
            $defaults = $this->inputManager->getSmartDefaults($featureId, $context);
            
            return response()->json([
                'success' => true,
                'data' => $defaults
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Smart defaults alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Input sıralamasını güncelle
     */
    public function updateOrder(Request $request, $featureId): JsonResponse
    {
        try {
            $orders = $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|integer',
                'orders.*.sort_order' => 'required|integer|min:0'
            ]);
            
            foreach ($orders['orders'] as $order) {
                AIFeatureInput::where('feature_id', $featureId)
                    ->where('id', $order['id'])
                    ->update(['sort_order' => $order['sort_order']]);
            }
            
            // Cache'i temizle
            $this->inputManager->clearFormCache($featureId);
            
            return response()->json([
                'success' => true,
                'message' => 'Sıralama güncellendi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncellenemedi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Input'u kopyala
     */
    public function duplicate(Request $request, $featureId, $inputId): JsonResponse
    {
        try {
            $input = AIFeatureInput::where('feature_id', $featureId)
                ->findOrFail($inputId);
                
            $newSlug = $input->slug . '_copy';
            $counter = 1;
            
            // Unique slug oluştur
            while (AIFeatureInput::where('feature_id', $featureId)
                      ->where('slug', $newSlug)
                      ->exists()) {
                $newSlug = $input->slug . '_copy_' . $counter;
                $counter++;
            }
            
            $newInput = $input->replicate();
            $newInput->slug = $newSlug;
            $newInput->name = $input->name . ' (Kopya)';
            $newInput->is_primary = false; // Kopya primary olamaz
            $newInput->sort_order = $input->sort_order + 1;
            $newInput->save();
            
            // Seçenekleri de kopyala
            foreach ($input->options as $option) {
                $newOption = $option->replicate();
                $newOption->input_id = $newInput->id;
                $newOption->save();
            }
            
            // Cache'i temizle
            $this->inputManager->clearFormCache($featureId);
            
            return response()->json([
                'success' => true,
                'message' => 'Input başarıyla kopyalandı.',
                'data' => $newInput->load('options')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input kopyalanamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Universal Input System ile AI çağrısı - MAIN ENDPOINT
     */
    public function executeAI(Request $request, $featureId): JsonResponse
    {
        try {
            // Request data'yı al
            $userInputs = $request->input('inputs', []);
            $options = $request->input('options', []);
            
            // User context ekle
            if (auth()->check()) {
                $options['user_id'] = auth()->id();
            }
            
            // Tenant context ekle
            $options['tenant_context'] = true;
            
            // AI Service ile işle
            $result = $this->aiService->processFormRequest($featureId, $userInputs, $options);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error_type' => 'controller',
                'message' => 'Request processing failed.',
                'error_details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API: Cache temizle
     */
    public function clearCache($featureId): JsonResponse
    {
        try {
            $this->inputManager->clearFormCache($featureId);
            
            return response()->json([
                'success' => true,
                'message' => 'Cache temizlendi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizlenemedi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}