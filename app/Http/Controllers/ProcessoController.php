<?php

namespace App\Http\Controllers;

use App\Models\{Processo, Andamento, Custa};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcessoController extends Controller
{
    public function show(int $id)
    {
        $processo = Processo::with([
            'cliente', 'advogado', 'juiz',
            'tipoAcao', 'tipoProcesso', 'fase',
            'assunto', 'risco', 'secretaria', 'reparticao',
        ])->findOrFail($id);

        return view('processo-show', compact('processo'));
    }

    public function andamentos(int $id)
    {
        $processo = Processo::with('andamentos.usuario.pessoa')->findOrFail($id);
        return view('processo-andamentos', compact('processo'));
    }

    public function custas(int $id)
    {
        $processo = Processo::with('custas')->findOrFail($id);
        $totais = [
            'total'    => $processo->custas->sum('valor'),
            'pago'     => $processo->custas->where('pago', true)->sum('valor'),
            'pendente' => $processo->custas->where('pago', false)->sum('valor'),
        ];
        return view('processo-custas', compact('processo', 'totais'));
    }
}
