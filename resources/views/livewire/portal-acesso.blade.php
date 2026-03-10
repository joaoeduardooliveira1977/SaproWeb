<div>
    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
            <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">🌐 Portal do Cliente</h2>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">Gerencie o acesso dos clientes ao portal</p>
        </div>
        <a href="{{ route('portal.login') }}" target="_blank" style="font-size:13px; color:#2563a8; text-decoration:none;">
            🔗 Abrir portal →
        </a>
    </div>

    {{-- Mensagem --}}
    @if($mensagem)
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:14px; color:#16a34a;">
        ✅ {{ $mensagem }}
    </div>
    @endif

    {{-- Busca --}}
    <div style="margin-bottom:20px;">
        <input wire:model.live="busca" type="text" placeholder="🔍 Buscar cliente..."
            style="width:100%; max-width:400px; padding:10px 16px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none;">
    </div>

    {{-- Tabela --}}
    <div style="background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.08); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Cliente</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">CPF/CNPJ</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">E-mail</th>
                    <th style="text-align:center; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Portal</th>
                    <th style="text-align:center; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Último acesso</th>
                    <th style="text-align:center; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pessoas as $pessoa)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 16px; font-weight:500;">{{ $pessoa->nome }}</td>
                    <td style="padding:12px 16px; color:#64748b; font-size:13px;">{{ $pessoa->cpf_cnpj ?? '—' }}</td>
                    <td style="padding:12px 16px; color:#64748b; font-size:13px;">{{ $pessoa->email ?? '—' }}</td>
                    <td style="padding:12px 16px; text-align:center;">
                        @if($pessoa->portal_ativo)
                            <span style="background:#dcfce7; color:#16a34a; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">Ativo</span>
                        @else
                            <span style="background:#f1f5f9; color:#64748b; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">Inativo</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px; text-align:center; font-size:13px; color:#64748b;">
                        {{ $pessoa->portal_ultimo_acesso?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                    <td style="padding:12px 16px; text-align:center;">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <button wire:click="abrirDefinirSenha({{ $pessoa->id }})"
                                style="padding:5px 12px; background:#2563a8; color:white; border:none; border-radius:6px; font-size:12px; cursor:pointer;">
                                🔑 Senha
                            </button>
                            @if($pessoa->portal_ativo)
                            <button wire:click="desativar({{ $pessoa->id }})"
                                style="padding:5px 12px; background:#fee2e2; color:#dc2626; border:none; border-radius:6px; font-size:12px; cursor:pointer;">
                                Desativar
                            </button>
                            @else
                            <button wire:click="ativar({{ $pessoa->id }})"
                                style="padding:5px 12px; background:#dcfce7; color:#16a34a; border:none; border-radius:6px; font-size:12px; cursor:pointer;">
                                Ativar
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8; font-size:14px;">
                        Nenhum cliente encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px;">{{ $pessoas->links() }}</div>
    </div>

    {{-- Modal definir senha --}}
    @if($pessoaId)
    <div style="position:fixed; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:50;">
        <div style="background:white; border-radius:16px; padding:32px; width:100%; max-width:400px;">
            <h3 style="font-size:16px; font-weight:600; color:#1a3a5c; margin-bottom:20px;">🔑 Definir Senha do Portal</h3>

            @if($erro)
            <div style="background:#fee2e2; border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:13px; color:#dc2626;">{{ $erro }}</div>
            @endif

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Nova senha</label>
                <input wire:model="novaSenha" type="password" placeholder="Mínimo 6 caracteres"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Confirmar senha</label>
                <input wire:model="confirmaSenha" type="password" placeholder="Repita a senha"
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>

            <div style="display:flex; gap:12px;">
                <button wire:click="definirSenha"
                    style="flex:1; padding:10px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                    Salvar
                </button>
                <button wire:click="$set('pessoaId', null)"
                    style="flex:1; padding:10px; background:#f1f5f9; color:#334155; border:none; border-radius:8px; font-size:14px; cursor:pointer;">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
