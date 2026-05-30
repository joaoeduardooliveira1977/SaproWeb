<?php

namespace App\Livewire\MasterAdmin;

use App\Jobs\EnviarEmailComunicado;
use App\Models\{Comunicado, MasterAdminLog, Tenant};
use Livewire\Component;

class Comunicados extends Component
{
    // ── Filtros ─────────────────────────────────────────────────
    public string $filtroTipo      = '';
    public string $filtroPrioridade = '';
    public string $filtroStatus    = '';

    // ── Modal ───────────────────────────────────────────────────
    public bool   $modalAberto = false;
    public bool   $preview     = false;
    public ?int   $editandoId  = null;

    public string $titulo      = '';
    public string $mensagem    = '';
    public string $tipo        = 'informativo';
    public string $prioridade  = 'banner';
    public string $destino     = 'todos';
    public ?int   $tenantAlvo  = null;
    public string $planoAlvo   = '';
    public string $dataInicio  = '';
    public string $dataFim     = '';
    public bool   $ativo       = true;

    public function mount(): void
    {
        $this->dataInicio = now()->format('Y-m-d\TH:i');
    }

    // ── Abrir / fechar ──────────────────────────────────────────

    public function novo(): void
    {
        $this->resetForm();
        $this->editandoId  = null;
        $this->modalAberto = true;
        $this->preview     = false;
    }

    public function editar(int $id): void
    {
        $c = Comunicado::findOrFail($id);
        $this->editandoId  = $c->id;
        $this->titulo      = $c->titulo;
        $this->mensagem    = $c->mensagem;
        $this->tipo        = $c->tipo;
        $this->prioridade  = $c->prioridade;
        $this->destino     = $c->destino;
        $this->tenantAlvo  = $c->tenant_id;
        $this->planoAlvo   = $c->plano ?? '';
        $this->dataInicio  = $c->data_inicio->format('Y-m-d\TH:i');
        $this->dataFim     = $c->data_fim?->format('Y-m-d\TH:i') ?? '';
        $this->ativo       = $c->ativo;
        $this->modalAberto = true;
        $this->preview     = false;
        $this->resetValidation();
    }

    public function fechar(): void
    {
        $this->modalAberto = false;
        $this->preview     = false;
        $this->resetForm();
    }

    // ── Salvar ──────────────────────────────────────────────────

    public function salvar(): void
    {
        $this->validate([
            'titulo'     => 'required|max:200',
            'mensagem'   => 'required',
            'tipo'       => 'required|in:' . implode(',', array_keys(Comunicado::$tipos)),
            'prioridade' => 'required|in:banner,modal,notificacao',
            'destino'    => 'required|in:todos,tenant_especifico,plano_especifico',
            'tenantAlvo' => 'required_if:destino,tenant_especifico|nullable|exists:tenants,id',
            'planoAlvo'  => 'required_if:destino,plano_especifico|nullable|in:demo,starter,pro,enterprise',
            'dataInicio' => 'required|date',
            'dataFim'    => 'nullable|date|after:dataInicio',
        ], [
            'titulo.required'     => 'Informe o título.',
            'mensagem.required'   => 'Informe a mensagem.',
            'tenantAlvo.required_if' => 'Selecione o tenant.',
            'planoAlvo.required_if'  => 'Selecione o plano.',
            'dataFim.after'       => 'A data fim deve ser após a data de início.',
        ]);

        $dados = [
            'titulo'      => trim($this->titulo),
            'mensagem'    => trim($this->mensagem),
            'tipo'        => $this->tipo,
            'prioridade'  => $this->prioridade,
            'destino'     => $this->destino,
            'tenant_id'   => $this->destino === 'tenant_especifico' ? $this->tenantAlvo : null,
            'plano'       => $this->destino === 'plano_especifico' ? $this->planoAlvo : null,
            'data_inicio' => $this->dataInicio,
            'data_fim'    => $this->dataFim ?: null,
            'ativo'       => $this->ativo,
            'criado_por'  => auth('usuarios')->id(),
        ];

        if ($this->editandoId) {
            $comunicado = Comunicado::findOrFail($this->editandoId);
            $comunicado->update($dados);
            $acao = 'comunicado_editado';
        } else {
            $comunicado = Comunicado::create($dados);
            $acao = 'comunicado_criado';

            // Dispara e-mail para manutenções
            if (in_array($this->tipo, ['manutencao_emergencial', 'manutencao_programada']) && $this->ativo) {
                EnviarEmailComunicado::dispatch($comunicado);
            }
        }

        MasterAdminLog::registrar($acao, null, null, "Comunicado: {$this->titulo}");

        $this->fechar();
        $this->dispatch('toast', message: 'Comunicado salvo com sucesso.', type: 'success');
    }

    public function toggleAtivo(int $id): void
    {
        $c = Comunicado::findOrFail($id);
        $c->update(['ativo' => !$c->ativo]);
        $this->dispatch('toast', message: $c->fresh()->ativo ? 'Comunicado ativado.' : 'Comunicado desativado.', type: 'success');
    }

    public function excluir(int $id): void
    {
        $c = Comunicado::findOrFail($id);
        MasterAdminLog::registrar('comunicado_excluido', null, null, "Comunicado #{$id}: {$c->titulo}");
        $c->delete();
        $this->dispatch('toast', message: 'Comunicado excluído.', type: 'success');
    }

    private function resetForm(): void
    {
        $this->titulo     = '';
        $this->mensagem   = '';
        $this->tipo       = 'informativo';
        $this->prioridade = 'banner';
        $this->destino    = 'todos';
        $this->tenantAlvo = null;
        $this->planoAlvo  = '';
        $this->dataInicio = now()->format('Y-m-d\TH:i');
        $this->dataFim    = '';
        $this->ativo      = true;
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        $comunicados = Comunicado::query()
            ->when($this->filtroTipo,       fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroPrioridade, fn($q) => $q->where('prioridade', $this->filtroPrioridade))
            ->when($this->filtroStatus !== '', fn($q) => $q->where('ativo', (bool) $this->filtroStatus))
            ->withCount('leituras')
            ->orderByDesc('created_at')
            ->get();

        $tenants = Tenant::orderBy('nome')->get(['id', 'nome']);

        return view('livewire.master-admin.comunicados', compact('comunicados', 'tenants'))
            ->extends('layouts.master-admin')
            ->section('content');
    }
}
