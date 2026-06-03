<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:40px;height:40px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;">🏢</div>
            <div>
                <h1 style="font-size:18px;font-weight:800;color:#0f2540;margin:0;">Filiais</h1>
                <p style="font-size:12px;color:#64748b;margin:0;">Gerencie as filiais vinculadas aos clientes</p>
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar filial..."
                style="padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;width:200px;">
            <button wire:click="abrir()" style="padding:8px 16px;background:#1a3a5c;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                + Nova Filial
            </button>
        </div>
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:11px 16px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Nome</th>
                    <th style="padding:11px 16px;text-align:center;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="padding:11px 16px;text-align:right;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filiais as $f)
                <tr style="border-bottom:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:10px 16px;font-weight:600;color:#0f2540;">{{ $f->nome }}</td>
                    <td style="padding:10px 16px;text-align:center;">
                        @if($f->ativo)
                            <span style="background:#dcfce7;color:#16a34a;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;">Ativa</span>
                        @else
                            <span style="background:#f1f5f9;color:#64748b;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;">Inativa</span>
                        @endif
                    </td>
                    <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                        <button wire:click="abrir({{ $f->id }})" style="background:#f1f5f9;color:#374151;border:none;border-radius:6px;padding:5px 10px;font-size:12px;cursor:pointer;margin-right:4px;">✏️ Editar</button>
                        @if($confirmarExcluirId === $f->id)
                            <span style="font-size:12px;color:#374151;">Excluir? </span>
                            <button wire:click="excluir({{ $f->id }})" style="background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;">Sim</button>
                            <button wire:click="$set('confirmarExcluirId', null)" style="background:#f1f5f9;color:#64748b;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;margin-left:2px;">Não</button>
                        @else
                            <button wire:click="$set('confirmarExcluirId', {{ $f->id }})" style="background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:5px 10px;font-size:12px;cursor:pointer;">🗑️</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding:36px;text-align:center;color:#94a3b8;font-size:13px;">Nenhuma filial cadastrada.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($modalAberto)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:16px;width:380px;box-shadow:0 24px 60px rgba(0,0,0,.3);">
            <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                <h2 style="font-size:16px;font-weight:800;color:#0f2540;margin:0;">{{ $filialId ? 'Editar Filial' : 'Nova Filial' }}</h2>
                <button wire:click="fechar" style="background:none;border:none;font-size:20px;cursor:pointer;color:#64748b;">✕</button>
            </div>
            <div style="padding:20px 24px;display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Nome *</label>
                    <input wire:model="nome" type="text" placeholder="Nome da filial..."
                        style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
                    @error('nome') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
                </div>
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" wire:model="ativo"> Filial ativa
                </label>
            </div>
            <div style="padding:14px 24px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:8px;">
                <button wire:click="fechar" style="padding:9px 18px;background:#f1f5f9;color:#374151;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Cancelar</button>
                <button wire:click="salvar" style="padding:9px 20px;background:#1a3a5c;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">Salvar</button>
            </div>
        </div>
    </div>
    @endif
</div>
