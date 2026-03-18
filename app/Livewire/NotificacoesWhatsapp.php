<?php

namespace App\Livewire;

use App\Models\{NotificacaoConfig, WhatsappTemplate};
use App\Services\WhatsAppSmsService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class NotificacoesWhatsapp extends Component
{
    use WithPagination;

    // ── Abas ─────────────────────────────────────────────────
    public string $aba = 'log'; // log | templates | config

    // ── Filtros ──────────────────────────────────────────────
    public string $filtroStatus = '';
    public string $filtroTipo   = '';
    public string $filtroCanal  = '';

    protected $queryString = [
        'aba'          => ['except' => 'log'],
        'filtroStatus' => ['except' => ''],
        'filtroTipo'   => ['except' => ''],
        'filtroCanal'  => ['except' => ''],
    ];

    // ── Teste manual ──────────────────────────────────────────
    public bool   $modalTeste   = false;
    public string $testeTel     = '';
    public string $testeMsg     = '';
    public string $testeCanal   = 'whatsapp';
    public string $testeSucesso = '';
    public string $testeErro    = '';
    public bool   $testeEnviando = false;

    // ── Templates ─────────────────────────────────────────────
    public bool   $modalTemplate   = false;
    public ?int   $templateId      = null;
    public string $tplNome         = '';
    public string $tplMensagem     = '';
    public string $tplCanal        = 'whatsapp';

    public function updatedFiltroStatus(): void { $this->resetPage(); }
    public function updatedFiltroTipo(): void   { $this->resetPage(); }
    public function updatedFiltroCanal(): void  { $this->resetPage(); }
    public function updatedAba(): void          { $this->resetPage(); }

    // ── Templates ─────────────────────────────────────────────

    public function abrirTemplate(?int $id = null): void
    {
        $this->templateId  = $id;
        $this->modalTemplate = true;

        if ($id) {
            $t = WhatsappTemplate::findOrFail($id);
            $this->tplNome     = $t->nome;
            $this->tplMensagem = $t->mensagem;
            $this->tplCanal    = $t->canal;
        } else {
            $this->tplNome = $this->tplMensagem = '';
            $this->tplCanal = 'whatsapp';
        }
    }

    public function fecharTemplate(): void
    {
        $this->modalTemplate = false;
        $this->templateId    = null;
        $this->resetErrorBag();
    }

    public function salvarTemplate(): void
    {
        $this->validate([
            'tplNome'     => 'required|string|max:100',
            'tplMensagem' => 'required|string',
            'tplCanal'    => 'required|in:whatsapp,sms,ambos',
        ], [], [
            'tplNome'     => 'nome',
            'tplMensagem' => 'mensagem',
            'tplCanal'    => 'canal',
        ]);

        WhatsappTemplate::updateOrCreate(
            ['id' => $this->templateId],
            ['nome' => $this->tplNome, 'mensagem' => $this->tplMensagem, 'canal' => $this->tplCanal, 'ativo' => true]
        );

        $this->fecharTemplate();
        $this->dispatch('toast', message: 'Template salvo!', type: 'success');
    }

    public function excluirTemplate(int $id): void
    {
        WhatsappTemplate::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Template removido.', type: 'success');
    }

    // ── Configurações ──────────────────────────────────────────

    public function toggleAtivo(string $tipo): void
    {
        $cfg = NotificacaoConfig::para($tipo);
        $cfg->update(['ativo' => ! $cfg->ativo]);
        $this->dispatch('toast', tipo: 'success', msg: 'Configuração salva.');
    }

    public function salvarConfig(string $tipo, string $canal, array $antecedencias): void
    {
        $cfg = NotificacaoConfig::para($tipo);
        $cfg->update([
            'canal'         => $canal,
            'antecedencias' => array_map('intval', array_filter($antecedencias)),
        ]);
        $this->dispatch('toast', tipo: 'success', msg: 'Configuração salva.');
    }

    public function usarTemplate(int $id): void
    {
        $t = WhatsappTemplate::findOrFail($id);
        $this->testeMsg   = $t->mensagem;
        $this->testeCanal = $t->canal === 'ambos' ? 'whatsapp' : $t->canal;
        $this->testeSucesso = '';
        $this->testeErro    = '';
        $this->modalTeste   = true;
    }

    public function abrirTeste(): void
    {
        $this->testeTel = '';
        $this->testeMsg = '✅ *SAPRO Jurídico — Teste*\nEsta é uma mensagem de teste do sistema. Tudo funcionando!';
        $this->testeCanal  = 'whatsapp';
        $this->testeSucesso = '';
        $this->testeErro    = '';
        $this->modalTeste   = true;
    }

    public function enviarTeste(): void
    {
        $this->validate([
            'testeTel' => 'required|min:10',
            'testeMsg' => 'required|min:5',
        ], [], [
            'testeTel' => 'telefone',
            'testeMsg' => 'mensagem',
        ]);

        $this->testeEnviando = true;
        $this->testeSucesso  = '';
        $this->testeErro     = '';

        try {
            $svc = new WhatsAppSmsService();

            if (!$svc->configurado()) {
                $this->testeErro = 'Twilio não configurado. Defina TWILIO_ACCOUNT_SID e TWILIO_AUTH_TOKEN no .env.';
                return;
            }

            $ok = $svc->enviar(
                telefone:         $this->testeTel,
                mensagem:         $this->testeMsg,
                destinatarioNome: 'Teste Manual',
                tipo:             'teste',
                canal:            $this->testeCanal,
            );

            if ($ok) {
                $this->testeSucesso = 'Mensagem enviada com sucesso!';
            } else {
                $this->testeErro = 'Falha ao enviar. Verifique o log abaixo.';
            }
        } finally {
            $this->testeEnviando = false;
        }
    }

    public function render()
    {
        $svc = new WhatsAppSmsService();

        // Estatísticas
        $stats = DB::selectOne("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'enviado' THEN 1 ELSE 0 END) as enviados,
                SUM(CASE WHEN status = 'falha'   THEN 1 ELSE 0 END) as falhas,
                SUM(CASE WHEN canal = 'whatsapp' AND status = 'enviado' THEN 1 ELSE 0 END) as whatsapp,
                SUM(CASE WHEN canal = 'sms'      AND status = 'enviado' THEN 1 ELSE 0 END) as sms,
                SUM(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 ELSE 0 END) as hoje
            FROM notificacoes_whatsapp
        ");

        $query = DB::table('notificacoes_whatsapp')
            ->when($this->filtroStatus, fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroTipo,   fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroCanal,  fn($q) => $q->where('canal', $this->filtroCanal))
            ->orderByDesc('created_at');

        $total = $query->count();
        $logs  = $query->paginate(25);

        $templates = WhatsappTemplate::orderBy('nome')->get();
        $configs   = NotificacaoConfig::orderBy('id')->get();

        // Estatísticas por tipo (últimas 24h)
        $statsPorTipo = DB::table('notificacoes_whatsapp')
            ->selectRaw("tipo, COUNT(*) as total, SUM(CASE WHEN status='enviado' THEN 1 ELSE 0 END) as enviados")
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('tipo')
            ->pluck('enviados', 'tipo');

        return view('livewire.notificacoes-whatsapp', compact(
            'svc', 'stats', 'logs', 'total', 'templates', 'configs', 'statsPorTipo'
        ));
    }
}
