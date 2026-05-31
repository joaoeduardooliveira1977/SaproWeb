section('page-title', 'Dashboard')
<div>

    {{-- â”€â”€ KPI Cards â”€â”€ --}}
    <div class="stat-grid">
        <div class="stat-card accent">
            <div class="stat-val">{{ $stats['ativos'] }}</div>
            <div class="stat-label">ðŸ¢ Tenants Ativos</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-val">{{ $stats['inativos'] }}</div>
            <div class="stat-label">ðŸ”´ Tenants Suspensos</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ number_format($stats['usuarios']) }}</div>
            <div class="stat-label">ðŸ‘¤ Total de UsuÃ¡rios</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ number_format($stats['processos']) }}</div>
            <div class="stat-label">ðŸ“ Total de Processos</div>
        </div>
        <div class="stat-card warn">
            <div class="stat-val">{{ $stats['disco_mb'] }} MB</div>
            <div class="stat-label">ðŸ’¾ Disco (storage)</div>
        </div>
        <div class="stat-card gold">
            <div class="stat-val">{{ $stats['novos_30d'] }}</div>
            <div class="stat-label">âœ¨ Novos (Ãºltimos 30 dias)</div>
        </div>
    </div>

    {{-- â”€â”€ GrÃ¡ficos â”€â”€ --}}
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-title">ðŸ“ˆ Novos Tenants por MÃªs</div>
            <canvas id="chartTenants" height="120"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-title">ðŸ“Š Processos Cadastrados por MÃªs</div>
            <canvas id="chartProcessos" height="120"></canvas>
        </div>
    </div>

    {{-- â”€â”€ Acesso rÃ¡pido â”€â”€ --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Acesso RÃ¡pido</span></div>
        <div class="card-body" style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('master.tenants') }}" class="btn btn-primary">ðŸ¢ Tenants</a>
            <a href="{{ route('master.infra') }}"  class="btn btn-outline">ðŸ–¥ï¸ Infraestrutura</a>
            <a href="{{ route('master.alertas') }}" class="btn btn-outline">ðŸ”” Alertas</a>
            <a href="{{ route('master.index') }}"   class="btn" style="background:#f1f5f9;color:#374151;">âš™ï¸ Super Admin</a>
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
                borderColor: '#1D9E75',
                backgroundColor: 'rgba(29,158,117,.1)',
                tension: .35, fill: true,
                pointRadius: 4, pointBackgroundColor: '#1D9E75',
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

