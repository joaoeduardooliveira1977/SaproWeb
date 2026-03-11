<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Honorarios extends Component
{
    use WithPagination;

    // Filtros
    public string $busca = '';
    public string $filtroStatus = '';
    public string $filtroTipo = '';

    // Modal honorário
    public bool $modalHonorario = false;
    public bool $modalParcelas = false;
    public bool $modalPagamento = false;

    // Form honorário
    public ?int $honorarioId = null;
    public ?int $processo_id = null;
    public int $cliente_id = 0;
    public string $tipo = 'fixo_mensal';
    public string $descricao = '';
    public string $valor_contrato = '';
    public string $percentual_exito = '';
    public int $total_parcelas = 1;
    public string $data_inicio = '';
    public string $data_fim = '';
    public string $status = 'ativo';
    public string $observacoes = '';

    // Form pagamento
    public ?int $parcelaId = null;
    public string $data_pagamento = '';
    public string $valor_pago = '';
    public string $forma_pagamento = 'pix';
    public string $obs_pagamento = '';

    // Dados auxiliares
    public array $processos = [];
    public array $clientes = [];
    public ?array $parcelasModal = null;
    public string $honorarioNome = '';

    protected array $rules = [
        'cliente_id'        => 'required|integer|min:1',
        'tipo'              => 'required|in:fixo_mensal,exito,hora,ato_diligencia',
        'descricao'         => 'required|min:3',
        'valor_contrato'    => 'required|numeric|min:0',
        'percentual_exito'  => 'nullable|numeric|min:0|max:100',
        'total_parcelas'    => 'required|integer|min:1|max:360',
        'data_inicio'       => 'required|date',
        'data_fim'          => 'nullable|date',
        'status'            => 'required',
    ];

    public function mount(): void
    {
        $this->data_inicio = now()->format('Y-m-d');
        $this->data_pagamento = now()->format('Y-m-d');
        $this->carregarClientes();
    }

    private function carregarClientes(): void
    {
        $this->clientes = DB::select("
            SELECT p.id, p.nome
            FROM pessoas p
            JOIN pessoa_tipos pt ON pt.pessoa_id = p.id
            WHERE pt.tipo = 'Cliente' AND p.ativo = true
            ORDER BY p.nome
        ");
    }

    public function updatedClienteId(): void
    {
        if ($this->cliente_id) {
            $this->processos = DB::select("
                SELECT id, numero, vara FROM processos
                WHERE cliente_id = ? AND status = 'Ativo'
                ORDER BY numero
            ", [$this->cliente_id]);
        }
    }

    public function novoHonorario(): void
    {
        $this->reset(['honorarioId','processo_id','cliente_id','tipo','descricao',
            'valor_contrato','percentual_exito','total_parcelas','data_fim','observacoes','processos']);
        $this->tipo = 'fixo_mensal';
        $this->total_parcelas = 1;
        $this->status = 'ativo';
        $this->data_inicio = now()->format('Y-m-d');
        $this->modalHonorario = true;
    }

    public function editarHonorario(int $id): void
    {
        $h = DB::selectOne("SELECT * FROM honorarios WHERE id = ?", [$id]);
        if (!$h) return;

        $this->honorarioId     = $h->id;
        $this->cliente_id      = $h->cliente_id;
        $this->processo_id     = $h->processo_id;
        $this->tipo            = $h->tipo;
        $this->descricao       = $h->descricao;
        $this->valor_contrato  = $h->valor_contrato;
        $this->percentual_exito= $h->percentual_exito ?? '';
        $this->total_parcelas  = $h->total_parcelas;
        $this->data_inicio     = $h->data_inicio;
        $this->data_fim        = $h->data_fim ?? '';
        $this->status          = $h->status;
        $this->observacoes     = $h->observacoes ?? '';

        $this->updatedClienteId();
        $this->modalHonorario = true;
    }

    public function salvarHonorario(): void
    {
        $this->validate();

        $valor = (float) str_replace(',', '.', $this->valor_contrato);
        $perc  = $this->percentual_exito ? (float) str_replace(',', '.', $this->percentual_exito) : null;

        if ($this->honorarioId) {
            DB::update("
                UPDATE honorarios SET
                    cliente_id=?, processo_id=?, tipo=?, descricao=?, valor_contrato=?,
                    percentual_exito=?, total_parcelas=?, data_inicio=?, data_fim=?,
                    status=?, observacoes=?, updated_at=NOW()
                WHERE id=?
            ", [
                $this->cliente_id, $this->processo_id ?: null, $this->tipo, $this->descricao,
                $valor, $perc, $this->total_parcelas, $this->data_inicio,
                $this->data_fim ?: null, $this->status, $this->observacoes ?: null,
                $this->honorarioId
            ]);
        } else {
            $id = DB::selectOne("
                INSERT INTO honorarios (cliente_id, processo_id, tipo, descricao, valor_contrato,
                    percentual_exito, total_parcelas, data_inicio, data_fim, status, observacoes, created_at, updated_at)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW()) RETURNING id
            ", [
                $this->cliente_id, $this->processo_id ?: null, $this->tipo, $this->descricao,
                $valor, $perc, $this->total_parcelas, $this->data_inicio,
                $this->data_fim ?: null, $this->status, $this->observacoes ?: null
            ])->id;

            // Gera parcelas automaticamente
            $this->gerarParcelas($id, $valor, $this->total_parcelas, $this->data_inicio);
        }

        $this->modalHonorario = false;
        session()->flash('success', 'Honorário salvo com sucesso!');
    }

    private function gerarParcelas(int $honorarioId, float $valorTotal, int $total, string $dataInicio): void
    {
        $valorParcela = round($valorTotal / $total, 2);
        $data = Carbon::parse($dataInicio);

        for ($i = 1; $i <= $total; $i++) {
            DB::insert("
                INSERT INTO honorario_parcelas (honorario_id, numero_parcela, valor, vencimento, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'pendente', NOW(), NOW())
            ", [$honorarioId, $i, $valorParcela, $data->format('Y-m-d')]);

            $data->addMonth();
        }
    }

    public function verParcelas(int $id): void
    {
        $h = DB::selectOne("
            SELECT h.*, p.nome as cliente_nome, pr.numero as processo_numero
            FROM honorarios h
            JOIN pessoas p ON p.id = h.cliente_id
            LEFT JOIN processos pr ON pr.id = h.processo_id
            WHERE h.id = ?
        ", [$id]);

        $this->honorarioNome = $h->cliente_nome . ($h->processo_numero ? ' — ' . $h->processo_numero : '');

        $this->parcelasModal = DB::select("
            SELECT * FROM honorario_parcelas WHERE honorario_id = ? ORDER BY numero_parcela
        ", [$id]);

        // Atualiza atrasadas
        DB::update("
            UPDATE honorario_parcelas SET status = 'atrasado'
            WHERE honorario_id = ? AND status = 'pendente' AND vencimento < CURRENT_DATE
        ", [$id]);

        $this->parcelasModal = DB::select("
            SELECT * FROM honorario_parcelas WHERE honorario_id = ? ORDER BY numero_parcela
        ", [$id]);

        $this->modalParcelas = true;
    }

    public function abrirPagamento(int $parcelaId): void
    {
        $this->parcelaId = $parcelaId;
        $p = DB::selectOne("SELECT * FROM honorario_parcelas WHERE id = ?", [$parcelaId]);
        $this->valor_pago = $p->valor;
        $this->data_pagamento = now()->format('Y-m-d');
        $this->forma_pagamento = 'pix';
        $this->obs_pagamento = '';
        $this->modalPagamento = true;
    }

    public function registrarPagamento(): void
    {
        DB::update("
            UPDATE honorario_parcelas SET
                status = 'pago', data_pagamento = ?, valor_pago = ?,
                forma_pagamento = ?, observacoes = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            $this->data_pagamento, $this->valor_pago,
            $this->forma_pagamento, $this->obs_pagamento ?: null,
            $this->parcelaId
        ]);

        $this->modalPagamento = false;

        // Atualiza parcelas do modal
        $parcela = DB::selectOne("SELECT honorario_id FROM honorario_parcelas WHERE id = ?", [$this->parcelaId]);
        if ($parcela) $this->verParcelas($parcela->honorario_id);

        session()->flash('success', 'Pagamento registrado!');
    }

    public function excluirHonorario(int $id): void
    {
        DB::delete("DELETE FROM honorario_parcelas WHERE honorario_id = ?", [$id]);
        DB::delete("DELETE FROM honorarios WHERE id = ?", [$id]);
        session()->flash('success', 'Honorário excluído.');
    }

    public function render()
    {
        // Resumo financeiro
        $resumo = DB::selectOne("
            SELECT
                COALESCE(SUM(hp.valor), 0) as total_contratado,
                COALESCE(SUM(CASE WHEN hp.status = 'pago' THEN hp.valor_pago ELSE 0 END), 0) as total_recebido,
                COALESCE(SUM(CASE WHEN hp.status IN ('pendente','atrasado') THEN hp.valor ELSE 0 END), 0) as total_pendente,
                COALESCE(SUM(CASE WHEN hp.status = 'atrasado' THEN hp.valor ELSE 0 END), 0) as total_atrasado,
                COUNT(DISTINCT h.id) as total_contratos
            FROM honorarios h
            JOIN honorario_parcelas hp ON hp.honorario_id = h.id
            WHERE h.status = 'ativo'
        ");

        // Lista honorários
        $where = "WHERE 1=1";
        $params = [];

        if ($this->busca) {
            $where .= " AND (p.nome ILIKE ? OR h.descricao ILIKE ?)";
            $params[] = "%{$this->busca}%";
            $params[] = "%{$this->busca}%";
        }
        if ($this->filtroStatus) {
            $where .= " AND h.status = ?";
            $params[] = $this->filtroStatus;
        }
        if ($this->filtroTipo) {
            $where .= " AND h.tipo = ?";
            $params[] = $this->filtroTipo;
        }

        $honorarios = DB::select("
            SELECT h.*,
                p.nome as cliente_nome,
                pr.numero as processo_numero,
                COUNT(hp.id) as total_parcelas_count,
                SUM(CASE WHEN hp.status = 'pago' THEN 1 ELSE 0 END) as parcelas_pagas,
                SUM(CASE WHEN hp.status = 'atrasado' THEN 1 ELSE 0 END) as parcelas_atrasadas,
                COALESCE(SUM(CASE WHEN hp.status = 'pago' THEN hp.valor_pago ELSE 0 END), 0) as valor_recebido,
                COALESCE(SUM(CASE WHEN hp.status IN ('pendente','atrasado') THEN hp.valor ELSE 0 END), 0) as valor_pendente
            FROM honorarios h
            JOIN pessoas p ON p.id = h.cliente_id
            LEFT JOIN processos pr ON pr.id = h.processo_id
            LEFT JOIN honorario_parcelas hp ON hp.honorario_id = h.id
            {$where}
            GROUP BY h.id, p.nome, pr.numero
            ORDER BY h.created_at DESC
        ", $params);

        return view('livewire.honorarios', compact('resumo', 'honorarios'));
    }
}
