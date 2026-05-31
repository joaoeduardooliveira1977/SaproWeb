@extends('layouts.master')
@section('page-title', 'Códigos de Recuperação')

@section('content')
<div style="max-width:500px;margin:0 auto;">

    <div style="background:#fffbeb;border:2px solid #fcd34d;border-radius:14px;padding:24px;margin-bottom:24px;">
        <div style="font-size:20px;margin-bottom:10px;">⚠️ Guarde estes códigos agora!</div>
        <p style="font-size:13px;color:#92400e;line-height:1.6;">
            Estes são seus <strong>8 códigos de recuperação</strong>. Guarde-os em local seguro.
            Cada código pode ser usado <strong>uma única vez</strong> se perder acesso ao seu authenticator.
            <strong>Esta é a única vez que eles serão exibidos.</strong>
        </p>
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><span class="card-title">Códigos de Recuperação</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                @foreach($codigos as $codigo)
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px 14px;font-family:monospace;font-size:15px;font-weight:700;color:#1a3a5c;text-align:center;letter-spacing:2px;">
                    {{ $codigo }}
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="display:flex;gap:10px;">
        <button onclick="window.print()" class="btn btn-outline" style="flex:1;">
            🖨️ Imprimir
        </button>
        <a href="{{ route('master.dashboard') }}" class="btn btn-primary" style="flex:1;text-align:center;">
            ✅ Feito — Ir para o Dashboard
        </a>
    </div>

</div>
@endsection
