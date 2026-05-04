<?php

namespace App\Livewire\Configuracoes;

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EscritorioConfig extends Component
{
    use WithFileUploads;

    public string $abaAtiva = 'identidade';

    // Aba 1 — Identidade
    public string  $nome      = '';
    public string  $email     = '';
    public string  $telefone  = '';
    public string  $cnpj      = '';
    public string  $oab       = '';
    public string  $cidade    = '';
    public string  $endereco  = '';
    public ?string $logoAtual = null;
    public $logoUpload        = null;

    // Aba 2 — Preferências
    public string $timezone            = 'America/Sao_Paulo';
    public bool   $whatsapp_habilitado = false;

    // Aba 3 — Integrações
    public string $gemini_api_key = '';

    public array $timezones = [
        'America/Sao_Paulo'   => 'Brasília (GMT-3)',
        'America/Manaus'      => 'Manaus (GMT-4)',
        'America/Belem'       => 'Belém (GMT-3)',
        'America/Fortaleza'   => 'Fortaleza (GMT-3)',
        'America/Recife'      => 'Recife (GMT-3)',
        'America/Cuiaba'      => 'Cuiabá (GMT-4)',
        'America/Porto_Velho' => 'Porto Velho (GMT-4)',
        'America/Boa_Vista'   => 'Boa Vista (GMT-4)',
        'America/Rio_Branco'  => 'Rio Branco (GMT-5)',
        'America/Noronha'     => 'Fernando de Noronha (GMT-2)',
    ];

    public function mount(): void
    {
        $tenant = $this->getTenant();

        $this->nome                = $tenant->nome         ?? '';
        $this->email               = $tenant->email        ?? '';
        $this->telefone            = $tenant->telefone     ?? '';
        $this->cnpj                = $tenant->cnpj         ?? '';
        $this->oab                 = $tenant->oab          ?? '';
        $this->cidade              = $tenant->cidade       ?? '';
        $this->endereco            = $tenant->endereco     ?? '';
        $this->logoAtual           = $tenant->logo;
        $this->timezone            = $tenant->timezone            ?? 'America/Sao_Paulo';
        $this->whatsapp_habilitado = $tenant->whatsapp_habilitado ?? false;
        $this->gemini_api_key      = $tenant->gemini_api_key      ?? '';
    }

    public function mudarAba(string $aba): void
    {
        $this->abaAtiva = $aba;
    }

    public function salvarIdentidade(): void
    {
        $this->validate([
            'nome'       => 'required|string|min:3|max:150',
            'email'      => 'required|email|max:150',
            'telefone'   => 'nullable|string|max:20',
            'cnpj'       => 'nullable|string|max:18',
            'oab'        => 'nullable|string|max:30',
            'cidade'     => 'nullable|string|max:100',
            'endereco'   => 'nullable|string|max:255',
            'logoUpload' => 'nullable|image|max:2048',
        ], [
            'nome.required'    => 'O nome do escritório é obrigatório.',
            'nome.min'         => 'O nome deve ter pelo menos 3 caracteres.',
            'email.required'   => 'O e-mail é obrigatório.',
            'email.email'      => 'Informe um e-mail válido.',
            'logoUpload.image' => 'O arquivo deve ser uma imagem.',
            'logoUpload.max'   => 'O logo não pode passar de 2 MB.',
        ]);

        $dados = [
            'nome'     => $this->nome,
            'email'    => $this->email,
            'telefone' => $this->telefone ?: null,
            'cnpj'     => $this->cnpj     ?: null,
            'oab'      => $this->oab      ?: null,
            'cidade'   => $this->cidade   ?: null,
            'endereco' => $this->endereco ?: null,
        ];

        if ($this->logoUpload) {
            if ($this->logoAtual) {
                Storage::disk('public')->delete($this->logoAtual);
            }
            $path = $this->logoUpload->store('logos', 'public');
            $dados['logo']    = $path;
            $this->logoAtual  = $path;
            $this->logoUpload = null;
        }

        $this->getTenant()->update($dados);
        session()->flash('sucesso_identidade', 'Dados do escritório atualizados!');
    }

    public function removerLogo(): void
    {
        $tenant = $this->getTenant();
        if ($tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
            $tenant->update(['logo' => null]);
            $this->logoAtual = null;
        }
        session()->flash('sucesso_identidade', 'Logo removido.');
    }

    public function salvarPreferencias(): void
    {
        $this->validate([
            'timezone' => 'required|string',
        ], [
            'timezone.required' => 'Selecione um fuso horário.',
        ]);

        $this->getTenant()->update([
            'timezone'            => $this->timezone,
            'whatsapp_habilitado' => $this->whatsapp_habilitado,
        ]);

        session()->flash('sucesso_preferencias', 'Preferências atualizadas!');
    }

    public function salvarIntegracoes(): void
    {
        $this->validate([
            'gemini_api_key' => 'nullable|string|max:200',
        ]);

        $this->getTenant()->update([
            'gemini_api_key' => $this->gemini_api_key ?: null,
        ]);

        session()->flash('sucesso_integracoes', 'Integrações atualizadas!');
    }



private function getTenant(): Tenant
{
    // Tenta via tenant_id do usuário
    $tenantId = auth('usuarios')->user()->tenant_id;

    // Se não tem (superadmin), tenta via app('tenant')
    if (!$tenantId) {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        if (!$tenant) {
            abort(403, 'Nenhum tenant selecionado. Acesse como um tenant específico.');
        }
        return $tenant;
    }

    return Tenant::findOrFail($tenantId);
}





    public function render(): \Illuminate\View\View
    {
        return view('livewire.configuracoes.escritorio-config', [
            'tenant' => $this->getTenant(),
        ])->extends('layouts.app')->section('content');
    }
}
