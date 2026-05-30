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
    const tenantData  = @json($chartTenants);
    const processoData = @json($chartProcessos);

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
