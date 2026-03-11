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

    const GEMINI_KEY = 'AIzaSyCPUK2n9Hv8xcB6H3bKFvTlsYdSriw2rTU';

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

        // Adiciona mensagem do usuário
        $this->mensagens[] = [
            'tipo'  => 'usuario',
            'texto' => $pergunta,
            'hora'  => now()->format('H:i'),
        ];

        $this->carregando = true;

        // Busca dados do banco
        $contexto = $this->buscarContexto($pergunta);

        // Pergunta ao Gemini
        $resposta = $this->perguntarGemini($pergunta, $contexto);

        $this->mensagens[] = [
            'tipo'  => 'bot',
            'texto' => $resposta,
            'hora'  => now()->format('H:i'),
        ];

        $this->carregando = false;
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

        return $contexto;
    }

    private function perguntarGemini(string $pergunta, string $contexto): string
    {
        try {
            $prompt = "Você é um assistente jurídico inteligente do sistema SAPRO (Sistema de Acompanhamento de Processos). 
Responda de forma clara, objetiva e profissional em português brasileiro.
Use os dados abaixo para responder com precisão. Não invente informações que não estejam nos dados.
Se não encontrar a informação, diga que não encontrou e sugira onde buscar.

=== DADOS DO SISTEMA ===
{$contexto}
=== FIM DOS DADOS ===

Pergunta do advogado: {$pergunta}

Responda de forma direta e útil:";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . self::GEMINI_KEY,
                [
                    'contents' => [[
                        'parts' => [[
                            'text' => $prompt
                        ]]
                    ]]
                ]
            );

if (!$response->successful()) {
    return 'Erro HTTP: ' . $response->status() . ' | ' . $response->body();
}

return $response->json('candidates.0.content.parts.0.text') ?? 'Não consegui processar sua pergunta.';



        
} catch (\Exception $e) {
    return 'Erro: ' . $e->getMessage() . ' | ' . $e->getCode();
}


    }

    public function render()
    {
        return view('livewire.assistente');
    }
}
