<?php

namespace App\Livewire;

use App\Models\{OfxImportacao, OfxLancamento, Pagamento, Recebimento};
use App\Services\OfxParser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class ConciliacaoBancaria extends Component
{
    use WithFileUploads;

    public string $aba = 'importar'; // importar | lancamentos | historico

    // Upload
    public $arquivo        = null;
    public array  $preview = [];
    public string $previewErro = '';

    // Filtros lançamentos
    public ?int   $importacaoId   = null;
    public string $filtroStatus   = ''; // '' | pendente | conciliado
    public string $filtroTipo     = ''; // '' | credito | debito

    // Modal conciliar
    public bool   $modalConciliar    = false;
    public ?int   $lancamentoSel     = null;
    public string $buscaConciliar    = '';
    public string $tipoConciliar     = ''; // pagamentos | recebimentos

    protected $queryString = [
        'aba'          => ['except' => 'importar'],
        'importacaoId' => ['except' => null],
        'filtroStatus' => ['except' => ''],
        'filtroTipo'   => ['except' => ''],
    ];

    // ── Upload / Preview ──────────────────────────────────────

    public function updatedArquivo(): void
    {
        $this->preview    = [];
        $this->previewErro = '';

        if (! $this->arquivo) return;

        try {
            $conteudo = file_get_contents($this->arquivo->getRealPath());
            $parser   = new OfxParser();
            $this->preview = $parser->parse($conteudo);

            if (empty($this->preview['lancamentos'])) {
                $this->previewErro = 'Nenhum lançamento encontrado no arquivo.';
            }
        } catch (\Throwable $e) {
            $this->previewErro = 'Erro ao ler arquivo: ' . $e->getMessage();
        }
    }

    public function confirmarImportacao(): void
    {
        if (empty($this->preview['lancamentos'])) return;

        $importacao = OfxImportacao::create([
            'arquivo'           => $this->arquivo?->getClientOriginalName() ?? 'ofx',
            'banco'             => $this->preview['banco']    ?? null,
            'agencia'           => $this->preview['agencia']  ?? null,
            'conta'             => $this->preview['conta']    ?? null,
            'data_ini'          => $this->preview['data_ini'] ?? null,
            'data_fim'          => $this->preview['data_fim'] ?? null,
            'total_lancamentos' => count($this->preview['lancamentos']),
            'usuario_id'        => auth('usuarios')->id(),
        ]);

        foreach ($this->preview['lancamentos'] as $l) {
            OfxLancamento::create([
                'importacao_id' => $importacao->id,
                'data'          => $l['data'],
                'valor'         => $l['valor'],
                'tipo'          => $l['tipo']      ?? null,
                'descricao'     => $l['descricao'] ?? null,
                'fitid'         => $l['fitid']     ?? null,
            ]);
        }

        // Auto-conciliação por valor+data
        $this->autoConciliar($importacao);
        $importacao->atualizarConciliados();

        $this->arquivo     = null;
        $this->preview     = [];
        $this->importacaoId = $importacao->id;
        $this->aba          = 'lancamentos';

        $this->dispatch('toast', tipo: 'success',
            msg: "{$importacao->total_lancamentos} lançamento(s) importado(s). {$importacao->conciliados} conciliado(s) automaticamente.");
    }

    // ── Auto-conciliação ──────────────────────────────────────

    private function autoConciliar(OfxImportacao $importacao): void
    {
        foreach ($importacao->lancamentos as $lance) {
            if ($lance->valor > 0) {
                $match = $this->buscarRecebimento($lance);
                if ($match) $this->salvarVinculo($lance, 'recebimentos', $match->id);
            } else {
                $match = $this->buscarPagamento($lance);
                if ($match) $this->salvarVinculo($lance, 'pagamentos', $match->id);
            }
        }
    }

    private function buscarRecebimento(OfxLancamento $lance): ?Recebimento
    {
        $valor = abs($lance->valor);
        $data  = Carbon::parse($lance->data);

        return Recebimento::whereBetween('data_recebimento', [
                $data->copy()->subDays(5),
                $data->copy()->addDays(5),
            ])
            ->where('valor_recebido', '>=', $valor - 0.02)
            ->where('valor_recebido', '<=', $valor + 0.02)
            ->where('recebido', true)
            ->whereNotIn('id', function ($q) {
                $q->select('referencia_id')
                  ->from('ofx_lancamentos')
                  ->where('referencia_tipo', 'recebimentos')
                  ->whereNotNull('referencia_id');
            })
            ->first();
    }

    private function buscarPagamento(OfxLancamento $lance): ?Pagamento
    {
        $valor = abs($lance->valor);
        $data  = Carbon::parse($lance->data);

        return Pagamento::whereBetween('data_pagamento', [
                $data->copy()->subDays(5),
                $data->copy()->addDays(5),
            ])
            ->where('valor_pago', '>=', $valor - 0.02)
            ->where('valor_pago', '<=', $valor + 0.02)
            ->where('pago', true)
            ->whereNotIn('id', function ($q) {
                $q->select('referencia_id')
                  ->from('ofx_lancamentos')
                  ->where('referencia_tipo', 'pagamentos')
                  ->whereNotNull('referencia_id');
            })
            ->first();
    }

    private function salvarVinculo(OfxLancamento $lance, string $tipo, int $id): void
    {
        $lance->update([
            'conciliado'      => true,
            'referencia_tipo' => $tipo,
            'referencia_id'   => $id,
        ]);
    }

    // ── Modal de conciliação manual ───────────────────────────

    public function abrirConciliar(int $lancamentoId): void
    {
        $lance = OfxLancamento::find($lancamentoId);
        if (! $lance) return;

        $this->lancamentoSel  = $lancamentoId;
        $this->tipoConciliar  = $lance->isCredito() ? 'recebimentos' : 'pagamentos';
        $this->buscaConciliar = '';
        $this->modalConciliar = true;
    }

    public function fecharConciliar(): void
    {
        $this->modalConciliar = false;
        $this->lancamentoSel  = null;
        $this->buscaConciliar = '';
    }

    public function vincular(string $tipo, int $referenciaId): void
    {
        $lance = OfxLancamento::find($this->lancamentoSel);
        if (! $lance) return;

        $this->salvarVinculo($lance, $tipo, $referenciaId);
        $lance->importacao->atualizarConciliados();
        $this->fecharConciliar();
        $this->dispatch('toast', tipo: 'success', msg: 'Lançamento conciliado.');
    }

    public function desvincular(int $lancamentoId): void
    {
        $lance = OfxLancamento::find($lancamentoId);
        if (! $lance) return;

        $lance->update([
            'conciliado'      => false,
            'referencia_tipo' => null,
            'referencia_id'   => null,
        ]);
        $lance->importacao->atualizarConciliados();
        $this->dispatch('toast', tipo: 'info', msg: 'Vínculo removido.');
    }

    public function excluirImportacao(int $id): void
    {
        OfxImportacao::destroy($id);
        if ($this->importacaoId === $id) {
            $this->importacaoId = null;
            $this->aba = 'historico';
        }
        $this->dispatch('toast', tipo: 'success', msg: 'Importação excluída.');
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        // Lista de lançamentos da importação selecionada
        $lancamentos = collect();
        $importacaoAtual = null;

        if ($this->importacaoId) {
            $importacaoAtual = OfxImportacao::find($this->importacaoId);

            if ($importacaoAtual) {
                $q = OfxLancamento::where('importacao_id', $this->importacaoId)
                    ->orderBy('data');

                if ($this->filtroStatus === 'pendente') {
                    $q->where('conciliado', false);
                } elseif ($this->filtroStatus === 'conciliado') {
                    $q->where('conciliado', true);
                }

                if ($this->filtroTipo === 'credito') {
                    $q->where('valor', '>', 0);
                } elseif ($this->filtroTipo === 'debito') {
                    $q->where('valor', '<', 0);
                }

                $lancamentos = $q->get();
            }
        }

        // Sugestões para o modal de conciliação
        $sugestoes = collect();
        if ($this->modalConciliar && $this->lancamentoSel) {
            $lance = OfxLancamento::find($this->lancamentoSel);
            if ($lance) {
                $sugestoes = $this->buscarSugestoes($lance);
            }
        }

        // Histórico de importações
        $importacoes = OfxImportacao::latest()->limit(20)->get();

        return view('livewire.conciliacao-bancaria', compact(
            'lancamentos', 'importacaoAtual', 'importacoes', 'sugestoes'
        ));
    }

    private function buscarSugestoes(OfxLancamento $lance)
    {
        $valor = abs($lance->valor);
        $data  = Carbon::parse($lance->data);
        $busca = trim($this->buscaConciliar);

        if ($this->tipoConciliar === 'recebimentos') {
            $q = Recebimento::with('processo.cliente')
                ->where('recebido', true);

            if ($busca) {
                $q->where(fn($s) => $s
                    ->where('descricao', 'ilike', "%{$busca}%")
                    ->orWhereHas('processo', fn($p) => $p->where('numero', 'ilike', "%{$busca}%"))
                );
            } else {
                $q->whereBetween('data_recebimento', [
                        $data->copy()->subDays(10),
                        $data->copy()->addDays(10),
                    ])
                  ->where('valor_recebido', '>=', $valor * 0.9)
                  ->where('valor_recebido', '<=', $valor * 1.1);
            }

            return $q->limit(10)->get()->map(fn($r) => [
                'id'        => $r->id,
                'tipo'      => 'recebimentos',
                'data'      => $r->data_recebimento?->format('d/m/Y') ?? '—',
                'valor'     => $r->valor_recebido,
                'descricao' => $r->descricao ?? '—',
                'processo'  => $r->processo?->numero ?? '—',
                'cliente'   => $r->processo?->cliente?->nome ?? '—',
            ]);
        }

        // pagamentos
        $q = Pagamento::with('processo.cliente')
            ->where('pago', true);

        if ($busca) {
            $q->where(fn($s) => $s
                ->where('descricao', 'ilike', "%{$busca}%")
                ->orWhereHas('processo', fn($p) => $p->where('numero', 'ilike', "%{$busca}%"))
            );
        } else {
            $q->whereBetween('data_pagamento', [
                    $data->copy()->subDays(10),
                    $data->copy()->addDays(10),
                ])
              ->where('valor_pago', '>=', $valor * 0.9)
              ->where('valor_pago', '<=', $valor * 1.1);
        }

        return $q->limit(10)->get()->map(fn($p) => [
            'id'        => $p->id,
            'tipo'      => 'pagamentos',
            'data'      => $p->data_pagamento?->format('d/m/Y') ?? '—',
            'valor'     => $p->valor_pago,
            'descricao' => $p->descricao,
            'processo'  => $p->processo?->numero ?? '—',
            'cliente'   => $p->processo?->cliente?->nome ?? '—',
        ]);
    }
}
