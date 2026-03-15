<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class BuscaGlobal extends Component
{
    public string $query   = '';
    public bool   $aberto  = false;

    public function updatedQuery(): void
    {
        $this->aberto = strlen(trim($this->query)) >= 2;
    }

    public function fechar(): void
    {
        $this->aberto = false;
        $this->query  = '';
    }

    public function render()
    {
        $resultados = [];

        $q = trim($this->query);

        if (strlen($q) >= 2) {
            $like = '%' . $q . '%';

            // ── Processos ──────────────────────────────────────
            $processos = DB::select(
                "SELECT p.id, p.numero, p.status,
                        pe.nome AS cliente_nome,
                        ta.descricao AS tipo_acao
                 FROM   processos p
                 LEFT   JOIN pessoas pe ON pe.id = p.cliente_id
                 LEFT   JOIN tipos_acao ta ON ta.id = p.tipo_acao_id
                 WHERE  (p.numero ILIKE ?
                    OR   p.parte_contraria ILIKE ?
                    OR   pe.nome ILIKE ?)
                 ORDER  BY p.updated_at DESC
                 LIMIT  5",
                [$like, $like, $like]
            );

            foreach ($processos as $r) {
                $resultados[] = [
                    'tipo'      => 'Processo',
                    'icone'     => '⚖️',
                    'titulo'    => $r->numero,
                    'subtitulo' => trim(implode(' · ', array_filter([$r->cliente_nome, $r->tipo_acao]))),
                    'url'       => route('processos.show', $r->id),
                    'badge'     => $r->status,
                    'badge_cor' => $r->status === 'Ativo' ? '#16a34a' : '#64748b',
                ];
            }

            // ── Pessoas ────────────────────────────────────────
            $pessoas = DB::select(
                "SELECT p.id, p.nome, p.cpf_cnpj, p.email
                 FROM   pessoas p
                 WHERE  (p.nome ILIKE ? OR p.cpf_cnpj ILIKE ? OR p.email ILIKE ?)
                   AND  p.ativo = true
                 ORDER  BY p.nome
                 LIMIT  4",
                [$like, $like, $like]
            );

            foreach ($pessoas as $r) {
                $resultados[] = [
                    'tipo'      => 'Pessoa',
                    'icone'     => '👤',
                    'titulo'    => $r->nome,
                    'subtitulo' => trim(implode(' · ', array_filter([$r->cpf_cnpj, $r->email]))),
                    'url'       => route('pessoas') . '?busca=' . urlencode($r->nome),
                    'badge'     => null,
                    'badge_cor' => '',
                ];
            }

            // ── Andamentos ─────────────────────────────────────
            $andamentos = DB::select(
                "SELECT a.id, a.descricao, a.data,
                        p.id AS processo_id, p.numero AS processo_numero
                 FROM   andamentos a
                 JOIN   processos p ON p.id = a.processo_id
                 WHERE  a.descricao ILIKE ?
                 ORDER  BY a.data DESC
                 LIMIT  4",
                [$like]
            );

            foreach ($andamentos as $r) {
                $resultados[] = [
                    'tipo'      => 'Andamento',
                    'icone'     => '📝',
                    'titulo'    => mb_strimwidth($r->descricao, 0, 70, '…'),
                    'subtitulo' => 'Processo ' . $r->processo_numero . ' · ' . \Carbon\Carbon::parse($r->data)->format('d/m/Y'),
                    'url'       => route('processos.show', $r->processo_id) . '#tab-andamentos',
                    'badge'     => null,
                    'badge_cor' => '',
                ];
            }

            // ── Prazos ─────────────────────────────────────────
            $prazos = DB::select(
                "SELECT pz.id, pz.titulo, pz.data_prazo, pz.status,
                        p.id AS processo_id, p.numero AS processo_numero
                 FROM   prazos pz
                 JOIN   processos p ON p.id = pz.processo_id
                 WHERE  pz.titulo ILIKE ?
                 ORDER  BY pz.data_prazo ASC
                 LIMIT  4",
                [$like]
            );

            foreach ($prazos as $r) {
                $resultados[] = [
                    'tipo'      => 'Prazo',
                    'icone'     => '⏳',
                    'titulo'    => mb_strimwidth($r->titulo, 0, 70, '…'),
                    'subtitulo' => 'Processo ' . $r->processo_numero . ' · ' . \Carbon\Carbon::parse($r->data_prazo)->format('d/m/Y'),
                    'url'       => route('processos.show', $r->processo_id) . '#tab-prazos',
                    'badge'     => $r->status,
                    'badge_cor' => $r->status === 'Pendente' ? '#d97706' : '#64748b',
                ];
            }
        }

        return view('livewire.busca-global', compact('resultados'));
    }
}
