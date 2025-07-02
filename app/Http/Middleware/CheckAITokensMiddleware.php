<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AITokenService;
use Symfony\Component\HttpFoundation\Response;

class CheckAITokensMiddleware
{
    /**
     * The AI token service instance.
     */
    protected AITokenService $aiTokenService;

    /**
     * Create a new middleware instance.
     */
    public function __construct(AITokenService $aiTokenService)
    {
        $this->aiTokenService = $aiTokenService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $tokensRequired = 1): Response
    {
        // Check if we're in a tenant context
        if (!tenant()) {
            return response()->json([
                'error' => 'AI kullanımı için tenant context gereklidir.'
            ], 400);
        }

        $tenant = tenant();

        // Check if AI is enabled for this tenant
        if (!$tenant->ai_enabled) {
            return response()->json([
                'error' => 'AI kullanımı bu hesap için etkinleştirilmemiş.',
                'code' => 'AI_DISABLED'
            ], 403);
        }

        // Check if tenant has enough tokens
        if (!$this->aiTokenService->canUseTokens($tenant, $tokensRequired)) {
            $remainingTokens = $tenant->remaining_monthly_tokens;
            
            if ($tenant->isMonthlyLimitExceeded()) {
                return response()->json([
                    'error' => 'Aylık AI kullanım limitiniz dolmuş.',
                    'code' => 'MONTHLY_LIMIT_EXCEEDED',
                    'remaining_tokens' => $remainingTokens,
                    'monthly_limit' => $tenant->ai_monthly_token_limit
                ], 429);
            }

            return response()->json([
                'error' => 'Yetersiz AI token bakiyesi.',
                'code' => 'INSUFFICIENT_TOKENS',
                'required_tokens' => $tokensRequired,
                'current_balance' => $tenant->ai_tokens_balance,
                'remaining_monthly' => $remainingTokens
            ], 402);
        }

        // Add token info to request for controllers to use
        $request->merge([
            'ai_tokens_required' => $tokensRequired,
            'ai_current_balance' => $tenant->ai_tokens_balance,
            'ai_remaining_monthly' => $tenant->remaining_monthly_tokens
        ]);

        return $next($request);
    }
}