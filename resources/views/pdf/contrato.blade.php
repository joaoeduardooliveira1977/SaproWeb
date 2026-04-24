<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.7; }
        .header { border-bottom: 3px solid #1a3a5c; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; }
        .logo-area h1 { font-size: 18px; color: #1a3a5c; font-weight: 700; }
        .logo-area p { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .doc-info { text-align: right; font-size: 10px; color: #6b7280; }
        .doc-info strong { display: block; font-size: 13px; color: #1a3a5c; font-weight: 700; }
        .meta-box { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .meta-item label { font-size: 9px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; display: block; }
        .meta-item span { font-size: 11px; color: #111; }
        .contrato-body { white-space: pre-line; font-size: 11px; line-height: 1.9; text-align: justify; margin-bottom: 32px; }
        .footer { border-top: 1px solid #e5e7eb; padding-top: 10px; text-align: center; font-size: 9px; color: #9ca3af; margin-top: 40px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: 700; background: #dbeafe; color: #1d4ed8; }
        img.logo { max-height: 48px; max-width: 160px; object-fit: contain; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-area">
            @if($tenant->logo)
                <img class="logo" src="{{ public_path('storage/' . $tenant->logo) }}" alt="Logo">
            @else
                <h1>{{ $tenant->nome }}</h1>
            @endif
            @if($tenant->oab)
                <p>OAB {{ $tenant->oab }} &nbsp;|&nbsp; {{ $tenant->email ?? '' }}</p>
            @endif
        </div>
        <div class="doc-info">
            <strong>CONTRATO DE PRESTAÇÃO DE SERVIÇOS</strong>
            Gerado em: {{ $gerado_em }}<br>
            <span class="badge">{{ $contrato->tipo_label }}</span>
        </div>
    </div>

    <div class="meta-box">
        <div class="meta-grid">
            <div class="meta-item">
                <label>Cliente</label>
                <span>{{ $contrato->cliente?->nome ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <label>CPF / CNPJ</label>
                <span>{{ $contrato->cliente?->cpf_cnpj ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <label>Advogado Responsável</label>
                <span>{{ $contrato->advogadoResponsavel?->nome ?? '—' }}</span>
            </div>
            <div class="meta-item">
                <label>OAB</label>
                <span>{{ $contrato->advogadoResponsavel?->oab ?? '—' }}</span>
            </div>
            @if($contrato->processo)
            <div class="meta-item">
                <label>Processo</label>
                <span>{{ $contrato->processo->numero }}</span>
            </div>
            @endif
            <div class="meta-item">
                <label>Valor</label>
                <span>R$ {{ number_format($contrato->valor, 2, ',', '.') }} — {{ $contrato->forma_label }}</span>
            </div>
            <div class="meta-item">
                <label>Início</label>
                <span>{{ $contrato->data_inicio->format('d/m/Y') }}</span>
            </div>
            @if($contrato->data_fim)
            <div class="meta-item">
                <label>Vigência até</label>
                <span>{{ $contrato->data_fim->format('d/m/Y') }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="contrato-body">{{ $contrato->texto_contrato ?: $contrato->descricao }}</div>

    <div class="footer">
        {{ $tenant->nome }} &nbsp;|&nbsp; Gerado pelo Software Jurídico em {{ $gerado_em }}
    </div>
</body>
</html>
