<?php

namespace App\Livewire;

use App\Services\TribunalService;
use Livewire\Component;
use App\Models\Processo;
use App\Models\Pessoa;
use App\Models\TjspVerificacao;
use App\Jobs\VerificarAndamentosTjsp;

class TjspConsulta extends Component
{
    public ?int    $verificacaoId  = null;
    public string  $erroMensagem   = '';
    public bool    $consultando    = false;

    // Filtros
    public string $filtroCliente    = '';
    public string $filtroFase       = '';
    public string $filtroAdvogado   = '';
    public string $filtroStatus     = 'Ativo';
    public string $filtroConsulta   = ''; // nunca, hoje, semana, mes

    public function iniciarVerificacao(): void
    {
        $this->erroMensagem = '';
        $this->consultando  = true;

        try {
            // Reset qualquer verificação travada (pendente OU rodando)
            TjspVerificacao::whereIn('status', ['pendente', 'rodando'])
                ->update(['status' => 'erro', 'concluido_em' => now()]);

            $processos = $this->queryProcessos();

            if ($processos->isEmpty()) {
                $this->erroMensagem = 'Nenhum processo encontrado com os filtros selecionados.';
                return;
            }

            // Pré-valida: ao menos um processo deve ter número CNJ reconhecível
            $service = new TribunalService();
            $semTribunal = $processos->filter(
                fn($p) => $service->detectarTribunal($p->numero) === null
            );

            if ($semTribunal->count() === $processos->count()) {
                $this->erroMensagem =
                    'Nenhum processo selecionado possui número CNJ reconhecível pelo DATAJUD. ' .
                    'Verifique se os números estão no formato correto (ex: 0001234-56.2023.8.26.0001).';
                return;
            }

            if ($semTribunal->isNotEmpty()) {
                // Avisa sobre os não reconhecidos mas continua com os válidos
                $processos = $processos->diff($semTribunal);
            }

            $processoIds = $processos->pluck('id')->toArray();

            $verificacao = TjspVerificacao::create([
                'status'      => 'pendente',
                'total'       => count($processoIds),
                'processado'  => 0,
                'iniciado_em' => now(),
                'filtros'     => json_encode($processoIds),
            ]);

            $this->verificacaoId = $verificacao->id;

            VerificarAndamentosTjsp::dispatch($verificacao->id, $processoIds);

        } catch (\Throwable $e) {
            $this->erroMensagem = 'Erro ao iniciar a verificação: ' . $e->getMessage();
            $this->verificacaoId = null;
        } finally {
            $this->consultando = false;
        }
    }

    private function queryProcessos()
    {
        return Processo::with(['cliente', 'fase', 'advogado'])
            ->when($this->filtroStatus, fn($q) =>
                $q->where('status', $this->filtroStatus)
            )
            ->when($this->filtroCliente, fn($q) =>
                $q->whereHas('cliente', fn($q2) =>
                    $q2->where('nome', 'ilike', "%{$this->filtroCliente}%")
                )
            )
            ->when($this->filtroFase, fn($q) =>
                $q->whereHas('fase', fn($q2) =>
                    $q2->where('descricao', 'ilike', "%{$this->filtroFase}%")
                )
            )
            ->when($this->filtroAdvogado, fn($q) =>
                $q->whereHas('advogado', fn($q2) =>
                    $q2->where('nome', 'ilike', "%{$this->filtroAdvogado}%")
                )
            )
            ->when($this->filtroConsulta === 'nunca', fn($q) =>
                $q->whereNull('tjsp_ultima_consulta')
            )
            ->when($this->filtroConsulta === 'hoje', fn($q) =>
                $q->whereDate('tjsp_ultima_consulta', today())
            )
            ->when($this->filtroConsulta === 'semana', fn($q) =>
                $q->where('tjsp_ultima_consulta', '<', now()->subWeek())
                  ->orWhereNull('tjsp_ultima_consulta')
            )
            ->when($this->filtroConsulta === 'mes', fn($q) =>
                $q->where('tjsp_ultima_consulta', '<', now()->subMonth())
                  ->orWhereNull('tjsp_ultima_consulta')
            )
            ->get();
    }

    public function limparFiltros(): void
    {
        $this->filtroCliente  = '';
        $this->filtroFase     = '';
        $this->filtroAdvogado = '';
        $this->filtroStatus   = 'Ativo';
        $this->filtroConsulta = '';
    }

    public function render()
    {
        $verificacao = $this->verificacaoId
            ? TjspVerificacao::find($this->verificacaoId)
            : TjspVerificacao::latest()->first();

        if ($verificacao) {
            $this->verificacaoId = $verificacao->id;
        }

        // Contar processos que serão consultados com os filtros atuais
        $totalFiltrado = $this->queryProcessos()->count();

        // Fases e advogados para os selects
        $fases = \App\Models\Fase::orderBy('descricao')->get();
        $advogados = Pessoa::doTipo('Advogado')->orderBy('nome')->get();

        return view('livewire.tjsp-consulta', [
            'verificacao'   => $verificacao,
            'totalFiltrado' => $totalFiltrado,
            'fases'         => $fases,
            'advogados'     => $advogados,
        ]);
    }
}
