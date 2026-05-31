<div>
@section('page-title', 'Dashboard')

    {{-- ── KPI Cards ── --}}
    <div class="stat-grid">
        <div class="stat-card green">
            <div class="stat-val">{{ $stats['ativos'] }}</div>
            <div class="stat-label">Tenants Ativos</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-val">{{ $stats['inativos'] }}</div>
            <div class="stat-label">Tenants Suspensos</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-val">{{ number_format($stats['usuarios']) }}</div>
            <div class="stat-label">Total de Usuários</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ number_format($stats['processos']) }}</div>
            <div class="stat-label">Total de Processos</div>
        </div>
        <div class="stat-card warn">
            <div class="stat-val">{{ $stats['disco_mb'] }} MB</div>
            <div class="stat-label">Disco (storage)</div>
        </div>
        <div class="stat-card gold">
            <div class="stat-val">{{ $stats['novos_30d'] }}</div>
            <div class="stat-label">Novos (últimos 30 dias)</div>
        </div>
    </div>

    {{-- ── Gráficos ── --}}
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
        <div class="card-header"><span class="card-title">Acesso Rápido</span></div>
        <div class="card-body" style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('master.tenants') }}"    class="btn btn-primary">🏢 Tenants</a>
            <a href="{{ route('master.lixeira') }}"    class="btn btn-ghost">🗑️ Lixeira</a>
            <a href="{{ route('master.comunicados') }}" class="btn btn-outline">📢 Comunicados</a>
            <a href="{{ route('master.infra') }}"      class="btn btn-outline">🖥️ Infraestrutura</a>
            <a href="{{ route('master.alertas') }}"    class="btn btn-outline">🔔 Alertas</a>
            <a href="/status" target="_blank"          class="btn btn-ghost">📡 Status público</a>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tenantData   = @json($chartTenants);
    const processoData = @json($chartProcessos);

    new Chart(document.getElementById('chartTenants'), {
        type: 'line',
        data: {
            labels: tenantData.labels,
            datasets: [{
                label: 'Novos Tenants',
                data: tenantData.data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,.1)',
                tension: .35, fill: true,
                pointRadius: 4, pointBackgroundColor: '#3b82f6',
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
