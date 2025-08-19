@extends('layouts.app')

@section('content')

@include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs ?? []])

<div class="container">
    <h2 class="mb-4">Panel de Estadísticas</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center">
                <h5>Tickets Hoy</h5>
                <h2>{{ $today }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center">
                <h5>Tickets esta Semana</h5>
                <h2>{{ $week }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center">
                <h5>Tickets este Mes</h5>
                <h2>{{ $month }}</h2>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico Pastel - Tickets por Status -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm p-3">
                <h5 class="text-center">Tickets por Estado</h5>
                <div id="chartStatus"></div>
            </div>
        </div>

        <!-- Gráfico Barras - Tickets por Prioridad -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm p-3">
                <h5 class="text-center">Tickets por Prioridad</h5>
                <div id="chartPriority"></div>
            </div>
        </div>
    </div>

    <!-- Gráfico Barras - Usuarios que abrieron más tickets -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm p-3">
                <h5 class="text-center">Usuarios con más Tickets</h5>
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
        chart: { type: 'pie' },
        series: @json($ticketsByStatus->values()),
        labels: @json($ticketsByStatus->keys()),
    };
    new ApexCharts(document.querySelector("#chartStatus"), optionsStatus).render();

    // Tickets por Prioridad
    var optionsPriority = {
        chart: { type: 'bar' },
        series: [{
            name: 'Tickets',
            data: @json($ticketsByPriority->values())
        }],
        xaxis: {
            categories: @json($ticketsByPriority->keys())
        }
    };
    new ApexCharts(document.querySelector("#chartPriority"), optionsPriority).render();

    // Tickets por Usuario
    var optionsUsers = {
        chart: { type: 'bar' },
        series: [{
            name: 'Tickets',
            data: @json($ticketsByUser->pluck('total'))
        }],
        xaxis: {
            categories: @json($ticketsByUser->pluck('name'))
        }
    };
    new ApexCharts(document.querySelector("#chartUsers"), optionsUsers).render();
</script>
@endsection
