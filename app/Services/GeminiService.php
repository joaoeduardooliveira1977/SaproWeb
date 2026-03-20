<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model   = 'gemini-2.5-flash';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
    }

    public function gerar(string $prompt, int $maxTokens = 1024): ?string
    {
        if (empty($this->apiKey)) {
            Log::channel('gemini')->warning('Gemini API key não configurada (GEMINI_API_KEY)');
            return null;
        }

        $inicio = microtime(true);

        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents'         => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'maxOutputTokens' => $maxTokens,
                        'temperature'     => 0.3,
                    ],
                ]
            );

            $ms = round((microtime(true) - $inicio) * 1000);

            if (!$response->ok()) {
                Log::channel('gemini')->error('Gemini API error', [
                    'status' => $response->status(),
                    'ms'     => $ms,
                    'body'   => substr($response->body(), 0, 400),
                ]);
                return null;
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            Log::channel('gemini')->info('Gemini OK', [
                'ms'    => $ms,
                'chars' => strlen($text ?? ''),
            ]);

            return $text;

        } catch (\Throwable $e) {
            Log::channel('gemini')->error('Gemini exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
