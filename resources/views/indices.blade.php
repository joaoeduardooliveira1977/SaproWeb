@extends('layouts.app')

@section('content')
<div>
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">💰 Índices Monetários</h2>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">IPCA, IGP-M, TR e outros índices</p>
    </div>

    <div style="background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.08); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Índice</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Sigla</th>
                    <th style="text-align:center; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Mês Ref.</th>
                    <th style="text-align:center; padding:12px 16px; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Percentual</th>
                </tr>
            </thead>
            <tbody>
                @foreach(DB::table('indices_monetarios')->orderBy('sigla')->orderBy('mes_ref', 'desc')->get() as $i)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 16px; font-size:14px;">{{ $i->nome }}</td>
                    <td style="padding:12px 16px;">
                        <span style="background:#dbeafe; color:#2563a8; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">{{ $i->sigla }}</span>
                    </td>
                    <td style="padding:12px 16px; text-align:center; font-size:14px; color:#64748b;">
                        {{ \Carbon\Carbon::parse($i->mes_ref)->format('m/Y') }}
                    </td>
                    <td style="padding:12px 16px; text-align:center; font-weight:600; color:#16a34a;">
                        {{ number_format($i->percentual, 4, ',', '.') }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection