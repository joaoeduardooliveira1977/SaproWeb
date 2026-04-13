<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Prazo;
use App\Models\Audiencia;
use App\Models\Agenda;
use App\Models\Usuario;
use Illuminate\Support\Collection;

class SlaMonitor extends Component
{
    public string $filtroTipo  = 'todos';  // todos | prazo | audiencia | agenda
    public int    $filtroDias  = 30;       // horizon: 0=hoje | 3 | 7 | 30
    public bool   $filtroFatal = false;
    public string $filtroResp  = '';

    // ── urgência helpers ─────────────────────────────────────────

    private function urgenciaFromDias(int $dias, bool $urgente = false): string
    {
        return match (true) {
            $dias < 0        => 'vencido',
            $dias === 0      => 'urgente',
            $urgente         => 'urgente',
            $dias <= 3       => 'atencao',
            $dias <= 7       => 'alerta',
            default          => 'normal',
        };
    }

    // ── render ───────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $tenantId = auth('usuarios')->user()->tenant_id;
        $hoje     = now()->startOfDay();
        $ate      = $hoje->copy()->addDays($this->filtroDias)->endOfDay();
        $items    = collect();

        // ── Prazos ──────────────────────────────────────────────
        if (in_array($this->filtroTipo, ['todos', 'prazo'])) {
            $q = Prazo::with(['processo:id,numero,cliente_id', 'processo.cliente:id,nome', 'responsavel:id,nome'])
                ->where('status', 'aberto')
                ->where('data_prazo', '<=', $ate);

            if ($this->filtroFatal) {
                $q->where('prazo_fatal', true);
            }
            if ($this->filtroResp !== '') {
                $q->where('responsavel_id', $this->filtroResp);
            }

            foreach ($q->get() as $p) {
                $diasRestantes = (int) $hoje->diffInDays($p->data_prazo, false);
                $items->push([
                    'tipo'          => 'prazo',
                    'id'            => $p->id,
                    'titulo'        => $p->titulo,
                    'data'          => $p->data_prazo,
                    'data_fmt'      => $p->data_prazo?->format('d/m/Y'),
                    'hora_fmt'      => null,
                    'dias'          => $diasRestantes,
                    'urgencia'      => $p->urgencia(),
                    'processo_id'   => $p->processo_id,
                    'processo_num'  => $p->processo?->numero,
                    'cliente'       => $p->processo?->cliente?->nome,
                    'responsavel'   => $p->responsavel?->nome,
                    'fatal'         => $p->prazo_fatal,
                    'subtipo'       => $p->tipo,
                ]);
            }
        }

        // ── Audiências ──────────────────────────────────────────
        if (in_array($this->filtroTipo, ['todos', 'audiencia'])) {
            $q = Audiencia::with(['processo:id,numero,cliente_id', 'processo.cliente:id,nome'])
                ->where('tenant_id', $tenantId)
                ->where('status', 'agendada')
                ->whereDate('data_hora', '<=', $ate);

            if ($this->filtroFatal) {
                // audiências não têm flag fatal — pular quando filtro fatal ativo
            } else {
                if ($this->filtroResp !== '') {
                    $q->where('advogado_id', $this->filtroResp);
                }

                foreach ($q->get() as $a) {
                    $diasRestantes = (int) $hoje->diffInDays($a->data_hora->startOfDay(), false);
                    $items->push([
                        'tipo'         => 'audiencia',
                        'id'           => $a->id,
                        'titulo'       => $a->tipoLabel(),
                        'data'         => $a->data_hora,
                        'data_fmt'     => $a->data_hora->format('d/m/Y'),
                        'hora_fmt'     => $a->data_hora->format('H:i'),
                        'dias'         => $diasRestantes,
                        'urgencia'     => $this->urgenciaFromDias($diasRestantes),
                        'processo_id'  => $a->processo_id,
                        'processo_num' => $a->processo?->numero,
                        'cliente'      => $a->processo?->cliente?->nome,
                        'responsavel'  => null,
                        'fatal'        => false,
                        'subtipo'      => $a->local ?? $a->sala ?? '',
                    ]);
                }
            }
        }

        // ── Agenda ──────────────────────────────────────────────
        if (in_array($this->filtroTipo, ['todos', 'agenda'])) {
            $q = Agenda::with(['processo:id,numero,cliente_id', 'processo.cliente:id,nome', 'responsavel:id,nome'])
                ->where('concluido', false)
                ->whereDate('data_hora', '<=', $ate);

            if ($this->filtroFatal) {
                $q->where('urgente', true);
            }
            if ($this->filtroResp !== '') {
                $q->where('responsavel_id', $this->filtroResp);
            }

            foreach ($q->get() as $ag) {
                $diasRestantes = (int) $hoje->diffInDays($ag->data_hora->startOfDay(), false);
                $items->push([
                    'tipo'         => 'agenda',
                    'id'           => $ag->id,
                    'titulo'       => $ag->titulo,
                    'data'         => $ag->data_hora,
                    'data_fmt'     => $ag->data_hora->format('d/m/Y'),
                    'hora_fmt'     => $ag->data_hora->format('H:i'),
                    'dias'         => $diasRestantes,
                    'urgencia'     => $this->urgenciaFromDias($diasRestantes, $ag->urgente),
                    'processo_id'  => $ag->processo_id,
                    'processo_num' => $ag->processo?->numero,
                    'cliente'      => $ag->processo?->cliente?->nome,
                    'responsavel'  => $ag->responsavel?->nome,
                    'fatal'        => $ag->urgente,
                    'subtipo'      => $ag->tipo ?? '',
                ]);
            }
        }

        // ── Ordenar: urgência → data ─────────────────────────────
        $urgOrder = ['vencido' => 0, 'urgente' => 1, 'atencao' => 2, 'alerta' => 3, 'normal' => 4];
        $items = $items->sortBy(function ($item) use ($urgOrder) {
            return [
                $urgOrder[$item['urgencia']] ?? 9,
                $item['data']?->timestamp ?? 0,
            ];
        })->values();

        // ── Contadores ───────────────────────────────────────────
        $counts = [
            'vencido' => $items->where('urgencia', 'vencido')->count(),
            'urgente' => $items->where('urgencia', 'urgente')->count(),
            'atencao' => $items->where('urgencia', 'atencao')->count(),
            'alerta'  => $items->where('urgencia', 'alerta')->count(),
            'normal'  => $items->where('urgencia', 'normal')->count(),
            'total'   => $items->count(),
        ];

        $responsaveis = Usuario::where('tenant_id', $tenantId)
            ->orderBy('nome')
            ->get(['id', 'nome', 'login']);

        return view('livewire.sla-monitor', compact('items', 'counts', 'responsaveis'));
    }
}
