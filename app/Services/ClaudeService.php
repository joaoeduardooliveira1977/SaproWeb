<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    private string $apiKey  = '';
    private string $model   = 'claude-sonnet-4-6';
    private string $baseUrl = 'https://api.anthropic.com/v1';

    public function __construct()
    {
        $tenantKey = null;

        try {
            $tenant = tenant();
            if ($tenant && !empty($tenant->claude_api_key)) {
                $tenantKey = $tenant->claude_api_key;
            }
        } catch (\Exception) {
            // Sem tenant no contexto (jobs, schedule)
        }

        $this->apiKey = $tenantKey ?? config('services.claude.key', '');
    }

    /**
     * Geração simples: prompt único → resposta.
     * Interface idêntica ao GeminiService para drop-in replacement.
     */
    public function gerar(string $prompt, int $maxTokens = 1024): ?string
    {
        return $this->enviar(null, [['role' => 'user', 'content' => $prompt]], $maxTokens);
    }

    /**
     * Geração multi-turn com system prompt separado.
     * Aproveita o campo `system` nativo do Claude.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function gerarChat(string $system, array $messages, int $maxTokens = 1024): ?string
    {
        return $this->enviar($system, $messages, $maxTokens);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Privado
    // ─────────────────────────────────────────────────────────────────────

    private function enviar(?string $system, array $messages, int $maxTokens): ?string
    {
        // Verificar se tenant tem IA habilitada
        try {
            $tenant = tenant();
            if ($tenant && !$tenant->ia_habilitada) {
                Log::channel('gemini')->warning('IA não habilitada para este tenant (Claude)', [
                    'tenant_id'   => $tenant->id,
                    'tenant_nome' => $tenant->nome ?? '',
                ]);
                return '__IA_BLOQUEADA__';
            }
        } catch (\Exception) {
            // Contexto sem tenant (jobs) — permite usar
        }

        if (empty($this->apiKey)) {
            Log::channel('gemini')->warning('Claude API key não configurada (ANTHROPIC_API_KEY)');
            return null;
        }

        // Claude exige que messages alternem user/assistant e comecem com user.
        $messages = $this->sanitizarMensagens($messages);

        if (empty($messages)) {
            return null;
        }

        $payload = [
            'model'      => $this->model,
            'max_tokens' => $maxTokens,
            'messages'   => $messages,
        ];

        if ($system !== null && $system !== '') {
            $payload['system'] = $system;
        }

        $inicio = microtime(true);

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(60)->post("{$this->baseUrl}/messages", $payload);

            $ms = round((microtime(true) - $inicio) * 1000);

            if (!$response->ok()) {
                Log::channel('gemini')->error('Claude API error', [
                    'status' => $response->status(),
                    'ms'     => $ms,
                    'body'   => substr($response->body(), 0, 400),
                ]);
                return null;
            }

            $text = $response->json('content.0.text');

            Log::channel('gemini')->info('Claude OK', [
                'ms'    => $ms,
                'chars' => strlen($text ?? ''),
                'model' => $this->model,
            ]);

            return $text;

        } catch (\Throwable $e) {
            Log::channel('gemini')->error('Claude exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Garante que o array de mensagens começa com role=user
     * e não tem dois turnos consecutivos do mesmo role.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array<int, array{role: string, content: string}>
     */
    private function sanitizarMensagens(array $messages): array
    {
        $limpos = [];

        foreach ($messages as $msg) {
            $role    = $msg['role']    ?? 'user';
            $content = $msg['content'] ?? '';

            if ($content === '') continue;

            // Normaliza role do Gemini → Claude
            if ($role === 'model') {
                $role = 'assistant';
            }

            // Evita dois turnos consecutivos do mesmo role
            if (!empty($limpos) && end($limpos)['role'] === $role) {
                // Concatena ao anterior
                $limpos[count($limpos) - 1]['content'] .= "\n\n" . $content;
                continue;
            }

            $limpos[] = ['role' => $role, 'content' => $content];
        }

        // Deve começar com user
        if (!empty($limpos) && $limpos[0]['role'] !== 'user') {
            array_unshift($limpos, ['role' => 'user', 'content' => '']);
        }

        return $limpos;
    }
}
