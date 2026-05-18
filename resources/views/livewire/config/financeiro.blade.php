<div>
<div style="max-width:700px;margin:0 auto;padding:24px 0;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h2 style="margin:0;font-size:20px;font-weight:800;color:var(--text);">Configurações Financeiras</h2>
            <p style="margin:4px 0 0;font-size:12px;color:var(--muted);">PIX e dados do escritório</p>
        </div>
    </div>

    <form wire:submit.prevent="salvar">

    {{-- ── Dados do Escritório ── --}}
    <div style="background:var(--white);border-radius:12px;border:1px solid var(--border);padding:20px;margin-bottom:20px;">
        <div style="font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
            Dados do Escritório
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Nome do Escritório</label>
                <input wire:model="escritorio_nome" type="text" placeholder="Ex: Advocacia Silva & Associados"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;box-sizing:border-box;">
            </div>
            <div class="form-group">
                <label class="form-label">CNPJ</label>
                <input wire:model="escritorio_cnpj" type="text" placeholder="00.000.000/0001-00"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;box-sizing:border-box;">
            </div>
            <div class="form-group">
                <label class="form-label">Telefone</label>
                <input wire:model="escritorio_telefone" type="text" placeholder="(11) 3333-4444"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;box-sizing:border-box;">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">E-mail do Escritório</label>
                <input wire:model="escritorio_email" type="email" placeholder="contato@escritorio.com.br"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;box-sizing:border-box;">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Endereço</label>
                <input wire:model="escritorio_endereco" type="text"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;box-sizing:border-box;">
            </div>
            <div class="form-group">
                <label class="form-label">Cidade</label>
                <input wire:model="escritorio_cidade" type="text"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;box-sizing:border-box;">
            </div>
            <div class="form-group">
                <label class="form-label">UF</label>
                <input wire:model="escritorio_uf" type="text" maxlength="2" placeholder="SP"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;box-sizing:border-box;text-transform:uppercase;">
            </div>
        </div>
    </div>

    {{-- ── Configuração PIX ── --}}
    <div style="background:var(--white);border-radius:12px;border:1px solid var(--border);padding:20px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
            <div style="font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.5px;">
                Chave PIX para Cobrança
            </div>
            <span style="font-size:10px;background:#faf5ff;color:#7c3aed;border:1px solid #e9d5ff;border-radius:10px;padding:2px 8px;font-weight:700;">QR Code Estático</span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div class="form-group">
                <label class="form-label">Tipo de Chave</label>
                <select wire:model="pix_tipo"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;">
                    <option value="telefone">Telefone</option>
                    <option value="cpf">CPF</option>
                    <option value="cnpj">CNPJ</option>
                    <option value="email">E-mail</option>
                    <option value="aleatoria">Chave Aleatória</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Chave PIX *</label>
                <input wire:model="pix_chave" type="text"
                    placeholder="{{ match($pix_tipo) { 'telefone' => '+5511999999999', 'cpf' => '000.000.000-00', 'cnpj' => '00.000.000/0001-00', 'email' => 'financeiro@escritorio.com', default => 'chave aleatória' } }}"
                    style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('pix_chave') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;box-sizing:border-box;">
                @error('pix_chave')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">
                    Nome do Beneficiário
                    <span style="color:var(--muted);font-weight:400;">(máx. 25 car.)</span>
                </label>
                <input wire:model="pix_beneficiario" type="text" maxlength="25"
                    placeholder="Como aparece no comprovante"
                    style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('pix_beneficiario') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;box-sizing:border-box;">
                @error('pix_beneficiario')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">
                    Cidade
                    <span style="color:var(--muted);font-weight:400;">(máx. 15 car.)</span>
                </label>
                <input wire:model="pix_cidade" type="text" maxlength="15"
                    placeholder="Sao Paulo"
                    style="width:100%;padding:8px 12px;border:1.5px solid {{ $errors->has('pix_cidade') ? 'var(--danger)' : 'var(--border)' }};border-radius:7px;font-size:13px;box-sizing:border-box;">
                @error('pix_cidade')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        @if($pix_chave)
        <div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:8px;padding:12px;margin-top:12px;font-size:12px;color:#6d28d9;">
            <strong>Chave configurada:</strong> {{ $pix_chave }} ({{ $pix_tipo }})
            — O QR Code será gerado automaticamente em cada lançamento.
        </div>
        @else
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px;margin-top:12px;font-size:12px;color:#92400e;">
            Sem chave PIX cadastrada. Os botões de QR Code não aparecerão nos lançamentos.
        </div>
        @endif
    </div>

    {{-- Botão salvar --}}
    <div style="display:flex;justify-content:flex-end;">
        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="salvar">Salvar Configurações</span>
            <span wire:loading wire:target="salvar">Salvando…</span>
        </button>
    </div>

    </form>
</div>
</div>
