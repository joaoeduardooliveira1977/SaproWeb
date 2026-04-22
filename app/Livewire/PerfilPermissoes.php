<?php

namespace App\Livewire;

use App\Http\Middleware\VerificarPerfil;
use Illuminate\Support\Facades\{Cache, DB};
use Livewire\Component;

class PerfilPermissoes extends Component
{
    public static array $MODULOS = [
        'processos'   => 'Processos',
        'pessoas'     => 'Pessoas / Clientes',
        'documentos'  => 'Documentos & Minutas',
        'financeiro'  => 'Financeiro',
        'honorarios'  => 'Honorários',
        'relatorios'  => 'Relatórios & Horas',
        'ferramentas' => 'Ferramentas (IA, CRM...)',
        'usuarios'    => 'Usuários',
    ];

    public static array $PERFIS = [
        'advogado'     => 'Advogado',
        'estagiario'   => 'Estagiário',
        'financeiro'   => 'Financeiro',
        'recepcionista'=> 'Recepcionista',
    ];

    // matriz[perfil][modulo] = bool
    public array $matriz = [];

    public function mount(): void
    {
        $this->carregarMatriz();
    }

    private function carregarMatriz(): void
    {
        $tenantId = tenant_id();

        $overrides = DB::table('perfil_permissoes')
            ->where('tenant_id', $tenantId)
            ->get()
            ->keyBy(fn($r) => "{$r->perfil}.{$r->modulo}");

        foreach (self::$PERFIS as $perfil => $_) {
            $padrao = VerificarPerfil::PERMISSOES[$perfil] ?? [];
            foreach (self::$MODULOS as $modulo => $_) {
                $key = "{$perfil}.{$modulo}";
                if ($overrides->has($key)) {
                    $this->matriz[$perfil][$modulo] = (bool) $overrides[$key]->permitido;
                } else {
                    $this->matriz[$perfil][$modulo] = in_array($modulo, $padrao);
                }
            }
        }
    }

    public function toggle(string $perfil, string $modulo): void
    {
        if (! isset(self::$PERFIS[$perfil]) || ! isset(self::$MODULOS[$modulo])) return;

        $tenantId  = tenant_id();
        $atual     = $this->matriz[$perfil][$modulo] ?? false;
        $novo      = ! $atual;

        DB::table('perfil_permissoes')->updateOrInsert(
            ['tenant_id' => $tenantId, 'perfil' => $perfil, 'modulo' => $modulo],
            ['permitido' => $novo, 'updated_at' => now(), 'created_at' => now()]
        );

        $this->matriz[$perfil][$modulo] = $novo;

        // Limpa cache para este perfil+módulo
        Cache::forget("perfil_perm.{$tenantId}.{$perfil}.{$modulo}");

        $this->dispatch('toast', tipo: 'success', msg: 'Permissão atualizada.');
    }

    public function restaurarPadrao(string $perfil): void
    {
        if (! isset(self::$PERFIS[$perfil])) return;

        $tenantId = tenant_id();
        DB::table('perfil_permissoes')
            ->where('tenant_id', $tenantId)
            ->where('perfil', $perfil)
            ->delete();

        foreach (self::$MODULOS as $modulo => $_) {
            Cache::forget("perfil_perm.{$tenantId}.{$perfil}.{$modulo}");
        }

        $this->carregarMatriz();
        $this->dispatch('toast', tipo: 'success', msg: 'Perfil restaurado ao padrão.');
    }

    public function render()
    {
        return view('livewire.perfil-permissoes');
    }
}
