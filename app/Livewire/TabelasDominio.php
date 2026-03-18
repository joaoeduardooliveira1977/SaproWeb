<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TabelasDominio extends Component
{
    public ?string $tabelaAtiva = null;

    // Form
    public bool   $modal      = false;
    public ?int   $registroId = null;
    public string $codigo     = '';
    public string $descricao  = '';
    public string $ordem      = '';
    public string $cor_hex    = '#1a3a5c';
    public bool   $ativo      = true;

    // ── Config das tabelas simples ────────────────
    public static function config(): array
    {
        return [
            'fases'           => ['label' => 'Fases do Processo',  'temOrdem' => true,  'temCor' => false, 'temAtivo' => true],
            'graus_risco'     => ['label' => 'Graus de Risco',      'temOrdem' => false, 'temCor' => true,  'temAtivo' => false],
            'tipos_acao'      => ['label' => 'Tipos de Ação',       'temOrdem' => false, 'temCor' => false, 'temAtivo' => true],
            'tipos_processo'  => ['label' => 'Tipos de Processo',   'temOrdem' => false, 'temCor' => false, 'temAtivo' => true],
            'assuntos'        => ['label' => 'Assuntos',             'temOrdem' => false, 'temCor' => false, 'temAtivo' => true],
            'reparticoes'     => ['label' => 'Repartições / Fóruns','temOrdem' => false, 'temCor' => false, 'temAtivo' => true],
            'secretarias'     => ['label' => 'Secretarias',         'temOrdem' => false, 'temCor' => false, 'temAtivo' => true],
        ];
    }

    public function abrirTabela(string $tabela): void
    {
        if ($this->tabelaAtiva === $tabela) {
            $this->tabelaAtiva = null;
        } else {
            $this->tabelaAtiva = $tabela;
            $this->fecharModal();
        }
    }

    public function abrirModal(?int $id = null): void
    {
        $this->resetForm();
        $this->registroId = $id;
        $this->modal      = true;

        if ($id) {
            $row = DB::table($this->tabelaAtiva)->find($id);
            $this->codigo   = $row->codigo   ?? '';
            $this->descricao= $row->descricao ?? '';
            $this->ordem    = isset($row->ordem)   ? (string) $row->ordem   : '';
            $this->cor_hex  = $row->cor_hex ?? '#1a3a5c';
            $this->ativo    = (bool) ($row->ativo ?? true);
        }
    }

    public function fecharModal(): void
    {
        $this->modal = false;
        $this->resetForm();
    }

    public function salvar(): void
    {
        $this->validate([
            'descricao' => 'required|string|max:200',
            'cor_hex'   => 'nullable|string|max:7',
        ], [
            'descricao.required' => 'A descrição é obrigatória.',
        ]);

        $cfg  = self::config()[$this->tabelaAtiva] ?? [];
        $dados = ['descricao' => $this->descricao];

        // codigo: upper do inicio da descricao se em branco
        $dados['codigo'] = $this->codigo ?: strtoupper(substr(preg_replace('/\s+/', '_', $this->descricao), 0, 20));

        if ($cfg['temOrdem']  ?? false) {
            $dados['ordem'] = $this->ordem !== '' ? (int) $this->ordem : null;
        }
        if ($cfg['temCor']    ?? false) {
            $dados['cor_hex'] = $this->cor_hex ?: '#1a3a5c';
        }
        if ($cfg['temAtivo']  ?? false) {
            $dados['ativo'] = $this->ativo;
        }

        if ($this->registroId) {
            DB::table($this->tabelaAtiva)->where('id', $this->registroId)->update(
                array_merge($dados, ['updated_at' => now()])
            );
            $msg = "Registro atualizado.";
        } else {
            DB::table($this->tabelaAtiva)->insert(
                array_merge($dados, ['created_at' => now(), 'updated_at' => now()])
            );
            $msg = "Registro adicionado.";
        }

        $this->fecharModal();
        $this->dispatch('toast', tipo: 'success', msg: $msg);
    }

    public function excluir(int $id): void
    {
        DB::table($this->tabelaAtiva)->where('id', $id)->delete();
        $this->dispatch('toast', tipo: 'success', msg: "Registro excluído.");
    }

    public function toggleAtivo(int $id): void
    {
        $row = DB::table($this->tabelaAtiva)->find($id);
        DB::table($this->tabelaAtiva)->where('id', $id)->update([
            'ativo'      => ! $row->ativo,
            'updated_at' => now(),
        ]);
    }

    private function resetForm(): void
    {
        $this->registroId = null;
        $this->codigo = $this->descricao = $this->ordem = '';
        $this->cor_hex = '#1a3a5c';
        $this->ativo = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $config  = self::config();
        $contagens = [];
        foreach (array_keys($config) as $t) {
            $contagens[$t] = DB::table($t)->count();
        }

        $registros = [];
        if ($this->tabelaAtiva && isset($config[$this->tabelaAtiva])) {
            $q = DB::table($this->tabelaAtiva)->orderBy(
                ($config[$this->tabelaAtiva]['temOrdem'] ?? false) ? 'ordem' : 'descricao'
            );
            $registros = $q->get();
        }

        return view('livewire.tabelas-dominio', compact('config', 'contagens', 'registros'));
    }
}
