<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    /**
     * Get user's active tokens
     */
    public function getTokens(Request $request)
    {
        try {
            $user = $request->user();
            
            $tokens = $user->tokens()->get()->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'last_used_at' => $token->last_used_at ? $token->last_used_at->format('Y-m-d H:i:s') : null,
                    'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                    'expires_at' => $token->expires_at ? $token->expires_at->format('Y-m-d H:i:s') : null,
                    'is_current' => $token->id === $request->user()->currentAccessToken()->id,
                ];
            });

            return response()->json([
                'message' => 'Tokens retrieved successfully',
                'tokens' => $tokens,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve tokens',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh current token
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();
            $currentToken = $request->user()->currentAccessToken();

            // Create new token
            $newToken = $user->createToken('mobile-app-refreshed')->plainTextToken;

            // Delete old token
            $currentToken->delete();

            return response()->json([
                'message' => 'Token refreshed successfully',
                'token' => $newToken,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to refresh token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Revoke specific token
     */
    public function revokeToken(Request $request)
    {
        try {
            // Basic validation
            if (!$request->has('token_id')) {
                return response()->json([
                    'message' => 'Token ID is required',
                ], 422);
            }

            $user = $request->user();
            $tokenId = $request->token_id;

            // Find token
            $token = $user->tokens()->where('id', $tokenId)->first();

            if (!$token) {
                return response()->json([
                    'message' => 'Token not found',
                ], 404);
            }

            // Check if trying to revoke current token
            if ($token->id === $request->user()->currentAccessToken()->id) {
                return response()->json([
                    'message' => 'Cannot revoke current token. Use logout instead.',
                ], 400);
            }

            // Revoke token
            $token->delete();

            return response()->json([
                'message' => 'Token revoked successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to revoke token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Revoke all tokens except current
     */
    public function revokeAllTokens(Request $request)
    {
        try {
            $user = $request->user();
            $currentTokenId = $request->user()->currentAccessToken()->id;

            // Delete all tokens except current
            $revokedCount = $user->tokens()->where('id', '!=', $currentTokenId)->delete();

            return response()->json([
                'message' => 'All other tokens revoked successfully',
                'revoked_count' => $revokedCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to revoke tokens',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get token info
     */
    public function getTokenInfo(Request $request)
    {
        try {
            $token = $request->user()->currentAccessToken();

            return response()->json([
                'message' => 'Token info retrieved successfully',
                'token' => [
                    'id' => $token->id,
                    'name' => $token->name,
                    'last_used_at' => $token->last_used_at ? $token->last_used_at->format('Y-m-d H:i:s') : null,
                    'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                    'expires_at' => $token->expires_at ? $token->expires_at->format('Y-m-d H:i:s') : null,
                    'abilities' => $token->abilities,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve token info',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate token
     */
    public function validateToken(Request $request)
    {
        try {
            $user = $request->user();
            $token = $request->user()->currentAccessToken();

            return response()->json([
                'message' => 'Token is valid',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                ],
                'token' => [
                    'id' => $token->id,
                    'name' => $token->name,
                    'last_used_at' => $token->last_used_at ? $token->last_used_at->format('Y-m-d H:i:s') : null,
                    'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token validation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}