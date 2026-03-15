<?php

namespace App\Http\Controllers;

use App\Models\{Processo, Andamento, Custa, Prazo};
use Illuminate\Support\Facades\DB;
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
            'agenda', 'andamentos',
            'audiencias.juiz', 'audiencias.advogado',
        ])->findOrFail($id);

        $prazos = Prazo::with('responsavel')
            ->where('processo_id', $id)
            ->orderBy('data_prazo')
            ->get();

        $documentos = DB::select("
            SELECT d.id, d.titulo, d.tipo, d.data_documento, d.arquivo,
                   d.arquivo_original, d.tamanho, d.portal_visivel, d.created_at
            FROM documentos d
            WHERE d.processo_id = ?
            ORDER BY d.created_at DESC
        ", [$id]);

        return view('processo-show', compact('processo', 'prazos', 'documentos'));
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
