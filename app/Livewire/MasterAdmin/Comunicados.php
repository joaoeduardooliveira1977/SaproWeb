<?php

namespace App\Livewire\MasterAdmin;

use App\Models\Comunicado;
use App\Models\MasterAdminLog;
use Livewire\Component;

class Comunicados extends Component
{
    public bool   $modalAberto = false;
    public ?int   $editandoId  = null;

    public string $titulo    = '';
    public string $mensagem  = '';
    public string $tipo      = 'info';
    public bool   $ativo     = true;
    public string $expiraEm  = '';

    public function abrirNovo(): void
    {
        $this->reset(['editandoId','titulo','mensagem','tipo','ativo','expiraEm']);
        $this->ativo      = true;
        $this->tipo       = 'info';
        $this->modalAberto = true;
        $this->resetValidation();
    }

    public function editar(int $id): void
    {
        $c = Comunicado::findOrFail($id);
        $this->editandoId = $c->id;
        $this->titulo     = $c->titulo;
        $this->mensagem   = $c->mensagem;
        $this->tipo       = $c->tipo;
        $this->ativo      = $c->ativo;
        $this->expiraEm   = $c->expira_em?->format('Y-m-d\TH:i') ?? '';
        $this->modalAberto = true;
        $this->resetValidation();
    }

    public function salvar(): void
    {
        $this->validate([
            'titulo'   => 'required|max:200',
            'mensagem' => 'required',
            'tipo'     => 'required|in:info,aviso,critico',
            'expiraEm' => 'nullable|date',
        ]);

        $dados = [
            'titulo'     => trim($this->titulo),
            'mensagem'   => trim($this->mensagem),
            'tipo'       => $this->tipo,
            'ativo'      => $this->ativo,
            'expira_em'  => $this->expiraEm ?: null,
            'criado_por' => auth('usuarios')->id(),
        ];

        if ($this->editandoId) {
            Comunicado::findOrFail($this->editandoId)->update($dados);
            MasterAdminLog::registrar('comunicado_editado', null, null, "Comunicado #{$this->editandoId}: {$this->titulo}");
        } else {
            $c = Comunicado::create($dados);
            MasterAdminLog::registrar('comunicado_criado', null, null, "Comunicado #{$c->id}: {$this->titulo}");
        }

        $this->modalAberto = false;
        $this->dispatch('toast', message: 'Comunicado salvo com sucesso.', type: 'success');
    }

    public function toggleAtivo(int $id): void
    {
        $c = Comunicado::findOrFail($id);
        $c->update(['ativo' => !$c->ativo]);
        $status = $c->fresh()->ativo ? 'ativado' : 'desativado';
        MasterAdminLog::registrar("comunicado_{$status}", null, null, "Comunicado #{$id}: {$c->titulo}");
        $this->dispatch('toast', message: "Comunicado {$status}.", type: 'success');
    }

    public function excluir(int $id): void
    {
        $c = Comunicado::findOrFail($id);
        MasterAdminLog::registrar('comunicado_excluido', null, null, "Comunicado #{$id}: {$c->titulo}");
        $c->delete();
        $this->dispatch('toast', message: 'Comunicado excluído.', type: 'success');
    }

    public function fechar(): void
    {
        $this->modalAberto = false;
    }

    public function render(): \Illuminate\View\View
    {
        $comunicados = Comunicado::orderByDesc('created_at')->get();

        return view('livewire.master-admin.comunicados', compact('comunicados'))
            ->extends('layouts.master-admin')
            ->section('content');
    }
}
