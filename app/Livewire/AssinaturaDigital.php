<?php

namespace App\Livewire;

use App\Models\Assinatura;
use App\Models\AssinaturaSignatario;
use App\Models\Documento;
use App\Models\Processo;
use App\Services\AssinaturaDigitalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class AssinaturaDigital extends Component
{
    use WithPagination, WithFileUploads;

    // ── Filtros ──────────────────────────────────────────────
    public string $filtroStatus  = '';
    public string $filtroBusca   = '';

    // ── Modal principal ───────────────────────────────────────
    public bool  $modalAberto    = false;
    public ?int  $assinaturaId   = null;

    // ── Formulário ───────────────────────────────────────────
    public string  $titulo        = '';
    public string  $descricao     = '';
    public string  $processo_id   = '';
    public string  $documento_id  = '';
    public string  $deadline_at   = '';
    public         $arquivoUpload = null;   // uploaded file

    // ── Signatários ───────────────────────────────────────────
    public array  $signatarios   = [];  // temp list no form
    // Campos do novo signatário
    public string $sig_nome      = '';
    public string $sig_email     = '';
    public string $sig_cpf       = '';
    public string $sig_celular   = '';
    public string $sig_papel     = 'assinar';
    public string $sig_auth      = 'email';
    public int    $sig_ordem     = 1;

    // ── Modal detalhe ─────────────────────────────────────────
    public bool  $modalDetalhe   = false;
    public ?int  $detalheId      = null;

    // ── Estado de envio ───────────────────────────────────────
    public bool   $enviando      = false;
    public string $erroEnvio     = '';

    public function updatedFiltroBusca(): void  { $this->resetPage(); }
    public function updatedFiltroStatus(): void { $this->resetPage(); }

    // ── Modal principal ───────────────────────────────────────

    public function abrirModal(): void
    {
        $this->resetForm();
        $this->modalAberto = true;
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    // ── Signatários no form ───────────────────────────────────

    public function adicionarSignatario(): void
    {
        $this->validate([
            'sig_nome'  => 'required|min:2',
            'sig_email' => 'required|email',
            'sig_papel' => 'required',
            'sig_auth'  => 'required',
        ], [], [
            'sig_nome'  => 'nome',
            'sig_email' => 'e-mail',
        ]);

        $this->signatarios[] = [
            'nome'    => $this->sig_nome,
            'email'   => $this->sig_email,
            'cpf'     => $this->sig_cpf,
            'celular' => $this->sig_celular,
            'papel'   => $this->sig_papel,
            'auth'    => $this->sig_auth,
            'ordem'   => count($this->signatarios) + 1,
        ];

        $this->sig_nome = $this->sig_email = $this->sig_cpf = $this->sig_celular = '';
        $this->sig_papel = 'assinar';
        $this->sig_auth  = 'email';
        $this->resetErrorBag(['sig_nome', 'sig_email', 'sig_papel', 'sig_auth']);
    }

    public function removerSignatario(int $index): void
    {
        array_splice($this->signatarios, $index, 1);
        // Reordena
        foreach ($this->signatarios as $i => &$sig) {
            $sig['ordem'] = $i + 1;
        }
    }

    // ── Salvar rascunho ───────────────────────────────────────

    public function salvar(): void
    {
        $this->validate([
            'titulo'       => 'required|min:3',
            'processo_id'  => 'nullable|exists:processos,id',
            'documento_id' => 'nullable|exists:documentos,id',
            'deadline_at'  => 'nullable|date|after:today',
            'arquivoUpload'=> 'nullable|file|mimes:pdf|max:20480',
        ]);

        if (empty($this->signatarios)) {
            $this->addError('signatarios', 'Adicione ao menos um signatário.');
            return;
        }

        // Se subiu arquivo, faz o upload para storage
        $arquivoPatch = null;
        $arquivoNome  = null;
        if ($this->arquivoUpload) {
            $arquivoPatch = $this->arquivoUpload->store('assinaturas', 'local');
            $arquivoNome  = $this->arquivoUpload->getClientOriginalName();
        } elseif ($this->documento_id) {
            $doc = Documento::find($this->documento_id);
            $arquivoPatch = $doc?->arquivo;
            $arquivoNome  = $doc?->arquivo_original;
        }

        if (!$arquivoPatch) {
            $this->addError('arquivoUpload', 'Selecione um arquivo PDF ou vincule a um documento existente.');
            return;
        }

        $assinatura = Assinatura::create([
            'titulo'       => $this->titulo,
            'descricao'    => $this->descricao ?: null,
            'processo_id'  => $this->processo_id ?: null,
            'documento_id' => $this->documento_id ?: null,
            'criado_por'   => Auth::id(),
            'arquivo_path' => $arquivoPatch,
            'arquivo_nome' => $arquivoNome,
            'deadline_at'  => $this->deadline_at ?: null,
            'status'       => 'rascunho',
        ]);

        foreach ($this->signatarios as $sig) {
            AssinaturaSignatario::create([
                'assinatura_id' => $assinatura->id,
                'nome'          => $sig['nome'],
                'email'         => $sig['email'],
                'cpf'           => $sig['cpf'] ?: null,
                'celular'       => $sig['celular'] ?: null,
                'papel'         => $sig['papel'],
                'auth'          => $sig['auth'],
                'ordem'         => $sig['ordem'],
                'status'        => 'pendente',
            ]);
        }

        $this->fecharModal();
        session()->flash('sucesso', 'Solicitação de assinatura criada como rascunho.');
    }

    // ── Enviar para ClickSign ─────────────────────────────────

    public function enviarAssinatura(int $id): void
    {
        $assinatura = Assinatura::with('signatarios')->findOrFail($id);
        $this->erroEnvio = '';
        $this->enviando  = true;

        try {
            $service = new AssinaturaDigitalService();

            if (!$service->configurado()) {
                throw new \RuntimeException(
                    'ClickSign não está configurado. Adicione CLICKSIGN_ACCESS_TOKEN ao .env.'
                );
            }

            $signatariosArray = $assinatura->signatarios->map(fn($s) => [
                'nome'    => $s->nome,
                'email'   => $s->email,
                'cpf'     => $s->cpf,
                'celular' => $s->celular,
                'papel'   => $s->papel,
                'auth'    => $s->auth,
            ])->toArray();

            $resultado = $service->enviarParaAssinatura(
                storagePath:  $assinatura->arquivo_path,
                nomeArquivo:  $assinatura->arquivo_nome ?? $assinatura->titulo . '.pdf',
                signatarios:  $signatariosArray,
                deadline:     $assinatura->deadline_at?->toIso8601String(),
            );

            // Atualiza a assinatura com as chaves retornadas
            $assinatura->update([
                'clicksign_document_key' => $resultado['document_key'],
                'clicksign_list_key'     => $resultado['list_key'],
                'status'                 => 'assinando',
                'enviado_em'             => now(),
            ]);

            // Atualiza as chaves individuais dos signatários
            foreach ($resultado['signer_keys'] as $i => $sk) {
                $sig = $assinatura->signatarios->get($i);
                if ($sig) {
                    $sig->update([
                        'clicksign_signer_key' => $sk['key'],
                        'status'               => 'enviado',
                    ]);
                }
            }

            session()->flash('sucesso', 'Documento enviado para assinatura com sucesso!');

        } catch (\Throwable $e) {
            Log::error('AssinaturaDigital: falha ao enviar', [
                'assinatura_id' => $id,
                'erro'          => $e->getMessage(),
            ]);

            $assinatura->update([
                'status'          => 'erro',
                'erro_mensagem'   => $e->getMessage(),
            ]);

            $this->erroEnvio = 'Falha ao enviar: ' . $e->getMessage();
        } finally {
            $this->enviando = false;
        }
    }

    // ── Cancelar ──────────────────────────────────────────────

    public function cancelar(int $id): void
    {
        $assinatura = Assinatura::findOrFail($id);

        if ($assinatura->clicksign_list_key) {
            try {
                (new AssinaturaDigitalService())->cancelarEnvelope($assinatura->clicksign_list_key);
            } catch (\Throwable $e) {
                Log::warning('AssinaturaDigital: falha ao cancelar no ClickSign', [
                    'list_key' => $assinatura->clicksign_list_key,
                    'erro'     => $e->getMessage(),
                ]);
            }
        }

        $assinatura->update(['status' => 'cancelado']);
        session()->flash('sucesso', 'Assinatura cancelada.');
    }

    // ── Sync status manual ────────────────────────────────────

    public function sincronizarStatus(int $id): void
    {
        $assinatura = Assinatura::with('signatarios')->findOrFail($id);

        if (!$assinatura->clicksign_list_key) return;

        try {
            $service = new AssinaturaDigitalService();
            $lista   = $service->consultarStatus($assinatura->clicksign_list_key);

            $statusMap = [
                'running'   => 'assinando',
                'closed'    => 'concluido',
                'canceled'  => 'cancelado',
            ];

            $novoStatus = $statusMap[$lista['status'] ?? ''] ?? $assinatura->status;
            $assinatura->update(['status' => $novoStatus]);

            session()->flash('sucesso', 'Status sincronizado: ' . Assinatura::statusLabel()[$novoStatus]);

        } catch (\Throwable $e) {
            session()->flash('erro', 'Falha ao sincronizar: ' . $e->getMessage());
        }
    }

    // ── Detalhe modal ─────────────────────────────────────────

    public function verDetalhe(int $id): void
    {
        $this->detalheId   = $id;
        $this->modalDetalhe = true;
    }

    public function excluir(int $id): void
    {
        $assinatura = Assinatura::findOrFail($id);
        if (!in_array($assinatura->status, ['rascunho', 'cancelado', 'erro'])) {
            session()->flash('erro', 'Só é possível excluir rascunhos ou cancelados.');
            return;
        }
        $assinatura->delete();
        session()->flash('sucesso', 'Solicitação excluída.');
    }

    // ── Helpers ───────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->assinaturaId  = null;
        $this->titulo        = '';
        $this->descricao     = '';
        $this->processo_id   = '';
        $this->documento_id  = '';
        $this->deadline_at   = now()->addDays(7)->format('Y-m-d');
        $this->arquivoUpload = null;
        $this->signatarios   = [];
        $this->sig_nome = $this->sig_email = $this->sig_cpf = $this->sig_celular = '';
        $this->sig_papel = 'assinar';
        $this->sig_auth  = 'email';
        $this->resetErrorBag();
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        $assinaturas = Assinatura::with(['processo.cliente', 'signatarios'])
            ->when($this->filtroStatus, fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroBusca, fn($q) =>
                $q->where(fn($s) =>
                    $s->where('titulo', 'ilike', "%{$this->filtroBusca}%")
                      ->orWhereHas('processo', fn($p) =>
                          $p->where('numero', 'ilike', "%{$this->filtroBusca}%")
                      )
                )
            )
            ->latest()
            ->paginate(15);

        $processos  = Processo::where('status', 'Ativo')->orderBy('numero')->get();
        $documentos = Documento::orderByDesc('created_at')->limit(100)->get();

        $detalhe = $this->detalheId
            ? Assinatura::with('signatarios')->find($this->detalheId)
            : null;

        $kpis = [
            'rascunhos'  => Assinatura::where('status', 'rascunho')->count(),
            'assinando'  => Assinatura::whereIn('status', ['enviado', 'assinando'])->count(),
            'concluidos' => Assinatura::where('status', 'concluido')
                ->whereMonth('concluido_em', now()->month)->count(),
            'configurado'=> (new AssinaturaDigitalService())->configurado(),
        ];

        return view('livewire.assinatura-digital', compact(
            'assinaturas', 'processos', 'documentos', 'detalhe', 'kpis'
        ));
    }
}
