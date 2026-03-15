<?php

namespace App\Livewire;

use App\Services\WhatsAppSmsService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class NotificacoesWhatsapp extends Component
{
    use WithPagination;

    // ── Filtros ──────────────────────────────────────────────
    public string $filtroStatus = '';
    public string $filtroTipo   = '';
    public string $filtroCanal  = '';

    // ── Teste manual ──────────────────────────────────────────
    public bool   $modalTeste   = false;
    public string $testeTel     = '';
    public string $testeMsg     = '';
    public string $testeCanal   = 'whatsapp';
    public string $testeSucesso = '';
    public string $testeErro    = '';
    public bool   $testeEnviando = false;

    public function updatedFiltroStatus(): void { $this->resetPage(); }
    public function updatedFiltroTipo(): void   { $this->resetPage(); }
    public function updatedFiltroCanal(): void  { $this->resetPage(); }

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

        return view('livewire.notificacoes-whatsapp', compact('svc', 'stats', 'logs', 'total'));
    }
}
