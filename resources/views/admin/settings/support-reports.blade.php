@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Configuracion de Informes Tecnicos</div>
                <div class="card-body">
                    <p class="text-muted">
                        Configura el correlativo anual y los datos del encabezado del informe.
                        El formato final sera: INFORME N°001-2026- SUFIJO.
                    </p>

                    <form action="{{ route('admin.settings.support-reports.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="report_code_suffix">Sufijo de codigo</label>
                                <input type="text" id="report_code_suffix" name="report_code_suffix" class="form-control @error('report_code_suffix') is-invalid @enderror" value="{{ old('report_code_suffix', $settings->report_code_suffix) }}" required>
                                @error('report_code_suffix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="sequence_year">Anio de correlativo</label>
                                <input type="number" id="sequence_year" name="sequence_year" class="form-control @error('sequence_year') is-invalid @enderror" value="{{ old('sequence_year', $settings->sequence_year ?? now()->year) }}" required>
                                @error('sequence_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="last_sequence">Ultimo correlativo usado</label>
                                <input type="number" id="last_sequence" name="last_sequence" class="form-control @error('last_sequence') is-invalid @enderror" value="{{ old('last_sequence', $settings->last_sequence) }}" required>
                                <div class="form-text">Si colocas 25, el siguiente informe sera 026.</div>
                                @error('last_sequence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="recipient_name">Para: nombre</label>
                                <input type="text" id="recipient_name" name="recipient_name" class="form-control @error('recipient_name') is-invalid @enderror" value="{{ old('recipient_name', $settings->recipient_name) }}">
                                @error('recipient_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="recipient_position">Para: cargo</label>
                                <input type="text" id="recipient_position" name="recipient_position" class="form-control @error('recipient_position') is-invalid @enderror" value="{{ old('recipient_position', $settings->recipient_position) }}">
                                @error('recipient_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="sender_prefix">Prefijo de remitente</label>
                                <input type="text" id="sender_prefix" name="sender_prefix" class="form-control @error('sender_prefix') is-invalid @enderror" value="{{ old('sender_prefix', $settings->sender_prefix) }}" required>
                                @error('sender_prefix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="sender_position">Cargo de remitente</label>
                                <input type="text" id="sender_position" name="sender_position" class="form-control @error('sender_position') is-invalid @enderror" value="{{ old('sender_position', $settings->sender_position) }}" required>
                                @error('sender_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3">Papel membretado</h6>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label" for="header_image">Imagen de encabezado</label>
                                <input type="file" id="header_image" name="header_image" class="form-control @error('header_image') is-invalid @enderror" accept="image/*">
                                @error('header_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if($settings->header_image_url)
                                    <div class="mt-2">
                                        <img src="{{ $settings->header_image_url }}" alt="Encabezado actual" class="img-fluid border rounded">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="remove_header_image" name="remove_header_image" value="1">
                                            <label class="form-check-label" for="remove_header_image">Quitar encabezado actual</label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="footer_image">Imagen de pie de pagina</label>
                                <input type="file" id="footer_image" name="footer_image" class="form-control @error('footer_image') is-invalid @enderror" accept="image/*">
                                @error('footer_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if($settings->footer_image_url)
                                    <div class="mt-2">
                                        <img src="{{ $settings->footer_image_url }}" alt="Pie actual" class="img-fluid border rounded">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="remove_footer_image" name="remove_footer_image" value="1">
                                            <label class="form-check-label" for="remove_footer_image">Quitar pie actual</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Configuracion</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light">Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
