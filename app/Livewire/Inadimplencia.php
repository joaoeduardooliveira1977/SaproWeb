<?php

namespace App\Livewire;

use App\Mail\CobrancaCliente;
use App\Models\Cobranca;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Inadimplencia extends Component
{
    // ── Filtros ──────────────────────────────────────────────
    public string $filtroCliente  = '';
    public string $filtroStatus   = '';  // atrasado | em_cobranca | inadimplente
    public string $filtroOrdem    = 'dias_desc';

    // ── Modal registrar contato ───────────────────────────────
    public bool   $modalContato   = false;
    public ?int   $parcelaIdContato = null;
    public int    $clienteIdContato = 0;
    public string $tipoContato    = 'ligacao';
    public string $descContato    = '';

    // ── Modal email ───────────────────────────────────────────
    public bool   $modalEmail     = false;
    public ?int   $clienteIdEmail = null;
    public string $clienteNomeEmail = '';
    public string $clienteEmailAddr = '';
    public string $emailMensagem  = '';
    public bool   $emailEnviando  = false;
    public string $emailSucesso   = '';
    public string $emailErro      = '';

    // ── Modal pagamento rápido ────────────────────────────────
    public bool   $modalPagamento  = false;
    public ?int   $parcelaIdPag    = null;
    public string $dataPagamento   = '';
    public string $valorPago       = '';
    public string $formaPagamento  = 'pix';

    public function updatedFiltroCliente(): void  { }
    public function updatedFiltroStatus(): void   { }
    public function updatedFiltroOrdem(): void    { }

    // ── Registrar contato ─────────────────────────────────────

    public function abrirContato(int $parcelaId, int $clienteId): void
    {
        $this->parcelaIdContato = $parcelaId;
        $this->clienteIdContato = $clienteId;
        $this->tipoContato      = 'ligacao';
        $this->descContato      = '';
        $this->modalContato     = true;
    }

    public function salvarContato(): void
    {
        $this->validate([
            'tipoContato'  => 'required|in:email,ligacao,whatsapp,reuniao,negociacao,acordo',
            'descContato'  => 'nullable|string|max:500',
        ]);

        Cobranca::create([
            'parcela_id'  => $this->parcelaIdContato,
            'cliente_id'  => $this->clienteIdContato,
            'usuario_id'  => Auth::id(),
            'tipo'        => $this->tipoContato,
            'data'        => today(),
            'descricao'   => $this->descContato ?: null,
        ]);

        // Se tipo = acordo, marca parcela como paga com 0 (tratamento especial)
        if ($this->tipoContato === 'acordo') {
            $this->dispatch('toast', message: 'Acordo registrado. Não esqueça de registrar o pagamento quando efetivado.', type: 'success');
        } else {
            $this->dispatch('toast', message: 'Tentativa de contato registrada.', type: 'success');
        }

        $this->modalContato = false;
    }

    // ── Enviar e-mail de cobrança ─────────────────────────────

    public function abrirEmail(int $clienteId): void
    {
        $cliente = DB::selectOne(
            "SELECT id, nome, email FROM pessoas WHERE id = ?",
            [$clienteId]
        );

        if (!$cliente) return;

        $this->clienteIdEmail   = $clienteId;
        $this->clienteNomeEmail = $cliente->nome;
        $this->clienteEmailAddr = $cliente->email ?? '';
        $this->emailMensagem    = '';
        $this->emailSucesso     = '';
        $this->emailErro        = '';
        $this->modalEmail       = true;
    }

    public function enviarEmail(): void
    {
        $this->validate([
            'clienteEmailAddr' => 'required|email',
        ], [], [
            'clienteEmailAddr' => 'e-mail do cliente',
        ]);

        $parcelas = DB::select("
            SELECT hp.numero_parcela as numero, hp.vencimento, hp.valor,
                   GREATEST(0, CURRENT_DATE - hp.vencimento) as dias
            FROM honorario_parcelas hp
            JOIN honorarios h ON h.id = hp.honorario_id
            WHERE h.cliente_id = ? AND hp.status = 'atrasado'
            ORDER BY hp.vencimento
        ", [$this->clienteIdEmail]);

        if (empty($parcelas)) {
            $this->emailErro = 'Nenhuma parcela atrasada encontrada para este cliente.';
            return;
        }

        $totalDevido = array_sum(array_column($parcelas, 'valor'));

        try {
            $parcelasArr = array_map(fn($p) => [
                'numero'     => $p->numero,
                'vencimento' => $p->vencimento,
                'valor'      => (float) $p->valor,
                'dias'       => (int) $p->dias,
            ], $parcelas);

            Mail::send(new CobrancaCliente(
                clienteNome:    $this->clienteNomeEmail,
                clienteEmail:   $this->clienteEmailAddr,
                parcelas:       $parcelasArr,
                totalDevido:    (float) $totalDevido,
                escritorioNome: config('app.name', 'Escritório de Advocacia'),
            ));

            // Registra cobrança
            foreach ($parcelas as $p) {
                $parcela = DB::selectOne(
                    "SELECT id FROM honorario_parcelas WHERE honorario_id IN
                     (SELECT id FROM honorarios WHERE cliente_id = ?)
                     AND numero_parcela = ? AND status = 'atrasado' LIMIT 1",
                    [$this->clienteIdEmail, $p->numero]
                );
                if ($parcela) {
                    Cobranca::create([
                        'parcela_id'  => $parcela->id,
                        'cliente_id'  => $this->clienteIdEmail,
                        'usuario_id'  => Auth::id(),
                        'tipo'        => 'email',
                        'data'        => today(),
                        'descricao'   => 'E-mail de cobrança automático enviado pelo sistema.',
                    ]);
                }
            }

            $this->emailSucesso = "E-mail enviado com sucesso para {$this->clienteEmailAddr}.";
            $this->modalEmail   = false;
            $this->dispatch('toast', message: $this->emailSucesso, type: 'success');

        } catch (\Throwable $e) {
            $this->emailErro = 'Falha ao enviar e-mail: ' . $e->getMessage();
        }
    }

    // ── Pagamento rápido ──────────────────────────────────────

    public function abrirPagamento(int $parcelaId): void
    {
        $p = DB::selectOne("SELECT * FROM honorario_parcelas WHERE id = ?", [$parcelaId]);
        if (!$p) return;

        $this->parcelaIdPag   = $parcelaId;
        $this->valorPago      = number_format($p->valor, 2, '.', '');
        $this->dataPagamento  = now()->format('Y-m-d');
        $this->formaPagamento = 'pix';
        $this->modalPagamento = true;
    }

    public function registrarPagamento(): void
    {
        $this->validate([
            'dataPagamento'  => 'required|date',
            'valorPago'      => 'required|numeric|min:0.01',
            'formaPagamento' => 'required|string',
        ]);

        DB::update("
            UPDATE honorario_parcelas SET
                status = 'pago', data_pagamento = ?, valor_pago = ?,
                forma_pagamento = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            $this->dataPagamento,
            (float) $this->valorPago,
            $this->formaPagamento,
            $this->parcelaIdPag,
        ]);

        $this->modalPagamento = false;
        $this->dispatch('toast', message: 'Pagamento registrado com sucesso.', type: 'success');
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        // Atualiza status das parcelas vencidas
        DB::update("
            UPDATE honorario_parcelas SET status = 'atrasado'
            WHERE status = 'pendente' AND vencimento < CURRENT_DATE
        ");

        // KPIs
        $kpis = DB::selectOne("
            SELECT
                COUNT(DISTINCT h.cliente_id)                                  AS clientes_inadimplentes,
                COUNT(hp.id)                                                   AS total_parcelas,
                COALESCE(SUM(hp.valor), 0)                                    AS total_valor,
                COALESCE(AVG(CURRENT_DATE - hp.vencimento), 0)::int           AS media_dias,
                COALESCE(SUM(CASE WHEN (CURRENT_DATE - hp.vencimento) > 30
                    THEN hp.valor ELSE 0 END), 0)                             AS valor_critico
            FROM honorario_parcelas hp
            JOIN honorarios h ON h.id = hp.honorario_id
            WHERE hp.status = 'atrasado'
        ");

        // Lista de clientes inadimplentes com suas parcelas
        $where = "WHERE hp.status = 'atrasado'";
        $params = [];

        if ($this->filtroCliente) {
            $where .= " AND p.nome ILIKE ?";
            $params[] = "%{$this->filtroCliente}%";
        }

        if ($this->filtroStatus === 'atrasado') {
            $where .= " AND (CURRENT_DATE - hp.vencimento) BETWEEN 1 AND 15";
        } elseif ($this->filtroStatus === 'em_cobranca') {
            $where .= " AND (CURRENT_DATE - hp.vencimento) BETWEEN 16 AND 30";
        } elseif ($this->filtroStatus === 'inadimplente') {
            $where .= " AND (CURRENT_DATE - hp.vencimento) > 30";
        }

        $order = match($this->filtroOrdem) {
            'valor_desc' => 'total_devido DESC',
            'valor_asc'  => 'total_devido ASC',
            'nome_asc'   => 'p.nome ASC',
            default      => 'max_dias DESC',
        };

        $clientes = DB::select("
            SELECT
                p.id                                    AS cliente_id,
                p.nome                                  AS cliente_nome,
                p.email                                 AS cliente_email,
                p.celular                               AS cliente_celular,
                COUNT(hp.id)                            AS qtd_parcelas,
                COALESCE(SUM(hp.valor), 0)              AS total_devido,
                MAX(CURRENT_DATE - hp.vencimento)       AS max_dias,
                MIN(hp.vencimento)                      AS primeira_vencimento,
                (SELECT COUNT(*) FROM cobrancas c WHERE c.cliente_id = p.id
                 AND c.created_at >= NOW() - INTERVAL '30 days') AS tentativas_recentes,
                (SELECT c2.tipo FROM cobrancas c2 WHERE c2.cliente_id = p.id
                 ORDER BY c2.created_at DESC LIMIT 1)   AS ultimo_contato_tipo,
                (SELECT c2.created_at FROM cobrancas c2 WHERE c2.cliente_id = p.id
                 ORDER BY c2.created_at DESC LIMIT 1)   AS ultimo_contato_em
            FROM honorario_parcelas hp
            JOIN honorarios h ON h.id = hp.honorario_id
            JOIN pessoas p ON p.id = h.cliente_id
            {$where}
            GROUP BY p.id, p.nome, p.email, p.celular
            ORDER BY {$order}
        ", $params);

        // Parcelas por cliente (para detalhe)
        $parcelasPorCliente = [];
        if (!empty($clientes)) {
            $ids = array_column($clientes, 'cliente_id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $parcelas = DB::select("
                SELECT
                    hp.id, hp.numero_parcela, hp.valor, hp.vencimento,
                    h.cliente_id, h.descricao as honorario_desc,
                    (CURRENT_DATE - hp.vencimento) AS dias_atraso,
                    (SELECT COUNT(*) FROM cobrancas c WHERE c.parcela_id = hp.id) AS tentativas
                FROM honorario_parcelas hp
                JOIN honorarios h ON h.id = hp.honorario_id
                WHERE hp.status = 'atrasado' AND h.cliente_id IN ({$placeholders})
                ORDER BY hp.vencimento ASC
            ", $ids);

            foreach ($parcelas as $parc) {
                $parcelasPorCliente[$parc->cliente_id][] = $parc;
            }
        }

        return view('livewire.inadimplencia', compact('kpis', 'clientes', 'parcelasPorCliente'));
    }
}
