@section('page-title', 'Dashboard')
<div>

    {{-- ── Stats ── --}}
    <div class="stat-grid">
        <div class="stat-card accent">
            <div class="stat-val">{{ $stats['ativos'] }}</div>
            <div class="stat-label">🏢 Tenants Ativos</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-val">{{ $stats['inativos'] }}</div>
            <div class="stat-label">🔴 Tenants Suspensos</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ number_format($stats['usuarios']) }}</div>
            <div class="stat-label">👤 Total de Usuários</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ number_format($stats['processos']) }}</div>
            <div class="stat-label">📁 Total de Processos</div>
        </div>
        <div class="stat-card warn">
            <div class="stat-val">{{ $stats['disco_mb'] }} MB</div>
            <div class="stat-label">💾 Disco Usado (storage)</div>
        </div>
        <div class="stat-card accent">
            <div class="stat-val">{{ $stats['novos_30d'] }}</div>
            <div class="stat-label">✨ Novos nos últimos 30 dias</div>
        </div>
    </div>

    {{-- ── Financeiro ── --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
            <span class="card-title">💰 Receita Estimada (preços configurados no código)</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:16px;">
                <div style="background:#f0fdf4;border-radius:10px;padding:16px;text-align:center;border:1px solid #bbf7d0;">
                    <div style="font-size:22px;font-weight:800;color:#16a34a;">R$ {{ number_format($financeiro['mrr'], 0, ',', '.') }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:3px;">MRR Estimado</div>
                </div>
                <div style="background:#eff6ff;border-radius:10px;padding:16px;text-align:center;border:1px solid #bfdbfe;">
                    <div style="font-size:22px;font-weight:800;color:#2563a8;">R$ {{ number_format($financeiro['arr'], 0, ',', '.') }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:3px;">ARR Estimado</div>
                </div>
                <div style="background:#faf5ff;border-radius:10px;padding:16px;text-align:center;border:1px solid #e9d5ff;">
                    <div style="font-size:22px;font-weight:800;color:#7c3aed;">{{ $financeiro['conversao'] }}%</div>
                    <div style="font-size:12px;color:#64748b;margin-top:3px;">Conversão Demo → Pago</div>
                </div>
                <div style="background:#fff7ed;border-radius:10px;padding:16px;text-align:center;border:1px solid #fed7aa;">
                    <div style="font-size:22px;font-weight:800;color:#d97706;">R$ {{ number_format($financeiro['ticket_medio'], 0, ',', '.') }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:3px;">Ticket Médio</div>
                </div>
            </div>
            <table style="font-size:13px;width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:8px 12px;text-align:left;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Plano</th>
                        <th style="padding:8px 12px;text-align:center;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Tenants</th>
                        <th style="padding:8px 12px;text-align:right;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Preço/mês</th>
                        <th style="padding:8px 12px;text-align:right;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">MRR</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($financeiro['detalhes'] as $d)
                <tr>
                    <td style="padding:8px 12px;border-bottom:1px solid #f1f5f9;font-weight:600;">{{ $d['plano'] }}</td>
                    <td style="padding:8px 12px;border-bottom:1px solid #f1f5f9;text-align:center;">{{ $d['qtd'] }}</td>
                    <td style="padding:8px 12px;border-bottom:1px solid #f1f5f9;text-align:right;color:#64748b;">
                        {{ $d['preco'] > 0 ? 'R$ '.number_format($d['preco'],0,',','.') : 'Gratuito' }}
                    </td>
                    <td style="padding:8px 12px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:700;color:#16a34a;">
                        {{ $d['subtotal'] > 0 ? 'R$ '.number_format($d['subtotal'],0,',','.') : '—' }}
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Charts ── --}}
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-title">📈 Novos Tenants por Mês</div>
            <canvas id="chartTenants" height="120"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-title">📊 Processos Cadastrados por Mês</div>
            <canvas id="chartProcessos" height="120"></canvas>
        </div>
    </div>

    {{-- ── Gráfico MRR ── --}}
    <div class="chart-card" style="margin-bottom:20px;">
        <div class="chart-title">📈 MRR Estimado — Evolução (últimos 12 meses)</div>
        <canvas id="chartMrr" height="80"></canvas>
    </div>

    {{-- ── Acesso rápido ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Acesso Rápido</span>
        </div>
        <div class="card-body" style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('master-admin.tenants') }}" class="btn btn-primary">🏢 Ver Tenants</a>
            <a href="{{ route('master-admin.infra') }}"  class="btn btn-outline">🖥️ Infraestrutura</a>
            <a href="{{ route('master-admin.alertas') }}" class="btn btn-outline">🔔 Alertas</a>
            <a href="{{ route('super-admin.index') }}"   class="btn" style="background:#f1f5f9;color:#374151;">⚙️ Super Admin Clássico</a>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tenantData   = @json($chartTenants);
    const processoData = @json($chartProcessos);
    const mrrData      = @json($chartMrr);

    new Chart(document.getElementById('chartMrr'), {
        type: 'line',
        data: {
            labels: mrrData.labels,
            datasets: [{
                label: 'MRR (R$)',
                data: mrrData.data,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,.08)',
                tension: .35,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#16a34a',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => 'R$ '+v.toLocaleString('pt-BR') } } }
        }
    });

    new Chart(document.getElementById('chartTenants'), {
        type: 'line',
        data: {
            labels: tenantData.labels,
            datasets: [{
                label: 'Novos Tenants',
                data: tenantData.data,
                borderColor: '#1D9E75',
                backgroundColor: 'rgba(29,158,117,.1)',
                tension: .35,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#1D9E75',
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    new Chart(document.getElementById('chartProcessos'), {
        type: 'bar',
        data: {
            labels: processoData.labels,
            datasets: [{
                label: 'Processos',
                data: processoData.data,
                backgroundColor: 'rgba(26,58,92,.75)',
                borderRadius: 4,
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
});
</script>
@endpush
