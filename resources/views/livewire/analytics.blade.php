<div>

@once
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endonce

<style>
    .chart-card { background:#fff;border:1px solid var(--border);border-radius:10px;padding:20px;box-shadow:0 1px 6px rgba(0,0,0,.07); }
    .chart-title { font-size:13px;font-weight:700;color:var(--primary);margin-bottom:14px; }
    .analytics-grid-3 { display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px; }
    .analytics-grid-2 { display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:16px; }
    @media(max-width:1024px) { .analytics-grid-3 { grid-template-columns:1fr 1fr; } }
    @media(max-width:640px)  { .analytics-grid-3,.analytics-grid-2 { grid-template-columns:1fr; } }
</style>

{{-- ══ KPIs ══ --}}
<div class="stat-grid">
    <div class="stat-card" style="border-left-color:var(--primary);">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 9l9-6 9 6M3 9h18M7 21h10"/><path d="M5 9l2 6H3L5 9zM19 9l2 6h-4l2-6z"/></svg></div>
        <div class="stat-val">{{ $kpis['processos_ativos'] }}</div>
        <div class="stat-label">Processos Ativos</div>
    </div>
    <div class="stat-card" style="border-left-color:#16a34a;">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
        <div class="stat-val" style="color:#16a34a;font-size:20px;">
            R$ {{ number_format($kpis['receita_mes'], 0, ',', '.') }}
        </div>
        <div class="stat-label">Receita este Mês</div>
    </div>
    <div class="stat-card" style="border-left-color:#2563a8;">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div class="stat-val" style="color:#2563a8;">{{ number_format($kpis['horas_mes'], 1, ',', '.') }}h</div>
        <div class="stat-label">Horas este Mês</div>
    </div>
    <div class="stat-card" style="border-left-color:#9d174d;">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
        <div class="stat-val" style="color:#9d174d;">{{ $kpis['prazos_fatais'] }}</div>
        <div class="stat-label">Prazos Fatais em Aberto</div>
    </div>
</div>

{{-- ══ Donuts: Status | Risco | Prazos Urgência ══ --}}
<div class="analytics-grid-3">
    <div class="chart-card">
        <div class="chart-title">Processos por Status</div>
        <div wire:ignore><canvas id="chartStatus" height="220"></canvas></div>
    </div>
    <div class="chart-card">
        <div class="chart-title">Processos por Risco (ativos)</div>
        <div wire:ignore><canvas id="chartRisco" height="220"></canvas></div>
    </div>
    <div class="chart-card">
        <div class="chart-title">Prazos em Aberto por Urgência</div>
        <div wire:ignore><canvas id="chartPrazos" height="220"></canvas></div>
    </div>
</div>

{{-- ══ Financeiro (12 meses) ══ --}}
<div class="chart-card" style="margin-bottom:16px;">
    <div class="chart-title">Recebimentos × Pagamentos — últimos 12 meses (R$)</div>
    <div wire:ignore><canvas id="chartFinanceiro" height="90"></canvas></div>
</div>

{{-- ══ Andamentos | Horas ══ --}}
<div class="analytics-grid-2" style="margin-bottom:16px;">
    <div class="chart-card">
        <div class="chart-title">Andamentos registrados — últimos 6 meses</div>
        <div wire:ignore><canvas id="chartAndamentos" height="180"></canvas></div>
    </div>
    <div class="chart-card">
        <div class="chart-title">Horas apontadas — últimos 6 meses</div>
        <div wire:ignore><canvas id="chartHoras" height="180"></canvas></div>
    </div>
</div>

{{-- ══ Fases (horizontal bar) ══ --}}
<div class="chart-card">
    <div class="chart-title">Distribuição de Processos Ativos por Fase</div>
    <div wire:ignore>
        <canvas id="chartFases"
                height="{{ max(60, count($porFase) * 32) }}"
                style="max-height:360px;"></canvas>
    </div>
</div>

{{-- ══ Script de inicialização ══ --}}
<script>
(function () {
    // Dados PHP → JS
    const STATUS = @json($porStatus);
    const FASES  = @json($porFase);
    const RISCO  = @json($porRisco);
    const FIN_LABELS = @json($labelsFinanceiro);
    const RECEB      = @json($dadosRecebimentos);
    const PAGO       = @json($dadosPagamentos);
    const AND_LABELS = @json($labelsAndamentos);
    const ANDAMENTOS = @json($dadosAndamentos);
    const HORAS      = @json($dadosHoras);
    const PRAZO_URG  = @json($prazosUrgencia);

    const PALETTE = ['#1a3a5c','#2563a8','#16a34a','#d97706','#dc2626','#7c3aed','#0891b2','#9d174d','#475569','#ca8a04'];

    function init() {
        // ── Status (donut) ──────────────────────────────────────
        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: STATUS.map(s => s.status),
                datasets: [{ data: STATUS.map(s => s.total), backgroundColor: PALETTE, borderWidth: 2 }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }, cutout: '60%' }
        });

        // ── Risco (donut) ───────────────────────────────────────
        new Chart(document.getElementById('chartRisco'), {
            type: 'doughnut',
            data: {
                labels: RISCO.map(r => r.risco),
                datasets: [{ data: RISCO.map(r => r.total), backgroundColor: RISCO.map(r => r.cor), borderWidth: 2 }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }, cutout: '60%' }
        });

        // ── Prazos urgência (donut) ─────────────────────────────
        const urgLabels = Object.keys(PRAZO_URG);
        const urgColors = ['#16a34a','#ca8a04','#ea580c','#dc2626','#991b1b'];
        new Chart(document.getElementById('chartPrazos'), {
            type: 'doughnut',
            data: {
                labels: urgLabels,
                datasets: [{ data: urgLabels.map(k => PRAZO_URG[k]), backgroundColor: urgColors, borderWidth: 2 }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }, cutout: '60%' }
        });

        // ── Financeiro (bar + line) ─────────────────────────────
        new Chart(document.getElementById('chartFinanceiro'), {
            type: 'bar',
            data: {
                labels: FIN_LABELS,
                datasets: [
                    {
                        label: 'Recebimentos',
                        data: RECEB,
                        backgroundColor: 'rgba(22,163,74,.75)',
                        borderColor: '#16a34a',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Pagamentos',
                        data: PAGO,
                        backgroundColor: 'rgba(220,38,38,.65)',
                        borderColor: '#dc2626',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
                scales: {
                    y: { ticks: { callback: v => 'R$' + Number(v).toLocaleString('pt-BR') }, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // ── Andamentos (bar) ────────────────────────────────────
        new Chart(document.getElementById('chartAndamentos'), {
            type: 'bar',
            data: {
                labels: AND_LABELS,
                datasets: [{
                    label: 'Andamentos',
                    data: ANDAMENTOS,
                    backgroundColor: 'rgba(37,99,168,.75)',
                    borderColor: '#2563a8',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
            }
        });

        // ── Horas (bar) ─────────────────────────────────────────
        new Chart(document.getElementById('chartHoras'), {
            type: 'bar',
            data: {
                labels: AND_LABELS,
                datasets: [{
                    label: 'Horas',
                    data: HORAS,
                    backgroundColor: 'rgba(124,58,237,.75)',
                    borderColor: '#7c3aed',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => v + 'h' }, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // ── Fases (horizontal bar) ──────────────────────────────
        new Chart(document.getElementById('chartFases'), {
            type: 'bar',
            data: {
                labels: FASES.map(f => f.fase),
                datasets: [{
                    data: FASES.map(f => f.total),
                    backgroundColor: FASES.map((_, i) => PALETTE[i % PALETTE.length]),
                    borderRadius: 4,
                    barThickness: 20,
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                    y: { grid: { display: false }, ticks: { font: { size: 12 } } }
                }
            }
        });
    }

    // Garante que o Chart.js já esteja carregado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

</div>
