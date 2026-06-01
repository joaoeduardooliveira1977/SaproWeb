<?php

namespace App\Livewire\Configuracoes;

use App\Models\{Auditoria, Pessoa, Processo, Documento, FinanceiroLancamento};
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Privacidade extends Component
{
    // ── Exportar dados ────────────────────────────────────────────────
    public ?int  $exportarClienteId  = null;
    public string $exportarFormato   = 'json'; // json | pdf
    public bool  $exportando         = false;

    // ── Anonimizar/Excluir ────────────────────────────────────────────
    public ?int  $acaoClienteId      = null;
    public string $acaoTipo          = 'anonimizar'; // anonimizar | excluir
    public bool  $modalConfirmar     = false;
    public string $confirmacaoTexto  = '';

    // ── Busca ─────────────────────────────────────────────────────────
    public string $buscaExportar = '';
    public string $buscaAcao    = '';

    private function tid(): int
    {
        return auth('usuarios')->user()->tenant_id;
    }

    #[Computed]
    public function clientesExportar(): \Illuminate\Database\Eloquent\Collection
    {
        return Pessoa::where('tenant_id', $this->tid())
            ->when($this->buscaExportar, fn($q) => $q->busca($this->buscaExportar))
            ->orderBy('nome')
            ->limit(30)
            ->get(['id', 'nome', 'cpf_cnpj', 'email']);
    }

    #[Computed]
    public function clientesAcao(): \Illuminate\Database\Eloquent\Collection
    {
        return Pessoa::where('tenant_id', $this->tid())
            ->when($this->buscaAcao, fn($q) => $q->busca($this->buscaAcao))
            ->orderBy('nome')
            ->limit(30)
            ->get(['id', 'nome', 'cpf_cnpj', 'email']);
    }

    public function exportarDados(): void
    {
        $this->validate([
            'exportarClienteId' => 'required|integer',
            'exportarFormato'   => 'required|in:json,pdf',
        ], [
            'exportarClienteId.required' => 'Selecione um cliente.',
        ]);

        $pessoa = Pessoa::where('tenant_id', $this->tid())
            ->findOrFail($this->exportarClienteId);

        $dados = $this->coletarDadosPessoa($pessoa);

        // Registra auditoria
        auth('usuarios')->user()->registrarAuditoria(
            'LGPD - Exportar Dados',
            'pessoas',
            $pessoa->id,
            null,
            ['cliente' => $pessoa->nome, 'formato' => $this->exportarFormato]
        );

        // Gera download via evento JS
        $this->dispatch('download-lgpd', dados: $dados, nome: "dados_{$pessoa->id}", formato: $this->exportarFormato);

        $this->dispatch('toast', type: 'sucesso', msg: 'Dados exportados com sucesso.');
    }

    public function abrirConfirmacao(): void
    {
        $this->validate([
            'acaoClienteId' => 'required|integer',
            'acaoTipo'      => 'required|in:anonimizar,excluir',
        ], [
            'acaoClienteId.required' => 'Selecione um cliente.',
        ]);

        $this->confirmacaoTexto = '';
        $this->modalConfirmar   = true;
    }

    public function executarAcao(): void
    {
        $acao   = $this->acaoTipo;
        $pessoa = Pessoa::where('tenant_id', $this->tid())
            ->findOrFail($this->acaoClienteId);

        $esperado = $acao === 'excluir' ? 'EXCLUIR' : 'CONFIRMAR';

        if (strtoupper(trim($this->confirmacaoTexto)) !== $esperado) {
            $this->addError('confirmacaoTexto', "Digite \"{$esperado}\" para confirmar.");
            return;
        }

        if ($acao === 'anonimizar') {
            $this->anonimizarPessoa($pessoa);
            $mensagem = 'Dados anonimizados com sucesso.';
        } else {
            $this->excluirPessoa($pessoa);
            $mensagem = 'Dados excluídos permanentemente.';
        }

        auth('usuarios')->user()->registrarAuditoria(
            'LGPD - ' . ucfirst($acao),
            'pessoas',
            $pessoa->id,
            ['nome' => $pessoa->nome, 'cpf_cnpj' => $pessoa->cpf_cnpj],
            ['acao' => $acao, 'executado_em' => now()->toISOString()]
        );

        $this->modalConfirmar   = false;
        $this->acaoClienteId    = null;
        $this->confirmacaoTexto = '';

        unset($this->clientesAcao, $this->clientesExportar);

        $this->dispatch('toast', type: 'sucesso', msg: $mensagem);
    }

    private function anonimizarPessoa(Pessoa $pessoa): void
    {
        $pessoa->update([
            'nome'        => 'Cliente Anonimizado',
            'cpf_cnpj'    => str_repeat('0', strlen($pessoa->cpf_cnpj ?? '11')),
            'rg'          => null,
            'email'       => null,
            'telefone'    => null,
            'celular'     => null,
            'data_nascimento' => null,
            'logradouro'  => null,
            'cidade'      => null,
            'estado'      => null,
            'cep'         => null,
            'observacoes' => null,
        ]);
    }

    private function excluirPessoa(Pessoa $pessoa): void
    {
        DB::transaction(function () use ($pessoa) {
            // Remove relacionamentos em cascata que não sejam deletados automaticamente
            Documento::where('tenant_id', $this->tid())
                ->where('cliente_id', $pessoa->id)
                ->delete();

            FinanceiroLancamento::where('tenant_id', $this->tid())
                ->where('cliente_id', $pessoa->id)
                ->delete();

            $pessoa->delete();
        });
    }

    private function coletarDadosPessoa(Pessoa $pessoa): array
    {
        $processos = Processo::where('tenant_id', $this->tid())
            ->where('cliente_id', $pessoa->id)
            ->with(['andamentos' => fn($q) => $q->orderByDesc('data')->limit(50)])
            ->get();

        $documentos = Documento::where('tenant_id', $this->tid())
            ->where('cliente_id', $pessoa->id)
            ->get(['id', 'titulo', 'tipo', 'data_documento', 'arquivo_original', 'created_at']);

        $financeiro = FinanceiroLancamento::where('tenant_id', $this->tid())
            ->where('cliente_id', $pessoa->id)
            ->orderByDesc('data_vencimento')
            ->get(['id', 'descricao', 'valor', 'tipo', 'status', 'data_vencimento', 'data_pagamento']);

        return [
            'exportado_em'   => now()->toDateTimeString(),
            'sistema'        => 'Software Jurídico',
            'dados_cadastrais' => [
                'nome'           => $pessoa->nome,
                'tipo_pessoa'    => $pessoa->tipo_pessoa,
                'cpf_cnpj'       => $pessoa->cpf_cnpj,
                'rg'             => $pessoa->rg,
                'data_nascimento'=> $pessoa->data_nascimento?->format('d/m/Y'),
                'email'          => $pessoa->email,
                'telefone'       => $pessoa->telefone,
                'celular'        => $pessoa->celular,
                'endereco'       => trim("{$pessoa->logradouro}, {$pessoa->cidade}/{$pessoa->estado} {$pessoa->cep}"),
                'oab'            => $pessoa->oab,
            ],
            'processos'      => $processos->map(fn($p) => [
                'numero'      => $p->numero,
                'titulo'      => $p->titulo ?? '',
                'status'      => $p->status,
                'data_entrada'=> $p->data_entrada?->format('d/m/Y'),
                'andamentos'  => $p->andamentos->map(fn($a) => [
                    'data'      => $a->data?->format('d/m/Y'),
                    'descricao' => $a->descricao,
                ])->toArray(),
            ])->toArray(),
            'documentos'     => $documentos->map(fn($d) => [
                'titulo'         => $d->titulo,
                'tipo'           => $d->tipo,
                'data_documento' => $d->data_documento?->format('d/m/Y'),
                'arquivo'        => $d->arquivo_original,
            ])->toArray(),
            'historico_financeiro' => $financeiro->map(fn($f) => [
                'descricao'      => $f->descricao,
                'valor'          => 'R$ ' . number_format($f->valor, 2, ',', '.'),
                'tipo'           => $f->tipo,
                'status'         => $f->status,
                'vencimento'     => $f->data_vencimento?->format('d/m/Y'),
                'pagamento'      => $f->data_pagamento?->format('d/m/Y'),
            ])->toArray(),
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.configuracoes.privacidade', [
            'clientesExportar' => $this->clientesExportar,
            'clientesAcao'     => $this->clientesAcao,
        ])->extends('layouts.app')->section('content');
    }
}
