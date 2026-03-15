<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Integração com ClickSign API v1
 * Docs: https://developers.clicksign.com
 *
 * Variáveis de ambiente necessárias:
 *   CLICKSIGN_ACCESS_TOKEN=seu_token
 *   CLICKSIGN_SANDBOX=true   (false em produção)
 */
class AssinaturaDigitalService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $sandbox       = config('services.clicksign.sandbox', true);
        $this->baseUrl = $sandbox
            ? 'https://sandbox.clicksign.com'
            : 'https://app.clicksign.com';
        $this->token   = config('services.clicksign.token', '');
    }

    public function configurado(): bool
    {
        return !empty($this->token);
    }

    // ── Documento ─────────────────────────────────────────────

    /**
     * Faz upload de um arquivo PDF para o ClickSign.
     * Retorna o document_key ou lança exceção.
     */
    public function uploadDocumento(string $storagePath, string $nomeArquivo): string
    {
        $conteudo = Storage::get($storagePath);
        if (!$conteudo) {
            throw new \RuntimeException("Arquivo não encontrado: {$storagePath}");
        }

        // ClickSign espera base64 do arquivo
        $base64 = base64_encode($conteudo);

        $response = $this->post('/api/v1/documents', [
            'document' => [
                'path'    => '/' . ltrim($nomeArquivo, '/'),
                'content_base64' => "data:application/pdf;base64,{$base64}",
            ],
        ]);

        $key = $response['document']['key'] ?? null;
        if (!$key) {
            throw new \RuntimeException('ClickSign não retornou a chave do documento: ' . json_encode($response));
        }

        return $key;
    }

    // ── Signatário ────────────────────────────────────────────

    /**
     * Cria um signatário no ClickSign.
     * Retorna o signer_key.
     */
    public function criarSignatario(array $dados): string
    {
        // $dados: ['nome', 'email', 'cpf', 'celular', 'auth']
        $response = $this->post('/api/v1/signers', [
            'signer' => [
                'email'        => $dados['email'],
                'phone_number' => $dados['celular'] ?? null,
                'auths'        => [$dados['auth'] ?? 'email'],
                'name'         => $dados['nome'],
                'documentation'=> $dados['cpf'] ?? null,
                'birthday'     => null,
                'has_documentation' => !empty($dados['cpf']),
            ],
        ]);

        $key = $response['signer']['key'] ?? null;
        if (!$key) {
            throw new \RuntimeException('ClickSign não retornou a chave do signatário: ' . json_encode($response));
        }

        return $key;
    }

    // ── Envelope (Lista) ──────────────────────────────────────

    /**
     * Cria um envelope (lista) com o documento e signatários.
     * Retorna o list_key.
     */
    public function criarEnvelope(string $documentKey, array $signerKeys, ?string $deadline = null): string
    {
        $payload = [
            'list' => [
                'document_key'  => $documentKey,
                'signer_keys'   => $signerKeys,
                'deadline_at'   => $deadline,
                'auto_close'    => true,
                'locale'        => 'pt-BR',
                'sequence_enabled' => false,
            ],
        ];

        $response = $this->post('/api/v1/lists', $payload);

        $key = $response['list']['key'] ?? null;
        if (!$key) {
            throw new \RuntimeException('ClickSign não retornou a chave da lista: ' . json_encode($response));
        }

        return $key;
    }

    /**
     * Adiciona signatário a um envelope já existente com papel específico.
     */
    public function adicionarSignatarioEnvelope(string $listKey, string $signerKey, string $papel): void
    {
        $papelClickSign = match($papel) {
            'assinar'                  => 'sign',
            'assinar_como_testemunha'  => 'witness',
            'aprovar'                  => 'approve',
            'reconhecer'               => 'acknowledge',
            'rubricar'                 => 'initial',
            'assinar_como_parte'       => 'party',
            default                    => 'sign',
        };

        $this->post("/api/v1/lists/{$listKey}/signers", [
            'list_signer' => [
                'signer_key' => $signerKey,
                'sign_as'    => $papelClickSign,
            ],
        ]);
    }

    /**
     * Fecha o envelope para iniciar o processo de assinatura.
     */
    public function fecharEnvelope(string $listKey): void
    {
        $this->patch("/api/v1/lists/{$listKey}/close");
    }

    /**
     * Cancela um envelope em andamento.
     */
    public function cancelarEnvelope(string $listKey): void
    {
        $this->patch("/api/v1/lists/{$listKey}/cancel");
    }

    /**
     * Consulta o status de um envelope.
     */
    public function consultarStatus(string $listKey): array
    {
        $response = $this->get("/api/v1/lists/{$listKey}");
        return $response['list'] ?? [];
    }

    // ── Fluxo completo ────────────────────────────────────────

    /**
     * Executa o fluxo completo: upload → criar signatários → criar envelope →
     * adicionar signatários com papéis → fechar.
     *
     * Retorna ['document_key', 'list_key', 'signer_keys'].
     */
    public function enviarParaAssinatura(
        string $storagePath,
        string $nomeArquivo,
        array  $signatarios,   // [['nome','email','cpf','celular','papel','auth'], ...]
        ?string $deadline = null
    ): array {
        if (!$this->configurado()) {
            throw new \RuntimeException(
                'ClickSign não configurado. Defina CLICKSIGN_ACCESS_TOKEN no .env.'
            );
        }

        Log::info('AssinaturaDigital: iniciando envio', [
            'arquivo'      => $nomeArquivo,
            'signatarios'  => count($signatarios),
        ]);

        // 1. Upload do documento
        $documentKey = $this->uploadDocumento($storagePath, $nomeArquivo);

        // 2. Criar signatários
        $signerKeys = [];
        foreach ($signatarios as $sig) {
            $signerKeys[] = [
                'key'   => $this->criarSignatario($sig),
                'papel' => $sig['papel'] ?? 'assinar',
            ];
        }

        // 3. Criar envelope (lista)
        $listKey = $this->criarEnvelope(
            $documentKey,
            array_column($signerKeys, 'key'),
            $deadline
        );

        // 4. Adicionar cada signatário com seu papel
        foreach ($signerKeys as $sk) {
            $this->adicionarSignatarioEnvelope($listKey, $sk['key'], $sk['papel']);
        }

        // 5. Fechar o envelope para disparar os e-mails
        $this->fecharEnvelope($listKey);

        Log::info('AssinaturaDigital: enviado com sucesso', [
            'document_key' => $documentKey,
            'list_key'     => $listKey,
        ]);

        return [
            'document_key' => $documentKey,
            'list_key'     => $listKey,
            'signer_keys'  => $signerKeys,
        ];
    }

    // ── HTTP helpers ──────────────────────────────────────────

    private function post(string $path, array $body): array
    {
        $response = Http::timeout(20)
            ->withHeaders(['Accept' => 'application/json'])
            ->post($this->url($path), $body);

        $this->assertSuccess($response, 'POST', $path);
        return $response->json() ?? [];
    }

    private function patch(string $path, array $body = []): array
    {
        $response = Http::timeout(20)
            ->withHeaders(['Accept' => 'application/json'])
            ->patch($this->url($path), $body);

        $this->assertSuccess($response, 'PATCH', $path);
        return $response->json() ?? [];
    }

    private function get(string $path): array
    {
        $response = Http::timeout(20)
            ->withHeaders(['Accept' => 'application/json'])
            ->get($this->url($path));

        $this->assertSuccess($response, 'GET', $path);
        return $response->json() ?? [];
    }

    private function url(string $path): string
    {
        $sep = str_contains($path, '?') ? '&' : '?';
        return $this->baseUrl . $path . $sep . 'access_token=' . $this->token;
    }

    private function assertSuccess(\Illuminate\Http\Client\Response $response, string $method, string $path): void
    {
        if (!$response->successful()) {
            $body = $response->body();
            Log::error("ClickSign: falha {$method} {$path}", [
                'status' => $response->status(),
                'body'   => $body,
            ]);
            throw new \RuntimeException(
                "ClickSign retornou HTTP {$response->status()} em {$method} {$path}: {$body}"
            );
        }
    }
}
