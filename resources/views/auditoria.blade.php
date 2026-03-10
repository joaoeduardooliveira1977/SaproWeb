@extends('layouts.app')

@section('content')
<div>
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">🔍 Auditoria</h2>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Histórico de ações no sistema</p>
    </div>

    <div style="background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.08); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Data/Hora</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Usuário</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Ação</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Tabela</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse(DB::table('auditorias')->orderByDesc('created_at')->paginate(20) as $a)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 16px; font-size:13px; color:#64748b;">{{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y H:i') }}</td>
                    <td style="padding:12px 16px; font-size:14px; font-weight:500;">{{ $a->login }}</td>
                    <td style="padding:12px 16px;">
                        <span style="background:#dbeafe; color:#2563a8; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">{{ $a->acao }}</span>
                    </td>
                    <td style="padding:12px 16px; font-size:13px; color:#64748b;">{{ $a->tabela ?? '—' }}</td>
                    <td style="padding:12px 16px; font-size:13px; color:#64748b;">{{ $a->ip ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:#94a3b8; font-size:14px;">
                        🔍 Nenhum registro de auditoria ainda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection