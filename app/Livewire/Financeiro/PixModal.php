<?php

namespace App\Livewire\Financeiro;

use App\Models\Configuracao;
use App\Models\FinanceiroLancamento;
use App\Services\PixService;
use Livewire\Component;

class PixModal extends Component
{
    public int    $lancamentoId = 0;
    public string $payload      = '';
    public string $qrBase64     = '';
    public string $valorFmt     = '';
    public string $beneficiario  = '';
    public string $erro          = '';

    public function mount(int $lancamentoId, int $tenantId): void
    {
        $lancamento = FinanceiroLancamento::where('tenant_id', $tenantId)
            ->findOrFail($lancamentoId);

        $config = Configuracao::doTenant($tenantId);

        if (empty($config->pix_chave)) {
            $this->erro = 'PIX não configurado. Acesse Configurações → Financeiro para cadastrar a chave PIX.';
            return;
        }

        $this->lancamentoId = $lancamentoId;
        $this->valorFmt     = 'R$ ' . number_format($lancamento->valor, 2, ',', '.');
        $this->beneficiario = $config->pix_beneficiario ?? $config->escritorio_nome ?? '';

        $this->payload = PixService::gerar(
            chave:     $config->pix_chave,
            nome:      $this->beneficiario,
            cidade:    $config->pix_cidade ?? $config->escritorio_cidade ?? 'Brasil',
            valor:     (float) $lancamento->valor,
            descricao: 'Honorarios',
            txid:      'LAN' . $lancamentoId,
        );

        $this->qrBase64 = PixService::qrCodeBase64($this->payload);
    }

    public function render()
    {
        return view('livewire.financeiro.pix-modal');
    }
}
