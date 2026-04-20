<?php

namespace Tests\Unit;

use App\Services\AIService;
use App\Services\ClaudeService;
use App\Services\GeminiService;
use Mockery;
use Tests\TestCase;

class AIServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_routes_simple_generation_to_claude_when_configured(): void
    {
        config()->set('services.ai.provider', 'claude');

        $claude = Mockery::mock(ClaudeService::class);
        $claude->shouldReceive('gerar')
            ->once()
            ->with('teste', 321)
            ->andReturn('resposta claude');

        $this->app->instance(ClaudeService::class, $claude);

        $service = new AIService();

        $this->assertSame('Claude', $service->provedor());
        $this->assertSame('resposta claude', $service->gerar('teste', 321));
    }

    public function test_it_routes_simple_generation_to_gemini_when_provider_is_not_claude(): void
    {
        config()->set('services.ai.provider', 'gemini');

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('gerar')
            ->once()
            ->with('pergunta', 222)
            ->andReturn('resposta gemini');

        $this->app->instance(GeminiService::class, $gemini);

        $service = new AIService();

        $this->assertSame('Gemini', $service->provedor());
        $this->assertSame('resposta gemini', $service->gerar('pergunta', 222));
    }

    public function test_it_uses_claude_native_chat_when_available(): void
    {
        config()->set('services.ai.provider', 'claude');

        $messages = [
            ['role' => 'user', 'content' => 'Quero um resumo.'],
            ['role' => 'assistant', 'content' => 'Claro.'],
        ];

        $claude = Mockery::mock(ClaudeService::class);
        $claude->shouldReceive('gerarChat')
            ->once()
            ->with('Você é um assistente jurídico.', $messages, 900)
            ->andReturn('chat claude');

        $this->app->instance(ClaudeService::class, $claude);

        $service = new AIService();

        $this->assertSame(
            'chat claude',
            $service->gerarChat('Você é um assistente jurídico.', $messages, 900)
        );
    }

    public function test_it_falls_back_to_single_prompt_chat_for_gemini(): void
    {
        config()->set('services.ai.provider', 'gemini');

        $messages = [
            ['role' => 'user', 'content' => 'Quais prazos vencem hoje?'],
            ['role' => 'assistant', 'content' => 'Estou analisando os dados.'],
        ];

        $expectedPrompt = "Contexto do escritório.\n\nQuais prazos vencem hoje?\n\nEstou analisando os dados.";

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('gerar')
            ->once()
            ->with($expectedPrompt, 700)
            ->andReturn('chat gemini');

        $this->app->instance(GeminiService::class, $gemini);

        $service = new AIService();

        $this->assertSame(
            'chat gemini',
            $service->gerarChat('Contexto do escritório.', $messages, 700)
        );
    }
}
