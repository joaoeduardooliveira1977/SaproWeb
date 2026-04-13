<?php

namespace App\Livewire;

use App\Models\{Andamento, Documento, HonorarioParcela, Pessoa, Prazo, Processo};
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PastaCliente extends Component
{
    public int    $clienteId;
    public string $aba = 'processos'; // processos | prazos | honorarios | documentos | historico

    public function mount(int $clienteId): void
    {
        $this->clienteId = $clienteId;

        // BelongsToTenant global scope garante isolamento; 404 se não pertencer ao tenant
        Pessoa::findOrFail($clienteId);
    }

    public function render(): \Illuminate\View\View
    {
        $cliente  = Pessoa::findOrFail($this->clienteId);

        // ── Processos ────────────────────────────────────────────────
        $processos = Processo::with(['fase', 'advogado', 'risco', 'tipoAcao'])
            ->where('cliente_id', $this->clienteId)
            ->orderByRaw("CASE status WHEN 'Ativo' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();

        $totalAtivos    = $processos->where('status', 'Ativo')->count();
        $totalArquivados = $processos->where('status', '!=', 'Ativo')->count();

        // ── Prazos próximos (processos do cliente) ───────────────────
        $processosIds = $processos->pluck('id');

        $prazos = Prazo::with(['processo:id,numero', 'responsavel:id,nome'])
            ->whereIn('processo_id', $processosIds)
            ->where('status', 'aberto')
            ->orderBy('data_prazo')
            ->take(20)
            ->get();

        $totalPrazosVencidos = $prazos->filter(fn($p) => $p->data_prazo->isPast())->count();
        $totalPrazosHoje     = $prazos->filter(fn($p) => $p->data_prazo->isToday())->count();

        // ── Honorários em aberto ─────────────────────────────────────
        $parcelas = HonorarioParcela::with(['honorario.processo:id,numero'])
            ->whereHas('honorario', fn($q) => $q->where('cliente_id', $this->clienteId))
            ->whereIn('status', ['pendente', 'vencido'])
            ->orderBy('vencimento')
            ->take(30)
            ->get();

        $totalHonorarios = $parcelas->sum('valor');

        // ── Documentos ───────────────────────────────────────────────
        $documentos = Documento::whereIn('processo_id', $processosIds)
            ->orWhere('cliente_id', $this->clienteId)
            ->orderByDesc('created_at')
            ->take(30)
            ->get();

        // ── Histórico de andamentos ───────────────────────────────────
        $historico = Andamento::with(['processo:id,numero'])
            ->whereIn('processo_id', $processosIds)
            ->orderByDesc('created_at')
            ->take(25)
            ->get();

        // ── Valor total em risco ─────────────────────────────────────
        $valorRisco = $processos->where('status', 'Ativo')->sum('valor_risco');
        $valorCausa = $processos->where('status', 'Ativo')->sum('valor_causa');

        return view('livewire.pasta-cliente', compact(
            'cliente', 'processos', 'totalAtivos', 'totalArquivados',
            'prazos', 'totalPrazosVencidos', 'totalPrazosHoje',
            'parcelas', 'totalHonorarios',
            'documentos', 'historico',
            'valorRisco', 'valorCausa'
        ));
    }
}
