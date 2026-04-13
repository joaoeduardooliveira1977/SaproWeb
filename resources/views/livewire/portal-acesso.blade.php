<div>
    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:18px;">
        <div>
            <h2 style="font-size:22px; font-weight:800; color:var(--primary);margin:0;display:flex;align-items:center;gap:8px;"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg> Portal do Cliente</h2>
            <p style="font-size:13px; color:#64748b; margin:4px 0 0;line-height:1.5;">Libere acesso para o cliente acompanhar processos, documentos e mensagens com o escritório.</p>
        </div>
        <a href="{{ route('portal.login') }}" target="_blank" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;">
            <span style="display:inline-flex;align-items:center;gap:4px;"><svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg> Abrir portal</span>
        </a>
    </div>

    <div class="portal-guide" style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:16px;margin-bottom:16px;display:grid;grid-template-columns:minmax(260px,1fr) repeat(3,minmax(150px,1fr));gap:12px;align-items:center;">
        <div style="display:flex;gap:12px;align-items:flex-start;">
            <div style="width:38px;height:38px;border-radius:8px;background:#eff6ff;color:#2563a8;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M12 4h9"/><path d="M4 9h16"/><path d="M4 15h16"/><path d="M4 4h.01"/><path d="M4 20h.01"/></svg></div>
            <div><div style="font-size:15px;font-weight:800;color:var(--text);margin-bottom:3px;">Como usar esta tela</div><div style="font-size:12px;color:var(--muted);line-height:1.5;">Ative o portal, defina uma senha e envie o link ao cliente. Ele poderá acompanhar processos e conversar pelo portal.</div></div>
        </div>
        <div style="border-left:3px solid #2563a8;padding-left:10px;"><strong style="display:block;font-size:12px;color:var(--text);margin-bottom:3px;">1. Localize o cliente</strong><span style="font-size:12px;color:var(--muted);line-height:1.4;">Use a busca por nome ou documento.</span></div>
        <div style="border-left:3px solid #059669;padding-left:10px;"><strong style="display:block;font-size:12px;color:var(--text);margin-bottom:3px;">2. Defina a senha</strong><span style="font-size:12px;color:var(--muted);line-height:1.4;">Ao salvar, o acesso já fica ativo.</span></div>
        <div style="border-left:3px solid #d97706;padding-left:10px;"><strong style="display:block;font-size:12px;color:var(--text);margin-bottom:3px;">3. Acompanhe mensagens</strong><span style="font-size:12px;color:var(--muted);line-height:1.4;">Responda pela tela Mensagens do portal.</span></div>
    </div>

    <div class="portal-kpis" style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;">
        <div style="background:white;border:1.5px solid var(--border);border-radius:10px;padding:15px;">
            <div style="font-size:22px;font-weight:800;color:#2563a8;">{{ $totalClientes }}</div>
            <div style="font-size:12px;color:var(--muted);margin-top:4px;">Clientes cadastrados</div>
        </div>
        <div style="background:white;border:1.5px solid var(--border);border-radius:10px;padding:15px;">
            <div style="font-size:22px;font-weight:800;color:#059669;">{{ $portalAtivos }}</div>
            <div style="font-size:12px;color:var(--muted);margin-top:4px;">Com portal ativo</div>
        </div>
        <div style="background:white;border:1.5px solid var(--border);border-radius:10px;padding:15px;">
            <div style="font-size:22px;font-weight:800;color:#d97706;">{{ $portalInativos }}</div>
            <div style="font-size:12px;color:var(--muted);margin-top:4px;">Aguardando liberação</div>
        </div>
    </div>

    {{-- Busca --}}
    <div style="background:white;border:1.5px solid var(--border);border-radius:10px;padding:14px;margin-bottom:16px;">
        <label style="display:block;font-size:12px;font-weight:800;color:var(--text);margin-bottom:7px;">Buscar cliente</label>
        <input wire:model.live="busca" type="text" placeholder="Digite nome, CPF/CNPJ ou e-mail..."
            style="width:100%; max-width:520px; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
    </div>

    {{-- Tabela --}}
    <div style="background:white; border:1.5px solid var(--border); border-radius:10px; overflow:hidden;">
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
                                <span style="display:inline-flex;align-items:center;gap:4px;"><svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Senha</span>
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
                    <td colspan="6" style="text-align:center; padding:42px; color:#64748b; font-size:14px;">
                        <div style="font-weight:800;color:var(--text);margin-bottom:4px;">Nenhum cliente encontrado</div>
                        <div style="font-size:12px;color:var(--muted);">Revise a busca ou cadastre o cliente antes de liberar o portal.</div>
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
            <h3 style="font-size:16px; font-weight:600; color:var(--primary); margin-bottom:20px;display:flex;align-items:center;gap:7px;"><svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Definir Senha do Portal</h3>

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

    <style>
        @media (max-width: 1100px) {
            .portal-guide { grid-template-columns:1fr 1fr !important; }
        }
        @media (max-width: 760px) {
            .portal-guide,
            .portal-kpis { grid-template-columns:1fr !important; }
        }
    </style>
</div>
