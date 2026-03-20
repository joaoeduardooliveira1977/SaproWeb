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
    public string $filtroDataIni = '';
    public string $filtroDataFim = '';

    // IA
    public string  $perguntaIA = '';
    public ?string $respostaIA = null;

    protected $queryString = [
        'busca'          => ['except' => ''],
        'filtroTipo'     => ['except' => ''],
        'filtroVinculo'  => ['except' => ''],
        'filtroDataIni'  => ['except' => ''],
        'filtroDataFim'  => ['except' => ''],
    ];

    // Ordenação
    public string $ordenarPor  = 'created_at';
    public string $ordenarDir  = 'DESC';

    // Preview
    public bool   $modalPreview  = false;
    public string $previewUrl    = '';
    public string $previewTitulo = '';
    public string $previewMime   = '';

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

    // Upload em lote
    public bool   $modalLote      = false;
    public array  $arquivosLote   = [];
    public string $loteTipo       = 'peticao';
    public string $loteProcessoId = '';
    public string $loteClienteId  = '';
    public string $loteData       = '';
    public array  $loteResultados = []; // ['nome', 'ok', 'msg']

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

    public function abrirLote(): void
    {
        $this->reset(['arquivosLote','loteProcessoId','loteClienteId','loteResultados']);
        $this->loteTipo = 'peticao';
        $this->loteData = now()->format('Y-m-d');
        $this->modalLote = true;
    }

    public function fecharLote(): void
    {
        $this->modalLote    = false;
        $this->loteResultados = [];
        $this->reset(['arquivosLote']);
    }

    public function salvarLote(): void
    {
        $this->validate([
            'arquivosLote'   => 'required|array|min:1',
            'arquivosLote.*' => 'file|max:20480',
            'loteTipo'       => 'required',
        ], [], [
            'arquivosLote'   => 'arquivos',
            'arquivosLote.*' => 'arquivo',
        ]);

        $this->loteResultados = [];
        $salvos = 0;

        foreach ($this->arquivosLote as $arq) {
            try {
                $nomeOriginal = $arq->getClientOriginalName();
                $tituloArq    = pathinfo($nomeOriginal, PATHINFO_FILENAME);

                $caminho = $arq->store('documentos', 'public');

                DB::insert(
                    "INSERT INTO documentos (processo_id, cliente_id, tipo, titulo, data_documento, arquivo, arquivo_original, mime_type, tamanho, uploaded_by, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
                    [
                        $this->loteProcessoId ?: null,
                        $this->loteClienteId  ?: null,
                        $this->loteTipo,
                        $tituloArq,
                        $this->loteData ?: null,
                        $caminho,
                        $nomeOriginal,
                        $arq->getMimeType(),
                        $arq->getSize(),
                        auth()->user()->nome ?? 'Sistema',
                    ]
                );

                $this->loteResultados[] = ['nome' => $nomeOriginal, 'ok' => true,  'msg' => 'Salvo'];
                $salvos++;
            } catch (\Throwable $e) {
                $this->loteResultados[] = ['nome' => $arq->getClientOriginalName(), 'ok' => false, 'msg' => $e->getMessage()];
            }
        }

        $this->reset(['arquivosLote']);

        if ($salvos > 0) {
            $this->dispatch('toast', message: "{$salvos} documento(s) enviado(s)!", type: 'success');
        }
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
        $this->dispatch('toast', message: 'Documento salvo com sucesso!', type: 'success');
    }

    public function ordenar(string $coluna): void
    {
        if ($this->ordenarPor === $coluna) {
            $this->ordenarDir = $this->ordenarDir === 'ASC' ? 'DESC' : 'ASC';
        } else {
            $this->ordenarPor = $coluna;
            $this->ordenarDir = 'DESC';
        }
    }

    public function abrirPreview(int $id): void
    {
        $doc = DB::selectOne("SELECT titulo, arquivo, mime_type FROM documentos WHERE id = ?", [$id]);
        if (! $doc || ! $doc->arquivo) return;

        $this->previewUrl    = Storage::url($doc->arquivo);
        $this->previewTitulo = $doc->titulo;
        $this->previewMime   = $doc->mime_type ?? '';
        $this->modalPreview  = true;
    }

    public function fecharPreview(): void
    {
        $this->modalPreview  = false;
        $this->previewUrl    = '';
        $this->previewTitulo = '';
        $this->previewMime   = '';
    }

    public function togglePortalVisivel(int $id): void
    {
        $doc = DB::selectOne("SELECT portal_visivel FROM documentos WHERE id = ?", [$id]);
        if (! $doc) return;

        $novo = $doc->portal_visivel ? 0 : 1;
        DB::update("UPDATE documentos SET portal_visivel = ?, updated_at = NOW() WHERE id = ?", [$novo, $id]);
    }

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        [$where, $params] = $this->buildWhere();

        $docs = DB::select("
            SELECT d.titulo, d.tipo, d.data_documento, d.arquivo_original,
                   d.tamanho, d.portal_visivel, d.created_at,
                   pe.nome as cliente_nome, pr.numero as processo_numero
            FROM documentos d
            LEFT JOIN pessoas pe ON pe.id = d.cliente_id
            LEFT JOIN processos pr ON pr.id = d.processo_id
            {$where}
            ORDER BY d.{$this->ordenarPor} {$this->ordenarDir}
        ", $params);

        $tipos = ['peticao'=>'Petição','contrato'=>'Contrato','procuracao'=>'Procuração',
                  'laudo'=>'Laudo','documento_cliente'=>'Doc. Cliente','sentenca'=>'Sentença','outro'=>'Outro'];

        return response()->streamDownload(function () use ($docs, $tipos) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Título','Tipo','Data','Cliente','Processo','Arquivo','Tamanho','Portal','Cadastrado em'], ';');
            foreach ($docs as $d) {
                $kb = $d->tamanho ? ($d->tamanho > 1048576 ? number_format($d->tamanho/1048576,1).' MB' : number_format($d->tamanho/1024,0).' KB') : '';
                fputcsv($out, [
                    $d->titulo,
                    $tipos[$d->tipo] ?? $d->tipo,
                    $d->data_documento ? \Carbon\Carbon::parse($d->data_documento)->format('d/m/Y') : '',
                    $d->cliente_nome ?? '',
                    $d->processo_numero ?? '',
                    $d->arquivo_original ?? '',
                    $kb,
                    $d->portal_visivel ? 'Sim' : 'Não',
                    \Carbon\Carbon::parse($d->created_at)->format('d/m/Y'),
                ], ';');
            }
            fclose($out);
        }, 'documentos-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function excluirDocumento(int $id): void
    {
        $doc = DB::selectOne("SELECT arquivo FROM documentos WHERE id = ?", [$id]);
        if ($doc && $doc->arquivo) {
            Storage::disk('public')->delete($doc->arquivo);
        }
        DB::delete("DELETE FROM documentos WHERE id = ?", [$id]);
        $this->dispatch('toast', message: 'Documento excluído.', type: 'success');
    }

    public function perguntarIA(): void
    {
        if (empty(trim($this->perguntaIA))) return;

        $resumo = DB::selectOne("
            SELECT COUNT(*) as total,
                SUM(tamanho) as total_tamanho,
                SUM(CASE WHEN tipo='peticao' THEN 1 ELSE 0 END) as peticoes,
                SUM(CASE WHEN tipo='contrato' THEN 1 ELSE 0 END) as contratos,
                SUM(CASE WHEN tipo='sentenca' THEN 1 ELSE 0 END) as sentencas,
                SUM(CASE WHEN tipo='procuracao' THEN 1 ELSE 0 END) as procuracoes,
                SUM(CASE WHEN tipo='documento_cliente' THEN 1 ELSE 0 END) as docs_cliente,
                SUM(CASE WHEN portal_visivel = true THEN 1 ELSE 0 END) as portal_visiveis
            FROM documentos
        ");

        $tamanhoTotal = $resumo->total_tamanho
            ? number_format($resumo->total_tamanho / 1024 / 1024, 1) . ' MB'
            : '0 MB';

        $contexto = "Você é um assistente jurídico do sistema SAPRO. Responda de forma objetiva em português.

Dados do arquivo de documentos:
- Total de documentos: {$resumo->total}
- Tamanho total: {$tamanhoTotal}
- Petições: {$resumo->peticoes}
- Contratos: {$resumo->contratos}
- Sentenças: {$resumo->sentencas}
- Procurações: {$resumo->procuracoes}
- Docs. Cliente: {$resumo->docs_cliente}
- Visíveis no portal: {$resumo->portal_visiveis}

Pergunta: {$this->perguntaIA}

Responda em 1-3 frases objetivas. Se pedir para filtrar, termine com: FILTRO:tipo=valor ou FILTRO:busca=texto ou FILTRO:vinculo=processo|cliente";

        $resposta = app(\App\Services\GeminiService::class)->gerar($contexto, 300);

        if ($resposta === null) {
            $this->respostaIA = 'IA temporariamente indisponível.';
            return;
        }

        if (str_contains($resposta, 'FILTRO:')) {
            preg_match('/FILTRO:(\w+)=(.+)/', $resposta, $matches);
            if (count($matches) === 3) {
                $campo = trim($matches[1]);
                $valor = trim($matches[2]);
                if ($campo === 'busca')   $this->busca        = $valor;
                if ($campo === 'tipo')    $this->filtroTipo   = $valor;
                if ($campo === 'vinculo') $this->filtroVinculo = $valor;
                $resposta = trim(preg_replace('/FILTRO:\w+=.+/', '', $resposta));
            }
        }

        $this->respostaIA = $resposta;
    }

    public function limparIA(): void
    {
        $this->perguntaIA = '';
        $this->respostaIA = null;
    }

    public function downloadUrl(int $id): void
    {
        $doc = DB::selectOne("SELECT arquivo FROM documentos WHERE id = ?", [$id]);
        if ($doc) {
            $this->dispatch('download', url: Storage::url($doc->arquivo));
        }
    }

    private function buildWhere(): array
    {
        $where  = "WHERE 1=1";
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
        if ($this->filtroDataIni) {
            $where .= " AND d.data_documento >= ?";
            $params[] = $this->filtroDataIni;
        }
        if ($this->filtroDataFim) {
            $where .= " AND d.data_documento <= ?";
            $params[] = $this->filtroDataFim;
        }

        return [$where, $params];
    }

    public function render()
    {
        $resumo = DB::selectOne("
            SELECT COUNT(*) as total,
                SUM(tamanho) as total_tamanho,
                SUM(CASE WHEN tipo='peticao' THEN 1 ELSE 0 END) as peticoes,
                SUM(CASE WHEN tipo='contrato' THEN 1 ELSE 0 END) as contratos,
                SUM(CASE WHEN tipo='sentenca' THEN 1 ELSE 0 END) as sentencas,
                SUM(CASE WHEN tipo='procuracao' THEN 1 ELSE 0 END) as procuracoes,
                SUM(CASE WHEN tipo='laudo' THEN 1 ELSE 0 END) as laudos,
                SUM(CASE WHEN tipo='documento_cliente' THEN 1 ELSE 0 END) as docs_cliente,
                SUM(CASE WHEN tipo='outro' THEN 1 ELSE 0 END) as outros
            FROM documentos
        ");

        [$where, $params] = $this->buildWhere();

        $colunasSafe = ['created_at', 'data_documento', 'titulo', 'tipo', 'tamanho'];
        $col = in_array($this->ordenarPor, $colunasSafe) ? $this->ordenarPor : 'created_at';
        $dir = $this->ordenarDir === 'ASC' ? 'ASC' : 'DESC';

        $documentos = DB::select("
            SELECT d.*, pe.nome as cliente_nome, pr.numero as processo_numero
            FROM documentos d
            LEFT JOIN pessoas pe ON pe.id = d.cliente_id
            LEFT JOIN processos pr ON pr.id = d.processo_id
            {$where}
            ORDER BY d.{$col} {$dir}
            LIMIT 200
        ", $params);

        return view('livewire.documentos', compact('resumo', 'documentos'));
    }
}
