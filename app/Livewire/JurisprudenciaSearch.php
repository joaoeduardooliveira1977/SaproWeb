<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProcessoJurisprudencia;
use App\Models\Processo;
use Illuminate\Support\Facades\Http;

class JurisprudenciaSearch extends Component
{
    public int    $processoId;
    public bool   $embed = false;

    // ── Busca ──────────────────────────────────
    public string $busca     = '';
    public string $tribunal  = 'stj';
    public array  $resultados = [];
    public bool   $carregando = false;
    public ?string $erro      = null;
    public bool   $buscaRealizada = false;

    // ── Formulário de salvar ───────────────────
    public bool   $formAberto    = false;
    public string $formTribunal  = 'STJ';
    public string $formNumero    = '';
    public string $formEmenta    = '';
    public string $formRelator   = '';
    public string $formData      = '';
    public string $formUrl       = '';
    public string $formObs       = '';
    public ?int   $editandoId    = null;

    protected function rules(): array
    {
        return [
            'formTribunal' => 'required|string|max:20',
            'formNumero'   => 'nullable|string|max:120',
            'formEmenta'   => 'nullable|string',
            'formRelator'  => 'nullable|string|max:200',
            'formData'     => 'nullable|date',
            'formUrl'      => 'nullable|url|max:1000',
            'formObs'      => 'nullable|string',
        ];
    }

    public function mount(int $processoId, bool $embed = false): void
    {
        $this->processoId = $processoId;
        $this->embed      = $embed;

        $processo = Processo::with(['tipoAcao'])->find($processoId);
        if ($processo) {
            $termos = array_filter([
                $processo->tipoAcao?->descricao,
            ]);
            $this->busca = implode(' ', array_slice($termos, 0, 2));
        }
    }

    public function pesquisar(): void
    {
        $this->buscaRealizada = false;
        $this->resultados     = [];
        $this->erro           = null;

        if (blank($this->busca)) return;

        $this->carregando = true;

        try {
            if ($this->tribunal === 'stj') {
                $res = Http::timeout(12)->get(
                    'https://jurisprudencia.stj.jus.br/webapi/rest/pesquisa-acumulo',
                    ['palavrasChave' => $this->busca, 'numeroRegistros' => 10, 'pagina' => 1]
                );

                if ($res->successful()) {
                    $body = $res->json();
                    $docs = $body['documentos'] ?? $body['results'] ?? [];

                    foreach ($docs as $doc) {
                        $this->resultados[] = [
                            'tribunal' => 'STJ',
                            'numero'   => $doc['numero'] ?? $doc['numeroAcordao'] ?? ($doc['id'] ?? ''),
                            'ementa'   => $doc['ementa'] ?? $doc['ementaFormatada'] ?? '',
                            'relator'  => $doc['relator'] ?? $doc['ministroRelator'] ?? '',
                            'data'     => $doc['dataJulgamento'] ?? $doc['data'] ?? '',
                            'url'      => $doc['urlAcordao'] ?? $doc['urlDocumento'] ?? null,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // silent — fallback to links only
        }

        $this->carregando     = false;
        $this->buscaRealizada = true;
    }

    public function preencherFormulario(array $resultado): void
    {
        $this->formAberto   = true;
        $this->editandoId   = null;
        $this->formTribunal = $resultado['tribunal'] ?? 'STJ';
        $this->formNumero   = $resultado['numero']   ?? '';
        $this->formEmenta   = $resultado['ementa']   ?? '';
        $this->formRelator  = $resultado['relator']  ?? '';
        $this->formData     = $resultado['data']     ?? '';
        $this->formUrl      = $resultado['url']      ?? '';
        $this->formObs      = '';
    }

    public function abrirForm(): void
    {
        $this->resetForm();
        $this->formAberto = true;
    }

    public function editarSalva(int $id): void
    {
        $j = ProcessoJurisprudencia::find($id);
        if (!$j) return;

        $this->editandoId   = $id;
        $this->formAberto   = true;
        $this->formTribunal = $j->tribunal;
        $this->formNumero   = $j->numero_acordao   ?? '';
        $this->formEmenta   = $j->ementa            ?? '';
        $this->formRelator  = $j->relator            ?? '';
        $this->formData     = $j->data_julgamento   ? $j->data_julgamento->format('Y-m-d') : '';
        $this->formUrl      = $j->url               ?? '';
        $this->formObs      = $j->observacoes       ?? '';
    }

    public function salvar(): void
    {
        $this->validate();

        $tenantId = auth('usuarios')->user()->tenant_id;
        $userId   = auth('usuarios')->id();

        $dados = [
            'processo_id'    => $this->processoId,
            'tenant_id'      => $tenantId,
            'user_id'        => $userId,
            'tribunal'       => $this->formTribunal,
            'numero_acordao' => $this->formNumero   ?: null,
            'ementa'         => $this->formEmenta   ?: null,
            'relator'        => $this->formRelator  ?: null,
            'data_julgamento'=> $this->formData     ?: null,
            'url'            => $this->formUrl      ?: null,
            'observacoes'    => $this->formObs      ?: null,
        ];

        if ($this->editandoId) {
            ProcessoJurisprudencia::where('id', $this->editandoId)
                ->where('tenant_id', $tenantId)
                ->update($dados);
            $msg = 'Jurisprudência atualizada.';
        } else {
            ProcessoJurisprudencia::create($dados);
            $msg = 'Jurisprudência salva no processo.';
        }

        $this->resetForm();
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function excluir(int $id): void
    {
        ProcessoJurisprudencia::where('id', $id)
            ->where('tenant_id', auth('usuarios')->user()->tenant_id)
            ->delete();
        $this->dispatch('toast', message: 'Jurisprudência removida.', type: 'success');
    }

    private function resetForm(): void
    {
        $this->formAberto   = false;
        $this->editandoId   = null;
        $this->formTribunal = 'STJ';
        $this->formNumero   = '';
        $this->formEmenta   = '';
        $this->formRelator  = '';
        $this->formData     = '';
        $this->formUrl      = '';
        $this->formObs      = '';
    }

    public function urlBusca(string $tribunal): string
    {
        $q = urlencode($this->busca ?: '');
        return match ($tribunal) {
            'stj'   => "https://jurisprudencia.stj.jus.br/pesquisa/?q={$q}",
            'stf'   => "https://jurisprudencia.stf.jus.br/pages/search?base=acordaos&pesquisa_inteiro_teor=false&sinonimo=true&plural=true&radicais=false&bvb_start=false&termo={$q}",
            'tjsp'  => "https://esaj.tjsp.jus.br/cjsg/resultadoCompleta.do?dados.buscaInteiroTeor={$q}",
            'trf1'  => "https://jurisprudencia.trf1.jus.br/pages/search?q={$q}",
            'trf3'  => "https://web.trf3.jus.br/base-textual/Home/ListaAcordaos?np={$q}",
            'jusbrasil' => "https://www.jusbrasil.com.br/jurisprudencia/busca?q={$q}",
            default => "https://www.jusbrasil.com.br/jurisprudencia/busca?q={$q}",
        };
    }

    public function render(): \Illuminate\View\View
    {
        $salvas = ProcessoJurisprudencia::where('processo_id', $this->processoId)
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.jurisprudencia-search', compact('salvas'));
    }
}
