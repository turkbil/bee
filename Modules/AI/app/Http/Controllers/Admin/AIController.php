<?php
namespace Modules\AI\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AI\App\Services\AIService;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        return view('ai::admin.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'context' => 'nullable|string',
            'module' => 'nullable|string',
            'entity_id' => 'nullable|integer',
            'prompt_id' => 'nullable|exists:ai_prompts,id'
        ]);
        
        $options = [
            'context' => $request->context,
            'module' => $request->module,
            'entity_id' => $request->entity_id,
            'prompt_id' => $request->prompt_id
        ];
        
        $response = $this->aiService->ask($request->prompt, $options);
        
        if (!$response) {
            return response()->json([
                'success' => false,
                'message' => 'Yanıt alınamadı. Lütfen daha sonra tekrar deneyin veya yöneticinize başvurun.'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'response' => $response
        ]);
    }
}