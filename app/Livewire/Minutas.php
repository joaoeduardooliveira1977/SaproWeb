<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuta;

class Minutas extends Component
{
    // Formulário
    public bool   $mostrarForm  = false;
    public ?int   $editandoId   = null;
    public string $titulo       = '';
    public string $categoria    = 'outros';
    public string $corpo        = '';
    public bool   $ativo        = true;

    // Busca
    public string $busca = '';

    public static array $placeholders = [
        '{{processo_numero}}'            => 'Número do processo',
        '{{processo_vara}}'              => 'Vara',
        '{{processo_data_distribuicao}}' => 'Data de distribuição',
        '{{processo_tipo_acao}}'         => 'Tipo de ação',
        '{{processo_fase}}'              => 'Fase atual',
        '{{processo_valor_causa}}'       => 'Valor da causa',
        '{{parte_contraria}}'            => 'Parte contrária',
        '{{cliente_nome}}'               => 'Nome do cliente',
        '{{cliente_cpf_cnpj}}'           => 'CPF/CNPJ do cliente',
        '{{cliente_rg}}'                 => 'RG do cliente',
        '{{cliente_email}}'              => 'E-mail do cliente',
        '{{cliente_telefone}}'           => 'Telefone do cliente',
        '{{cliente_celular}}'            => 'Celular do cliente',
        '{{cliente_endereco}}'           => 'Endereço completo',
        '{{cliente_cidade}}'             => 'Cidade do cliente',
        '{{cliente_estado}}'             => 'Estado do cliente',
        '{{advogado_nome}}'              => 'Nome do advogado',
        '{{advogado_oab}}'               => 'OAB do advogado',
        '{{juiz_nome}}'                  => 'Nome do juiz',
        '{{data_atual}}'                 => 'Data por extenso (ex: 14 de março de 2026)',
        '{{data_atual_curta}}'           => 'Data curta (ex: 14/03/2026)',
    ];

    protected function rules(): array
    {
        return [
            'titulo'    => 'required|min:3|max:200',
            'categoria' => 'required',
            'corpo'     => 'required|min:10',
        ];
    }

    // ── Formulário ─────────────────────────────────────────────────

    public function novo(): void
    {
        $this->resetForm();
        $this->mostrarForm = true;
        $this->editandoId  = null;
    }

    public function editar(int $id): void
    {
        $m = Minuta::findOrFail($id);
        $this->editandoId = $id;
        $this->titulo     = $m->titulo;
        $this->categoria  = $m->categoria;
        $this->corpo      = $m->corpo;
        $this->ativo      = $m->ativo;
        $this->mostrarForm = true;
    }

    public function salvar(): void
    {
        $this->validate();

        $data = [
            'titulo'    => trim($this->titulo),
            'categoria' => $this->categoria,
            'corpo'     => $this->corpo,
            'ativo'     => $this->ativo,
        ];

        if ($this->editandoId) {
            Minuta::findOrFail($this->editandoId)->update($data);
        } else {
            Minuta::create($data);
        }

        $this->resetForm();
        $this->mostrarForm = false;
    }

    public function cancelar(): void
    {
        $this->resetForm();
        $this->mostrarForm = false;
    }

    public function excluir(int $id): void
    {
        Minuta::findOrFail($id)->delete();
    }

    public function toggleAtivo(int $id): void
    {
        $m = Minuta::findOrFail($id);
        $m->update(['ativo' => !$m->ativo]);
    }

    private function resetForm(): void
    {
        $this->editandoId = null;
        $this->titulo     = '';
        $this->categoria  = 'outros';
        $this->corpo      = '';
        $this->ativo      = true;
        $this->resetValidation();
    }

    // ── Render ─────────────────────────────────────────────────────

    public function render()
    {
        $minutas = Minuta::query()
            ->when($this->busca, fn($q) => $q->where('titulo', 'ilike', '%' . $this->busca . '%'))
            ->orderBy('titulo')
            ->get();

        return view('livewire.minutas', [
            'minutas'    => $minutas,
            'categorias' => Minuta::categorias(),
        ]);
    }
}
