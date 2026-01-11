<?php

namespace Modules\AI\App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Streaming AI Service
 *
 * Claude/OpenAI streaming responses
 * Real-time word-by-word output
 */
class StreamingAIService
{
    protected $provider;
    protected $apiKey;

    public function __construct(string $provider = 'anthropic')
    {
        $this->provider = $provider;
        $this->apiKey = config("services.{$provider}.api_key");
    }

    /**
     * Stream AI response
     *
     * @param array $messages [['role' => 'user', 'content' => '...']]
     * @param callable $onChunk function($chunk) { broadcast(...) }
     * @return string Full response
     */
    public function stream(array $messages, callable $onChunk): string
    {
        Log::info('🌊 Starting streaming response', [
            'provider' => $this->provider
        ]);

        $fullResponse = '';

        if ($this->provider === 'anthropic') {
            $fullResponse = $this->streamClaude($messages, $onChunk);
        } elseif ($this->provider === 'openai') {
            $fullResponse = $this->streamOpenAI($messages, $onChunk);
        }

        Log::info('✅ Streaming completed', [
            'length' => strlen($fullResponse)
        ]);

        return $fullResponse;
    }

    /**
     * Claude streaming
     */
    protected function streamClaude(array $messages, callable $onChunk): string
    {
        $url = 'https://api.anthropic.com/v1/messages';
        $fullResponse = '';

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json'
        ])->timeout(60)->stream('POST', $url, [
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 1024,
            'stream' => true,
            'messages' => $messages
        ]);

        $buffer = '';

        foreach ($response as $chunk) {
            $buffer .= $chunk;

            // Parse SSE format
            $lines = explode("\n", $buffer);
            $buffer = array_pop($lines); // Keep incomplete line

            foreach ($lines as $line) {
                if (strpos($line, 'data: ') === 0) {
                    $jsonData = substr($line, 6);

                    if ($jsonData === '[DONE]') {
                        break 2;
                    }

                    $data = json_decode($jsonData, true);

                    if ($data && $data['type'] === 'content_block_delta') {
                        $text = $data['delta']['text'] ?? '';

                        if ($text) {
                            $fullResponse .= $text;
                            $onChunk($text);
                        }
                    }
                }
            }
        }

        return $fullResponse;
    }

    /**
     * OpenAI streaming
     */
    protected function streamOpenAI(array $messages, callable $onChunk): string
    {
        $url = 'https://api.openai.com/v1/chat/completions';
        $fullResponse = '';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->timeout(60)->stream('POST', $url, [
            'model' => 'gpt-4-turbo-preview',
            'stream' => true,
            'messages' => $messages
        ]);

        $buffer = '';

        foreach ($response as $chunk) {
            $buffer .= $chunk;

            $lines = explode("\n", $buffer);
            $buffer = array_pop($lines);

            foreach ($lines as $line) {
                if (strpos($line, 'data: ') === 0) {
                    $jsonData = substr($line, 6);

                    if ($jsonData === '[DONE]') {
                        break 2;
                    }

                    $data = json_decode($jsonData, true);

                    if ($data && isset($data['choices'][0]['delta']['content'])) {
                        $text = $data['choices'][0]['delta']['content'];

                        if ($text) {
                            $fullResponse .= $text;
                            $onChunk($text);
                        }
                    }
                }
            }
        }

        return $fullResponse;
    }

    /**
     * Broadcast streaming chunk
     */
    public function broadcastChunk(string $channel, string $chunk): void
    {
        broadcast(new \Modules\AI\App\Events\MessageChunkReceived($channel, $chunk));
    }
}
