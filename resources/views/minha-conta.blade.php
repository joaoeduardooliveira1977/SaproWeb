@extends('layouts.app')

@section('content')
<div style="max-width:500px;">
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:var(--primary);display:flex;align-items:center;gap:8px;"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Minha Conta</h2>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Altere sua senha de acesso</p>
    </div>

    <div style="background:white; border-radius:12px; padding:32px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">

        @if(session('sucesso'))
        <div style="background:#dcfce7; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:14px; color:#16a34a;">
            <span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> {{ session('sucesso') }}</span>
        </div>
        @endif

        @if(session('erro'))
        <div style="background:#fee2e2; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:14px; color:#dc2626;">
            <span style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> {{ session('erro') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('minha-conta.salvar') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Login</label>
                <input type="text" value="{{ auth('usuarios')->user()->login }}" disabled
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; background:#f8fafc; color:#64748b;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Senha atual</label>
                <input type="password" name="senha_atual" required
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Nova senha</label>
                <input type="password" name="nova_senha" required
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Confirmar nova senha</label>
                <input type="password" name="confirma_senha" required
                    style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none;">
            </div>
            <button type="submit"
                style="width:100%; padding:12px; background:#2563a8; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                Salvar nova senha
            </button>
        </form>
    </div>
</div>
@endsection
