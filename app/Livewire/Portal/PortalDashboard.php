<?php

namespace App\Livewire\Portal;

use App\Models\{Pessoa, Processo, Andamento, Agenda, FinanceiroLancamento};
use App\Services\PixService;
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Session, Storage};
use Livewire\Component;
use Livewire\WithFileUploads;

class PortalDashboard extends Component
{
    use WithFileUploads;
    public ?Pessoa $pessoa         = null;
    public string  $aba            = 'inicio';
    public ?int    $processoAberto = null;  // processo em detalhe

    // Filtro de processos: 'todos' | 'judiciais' | 'extrajudiciais'
    public string $filtroProcessos = 'todos';

    // Mensagens
    public string $novaMensagem   = '';
    public string $msgProcessoId  = '';

    // PIX
    public bool   $modalPix       = false;
    public int    $pixParcelaId   = 0;
    public string $pixPayload     = '';
    public string $pixQrUrl       = '';
    public float  $pixValor       = 0;
    public string $pixDescricao   = '';
    public bool   $pixPago        = false;

    // Upload de documento pelo cliente
    public bool   $modalUpload     = false;
    public string $uploadTitulo    = '';
    public string $uploadDescricao = '';
    public        $uploadArquivo   = null;

    public function mount(): void
    {
        $id = Session::get('portal_pessoa_id');
        if (! $id) {
            $this->redirect(route('portal.login'));
            return;
        }
        $this->pessoa = Pessoa::find($id);
        if (! $this->pessoa) {
            $this->redirect(route('portal.login'));
        }
    }

    // ── Navegação ────────────────────────────────────────────────

    public function trocarAba(string $aba): void
    {
        $this->aba = $aba;
        $this->processoAberto = null;
    }

    public function setFiltroProcessos(string $filtro): void
    {
        $this->filtroProcessos = $filtro;
        $this->processoAberto  = null;
    }

    public function abrirProcesso(int $id): void
    {
        $processo = Processo::where('id', $id)
            ->where('cliente_id', $this->pessoa->id)
            ->first();

        if ($processo) {
            $this->processoAberto = $id;
            $this->aba = 'processos';
        }
    }

    public function fecharProcesso(): void
    {
        $this->processoAberto = null;
    }

    // ── Mensagens ────────────────────────────────────────────────

    public function enviarMensagem(): void
    {
        $this->novaMensagem = trim($this->novaMensagem);
        if (! $this->novaMensagem) return;

        DB::table('portal_mensagens')->insert([
            'pessoa_id'       => $this->pessoa->id,
            'processo_id'     => $this->msgProcessoId ?: null,
            'usuario_id'      => null,
            'mensagem'        => $this->novaMensagem,
            'de'              => 'cliente',
            'lida_escritorio' => false,
            'lida_cliente'    => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $this->novaMensagem = '';
    }

    // ── PIX ──────────────────────────────────────────────────────

    public function abrirPix(int $parcelaId): void
    {
        if (! PixService::configurado()) return;

        $parcela = DB::table('honorario_parcelas as hp')
            ->join('honorarios as h', 'h.id', '=', 'hp.honorario_id')
            ->where('hp.id', $parcelaId)
            ->where('h.cliente_id', $this->pessoa->id)
            ->whereIn('hp.status', ['pendente', 'atrasado'])
            ->select('hp.id', 'hp.numero_parcela', 'hp.valor', 'hp.vencimento', 'h.descricao')
            ->first();

        if (! $parcela) return;

        $descricao = 'Honorarios ' . ($parcela->numero_parcela ? $parcela->numero_parcela.'a parcela' : '');

        $this->pixParcelaId = $parcela->id;
        $this->pixValor     = (float) $parcela->valor;
        $this->pixDescricao = $descricao;
        $this->pixPago      = false;

        $payload = PixService::gerar(
            chave:     config('services.pix.chave'),
            nome:      config('services.pix.nome', 'ESCRITORIO'),
            cidade:    config('services.pix.cidade', 'SAO PAULO'),
            valor:     $this->pixValor,
            descricao: $descricao,
            txid:      'PARC' . $parcela->id,
        );

        $this->pixPayload = $payload;
        $this->pixQrUrl   = PixService::qrCodeUrl($payload);
        $this->modalPix   = true;
    }

    public function confirmarPagamentoPix(): void
    {
        DB::table('portal_mensagens')->insert([
            'pessoa_id'       => $this->pessoa->id,
            'processo_id'     => null,
            'usuario_id'      => null,
            'mensagem'        => '💰 Realizei o pagamento via PIX no valor de R$ ' . number_format($this->pixValor, 2, ',', '.') . ' (' . $this->pixDescricao . '). Por favor, confirme o recebimento.',
            'de'              => 'cliente',
            'lida_escritorio' => false,
            'lida_cliente'    => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $this->pixPago    = true;
        $this->modalPix   = false;

        // Switch to messages tab so client sees their message
        $this->aba = 'mensagens';
    }

    // ── Upload de documento ───────────────────────────────────────

    public function abrirUpload(): void
    {
        $this->uploadTitulo    = '';
        $this->uploadDescricao = '';
        $this->uploadArquivo   = null;
        $this->resetErrorBag();
        $this->modalUpload = true;
    }

    public function fecharUpload(): void
    {
        $this->modalUpload   = false;
        $this->uploadArquivo = null;
        $this->resetErrorBag();
    }

    public function enviarDocumento(): void
    {
        $this->validate([
            'uploadTitulo'  => 'required|string|max:200',
            'uploadArquivo' => 'required|file|max:20480',
        ], [
            'uploadTitulo.required'  => 'Informe um título para o documento.',
            'uploadArquivo.required' => 'Selecione um arquivo.',
            'uploadArquivo.max'      => 'O arquivo não pode passar de 20 MB.',
        ]);

        $caminho = $this->uploadArquivo->store('documentos', 'public');

        DB::table('documentos')->insert([
            'cliente_id'       => $this->pessoa->id,
            'processo_id'      => null,
            'tipo'             => 'documento_cliente',
            'titulo'           => $this->uploadTitulo,
            'descricao'        => $this->uploadDescricao ?: null,
            'arquivo'          => $caminho,
            'arquivo_original' => $this->uploadArquivo->getClientOriginalName(),
            'mime_type'        => $this->uploadArquivo->getMimeType(),
            'tamanho'          => $this->uploadArquivo->getSize(),
            'data_documento'   => now()->toDateString(),
            'portal_visivel'   => true,
            'uploaded_by'      => 'cliente',
            'tenant_id'        => $this->pessoa->tenant_id ?? null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Avisa o escritório via mensagem interna
        DB::table('portal_mensagens')->insert([
            'pessoa_id'       => $this->pessoa->id,
            'processo_id'     => null,
            'usuario_id'      => null,
            'mensagem'        => '📎 Enviei um documento: "' . $this->uploadTitulo . '". Por favor, confirme o recebimento.',
            'de'              => 'cliente',
            'lida_escritorio' => false,
            'lida_cliente'    => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $this->fecharUpload();
        $this->aba = 'documentos';
    }

    // ── Sair ─────────────────────────────────────────────────────

    public function sair(): void
    {
        Session::forget(['portal_pessoa_id', 'portal_pessoa_nome']);
        $this->redirect(route('portal.login'));
    }

    // ── Render ───────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $processosIds = Processo::where('cliente_id', $this->pessoa->id)->pluck('id');

        // ── Início ──
        $stats = [
            'total'   => $processosIds->count(),
            'ativos'  => Processo::where('cliente_id', $this->pessoa->id)->where('status', 'Ativo')->count(),
            'agenda'  => Agenda::whereIn('processo_id', $processosIds)->where('concluido', false)->where('data_hora', '>=', now())->count(),
            'msgs_nao_lidas' => DB::table('portal_mensagens')
                ->where('pessoa_id', $this->pessoa->id)
                ->where('de', 'escritorio')
                ->where('lida_cliente', false)
                ->count(),
        ];

        $proximosEventos = Agenda::with('processo')
            ->whereIn('processo_id', $processosIds)
            ->where('concluido', false)
            ->where('data_hora', '>=', now())
            ->orderBy('data_hora')
            ->take(5)
            ->get();

        $ultimosAndamentos = \App\Models\Andamento::publico()->with('processo')
            ->whereIn('processo_id', $processosIds)
            ->orderByDesc('data')
            ->take(6)
            ->get();

        $prazosProximos = \App\Models\Prazo::with('processo')
            ->whereIn('processo_id', $processosIds)
            ->where('status', 'aberto')
            ->where('data_prazo', '<=', now()->addDays(30))
            ->orderBy('data_prazo')
            ->take(5)
            ->get();

        // ── Processos ──
        $processos = Processo::with(['fase', 'risco', 'advogado'])
            ->where('cliente_id', $this->pessoa->id)
            ->when($this->filtroProcessos === 'judiciais',      fn ($q) => $q->whereNotNull('numero')->where('numero', '!=', ''))
            ->when($this->filtroProcessos === 'extrajudiciais', fn ($q) => $q->where(fn ($i) => $i->whereNull('numero')->orWhere('numero', '')))
            ->orderByDesc('data_distribuicao')
            ->get();

        // ── Detalhe do processo ──
        $processoDetalhe = null;
        $andamentos      = collect();
        $prazosProcesso  = collect();

        if ($this->processoAberto) {
            $processoDetalhe = Processo::with(['fase', 'risco', 'advogado', 'tipoAcao'])
                ->where('id', $this->processoAberto)
                ->where('cliente_id', $this->pessoa->id)
                ->first();

            if ($processoDetalhe) {
                $andamentos = \App\Models\Andamento::publico()
                    ->where('processo_id', $this->processoAberto)
                    ->orderByDesc('data')
                    ->get();

                $prazosProcesso = \App\Models\Prazo::where('processo_id', $this->processoAberto)
                    ->where('status', 'aberto')
                    ->orderBy('data_prazo')
                    ->get();

                // Documentos agrupados por andamento_id (apenas os que têm andamento_id)
                $docsAndamentos = DB::table('documentos')
                    ->whereIn('andamento_id', $andamentos->pluck('id'))
                    ->whereNotNull('andamento_id')
                    ->select('andamento_id', 'id', 'titulo', 'arquivo', 'arquivo_original', 'mime_type')
                    ->get()
                    ->groupBy('andamento_id');
            }
        }

        // ── Documentos ──
        $documentos = collect();
        if ($this->aba === 'documentos') {
            $documentos = DB::table('documentos as d')
                ->leftJoin('processos as pr', 'pr.id', '=', 'd.processo_id')
                ->where('d.portal_visivel', true)
                ->where(function ($q) use ($processosIds) {
                    $q->whereIn('d.processo_id', $processosIds)
                      ->orWhere('d.cliente_id', $this->pessoa->id);
                })
                ->select('d.id', 'd.titulo', 'd.tipo', 'd.descricao', 'd.arquivo', 'd.arquivo_original',
                         'd.mime_type', 'd.tamanho', 'd.data_documento', 'd.created_at',
                         'd.uploaded_by', 'pr.numero as processo_numero')
                ->orderByDesc('d.created_at')
                ->get();
        }

        // ── Honorários ──
        $honorarios = collect();
        if ($this->aba === 'honorarios') {
            $honorarios = DB::table('honorario_parcelas as hp')
                ->join('honorarios as h', 'h.id', '=', 'hp.honorario_id')
                ->leftJoin('processos as pr', 'pr.id', '=', 'h.processo_id')
                ->where('h.cliente_id', $this->pessoa->id)
                ->select(
                    'hp.id', 'hp.numero_parcela', 'hp.valor', 'hp.vencimento',
                    'hp.status', 'hp.data_pagamento',
                    'h.descricao as contrato', 'h.tipo as contrato_tipo',
                    'pr.numero as processo_numero'
                )
                ->orderByDesc('hp.vencimento')
                ->get();
        }

        // ── Financeiro (lançamentos do novo módulo) ──
        $lancamentosFinanceiro = collect();
        $resumoFinanceiro      = ['a_receber' => 0, 'recebido' => 0, 'atrasado' => 0];
        if ($this->aba === 'financeiro') {
            $lancamentosFinanceiro = FinanceiroLancamento::with(['contrato'])
                ->where('cliente_id', $this->pessoa->id)
                ->whereNotIn('status', ['cancelado'])
                ->orderBy('vencimento')
                ->get();
            $resumoFinanceiro = [
                'a_receber' => $lancamentosFinanceiro->whereIn('status', ['previsto', 'atrasado'])->sum('valor'),
                'recebido'  => $lancamentosFinanceiro->where('status', 'recebido')->sum('valor_pago'),
                'atrasado'  => $lancamentosFinanceiro->where('status', 'atrasado')->sum('valor'),
            ];
        }

        // ── Mensagens ──
        $mensagens = collect();
        if ($this->aba === 'mensagens') {
            $mensagens = DB::table('portal_mensagens as m')
                ->leftJoin('usuarios as u', 'u.id', '=', 'm.usuario_id')
                ->leftJoin('processos as pr', 'pr.id', '=', 'm.processo_id')
                ->where('m.pessoa_id', $this->pessoa->id)
                ->select('m.*', 'u.nome as usuario_nome', 'pr.numero as processo_numero')
                ->orderBy('m.created_at')
                ->get();

            // Marca mensagens do escritório como lidas
            DB::table('portal_mensagens')
                ->where('pessoa_id', $this->pessoa->id)
                ->where('de', 'escritorio')
                ->where('lida_cliente', false)
                ->update(['lida_cliente' => true]);
        }

        $processosFiltro = $processos->where('status', 'Ativo');

        $docsAndamentos  = $docsAndamentos  ?? collect();

        return view('livewire.portal.dashboard', compact(
            'stats', 'proximosEventos', 'ultimosAndamentos', 'prazosProximos',
            'processos', 'processoDetalhe', 'andamentos', 'prazosProcesso', 'docsAndamentos',
            'documentos', 'honorarios', 'mensagens', 'processosFiltro',
            'lancamentosFinanceiro', 'resumoFinanceiro'
        ))->layout('portal.layout');
    }
}
