<?php

namespace App\Services;

/**
 * Roteador unificado de IA.
 *
 * Escolhe Claude ou Gemini com base em AI_PROVIDER no .env.
 * Todos os componentes Livewire devem injetar esta classe — nunca
 * GeminiService ou ClaudeService diretamente.
 *
 * Interface pública é idêntica ao GeminiService para compatibilidade.
 */
class AIService
{
    private GeminiService|ClaudeService $driver;

    public function __construct()
    {
        $provider    = config('services.ai.provider', 'gemini');
        $this->driver = $provider === 'claude'
            ? app(ClaudeService::class)
            : app(GeminiService::class);
    }

    /**
     * Geração simples: um prompt → uma resposta.
     * Drop-in replacement do GeminiService::gerar().
     */
    public function gerar(string $prompt, int $maxTokens = 1024): ?string
    {
        return $this->driver->gerar($prompt, $maxTokens);
    }

    /**
     * Geração multi-turn com system prompt separado.
     *
     * Claude: usa o campo `system` nativo + array messages user/assistant.
     * Gemini: injeta o system no primeiro turno de usuário como fallback.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function gerarChat(string $system, array $messages, int $maxTokens = 1024): ?string
    {
        if ($this->driver instanceof ClaudeService) {
            return $this->driver->gerarChat($system, $messages, $maxTokens);
        }

        // Fallback Gemini: concatena system no prompt da primeira mensagem
        if (!empty($messages)) {
            $messages[0]['content'] = $system . "\n\n" . ($messages[0]['content'] ?? '');
        }

        $prompt = implode("\n\n", array_column($messages, 'content'));
        return $this->driver->gerar($prompt, $maxTokens);
    }

    /**
     * Retorna o nome legível do provedor ativo.
     */
    public function provedor(): string
    {
        return $this->driver instanceof ClaudeService ? 'Claude' : 'Gemini';
    }
}
