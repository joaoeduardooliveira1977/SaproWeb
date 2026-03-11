<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Documentos extends Component
{
    use WithFileUploads;

    // Filtros
    public string $busca = '';
    public string $filtroTipo = '';
    public string $filtroVinculo = '';

    // Modal
    public bool $modalDocumento = false;

    // Form
    public ?int $documentoId = null;
    public ?int $processo_id = null;
    public ?int $cliente_id = null;
    public string $tipo = 'peticao';
    public string $titulo = '';
    public string $descricao = '';
    public string $data_documento = '';
    public $arquivo = null;

    // Dados auxiliares
    public array $processos = [];
    public array $clientes = [];

    protected array $rules = [
        'titulo'         => 'required|min:3',
        'tipo'           => 'required',
        'data_documento' => 'nullable|date',
        'arquivo'        => 'nullable|file|max:20480', // 20MB
    ];

    public function mount(): void
    {
        $this->data_documento = now()->format('Y-m-d');
        $this->carregarDados();
    }

    private function carregarDados(): void
    {
        $this->clientes = DB::select("
            SELECT p.id, p.nome
            FROM pessoas p
            JOIN pessoa_tipos pt ON pt.pessoa_id = p.id
            WHERE pt.tipo = 'Cliente' AND p.ativo = true
            ORDER BY p.nome
        ");

        $this->processos = DB::select("
            SELECT p.id, p.numero, pe.nome as cliente_nome
            FROM processos p
            JOIN pessoas pe ON pe.id = p.cliente_id
            WHERE p.status = 'Ativo'
            ORDER BY p.numero
        ");
    }

    public function novoDocumento(): void
    {
        $this->reset(['documentoId','processo_id','cliente_id','tipo','titulo','descricao','arquivo']);
        $this->tipo = 'peticao';
        $this->data_documento = now()->format('Y-m-d');
        $this->modalDocumento = true;
    }

    public function editarDocumento(int $id): void
    {
        $d = DB::selectOne("SELECT * FROM documentos WHERE id = ?", [$id]);
        if (!$d) return;

        $this->documentoId    = $d->id;
        $this->processo_id    = $d->processo_id;
        $this->cliente_id     = $d->cliente_id;
        $this->tipo           = $d->tipo;
        $this->titulo         = $d->titulo;
        $this->descricao      = $d->descricao ?? '';
        $this->data_documento = $d->data_documento ?? '';
        $this->arquivo        = null;
        $this->modalDocumento = true;
    }

    public function salvarDocumento(): void
    {
        $this->validate();

        $dados = [
            'processo_id'    => $this->processo_id ?: null,
            'cliente_id'     => $this->cliente_id ?: null,
            'tipo'           => $this->tipo,
            'titulo'         => $this->titulo,
            'descricao'      => $this->descricao ?: null,
            'data_documento' => $this->data_documento ?: null,
            'uploaded_by'    => auth()->user()->nome ?? 'Sistema',
        ];

        // Upload do arquivo
        if ($this->arquivo) {
            $nomeOriginal = $this->arquivo->getClientOriginalName();
            $mime         = $this->arquivo->getMimeType();
            $tamanho      = $this->arquivo->getSize();
            $caminho      = $this->arquivo->store('documentos', 'public');

            $dados['arquivo']          = $caminho;
            $dados['arquivo_original'] = $nomeOriginal;
            $dados['mime_type']        = $mime;
            $dados['tamanho']          = $tamanho;
        }

        if ($this->documentoId) {
            $sets = implode(', ', array_map(fn($k) => "{$k}=?", array_keys($dados)));
            $dados['updated_at'] = now();
            DB::update(
                "UPDATE documentos SET {$sets}, updated_at=NOW() WHERE id=?",
                [...array_values($dados), $this->documentoId]
            );
        } else {
            if (empty($dados['arquivo'])) {
                $this->addError('arquivo', 'O arquivo é obrigatório para novos documentos.');
                return;
            }
            $cols = implode(', ', array_keys($dados));
            $placeholders = implode(', ', array_fill(0, count($dados), '?'));
            DB::insert(
                "INSERT INTO documentos ({$cols}, created_at, updated_at) VALUES ({$placeholders}, NOW(), NOW())",
                array_values($dados)
            );
        }

        $this->modalDocumento = false;
        $this->reset(['arquivo']);
        session()->flash('success', 'Documento salvo com sucesso!');
    }

    public function excluirDocumento(int $id): void
    {
        $doc = DB::selectOne("SELECT arquivo FROM documentos WHERE id = ?", [$id]);
        if ($doc && $doc->arquivo) {
            Storage::disk('public')->delete($doc->arquivo);
        }
        DB::delete("DELETE FROM documentos WHERE id = ?", [$id]);
        session()->flash('success', 'Documento excluído.');
    }

    public function downloadUrl(int $id): void
    {
        $doc = DB::selectOne("SELECT arquivo FROM documentos WHERE id = ?", [$id]);
        if ($doc) {
            $this->dispatch('download', url: Storage::url($doc->arquivo));
        }
    }

    public function render()
    {
        // Resumo
        $resumo = DB::selectOne("
            SELECT
                COUNT(*) as total,
                SUM(tamanho) as total_tamanho,
                SUM(CASE WHEN tipo='peticao' THEN 1 ELSE 0 END) as peticoes,
                SUM(CASE WHEN tipo='contrato' THEN 1 ELSE 0 END) as contratos,
                SUM(CASE WHEN tipo='sentenca' THEN 1 ELSE 0 END) as sentencas,
                SUM(CASE WHEN tipo='documento_cliente' THEN 1 ELSE 0 END) as docs_cliente
            FROM documentos
        ");

        // Lista
        $where = "WHERE 1=1";
        $params = [];

        if ($this->busca) {
            $where .= " AND (d.titulo ILIKE ? OR pe.nome ILIKE ? OR pr.numero ILIKE ?)";
            $params = array_merge($params, ["%{$this->busca}%", "%{$this->busca}%", "%{$this->busca}%"]);
        }
        if ($this->filtroTipo) {
            $where .= " AND d.tipo = ?";
            $params[] = $this->filtroTipo;
        }
        if ($this->filtroVinculo === 'processo') {
            $where .= " AND d.processo_id IS NOT NULL";
        } elseif ($this->filtroVinculo === 'cliente') {
            $where .= " AND d.cliente_id IS NOT NULL AND d.processo_id IS NULL";
        }

        $documentos = DB::select("
            SELECT d.*,
                pe.nome as cliente_nome,
                pr.numero as processo_numero
            FROM documentos d
            LEFT JOIN pessoas pe ON pe.id = d.cliente_id
            LEFT JOIN processos pr ON pr.id = d.processo_id
            {$where}
            ORDER BY d.created_at DESC
            LIMIT 100
        ", $params);

        return view('livewire.documentos', compact('resumo', 'documentos'));
    }
}
