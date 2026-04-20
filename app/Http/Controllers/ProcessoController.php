<?php

namespace App\Http\Controllers;

use App\Models\{Processo, Prazo};
use App\Services\AIService;
use Illuminate\Support\Facades\{DB, Schema};


class ProcessoController extends Controller
{
    


// ── IA gerar resumo ───────────────────────────────────────────

	public function gerarResumo(int $id, AIService $ai)
{
    $processo = Processo::with(['cliente', 'advogado', 'fase', 'risco', 'andamentos'])
        ->findOrFail($id);

    $andamentos = $processo->andamentos
        ->take(8)
        ->map(fn ($andamento) => ($andamento->data?->format('d/m/Y') ?? 'sem data') . ' - ' . $andamento->descricao)
        ->implode("\n");

    $prompt = "Voce e um assistente juridico. Gere um resumo objetivo do processo abaixo em portugues do Brasil, com no maximo 5 bullets e uma recomendacao de proximo passo.\n\n"
        . "Numero: " . ($processo->numero ?? 'sem numero') . "\n"
        . "Status: " . ($processo->status ?? 'sem status') . "\n"
        . "Cliente: " . ($processo->cliente?->nome ?? 'nao informado') . "\n"
        . "Advogado: " . ($processo->advogado?->nome ?? 'nao informado') . "\n"
        . "Fase: " . ($processo->fase?->descricao ?? 'nao informada') . "\n"
        . "Risco: " . ($processo->risco?->descricao ?? 'nao informado') . "\n"
        . "Observacoes: " . ($processo->observacoes ?? 'sem observacoes') . "\n\n"
        . "Ultimos andamentos:\n" . ($andamentos ?: 'sem andamentos cadastrados');

    $resumo = $ai->gerar($prompt, 700);

    return response()->json([
        'resumo' => $resumo ?? 'IA temporariamente indisponivel. Tente novamente em instantes.',
    ]);
}



// ──  ───────────────────────────────────────────

    public function show(int $id)
    {
        $relacoes = [
            'cliente', 'advogado',
            'tipoAcao', 'tipoProcesso', 'fase',
            'risco', 'reparticao',
            'agenda', 'andamentos.usuario.pessoa',
            'audiencias.juiz', 'audiencias.advogado',
        ];

        $processo = Processo::with($relacoes)->findOrFail($id);

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

        $contratosVinculados = collect();
        if (Schema::hasColumn('contratos', 'processo_id')) {
            $contratosVinculados = \App\Models\Contrato::with(['advogadoResponsavel', 'servicos'])
                ->where('processo_id', $id)
                ->orderByDesc('created_at')
                ->get();
        }

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

        // ── Timeline: merge de andamentos, prazos, agenda, audiências, documentos ──
        $timeline = collect();

        foreach ($processo->andamentos as $a) {
            $timeline->push([
                'data'   => $a->data,
                'tipo'   => 'andamento',
                'titulo' => $a->descricao,
                'sub'    => $a->usuario->pessoa->nome ?? null,
                'cor'    => '#2563eb',
                'extra'  => [],
            ]);
        }

        foreach ($prazos as $p) {
            $vencido   = $p->data_prazo->isPast() && $p->status !== 'cumprido';
            $cumprido  = $p->status === 'cumprido';
            $timeline->push([
                'data'   => $p->data_prazo,
                'tipo'   => 'prazo',
                'titulo' => $p->titulo,
                'sub'    => $p->responsavel?->nome ?? null,
                'cor'    => $cumprido ? '#16a34a' : ($vencido ? '#dc2626' : '#d97706'),
                'extra'  => [
                    'fatal'    => (bool) $p->prazo_fatal,
                    'status'   => $p->status,
                    'vencido'  => $vencido,
                    'cumprido' => $cumprido,
                ],
            ]);
        }

        foreach ($processo->agenda as $ev) {
            $timeline->push([
                'data'   => $ev->data_hora,
                'tipo'   => 'agenda',
                'titulo' => $ev->titulo,
                'sub'    => $ev->tipo,
                'cor'    => '#7c3aed',
                'extra'  => [
                    'local'    => $ev->local ?? null,
                    'urgente'  => (bool) ($ev->urgente ?? false),
                    'concluido'=> (bool) ($ev->concluido ?? false),
                ],
            ]);
        }

        foreach ($processo->audiencias as $aud) {
            $timeline->push([
                'data'   => $aud->data_hora,
                'tipo'   => 'audiencia',
                'titulo' => $aud->tipoLabel(),
                'sub'    => $aud->advogado?->nome ?? null,
                'cor'    => '#0891b2',
                'extra'  => [
                    'local' => $aud->local ?? null,
                    'juiz'  => $aud->juiz?->nome ?? null,
                ],
            ]);
        }

        foreach ($documentos as $doc) {
            $timeline->push([
                'data'   => $doc->created_at,
                'tipo'   => 'documento',
                'titulo' => $doc->titulo,
                'sub'    => $doc->tipo ?? null,
                'cor'    => '#64748b',
                'extra'  => [
                    'arquivo' => $doc->arquivo ?? null,
                ],
            ]);
        }

        $timeline = $timeline->sortByDesc('data')->values();

        return view('processo-show', compact('processo', 'prazos', 'documentos', 'timeline', 'rentabilidade', 'contratosVinculados'));
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
