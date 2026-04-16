<?php

namespace App\Livewire;

use App\Models\{Andamento, Documento, HonorarioParcela, Pessoa, Prazo, Processo};
use Illuminate\Support\Facades\{Auth, DB, Storage};
use Livewire\Component;
use Livewire\WithFileUploads;

class PastaCliente extends Component
{
    use WithFileUploads;

    public int    $clienteId;
    public string $aba = 'processos'; // processos | prazos | honorarios | documentos | historico

    // ── Upload de documento ───────────────────────────────────
    public bool    $modalDoc       = false;
    public ?int    $docId          = null;
    public string  $docTitulo      = '';
    public string  $docTipo        = 'outro';
    public string  $docDescricao   = '';
    public string  $docData        = '';
    public         $docArquivo     = null;

    public const TIPOS_DOC = [
        'contrato'       => 'Contrato',
        'procuracao'     => 'Procuração',
        'identidade'     => 'Identidade / CPF',
        'comprovante'    => 'Comprovante',
        'peticao'        => 'Petição',
        'decisao'        => 'Decisão',
        'certidao'       => 'Certidão',
        'outro'          => 'Outro',
    ];

    public function mount(int $clienteId): void
    {
        $this->clienteId = $clienteId;
        $this->docData   = now()->format('Y-m-d');

        // BelongsToTenant global scope garante isolamento; 404 se não pertencer ao tenant
        Pessoa::findOrFail($clienteId);
    }

    // ── Abrir modal de upload ─────────────────────────────────
    public function abrirModalDoc(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->docId        = $id;
        $this->docArquivo   = null;

        if ($id) {
            $doc = Documento::findOrFail($id);
            $this->docTitulo    = $doc->titulo;
            $this->docTipo      = $doc->tipo ?? 'outro';
            $this->docDescricao = $doc->descricao ?? '';
            $this->docData      = $doc->data_documento?->format('Y-m-d') ?? now()->format('Y-m-d');
        } else {
            $this->docTitulo    = '';
            $this->docTipo      = 'outro';
            $this->docDescricao = '';
            $this->docData      = now()->format('Y-m-d');
        }

        $this->modalDoc = true;
    }

    public function fecharModalDoc(): void
    {
        $this->modalDoc   = false;
        $this->docArquivo = null;
        $this->resetErrorBag();
    }

    // ── Salvar documento ──────────────────────────────────────
    public function salvarDocumento(): void
    {
        $this->validate([
            'docTitulo'   => 'required|string|max:200',
            'docTipo'     => 'required|string',
            'docData'     => 'nullable|date',
            'docArquivo'  => $this->docId ? 'nullable|file|max:20480' : 'required|file|max:20480',
        ], [
            'docTitulo.required'  => 'O título é obrigatório.',
            'docArquivo.required' => 'Selecione um arquivo.',
            'docArquivo.max'      => 'O arquivo não pode passar de 20 MB.',
        ]);

        $dados = [
            'cliente_id'     => $this->clienteId,
            'processo_id'    => null,
            'tipo'           => $this->docTipo,
            'titulo'         => $this->docTitulo,
            'descricao'      => $this->docDescricao ?: null,
            'data_documento' => $this->docData ?: null,
            'uploaded_by'    => Auth::guard('usuarios')->user()?->nome ?? 'Sistema',
        ];

        if ($this->docArquivo) {
            $dados['arquivo']          = $this->docArquivo->store('documentos', 'public');
            $dados['arquivo_original'] = $this->docArquivo->getClientOriginalName();
            $dados['mime_type']        = $this->docArquivo->getMimeType();
            $dados['tamanho']          = $this->docArquivo->getSize();
        }

        if ($this->docId) {
            DB::table('documentos')->where('id', $this->docId)->update(array_merge($dados, ['updated_at' => now()]));
        } else {
            DB::table('documentos')->insert(array_merge($dados, ['created_at' => now(), 'updated_at' => now()]));
        }

        $this->fecharModalDoc();
        $this->dispatch('toast', message: 'Documento salvo com sucesso!', type: 'success');
    }

    // ── Excluir documento ─────────────────────────────────────
    public function excluirDocumento(int $id): void
    {
        $doc = Documento::findOrFail($id);
        if ($doc->arquivo) {
            Storage::disk('public')->delete($doc->arquivo);
        }
        $doc->delete();
        $this->dispatch('toast', message: 'Documento excluído.', type: 'success');
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

        $tiposDoc = self::TIPOS_DOC;

        return view('livewire.pasta-cliente', compact(
            'cliente', 'processos', 'totalAtivos', 'totalArquivados',
            'prazos', 'totalPrazosVencidos', 'totalPrazosHoje',
            'parcelas', 'totalHonorarios',
            'documentos', 'historico',
            'valorRisco', 'valorCausa',
            'tiposDoc'
        ));
    }
}
