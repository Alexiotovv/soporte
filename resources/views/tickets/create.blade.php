@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Crear Nuevo Ticket</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="title">Título</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="priority">Prioridad</label>
                            <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🔵 Bajo</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>🟡 Medio</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🔴 Alto</option>
                            </select>
                            @error('priority')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="file">Archivo adjunto <small>(Opcional)</small></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                            <small class="form-text text-muted">
                                Formatos permitidos: JPG, PNG, JPEG. Tamaño máximo: 5MB
                            </small>
                            @error('file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            
                            @isset($ticket->file)
                                <div class="mt-2">
                                    <small>Archivo actual:</small>
                                    <a href="{{ $ticket->file_url }}" target="_blank" class="d-block">
                                        {{ $ticket->file_name }}
                                    </a>
                                </div>
                            @endisset
                        </div>

                        <br>
                         <a href="{{ route('tickets.index') }}" class="btn btn-light btn-sm">⬅️ Regresar a lista</a>
                        <button type="submit" class="btn btn-light btn-sm">📤 Enviar Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection