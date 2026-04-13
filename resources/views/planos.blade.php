@extends('layouts.app')
@section('page-title', 'Escolha seu Plano')
@section('content')
@php $tenant = tenant(); @endphp

<div style="max-width:900px;margin:0 auto;padding:40px 20px;">
    <div style="text-align:center;margin-bottom:40px;">
        <h1 style="font-size:28px;font-weight:800;color:var(--primary);">Escolha o melhor plano</h1>
        <p style="font-size:14px;color:var(--muted);margin-top:8px;">Sem taxa de instalação. Cancele quando quiser.</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
        @php
        $planos = [
            [
                'nome'     => 'Demo',
                'preco'    => 'Gratuito',
                'periodo'  => '30 dias',
                'cor'      => '#64748b',
                'bg'       => '#f8fafc',
                'atual'    => $tenant?->plano === 'demo',
                'recursos' => ['5 processos', '2 usuários', 'Sem IA', 'Sem DATAJUD', 'Suporte por e-mail'],
            ],
            [
                'nome'     => 'Starter',
                'preco'    => 'R$ 149',
                'periodo'  => '/mês',
                'cor'      => '#2563a8',
                'bg'       => '#eff6ff',
                'atual'    => $tenant?->plano === 'starter',
                'destaque' => true,
                'recursos' => ['50 processos', '5 usuários', 'IA incluída', 'DATAJUD incluído', 'Suporte prioritário'],
            ],
            [
                'nome'     => 'Pro',
                'preco'    => 'R$ 299',
                'periodo'  => '/mês',
                'cor'      => '#7c3aed',
                'bg'       => '#f5f3ff',
                'atual'    => $tenant?->plano === 'pro',
                'recursos' => ['Ilimitado', 'Usuários ilimitados', 'IA incluída', 'DATAJUD + WhatsApp', 'Suporte VIP'],
            ],
        ];
        @endphp

        @foreach($planos as $p)
        <div style="background:{{ $p['bg'] }};border:2px solid {{ isset($p['destaque']) ? $p['cor'] : '#e2e8f0' }};border-radius:16px;padding:28px;position:relative;">
            @if(isset($p['destaque']))
            <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:{{ $p['cor'] }};color:#fff;font-size:11px;font-weight:700;padding:4px 12px;border-radius:99px;">
                MAIS POPULAR
            </div>
            @endif
            @if($p['atual'])
            <div style="position:absolute;top:12px;right:12px;background:#16a34a;color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:99px;">
                ATUAL
            </div>
            @endif

            <div style="font-size:18px;font-weight:800;color:{{ $p['cor'] }};margin-bottom:8px;">{{ $p['nome'] }}</div>
            <div style="font-size:32px;font-weight:800;color:var(--text);">{{ $p['preco'] }}<span style="font-size:14px;font-weight:400;color:var(--muted);">{{ $p['periodo'] }}</span></div>

            <div style="margin:20px 0;border-top:1px solid #e2e8f0;padding-top:20px;display:flex;flex-direction:column;gap:8px;">
                @foreach($p['recursos'] as $r)
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="{{ $p['cor'] }}" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $r }}
                </div>
                @endforeach
            </div>

            @if(!$p['atual'])
            <a href="mailto:comercial@softwarejuridico.com.br?subject=Upgrade para {{ $p['nome'] }}"
                style="display:block;text-align:center;padding:12px;background:{{ $p['cor'] }};color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;">
                Escolher {{ $p['nome'] }}
            </a>
            @else
            <div style="text-align:center;padding:12px;background:#e2e8f0;color:#64748b;border-radius:10px;font-size:13px;font-weight:600;">
                Plano atual
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div style="text-align:center;margin-top:32px;font-size:13px;color:var(--muted);">
        Dúvidas? Entre em contato: <a href="mailto:comercial@softwarejuridico.com.br" style="color:var(--primary);">comercial@softwarejuridico.com.br</a>
    </div>
</div>
@endsection
