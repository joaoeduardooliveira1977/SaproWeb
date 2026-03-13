<?php

namespace App\Livewire\Portal;

use App\Models\{Pessoa, Processo, Andamento, Agenda};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PortalDashboard extends Component
{
    public ?Pessoa $pessoa       = null;
    public string  $aba          = 'inicio';
    public ?int    $processoAberto = null;  // processo em detalhe

    // Mensagens
    public string $novaMensagem   = '';
    public string $msgProcessoId  = '';

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

        // ── Processos ──
        $processos = Processo::with(['fase', 'risco', 'advogado'])
            ->where('cliente_id', $this->pessoa->id)
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
                $andamentos = \App\Models\Andamento::where('processo_id', $this->processoAberto)
                    ->orderByDesc('data')
                    ->get();

                $prazosProcesso = \App\Models\Prazo::where('processo_id', $this->processoAberto)
                    ->where('status', 'aberto')
                    ->orderBy('data_prazo')
                    ->get();
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
                         'pr.numero as processo_numero')
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

        return view('livewire.portal.dashboard', compact(
            'stats', 'proximosEventos',
            'processos', 'processoDetalhe', 'andamentos', 'prazosProcesso',
            'documentos', 'honorarios', 'mensagens', 'processosFiltro'
        ))->layout('portal.layout');
    }
}
