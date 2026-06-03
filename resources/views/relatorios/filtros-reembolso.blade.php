@extends('layouts.app')
@section('page-title', 'Pedido de Reembolso')
@section('content')
<div style="max-width:520px;margin:0 auto;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <div style="width:44px;height:44px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;">📄</div>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#0f2540;margin:0;">Pedido de Reembolso</h1>
            <p style="font-size:12px;color:#64748b;margin:0;">Gera documento formal de pedido de reembolso para o cliente</p>
        </div>
    </div>
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px;">
        <form method="GET" action="{{ route('relatorios.reembolso.pdf') }}" target="_blank">
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Cliente *</label>
                <select name="cliente_id" required style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;background:#fff;">
                    <option value="">— Selecione —</option>
                    @foreach($clientes as $c)
                    <option value="{{ $c->id }}">{{ $c->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Data início *</label>
                    <input type="date" name="data_ini" value="{{ now()->startOfMonth()->format('Y-m-d') }}" required style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Data fim *</label>
                    <input type="date" name="data_fim" value="{{ now()->endOfMonth()->format('Y-m-d') }}" required style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;">
                </div>
            </div>
            <button type="submit" style="width:100%;padding:12px;background:#1a3a5c;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
                📥 Gerar Pedido de Reembolso (PDF)
            </button>
        </form>
    </div>
    <div style="margin-top:12px;text-align:center;">
        <a href="{{ route('despesas-reembolsos') }}" style="font-size:13px;color:#64748b;text-decoration:none;">← Voltar para Despesas & Reembolsos</a>
    </div>
</div>
@endsection
