<?php

namespace App\Livewire;

use App\Mail\PortalNovaMensagem;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class PortalMensagens extends Component
{
    public ?int   $pessoaId    = null;
    public string $resposta    = '';
    public string $filtro      = 'nao_lidas'; // nao_lidas | todas

    public function selecionarCliente(int $id): void
    {
        $this->pessoaId = $id;
        $this->resposta = '';

        // Marca como lidas pelo escritório
        DB::table('portal_mensagens')
            ->where('pessoa_id', $id)
            ->where('de', 'cliente')
            ->where('lida_escritorio', false)
            ->update(['lida_escritorio' => true]);
    }

    public function responder(): void
    {
        $this->resposta = trim($this->resposta);
        if (! $this->resposta || ! $this->pessoaId) return;

        DB::table('portal_mensagens')->insert([
            'pessoa_id'       => $this->pessoaId,
            'processo_id'     => null,
            'usuario_id'      => Auth::guard('usuarios')->id(),
            'mensagem'        => $this->resposta,
            'de'              => 'escritorio',
            'lida_escritorio' => true,
            'lida_cliente'    => false,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Envia e-mail ao cliente se tiver e-mail cadastrado
        $cliente = Pessoa::find($this->pessoaId);
        if ($cliente?->email) {
            $usuario = Auth::guard('usuarios')->user();
            $remetente = $usuario?->nome ?? $usuario?->login ?? 'Escritório';
            try {
                Mail::to($cliente->email)
                    ->queue(new PortalNovaMensagem($cliente, $this->resposta, $remetente));
            } catch (\Throwable) {
                // Falha no e-mail não deve bloquear o envio da mensagem
            }
        }

        $this->resposta = '';
    }

    public function render(): \Illuminate\View\View
    {
        // Lista de clientes com mensagens
        $clientes = DB::table('portal_mensagens as m')
            ->join('pessoas as p', 'p.id', '=', 'm.pessoa_id')
            ->select(
                'p.id', 'p.nome',
                DB::raw('MAX(m.created_at) as ultima_msg'),
                DB::raw("SUM(CASE WHEN m.de = 'cliente' AND m.lida_escritorio = false THEN 1 ELSE 0 END) as nao_lidas")
            )
            ->groupBy('p.id', 'p.nome')
            ->when($this->filtro === 'nao_lidas', function ($q) {
                $q->havingRaw("SUM(CASE WHEN m.de = 'cliente' AND m.lida_escritorio = false THEN 1 ELSE 0 END) > 0");
            })
            ->orderByDesc('ultima_msg')
            ->get();

        // Conversa do cliente selecionado
        $conversa = collect();
        $clienteSelecionado = null;

        if ($this->pessoaId) {
            $clienteSelecionado = Pessoa::find($this->pessoaId);

            $conversa = DB::table('portal_mensagens as m')
                ->leftJoin('usuarios as u', 'u.id', '=', 'm.usuario_id')
                ->leftJoin('processos as pr', 'pr.id', '=', 'm.processo_id')
                ->where('m.pessoa_id', $this->pessoaId)
                ->select('m.*', 'u.nome as usuario_nome', 'pr.numero as processo_numero')
                ->orderBy('m.created_at')
                ->get();
        }

        $totalNaoLidas = DB::table('portal_mensagens')
            ->where('de', 'cliente')
            ->where('lida_escritorio', false)
            ->count();

        return view('livewire.portal-mensagens', compact(
            'clientes', 'conversa', 'clienteSelecionado', 'totalNaoLidas'
        ));
    }
}
