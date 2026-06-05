@extends('layouts.app')

@section('content')

<style>
    .dashboard-compact {
        --dc-title: #1f2937;
        --dc-muted: #6b7280;
    }

    .dashboard-compact .dashboard-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dc-title);
        margin-bottom: 1rem;
    }

    .dashboard-compact .metric-card {
        padding: 0.9rem 0.75rem;
        border-radius: 12px;
    }

    .dashboard-compact .metric-label {
        font-size: 0.92rem;
        color: var(--dc-muted);
        margin-bottom: 0.3rem;
        font-weight: 600;
    }

    .dashboard-compact .metric-value {
        font-size: 1.6rem;
        margin: 0;
        line-height: 1.1;
    }

    .dashboard-compact .chart-card {
        padding: 0.9rem 0.85rem;
        border-radius: 12px;
    }

    .dashboard-compact .chart-title {
        font-size: 0.98rem;
        margin-bottom: 0.65rem;
        font-weight: 700;
        color: var(--dc-title);
    }
</style>

@include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs ?? []])

<div class="container dashboard-compact">
    <h2 class="dashboard-title">Panel de Estadísticas</h2>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm text-center metric-card">
                <h5 class="metric-label">Tickets Hoy</h5>
                <h2 class="metric-value">{{ $today }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center metric-card">
                <h5 class="metric-label">Tickets esta Semana</h5>
                <h2 class="metric-value">{{ $week }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center metric-card">
                <h5 class="metric-label">Tickets este Mes</h5>
                <h2 class="metric-value">{{ $month }}</h2>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico Pastel - Tickets por Status -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm chart-card">
                <h5 class="text-center chart-title">Tickets por Estado</h5>
                <div id="chartStatus"></div>
            </div>
        </div>

        <!-- Gráfico Barras - Tickets por Prioridad -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm chart-card">
                <h5 class="text-center chart-title">Tickets por Prioridad</h5>
                <div id="chartPriority"></div>
            </div>
        </div>
    </div>

    <!-- Gráfico Barras - Usuarios que abrieron más tickets -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm chart-card">
                <h5 class="text-center chart-title">Usuarios con más Tickets</h5>
                <div id="chartUsers"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Tickets por Status
    var optionsStatus = {
        chart: { type: 'pie', height: 260 },
        series: @json($ticketsByStatus->values()),
        labels: @json($ticketsByStatus->keys()),
        legend: { fontSize: '12px' },
    };
    new ApexCharts(document.querySelector("#chartStatus"), optionsStatus).render();

    // Tickets por Prioridad
    var optionsPriority = {
        chart: { type: 'bar', height: 260 },
        series: [{
            name: 'Tickets',
            data: @json($ticketsByPriority->values())
        }],
        xaxis: {
            categories: @json($ticketsByPriority->keys())
        },
        dataLabels: { enabled: false }
    };
    new ApexCharts(document.querySelector("#chartPriority"), optionsPriority).render();

    // Tickets por Usuario
    var optionsUsers = {
        chart: { type: 'bar', height: 280 },
        series: [{
            name: 'Tickets',
            data: @json($ticketsByUser->pluck('total'))
        }],
        xaxis: {
            categories: @json($ticketsByUser->pluck('name'))
        },
        dataLabels: { enabled: false }
    };
    new ApexCharts(document.querySelector("#chartUsers"), optionsUsers).render();
</script>
@endsection
