<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Assistente extends Component
{
    public array  $mensagens  = [];
    public string $pergunta   = '';
    public bool   $carregando = false;


    public function mount(): void
    {
        $this->mensagens[] = [
            'tipo'  => 'bot',
            'texto' => '👋 Olá! Sou o assistente jurídico do SAPRO. Posso te ajudar com informações sobre processos, agenda e clientes. O que deseja saber?',
            'hora'  => now()->format('H:i'),
        ];
    }

    public function enviar(): void
    {
        if (empty(trim($this->pergunta))) return;

        $pergunta = trim($this->pergunta);
        $this->pergunta = '';

        $this->mensagens[] = [
            'tipo'  => 'usuario',
            'texto' => $pergunta,
            'hora'  => now()->format('H:i'),
        ];

        $this->carregando = true;

        $contexto = $this->buscarContexto($pergunta);
        $resposta = $this->perguntarGemini($pergunta, $contexto);

        $this->mensagens[] = [
            'tipo'  => 'bot',
            'texto' => $resposta,
            'hora'  => now()->format('H:i'),
        ];

        $this->carregando = false;
    }

    public function limpar(): void
    {
        $this->mensagens = [[
            'tipo'  => 'bot',
            'texto' => '👋 Conversa reiniciada. Como posso ajudar?',
            'hora'  => now()->format('H:i'),
        ]];
    }

    private function buscarContexto(string $pergunta): string
    {
        $contexto = '';
        $perguntaLower = strtolower($pergunta);

        // Estatísticas gerais sempre incluídas
        $stats = DB::selectOne("
            SELECT 
                (SELECT COUNT(*) FROM processos WHERE status = 'Ativo') as processos_ativos,
                (SELECT COUNT(*) FROM processos WHERE status = 'Encerrado') as processos_encerrados,
                (SELECT COUNT(*) FROM pessoas p JOIN pessoa_tipos pt ON pt.pessoa_id = p.id WHERE pt.tipo = 'Cliente' AND p.ativo = true) as total_clientes,
                (SELECT COUNT(*) FROM agenda WHERE data_hora >= NOW() AND concluido = false) as compromissos_futuros
        ");

        $contexto .= "=== ESTATÍSTICAS GERAIS ===\n";
        $contexto .= "Processos ativos: {$stats->processos_ativos}\n";
        $contexto .= "Processos encerrados: {$stats->processos_encerrados}\n";
        $contexto .= "Total de clientes ativos: {$stats->total_clientes}\n";
        $contexto .= "Compromissos futuros pendentes: {$stats->compromissos_futuros}\n\n";

        // Agenda
        if (str_contains($perguntaLower, 'agenda') || 
            str_contains($perguntaLower, 'audiência') || 
            str_contains($perguntaLower, 'audiencia') ||
            str_contains($perguntaLower, 'prazo') || 
            str_contains($perguntaLower, 'hoje') || 
            str_contains($perguntaLower, 'amanhã') ||
            str_contains($perguntaLower, 'semana') ||
            str_contains($perguntaLower, 'compromisso')) {

            $agenda = DB::select("
                SELECT a.titulo, a.tipo, a.data_hora, a.urgente, a.local,
                       p.numero as processo_numero,
                       pe.nome as cliente_nome
                FROM agenda a
                LEFT JOIN processos p ON p.id = a.processo_id
                LEFT JOIN pessoas pe ON pe.id = p.cliente_id
                WHERE a.data_hora >= NOW() AND a.concluido = false
                ORDER BY a.data_hora
                LIMIT 15
            ");

            $contexto .= "=== PRÓXIMOS COMPROMISSOS ===\n";
            foreach ($agenda as $ev) {
                $data = Carbon::parse($ev->data_hora)->format('d/m/Y H:i');
                $urgente = $ev->urgente ? ' [URGENTE]' : '';
                $processo = $ev->processo_numero ? " | Processo: {$ev->processo_numero}" : '';
                $cliente = $ev->cliente_nome ? " | Cliente: {$ev->cliente_nome}" : '';
                $local = $ev->local ? " | Local: {$ev->local}" : '';
                $contexto .= "- {$data}{$urgente} | {$ev->tipo}: {$ev->titulo}{$processo}{$cliente}{$local}\n";
            }
            $contexto .= "\n";
        }

        // Processos
        if (str_contains($perguntaLower, 'processo') || 
            str_contains($perguntaLower, 'andamento') ||
            str_contains($perguntaLower, 'fase') ||
            str_contains($perguntaLower, 'risco')) {

            $processos = DB::select("
                SELECT p.numero, p.status, p.vara, p.valor_causa,
                       pe.nome as cliente_nome,
                       f.descricao as fase,
                       g.descricao as risco,
                       p.created_at,
                       (SELECT COUNT(*) FROM andamentos a WHERE a.processo_id = p.id) as total_andamentos,
                       (SELECT descricao FROM andamentos a WHERE a.processo_id = p.id ORDER BY data DESC LIMIT 1) as ultimo_andamento
                FROM processos p
                LEFT JOIN pessoas pe ON pe.id = p.cliente_id
                LEFT JOIN fases f ON f.id = p.fase_id
                LEFT JOIN graus_risco g ON g.id = p.risco_id
                WHERE p.status = 'Ativo'
                ORDER BY p.updated_at DESC
                LIMIT 20
            ");

            $contexto .= "=== PROCESSOS ATIVOS (últimos 20) ===\n";
            foreach ($processos as $proc) {
                $valor = $proc->valor_causa ? 'R$ ' . number_format($proc->valor_causa, 2, ',', '.') : 'não informado';
                $contexto .= "- Processo: {$proc->numero} | Cliente: {$proc->cliente_nome} | Fase: {$proc->fase} | Risco: {$proc->risco} | Andamentos: {$proc->total_andamentos} | Valor: {$valor}\n";
                if ($proc->ultimo_andamento) {
                    $contexto .= "  Último andamento: {$proc->ultimo_andamento}\n";
                }
            }
            $contexto .= "\n";
        }

        // Clientes / Pessoas
        if (str_contains($perguntaLower, 'cliente') ||
            str_contains($perguntaLower, 'pessoa') ||
            str_contains($perguntaLower, 'advogado')) {

            $clientes = DB::select("
                SELECT pe.nome, pe.telefone, pe.celular, pe.email,
                       COUNT(DISTINCT p.id) as total_processos
                FROM pessoas pe
                JOIN pessoa_tipos pt ON pt.pessoa_id = pe.id
                LEFT JOIN processos p ON p.cliente_id = pe.id AND p.status = 'Ativo'
                WHERE pt.tipo = 'Cliente' AND pe.ativo = true
                GROUP BY pe.id, pe.nome, pe.telefone, pe.celular, pe.email
                ORDER BY total_processos DESC
                LIMIT 20
            ");

            $contexto .= "=== CLIENTES ATIVOS (top 20 por processos) ===\n";
            foreach ($clientes as $cli) {
                $contato = $cli->celular ?: $cli->telefone ?: 'sem contato';
                $contexto .= "- {$cli->nome} | Processos ativos: {$cli->total_processos} | Contato: {$contato}\n";
            }
            $contexto .= "\n";
        }

        // Prazos urgentes
        if (str_contains($perguntaLower, 'prazo') ||
            str_contains($perguntaLower, 'urgente') ||
            str_contains($perguntaLower, 'vencimento') ||
            str_contains($perguntaLower, 'vencendo') ||
            str_contains($perguntaLower, 'vencido')) {

            $prazos = DB::select("
                SELECT pz.titulo, pz.data_prazo, pz.prazo_fatal, pz.status,
                       pr.numero as processo_numero,
                       pe.nome as cliente_nome,
                       u.nome as responsavel
                FROM prazos pz
                LEFT JOIN processos pr ON pr.id = pz.processo_id
                LEFT JOIN pessoas pe ON pe.id = pr.cliente_id
                LEFT JOIN usuarios u ON u.id = pz.responsavel_id
                WHERE pz.status = 'aberto'
                ORDER BY pz.data_prazo ASC
                LIMIT 20
            ");

            $contexto .= "=== PRAZOS EM ABERTO (próximos/vencidos) ===\n";
            foreach ($prazos as $pz) {
                $data      = Carbon::parse($pz->data_prazo)->format('d/m/Y');
                $dias      = (int) Carbon::today()->diffInDays($pz->data_prazo, false);
                $situacao  = $dias < 0 ? "VENCIDO há {$dias} dias" : "em {$dias} dia(s)";
                $fatal     = $pz->prazo_fatal ? ' [FATAL]' : '';
                $resp      = $pz->responsavel ? " | Resp: {$pz->responsavel}" : '';
                $proc      = $pz->processo_numero ? " | Proc: {$pz->processo_numero}" : '';
                $cliente   = $pz->cliente_nome ? " | Cliente: {$pz->cliente_nome}" : '';
                $contexto .= "- {$data}{$fatal} ({$situacao}): {$pz->titulo}{$proc}{$cliente}{$resp}\n";
            }
            $contexto .= "\n";
        }

        // Honorários + Inadimplência
        if (str_contains($perguntaLower, 'honorário') ||
            str_contains($perguntaLower, 'honorario') ||
            str_contains($perguntaLower, 'parcela') ||
            str_contains($perguntaLower, 'receber') ||
            str_contains($perguntaLower, 'inadimpl') ||
            str_contains($perguntaLower, 'contrato') ||
            str_contains($perguntaLower, 'devendo') ||
            str_contains($perguntaLower, 'atraso')) {

            // Resumo por status
            $resumoHon = DB::selectOne("
                SELECT
                    COUNT(*) FILTER (WHERE hp.status = 'pago')     as qtd_pagas,
                    COUNT(*) FILTER (WHERE hp.status = 'pendente') as qtd_pendentes,
                    COUNT(*) FILTER (WHERE hp.status = 'atrasado') as qtd_atrasadas,
                    COALESCE(SUM(hp.valor) FILTER (WHERE hp.status = 'pago'),     0) as total_pago,
                    COALESCE(SUM(hp.valor) FILTER (WHERE hp.status = 'pendente'), 0) as total_pendente,
                    COALESCE(SUM(hp.valor) FILTER (WHERE hp.status = 'atrasado'), 0) as total_atrasado,
                    COALESCE(SUM(hp.valor) FILTER (WHERE hp.status = 'pago'
                        AND DATE_TRUNC('month', hp.data_pagamento) = DATE_TRUNC('month', CURRENT_DATE)), 0) as recebido_mes,
                    COALESCE(SUM(hp.valor) FILTER (WHERE hp.status = 'pago'
                        AND DATE_TRUNC('month', hp.data_pagamento) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')), 0) as recebido_mes_anterior
                FROM honorario_parcelas hp
            ");

            $contexto .= "=== HONORÁRIOS — VISÃO GERAL ===\n";
            $contexto .= "Recebido este mês: R$ " . number_format($resumoHon->recebido_mes, 2, ',', '.') . "\n";
            $contexto .= "Recebido mês anterior: R$ " . number_format($resumoHon->recebido_mes_anterior, 2, ',', '.') . "\n";
            $contexto .= "Total pago (histórico): R$ " . number_format($resumoHon->total_pago, 2, ',', '.') . " ({$resumoHon->qtd_pagas} parcelas)\n";
            $contexto .= "Pendente (a vencer): R$ " . number_format($resumoHon->total_pendente, 2, ',', '.') . " ({$resumoHon->qtd_pendentes} parcelas)\n";
            $contexto .= "ATRASADO (inadimplente): R$ " . number_format($resumoHon->total_atrasado, 2, ',', '.') . " ({$resumoHon->qtd_atrasadas} parcelas)\n\n";

            // Inadimplentes por cliente
            $inadimplentes = DB::select("
                SELECT cl.nome as cliente_nome,
                       COUNT(hp.id) as parcelas_atrasadas,
                       SUM(hp.valor) as total_devido,
                       MIN(hp.vencimento) as vencimento_mais_antigo,
                       MAX(CURRENT_DATE - hp.vencimento) as maior_atraso_dias
                FROM honorario_parcelas hp
                JOIN honorarios h ON h.id = hp.honorario_id
                JOIN pessoas cl ON cl.id = h.cliente_id
                WHERE hp.status = 'atrasado'
                GROUP BY cl.id, cl.nome
                ORDER BY total_devido DESC
                LIMIT 10
            ");

            if ($inadimplentes) {
                $contexto .= "=== INADIMPLÊNCIA POR CLIENTE ===\n";
                foreach ($inadimplentes as $i) {
                    $venc  = Carbon::parse($i->vencimento_mais_antigo)->format('d/m/Y');
                    $total = 'R$ ' . number_format($i->total_devido, 2, ',', '.');
                    $contexto .= "- {$i->cliente_nome} | {$total} em {$i->parcelas_atrasadas} parcela(s) | Desde: {$venc} | Maior atraso: {$i->maior_atraso_dias} dias\n";
                }
                $contexto .= "\n";
            }

            // Próximos vencimentos (7 dias)
            $vencendo = DB::select("
                SELECT hp.vencimento, hp.valor, hp.numero_parcela,
                       cl.nome as cliente_nome,
                       pr.numero as processo_numero
                FROM honorario_parcelas hp
                JOIN honorarios h ON h.id = hp.honorario_id
                LEFT JOIN pessoas cl ON cl.id = h.cliente_id
                LEFT JOIN processos pr ON pr.id = h.processo_id
                WHERE hp.status = 'pendente'
                  AND hp.vencimento BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '7 days'
                ORDER BY hp.vencimento ASC
            ");

            if ($vencendo) {
                $contexto .= "=== HONORÁRIOS VENCENDO EM 7 DIAS ===\n";
                foreach ($vencendo as $v) {
                    $venc  = Carbon::parse($v->vencimento)->format('d/m/Y');
                    $valor = 'R$ ' . number_format($v->valor, 2, ',', '.');
                    $proc  = $v->processo_numero ? " | Proc: {$v->processo_numero}" : '';
                    $contexto .= "- {$venc} | {$v->cliente_nome} | Parcela {$v->numero_parcela} | {$valor}{$proc}\n";
                }
                $contexto .= "\n";
            }
        }

        // Financeiro (recebimentos/pagamentos)
        if (str_contains($perguntaLower, 'financeiro') ||
            str_contains($perguntaLower, 'receita') ||
            str_contains($perguntaLower, 'despesa') ||
            str_contains($perguntaLower, 'caixa') ||
            str_contains($perguntaLower, 'saldo') ||
            str_contains($perguntaLower, 'custo') ||
            str_contains($perguntaLower, 'pagamento') ||
            str_contains($perguntaLower, 'fluxo')) {

            $fin = DB::selectOne("
                SELECT
                    COALESCE(SUM(CASE WHEN recebido = true  AND DATE_TRUNC('month', data_recebimento) = DATE_TRUNC('month', CURRENT_DATE)                    THEN valor_recebido ELSE 0 END), 0) as recebido_mes,
                    COALESCE(SUM(CASE WHEN recebido = true  AND DATE_TRUNC('month', data_recebimento) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month') THEN valor_recebido ELSE 0 END), 0) as recebido_mes_ant,
                    COALESCE(SUM(CASE WHEN recebido = false THEN valor ELSE 0 END), 0) as a_receber_total,
                    COUNT(CASE WHEN recebido = false THEN 1 END) as qtd_pendentes
                FROM recebimentos
            ");

            $pag = DB::selectOne("
                SELECT
                    COALESCE(SUM(CASE WHEN pago = true AND DATE_TRUNC('month', data_pagamento) = DATE_TRUNC('month', CURRENT_DATE)                    THEN valor_pago ELSE 0 END), 0) as pago_mes,
                    COALESCE(SUM(CASE WHEN pago = true AND DATE_TRUNC('month', data_pagamento) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month') THEN valor_pago ELSE 0 END), 0) as pago_mes_ant,
                    COALESCE(SUM(CASE WHEN pago = false THEN valor ELSE 0 END), 0) as a_pagar_total
                FROM pagamentos
            ");

            $custas = DB::selectOne("
                SELECT
                    COALESCE(SUM(valor) FILTER (WHERE pago = false), 0) as pendentes,
                    COUNT(*) FILTER (WHERE pago = false) as qtd_pendentes
                FROM custas
            ");

            $saldoMes = $fin->recebido_mes - $pag->pago_mes;
            $saldoAnt = $fin->recebido_mes_ant - $pag->pago_mes_ant;

            $contexto .= "=== RESUMO FINANCEIRO ===\n";
            $contexto .= "— MÊS ATUAL —\n";
            $contexto .= "Receitas recebidas: R$ " . number_format($fin->recebido_mes, 2, ',', '.') . "\n";
            $contexto .= "Despesas pagas: R$ " . number_format($pag->pago_mes, 2, ',', '.') . "\n";
            $contexto .= "Saldo do mês: R$ " . number_format($saldoMes, 2, ',', '.') . ($saldoMes >= 0 ? ' (positivo)' : ' (negativo)') . "\n\n";
            $contexto .= "— MÊS ANTERIOR —\n";
            $contexto .= "Receitas recebidas: R$ " . number_format($fin->recebido_mes_ant, 2, ',', '.') . "\n";
            $contexto .= "Despesas pagas: R$ " . number_format($pag->pago_mes_ant, 2, ',', '.') . "\n";
            $contexto .= "Saldo: R$ " . number_format($saldoAnt, 2, ',', '.') . "\n\n";
            $contexto .= "— PENDÊNCIAS —\n";
            $contexto .= "A receber: R$ " . number_format($fin->a_receber_total, 2, ',', '.') . " ({$fin->qtd_pendentes} lançamento(s))\n";
            $contexto .= "A pagar: R$ " . number_format($pag->a_pagar_total, 2, ',', '.') . "\n";
            $contexto .= "Custas processuais pendentes: R$ " . number_format($custas->pendentes, 2, ',', '.') . " ({$custas->qtd_pendentes} item(s))\n";
            $contexto .= "\n";
        }

        return $contexto;
    }

    private function perguntarGemini(string $pergunta, string $contexto): string
    {
        try {
            $sistemaPrompt = "Você é um assistente jurídico inteligente do sistema SAPRO.
Responda de forma clara, objetiva e profissional em português brasileiro.
Use os dados abaixo para responder com precisão. Não invente informações.
Se não encontrar a informação, diga que não encontrou e sugira onde buscar.
Pode usar markdown: **negrito**, listas com -, `código`.

=== DADOS DO SISTEMA (atualizados agora) ===
{$contexto}
=== FIM DOS DADOS ===";

            // Monta histórico multi-turn (últimas 10 trocas = 20 mensagens)
            $historico = array_slice($this->mensagens, -20);
            $contents  = [];

            // Primeira mensagem traz o contexto do sistema
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $sistemaPrompt . "\n\nPrimeira pergunta: " . $pergunta]],
            ];

            // Se há histórico anterior, substitui a primeira entrada e adiciona o restante
            if (count($historico) > 1) {
                $contents = [];
                foreach ($historico as $msg) {
                    if ($msg['tipo'] === 'bot' && $msg['texto'] === $this->mensagens[0]['texto']) {
                        continue; // pula a saudação inicial
                    }
                    $role     = $msg['tipo'] === 'usuario' ? 'user' : 'model';
                    $contents[] = [
                        'role'  => $role,
                        'parts' => [['text' => $msg['texto']]],
                    ];
                }
                // Injeta contexto na última mensagem do usuário
                if (!empty($contents) && end($contents)['role'] === 'user') {
                    $last = array_pop($contents);
                    array_unshift($contents, [
                        'role'  => 'user',
                        'parts' => [['text' => $sistemaPrompt]],
                    ]);
                    array_unshift($contents, ...[]);
                    $contents[] = $last;
                }
            }

            // Garante que contents não está vazio e termina com role=user
            if (empty($contents)) {
                $contents = [[
                    'role'  => 'user',
                    'parts' => [['text' => $sistemaPrompt . "\n\n" . $pergunta]],
                ]];
            }

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(30)
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'),
                    ['contents' => $contents]
                );

            if (! $response->successful()) {
                return '⚠️ Erro ao consultar a IA (HTTP ' . $response->status() . '). Tente novamente.';
            }

            return $response->json('candidates.0.content.parts.0.text')
                ?? 'Não consegui processar sua pergunta.';

        } catch (\Exception $e) {
            return '⚠️ Erro: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.assistente');
    }
}
