<div>
    {{-- ══ CABEÇALHO ══ --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:28px;">
        <div style="width:44px;height:44px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;">🔒</div>
        <div>
            <h1 style="font-size:22px;font-weight:800;color:#0f2540;margin:0;">Privacidade & LGPD</h1>
            <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Direitos dos titulares de dados conforme a Lei Geral de Proteção de Dados.</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

        {{-- ══ CARD: EXPORTAR DADOS ══ --}}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <span style="font-size:20px;">📤</span>
                <h2 style="font-size:16px;font-weight:700;color:#0f2540;margin:0;">Exportar dados de um cliente</h2>
            </div>
            <p style="font-size:13px;color:#64748b;margin:0 0 20px;">Gere um arquivo com todos os dados pessoais, processos, documentos e histórico financeiro do cliente.</p>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Buscar cliente</label>
                <input type="text" wire:model.live.debounce.300ms="buscaExportar"
                    placeholder="Nome, CPF/CNPJ ou e-mail..."
                    style="width:100%;padding:9px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Cliente</label>
                <select wire:model="exportarClienteId"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                    <option value="">— Selecione —</option>
                    @foreach($clientesExportar as $c)
                        <option value="{{ $c->id }}">{{ $c->nome }}{{ $c->cpf_cnpj ? ' — '.$c->cpf_cnpj : '' }}</option>
                    @endforeach
                </select>
                @error('exportarClienteId') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;">Formato</label>
                <div style="display:flex;gap:12px;">
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                        <input type="radio" wire:model="exportarFormato" value="json"> JSON
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                        <input type="radio" wire:model="exportarFormato" value="pdf"> PDF (em breve)
                    </label>
                </div>
            </div>

            <button wire:click="exportarDados" wire:loading.attr="disabled"
                style="width:100%;padding:10px;background:#1a3a5c;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
                <span wire:loading.remove wire:target="exportarDados">📥 Exportar dados</span>
                <span wire:loading wire:target="exportarDados">Gerando...</span>
            </button>
        </div>

        {{-- ══ CARD: ANONIMIZAR / EXCLUIR ══ --}}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <span style="font-size:20px;">🗑️</span>
                <h2 style="font-size:16px;font-weight:700;color:#0f2540;margin:0;">Anonimizar ou excluir dados</h2>
            </div>
            <p style="font-size:13px;color:#64748b;margin:0 0 20px;">Cumpra o direito ao esquecimento. A anonimização preserva o histórico sem identificar o titular.</p>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Buscar cliente</label>
                <input type="text" wire:model.live.debounce.300ms="buscaAcao"
                    placeholder="Nome, CPF/CNPJ ou e-mail..."
                    style="width:100%;padding:9px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Cliente</label>
                <select wire:model="acaoClienteId"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                    <option value="">— Selecione —</option>
                    @foreach($clientesAcao as $c)
                        <option value="{{ $c->id }}">{{ $c->nome }}{{ $c->cpf_cnpj ? ' — '.$c->cpf_cnpj : '' }}</option>
                    @endforeach
                </select>
                @error('acaoClienteId') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;">Ação</label>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:12px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;{{ $acaoTipo === 'anonimizar' ? 'border-color:#3b82f6;background:#eff6ff;' : '' }}">
                        <input type="radio" wire:model="acaoTipo" value="anonimizar" style="margin-top:2px;">
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#0f2540;">Anonimizar</div>
                            <div style="font-size:12px;color:#64748b;">Substitui dados pessoais por valores neutros. Histórico de processos e financeiro é mantido sem identificação.</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:12px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;{{ $acaoTipo === 'excluir' ? 'border-color:#ef4444;background:#fef2f2;' : '' }}">
                        <input type="radio" wire:model="acaoTipo" value="excluir" style="margin-top:2px;">
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#dc2626;">Excluir permanentemente</div>
                            <div style="font-size:12px;color:#64748b;">Remove todos os dados e registros do cliente. Ação irreversível.</div>
                        </div>
                    </label>
                </div>
            </div>

            <button wire:click="abrirConfirmacao"
                style="width:100%;padding:10px;background:{{ $acaoTipo === 'excluir' ? '#dc2626' : '#0f2540' }};color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
                {{ $acaoTipo === 'excluir' ? '⚠️ Excluir dados permanentemente' : '🔒 Anonimizar dados' }}
            </button>
        </div>
    </div>

    {{-- ══ AVISO LGPD ══ --}}
    <div style="margin-top:24px;background:#fffbeb;border:1px solid #fcd34d;border-radius:12px;padding:16px 20px;display:flex;gap:12px;align-items:flex-start;">
        <span style="font-size:20px;flex-shrink:0;">⚖️</span>
        <div style="font-size:13px;color:#78350f;line-height:1.6;">
            <strong>Lei Geral de Proteção de Dados — Art. 18 (Lei 13.709/2018):</strong>
            O titular tem direito à confirmação de tratamento, acesso, correção, anonimização, bloqueio ou eliminação de dados desnecessários, portabilidade e revogação do consentimento.
            Todas as ações realizadas aqui são registradas no log de auditoria do sistema.
            <a href="{{ route('privacidade') }}" target="_blank" style="color:#92400e;font-weight:600;">Ver Política de Privacidade</a>
        </div>
    </div>

    {{-- ══ MODAL DE CONFIRMAÇÃO ══ --}}
    @if($modalConfirmar)
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;display:flex;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:16px;padding:32px;max-width:440px;width:90%;box-shadow:0 24px 60px rgba(0,0,0,.3);">
            <div style="text-align:center;margin-bottom:20px;">
                <div style="font-size:40px;margin-bottom:8px;">{{ $acaoTipo === 'excluir' ? '⚠️' : '🔒' }}</div>
                <h3 style="font-size:18px;font-weight:800;color:#0f2540;margin:0 0 6px;">
                    {{ $acaoTipo === 'excluir' ? 'Excluir dados permanentemente?' : 'Anonimizar dados do cliente?' }}
                </h3>
                <p style="font-size:13px;color:#64748b;margin:0;">
                    @if($acaoTipo === 'excluir')
                        Esta ação é <strong>irreversível</strong>. Todos os dados, documentos e histórico financeiro serão removidos.
                    @else
                        Os dados pessoais identificáveis serão substituídos. O histórico de processos e financeiro será mantido de forma anônima.
                    @endif
                </p>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">
                    Digite <strong>{{ $acaoTipo === 'excluir' ? 'EXCLUIR' : 'CONFIRMAR' }}</strong> para prosseguir:
                </label>
                <input type="text" wire:model="confirmacaoTexto"
                    placeholder="{{ $acaoTipo === 'excluir' ? 'EXCLUIR' : 'CONFIRMAR' }}"
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:monospace;outline:none;box-sizing:border-box;">
                @error('confirmacaoTexto') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex;gap:10px;">
                <button wire:click="$set('modalConfirmar', false)"
                    style="flex:1;padding:10px;background:#f1f5f9;color:#374151;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                    Cancelar
                </button>
                <button wire:click="executarAcao" wire:loading.attr="disabled"
                    style="flex:1;padding:10px;background:{{ $acaoTipo === 'excluir' ? '#dc2626' : '#1a3a5c' }};color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
                    <span wire:loading.remove wire:target="executarAcao">Confirmar</span>
                    <span wire:loading wire:target="executarAcao">Processando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Script: download JSON via JS --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('download-lgpd', ({ dados, nome, formato }) => {
                if (formato === 'json') {
                    const blob = new Blob([JSON.stringify(dados, null, 2)], { type: 'application/json' });
                    const url  = URL.createObjectURL(blob);
                    const a    = document.createElement('a');
                    a.href     = url;
                    a.download = nome + '.json';
                    a.click();
                    URL.revokeObjectURL(url);
                }
            });
        });
    </script>
</div>
