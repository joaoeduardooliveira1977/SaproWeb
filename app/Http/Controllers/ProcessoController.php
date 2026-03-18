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
            'agenda', 'andamentos.usuario.pessoa',
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

        // ── Rentabilidade ───────────────────────────────────────────
        $rentabilidade = [
            'recebido'       => (float) DB::table('recebimentos')
                ->where('processo_id', $id)->where('recebido', true)->sum('valor'),
            'pendente'       => (float) DB::table('recebimentos')
                ->where('processo_id', $id)->where('recebido', false)->sum('valor'),
            'horas'          => (float) DB::table('apontamentos')
                ->where('processo_id', $id)->sum('horas'),
            'custo_estimado' => (float) DB::table('apontamentos')
                ->where('processo_id', $id)->sum('valor'),
            'custas'         => (float) DB::table('custas')
                ->where('processo_id', $id)->sum('valor'),
        ];
        $rentabilidade['saldo'] = $rentabilidade['recebido'] - $rentabilidade['custo_estimado'] - $rentabilidade['custas'];

        // ── Timeline: merge de andamentos, prazos, agenda ──────────
        $timeline = collect();

        foreach ($processo->andamentos as $a) {
            $timeline->push([
                'data'   => $a->data,
                'tipo'   => 'andamento',
                'titulo' => $a->descricao,
                'sub'    => $a->usuario->pessoa->nome ?? null,
                'cor'    => '#2563a8',
            ]);
        }

        foreach ($prazos as $p) {
            $timeline->push([
                'data'   => $p->data_prazo,
                'tipo'   => 'prazo',
                'titulo' => $p->titulo,
                'sub'    => $p->prazo_fatal ? 'Prazo fatal' : null,
                'cor'    => $p->status === 'concluido' ? '#16a34a' : ($p->data_prazo->isPast() ? '#dc2626' : '#d97706'),
            ]);
        }

        foreach ($processo->agenda as $ev) {
            $timeline->push([
                'data'   => $ev->data_hora,
                'tipo'   => 'agenda',
                'titulo' => $ev->titulo,
                'sub'    => $ev->tipo,
                'cor'    => '#7c3aed',
            ]);
        }

        foreach ($processo->audiencias as $aud) {
            $timeline->push([
                'data'   => $aud->data_hora,
                'tipo'   => 'audiencia',
                'titulo' => $aud->tipoLabel(),
                'sub'    => $aud->local ?? null,
                'cor'    => '#0891b2',
            ]);
        }

        $timeline = $timeline->sortByDesc('data')->values();

        return view('processo-show', compact('processo', 'prazos', 'documentos', 'timeline', 'rentabilidade'));
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
