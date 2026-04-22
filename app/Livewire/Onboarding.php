<?php

namespace App\Livewire;

use App\Models\Tenant;
use Illuminate\Support\Facades\{Auth, Cache, Storage};
use Livewire\Component;
use Livewire\WithFileUploads;

class Onboarding extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 4;

    // Step 1 — Escritório
    public string $nome      = '';
    public string $cnpj      = '';
    public string $telefone  = '';
    public string $email     = '';
    public string $endereco  = '';
    public string $cidade    = '';
    public string $oab       = '';

    // Step 2 — Logo
    public $logo = null;
    public ?string $logoAtual = null;

    // Step 3 — Áreas de atuação (configurações iniciais)
    public array $areas = [];
    public bool $temProcessosTrabalhistas = false;
    public bool $temProcessosCivis       = false;
    public bool $temProcessosFamilia     = false;
    public bool $temProcessosTributarios = false;
    public bool $temProcessosCriminais   = false;
    public bool $temProcessosEmpresariais= false;

    // Step 4 — Concluído

    public function mount(): void
    {
        $tenant = $this->tenant();

        $this->nome     = $tenant->nome     ?? '';
        $this->cnpj     = $tenant->cnpj     ?? '';
        $this->telefone = $tenant->telefone  ?? '';
        $this->email    = $tenant->email    ?? '';
        $this->endereco = $tenant->endereco ?? '';
        $this->cidade   = $tenant->cidade   ?? '';
        $this->oab      = $tenant->oab      ?? '';
        $this->logoAtual= $tenant->logo     ?? null;
    }

    private function tenant(): Tenant
    {
        return Tenant::findOrFail(auth('usuarios')->user()->tenant_id);
    }

    public function proximoStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'nome'  => 'required|min:2',
                'email' => 'required|email',
            ], [
                'nome.required'  => 'Nome do escritório é obrigatório.',
                'email.required' => 'E-mail é obrigatório.',
                'email.email'    => 'E-mail inválido.',
            ]);

            $this->tenant()->update([
                'nome'     => $this->nome,
                'cnpj'     => $this->cnpj    ?: null,
                'telefone' => $this->telefone ?: null,
                'email'    => $this->email,
                'endereco' => $this->endereco ?: null,
                'cidade'   => $this->cidade   ?: null,
                'oab'      => $this->oab      ?: null,
            ]);
        }

        if ($this->step === 2) {
            if ($this->logo) {
                $this->validate(['logo' => 'image|max:2048']);
                $caminho = $this->logo->store('logos', 'public');
                $this->tenant()->update(['logo' => $caminho]);
                $this->logoAtual = $caminho;
                $this->logo = null;
            }
        }

        if ($this->step === 3) {
            $areas = array_filter([
                $this->temProcessosCivis        ? 'Civil'        : null,
                $this->temProcessosTrabalhistas  ? 'Trabalhista'  : null,
                $this->temProcessosFamilia       ? 'Família'      : null,
                $this->temProcessosTributarios   ? 'Tributário'   : null,
                $this->temProcessosCriminais     ? 'Criminal'     : null,
                $this->temProcessosEmpresariais  ? 'Empresarial'  : null,
            ]);

            $tenant = $this->tenant();
            $config = $tenant->configuracoes ?? [];
            $config['areas_atuacao'] = array_values($areas);
            $tenant->update(['configuracoes' => $config]);
        }

        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function voltarStep(): void
    {
        if ($this->step > 1) $this->step--;
    }

    public function concluir(): void
    {
        $tenant = $this->tenant();
        $tenant->update(['onboarding_concluido' => true]);
        Cache::forget("onboarding.{$tenant->id}");
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function pularStep(): void
    {
        $this->step++;
    }

    public function render()
    {
        return view('livewire.onboarding')
            ->layout('layouts.onboarding');
    }
}
