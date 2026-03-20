<?php

namespace App\Http\Controllers;

use App\Services\AIService;

class IAController extends Controller
{
    public function teste(AIService $ai)
    {
        $resposta = $ai->perguntar("Explique o que é uma petição inicial de forma simples");

        return response()->json([
            'resposta' => $resposta
        ]);
    }
}