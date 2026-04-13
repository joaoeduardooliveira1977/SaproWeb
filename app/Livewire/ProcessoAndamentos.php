<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Andamento;
use App\Models\Processo;
use App\Services\TribunalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProcessoAndamentos extends Component
{
    use WithFileUploads;

    public int $processoId;
    public object $processo;

    // Formulário de andamento
    public string $data       = '';
    public string $descricao  = '';
    public ?int   $editandoId = null;
    public $arquivo = null; // arquivo opcional ao salvar andamento

    // Confirmação de exclusão
    public ?int $excluindoId = null;

    // Sugestão de prazo automático
    public bool   $sugestaoAutoPrazo   = false;
    public string $sugestaoDescricao   = '';
    public string $sugestaoData        = '';
    public string $sugestaoTitulo      = '';

    // Sugestão IA de próximo passo
    public ?string $sugestaoIA        = null;
    public bool    $carregandoIA      = false;
    public bool    $mostrarSugestaoIA = false;

    // Consulta DATAJUD individual
    public bool    $consultandoDatajud       = false;
    public ?string $resultadoDatajud         = null;
    public bool    $mostrarResultadoDatajud  = false;
    public int     $novosAndamentosDatajud   = 0;

    public bool $mostrarFormulario = false;
    public bool $embed             = false;

    // Modal de upload para andamentos já existentes
    public bool $mostrarUploadModal  = false;
    public ?int $uploadAndamentoId   = null;
    public $arquivoUpload            = null;

    public function mount(int $processoId, bool $embed = false): void
    {
        $this->processoId = $processoId;
        $this->processo   = Processo::findOrFail($processoId);
        $this->data       = now()->format('Y-m-d');
        $this->embed      = $embed;
    }

    public function novoAndamento(): void
    {
        $this->resetForm();
        $this->mostrarFormulario = true;
    }

    public function editar(int $id): void
    {
        $andamento = Andamento::findOrFail($id);
        $this->editandoId        = $id;
        $this->data              = $andamento->data->format('Y-m-d');
        $this->descricao         = $andamento->descricao;
        $this->arquivo           = null;
        $this->mostrarFormulario = true;
    }

    public function salvar(): void
    {
        $this->validate([
            'data'      => 'required|date',
            'descricao' => 'required|string|min:3',
            'arquivo'   => 'nullable|file|max:20480',
        ]);

        if ($this->editandoId) {
            Andamento::findOrFail($this->editandoId)->update([
                'data'      => $this->data,
                'descricao' => $this->descricao,
            ]);
            $andamentoId = $this->editandoId;
        } else {
            $andamentoId = Andamento::create([
                'processo_id' => $this->processoId,
                'data'        => $this->data,
                'descricao'   => $this->descricao,
                'usuario_id'  => Auth::guard('usuarios')->id(),
            ])->id;
        }

        if ($this->arquivo) {
            $this->persistirArquivo($this->arquivo, $andamentoId);
        }

        // Envia e-mail ao cliente somente em novos andamentos
        if (! $this->editandoId) {
            $this->notificarClientePorEmail($this->data, $this->descricao);
            $this->detectarSugestaoPrazo($this->descricao, $this->data);
        }

        $this->resetForm();
        $this->mostrarFormulario = false;
        $this->dispatch('toast', message: 'Andamento salvo com sucesso!', type: 'success');
    }

    // ── Upload para andamento já existente ────────────────────────────

    public function abrirUploadModal(int $andamentoId): void
    {
        $this->uploadAndamentoId = $andamentoId;
        $this->arquivoUpload     = null;
        $this->mostrarUploadModal = true;
    }

    public function fecharUploadModal(): void
    {
        $this->mostrarUploadModal = false;
        $this->uploadAndamentoId  = null;
        $this->arquivoUpload      = null;
    }

    public function salvarUploadAndamento(): void
    {
        $this->validate([
            'arquivoUpload' => 'required|file|max:20480',
        ]);

        $this->persistirArquivo($this->arquivoUpload, $this->uploadAndamentoId);
        $this->fecharUploadModal();
        $this->dispatch('toast', message: 'Arquivo anexado com sucesso!', type: 'success');
    }

    public function excluirDocumento(int $docId): void
    {
        $doc = DB::selectOne('SELECT arquivo FROM documentos WHERE id = ?', [$docId]);
        if ($doc && $doc->arquivo) {
            Storage::disk('public')->delete($doc->arquivo);
        }
        DB::delete('DELETE FROM documentos WHERE id = ?', [$docId]);
        $this->dispatch('toast', message: 'Documento removido com sucesso!', type: 'success');
    }

    // ── Exclusão de andamento ─────────────────────────────────────────

    public function confirmarExclusao(int $id): void
    {
        $this->excluindoId = $id;
    }

    public function excluir(): void
    {
        if ($this->excluindoId) {
            // Remove arquivos vinculados ao andamento antes de excluir
            $docs = DB::select('SELECT arquivo FROM documentos WHERE andamento_id = ?', [$this->excluindoId]);
            foreach ($docs as $doc) {
                Storage::disk('public')->delete($doc->arquivo);
            }
            DB::delete('DELETE FROM documentos WHERE andamento_id = ?', [$this->excluindoId]);

            Andamento::findOrFail($this->excluindoId)->delete();
            $this->excluindoId = null;
            $this->dispatch('toast', message: 'Andamento excluído com sucesso!', type: 'success');
        }
    }

    public function cancelar(): void
    {
        $this->resetForm();
        $this->mostrarFormulario = false;
        $this->excluindoId       = null;
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function notificarClientePorEmail(string $data, string $descricao): void
    {
        $cliente = DB::selectOne(
            'SELECT pe.nome, pe.email
             FROM pessoas pe
             JOIN processos pr ON pr.cliente_id = pe.id
             WHERE pr.id = ? AND pe.portal_ativo = true AND pe.email IS NOT NULL',
            [$this->processoId]
        );

        if (! $cliente) {
            return;
        }

        $numero   = $this->processo->numero ?? '—';
        $dataFmt  = \Carbon\Carbon::parse($data)->format('d/m/Y');
        $sistNome = config('mail.from.name', 'Software Jur�dico');

        $corpo = "
        <div style='font-family:Arial,Helvetica,sans-serif;max-width:600px;margin:0 auto;background:#f1f5f9;border-radius:8px;overflow:hidden;'>
            <div style='background:#1a3a5c;padding:24px 32px;'>
                <div style='color:#93c5fd;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;'>Software Jur�dico — {$sistNome}</div>
                <div style='color:#fff;font-size:20px;font-weight:700;margin-top:8px;'>Atualização no seu processo</div>
            </div>
            <div style='background:#fff;padding:24px 32px;border:1px solid #e2e8f0;border-top:none;'>
                <p style='font-size:14px;color:#334155;margin:0 0 16px;'>Olá, <strong>{$cliente->nome}</strong>!</p>
                <p style='font-size:14px;color:#334155;margin:0 0 20px;'>
                    Há uma nova atualização no processo <strong>{$numero}</strong>:
                </p>
                <div style='background:#f0f9ff;border-left:4px solid #2563a8;border-radius:0 8px 8px 0;padding:14px 18px;margin-bottom:20px;'>
                    <div style='font-size:12px;font-weight:700;color:#0369a1;margin-bottom:6px;'>{$dataFmt}</div>
                    <div style='font-size:14px;color:#1e293b;line-height:1.6;'>{$descricao}</div>
                </div>
                <p style='font-size:13px;color:#64748b;margin:0;'>
                    Acesse o portal do cliente para acompanhar todos os detalhes do seu processo.
                </p>
            </div>
            <div style='background:#e2e8f0;padding:12px 32px;text-align:center;'>
                <span style='font-size:11px;color:#64748b;'>{$sistNome} — Este é um e-mail automático, não responda.</span>
            </div>
        </div>";

        try {
            Mail::html($corpo, function ($msg) use ($cliente, $numero) {
                $msg->to($cliente->email)
                    ->subject("Atualização — Processo {$numero}");
            });
        } catch (\Exception) {
            // Silencia falha de e-mail para não interromper o fluxo
        }
    }

    private function persistirArquivo($arquivo, int $andamentoId): void
    {
        $pasta        = "andamentos/{$this->processoId}";
        $nomeOriginal = $arquivo->getClientOriginalName();
        $mime         = $arquivo->getMimeType();
        $tamanho      = $arquivo->getSize();
        $caminho      = $arquivo->store($pasta, 'public');
        $uploadedBy   = auth('usuarios')->user()?->login ?? 'Sistema';

        DB::insert(
            'INSERT INTO documentos
                (processo_id, andamento_id, tipo, titulo, arquivo, arquivo_original, mime_type, tamanho, data_documento, uploaded_by, portal_visivel, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, false, NOW(), NOW())',
            [
                $this->processoId,
                $andamentoId,
                'outro',
                'Anexo: ' . $nomeOriginal,
                $caminho,
                $nomeOriginal,
                $mime,
                $tamanho,
                $uploadedBy,
            ]
        );
    }

    public function sugerirProximoPasso(): void
    {
        if ($this->carregandoIA) return;
        $this->carregandoIA      = true;
        $this->sugestaoIA        = null;
        $this->mostrarSugestaoIA = false;

        $historico = Andamento::where('processo_id', $this->processoId)
            ->orderBy('data', 'desc')
            ->take(10)
            ->get()
            ->map(fn($a) => '- ' . $a->data->format('d/m/Y') . ': ' . $a->descricao)
            ->join("\n");

        $processo   = $this->processo;
        $tipoAcao   = $processo->tipoAcao?->descricao ?? 'não informado';
        $fase       = $processo->fase?->descricao ?? 'não informada';
        $risco      = $processo->risco?->descricao ?? 'não informado';
        $cliente    = $processo->cliente?->nome ?? 'não informado';
        $parteContr = $processo->parte_contraria ?? 'não informada';

        if (empty($historico)) {
            $this->sugestaoIA        = 'Nenhum andamento cadastrado ainda. Adicione o primeiro andamento para receber sugestões de próximos passos.';
            $this->carregandoIA      = false;
            $this->mostrarSugestaoIA = true;
            return;
        }

        $prompt = "Você é um advogado experiente no direito brasileiro, especialista em direito condominial e cível.

Analise o histórico de andamentos do processo abaixo e sugira o próximo passo jurídico mais adequado.

DADOS DO PROCESSO:
- Tipo de Ação: {$tipoAcao}
- Fase atual: {$fase}
- Grau de Risco: {$risco}
- Cliente: {$cliente}
- Parte Contrária: {$parteContr}

HISTÓRICO DE ANDAMENTOS (mais recentes primeiro):
{$historico}

Responda em formato estruturado com:
1. PRÓXIMO PASSO: (ação recomendada em 1 linha)
2. JUSTIFICATIVA: (explicação em 2-3 linhas)
3. PRAZO SUGERIDO: (se houver prazo legal, informe em dias úteis ou corridos)
4. ATENÇÃO: (alertas importantes, se houver)

Seja objetivo e use linguagem jurídica profissional.";

        $resultado = app(\App\Services\AIService::class)->gerar($prompt, 500);

        if ($resultado === '__IA_BLOQUEADA__') {
            $this->sugestaoIA        = 'IA disponível nos planos Starter e Pro. Faça upgrade para acessar este recurso.';
            $this->carregandoIA      = false;
            $this->mostrarSugestaoIA = true;
            return;
        }

        $this->sugestaoIA        = $resultado ?? 'IA temporariamente indisponível. Tente novamente em instantes.';
        $this->carregandoIA      = false;
        $this->mostrarSugestaoIA = true;
    }

    public function fecharSugestaoIA(): void
    {
        $this->mostrarSugestaoIA = false;
        $this->sugestaoIA        = null;
    }

    public function consultarDatajud(): void
    {
        if ($this->consultandoDatajud) return;
        $this->consultandoDatajud      = true;
        $this->resultadoDatajud        = null;
        $this->mostrarResultadoDatajud = false;
        $this->novosAndamentosDatajud  = 0;

        try {
            $service   = new TribunalService();
            $resultado = $service->consultarProcesso($this->processo->numero);

            if (!$resultado['sucesso']) {
                $this->resultadoDatajud        = '❌ ' . ($resultado['erro'] ?? 'Processo não encontrado no DATAJUD.');
                $this->mostrarResultadoDatajud = true;
                return;
            }

            if (empty($resultado['andamentos'])) {
                $this->resultadoDatajud        = '✅ Processo encontrado no DATAJUD, mas sem andamentos registrados.';
                $this->mostrarResultadoDatajud = true;
                return;
            }

            $novos = 0;
            foreach ($resultado['andamentos'] as $a) {
                if (!$a['data']) continue;

                $existe = Andamento::where('processo_id', $this->processoId)
                    ->whereDate('data', $a['data'])
                    ->where('descricao', $a['descricao'])
                    ->exists();

                if (!$existe) {
                    Andamento::create([
                        'processo_id' => $this->processoId,
                        'data'        => $a['data'],
                        'descricao'   => $a['descricao'],
                    ]);
                    $novos++;
                }
            }

            $this->processo->update(['tjsp_ultima_consulta' => now()]);
            $this->novosAndamentosDatajud = $novos;

            if ($novos > 0) {
                $this->resultadoDatajud = "✅ {$novos} andamento(s) novo(s) importado(s) do DATAJUD!";
                $this->dispatch('toast', message: "{$novos} andamento(s) novo(s) encontrado(s)!", type: 'success');
            } else {
                $this->resultadoDatajud = '✅ Processo já está atualizado — nenhum andamento novo encontrado.';
            }

            $this->mostrarResultadoDatajud = true;

        } catch (\Throwable $e) {
            $this->resultadoDatajud        = '❌ Erro ao consultar DATAJUD: ' . $e->getMessage();
            $this->mostrarResultadoDatajud = true;
        } finally {
            $this->consultandoDatajud = false;
        }
    }

    public function fecharResultadoDatajud(): void
    {
        $this->mostrarResultadoDatajud = false;
        $this->resultadoDatajud        = null;
    }

    public function descartarSugestaoPrazo(): void
    {
        $this->sugestaoAutoPrazo = false;
        $this->sugestaoDescricao = '';
        $this->sugestaoData      = '';
        $this->sugestaoTitulo    = '';
    }

    private function detectarSugestaoPrazo(string $descricao, string $data): void
    {
        $lower = mb_strtolower($descricao);
        $keywords = [
            'intimação'   => ['titulo' => 'Resposta à Intimação', 'dias' => 15, 'tipo' => 'uteis'],
            'intimacao'   => ['titulo' => 'Resposta à Intimação', 'dias' => 15, 'tipo' => 'uteis'],
            'citação'     => ['titulo' => 'Contestação / Prazo de Resposta', 'dias' => 15, 'tipo' => 'uteis'],
            'citacao'     => ['titulo' => 'Contestação / Prazo de Resposta', 'dias' => 15, 'tipo' => 'uteis'],
            'notificação' => ['titulo' => 'Resposta à Notificação', 'dias' => 10, 'tipo' => 'corridos'],
            'notificacao' => ['titulo' => 'Resposta à Notificação', 'dias' => 10, 'tipo' => 'corridos'],
            'despacho'    => ['titulo' => 'Cumprimento de Despacho', 'dias' => 5, 'tipo' => 'uteis'],
            'recurso'     => ['titulo' => 'Interposição de Recurso', 'dias' => 15, 'tipo' => 'uteis'],
        ];

        foreach ($keywords as $kw => $cfg) {
            if (str_contains($lower, $kw)) {
                $dataSugerida = \App\Models\Prazo::calcularData($data, $cfg['dias'], $cfg['tipo']);
                $this->sugestaoAutoPrazo = true;
                $this->sugestaoTitulo    = $cfg['titulo'];
                $this->sugestaoData      = $dataSugerida->format('d/m/Y');
                $this->sugestaoDescricao = "Prazo sugerido: {$cfg['dias']} dias {$cfg['tipo']} a partir de " .
                    \Carbon\Carbon::parse($data)->format('d/m/Y');
                break;
            }
        }
    }

    private function resetForm(): void
    {
        $this->editandoId = null;
        $this->data       = now()->format('Y-m-d');
        $this->descricao  = '';
        $this->arquivo    = null;
    }

    public function render()
    {
        $andamentos = Andamento::where('processo_id', $this->processoId)
            ->orderBy('data', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Carrega documentos vinculados aos andamentos deste processo
        $docsPorAndamento = collect();
        $andamentoIds = $andamentos->pluck('id')->toArray();
        if (! empty($andamentoIds)) {
            $placeholders = implode(',', array_fill(0, count($andamentoIds), '?'));
            $docs = DB::select(
                "SELECT id, andamento_id, titulo, arquivo, arquivo_original, mime_type, tamanho
                 FROM documentos WHERE andamento_id IN ({$placeholders})",
                $andamentoIds
            );
            $docsPorAndamento = collect($docs)->groupBy('andamento_id');
        }

        return view('livewire.processo-andamentos', compact('andamentos', 'docsPorAndamento'));
    }
}
