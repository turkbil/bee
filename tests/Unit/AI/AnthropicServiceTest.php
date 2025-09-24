<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use Modules\AI\app\Services\AnthropicService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnthropicServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AnthropicService $anthropicService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->anthropicService = app(AnthropicService::class);
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(AnthropicService::class, $this->anthropicService);
    }

    /** @test */
    public function it_formats_messages_correctly()
    {
        $messages = [
            ['role' => 'user', 'content' => 'Test message']
        ];

        $reflection = new \ReflectionClass($this->anthropicService);
        $method = $reflection->getMethod('formatMessages');
        $method->setAccessible(true);

        $result = $method->invoke($this->anthropicService, $messages);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertEquals('user', $result[0]['role']);
        $this->assertEquals('Test message', $result[0]['content']);
    }

    /** @test */
    public function it_validates_model_parameter()
    {
        $validModel = 'claude-3-5-sonnet-20241022';

        $reflection = new \ReflectionClass($this->anthropicService);
        $method = $reflection->getMethod('validateModel');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->anthropicService, $validModel));
    }

    /** @test */
    public function it_handles_rate_limiting()
    {
        $this->markTestSkipped('Rate limiting test requires API integration');
    }

    /** @test */
    public function it_calculates_token_usage()
    {
        $text = 'This is a sample text for token calculation.';

        $reflection = new \ReflectionClass($this->anthropicService);
        $method = $reflection->getMethod('estimateTokens');
        $method->setAccessible(true);

        $tokens = $method->invoke($this->anthropicService, $text);

        $this->assertIsInt($tokens);
        $this->assertGreaterThan(0, $tokens);
    }
}