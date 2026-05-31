@section('page-title', 'Lixeira de Tenants')
<div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:4px;">Lixeira</h2>
            <p style="font-size:13px;color:#64748b;">Tenants excluídos. Podem ser restaurados ou excluídos permanentemente.</p>
        </div>
        <a href="{{ route('master.tenants') }}" class="btn btn-outline">← Voltar aos Tenants</a>
    </div>

    @if($tenants->isEmpty())
    <div class="card">
        <div class="card-body" style="text-align:center;padding:48px;color:#94a3b8;">
            <div style="font-size:40px;margin-bottom:12px;">🗑️</div>
            <div style="font-size:15px;font-weight:600;margin-bottom:6px;">Lixeira vazia</div>
            <div style="font-size:13px;">Nenhum tenant foi movido para a lixeira.</div>
        </div>
    </div>
    @else

    {{-- Aviso tenants há mais de 30 dias --}}
    @php $antigos = $tenants->filter(fn($t) => $t->deleted_at && \Carbon\Carbon::parse($t->deleted_at)->diffInDays() >= 30); @endphp
    @if($antigos->isNotEmpty())
    <div style="background:#fff5f5;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
        <span style="font-size:18px;">⚠️</span>
        <div>
            <strong style="font-size:13px;color:#b91c1c;">{{ $antigos->count() }} tenant(s) na lixeira há mais de 30 dias.</strong>
            <span style="font-size:12px;color:#dc2626;"> Considere excluí-los definitivamente ou restaurá-los.</span>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $tenants->count() }} tenant(s) na lixeira</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Plano</th>
                        <th>Data exclusão</th>
                        <th>Excluído por</th>
                        <th>Motivo</th>
                        <th>Na lixeira há</th>
                        <th>Dados</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tenants as $t)
                @php
                    $diasLixeira = $t->deleted_at ? \Carbon\Carbon::parse($t->deleted_at)->diffInDays() : 0;
                    $corDias = $diasLixeira >= 30 ? '#dc2626' : ($diasLixeira >= 15 ? '#d97706' : '#64748b');
                @endphp
                <tr wire:key="{{ $t->id }}">
                    <td>
                        <div style="font-weight:600;color:#0f172a;">{{ $t->nome }}</div>
                        <div style="font-size:11px;color:#94a3b8;font-family:monospace;">{{ $t->slug }}</div>
                    </td>
                    <td>
                        @php $planoCor = ['demo'=>'badge-gray','starter'=>'badge-blue','pro'=>'badge-purple','enterprise'=>'badge-yellow'][$t->plano] ?? 'badge-gray'; @endphp
                        <span class="badge {{ $planoCor }}">{{ ucfirst($t->plano) }}</span>
                    </td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $t->deleted_at ? \Carbon\Carbon::parse($t->deleted_at)->format('d/m/Y H:i') : '—' }}
                    </td>
                    <td style="font-size:12px;">
                        {{ $t->deleted_by ? ($excluindoPorUsuario[$t->deleted_by] ?? 'ID:'.$t->deleted_by) : '—' }}
                    </td>
                    <td style="font-size:12px;color:#64748b;max-width:200px;">
                        <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $t->delete_reason }}">
                            {{ $t->delete_reason ?? '—' }}
                        </div>
                    </td>
                    <td>
                        <span style="font-size:12px;font-weight:700;color:{{ $corDias }};">
                            {{ $diasLixeira }} dia(s)
                        </span>
                    </td>
                    <td style="font-size:12px;color:#64748b;">
                        {{ $t->usuarios_count }} usuário(s)<br>
                        {{ $t->processos_count }} processo(s)
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                            <button wire:click="restaurar({{ $t->id }})"
                                    wire:confirm="Restaurar o tenant {{ $t->nome }}? Ele voltará a funcionar normalmente."
                                    class="btn btn-sm btn-success">
                                ♻️ Restaurar
                            </button>
                            <button wire:click="abrirModalDefinitivo({{ $t->id }})"
                                    class="btn btn-sm btn-danger">
                                🔴 Excluir def.
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Modal: Exclusão Definitiva ── --}}
    @if($modalDefinitivo)
    <div class="modal-overlay" wire:click.self="fecharModalDefinitivo">
        <div class="modal" style="max-width:520px;">
            <div class="modal-header" style="background:#fff5f5;border-bottom-color:#fecaca;">
                <span class="modal-title" style="color:#dc2626;">🔴 Exclusão Permanente</span>
                <button class="modal-close" wire:click="fecharModalDefinitivo">✕</button>
            </div>
            <div class="modal-body">

                <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                    <div style="font-weight:800;color:#7f1d1d;font-size:13px;margin-bottom:4px;">⚠️ ATENÇÃO: AÇÃO IRREVERSÍVEL</div>
                    <div style="font-size:12px;color:#b91c1c;">Esta ação excluirá permanentemente o tenant <strong>{{ $excluindoNome }}</strong> e todos os seus dados. Não é possível desfazer.</div>
                </div>

                <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:10px;">Será excluído permanentemente:</div>
                <div style="background:#f8fafc;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                    @foreach($contagensExclusao as $tipo => $total)
                    <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0;border-bottom:1px solid #f1f5f9;">
                        <span style="color:#374151;">{{ ucfirst($tipo) }}</span>
                        <span style="font-weight:700;color:#dc2626;">{{ number_format($total) }} registro(s)</span>
                    </div>
                    @endforeach
                    <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0;">
                        <span style="color:#374151;">Arquivos no storage</span>
                        <span style="font-weight:700;color:#dc2626;">Todos</span>
                    </div>
                </div>

                <div class="fg">
                    <label>Digite o nome do tenant para confirmar: <strong>{{ $excluindoNome }}</strong></label>
                    <input wire:model="confirmacaoNome"
                           type="text"
                           placeholder="{{ $excluindoNome }}"
                           style="border-color:#fca5a5;">
                    @error('confirmacaoNome') <span class="err">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="modal-footer" style="border-top-color:#fecaca;">
                <button wire:click="fecharModalDefinitivo" class="btn btn-outline">Cancelar</button>
                <button wire:click="excluirDefinitivamente"
                        wire:loading.attr="disabled"
                        class="btn btn-danger"
                        @if(trim($confirmacaoNome) !== $excluindoNome) disabled style="opacity:.4;cursor:not-allowed;" @endif>
                    <span wire:loading.remove wire:target="excluirDefinitivamente">Excluir Permanentemente</span>
                    <span wire:loading wire:target="excluirDefinitivamente">Excluindo…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    // Habilita o botão de exclusão definitiva somente quando o nome estiver correto
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('update', () => {
            const input = document.querySelector('[wire\\:model="confirmacaoNome"]');
            const btn   = document.querySelector('[wire\\:click="excluirDefinitivamente"]');
            if (!input || !btn) return;

            const nomeEsperado = "{{ $excluindoNome }}";
            const atualizar = () => {
                if (input.value === nomeEsperado) {
                    btn.removeAttribute('disabled');
                    btn.style.opacity = '';
                    btn.style.cursor = '';
                } else {
                    btn.setAttribute('disabled', true);
                    btn.style.opacity = '.4';
                    btn.style.cursor = 'not-allowed';
                }
            };

            input.addEventListener('input', atualizar);
            atualizar();
        });
    });
</script>
@endpush
