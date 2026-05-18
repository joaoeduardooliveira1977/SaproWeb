<?php

namespace App\Livewire\Config;

use App\Models\Configuracao;
use Livewire\Component;

class Financeiro extends Component
{
    public ?int $tenantId = null;

    // PIX
    public string $pix_chave        = '';
    public string $pix_tipo         = 'telefone';
    public string $pix_beneficiario = '';
    public string $pix_cidade       = '';

    // Escritório
    public string $escritorio_nome     = '';
    public string $escritorio_cnpj     = '';
    public string $escritorio_telefone = '';
    public string $escritorio_email    = '';
    public string $escritorio_endereco = '';
    public string $escritorio_cidade   = '';
    public string $escritorio_uf       = '';

    public function mount(): void
    {
        $this->tenantId = tenant_id();

        if (!$this->tenantId) {
            return;
        }

        $config = Configuracao::doTenant($this->tenantId);

        $this->pix_chave        = $config->pix_chave        ?? '';
        $this->pix_tipo         = $config->pix_tipo         ?? 'telefone';
        $this->pix_beneficiario = $config->pix_beneficiario ?? '';
        $this->pix_cidade       = $config->pix_cidade       ?? '';
        $this->escritorio_nome     = $config->escritorio_nome     ?? '';
        $this->escritorio_cnpj     = $config->escritorio_cnpj     ?? '';
        $this->escritorio_telefone = $config->escritorio_telefone ?? '';
        $this->escritorio_email    = $config->escritorio_email    ?? '';
        $this->escritorio_endereco = $config->escritorio_endereco ?? '';
        $this->escritorio_cidade   = $config->escritorio_cidade   ?? '';
        $this->escritorio_uf       = $config->escritorio_uf       ?? '';
    }

    public function salvar(): void
    {
        $this->validate([
            'pix_chave'        => 'nullable|max:77',
            'pix_tipo'         => 'required|in:cpf,cnpj,email,telefone,aleatoria',
            'pix_beneficiario' => 'nullable|max:25',
            'pix_cidade'       => 'nullable|max:15',
        ]);

        if (!$this->tenantId) {
            return;
        }

        Configuracao::updateOrCreate(
            ['tenant_id' => $this->tenantId],
            [
                'pix_chave'           => $this->pix_chave        ?: null,
                'pix_tipo'            => $this->pix_tipo,
                'pix_beneficiario'    => $this->pix_beneficiario ?: null,
                'pix_cidade'          => $this->pix_cidade        ?: null,
                'escritorio_nome'     => $this->escritorio_nome     ?: null,
                'escritorio_cnpj'     => $this->escritorio_cnpj     ?: null,
                'escritorio_telefone' => $this->escritorio_telefone ?: null,
                'escritorio_email'    => $this->escritorio_email    ?: null,
                'escritorio_endereco' => $this->escritorio_endereco ?: null,
                'escritorio_cidade'   => $this->escritorio_cidade   ?: null,
                'escritorio_uf'       => $this->escritorio_uf       ?: null,
            ]
        );

        $this->dispatch('toast', tipo: 'success', msg: 'Configurações salvas.');
    }

    public function render()
    {
        return view('livewire.config.financeiro')
            ->extends('layouts.app')
            ->section('content');
    }
}
