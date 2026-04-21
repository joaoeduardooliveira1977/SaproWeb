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
    public string $filtroFonte    = 'todos'; // todos | honorarios | lancamentos

    protected $queryString = [
        'filtroCliente' => ['except' => ''],
        'filtroStatus'  => ['except' => ''],
        'filtroOrdem'   => ['except' => 'dias_desc'],
        'filtroFonte'   => ['except' => 'todos'],
    ];

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
    public string $parcelaFonte    = 'honorario'; // honorario | lancamento
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
            'usuario_id'  => Auth::guard('usuarios')->id(),
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
                        'usuario_id'  => Auth::guard('usuarios')->id(),
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

    public function abrirPagamento(int $id, string $fonte = 'honorario'): void
    {
        if ($fonte === 'lancamento') {
            $p = DB::selectOne("SELECT * FROM financeiro_lancamentos WHERE id = ?", [$id]);
        } else {
            $p = DB::selectOne("SELECT * FROM honorario_parcelas WHERE id = ?", [$id]);
        }
        if (!$p) return;

        $this->parcelaIdPag   = $id;
        $this->parcelaFonte   = $fonte;
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

        if ($this->parcelaFonte === 'lancamento') {
            DB::table('financeiro_lancamentos')->where('id', $this->parcelaIdPag)->update([
                'status'         => 'recebido',
                'data_pagamento' => $this->dataPagamento,
                'valor_pago'     => (float) $this->valorPago,
                'forma_pagamento'=> $this->formaPagamento,
                'updated_at'     => now(),
            ]);
        } else {
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
        }

        $this->modalPagamento = false;
        $this->dispatch('toast', message: 'Pagamento registrado com sucesso.', type: 'success');
    }

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $diasWhere = match($this->filtroStatus) {
            'atrasado'    => "AND dias_atraso BETWEEN 1 AND 15",
            'em_cobranca' => "AND dias_atraso BETWEEN 16 AND 30",
            'inadimplente'=> "AND dias_atraso > 30",
            default       => '',
        };
        $clienteWhere = $this->filtroCliente
            ? "AND p.nome ILIKE " . DB::connection()->getPdo()->quote('%' . $this->filtroCliente . '%')
            : '';

        $unionParts = [];
        if (in_array($this->filtroFonte, ['todos', 'honorarios'])) {
            $unionParts[] = "
                SELECT p.id AS cliente_id, p.nome, p.email, p.celular,
                       'Honorários' AS fonte, hp.valor,
                       (CURRENT_DATE - hp.vencimento)::int AS dias_atraso,
                       hp.vencimento
                FROM honorario_parcelas hp
                JOIN honorarios h ON h.id = hp.honorario_id
                JOIN pessoas p ON p.id = h.cliente_id
                WHERE hp.status = 'atrasado' {$clienteWhere}
            ";
        }
        if (in_array($this->filtroFonte, ['todos', 'lancamentos'])) {
            $unionParts[] = "
                SELECT p.id AS cliente_id, p.nome, p.email, p.celular,
                       'Financeiro' AS fonte, fl.valor,
                       (CURRENT_DATE - fl.vencimento)::int AS dias_atraso,
                       fl.vencimento
                FROM financeiro_lancamentos fl
                JOIN pessoas p ON p.id = fl.cliente_id
                WHERE fl.status = 'atrasado' AND fl.tipo = 'receita' {$clienteWhere}
            ";
        }

        $union = implode(' UNION ALL ', $unionParts ?: ["SELECT NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL WHERE false"]);

        $rows = DB::select("
            SELECT nome AS cliente_nome, email AS cliente_email, celular AS cliente_celular,
                   COUNT(*) AS qtd, COALESCE(SUM(valor),0) AS total_devido,
                   MAX(dias_atraso) AS max_dias, MIN(vencimento) AS primeira_vencimento,
                   string_agg(DISTINCT fonte, ', ') AS fontes
            FROM ({$union}) u
            WHERE 1=1 {$diasWhere}
            GROUP BY cliente_id, nome, email, celular
            ORDER BY max_dias DESC
        ");

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Cliente','E-mail','Celular','Itens em Atraso','Total Devido','Maior Atraso (dias)','Primeira Vencimento','Origem'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->cliente_nome,
                    $r->cliente_email ?? '',
                    $r->cliente_celular ?? '',
                    $r->qtd,
                    number_format($r->total_devido, 2, ',', '.'),
                    $r->max_dias,
                    $r->primeira_vencimento ? \Carbon\Carbon::parse($r->primeira_vencimento)->format('d/m/Y') : '',
                    $r->fontes,
                ], ';');
            }
            fclose($out);
        }, 'inadimplencia-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        // ── Filtro por faixa de dias ──────────────────────────
        $diasWhere = match($this->filtroStatus) {
            'atrasado'    => "AND dias_atraso BETWEEN 1 AND 15",
            'em_cobranca' => "AND dias_atraso BETWEEN 16 AND 30",
            'inadimplente'=> "AND dias_atraso > 30",
            default       => '',
        };

        $clienteWhere = $this->filtroCliente
            ? "AND p.nome ILIKE " . DB::connection()->getPdo()->quote('%' . $this->filtroCliente . '%')
            : '';

        // ── UNION: honorarios + lancamentos ──────────────────
        $fonteHon = in_array($this->filtroFonte, ['todos', 'honorarios']);
        $fonteLan = in_array($this->filtroFonte, ['todos', 'lancamentos']);

        $unionParts = [];
        if ($fonteHon) {
            $unionParts[] = "
                SELECT p.id AS cliente_id, p.nome, p.email, p.celular,
                       'honorario' AS fonte,
                       hp.id AS item_id, hp.valor, hp.vencimento,
                       (CURRENT_DATE - hp.vencimento)::int AS dias_atraso,
                       h.descricao AS item_desc,
                       hp.numero_parcela AS numero
                FROM honorario_parcelas hp
                JOIN honorarios h ON h.id = hp.honorario_id
                JOIN pessoas p ON p.id = h.cliente_id
                WHERE hp.status = 'atrasado' {$clienteWhere}
            ";
        }
        if ($fonteLan) {
            $unionParts[] = "
                SELECT p.id AS cliente_id, p.nome, p.email, p.celular,
                       'lancamento' AS fonte,
                       fl.id AS item_id, fl.valor, fl.vencimento,
                       (CURRENT_DATE - fl.vencimento)::int AS dias_atraso,
                       fl.descricao AS item_desc,
                       NULL AS numero
                FROM financeiro_lancamentos fl
                JOIN pessoas p ON p.id = fl.cliente_id
                WHERE fl.status = 'atrasado' AND fl.tipo = 'receita' {$clienteWhere}
            ";
        }

        if (empty($unionParts)) {
            return view('livewire.inadimplencia', [
                'kpis' => (object)['clientes_inadimplentes'=>0,'total_parcelas'=>0,'total_valor'=>0,'media_dias'=>0,'valor_critico'=>0,'total_honorarios'=>0,'total_lancamentos'=>0],
                'clientes' => [],
                'parcelasPorCliente' => [],
            ]);
        }

        $union = implode(' UNION ALL ', $unionParts);

        // ── KPIs ─────────────────────────────────────────────
        $kpis = DB::selectOne("
            SELECT
                COUNT(DISTINCT cliente_id)                                      AS clientes_inadimplentes,
                COUNT(*)                                                         AS total_parcelas,
                COALESCE(SUM(valor), 0)                                          AS total_valor,
                COALESCE(AVG(dias_atraso), 0)::int                               AS media_dias,
                COALESCE(SUM(CASE WHEN dias_atraso > 30 THEN valor ELSE 0 END),0) AS valor_critico,
                COALESCE(SUM(CASE WHEN fonte='honorario'  THEN valor ELSE 0 END),0) AS total_honorarios,
                COALESCE(SUM(CASE WHEN fonte='lancamento' THEN valor ELSE 0 END),0) AS total_lancamentos
            FROM ({$union}) u
            WHERE 1=1 {$diasWhere}
        ");

        // ── Clientes agrupados ────────────────────────────────
        $order = match($this->filtroOrdem) {
            'valor_desc' => 'total_devido DESC',
            'valor_asc'  => 'total_devido ASC',
            'nome_asc'   => 'nome ASC',
            default      => 'max_dias DESC',
        };

        $clientes = DB::select("
            SELECT
                cliente_id, nome AS cliente_nome, email AS cliente_email, celular AS cliente_celular,
                COUNT(*)                    AS qtd_parcelas,
                COALESCE(SUM(valor), 0)     AS total_devido,
                MAX(dias_atraso)            AS max_dias,
                MIN(vencimento)             AS primeira_vencimento,
                COUNT(DISTINCT fonte)       AS qtd_fontes,
                bool_or(fonte='honorario')  AS tem_honorario,
                bool_or(fonte='lancamento') AS tem_lancamento,
                (SELECT COUNT(*) FROM cobrancas c WHERE c.cliente_id = u2.cliente_id
                 AND c.created_at >= NOW() - INTERVAL '30 days') AS tentativas_recentes,
                (SELECT c2.tipo FROM cobrancas c2 WHERE c2.cliente_id = u2.cliente_id
                 ORDER BY c2.created_at DESC LIMIT 1) AS ultimo_contato_tipo,
                (SELECT c2.created_at FROM cobrancas c2 WHERE c2.cliente_id = u2.cliente_id
                 ORDER BY c2.created_at DESC LIMIT 1) AS ultimo_contato_em
            FROM ({$union}) u2
            WHERE 1=1 {$diasWhere}
            GROUP BY cliente_id, nome, email, celular
            ORDER BY {$order}
        ");

        // ── Itens por cliente ─────────────────────────────────
        $parcelasPorCliente = [];
        if (!empty($clientes)) {
            $ids = array_column($clientes, 'cliente_id');
            $inList = implode(',', array_map('intval', $ids));

            $itens = DB::select("
                SELECT item_id AS id, fonte, numero AS numero_parcela, valor, vencimento,
                       cliente_id, item_desc AS honorario_desc, dias_atraso,
                       CASE WHEN fonte='honorario'
                           THEN (SELECT COUNT(*) FROM cobrancas c WHERE c.parcela_id = item_id)
                           ELSE 0 END AS tentativas
                FROM ({$union}) u3
                WHERE cliente_id IN ({$inList})
                ORDER BY vencimento ASC
            ");

            foreach ($itens as $item) {
                $parcelasPorCliente[$item->cliente_id][] = $item;
            }
        }

        return view('livewire.inadimplencia', compact('kpis', 'clientes', 'parcelasPorCliente'));
    }
}
