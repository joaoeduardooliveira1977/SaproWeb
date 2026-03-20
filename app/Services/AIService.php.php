<?php

namespace App\Services;

use OpenAI;

class AIService
{
    public function perguntar($mensagem)
    {
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Você é um assistente jurídico brasileiro especialista em direito civil, trabalhista e processual.'],
                ['role' => 'user', 'content' => $mensagem],
            ],
        ]);

        return $response->choices[0]->message->content;
    }
}