@extends('layouts.master')
@section('page-title', 'Configurar 2FA')

@section('content')
<div style="max-width:600px;margin:0 auto;">

    <div style="margin-bottom:20px;">
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:4px;">Autenticação em 2 Fatores</h2>
        <p style="font-size:13px;color:#64748b;">Configure o 2FA para aumentar a segurança do acesso master.</p>
    </div>

    @if(session('sucesso'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;margin-bottom:16px;color:#166534;font-size:13px;">
        ✅ {{ session('sucesso') }}
    </div>
    @endif

    @if($errors->any())
    <div style="background:#fff5f5;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;margin-bottom:16px;color:#dc2626;font-size:13px;">
        {{ $errors->first() }}
    </div>
    @endif

    @if($user->master_2fa_ativo)
    {{-- 2FA já ativado --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <span class="card-title">🛡️ 2FA Ativo</span>
            <span class="badge badge-green">Configurado</span>
        </div>
        <div class="card-body">
            <p style="font-size:13px;color:#374151;margin-bottom:16px;">
                O 2FA está ativo desde <strong>{{ $user->master_2fa_confirmado_em?->format('d/m/Y H:i') }}</strong>.
                Você usa o Google Authenticator para confirmar seu login.
            </p>
            <div style="background:#fff5f5;border:1px solid #fecaca;border-radius:8px;padding:14px 16px;">
                <div style="font-size:12px;font-weight:700;color:#dc2626;margin-bottom:10px;">Desativar 2FA</div>
                <p style="font-size:12px;color:#b91c1c;margin-bottom:12px;">Para desativar, confirme com um código atual do Authenticator.</p>
                <form method="POST" action="{{ route('master.2fa.desativar') }}">
                    @csrf
                    <div style="display:flex;gap:10px;align-items:flex-end;">
                        <div style="flex:1;">
                            <input type="text" name="codigo" maxlength="6" inputmode="numeric"
                                   placeholder="Código de 6 dígitos"
                                   style="width:100%;padding:9px 12px;border:1.5px solid #fca5a5;border-radius:7px;font-size:14px;text-align:center;letter-spacing:4px;">
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Desativar o 2FA reduz a segurança do master. Confirma?')">
                            Desativar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @else
    {{-- Setup do 2FA --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Configurar Google Authenticator</span></div>
        <div class="card-body">

            <div style="display:grid;grid-template-columns:auto 1fr;gap:24px;align-items:start;margin-bottom:24px;">

                {{-- QR Code --}}
                <div style="text-align:center;">
                    @if($qrCodeSvg)
                        <div style="background:#fff;padding:12px;border-radius:10px;border:1px solid #e2e8f0;display:inline-block;">
                            {!! $qrCodeSvg !!}
                        </div>
                    @else
                        <div style="background:#f8fafc;border-radius:10px;padding:20px;border:1px solid #e2e8f0;font-size:12px;color:#94a3b8;text-align:center;">
                            QR Code não disponível
                        </div>
                    @endif
                    <div style="font-size:11px;color:#94a3b8;margin-top:8px;">Escaneie com o Authenticator</div>
                </div>

                {{-- Instruções --}}
                <div>
                    <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:10px;">Passos:</div>
                    <ol style="font-size:13px;color:#374151;line-height:2;padding-left:18px;">
                        <li>Instale o <strong>Google Authenticator</strong> no celular</li>
                        <li>Toque em <strong>+</strong> → "Escanear QR Code"</li>
                        <li>Aponte a câmera para o QR Code ao lado</li>
                        <li>Digite o código gerado abaixo para confirmar</li>
                    </ol>

                    <div style="margin-top:14px;padding:10px 14px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                        <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Ou insira a chave manual:</div>
                        <code style="font-size:13px;color:#1a3a5c;font-weight:700;letter-spacing:2px;">{{ $secret }}</code>
                    </div>
                </div>
            </div>

            <div style="border-top:1px solid #f1f5f9;padding-top:20px;">
                <form method="POST" action="{{ route('master.2fa.confirmar') }}">
                    @csrf
                    <div style="display:flex;gap:12px;align-items:flex-end;">
                        <div style="flex:1;">
                            <label style="font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">
                                Código de verificação
                            </label>
                            <input type="text" name="codigo" maxlength="6" inputmode="numeric" autofocus
                                   placeholder="000000"
                                   style="width:100%;padding:12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:20px;font-weight:700;text-align:center;letter-spacing:6px;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding:12px 24px;">
                            Confirmar e Ativar 2FA
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    @endif

</div>
@endsection
