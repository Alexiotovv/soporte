@extends('layouts.app')

@section('content')
@php
    $dateText = $report->report_date
        ? $report->report_date->locale('es')->translatedFormat('d \\d\\e F \\d\\e\\l Y')
        : now()->locale('es')->translatedFormat('d \\d\\e F \\d\\e\\l Y');
@endphp

<div class="container">
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        @if(auth()->check() && auth()->user()->isSupportUser() && (int) $report->user_id === (int) auth()->id())
            <a href="{{ route('support-reports.edit', $report) }}" class="btn btn-light btn-sm">✏️ Editar</a>
        @endif
        <a href="{{ route('tickets.show', $report->ticket_id) }}" class="btn btn-light btn-sm">⬅️ Volver al Ticket</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()">🖨️ Reimprimir</button>
    </div>

    <div class="card print-card">
        <div class="card-body report-paper">
            @if($report->header_image_url)
                <div class="report-header-image">
                    <img src="{{ $report->header_image_url }}" alt="Membrete de encabezado">
                </div>
            @endif

            <h4 class="text-center report-title">INFORME N°{{ $report->report_code }}</h4>

            <table class="report-meta-table">
                <tr>
                    <td class="label">Para</td>
                    <td class="sep">:</td>
                    <td>
                        <strong>{{ $report->recipient_name }}</strong><br>
                        {{ $report->recipient_position }}
                    </td>
                </tr>
                <tr>
                    <td class="label">De</td>
                    <td class="sep">:</td>
                    <td>
                        <strong>{{ $report->sender_name }}</strong><br>
                        {{ $report->sender_position }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Fecha</td>
                    <td class="sep">:</td>
                    <td>{{ $dateText }}</td>
                </tr>
                <tr>
                    <td class="label">Asunto</td>
                    <td class="sep">:</td>
                    <td>{{ $report->subject }}</td>
                </tr>
            </table>

            <div class="report-content">
                {!! $report->content !!}
            </div>

            @if($report->footer_image_url)
                <div class="report-footer-image">
                    <img src="{{ $report->footer_image_url }}" alt="Membrete de pie de pagina">
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .report-paper {
        background: #fff;
        max-width: 960px;
        margin: 0 auto;
        padding: 1.4rem 1.8rem;
    }

    .report-header-image img,
    .report-footer-image img {
        width: 100%;
        height: auto;
        display: block;
    }

    .report-header-image {
        margin-bottom: 1.1rem;
    }

    .report-footer-image {
        margin-top: 2.2rem;
    }

    .report-title {
        margin-bottom: 1.4rem;
        font-weight: 800;
    }

    .report-meta-table {
        width: 100%;
        margin-bottom: 1.2rem;
        border-collapse: collapse;
    }

    .report-meta-table td {
        vertical-align: top;
        padding: 0.2rem 0;
    }

    .report-meta-table .label {
        width: 70px;
        font-weight: 700;
    }

    .report-meta-table .sep {
        width: 20px;
        text-align: center;
        font-weight: 700;
    }

    .report-content {
        min-height: 280px;
        font-size: 1rem;
        line-height: 1.65;
    }

    @media print {
        body {
            background: #fff !important;
            padding-top: 0 !important;
        }

        .no-print,
        .navbar,
        main > .container > .alert,
        .card-header {
            display: none !important;
        }

        main,
        .container,
        .print-card,
        .print-card .card-body {
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: 0 !important;
            max-width: 100% !important;
        }

        .report-paper {
            padding: 0.5cm 0.6cm !important;
        }
    }
</style>
@endsection
