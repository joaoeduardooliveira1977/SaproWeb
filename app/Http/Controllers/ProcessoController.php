<?php

namespace App\Http\Controllers;

use App\Models\{Custa, FinanceiroLancamento, Processo, Prazo};
use App\Services\AIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\{Auth, DB, Schema};


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
        $processo = Processo::with(['cliente', 'custas.cobrancaLancamento'])->findOrFail($id);
        $totais = [
            'total'    => $processo->custas->sum('valor'),
            'pago'     => $processo->custas->where('pago', true)->sum('valor'),
            'pendente' => $processo->custas->where('pago', false)->sum('valor'),
            'reembolsavel' => $processo->custas->where('reembolsavel', true)->sum('valor'),
            'a_cobrar' => $processo->custas
                ->where('reembolsavel', true)
                ->where('pago', true)
                ->whereNull('cobranca_lancamento_id')
                ->sum('valor'),
            'cobrado' => $processo->custas->whereNotNull('cobranca_lancamento_id')->sum('valor'),
        ];
        return view('processo-custas', compact('processo', 'totais'));
    }

    public function alternarReembolsoCusta(int $id, int $custaId): RedirectResponse
    {
        $processo = Processo::findOrFail($id);
        $custa = Custa::where('processo_id', $processo->id)->findOrFail($custaId);

        if ($custa->cobranca_lancamento_id) {
            return back()->with('erro', 'Essa custa já foi cobrada no financeiro.');
        }

        $custa->update([
            'reembolsavel' => !$custa->reembolsavel,
        ]);

        return back()->with('sucesso', $custa->reembolsavel
            ? 'Custa marcada como reembolsável.'
            : 'Custa marcada como não reembolsável.');
    }

    public function gerarCobrancaCusta(int $id, int $custaId): RedirectResponse
    {
        $processo = Processo::with('cliente')->findOrFail($id);
        $custa = Custa::where('processo_id', $processo->id)->findOrFail($custaId);

        if (!$custa->reembolsavel) {
            return back()->with('erro', 'Essa custa está marcada como não reembolsável.');
        }

        if (!$custa->pago) {
            return back()->with('erro', 'Marque a custa como paga antes de gerar a cobrança de reembolso.');
        }

        if ($custa->cobranca_lancamento_id) {
            return back()->with('erro', 'Essa custa já possui cobrança gerada.');
        }

        if (!$processo->cliente_id) {
            return back()->with('erro', 'O processo não possui cliente vinculado para gerar a cobrança.');
        }

        $usuario = Auth::guard('usuarios')->user();

        $lancamentoId = DB::table('financeiro_lancamentos')->insertGetId([
            'tenant_id'   => $processo->tenant_id ?? $usuario?->tenant_id,
            'cliente_id'  => $processo->cliente_id,
            'processo_id' => $processo->id,
            'tipo'        => 'receita',
            'descricao'   => 'Reembolso de custa - ' . $custa->descricao . ' - Proc. ' . $processo->numero,
            'valor'       => $custa->valor,
            'vencimento'  => now()->toDateString(),
            'status'      => 'previsto',
            'observacoes' => 'Cobrança gerada a partir da custa processual em ' . now()->format('d/m/Y H:i'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $custa->update([
            'cobranca_lancamento_id' => $lancamentoId,
            'cobrado_em'             => now(),
            'cobrado_por'            => $usuario?->nome ?? 'Sistema',
        ]);

        return back()->with('sucesso', 'Cobrança de reembolso gerada no Financeiro Central.');
    }
}
